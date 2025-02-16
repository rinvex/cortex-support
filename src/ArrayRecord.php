<?php

declare(strict_types=1);

namespace Cortex\Support;

class ArrayRecord
{
    protected static string $keyName = '__key';

    public static function keyName(string $keyName): void
    {
        static::$keyName = $keyName;
    }

    public static function getKeyName(): string
    {
        return static::$keyName;
    }
}
