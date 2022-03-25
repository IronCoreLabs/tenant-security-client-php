<?php

declare(strict_types=1);

namespace IronCore\Rest;

use InvalidArgumentException;
use IronCore\Bytes;

/**
 * Response from the Tenant Security Proxy's re-key endpoint
 */
class RekeyResponse
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
     * Converts from a TSP response to a RekeyResponse.
     *
     * @param string $response Response from the TSP from the re-key endpoint
     *
     * @throws InvalidArgumentException if the provided response is not a RekeyResponse
     *
     * @return RekeyResponse A valid TSP re-key response
     */
    public static function fromResponse(string $response): RekeyResponse
    {
        $decoded = json_decode($response, true);
        if (
            !is_array($decoded) || !array_key_exists("dek", $decoded) || !array_key_exists("edek", $decoded) ||
            !is_string($decoded["dek"]) || !is_string($decoded["edek"])
        ) {
            throw new InvalidArgumentException("$response is not a valid RekeyResponse.");
        }
        return new RekeyResponse(Bytes::fromBase64($decoded["dek"]), Bytes::fromBase64($decoded["edek"]));
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
