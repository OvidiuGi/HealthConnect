<?php

namespace App\Controller\Web;

use App\Entity\Service;
use App\Form\Web\ServiceType;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path:'/medic/services')]
class ServiceController extends AbstractController
{
    public function __construct(
        private ServiceRepository $serviceRepository,
        private UserRepository $userRepository,
    ) {
    }

    #[Route(name: 'web_show_services', methods: ['GET'])]
    public function showServices(Request $request): Response
    {
        $services = $this->getUser()->getServices();

        return $this->render('web/service/show_services.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route(path: '/add', name: 'web_add_service', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $form = $this->createForm(ServiceType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Service $service */
            $service = $form->getData();
            $service->setDoctor($this->userRepository->findOneBy(['id' => $this->getUser()->getId()]));


            $this->serviceRepository->save($service);

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
            $this->addFlash('error','Service not found');

            return $this->redirectToRoute('web_show_services');
        }

        $this->serviceRepository->delete($service);
        $this->addFlash('success','Service deleted successfully');

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

            return $this->redirectToRoute('web_show_services');
        }

        return $this->render('web/service/service_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit Service'
        ]);
    }
}