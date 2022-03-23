<?php

declare(strict_types=1);

namespace IronCore\Rest;

use InvalidArgumentException;
use IronCore\Bytes;
use IronCore\Exception\TenantSecurityException;

/**
 * Response from the Tenant Security Proxy's batch wrap key endpoint
 */
class BatchWrapKeyResponse
{
    /**
     * @var WrapKeyResponse[]
     */
    private $keys;
    /**
     * @var TenantSecurityException[]
     */
    private $failures;

    public function __construct(array $keys, array $failures)
    {
        $this->keys = $keys;
        $this->failures = $failures;
    }

    public static function fromResponse(string $response): BatchWrapKeyResponse
    {
        $decoded = json_decode($response, true);
        if (!is_array($decoded) || !is_array($decoded["keys"]) || !is_array($decoded["failures"])) {
            throw new InvalidArgumentException("$response is not a valid BatchWrapKeyResponse.");
        }
        $keysCallback = fn (array $key): WrapKeyResponse => new WrapKeyResponse(Bytes::fromBase64($key["dek"]), Bytes::fromBase64($key["edek"]));
        $keys = array_map($keysCallback, $decoded["keys"]);
        $failuresCallback = fn (array $failure): TenantSecurityException => TenantSecurityException::fromResponse(json_encode($failure)); // TODO: I hate this
        $failures = array_map($failuresCallback, $decoded["failures"]);
        return new BatchWrapKeyResponse($keys, $failures);
    }

    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @return TenantSecurityException[] TODO
     */
    public function getFailures(): array
    {
        return $this->failures;
    }
}
