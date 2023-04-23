<?php

namespace App\Analytics;

use App\Dto\AnalyticsDto;
use Doctrine\Common\Collections\ArrayCollection;

class AccountsAnalytics implements AnalyticsCollectionInterface
{
    public function __construct(
        private ArrayCollection $accounts = new ArrayCollection()
    ) {
    }

    public function add(AnalyticsDto $analyticsDto): void
    {
        if ($analyticsDto->context['type'] === 'register' && !$this->accounts->contains($analyticsDto)) {
            $this->accounts->add($analyticsDto);
        }
    }

    public function getAccountsByRole(string $role): int
    {
        return $this->accounts->filter(fn (AnalyticsDto $dto) => $dto->context['role'] === $role)->count();
    }

    public function get(): ArrayCollection
    {
        return $this->accounts;
    }
}