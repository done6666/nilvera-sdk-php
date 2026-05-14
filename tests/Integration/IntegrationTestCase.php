<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration;

use Nilvera\NilveraClient;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected NilveraClient $client;

    protected function setUp(): void
    {
        $apiKey = getenv('NILVERA_INTEGRATION_API_KEY');

        if (empty($apiKey)) {
            $this->markTestSkipped(
                'Integration tests require NILVERA_INTEGRATION_API_KEY env var. ' .
                'Copy .env.integration.example → .env.integration and set your test API key.'
            );
        }

        $this->client = NilveraClient::test($apiKey);
    }
}
