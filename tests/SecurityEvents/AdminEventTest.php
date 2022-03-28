<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

use PHPUnit\Framework\TestCase;

final class AdminEventTest extends TestCase
{
    public function testEventExpectedValues(): void
    {
        $this->assertEquals(AdminEvent::add()->getFlatEvent(), "ADMIN_ADD");
        $this->assertEquals(AdminEvent::remove()->getFlatEvent(), "ADMIN_REMOVE");
        $this->assertEquals(AdminEvent::changePermissions()->getFlatEvent(), "ADMIN_CHANGE_PERMISSIONS");
        $this->assertEquals(AdminEvent::changeSetting()->getFlatEvent(), "ADMIN_CHANGE_SETTING");
    }
}
