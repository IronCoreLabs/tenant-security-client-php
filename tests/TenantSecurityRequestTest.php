<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
use IronCore\SecurityEvents\UserEvent;
use PHPUnit\Framework\TestCase;

final class TenantSecurityRequestTest extends TestCase
{

    public function testFailedToMakeRequest(): void
    {
        $request = new TenantSecurityRequest("localhost:99999", "");
        $this->expectException(TenantSecurityException::class);
        $request->makeJsonRequest("/", "");
    }

    public function testFailedWrapRequest(): void
    {
        $request = new TenantSecurityRequest("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $request->wrapKey($metadata);
    }

    public function testFailedUnwrapRequest(): void
    {
        $request = new TenantSecurityRequest("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $edek = new Bytes("boo");
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $request->unwrapKey($edek, $metadata);
    }

    public function testFailedRekeyRequest(): void
    {
        $request = new TenantSecurityRequest("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $edek = new Bytes("boo");
        $newTenantId = "foo";
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $request->rekey($edek, $newTenantId, $metadata);
    }

    public function testFailedBatchWrapRequest(): void
    {
        $request = new TenantSecurityRequest("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $request->batchWrapKeys(["a", "2"], $metadata);
    }

    public function testFailedBatchunWrapRequest(): void
    {
        $request = new TenantSecurityRequest("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $request->batchUnwrapKeys(["a" => new Bytes("edek1"), "2" => new Bytes("edek2")], $metadata);
    }

    public function testFailedLogSecurityEventRequest(): void
    {
        $request = new TenantSecurityRequest("localhost:99999", "");
        $metadata = new EventMetadata("tenant", new IclFields("foo"), [], 1);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $request->logSecurityEvent(UserEvent::login(), $metadata);
    }
}
