<?php

namespace App\Controller;

use App\Entity\Installation;
use App\Form\InstallationType;
use App\Repository\InstallationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/installation")
 */
class InstallationController extends AbstractController
{
//    /**
//     * @Route("/", name="installation_index", methods={"GET"})
//     */
//    public function index(InstallationRepository $installationRepository): Response
//    {
//        return $this->render('installation/index.html.twig', [
//            'installations' => $installationRepository->findAll(),
//        ]);
//    }

//    /**
//     * @Route("/new", name="installation_new", methods={"GET","POST"})
//     */
//    public function new(Request $request): Response
//    {
//        $installation = new Installation();
//        $form = $this->createForm(InstallationType::class, $installation);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($installation);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('installation_index');
//        }
//
//        return $this->render('installation/new.html.twig', [
//            'installation' => $installation,
//            'form' => $form->createView(),
//        ]);
//    }
//
    /**
     * @Route("/{title}", name="installation_show", methods={"GET"})
     */
    public function show(Installation $installation): Response
    {
        return $this->render('installation/'. $installation->getTitle() .'.html.twig', [
            'installation' => $installation,
        ]);
    }
//
//    /**
//     * @Route("/{id}/edit", name="installation_edit", methods={"GET","POST"})
//     */
//    public function edit(Request $request, Installation $installation): Response
//    {
//        $form = $this->createForm(InstallationType::class, $installation);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('installation_index');
//        }
//
//        return $this->render('installation/edit.html.twig', [
//            'installation' => $installation,
//            'form' => $form->createView(),
//        ]);
//    }
//
//    /**
//     * @Route("/{id}", name="installation_delete", methods={"DELETE"})
//     */
//    public function delete(Request $request, Installation $installation): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$installation->getId(), $request->request->get('_token'))) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->remove($installation);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('installation_index');
//    }
}
