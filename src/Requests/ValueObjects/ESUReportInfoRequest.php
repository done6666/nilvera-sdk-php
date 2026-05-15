<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * ESU (Elektrikli Şarj Ünitesi) rapor bilgisi — API'deki ESUReportInfoDto.
 *
 * Fatura tipi SARJ (EV şarj faturası) olduğunda kullanılır.
 */
readonly class ESUReportInfoRequest extends AbstractRequest
{
    public function __construct(
        public ?string $id = null,
        public ?\DateTimeImmutable $issueDate = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'ID'        => $this->id,
            'IssueDate' => $this->issueDate?->format('Y-m-d\TH:i:s\Z'),
        ]);
    }
}
