<?php

declare(strict_types=1);

namespace IronCore\Exception;

/**
 * This exception indicates a problem with the Tenant Security Proxy talking to the
 * KMS, or a problem with KMS keys.
 */
class KmsException extends TenantSecurityException
{
}
