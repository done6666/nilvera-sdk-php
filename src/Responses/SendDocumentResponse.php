<?php

declare(strict_types=1);

namespace Nilvera\Responses;

/**
 * e-Fatura, e-Arsiv vb. belge gonderme islemlerinin tipli yaniti.
 * POST /einvoice/Send/Model, POST /earchive/Send/Model vb. icin kullanilir.
 */
readonly class SendDocumentResponse
{
    public function __construct(
        public string $uuid,
        public ?string $invoiceNumber,
    ) {}

    /** @param array{UUID: string, InvoiceNumber?: string|null} $data */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['UUID'] ?? '',
            invoiceNumber: $data['InvoiceNumber'] ?? null,
        );
    }
}
