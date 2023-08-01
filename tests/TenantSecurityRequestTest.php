<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
use IronCore\SecurityEvents\UserEvent;
use PHPUnit\Framework\TestCase;

final class TestableTenantSecurityRequest extends TenantSecurityRequest
{
    public function __construct(string $tspAddress, string $apiKey)
    {
        return parent::__construct($tspAddress, $apiKey);
    }
}

final class TenantSecurityRequestTest extends TestCase
{

    private static function callMethod(string $name, array $args)
    {
        $class = new \ReflectionClass('IronCore\TenantSecurityRequest');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        $object = new TestableTenantSecurityRequest("localhost:99999", "");
        $result = $method->invokeArgs($object, $args);
        return $result;
    }

    public function testFailedToMakeRequest(): void
    {
        $this->expectException(TenantSecurityException::class);
        self::callMethod("makeJsonRequest", ["/", ""]);
    }

    public function testFailedWrapRequest(): void
    {
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        self::callMethod("wrapKey", [$metadata]);
    }

    public function testFailedUnwrapRequest(): void
    {
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $edek = new Bytes("boo");
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        self::callMethod("unwrapKey", [$edek, $metadata]);
    }

    public function testFailedRekeyRequest(): void
    {
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $edek = new Bytes("boo");
        $newTenantId = "foo";
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        self::callMethod("rekey", [$edek, $newTenantId, $metadata]);
    }

    public function testFailedBatchWrapRequest(): void
    {
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        self::callMethod("batchWrapKeys", [["a", "2"], $metadata]);
    }

    public function testFailedBatchunWrapRequest(): void
    {
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        self::callMethod("batchUnwrapKeys", [["a" => new Bytes("edek1"), "2" => new Bytes("edek2")], $metadata]);
    }

    public function testFailedLogSecurityEventRequest(): void
    {
        $metadata = new EventMetadata("tenant", new IclFields("foo"), [], 1);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        self::callMethod("logSecurityEvent", [UserEvent::login(), $metadata]);
    }
}
