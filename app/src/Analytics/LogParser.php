<?php

namespace App\Analytics;

use App\Dto\AnalyticsDto;
use Symfony\Component\Serializer\SerializerInterface;

class LogParser
{
    public function __construct(
        private string $analyticsLogFile,
        private SerializerInterface $serializer
    ) {
    }

    // number of appointments
    // number of accounts created by role
    // number of logins by role
    public function parseLogs(AnalyticsCollectionInterface $analyticsCollection): AnalyticsCollectionInterface
    {
        $handler = \fopen($this->analyticsLogFile, 'r',true);
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