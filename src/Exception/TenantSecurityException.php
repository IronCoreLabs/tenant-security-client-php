<?php

declare(strict_types=1);

namespace IronCore\Exception;

use Exception;
use JsonException;

/**
 * This exception indicates a problem talking to the TSP or the TSP talking to the
 * key management servers. See the fromResponse static function for the code -> exception mapping.
 */
class TenantSecurityException extends Exception
{
    /**
     * Converts from a TSP error response to a TenantSecurityException.
     *
     * @param string $response Response from the TSP
     *
     * @return TenantSecurityException TenantSecurityException associated with the TSP status code
     */
    public static function fromResponse(string $response): TenantSecurityException
    {
        $decodedResponse = json_decode($response, true);
        if (
            !is_array($decodedResponse) ||
            !array_key_exists("code", $decodedResponse) ||
            !is_int($decodedResponse["code"])
        ) {
            return new TspServiceException("UnknownError: Unknown request error occurred", -1);
        }
        $code = (int) $decodedResponse["code"];
        switch ($code) {
            case 0:
                return new TspServiceException(
                    "UnableToMakeRequest: Request to Tenant Security Proxy could not be made",
                    $code
                );
            case 100:
                return new TspServiceException(
                    "UnknownError: Unknown request error occurred",
                    $code
                );
            case 101:
                return new TspServiceException(
                    "UnauthorizedRequest: Request authorization header API key was incorrect.",
                    $code
                );
            case 102:
                return new TspServiceException(
                    "InvalidRequestBody: Request body was invalid.",
                    $code
                );
            case 200:
                return new KmsException(
                    "NoPrimaryKmsConfiguration: Tenant has no primary KMS configuration.",
                    $code
                );
            case 201:
                return new KmsException(
                    "UnknownTenantOrNoActiveKmsConfigurations: Tenant either doesn't exist
                    or has no active KMS configurations.",
                    $code
                );
            case 202:
                return new KmsException(
                    "KmsConfigurationDisabled: Tenant configuration specified in EDEK is no longer active.",
                    $code
                );
            case 203:
                return new KmsException(
                    "InvalidProvidedEdek: Provided EDEK was not valid.",
                    $code
                );
            case 204:
                return new KmsException(
                    "KmsWrapFailed: Request to KMS API to wrap key returned invalid results.",
                    $code
                );
            case 205:
                return new KmsException(
                    "KmsUnwrapFailed: Request to KMS API to unwrap key returned invalid results.",
                    $code
                );
            case 206:
                return new KmsException(
                    "KmsAuthorizationFailed: Request to KMS failed because the tenant credentials
                    were invalid or have been revoked.",
                    $code
                );
            case 207:
                return new KmsException(
                    "KmsConfigurationInvalid: Request to KMS failed because the key configuration was
                    invalid or the necessary permissions for the operation were missing/revoked.",
                    $code
                );
            case 208:
                return new KmsException(
                    "KmsUnreachable: Request to KMS failed because KMS was unreachable.",
                    $code
                );
            case 301:
                return new SecurityEventException(
                    "SecurityEventRejected: Tenant Security Proxy could not accept the security event",
                    $code
                );
            default:
                return new TenantSecurityException(
                    "Unknown TSP error code.",
                    $code
                );
        }
    }
}
