<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Fatura dönemi bilgisi — API'deki InvoicePeriodDto.
 *
 * Abonelik, kira veya dönemsel hizmet faturalarında fatura dönemini belirtmek için kullanılır.
 */
readonly class InvoicePeriodRequest extends AbstractRequest
{
    public function __construct(
        public ?\DateTimeImmutable $startDate = null,
        public ?string $startTime = null,
        public ?\DateTimeImmutable $endDate = null,
        public ?string $endTime = null,
        public ?float $durationMeasureValue = null,
        public ?string $description = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'StartDate'            => $this->startDate?->format('Y-m-d\TH:i:s\Z'),
            'StartTime'            => $this->startTime,
            'EndDate'              => $this->endDate?->format('Y-m-d\TH:i:s\Z'),
            'EndTime'              => $this->endTime,
            'DurationMeasureValue' => $this->durationMeasureValue,
            'Description'          => $this->description,
        ]);
    }
}
