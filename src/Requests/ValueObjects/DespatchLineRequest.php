<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Enums\UnitType;
use Nilvera\Requests\AbstractRequest;

/**
 * e-Irsaliye kalemi — DespatchLine.
 */
readonly class DespatchLineRequest extends AbstractRequest
{
    public function __construct(
        /** Urun satici kodu (zorunlu) */
        public string $sellerCode,
        /** Birim fiyat */
        public float $quantityPrice,
        /** Satir toplami */
        public float $lineTotal,
        public ?string $name = null,
        public ?string $buyerCode = null,
        public ?string $description = null,
        public UnitType|string|null $deliveredUnitType = null,
        public ?string $deliveredUnitName = null,
        public ?float $deliveredQuantity = null,
        public ?float $outstandingQuantity = null,
        public UnitType|string|null $outstandingUnitType = null,
        public ?string $outstandingUnitName = null,
        public ?string $outstandingReason = null,
        public ?string $manufacturerCode = null,
        public ?string $brandName = null,
        public ?string $modelName = null,
        /** IDIS senaryosunda zorunlu: 2 harf + 7 rakam */
        public ?string $labelNumber = null,
    ) {
        if (trim($this->sellerCode) === '') {
            throw new \InvalidArgumentException('SellerCode bos olamaz.');
        }
        if ($this->quantityPrice < 0.0) {
            throw new \InvalidArgumentException('QuantityPrice negatif olamaz.');
        }
        if ($this->lineTotal < 0.0) {
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
            'SellerCode'          => $this->sellerCode,
            'BuyerCode'           => $this->buyerCode,
            'Name'                => $this->name,
            'Description'         => $this->description,
            'QuantityPrice'       => (string) $this->quantityPrice,
            'LineTotal'           => $this->lineTotal,
            'DeliveredUnitType'   => $deliveredUnit,
            'DeliveredUnitName'   => $this->deliveredUnitName,
            'DeliveredQuantity'   => $this->deliveredQuantity,
            'OutstandingQuantity' => $this->outstandingQuantity,
            'OutstandingUnitType' => $outstandingUnit,
            'OutstandingUnitName' => $this->outstandingUnitName,
            'OutstandingReason'   => $this->outstandingReason,
            'ManufacturerCode'    => $this->manufacturerCode,
            'BrandName'           => $this->brandName,
            'ModelName'           => $this->modelName,
            'LabelNumber'         => $this->labelNumber,
        ]);
    }
}
