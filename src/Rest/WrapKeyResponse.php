<?php

declare(strict_types=1);

namespace IronCore\Rest;

use InvalidArgumentException;
use IronCore\Bytes;

/**
 * Response from the Tenant Security Proxy's wrap key endpoint
 */
class WrapKeyResponse
{
    /**
     * @var Bytes
     */
    private $dek;
    /**
     * @var Bytes
     */
    private $edek;

    /**
     * @param Bytes $dek Document key sent back from the TSP
     * @param Bytes $edek Encrypted document key sent back from the TSP.
     */

    public function __construct(Bytes $dek, Bytes $edek)
    {
        $this->dek = $dek;
        $this->edek = $edek;
    }

    /**
     * Converts from a TSP response to a WrapKeyResponse.
     *
     * @param string $response Response from the TSP from the wrap endpoint
     *
     * @throws InvalidArgumentException if the provided response is not a WrapKeyResponse
     *
     * @return WrapKeyResponse A valid TSP wrap key response
     */
    public static function fromResponse(string $response): WrapKeyResponse
    {
        $decoded = json_decode($response, true);
        if (!is_array($decoded) || !is_string($decoded["dek"]) || !is_string($decoded["edek"])) {
            throw new InvalidArgumentException("$response is not a valid WrapKeyResponse.");
        }
        return new WrapKeyResponse(Bytes::fromBase64($decoded["dek"]), Bytes::fromBase64($decoded["edek"]));
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

    /**
     * Gets the encrypted document key sent back from the TSP.
     *
     * @return Bytes Encrypted document key sent back from the TSP
     */
    public function getEdek(): Bytes
    {
        return $this->edek;
    }
}
