<?php

declare(strict_types=1);

namespace IronCore\Exception;

use Exception;

/**
 * This exception indicates a problem with encrypting, decrypting or verifying signatures.
 */
class CryptoException extends Exception
{
    protected $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
