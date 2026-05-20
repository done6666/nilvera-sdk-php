<?php

declare(strict_types=1);

namespace Nilvera\Requests;

/**
 * Seri güncelleme isteği — PUT /earchive/Series
 *
 * API şeması: UpdateSerieCommand { ID, IsDefault, IsActive }
 * Her iki alan da zorunludur; API dokümantasyonu nullable gösterse de
 * gerçekte her ikisi de gönderilmezse 400 döner.
 */
readonly class UpdateSeriesRequest extends AbstractRequest
{
    public function __construct(
        public int $id,
        public bool $isDefault,
        public bool $isActive,
    ) {}

    public function toArray(): array
    {
        return [
            'ID'        => $this->id,
            'IsDefault' => $this->isDefault,
            'IsActive'  => $this->isActive,
        ];
    }
}
