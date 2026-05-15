<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * İrsaliyede sipariş referansı — EDespatch.OrderReference.
 * Eklenirse ID ve IssueDate zorunludur.
 */
readonly class WaybillOrderReferenceRequest extends AbstractRequest
{
    public function __construct(
        public string $id,
        public \DateTimeImmutable $issueDate,
        /** Referans belge — opsiyonel ek belge bilgisi */
        public ?AdditionalDocumentReferenceRequest $documentReference = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'ID'        => $this->id,
            'IssueDate' => $this->issueDate->format('Y-m-d\TH:i:s'),
        ];

        if ($this->documentReference !== null) {
            $data['DocumentReference'] = $this->documentReference->toArray();
        }

        return $data;
    }
}
