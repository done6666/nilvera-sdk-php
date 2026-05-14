<?php

declare(strict_types=1);

namespace Nilvera\Exception;

use Throwable;

class ApiException extends NilveraException
{
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

    public static function fromResponse(int $statusCode, string $body): static
    {
        $decoded = json_decode($body, true);
        $message = $decoded['message'] ?? $decoded['title'] ?? "HTTP {$statusCode}";

        return new static($message, $statusCode, $decoded);
    }
}
