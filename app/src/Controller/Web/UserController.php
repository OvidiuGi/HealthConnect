<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\ResetMyPasswordType;
use App\Form\Web\UpdateUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route(path: '/')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TagAwareCacheInterface $cache
    ) {
    }

    #[Route(path: '/edit', name: 'web_edit_user', methods: ['GET', 'POST'])]
    public function update(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UpdateUserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->userRepository->update($user);
            $this->cache->invalidateTags(['appointment_customer_id_' . $user->getId()]);

            return $this->redirectToRoute('web_login');
        }

        return $this->render('web/user/edit_user.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/hospitals/{id}/medics', name: 'web_show_medics', methods: ['GET'])]
    public function getMedicsByHospitalId(int $id): Response
    {
        $cacheTag = 'browse_medics_hospital_' . $id;
        return $this->cache->get($cacheTag, function (ItemInterface $item) use ($id, $cacheTag) {
            $item->expiresAfter(43200);
            $item->tag($cacheTag);
            return $this->render('web/user/show_medics.html.twig', [
                'medics' => $this->userRepository->findBy(['office' => $id]),
            ]);
        });
    }

    #[Route(path: '/delete', name: 'web_delete_user', methods: ['GET','POST'])]
    public function delete(Security $security): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $security->logout(false);

        $this->cache->invalidateTags(['appointment_customer_iddo_' . $user->getId()]);
        $this->userRepository->delete($user);

        return $this->redirectToRoute('web_login');
    }

    #[Route(path: '/change-password', name: 'web_change_password', methods: ['GET','POST'])]
    public function changePassword(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ResetMyPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->password = $this->passwordHasher->hashPassword($user, $form->getData()->plainPassword);
            $this->userRepository->save($user);

            return new Response('Done', 200);
        }

        return $this->render('web/user/change_password.html.twig', [
            'form' => $form,
        ]);
    }
}
