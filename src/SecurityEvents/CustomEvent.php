<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

use InvalidArgumentException;

final class CustomEvent extends SecurityEvent
{
    private $flatEvent;

    private function __construct(string $eventName)
    {
        $this->flatEvent = "CUSTOM_" . $eventName;
    }

    public function getFlatEvent(): string
    {
        return $this->flatEvent;
    }

    public static function customEvent(string $event): CustomEvent
    {
        if (preg_match("<[A-Z_]+>", $event) !== 1 || $event === "" || $event[0] === "_") {
            throw new InvalidArgumentException("Custom event must be screaming snake case, " .
                "not empty, and start with a letter.");
        }
        return new CustomEvent($event);
    }
}
