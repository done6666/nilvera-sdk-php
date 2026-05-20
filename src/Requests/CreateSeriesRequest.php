<?php

declare(strict_types=1);

namespace Nilvera\Requests;

/**
 * Yeni seri oluşturma isteği — POST /earchive/Series
 *
 * API şeması: CreateSerieCommand { Name, IsActive, IsDefault }
 */
readonly class CreateSeriesRequest extends AbstractRequest
{
    public function __construct(
        public string $name,
        public bool $isActive = true,
        public bool $isDefault = false,
    ) {
        if (strlen($this->name) !== 3) {
            throw new \InvalidArgumentException('Seri adı (name) tam olarak 3 karakter olmalıdır.');
        }
    }

    public function toArray(): array
    {
        return [
            'Name'      => $this->name,
            'IsActive'  => $this->isActive,
            'IsDefault' => $this->isDefault,
        ];
    }
}
