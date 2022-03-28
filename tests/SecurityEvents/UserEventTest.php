<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

use PHPUnit\Framework\TestCase;

final class UserEventTest extends TestCase
{
    public function testEventExpectedValues(): void
    {
        $this->assertEquals(UserEvent::add()->getFlatEvent(), "USER_ADD");
        $this->assertEquals(UserEvent::suspend()->getFlatEvent(), "USER_SUSPEND");
        $this->assertEquals(UserEvent::remove()->getFlatEvent(), "USER_REMOVE");
        $this->assertEquals(UserEvent::login()->getFlatEvent(), "USER_LOGIN");
        $this->assertEquals(UserEvent::timeoutSession()->getFlatEvent(), "USER_TIMEOUT_SESSION");
        $this->assertEquals(UserEvent::lockout()->getFlatEvent(), "USER_LOCKOUT");
        $this->assertEquals(UserEvent::logout()->getFlatEvent(), "USER_LOGOUT");
        $this->assertEquals(UserEvent::changePermissions()->getFlatEvent(), "USER_CHANGE_PERMISSIONS");
        $this->assertEquals(UserEvent::expirePassword()->getFlatEvent(), "USER_EXPIRE_PASSWORD");
        $this->assertEquals(UserEvent::resetPassword()->getFlatEvent(), "USER_RESET_PASSWORD");
        $this->assertEquals(UserEvent::changePassword()->getFlatEvent(), "USER_CHANGE_PASSWORD");
        $this->assertEquals(UserEvent::rejectLogin()->getFlatEvent(), "USER_REJECT_LOGIN");
        $this->assertEquals(UserEvent::enableTwoFactor()->getFlatEvent(), "USER_ENABLE_TWO_FACTOR");
        $this->assertEquals(UserEvent::disableTwoFactor()->getFlatEvent(), "USER_DISABLE_TWO_FACTOR");
        $this->assertEquals(UserEvent::changeEmail()->getFlatEvent(), "USER_CHANGE_EMAIL");
        $this->assertEquals(UserEvent::requestEmailVerification()->getFlatEvent(), "USER_REQUEST_EMAIL_VERIFICATION");
        $this->assertEquals(UserEvent::verifyEmail()->getFlatEvent(), "USER_VERIFY_EMAIL");
    }
}
