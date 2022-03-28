<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
use IronCore\SecurityEvents\UserEvent;
use PHPUnit\Framework\TestCase;

final class TenantSecurityClientTest extends TestCase
{

    public function testFailedToMakeEncryptRequest(): void
    {
        $tsc = new TenantSecurityClient("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $tsc->encrypt(["/" => new Bytes("")], $metadata);
    }

    public function testFailedToMakeDecryptRequest(): void
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

    public function testFailedToMakeBatchEncryptRequest(): void
    {
        $tsc = new TenantSecurityClient("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $tsc->batchEncrypt(["a" => ["/" => new Bytes("")]], $metadata);
    }

    public function testFailedToMakeBatchDecryptRequest(): void
    {
        $tsc = new TenantSecurityClient("localhost:99999", "");
        $metadata = new RequestMetadata("tenant", new IclFields("foo"), []);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $tsc->batchDecrypt(["a" => new EncryptedDocument(["/" => new Bytes("")], new Bytes("edek"))], $metadata);
    }

    public function testFailedToMakeLogSecurityEventRequest(): void
    {
        $tsc = new TenantSecurityClient("localhost:99999", "");
        $metadata = new EventMetadata("tenant", new IclFields("foo"), [], 1);
        $this->expectException(TenantSecurityException::class);
        $this->expectExceptionMessage("Failed to make a request to the TSP.");
        $tsc->logSecurityEvent(UserEvent::add(), $metadata);
    }
}
