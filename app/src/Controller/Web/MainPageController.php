<?php

namespace App\Controller\Web;

use App\Repository\BuildingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
class MainPageController extends AbstractController
{
    private BuildingRepository $buildingRepository;

    public function __construct(BuildingRepository $buildingRepository)
    {
        $this->buildingRepository = $buildingRepository;
    }

    #[Route(path: '/', name: 'web_main_page', methods: ['GET'])]
    public function load(): Response
    {
        return $this->render('web/main_page/main_page.html.twig');
    }

    #[Route(path: '/new-appointment', name: 'web_new_appointment', methods: ['GET'])]
    public function newAppointment(): Response
    {
        return $this->render('web/main_page/new_appointment.html.twig', [
            'buildings' => $this->buildingRepository->findAll(),
        ]);
    }
}