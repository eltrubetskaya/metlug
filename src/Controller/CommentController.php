<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Installation;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\UploadService;
use Doctrine\Common\Collections\Criteria;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{installationId}/comment")
 * @ParamConverter("installation", options={"id" = "installationId"})
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository, Installation $installation): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findBy([
                'installation' => $installation,
            ], ['createdAt' => Criteria::DESC]),
        ]);
    }

    /**
     * @Route("/create", name="comment_create", methods={"GET","POST"})
     */
    public function create(
        Request $request,
        Installation $installation,
        UserRepository $repository,
        MailService $mailService,
        LoggerInterface $logger,
        UploadService $fileStack
    ): Response
    {
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

//        $comment = new Comment();
//        $comment->setInstallation($installation);
//        $form = $this->createForm(CommentType::class, $comment, [
//            'action' => $this->generateUrl('comment_create', ['installationId' => $installation->getId()]),
//            'method' => Request::METHOD_POST
//        ]);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            /** @var UploadedFile $mediaLink */
//            $mediaLink = $form->get('mediaLink')->getData();
//
//            if ($mediaLink) {
//                $originalFilename = pathinfo($mediaLink->getClientOriginalName(), PATHINFO_FILENAME);
//                // this is needed to safely include the file name as part of the URL
//                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
//                $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $mediaLink->guessExtension();
//
//                // Move the file to the directory where brochures are stored
//                try {
//                    $mediaLink->move(
//                        $this->getParameter('upload_directory'),
//                        $newFilename
//                    );
//                    $link = $fileStack->upload($this->getParameter('upload_directory'). $newFilename);
//                    if ($link) {
//                        $comment->setMediaLink('https://cdn.filestackcontent.com/resize=height:300,width:300,fit:crop/output=compress:true,quality:70,strip:true/' . $link);
//                    }
//                } catch (FileException $e) {
//                    $logger->critical($e->getMessage());
//                }
//            }
//
//
//
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($comment);
//            $entityManager->flush();
//            if ($comment->getType() === Comment::TYPE_VIDEO) {
//                $link = $comment->getMediaLink();
//                parse_str( parse_url( $link, PHP_URL_QUERY ), $vars );
//                preg_match('/(http(s|):|)\/\/(www\.|)yout(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $link, $results);
//                $comment->setMediaLink($results[6]);
//            }
//            /** @var User $logged */
//            $logged = $this->getUser();
//            if ($logged) {
//                $comment
//                    ->setUser($logged)
//                    ->setUsername($logged->getFirstName() . ' ' . $logged->getLastName())
//                ;
//            }
//            $entityManager->flush();
//            try {
//                $users = $repository->findBy(['subscribed' => true]);
//                foreach ($users as $user) {
//                    $mailService->sendMail($user->getEmail(),$installation->getDescription(), MailService::CREATE_COMMENT_TEMPLATE, [
//                        'title' => $installation->getDescription(),
//                        'link' => $this->generateUrl('installation_show', [
//                            'title' => $installation->getTitle()
//                        ]),
//                        'user' => $comment->getUsername()
//                    ]);
//                    $logger->info('Email success sent to ' . $user->getEmail());
//                }
//            } catch (\Exception $e) {
//                $logger->info('Emails error: ' . $e->getMessage());
//            }
//
//            return $this->redirectToRoute('installation_show', ['title' => $installation->getTitle()]);
//        }
//
//        return $this->render('comment/new.html.twig', [
//            'comment' => $comment,
//            'form' => $form->createView(),
//        ]);
    }

    /**
     * @Route("/new", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request, Installation $installation, UserRepository $repository, MailService $mailService, LoggerInterface $logger): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $comment = new Comment();
        $comment->setInstallation($installation);
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_new', ['installationId' => $installation->getId()]),
            'method' => Request::METHOD_POST
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            try {
                $users = $repository->findBy(['subscribed' => true]);
                foreach ($users as $user) {
                    if ($comment->getUser() && $comment->getUser()->getEmail() !== $user->getEmail()) {
                        $mailService->sendMail($user->getEmail(),$installation->getDescription(), MailService::CREATE_COMMENT_TEMPLATE, [
                            'title' => $installation->getDescription(),
                            'link' => $this->generateUrl('installation_show', [
                                'title' => $installation->getTitle()
                            ]),
                            'user' => (string) $comment->getUser()
                        ]);
                        $logger->info('Email success sent to ' . $user->getEmail());
                    }
                }
            } catch (\Exception $e) {
                $logger->info('Emails error: ' . $e->getMessage());
            }

            return $this->redirectToRoute('installation_show', ['title' => $installation->getTitle()]);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Comment $comment, Installation $installation): Response
    {
        if ($this->getUser()->getId() !== $comment->getUser()->getId()) {
            $this->createAccessDeniedException();
        }
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index', ['installationId' => $installation->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Comment $comment, Installation $installation): Response
    {
        if ($this->getUser()->getId() !== $comment->getUser()->getId()) {
            $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index', ['installationId' => $installation->getId()]);
    }
}
