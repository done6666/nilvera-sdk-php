<?php

declare(strict_types=1);

namespace Nilvera\Http;

class Response
{
    public function __construct(
        private readonly int $statusCode,
        private readonly string $body,
        private readonly array $headers = [],
    ) {}

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /** @return array<string, mixed> */
    public function json(): array
    {
        if ($this->body === '' || $this->body === 'null') {
            return [];
        }

        return json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)][0] ?? null;
    }
}
