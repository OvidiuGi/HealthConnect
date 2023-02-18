<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
class MainPageController extends AbstractController
{
    #[Route(path: '/', name: 'web_main_page', methods: ['GET'])]
    public function load(): Response
    {
        return $this->render('web/main_page/main_page.html.twig');
    }
}