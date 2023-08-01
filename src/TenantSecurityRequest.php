<?php

declare(strict_types=1);

namespace IronCore;

use InvalidArgumentException;
use IronCore\Bytes;
use IronCore\Rest\UnwrapKeyRequest;
use IronCore\Rest\WrapKeyRequest;
use IronCore\Rest\WrapKeyResponse;
use IronCore\Rest\UnwrapKeyResponse;
use IronCore\Rest\RekeyResponse;
use IronCore\Exception\TenantSecurityException;
use IronCore\Rest\BatchUnwrapKeyRequest;
use IronCore\Rest\BatchUnwrapKeyResponse;
use IronCore\Rest\BatchWrapKeyRequest;
use IronCore\Rest\BatchWrapKeyResponse;
use IronCore\Rest\LogSecurityEventRequest;
use IronCore\Rest\RekeyRequest;
use IronCore\SecurityEvents\SecurityEvent;

/**
 * Class used to communicate with the Tenant Security Proxy.
 * Not intended to be used by consumers of the SDK.
 */
class TenantSecurityRequest
{
    private const TSP_API_PREFIX = "/api/1/";
    private const WRAP_ENDPOINT = "document/wrap";
    private const BATCH_WRAP_ENDPOINT = "document/batch-wrap";
    private const UNWRAP_ENDPOINT = "document/unwrap";
    private const BATCH_UNWRAP_ENDPOINT = "document/batch-unwrap";
    private const REKEY_ENDPOINT = "document/rekey";
    private const TENANT_KEY_DERIVE_ENDPOINT = "key/derive";
    private const SECURITY_EVENT_ENDPOINT = "event/security-event";

    /**
     * @var string URL of the Tenant Security Proxy
     */
    private $tspAddress;
    /**
     * @var string Secret key used to communicate with the Tenant Security Proxy
     */
    private $apiKey;
    /**
     * @var \CurlHandle Curl handle used to make requests
     */
    private $ch;

