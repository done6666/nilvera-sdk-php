<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Enums\UnitType;
use Nilvera\Requests\AbstractRequest;

/**
 * e-Irsaliye kalemi — EDespatch.DespatchLines dizisinin bir elemani.
 */
readonly class DespatchLineRequest extends AbstractRequest
{
    public function __construct(
        /** Urun adi (zorunlu) */
        public string $name,
        /** Teslim edilen birim tipi (zorunlu) */
        public UnitType|string $deliveredUnitType,
        /** Teslim edilen miktar (zorunlu) */
        public float $deliveredQuantity,
        /** Satici urun kodu */
        public ?string $sellerCode = null,
        /** Alici urun kodu */
        public ?string $buyerCode = null,
        public ?string $description = null,
        /** Birim adi — okunabilir etiket (orn. "Adet") */
        public ?string $deliveredUnitName = null,
        /** Birim fiyat */
        public ?float $quantityPrice = null,
        /** Satir toplami */
        public ?float $lineTotal = null,
        public ?float $outstandingQuantity = null,
        public UnitType|string|null $outstandingUnitType = null,
        public ?string $outstandingUnitName = null,
        public ?string $outstandingReason = null,
        public ?string $manufacturerCode = null,
        public ?string $brandName = null,
        public ?string $modelName = null,
        /** IDIS senaryosunda zorunlu: 2 harf + 7 rakam */
        public ?string $labelNumber = null,
        /** Ek ürün kimlik bilgisi */
        public ?string $additionalItemIdentification = null,
    ) {
        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('Name bos olamaz.');
        }
        if ($this->deliveredQuantity < 0.0) {
            throw new \InvalidArgumentException('DeliveredQuantity negatif olamaz.');
        }
        if ($this->quantityPrice !== null && $this->quantityPrice < 0.0) {
            throw new \InvalidArgumentException('QuantityPrice negatif olamaz.');
        }
        if ($this->lineTotal !== null && $this->lineTotal < 0.0) {
            throw new \InvalidArgumentException('LineTotal negatif olamaz.');
        }
    }

    public function toArray(): array
    {
        $deliveredUnit = $this->deliveredUnitType instanceof UnitType
            ? $this->deliveredUnitType->value
            : $this->deliveredUnitType;

        $outstandingUnit = $this->outstandingUnitType instanceof UnitType
            ? $this->outstandingUnitType->value
            : $this->outstandingUnitType;

        return $this->filterNulls([
            'Name'                => $this->name,
            'DeliveredUnitType'   => $deliveredUnit,
            'DeliveredUnitName'   => $this->deliveredUnitName,
            'DeliveredQuantity'   => $this->deliveredQuantity,
            'SellerCode'          => $this->sellerCode,
            'BuyerCode'           => $this->buyerCode,
            'Description'         => $this->description,
            'QuantityPrice'       => $this->quantityPrice,
            'LineTotal'           => $this->lineTotal,
            'OutstandingQuantity' => $this->outstandingQuantity,
            'OutstandingUnitType' => $outstandingUnit,
            'OutstandingUnitName' => $this->outstandingUnitName,
            'OutstandingReason'   => $this->outstandingReason,
            'ManufacturerCode'              => $this->manufacturerCode,
            'BrandName'                     => $this->brandName,
            'ModelName'                     => $this->modelName,
            'LabelNumber'                   => $this->labelNumber,
            'AdditionalItemIdentification'  => $this->additionalItemIdentification,
        ]);
    }
}
