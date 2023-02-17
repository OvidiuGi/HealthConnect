<?php

namespace App\Controller\Web;

use App\Form\Admin\RegisterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/')]
class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'web_login', methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('web/login/login.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/register', name: 'web_register', methods: ['GET','POST'])]
    public function register()
    {
        $form = $this->createForm(RegisterFormType::class);

        return $this->render('web/login/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/logout', name: 'web_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}