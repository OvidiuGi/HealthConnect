<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\Admin\BuildingType;
use App\Repository\BuildingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route(path: '/admin/buildings')]
class BuildingController extends AbstractController
{
    public function __construct(
        private BuildingRepository $buildingRepository,
        private TagAwareCacheInterface $cache,
    ) {
    }

    #[Route(path: "/", name: 'admin_show_buildings', methods: ['GET'])]
    public function showBuildings(Request $request): Response
    {
        $options = [];
        $options['page'] = (int)$request->query->get('page', 1);
        $options['limit'] = (int)$request->query->get('limit', 10);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['direction'] = $request->query->get('direction');
        $options['search'] = $request->query->get('search');

        return $this->render('admin/building/show_buildings_page.html.twig', [
            'buildings' => $this->buildingRepository->getPaginatedFilteredSorted($options),
            'totalPages' => \ceil(\count($this->buildingRepository->findAll()) / $options['limit']),
        ]);
    }

    #[Route(path: "/{id}/edit", name: 'admin_edit_buildings', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $building = $this->buildingRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->buildingRepository->save($building);
            $this->cache->invalidateTags(['browse_hospitals']);

            return $this->redirectToRoute('admin_show_buildings');
        }

        return $this->render('admin/building/edit_buildings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: "/add", name: 'admin_add_building', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(BuildingType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $building = $form->getData();

            $this->buildingRepository->save($building);
            $this->cache->invalidateTags(['browse_hospitals']);

            return $this->redirectToRoute('admin_show_buildings');
        }

        return $this->render('admin/building/edit_buildings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}