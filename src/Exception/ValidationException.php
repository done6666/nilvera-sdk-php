<?php

declare(strict_types=1);

namespace Nilvera\Exception;

class ValidationException extends ApiException
{
    /** @return array<string, string[]> */
    public function getErrors(): array
    {
        $body = $this->getResponseBody();

        return $body['errors'] ?? [];
    }
}
