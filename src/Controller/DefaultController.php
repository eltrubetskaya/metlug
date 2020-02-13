<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/page/{title}")
     */
    public function view(Request $request)
    {
        $title = $request->get('title');

        return $this->render($title . '.html.twig');
    }
}
