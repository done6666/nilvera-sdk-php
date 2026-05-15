<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * İade edilen fatura bilgisi — API'deki ReturnInvoiceInfoDto.
 *
 * Fatura tipi IADE olduğunda, iade edilen orijinal fatura numarası ve tarihi girilir.
 * Birden fazla fatura iade ediliyorsa her biri için ayrı bir nesne oluşturulur.
 */
readonly class ReturnInvoiceInfoRequest extends AbstractRequest
{
    public function __construct(
        public string $invoiceNumber,
        public \DateTimeImmutable $issueDate,
    ) {
        if (trim($this->invoiceNumber) === '') {
            throw new \InvalidArgumentException('InvoiceNumber boş olamaz.');
        }
    }

    public function toArray(): array
    {
        return [
            'InvoiceNumber' => $this->invoiceNumber,
            'IssueDate'     => $this->issueDate->format('Y-m-d\TH:i:s\Z'),
        ];
    }
}
