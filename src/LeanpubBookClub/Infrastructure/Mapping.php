<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

trait Mapping
{
    /**
     * @param array<string,mixed> $data
     */
    private static function asString(array $data, string $key): string
    {
        if (!isset($data[$key]) || $data[$key] === null || $data[$key] === '') {
            return '';
        }

        return (string)$data[$key];
    }

    /**
     * @param array<string,mixed> $data
     */
    private static function asStringOrNull(array $data, string $key): ?string
    {
        if (!isset($data[$key]) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return (string)$data[$key];
    }

    /**
     * @param array<string,mixed> $data
     */
    private static function asInt(array $data, string $key): int
    {
        if (!isset($data[$key]) || $data[$key] === null || $data[$key] === '') {
            return 0;
        }

        return (int)$data[$key];
    }

    /**
     * @param array<string,mixed> $data
     */
    private static function asIntOrNull(array $data, string $key): ?int
    {
        if (!isset($data[$key]) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return (int)$data[$key];
    }

    /**
     * @param array<string,mixed> $data
     */
    private static function asBool(array $data, string $key): bool
    {
        if (!isset($data[$key]) || $data[$key] === null || $data[$key] === '') {
            return false;
        }

        return (bool)$data[$key];
    }

    /**
     * @param array<string,mixed> $data
     */
    private static function asBoolOrNull(array $data, string $key): ?bool
    {
        if (!isset($data[$key]) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return (bool)$data[$key];
    }
}
