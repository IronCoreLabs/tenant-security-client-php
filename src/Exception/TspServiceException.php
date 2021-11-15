<?php

declare(strict_types=1);

namespace IronCore\Exception;

/**
 * This indicates a problem from the Tenant Security Proxy itself (such as invalid API key).
 */
class TspServiceException extends TenantSecurityException
{
}
