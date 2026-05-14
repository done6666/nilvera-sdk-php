<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration;

use Nilvera\Exception\AuthenticationException;
use Nilvera\NilveraClient;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class AuthTest extends IntegrationTestCase
{
    public function test_invalid_api_key_throws_authentication_exception(): void
    {
        $client = NilveraClient::test('invalid-api-key-that-does-not-exist');

        $this->expectException(AuthenticationException::class);

        $client->general()->getCompany();
    }
}
