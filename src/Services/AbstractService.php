<?php

declare(strict_types=1);

namespace Nilvera\Services;

use Nilvera\Http\HttpClient;
use Nilvera\Http\Response;

abstract class AbstractService
{
    public function __construct(protected readonly HttpClient $client) {}

    /** @param array<string, mixed> $query */
    protected function get(string $path, array $query = []): Response
    {
        return $this->client->get($path, array_filter($query, fn ($v) => $v !== null));
    }

    /** @param array<string, mixed> $data */
    protected function post(string $path, array $data = []): Response
    {
        return $this->client->post($path, $data);
    }

    /** @param array<string, mixed> $data */
    protected function put(string $path, array $data = []): Response
    {
        return $this->client->put($path, $data);
    }

    /** @param array<string, mixed> $data */
    protected function patch(string $path, array $data = []): Response
    {
        return $this->client->patch($path, $data);
    }

    protected function delete(string $path): Response
    {
        return $this->client->delete($path);
    }
}
