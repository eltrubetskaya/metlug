<?php

namespace App\Controller;

use App\Entity\Installation;
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
    public function show(Request $request, Installation $installation): Response
    {
        return $this->render('installation/'. $installation->getTitle() .'.html.twig', [
            'installation' => $installation,
            'modalShow' => $request->query->get('modalShow')
        ]);
    }
}
