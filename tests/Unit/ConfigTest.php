<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit;

use Nilvera\Config;
use Nilvera\Enums\Environment;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test_live_factory_sets_live_environment(): void
    {
        $config = Config::live('my-key');

        $this->assertSame('my-key', $config->getApiKey());
        $this->assertSame(Environment::Live, $config->getEnvironment());
        $this->assertSame('https://api.nilvera.com', $config->getBaseUrl());
        $this->assertFalse($config->isTestMode());
    }

    public function test_test_factory_sets_test_environment(): void
    {
        $config = Config::test('my-key');

        $this->assertSame(Environment::Test, $config->getEnvironment());
        $this->assertSame('https://apitest.nilvera.com', $config->getBaseUrl());
        $this->assertTrue($config->isTestMode());
    }

    public function test_default_timeouts(): void
    {
        $config = Config::live('my-key');

        $this->assertSame(30, $config->getTimeout());
        $this->assertSame(10, $config->getConnectTimeout());
    }

    public function test_with_timeout_returns_new_instance(): void
    {
        $original = Config::live('my-key');
        $modified = $original->withTimeout(60);

        $this->assertSame(30, $original->getTimeout());
        $this->assertSame(60, $modified->getTimeout());
        $this->assertNotSame($original, $modified);
    }

    public function test_with_connect_timeout_returns_new_instance(): void
    {
        $original = Config::live('my-key');
        $modified = $original->withConnectTimeout(5);

        $this->assertSame(10, $original->getConnectTimeout());
        $this->assertSame(5, $modified->getConnectTimeout());
    }
}