    /**
     * @param string $tspAddress URL of the Tenant Security Proxy
     * @param string $apiKey Secret key needed to communicate with the Tenant Security Proxy
     */
    protected function __construct(string $tspAddress, string $apiKey)
    {
        $this->tspAddress = Utils\trimSlashes($tspAddress);
        $this->apiKey = $apiKey;
        $this->ch = curl_init();
        // All of our requests are POSTs
        curl_setopt($this->ch, CURLOPT_POST, true);
        // All of our requests are JSON and need our authorization header
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: cmk $this->apiKey"
        ]);
        // Make `curl_exec` return the response instead of just true/false
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Makes a POST request to a Tenant Security Proxy endpoint with the provided JSON payload.
     *
     * @param string $endpoint Tenant Security Proxy endpoint to make a request to
     * @param string $jsonEncodedData Payload to send to the Tenant Security Proxy
     *
     * @throws TenantSecurityException if the request to the Tenant Security Proxy fails
     *
     * @return string The response from the Tenant Security Proxy
     */
    protected function makeJsonRequest(string $endpoint, string $jsonEncodedData): string
    {
        // Set the request URL
        curl_setopt($this->ch, CURLOPT_URL, $this->tspAddress . self::TSP_API_PREFIX . $endpoint);
        // Set the request body
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $jsonEncodedData);
        $response = curl_exec($this->ch);
        if ($response == false) {
            throw new TenantSecurityException("Request Error: Failed to make a request to the TSP.", -1);
        } else {
            return $response;
        }
    }

    /**
     * Requests the TSP to generate a DEK and an EDEK.
     *
     * @param RequestMetadata $metadata Metadata about the requesting user/service
     *
     * @throws TenantSecurityException if the TSP responds with an error or if the request to the TSP fails.
     *
     * @return WrapKeyResponse The generated DEK and EDEK
     */
    protected function wrapKey(RequestMetadata $metadata): WrapKeyResponse
    {
        $request = new WrapKeyRequest($metadata);
        $response = $this->makeJsonRequest(self::WRAP_ENDPOINT, $request->getJsonData());
        try {
            $wrapResponse = WrapKeyResponse::fromResponse($response);
        } catch (InvalidArgumentException $e) {
            throw TenantSecurityException::fromResponse($response);
        }
        return $wrapResponse;
    }

    /**
     * Requests the TSP to generate multiple DEK/EDEK pairs.
     *
     * @param string[] $documentIds Document IDs to generate DEK/EDEK for.
     * @param RequestMetadata $metadata Metadata about the requesting user/service
     *
     * @throws TenantSecurityException if the request to the TSP fails.
     *
     * @return BatchWrapKeyResponse The generated DEKs and EDEKs, as well as any failures
     */
    protected function batchWrapKeys(array $documentIds, RequestMetadata $metadata): BatchWrapKeyResponse
    {
        $request = new BatchWrapKeyRequest($metadata, $documentIds);
        $response = $this->makeJsonRequest(self::BATCH_WRAP_ENDPOINT, $request->getJsonData());
        try {
            $batchWrapResponse = BatchWrapKeyResponse::fromResponse($response);
        } catch (InvalidArgumentException $e) {
            throw TenantSecurityException::fromResponse($response);
        }
        return $batchWrapResponse;
    }

    /**
     * Requests the TSP to unwrap an EDEK.
     *
     * @param Bytes $edek The encrypted document key to unwrap
     * @param RequestMetadata $metadata Metadata about the requesting user/service
     *
     * @throws TenantSecurityException if the TSP responds with an error or if the request to the TSP fails.
     *
     * @return UnwrapKeyResponse The unwrapped DEK
     */
    protected function unwrapKey(Bytes $edek, RequestMetadata $metadata): UnwrapKeyResponse
    {
        $request = new UnwrapKeyRequest($metadata, $edek);
        $response = $this->makeJsonRequest(self::UNWRAP_ENDPOINT, $request->getJsonData());
        try {
            $unwrapResponse = UnwrapKeyResponse::fromResponse($response);
        } catch (InvalidArgumentException $e) {
            throw TenantSecurityException::fromResponse($response);
        }
        return $unwrapResponse;
    }

    /**
     * Requests the TSP to unwrap multiple EDEKs.
     *
     * @param Bytes[] $edeks Map from document IDs to EDEKs to unwrap
     * @param RequestMetadata $metadata Metadata about the requesting user/service
     *
     * @throws TenantSecurityException if the request to the TSP fails.
     *
     * @return BatchUnwrapKeyResponse The unwrapped DEKs, as well as any failures
     */
    protected function batchUnwrapKeys(array $edeks, RequestMetadata $metadata): BatchUnwrapKeyResponse
    {
        $request = new BatchUnwrapKeyRequest($metadata, $edeks);
        $response = $this->makeJsonRequest(self::BATCH_UNWRAP_ENDPOINT, $request->getJsonData());
        try {
            $batchWrapResponse = BatchUnwrapKeyResponse::fromResponse($response);
        } catch (InvalidArgumentException $e) {
            throw TenantSecurityException::fromResponse($response);
        }
        return $batchWrapResponse;
    }

    /**
     * Requests the TSP to re-key an EDEK.
     *
     * @param Bytes $edek The encrypted document key to re-key
     * @param string $newTenantId Tenant ID to re-key to
     * @param RequestMetadata $metadata Metadata about the requesting user/service
     *
     * @throws TenantSecurityException if the TSP responds with an error or if the request to the TSP fails.
     *
     * @return RekeyResponse The new DEK and EDEK
     */
    protected function rekey(Bytes $edek, string $newTenantId, RequestMetadata $metadata): RekeyResponse
    {
        $request = new RekeyRequest($metadata, $edek, $newTenantId);
        $response = $this->makeJsonRequest(self::REKEY_ENDPOINT, $request->getJsonData());
        try {
            $rekeyResponse = RekeyResponse::fromResponse($response);
        } catch (InvalidArgumentException $e) {
            throw TenantSecurityException::fromResponse($response);
        }
        return $rekeyResponse;
    }

    /**
     * Request to the security event endpoint with the provided event and metadata.
     *
     * @param SecurityEvent $event Security event representing the action to be logged.
     * @param EventMetadata $metadata Metadata associated with the security event.
     * @return void Failures come back as exceptions
     */
    protected function logSecurityEvent(SecurityEvent $event, EventMetadata $metadata): void
    {
        $request = new LogSecurityEventRequest($metadata, $event);
        $response = $this->makeJsonRequest(self::SECURITY_EVENT_ENDPOINT, $request->getJsonData());
        if ($response !== "null") {
            throw TenantSecurityException::fromResponse($response);
        }
    }
}
