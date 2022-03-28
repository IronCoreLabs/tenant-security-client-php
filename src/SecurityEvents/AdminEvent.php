<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

class AdminEvent extends SecurityEvent
{
    private $flatEvent;

    private function __construct(string $eventName)
    {
        $this->flatEvent = "ADMIN_" . $eventName;
    }

    public function getFlatEvent(): string
    {
        return $this->flatEvent;
    }

    public static function add(): AdminEvent
    {
        return new AdminEvent("ADD");
    }
    public static function remove(): AdminEvent
    {
        return new AdminEvent("REMOVE");
    }
    public static function changePermissions(): AdminEvent
    {
        return new AdminEvent("CHANGE_PERMISSIONS");
    }
    public static function changeSetting(): AdminEvent
    {
        return new AdminEvent("CHANGE_SETTING");
    }
}
