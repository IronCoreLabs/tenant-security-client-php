<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class CustomEventTest extends TestCase
{
    public function testValidCustomEvent(): void
    {
        $event = CustomEvent::customEvent("FOO");
        $this->assertEquals($event->getFlatEvent(), "CUSTOM_FOO");
    }
    public function testLowercaseCustomEvent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom event must be screaming snake case, not empty, and start with a letter.");
        CustomEvent::customEvent("foo");
    }
    public function testEmptyCustomEvent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom event must be screaming snake case, not empty, and start with a letter.");
        CustomEvent::customEvent("");
    }
    public function testInvalidStartCustomEvent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom event must be screaming snake case, not empty, and start with a letter.");
        CustomEvent::customEvent("_FOO");
    }
}
