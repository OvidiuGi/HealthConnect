<?php

declare(strict_types=1);

namespace App\Analytics;

use App\Dto\AnalyticsDto;
use Symfony\Component\Serializer\SerializerInterface;

class LogParser
{
    public function __construct(
        private readonly string $analyticsLogFile,
        private readonly SerializerInterface $serializer
    ) {
    }

    public function parseLogs(AnalyticsCollectionInterface $analyticsCollection): AnalyticsCollectionInterface
    {
        $handler = \fopen($this->analyticsLogFile, 'r', true);
        $line = \fgets($handler);
        while ($line != null) {
            $analyticsCollection->add(
                $this->serializer->deserialize($line, AnalyticsDto::class, 'json')
            );
            $line = \fgets($handler);
        }

        return $analyticsCollection;
    }
}
