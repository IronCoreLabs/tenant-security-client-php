<?php

declare(strict_types=1);

namespace IronCore\Exception;

/**
 * This indicates a problem from the Tenant Security Proxy itself (such as invalid API key).
 */
final class TspServiceException extends TenantSecurityException
{
}
