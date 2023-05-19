<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Form\Web\UserRegisterFormType;
use App\Repository\UserRepository;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/')]
class SecurityController extends AbstractController
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository              $userRepository,
        private readonly LoggerInterface $analyticsLogger
    ) {
    }

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
    public function register(Request $request): Response
    {
        $form = $this->createForm(UserRegisterFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->password = $this->passwordHasher->hashPassword($user, $user->plainPassword);
            $this->userRepository->save($user);

            $this->analyticsLogger->info(
                'User succesfully created',
                [
                    'email' => $user->email,
                    'role' => $user->role,
                    'type' => 'register',
                    'success' => true,
                ]
            );
            return $this->redirectToRoute('web_login');
        }

        return $this->render('web/login/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/logout', name: 'web_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
