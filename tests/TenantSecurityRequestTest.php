<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
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
}
