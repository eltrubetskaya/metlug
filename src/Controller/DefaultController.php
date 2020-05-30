<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/page/{title}", name="app_page_view")
     */
    public function view(Request $request)
    {
        $title = $request->get('title');
        $pages = [
            '25-12-2019',
            '24-04-2019',
            '27-03-2019',
            '08-02-2019',
            '08-05-2020',
            '32-05-2020'
        ];
        if (\in_array($title, $pages, true)) {
            return $this->render($title . '.html.twig');
        }

        return $this->redirect($this->generateUrl('homepage'));
    }
}
