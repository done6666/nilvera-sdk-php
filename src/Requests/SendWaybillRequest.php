<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Enums\DespatchProfile;
use Nilvera\Enums\DespatchType;
use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ShipmentDetailRequest;
use Nilvera\Requests\ValueObjects\WaybillOrderReferenceRequest;

/**
 * e-Irsaliye gonderme istegi — POST /edespatch/Send/Model
 *
 * toArray() ciktisi:
 * {
 *   "CustomerAlias": "urn:mail:defaultpk@nilvera.com",
 *   "EDespatch": {
 *     "DespatchInfo":         { ... },
 *     "DeliveryCustomerInfo": { ... },
 *     "DespatchLines":        [ ... ],
 *     "ShipmentDetail":       { ... },  // opsiyonel
 *     "OrderReference":       { ... },  // opsiyonel
 *     "Notes":                [ "..." ] // opsiyonel
 *   }
 * }
 */
readonly class SendWaybillRequest extends AbstractRequest
{
    /**
     * @param DespatchLineRequest[] $despatchLines En az bir kalem zorunludur
     * @param string[]              $notes
     */
    public function __construct(
        /** GIB sisteminde kayitli alici icin alias (orn. "urn:mail:defaultpk@nilvera.com") */
        public string $customerAlias,
        /** Alici bilgileri — DeliveryCustomerInfo */
        public ReceiverRequest $customerInfo,
        public array $despatchLines,
        public \DateTimeImmutable $issueDate,
        public DespatchType $despatchType = DespatchType::Sevk,
        public DespatchProfile $despatchProfile = DespatchProfile::TemelIrsaliye,
        /** 16 haneli irsaliye numarasi veya 3 haneli seri kodu */
        public ?string $despatchSerieOrNumber = null,
        public ?\DateTimeImmutable $actualDespatchDateTime = null,
        public string $currencyCode = 'TRY',
        public ?string $uuid = null,
        public ?string $templateUuid = null,
        public ?ShipmentDetailRequest $shipmentDetail = null,
        public ?WaybillOrderReferenceRequest $orderReference = null,
        public array $notes = [],
    ) {
        if ($this->despatchLines === []) {
            throw new \InvalidArgumentException('Irsaliyede en az bir kalem bulunmalidir.');
        }

        foreach ($this->despatchLines as $i => $line) {
            if (!$line instanceof DespatchLineRequest) {
                throw new \InvalidArgumentException(
                    "despatchLines[{$i}] bir DespatchLineRequest nesnesi olmalidir."
                );
            }
        }
    }

    public function toArray(): array
    {
        $despatchInfo = $this->filterNulls([
            'UUID'                   => $this->uuid,
            'TemplateUUID'           => $this->templateUuid,
            'DespatchType'           => $this->despatchType->value,
            'DespatchProfile'        => $this->despatchProfile->value,
            'DespatchSerieOrNumber'  => $this->despatchSerieOrNumber,
            'IssueDate'              => $this->issueDate->format('Y-m-d\TH:i:s'),
            'ActualDespatchDateTime' => $this->actualDespatchDateTime?->format('Y-m-d\TH:i:s'),
            'CurrencyCode'           => $this->currencyCode,
        ]);

        $eDespatch = [
            'DespatchInfo'         => $despatchInfo,
            'DeliveryCustomerInfo' => $this->customerInfo->toArray(),
            'DespatchLines'        => array_map(
                static fn (DespatchLineRequest $l) => $l->toArray(),
                $this->despatchLines,
            ),
        ];

        if ($this->shipmentDetail !== null) {
            $eDespatch['ShipmentDetail'] = $this->shipmentDetail->toArray();
        }

        if ($this->orderReference !== null) {
            $eDespatch['OrderReference'] = $this->orderReference->toArray();
        }

        if ($this->notes !== []) {
            $eDespatch['Notes'] = $this->notes;
        }

        return [
            'CustomerAlias' => $this->customerAlias,
            'EDespatch'     => $eDespatch,
        ];
    }
}
