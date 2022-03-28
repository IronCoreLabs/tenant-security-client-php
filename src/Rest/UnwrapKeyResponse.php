<?php

declare(strict_types=1);

namespace IronCore\Rest;

use InvalidArgumentException;
use IronCore\Bytes;

/**
 * Response from the Tenant Security Proxy's unwrap key endpoint
 */
class UnwrapKeyResponse
{
    /**
     * @var Bytes
     */
    private $dek;

    /**
     * @param Bytes $dek Document key sent back from the TSP
     */
    public function __construct(Bytes $dek)
    {
        $this->dek = $dek;
    }

    /**
     * Converts from a TSP response to an UnwrapKeyResponse.
     *
     * @param string $response Response from the TSP from the unwrap endpoint
     *
     * @throws InvalidArgumentException if the provided response is not an UnwrapKeyResponse
     *
     * @return UnwrapKeyResponse A valid TSP unwrap key response
     */
    public static function fromResponse(string $response): UnwrapKeyResponse
    {
        $decoded = json_decode($response, true);
        if (!is_array($decoded) ||  !array_key_exists("dek", $decoded) || !is_string($decoded["dek"])) {
            throw new InvalidArgumentException("$response is not a valid UnwrapKeyResponse.");
        }
        return new UnwrapKeyResponse(Bytes::fromBase64($decoded["dek"]));
    }

    /**
     * Gets the document key sent back from the TSP.
     *
     * @return Bytes Document key sent back from the TSP
     */
    public function getDek(): Bytes
    {
        return $this->dek;
    }
}
