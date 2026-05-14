<?php

declare(strict_types=1);

namespace Nilvera\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Nilvera\Config;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\AuthenticationException;
use Nilvera\Exception\ConflictException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Exception\ValidationException;

class HttpClient
{
    public function __construct(
        private readonly ClientInterface $guzzle,
        private readonly Config $config,
    ) {}

    /** @param array<string, mixed> $query */
    public function get(string $path, array $query = []): Response
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    /** @param array<string, mixed> $data */
    public function post(string $path, array $data = []): Response
    {
        return $this->request('POST', $path, ['json' => $data]);
    }

    /** @param array<string, mixed> $data */
    public function put(string $path, array $data = []): Response
    {
        return $this->request('PUT', $path, ['json' => $data]);
    }

    /** @param array<string, mixed> $data */
    public function patch(string $path, array $data = []): Response
    {
        return $this->request('PATCH', $path, ['json' => $data]);
    }

    public function delete(string $path): Response
    {
        return $this->request('DELETE', $path);
    }

    /** @param array<string, mixed> $options */
    private function request(string $method, string $path, array $options = []): Response
    {
        $url     = rtrim($this->config->getBaseUrl(), '/') . '/' . ltrim($path, '/');
        $options = array_merge($this->defaultOptions(), $options);

        try {
            $psrResponse = $this->guzzle->request($method, $url, $options);

            return new Response(
                $psrResponse->getStatusCode(),
                (string) $psrResponse->getBody(),
                $psrResponse->getHeaders(),
            );
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body       = (string) $e->getResponse()->getBody();

            throw match ($statusCode) {
                401, 403 => AuthenticationException::fromResponse($statusCode, $body),
                404      => NotFoundException::fromResponse($statusCode, $body),
                409      => ConflictException::fromResponse($statusCode, $body),
                422      => ValidationException::fromResponse($statusCode, $body),
                default  => ApiException::fromResponse($statusCode, $body),
            };
        } catch (ServerException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body       = (string) $e->getResponse()->getBody();

            throw ApiException::fromResponse($statusCode, $body);
        } catch (ConnectException $e) {
            throw new ApiException('Connection failed: ' . $e->getMessage(), 0, null, $e);
        }
    }

    /** @return array<string, mixed> */
    private function defaultOptions(): array
    {
        return [
            'timeout'         => $this->config->getTimeout(),
            'connect_timeout' => $this->config->getConnectTimeout(),
            'headers'         => [
                'Authorization' => 'Bearer ' . $this->config->getApiKey(),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
        ];
    }
}
