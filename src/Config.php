<?php

declare(strict_types=1);

namespace Nilvera;

use Nilvera\Enums\Environment;

class Config
{
    public function __construct(
        private readonly string $apiKey,
        private readonly Environment $environment = Environment::Live,
        private readonly int $timeout = 30,
        private readonly int $connectTimeout = 10,
    ) {}

    public static function live(string $apiKey): self
    {
        return new self($apiKey, Environment::Live);
    }

    public static function test(string $apiKey): self
    {
        return new self($apiKey, Environment::Test);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getBaseUrl(): string
    {
        return $this->environment->baseUrl();
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    public function isTestMode(): bool
    {
        return $this->environment === Environment::Test;
    }

    public function withTimeout(int $timeout): self
    {
        return new self($this->apiKey, $this->environment, $timeout, $this->connectTimeout);
    }

    public function withConnectTimeout(int $connectTimeout): self
    {
        return new self($this->apiKey, $this->environment, $this->timeout, $connectTimeout);
    }
}
