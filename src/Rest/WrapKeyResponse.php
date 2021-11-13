<?php

declare(strict_types=1);

namespace IronCore\Rest;

use InvalidArgumentException;
use IronCore\Bytes;

class WrapKeyResponse
{
    private $dek;
    private $edek;

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

    public function getDek(): Bytes
    {
        return $this->dek;
    }

    public function getEdek(): Bytes
    {
        return $this->edek;
    }
}
