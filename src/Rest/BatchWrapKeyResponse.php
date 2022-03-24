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

    /**
     * @param WrapKeyResponse[] $keys Successfully wrapped DEK/EDEK pairs
     * @param TenantSecurityException[] $failures Failures when attempting to wrap keys.
     */
    public function __construct(array $keys, array $failures)
    {
        $this->keys = $keys;
        $this->failures = $failures;
    }

    public static function fromResponse(string $response): BatchWrapKeyResponse
    {
        $decoded = json_decode($response, true);
        if (!is_array($decoded) || !array_key_exists("keys", $decoded) || !array_key_exists("failures", $decoded) || !is_array($decoded["keys"]) || !is_array($decoded["failures"])) {
            throw new InvalidArgumentException("$response is not a valid BatchWrapKeyResponse.");
        }
        $keysCallback = fn (array $key): WrapKeyResponse => new WrapKeyResponse(Bytes::fromBase64($key["dek"]), Bytes::fromBase64($key["edek"]));
        $keys = array_map($keysCallback, $decoded["keys"]);
        $failuresCallback = fn (array $failure): TenantSecurityException => TenantSecurityException::fromDecodedJson($failure);
        $failures = array_map($failuresCallback, $decoded["failures"]);
        return new BatchWrapKeyResponse($keys, $failures);
    }

    /**
     * @return WrapKeyResponse[] Keys that were successfully wrapped
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @return TenantSecurityException[] Keys that failed to be wrapped
     */
    public function getFailures(): array
    {
        return $this->failures;
    }
}
