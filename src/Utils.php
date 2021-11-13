<?php

declare(strict_types=1);

namespace IronCore\Utils;

function trim_slashes(string $string): string
{
    return trim($string, "/");
}
