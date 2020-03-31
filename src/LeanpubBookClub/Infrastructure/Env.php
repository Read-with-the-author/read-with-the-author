<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use RuntimeException;

final class Env
{
    public static function get(string $key, ?string $default = null): string
    {
        $value = getenv($key);

        if ($value === false) {
            if ($default !== null) {
                return $default;
            }

            throw new RuntimeException(
                sprintf(
                    'Required environment variable "%s" is undefined',
                    $key
                )
            );
        }

        return $value;
    }
}
