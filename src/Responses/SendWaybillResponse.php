<?php

declare(strict_types=1);

namespace Nilvera\Responses;

/**
 * e-Irsaliye gonderme isleminin tipli yaniti.
 * POST /edespatch/Send/Model icin kullanilir.
 */
readonly class SendWaybillResponse
{
    public function __construct(
        public string $uuid,
        public string $despatchNumber,
    ) {}

    /** @param array{UUID: string, DespatchNumber: string} $data */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid:           $data['UUID'] ?? '',
            despatchNumber: $data['DespatchNumber'] ?? '',
        );
    }
}
