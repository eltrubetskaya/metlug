<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lib")
 */
class LibController extends AbstractController
{
    /**
     * @Route("/{title}", name="lib_read", methods={"GET"})
     */
    public function read(string $title): Response
    {
        return $this->render('lib/' . $title . '.html.twig');
    }
}
