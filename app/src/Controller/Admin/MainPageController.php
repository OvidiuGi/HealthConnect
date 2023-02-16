<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class MainPageController extends AbstractController
{
    #[Route(path: '/', name: 'admin_main_page', methods: ['GET'])]
    public function load(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/main_page/main_page.html.twig', []);
    }
}