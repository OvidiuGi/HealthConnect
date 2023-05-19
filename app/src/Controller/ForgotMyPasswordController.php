<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ForgotMyPasswordType;
use App\Form\ResetMyPasswordType;
use App\Message\ForgotMyPasswordNotification;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route(path:"/")]
class ForgotMyPasswordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MessageBusInterface $bus
    ) {
    }

    #[Route(path:"/forgot-password", name:"forgot_password", methods:["GET","POST"])]
    public function send(Request $request): Response
    {
        $form = $this->createForm(ForgotMyPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['email' => $form->getData()->email]);
            if (null === $user) {
                return $this->render('forgot_password/forgot_password.html.twig', [
                    'form' => $form,
                ]);
            }

            $user->forgotPasswordToken = (string)Uuid::v4();
            $user->setForgotPasswordTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
            $this->userRepository->save($user);

            $this->bus->dispatch(new ForgotMyPasswordNotification($user));

            return new Response('Check your email', 200);
        }

        return $this->render('forgot_password/forgot_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path:"/reset-password", name:"reset_password", methods:["GET","POST"])]
    public function reset(Request $request): Response
    {
        if ($request->get('token') === null) {
            return $this->redirectToRoute('web_login');
        }

        $user = $this->userRepository->findOneBy(['forgotPasswordToken' => $request->get('token')]);

        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetMyPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getForgotPasswordTokenExpiresAt() < new \DateTimeImmutable()) {
                return $this->redirectToRoute('web_login');
            }

            $user->password = $this->passwordHasher->hashPassword($user, $form->getData()->plainPassword);
            $user->forgotPasswordToken = null;
            $user->setForgotPasswordTokenExpiresAt(null);
            $this->userRepository->save($user);

            return $this->redirectToRoute('web_login');
        }

        return $this->render('forgot_password/reset_password.html.twig', [
            'form' => $form,
        ]);
    }
}
