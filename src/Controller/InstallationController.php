<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Installation;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/installation")
 */
class InstallationController extends AbstractController
{
    /**
     * @Route("/{title}", name="installation_show", methods={"GET"})
     */
    public function show(Request $request, CommentRepository $commentRepository, Installation $installation): Response
    {
        $comment = new Comment();
        $comment->setInstallation($installation);
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_create', ['installationId' => $installation->getId()]),
            'method' => Request::METHOD_POST
        ]);

        return $this->render('installation/'. $installation->getTitle() .'.html.twig', [
            'installation' => $installation,
            'modalShow' => $request->query->get('modalShow'),
            'form' => $form->createView(),
            'comments' => $commentRepository->findBy([
                'installation' => $installation,
            ], ['createdAt' => Criteria::DESC])
        ]);
    }
}
