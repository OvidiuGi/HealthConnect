<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Entity\Service;
use App\Form\Web\ServiceType;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route(path:'/medic/services')]
class ServiceController extends AbstractController
{
    public function __construct(
        private readonly ServiceRepository      $serviceRepository,
        private readonly UserRepository         $userRepository,
        private readonly TagAwareCacheInterface $cache,
    ) {
    }

    #[Route(name: 'web_show_services', methods: ['GET'])]
    public function showServices(Request $request): Response
    {
        $cacheTag = 'services_' . $this->getUser()->getId();

        return $this->cache->get($cacheTag, function (ItemInterface $item) use ($cacheTag) {
            $services = $this->getUser()->getServices();
            $item->expiresAfter(43200);

            foreach ($services as $service) {
                $item->tag('service_id_' . $service->getId());
            }

            return $this->render('web/service/show_services.html.twig', [
                'services' => $services,
            ]);
        });
    }

    #[Route(path: '/add', name: 'web_add_service', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $form = $this->createForm(ServiceType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Service $service */
            $service = $form->getData();
            $service->setMedic($this->userRepository->findOneBy(['id' => $this->getUser()->getId()]));
            $this->serviceRepository->save($service);

            $this->cache->invalidateTags(['service_id_' . $service->getId()]);

            return $this->redirectToRoute('web_show_services');
        }

        return $this->render('web/service/service_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Add service',
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'web_delete_service', methods: ['GET', 'POST'])]
    public function delete(int $id): Response
    {
        $service = $this->serviceRepository->findOneBy(['id' => $id]);
        if (null === $service) {
            return $this->redirectToRoute('web_show_services');
        }

        $this->cache->invalidateTags([
            'service_id_' . $service->getId(),
            'appointment_service_id_' . $service->getId(),
        ]);
        $this->serviceRepository->delete($service);

        return $this->redirectToRoute('web_show_services');
    }

    #[Route(path: '/{id}/edit', name: 'web_edit_service', methods: ['GET', 'POST'])]
    public function update(int $id, Request $request): Response
    {
        $service = $this->serviceRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service = $form->getData();

            $this->serviceRepository->update($service);

            $this->cache->invalidateTags([
                'service_id_' . $service->getId(),
                'appointment_service_id_' . $service->getId(),
            ]);
            return $this->redirectToRoute('web_show_services');
        }

        return $this->render('web/service/service_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit Service'
        ]);
    }
}
