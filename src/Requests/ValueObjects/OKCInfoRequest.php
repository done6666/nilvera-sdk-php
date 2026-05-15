<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Ödeme kaydedici cihaz (ÖKC) ve fiş bilgisi — API'deki OKCInfoDto.
 *
 * DocumentDescription için alabileceği değerler OKC Fiş Tipleri listesine bakın.
 */
readonly class OKCInfoRequest extends AbstractRequest
{
    public function __construct(
        public ?string $id = null,
        public ?\DateTimeImmutable $issueDate = null,
        public ?string $time = null,
        public ?string $zNo = null,
        public ?string $endPointId = null,
        public ?string $documentDescription = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'ID'                  => $this->id,
            'IssueDate'           => $this->issueDate?->format('Y-m-d\TH:i:s\Z'),
            'Time'                => $this->time,
            'ZNo'                 => $this->zNo,
            'EndPointID'          => $this->endPointId,
            'DocumentDescription' => $this->documentDescription,
        ]);
    }
}
