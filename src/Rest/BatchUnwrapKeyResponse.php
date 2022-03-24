<?php

declare(strict_types=1);

namespace IronCore\Rest;

use InvalidArgumentException;
use IronCore\Bytes;
use IronCore\Exception\TenantSecurityException;

/**
 * Response from the Tenant Security Proxy's batch unwrap key endpoint
 */
class BatchUnwrapKeyResponse
{
    /**
     * @var UnwrapKeyResponse[]
     */
    private $keys;
    /**
     * @var TenantSecurityException[]
     */
    private $failures;

    /**
     * @param UnwrapKeyResponse[] $keys Successfully unwrapped DEKs
     * @param TenantSecurityException[] $failures Failures when attempting to unwrap DEKs.
     */
    public function __construct(array $keys, array $failures)
    {
        $this->keys = $keys;
        $this->failures = $failures;
    }

    public static function fromResponse(string $response): BatchUnwrapKeyResponse
    {
        $decoded = json_decode($response, true);
        if (!is_array($decoded) ||  !array_key_exists("keys", $decoded) || !array_key_exists("failures", $decoded) || !is_array($decoded["keys"]) || !is_array($decoded["failures"])) {
            throw new InvalidArgumentException("$response is not a valid BatchUnwrapKeyResponse.");
        }
        $keysCallback = fn (array $key): UnwrapKeyResponse => new UnwrapKeyResponse(Bytes::fromBase64($key["dek"]));
        $keys = array_map($keysCallback, $decoded["keys"]);
        $failuresCallback = fn (array $failure): TenantSecurityException => TenantSecurityException::fromDecodedJson($failure);
        $failures = array_map($failuresCallback, $decoded["failures"]);
        return new BatchUnwrapKeyResponse($keys, $failures);
    }

    /**
     * @return UnwrapKeyResponse[]
     */
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
