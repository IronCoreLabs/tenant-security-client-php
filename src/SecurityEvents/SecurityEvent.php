<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

abstract class SecurityEvent
{
    abstract public function getFlatEvent(): string;
}
