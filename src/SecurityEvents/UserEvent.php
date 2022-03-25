<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

class UserEvent extends SecurityEvent
{
    private $flatEvent;

    private function __construct(string $eventName)
    {
        $this->flatEvent = "USER_" . $eventName;
    }

    public function getFlatEvent(): string
    {
        return $this->flatEvent;
    }

    public static function add(): UserEvent
    {
        return new UserEvent("ADD");
    }
    public static function suspend(): UserEvent
    {
        return new UserEvent("SUSPEND");
    }
    public static function remove(): UserEvent
    {
        return new UserEvent("REMOVE");
    }
    public static function login(): UserEvent
    {
        return new UserEvent("LOGIN");
    }
    public static function timeoutSession(): UserEvent
    {
        return new UserEvent("TIMEOUT_SESSION");
    }
    public static function lockout(): UserEvent
    {
        return new UserEvent("LOCKOUT");
    }
    public static function logout(): UserEvent
    {
        return new UserEvent("LOGOUT");
    }
    public static function changePermissions(): UserEvent
    {
        return new UserEvent("CHANGE_PERMISSIONS");
    }
    public static function expirePassword(): UserEvent
    {
        return new UserEvent("EXPIRE_PASSWORD");
    }
    public static function resetPassword(): UserEvent
    {
        return new UserEvent("RESET_PASSWORD");
    }
    public static function changePassword(): UserEvent
    {
        return new UserEvent("CHANGE_PASSWORD");
    }
    public static function rejectLogin(): UserEvent
    {
        return new UserEvent("REJECT_LOGIN");
    }
    public static function enableTwoFactor(): UserEvent
    {
        return new UserEvent("ENABLE_TWO_FACTOR");
    }
    public static function disableTwoFactor(): UserEvent
    {
        return new UserEvent("DISABLE_TWO_FACTOR");
    }
    public static function changeEmail(): UserEvent
    {
        return new UserEvent("CHANGE_EMAIL");
    }
    public static function requestEmailVerification(): UserEvent
    {
        return new UserEvent("REQUEST_EMAIL_VERIFICATION");
    }
    public static function verifyEmail(): UserEvent
    {
        return new UserEvent("VERIFY_EMAIL");
    }
}
