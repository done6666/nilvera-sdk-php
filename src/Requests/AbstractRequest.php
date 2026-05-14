<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Contracts\RequestInterface;

abstract readonly class AbstractRequest implements RequestInterface
{
    abstract public function toArray(): array;

    /**
     * Remove null entries so optional fields are not sent in the JSON payload.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function filterNulls(array $data): array
    {
        return array_filter($data, static fn (mixed $value): bool => $value !== null);
    }
}
