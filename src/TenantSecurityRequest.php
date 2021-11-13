<?php

declare(strict_types=1);

namespace IronCore;

use InvalidArgumentException;
use IronCore\Bytes;
use IronCore\Rest\UnwrapKeyRequest;
use IronCore\Rest\WrapKeyRequest;
use IronCore\Rest\WrapKeyResponse;
use IronCore\Rest\UnwrapKeyResponse;
use IronCore\Exception\TenantSecurityException;
use RuntimeException;

const TSP_API_PREFIX = "/api/1/";
const WRAP_ENDPOINT = "document/wrap";
const UNWRAP_ENDPOINT = "document/unwrap";
const BATCH_UNWRAP_ENDPOINT = "document/batch-unwrap";
const TENANT_KEY_DERIVE_ENDPOINT = "key/derive";

class TenantSecurityRequest
{
    private $tsp_address;
    private $api_key;
    private $ch;

    public function __construct(string $tsp_address, string $api_key)
    {
        $this->tsp_address = Utils\trim_slashes($tsp_address);
        $this->api_key = $api_key;
        $this->ch = curl_init();
        // All of our requests are POSTs
        curl_setopt($this->ch, CURLOPT_POST, true);
        // All of our requests are JSON and need our authorization header
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: cmk $this->api_key"
        ]);
        // Make `curl_exec` return the response instead of just true/false
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    public function makeJsonRequest(string $endpoint, string $json_encoded_data)
    {
        // Set the request URL
        curl_setopt($this->ch, CURLOPT_URL, $this->tsp_address . TSP_API_PREFIX . $endpoint);
        // Set the request body
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $json_encoded_data);
        return curl_exec($this->ch);
    }

    /**
     * Requests the TSP to generate a DEK and an EDEK.
     *
     * @param RequestMetadata $metadata Metadata about the requesting user/service
     *
     * @throws RuntimeException if the request to the TSP fails
     * @throws TenantSecurityException if the TSP responds with an error
     *
     * @return WrapKeyResponse The generated DEK and EDEK
     */
    public function wrapKey(RequestMetadata $metadata): WrapKeyResponse
    {
        $request = new WrapKeyRequest($metadata);
        $response = $this->makeJsonRequest(WRAP_ENDPOINT, $request->getJsonData());
        if ($response == false) {
            throw new RuntimeException("Failed to make request to TSP.");
        }
        try {
            $wrap_response = WrapKeyResponse::fromResponse($response);
        } catch (InvalidArgumentException $e) {
            throw TenantSecurityException::fromResponse($response);
        }
        return $wrap_response;
    }

    /**
     * Requests the TSP to unwrap an EDEK.
     *
     * @param Bytes $edek The encrypted document key to unwrap
     * @param RequestMetadata $metadata Metadata about the requesting user/service
     *
     * @throws RuntimeException if the request to the TSP fails
     * @throws TenantSecurityException if the TSP responds with an error
     *
     * @return UnwrapKeyResponse The unwrapped DEK
     */
    public function unwrapKey(Bytes $edek, RequestMetadata $metadata): UnwrapKeyResponse
    {
        $request = new UnwrapKeyRequest($metadata, $edek);
        $response = $this->makeJsonRequest(UNWRAP_ENDPOINT, $request->getJsonData());
        if ($response == false) {
            throw new RuntimeException("Failed to make request to TSP.");
        }
        try {
            $unwrap_response = UnwrapKeyResponse::fromResponse($response);
        } catch (InvalidArgumentException $e) {
            throw TenantSecurityException::fromResponse($response);
        }
        return $unwrap_response;
    }
}
