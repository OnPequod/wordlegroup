<?php

use Tests\TestCase;

uses(TestCase::class);

function setEnvironmentValue(string $key, ?string $value): void
{
    if ($value === null) {
        putenv($key);
        unset($_ENV[$key], $_SERVER[$key]);

        return;
    }

    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

function cacheConfig(): array
{
    return require dirname(__DIR__, 2).'/config/cache.php';
}

test('cache config prefers CACHE_STORE when it is set', function () {
    $originalStore = getenv('CACHE_STORE') ?: null;
    $originalDriver = getenv('CACHE_DRIVER') ?: null;

    try {
        setEnvironmentValue('CACHE_STORE', 'redis');
        setEnvironmentValue('CACHE_DRIVER', 'file');

        expect(cacheConfig()['default'])->toBe('redis');
    } finally {
        setEnvironmentValue('CACHE_STORE', $originalStore);
        setEnvironmentValue('CACHE_DRIVER', $originalDriver);
    }
});

test('cache config falls back to CACHE_DRIVER for legacy environments', function () {
    $originalStore = getenv('CACHE_STORE') ?: null;
    $originalDriver = getenv('CACHE_DRIVER') ?: null;

    try {
        setEnvironmentValue('CACHE_STORE', null);
        setEnvironmentValue('CACHE_DRIVER', 'array');

        expect(cacheConfig()['default'])->toBe('array');
    } finally {
        setEnvironmentValue('CACHE_STORE', $originalStore);
        setEnvironmentValue('CACHE_DRIVER', $originalDriver);
    }
});
