<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/appointments')]
class AppointmentController extends AbstractController
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    #[Route(name: 'web_show_appointments', methods: ['GET'])]
    public function showAppointments(Request $request): Response
    {
        $paginate['page'] = (int)$request->query->get('page',1);
        $paginate['size'] = (int)$request->query->get('size',10);

        $appointments = $this
            ->appointmentRepository
            ->getPaginatedByUser($paginate['page'], $paginate['size'], $this->getUser()->getId());
        $totalPages = \ceil(\count($this->appointmentRepository->findAll()) / $paginate['size']);

        return $this->render('web/appointment/show_appointments_page.html.twig', [
            'appointments' => $appointments,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages,
        ]);
    }
}