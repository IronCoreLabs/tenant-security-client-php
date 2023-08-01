<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

final class DataEvent extends SecurityEvent
{
    private $flatEvent;

    private function __construct(string $eventName)
    {
        $this->flatEvent = "DATA_" . $eventName;
    }

    public function getFlatEvent(): string
    {
        return $this->flatEvent;
    }

    public static function import(): DataEvent
    {
        return new DataEvent("IMPORT");
    }
    public static function export(): DataEvent
    {
        return new DataEvent("EXPORT");
    }
    public static function encrypt(): DataEvent
    {
        return new DataEvent("ENCRYPT");
    }
    public static function decrypt(): DataEvent
    {
        return new DataEvent("DECRYPT");
    }
    public static function create(): DataEvent
    {
        return new DataEvent("CREATE");
    }
    public static function delete(): DataEvent
    {
        return new DataEvent("DELETE");
    }
    public static function denyAccess(): DataEvent
    {
        return new DataEvent("DENY_ACCESS");
    }
    public static function changePermissions(): DataEvent
    {
        return new DataEvent("CHANGE_PERMISSIONS");
    }
}
