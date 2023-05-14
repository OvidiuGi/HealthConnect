<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route(path: '/')]
class MainPageController extends AbstractController
{
    public function __construct(
        private HospitalRepository $hospitalRepository,
        private TagAwareCacheInterface $cache
    ) {
    }

    #[Route(path: '/', name: 'web_main_page', methods: ['GET'])]
    public function load(): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            $cacheKey = 'main_page_' . $this->getUser()->getId();

            return $this->cache->get($cacheKey, function (ItemInterface $item) {
                $item->expiresAfter(43200);

                return $this->render('web/main_page/main_page.html.twig');
            });
        }

        return $this->cache->get('main_page', function (ItemInterface $item) {
            $item->expiresAfter(43200);

            return $this->render('web/main_page/main_page.html.twig');
        });
    }

    #[Route(path: '/hospitals', name: 'web_browse_hospitals', methods: ['GET'])]
    public function browseHospitals(Request $request): Response
    {
        $options = [];
        $options['page'] = (int)$request->query->get('page', 1);
        $options['limit'] = (int)$request->query->get('limit', 10);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['direction'] = $request->query->get('direction');
        $options['search'] = $request->query->get('search');

        return $this->render('web/main_page/browse_hospitals.html.twig', [
            'hospitals' => $this->hospitalRepository->getPaginatedFilteredSorted($options),
            'totalPages' => \ceil(\count($this->hospitalRepository->findAll()) / $options['limit']),
        ]);
    }
}
