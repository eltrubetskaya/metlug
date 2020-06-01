<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Installation;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\Common\Collections\Criteria;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                    if ($comment->getUser()->getEmail() !== $user->getEmail()) {
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
