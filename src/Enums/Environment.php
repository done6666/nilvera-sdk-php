<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum Environment: string
{
    case Live = 'live';
    case Test = 'test';

    public function baseUrl(): string
    {
        return match ($this) {
            self::Live => 'https://api.nilvera.com',
            self::Test => 'https://apitest.nilvera.com',
        };
    }
}
