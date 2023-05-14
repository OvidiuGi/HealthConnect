<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\Admin\HospitalType;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route(path: '/admin/hospitals')]
class HospitalController extends AbstractController
{
    public function __construct(
        private HospitalRepository $hospitalRepository,
        private TagAwareCacheInterface $cache,
    ) {
    }

    #[Route(path: "/", name: 'admin_show_hospitals', methods: ['GET'])]
    public function showHospitals(Request $request): Response
    {
        $options = [];
        $options['page'] = (int)$request->query->get('page', 1);
        $options['limit'] = (int)$request->query->get('limit', 10);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['direction'] = $request->query->get('direction');
        $options['search'] = $request->query->get('search');

        return $this->render('admin/hospital/show_hospitals.html.twig', [
            'hospitals' => $this->hospitalRepository->getPaginatedFilteredSorted($options),
            'totalPages' => \ceil(\count($this->hospitalRepository->findAll()) / $options['limit']),
        ]);
    }

    #[Route(path: "/{id}/edit", name: 'admin_edit_hospitals', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $hospital = $this->hospitalRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(HospitalType::class, $hospital);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->hospitalRepository->save($hospital);
            $this->cache->invalidateTags(['browse_hospitals']);

            return $this->redirectToRoute('admin_show_hospitals');
        }

        return $this->render('admin/hospital/hospital_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: "/add", name: 'admin_add_hospital', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(HospitalType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hospital = $form->getData();

            $this->hospitalRepository->save($hospital);
            $this->cache->invalidateTags(['browse_hospitals']);

            return $this->redirectToRoute('admin_show_hospitals');
        }

        return $this->render('admin/hospital/hospital_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
