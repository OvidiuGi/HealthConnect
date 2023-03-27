<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Admin\NewsletterType;
use App\Message\NewsletterNotification;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private MessageBusInterface $bus)
    {
    }

    #[Route(path: '/admin/newsletter/send', name: 'admin_send_newsletter', methods: ['GET', 'POST'])]
    public function send(Request $request): Response
    {
        $form = $this->createForm(NewsletterType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $users = $this->userRepository->findBy(['isSubscribed' => true]);
            foreach ($users as $user) {
               $this->bus->dispatch(new NewsletterNotification($user, $data['content'], $data['subject']));
            }
        }

        return $this->render('admin/newsletter_send.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/newsletter/subscribe', name: 'web_subscribe_newsletter', methods: ['POST'])]
    public function subscribe(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->isSubscribed = true;
        $this->userRepository->update($user);

        return $this->redirectToRoute('web_main_page');
    }
}