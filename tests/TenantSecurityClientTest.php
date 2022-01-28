<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
use PHPUnit\Framework\TestCase;

final class TenantSecurityClientTest extends TestCase
{

    public function testFailedToMakeWrapRequest(): void
    {
        $tsc = new TenantSecurityClient("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $tsc->encrypt(["/" => new Bytes("")], $metadata);
    }

    public function testFailedToMakeUnwrapRequest(): void
    {
        $tsc = new TenantSecurityClient("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $tsc->decrypt(new EncryptedDocument(["/" => new Bytes("")], new Bytes("edek")), $metadata);
    }

    public function testFailedToMakeRekeyRequest(): void
    {
        $tsc = new TenantSecurityClient("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $newTenantId = "foo";
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $tsc->rekeyEdek(new Bytes("edek"), $newTenantId, $metadata);
    }
}
