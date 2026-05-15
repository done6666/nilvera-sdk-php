<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Enums\DespatchProfile;
use Nilvera\Enums\DespatchType;
use Nilvera\Requests\ValueObjects\AdditionalDocumentReferenceRequest;
use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ShipmentDetailRequest;
use Nilvera\Requests\ValueObjects\WaybillOrderReferenceRequest;
use Nilvera\Requests\ValueObjects\WaybillPartyRequest;

/**
 * e-İrsaliye gönderme isteği — POST /edespatch/Send/Model
 *
 * toArray() çıktısı:
 * {
 *   "CustomerAlias": "urn:mail:defaultpk@nilvera.com",
 *   "EDespatch": {
 *     "DespatchInfo":             { ... },
 *     "DeliveryCustomerInfo":     { ... },
 *     "DespatchSupplierInfo":     { ... },  // opsiyonel
 *     "BuyerCustomerInfo":        { ... },  // opsiyonel
 *     "SellerSupplierInfo":       { ... },  // opsiyonel
 *     "OriginatorCustomerInfo":   { ... },  // opsiyonel
 *     "DespatchLines":            [ ... ],
 *     "ShipmentDetail":           { ... },  // opsiyonel
 *     "OrderReference":           { ... },  // opsiyonel
 *     "AdditionalDocumentReference": [ ... ], // opsiyonel
 *     "Notes":                    [ "..." ] // opsiyonel
 *   }
 * }
 */
readonly class SendWaybillRequest extends AbstractRequest
{
    /**
     * @param DespatchLineRequest[]                $despatchLines                En az bir kalem zorunludur
     * @param AdditionalDocumentReferenceRequest[] $additionalDocumentReferences Ek belgeler
     * @param string[]                             $notes
     */
    public function __construct(
        /** GIB sisteminde kayıtlı alıcı için alias (orn. "urn:mail:defaultpk@nilvera.com") */
        public string $customerAlias,
        /** Alıcı teslimat bilgileri — DeliveryCustomerInfo */
        public ReceiverRequest $customerInfo,
        public array $despatchLines,
        public \DateTimeImmutable $issueDate,
        public DespatchType $despatchType = DespatchType::Sevk,
        public DespatchProfile $despatchProfile = DespatchProfile::TemelIrsaliye,
        /** 16 haneli irsaliye numarası veya 3 haneli seri kodu */
        public ?string $despatchSerieOrNumber = null,
        public ?\DateTimeImmutable $actualDespatchDateTime = null,
        public string $currencyCode = 'TRY',
        /** Ödenecek tutar */
        public ?float $payableAmount = null,
        public ?string $uuid = null,
        public ?string $templateUuid = null,
        /** Base64 kodlanmış özel şablon */
        public ?string $templateBase64String = null,
        /** Matbu (kağıt) irsaliye tarihi — MATBUDAN profili için */
        public ?\DateTimeImmutable $matbuIssueDate = null,
        /** Matbu irsaliye numarası — MATBUDAN profili için */
        public ?string $matbuNumber = null,
        /** Sevkiyat/taşıma numarası */
        public ?string $shipmentNumber = null,
        /** Satıcı/gönderici taraf — DespatchSupplierInfo */
        public ?WaybillPartyRequest $despatchSupplierInfo = null,
        /** Alıcı müşteri — BuyerCustomerInfo */
        public ?WaybillPartyRequest $buyerCustomerInfo = null,
        /** Satıcı tedarikçi — SellerSupplierInfo */
        public ?WaybillPartyRequest $sellerSupplierInfo = null,
        /** Başlangıç müşterisi — OriginatorCustomerInfo */
        public ?WaybillPartyRequest $originatorCustomerInfo = null,
        public ?ShipmentDetailRequest $shipmentDetail = null,
        public ?WaybillOrderReferenceRequest $orderReference = null,
        public array $additionalDocumentReferences = [],
        public array $notes = [],
    ) {
        if ($this->despatchLines === []) {
            throw new \InvalidArgumentException('İrsaliyede en az bir kalem bulunmalıdır.');
        }

        foreach ($this->despatchLines as $i => $line) {
            if (!$line instanceof DespatchLineRequest) {
                throw new \InvalidArgumentException(
                    "despatchLines[{$i}] bir DespatchLineRequest nesnesi olmalıdır."
                );
            }
        }

        foreach ($this->additionalDocumentReferences as $i => $ref) {
            if (!$ref instanceof AdditionalDocumentReferenceRequest) {
                throw new \InvalidArgumentException(
                    "additionalDocumentReferences[{$i}] bir AdditionalDocumentReferenceRequest nesnesi olmalıdır."
                );
            }
        }
    }

    public function toArray(): array
    {
        $despatchInfo = $this->filterNulls([
            'UUID'                   => $this->uuid,
            'TemplateUUID'           => $this->templateUuid,
            'TemplateBase64String'   => $this->templateBase64String,
            'DespatchType'           => $this->despatchType->value,
            'DespatchProfile'        => $this->despatchProfile->value,
            'DespatchSerieOrNumber'  => $this->despatchSerieOrNumber,
            'IssueDate'              => $this->issueDate->format('Y-m-d\TH:i:s'),
            'ActualDespatchDateTime' => $this->actualDespatchDateTime?->format('Y-m-d\TH:i:s'),
            'CurrencyCode'           => $this->currencyCode,
            'PayableAmount'          => $this->payableAmount,
            'MatbuIssueDate'         => $this->matbuIssueDate?->format('Y-m-d\TH:i:s'),
            'MatbuNumber'            => $this->matbuNumber,
            'ShipmentNumber'         => $this->shipmentNumber,
        ]);

        $eDespatch = $this->filterNulls([
            'DespatchInfo'           => $despatchInfo,
            'DeliveryCustomerInfo'   => $this->customerInfo->toArray(),
            'DespatchSupplierInfo'   => $this->despatchSupplierInfo?->toArray(),
            'BuyerCustomerInfo'      => $this->buyerCustomerInfo?->toArray(),
            'SellerSupplierInfo'     => $this->sellerSupplierInfo?->toArray(),
            'OriginatorCustomerInfo' => $this->originatorCustomerInfo?->toArray(),
        ]);

        $eDespatch['DespatchLines'] = array_map(
            static fn (DespatchLineRequest $l) => $l->toArray(),
            $this->despatchLines,
        );

        if ($this->shipmentDetail !== null) {
            $eDespatch['ShipmentDetail'] = $this->shipmentDetail->toArray();
        }

        if ($this->orderReference !== null) {
            $eDespatch['OrderReference'] = $this->orderReference->toArray();
        }

        if ($this->additionalDocumentReferences !== []) {
            $eDespatch['AdditionalDocumentReference'] = array_map(
                static fn (AdditionalDocumentReferenceRequest $r) => $r->toArray(),
                $this->additionalDocumentReferences,
            );
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
