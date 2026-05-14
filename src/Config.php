<?php

declare(strict_types=1);

namespace Nilvera;

use Nilvera\Enums\Environment;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Config
{
    public function __construct(
        private readonly string $apiKey,
        private readonly Environment $environment = Environment::Live,
        private readonly int $timeout = 30,
        private readonly int $connectTimeout = 10,
        private readonly LoggerInterface $logger = new NullLogger(),
        /** Kac kez yeniden deneme yapilacak (0 = devre disi) */
        private readonly int $retryAttempts = 2,
        /** Yeniden denemeler arasindaki bekleme suresi (ms); her denemede ikiye katlanir */
        private readonly int $retryDelayMs = 500,
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

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getRetryAttempts(): int
    {
        return $this->retryAttempts;
    }

    public function getRetryDelayMs(): int
    {
        return $this->retryDelayMs;
    }

    public function isTestMode(): bool
    {
        return $this->environment === Environment::Test;
    }

    public function withTimeout(int $timeout): self
    {
        return new self($this->apiKey, $this->environment, $timeout, $this->connectTimeout, $this->logger, $this->retryAttempts, $this->retryDelayMs);
    }

    public function withConnectTimeout(int $connectTimeout): self
    {
        return new self($this->apiKey, $this->environment, $this->timeout, $connectTimeout, $this->logger, $this->retryAttempts, $this->retryDelayMs);
    }

    public function withLogger(LoggerInterface $logger): self
    {
        return new self($this->apiKey, $this->environment, $this->timeout, $this->connectTimeout, $logger, $this->retryAttempts, $this->retryDelayMs);
    }

    /**
     * @param int $attempts  Maksimum deneme sayisi (ilk istek haric); 0 = kapal
     * @param int $delayMs   Ilk bekleme suresi (ms); her denemede ikiye katlanir
     */
    public function withRetry(int $attempts = 2, int $delayMs = 500): self
    {
        return new self($this->apiKey, $this->environment, $this->timeout, $this->connectTimeout, $this->logger, $attempts, $delayMs);
    }
}
