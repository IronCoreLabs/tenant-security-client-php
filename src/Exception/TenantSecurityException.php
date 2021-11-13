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
    protected $message;
    protected $code;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

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
            return new TspServiceException(-1, "UnknownError: Unknown request error occurred");
        }
        $code = (int) $decodedResponse["code"];
        switch ($code) {
            case 0:
                return new TspServiceException(
                    $code,
                    "UnableToMakeRequest: Request to Tenant Security Proxy could not be made"
                );
            case 100:
                return new TspServiceException(
                    $code,
                    "UnknownError: Unknown request error occurred"
                );
            case 101:
                return new TspServiceException(
                    $code,
                    "UnauthorizedRequest: Request authorization header API key was incorrect."
                );
            case 102:
                return new TspServiceException(
                    $code,
                    "InvalidRequestBody: Request body was invalid."
                );
            case 200:
                return new KmsException(
                    $code,
                    "NoPrimaryKmsConfiguration: Tenant has no primary KMS configuration."
                );
            case 201:
                return new KmsException(
                    $code,
                    "UnknownTenantOrNoActiveKmsConfigurations: Tenant either doesn't exist 
                    or has no active KMS configurations."
                );
            case 202:
                return new KmsException(
                    $code,
                    "KmsConfigurationDisabled: Tenant configuration specified in EDEK is no longer active."
                );
            case 203:
                return new KmsException(
                    $code,
                    "InvalidProvidedEdek: Provided EDEK was not valid."
                );
            case 204:
                return new KmsException(
                    $code,
                    "KmsWrapFailed: Request to KMS API to wrap key returned invalid results."
                );
            case 205:
                return new KmsException(
                    $code,
                    "KmsUnwrapFailed: Request to KMS API to unwrap key returned invalid results."
                );
            case 206:
                return new KmsException(
                    $code,
                    "KmsAuthorizationFailed: Request to KMS failed because the tenant credentials 
                    were invalid or have been revoked."
                );
            case 207:
                return new KmsException(
                    $code,
                    "KmsConfigurationInvalid: Request to KMS failed because the key configuration was 
                    invalid or the necessary permissions for the operation were missing/revoked."
                );
            case 208:
                return new KmsException(
                    $code,
                    "KmsUnreachable: Request to KMS failed because KMS was unreachable."
                );
            case 301:
                return new SecurityEventException(
                    $code,
                    "SecurityEventRejected: Tenant Security Proxy could not accept the security event"
                );
            default:
                return new TenantSecurityException(
                    $code,
                    "Unknown TSP error code."
                );
        }
    }
}
