<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Irsaliyede siparis referansi — EDespatch.OrderReference.
 * Eklenirse ID ve IssueDate zorunludur.
 */
readonly class WaybillOrderReferenceRequest extends AbstractRequest
{
    public function __construct(
        public string $id,
        public \DateTimeImmutable $issueDate,
    ) {}

    public function toArray(): array
    {
        return [
            'ID'        => $this->id,
            'IssueDate' => $this->issueDate->format('Y-m-d\TH:i:s'),
        ];
    }
}
