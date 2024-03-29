<?php

declare(strict_types=1);

namespace App\Analytics;

use App\Dto\AnalyticsDto;
use Doctrine\Common\Collections\ArrayCollection;

interface AnalyticsCollectionInterface
{
    public function add(AnalyticsDto $analyticsDto): void;

    public function get(): ArrayCollection;
}
