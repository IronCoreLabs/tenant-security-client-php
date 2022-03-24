<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

class PeriodicEvent extends SecurityEvent
{
    var $flatEvent;

    private function __construct(string $eventName)
    {
        $this->flatEvent = "PERIODIC_" . $eventName;
    }

    public function getFlatEvent(): string
    {
        return $this->flatEvent;
    }

    public static function enforceRetentionPolicy(): PeriodicEvent
    {
        return new PeriodicEvent("ENFORCE_RETENTION_POLICY");
    }
    public static function createBackup(): PeriodicEvent
    {
        return new PeriodicEvent("CREATE_BACKUP");
    }
}
