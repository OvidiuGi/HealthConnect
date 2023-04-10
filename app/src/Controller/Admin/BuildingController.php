<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\Admin\UpdateBuildingType;
use App\Repository\BuildingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class BuildingController extends AbstractController
{
    private BuildingRepository $buildingRepository;

    public function __construct(BuildingRepository $buildingRepository)
    {
        $this->buildingRepository = $buildingRepository;
    }

    #[Route(path: "/buildings", name: 'admin_show_buildings', methods: ['GET'])]
    public function showBuildings(Request $request): Response
    {
        $paginate['page'] = (int)$request->query->get('page',1);
        $paginate['size'] = (int)$request->query->get('size',10);

        $buildings = $this->buildingRepository->getPaginated($paginate['page'], $paginate['size']);
        $totalPages = \ceil(\count($this->buildingRepository->findAll()) / $paginate['size']);

        return $this->render('admin/building/show_buildings_page.html.twig', [
            'buildings' => $buildings,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages
        ]);
    }

    #[Route(path: "/buildings/{id}", name: 'admin_edit_buildings', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $building = $this->buildingRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(UpdateBuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->buildingRepository->save($building);

            return $this->redirectToRoute('admin_show_buildings');
        }

        return $this->render('admin/building/edit_buildings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}