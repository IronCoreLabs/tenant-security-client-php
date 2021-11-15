<?php

declare(strict_types=1);

namespace IronCore\Utils;

use PHPUnit\Framework\TestCase;

final class UtilsTest extends TestCase
{
    public function testTrimSlashes(): void
    {
        $url = "//foobar.com//";
        $trimmed = trimSlashes($url);
        $this->assertEquals($trimmed, "foobar.com");
    }
}
