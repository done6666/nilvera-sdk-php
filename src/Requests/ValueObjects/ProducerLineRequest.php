<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Enums\UnitType;
use Nilvera\Requests\AbstractRequest;

/**
 * e-MM (Mustahsil Makbuzu) kalemi — EProducerLineDto.
 */
readonly class ProducerLineRequest extends AbstractRequest
{
    /**
     * @param TaxRequest[] $taxes
     */
    public function __construct(
        /** Urun adi */
        public string $name,
        /** Miktar */
        public float $quantity,
        /** Birim tipi */
        public UnitType|string $unitType,
        /** Birim fiyat */
        public float $price,
        public array $taxes = [],
        /** Gelir Vergisi Stopaji yuzdesi */
        public ?float $gvWithholdingPercent = null,
        /** Gelir Vergisi Stopaji tutari */
        public ?float $gvWithholdingAmount = null,
    ) {
        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('Name bos olamaz.');
        }
        if ($this->quantity <= 0.0) {
            throw new \InvalidArgumentException('Quantity sifirdan buyuk olmalidir.');
        }
        if ($this->price < 0.0) {
            throw new \InvalidArgumentException('Price negatif olamaz.');
        }
    }

    public function toArray(): array
    {
        $unitValue = $this->unitType instanceof UnitType
            ? $this->unitType->value
            : $this->unitType;

        $data = $this->filterNulls([
            'Name'                 => $this->name,
            'Quantity'             => $this->quantity,
            'UnitType'             => $unitValue,
            'Price'                => $this->price,
            'GVWithholdingPercent' => $this->gvWithholdingPercent,
            'GVWithholdingAmount'  => $this->gvWithholdingAmount,
        ]);

        if ($this->taxes !== []) {
            $data['Taxes'] = array_map(
                static fn (TaxRequest $t) => $t->toArray(),
                $this->taxes,
            );
        }

        return $data;
    }
}
