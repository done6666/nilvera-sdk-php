<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Nilvera\Config;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\AuthenticationException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Exception\ValidationException;
use Nilvera\Http\HttpClient;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    private ClientInterface $guzzle;
    private HttpClient $client;

    protected function setUp(): void
    {
        $this->guzzle = $this->createMock(ClientInterface::class);
        $this->client = new HttpClient($this->guzzle, Config::test('test-key'));
    }

    public function test_get_returns_response(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/einvoice/Sale', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"items":[]}'));

        $response = $this->client->get('/einvoice/Sale');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['items' => []], $response->json());
    }

    public function test_post_sends_json_body(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', $this->anything(), $this->callback(fn ($opts) => isset($opts['json']['EInvoice'])))
            ->willReturn(new GuzzleResponse(200, [], '{"UUID":"abc-123","InvoiceNumber":null}'));

        $response = $this->client->post('/einvoice/Send/Model', ['EInvoice' => []]);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_throws_authentication_exception_on_403(): void
    {
        $this->expectException(AuthenticationException::class);

        $psrRequest  = new Request('GET', 'https://apitest.nilvera.com/einvoice/Sale');
        $psrResponse = new GuzzleResponse(403, [], '{"message":"Forbidden"}');

        $this->guzzle->method('request')
            ->willThrowException(new ClientException('Forbidden', $psrRequest, $psrResponse));

        $this->client->get('/einvoice/Sale');
    }

    public function test_throws_not_found_exception_on_404(): void
    {
        $this->expectException(NotFoundException::class);

        $psrRequest  = new Request('GET', 'https://apitest.nilvera.com/einvoice/Sale/unknown');
        $psrResponse = new GuzzleResponse(404, [], '{"message":"Not found"}');

        $this->guzzle->method('request')
            ->willThrowException(new ClientException('Not found', $psrRequest, $psrResponse));

        $this->client->get('/einvoice/Sale/unknown');
    }

    public function test_throws_validation_exception_on_422(): void
    {
        $this->expectException(ValidationException::class);

        $psrRequest  = new Request('POST', 'https://apitest.nilvera.com/einvoice/Send/Model');
        $psrResponse = new GuzzleResponse(422, [], '{"message":"Invalid","errors":{"UUID":["Required"]}}');

        $this->guzzle->method('request')
            ->willThrowException(new ClientException('Unprocessable', $psrRequest, $psrResponse));

        $this->client->post('/einvoice/Send/Model');
    }

    public function test_bearer_token_is_set_in_headers(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with($this->anything(), $this->anything(), $this->callback(
                fn ($opts) => ($opts['headers']['Authorization'] ?? '') === 'Bearer test-key'
            ))
            ->willReturn(new GuzzleResponse(200, [], '[]'));

        $this->client->get('/einvoice/Sale');
    }

    public function test_throws_api_exception_on_500(): void
    {
        $this->expectException(ApiException::class);

        $psrRequest  = new Request('GET', 'https://apitest.nilvera.com/einvoice/Sale');
        $psrResponse = new GuzzleResponse(500, [], '{"message":"Internal Server Error"}');

        $this->guzzle->method('request')
            ->willThrowException(new \GuzzleHttp\Exception\ServerException('Server error', $psrRequest, $psrResponse));

        $this->client->get('/einvoice/Sale');
    }
}
