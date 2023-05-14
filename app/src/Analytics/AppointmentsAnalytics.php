<?php

declare(strict_types=1);

namespace App\Analytics;

use App\Dto\AnalyticsDto;
use Doctrine\Common\Collections\ArrayCollection;

class AppointmentsAnalytics implements AnalyticsCollectionInterface
{
    public function __construct(
        private readonly ArrayCollection $appointments = new ArrayCollection()
    ) {
    }

    public function add(AnalyticsDto $analyticsDto): void
    {
        if ($analyticsDto->context['type'] === 'new-appointment' && !$this->appointments->contains($analyticsDto)) {
            $this->appointments->add($analyticsDto);
        }
    }

    public function get(): ArrayCollection
    {
        return $this->appointments;
    }

    public function getAppointmentsByMedicId(int $id): int
    {
        return $this->appointments->filter(fn (AnalyticsDto $dto) => $dto->context['medicId'] === $id)->count();
    }

    public function getAppointmentsByDateInterval(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        return $this->appointments->filter(
            fn (AnalyticsDto $dto) =>
            $dto->context['date'] >= $start && $dto->context['date'] <= $end
        )->count();
    }

    public function getAppointmentsByDate(string $date): int
    {
        return $this->appointments->filter(fn (AnalyticsDto $dto) => $dto->context['date'] === $date)->count();
    }

    public function getByMedicId(int $id): ArrayCollection
    {
        return $this->appointments->filter(fn (AnalyticsDto $dto) => $dto->context['medicId'] === $id);
    }

    public function getAppointmentsByService(string $service): int
    {
        return $this->appointments->filter(fn (AnalyticsDto $dto) => $dto->context['service'] === $service)->count();
    }
}
