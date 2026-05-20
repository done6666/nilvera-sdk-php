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

    /**
     * Return all validation errors as a single formatted string.
     * Each line: [FieldName] error message one, error message two
     */
    public function getSummary(): string
    {
        $errors = $this->getErrors();

        if ($errors === []) {
            return '';
        }

        $lines = [];
        foreach ($errors as $field => $messages) {
            $lines[] = "[{$field}] " . implode(', ', (array) $messages);
        }

        return implode(PHP_EOL, $lines);
    }
}
