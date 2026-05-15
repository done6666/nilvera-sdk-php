<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Yatırım teşvik belgesi bilgileri — InvestmentIncentiveDto.
 *
 * InvoiceProfile::InvestmentIncentive (YATIRIMTESVIK) faturalarında
 * InvoiceInfo.InvestmentIncentive alanında kullanılır.
 */
readonly class InvestmentIncentiveRequest extends AbstractRequest
{
    public function __construct(
        /** Yatırım teşvik belge numarası */
        public string $documentNumber,
        /** Yatırım teşvik belgesinin tarihi */
        public \DateTimeImmutable $documentDate,
    ) {
        if (trim($this->documentNumber) === '') {
            throw new \InvalidArgumentException('DocumentNumber boş olamaz.');
        }
    }

    public function toArray(): array
    {
        return [
            'DocumentNumber' => $this->documentNumber,
            'DocumentDate'   => $this->documentDate->format('Y-m-d\TH:i:s\Z'),
        ];
    }
}
