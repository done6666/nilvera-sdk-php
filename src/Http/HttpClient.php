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
use Psr\Log\LoggerInterface;

class HttpClient
{
    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly ClientInterface $guzzle,
        private readonly Config $config,
    ) {
        $this->logger = $config->getLogger();
    }

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

    /** @param array<string, mixed> $data */
    public function delete(string $path, array $data = []): Response
    {
        return $this->request('DELETE', $path, $data !== [] ? ['json' => $data] : []);
    }

    /** @param array<string, mixed> $options */
    private function request(string $method, string $path, array $options = []): Response
    {
        $url     = rtrim($this->config->getBaseUrl(), '/') . '/' . ltrim($path, '/');
        $options = array_merge($this->defaultOptions(), $options);

        $maxAttempts = max(1, $this->config->getRetryAttempts() + 1);
        $delayMs     = $this->config->getRetryDelayMs();
        $attempt     = 0;
        $lastException = null;

        // Payload present for POST/PUT/PATCH; null for GET/DELETE.
        $payload = $options['json'] ?? null;

        while ($attempt < $maxAttempts) {
            if ($attempt > 0) {
                $wait = $delayMs * (2 ** ($attempt - 1));
                $this->logger->warning('Nilvera API retry', [
                    'attempt' => $attempt + 1,
                    'max'     => $maxAttempts,
                    'wait_ms' => $wait,
                    'url'     => $url,
                ]);
                usleep($wait * 1000);
            }

            $attempt++;

            $this->logger->debug('Nilvera API request', [
                'method'  => $method,
                'url'     => $url,
                'payload' => $payload,
            ]);

            $startTime = hrtime(true);

            try {
                $psrResponse = $this->guzzle->request($method, $url, $options);
                $statusCode  = $psrResponse->getStatusCode();
                $body        = (string) $psrResponse->getBody();
                $durationMs  = (int) round((hrtime(true) - $startTime) / 1_000_000);

                // Guzzle http_errors=>false modunda exception fırlatmaz;
                // durum kodunu burada kontrol ederek doğru exception'ı fırlatıyoruz.
                if ($statusCode >= 500) {
                    $lastException = ApiException::fromResponse($statusCode, $body)
                        ->withRequestContext($method, $url, $payload);

                    $this->logger->warning('Nilvera API server error', [
                        'status'      => $statusCode,
                        'url'         => $url,
                        'attempt'     => $attempt,
                        'duration_ms' => $durationMs,
                    ]);
                    continue;
                }

                if ($statusCode >= 400) {
                    $this->logger->error('Nilvera API client error', [
                        'status'      => $statusCode,
                        'url'         => $url,
                        'body'        => $body,
                        'duration_ms' => $durationMs,
                    ]);
                    throw match ($statusCode) {
                        401, 403 => AuthenticationException::fromResponse($statusCode, $body)
                                        ->withRequestContext($method, $url, $payload),
                        404      => NotFoundException::fromResponse($statusCode, $body)
                                        ->withRequestContext($method, $url, $payload),
                        409      => ConflictException::fromResponse($statusCode, $body)
                                        ->withRequestContext($method, $url, $payload),
                        422      => ValidationException::fromResponse($statusCode, $body)
                                        ->withRequestContext($method, $url, $payload),
                        default  => ApiException::fromResponse($statusCode, $body)
                                        ->withRequestContext($method, $url, $payload),
                    };
                }

                $this->logger->debug('Nilvera API response', [
                    'status'      => $statusCode,
                    'url'         => $url,
                    'duration_ms' => $durationMs,
                    'body'        => $body,
                ]);

                return new Response($statusCode, $body, $psrResponse->getHeaders());

            } catch (ClientException $e) {
                // http_errors=>true modunda Guzzle'ın fırlattığı 4xx exception'ları
                $statusCode = $e->getResponse()->getStatusCode();
                $body       = (string) $e->getResponse()->getBody();
                $durationMs = (int) round((hrtime(true) - $startTime) / 1_000_000);

                $this->logger->error('Nilvera API client error', [
                    'status'      => $statusCode,
                    'url'         => $url,
                    'body'        => $body,
                    'duration_ms' => $durationMs,
                ]);

                throw match ($statusCode) {
                    401, 403 => AuthenticationException::fromResponse($statusCode, $body)
                                    ->withRequestContext($method, $url, $payload),
                    404      => NotFoundException::fromResponse($statusCode, $body)
                                    ->withRequestContext($method, $url, $payload),
                    409      => ConflictException::fromResponse($statusCode, $body)
                                    ->withRequestContext($method, $url, $payload),
                    422      => ValidationException::fromResponse($statusCode, $body)
                                    ->withRequestContext($method, $url, $payload),
                    default  => ApiException::fromResponse($statusCode, $body)
                                    ->withRequestContext($method, $url, $payload),
                };

            } catch (ServerException $e) {
                // http_errors=>true modunda Guzzle'ın fırlattığı 5xx exception'ları
                $statusCode    = $e->getResponse()->getStatusCode();
                $body          = (string) $e->getResponse()->getBody();
                $durationMs    = (int) round((hrtime(true) - $startTime) / 1_000_000);
                $lastException = ApiException::fromResponse($statusCode, $body)
                    ->withRequestContext($method, $url, $payload);

                $this->logger->warning('Nilvera API server error', [
                    'status'      => $statusCode,
                    'url'         => $url,
                    'attempt'     => $attempt,
                    'duration_ms' => $durationMs,
                ]);

            } catch (ConnectException $e) {
                // Baglanti hatasi — yeniden denenebilir
                $lastException = new ApiException('Connection failed: ' . $e->getMessage(), 0, null, $e);
                $lastException->withRequestContext($method, $url, $payload);

                $this->logger->warning('Nilvera API connection error', [
                    'url'     => $url,
                    'attempt' => $attempt,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        throw $lastException ?? new ApiException('Request failed after retries.', 0);
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
