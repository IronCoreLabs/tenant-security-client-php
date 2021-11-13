<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use IronCore\Bytes;

final class CryptoRng extends Rng
{
    private static $instance = null;
    public function randomBytes(int $length): Bytes
    {
        return new Bytes(random_bytes($length));
    }

    public static function getInstance(): CryptoRng
    {
        if (self::$instance == null) {
            self::$instance = new CryptoRng();
        }
        return self::$instance;
    }
}
