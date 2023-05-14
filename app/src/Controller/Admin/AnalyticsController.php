<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Analytics\AccountsAnalytics;
use App\Analytics\LogParser;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/analytics')]
class AnalyticsController extends AbstractController
{
    public function __construct(
        private readonly LogParser         $logParser,
        private readonly AccountsAnalytics $accountsAnalytics,
    ) {
    }

    #[Route(path: '/accounts', name: 'admin_analytics_accounts', methods: ['GET'])]
    public function accountsAnalytics(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $analytics = $this->logParser->parseLogs($this->accountsAnalytics);
        $accounts = [];

        array_map(function ($role) use (&$analytics, &$accounts) {
            /** @var AccountsAnalytics $analytics  */
            $accounts[$role] = $analytics->getAccountsByRole($role);
        }, User::ROLES);


        return $this->render('admin/analytics/number_accounts.html.twig', [
            'accounts' => $accounts,
        ]);
    }
}
