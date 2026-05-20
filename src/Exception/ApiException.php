<?php

declare(strict_types=1);

namespace Nilvera\Exception;

use Throwable;

class ApiException extends NilveraException
{
    // Set after construction by HttpClient via withRequestContext().
    private ?string $requestMethod  = null;
    private ?string $requestUrl     = null;
    private mixed   $requestPayload = null;

    public function __construct(
        string $message,
        private readonly int $statusCode,
        private readonly mixed $responseBody = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): mixed
    {
        return $this->responseBody;
    }

    public function getRequestMethod(): ?string
    {
        return $this->requestMethod;
    }

    public function getRequestUrl(): ?string
    {
        return $this->requestUrl;
    }

    public function getRequestPayload(): mixed
    {
        return $this->requestPayload;
    }

    /**
     * Attach the originating HTTP request details to this exception.
     * Called by HttpClient before throwing; returns static to preserve the concrete subtype.
     *
     * @param array<string, mixed>|null $payload
     */
    public function withRequestContext(string $method, string $url, ?array $payload = null): static
    {
        $this->requestMethod  = $method;
        $this->requestUrl     = $url;
        $this->requestPayload = $payload;

        return $this;
    }

    /**
     * Return a human-readable dump of all available debug information.
     * Useful for CLI output or logging during development.
     */
    public function toDebugString(): string
    {
        $lines = [
            '=== Nilvera API Exception ===',
            'Class   : ' . static::class,
            'Message : ' . $this->getMessage(),
            'Status  : ' . $this->statusCode,
        ];

        if ($this->requestMethod !== null && $this->requestUrl !== null) {
            $lines[] = 'Request : ' . $this->requestMethod . ' ' . $this->requestUrl;
        }

        if ($this->requestPayload !== null) {
            $lines[] = 'Payload : ' . json_encode(
                $this->requestPayload,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        if ($this->responseBody !== null) {
            $lines[] = 'Response: ' . json_encode(
                $this->responseBody,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        return implode(PHP_EOL, $lines);
    }

    public static function fromResponse(int $statusCode, string $body): static
    {
        $decoded = json_decode($body, true);
        $message = $decoded['message'] ?? $decoded['title'] ?? "HTTP {$statusCode}";

        return new static($message, $statusCode, $decoded);
    }
}
