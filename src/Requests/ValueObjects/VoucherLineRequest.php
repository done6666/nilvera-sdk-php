<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * e-SMM (Serbest Meslek Makbuzu) kalemi — EVoucherLineDto.
 */
readonly class VoucherLineRequest extends AbstractRequest
{
    /**
     * @param TaxRequest[] $taxes
     */
    public function __construct(
        /** Hizmet adi */
        public string $name,
        /** Brut ucret */
        public float $grossWage,
        /** Birim fiyat */
        public float $price,
        public float $kdvPercent = 0.0,
        public float $kdvTotal = 0.0,
        public array $taxes = [],
        /** Gelir Vergisi Stopaji yuzdesi */
        public ?float $gvWithholdingPercent = null,
        /** Gelir Vergisi Stopaji tutari */
        public ?float $gvWithholdingTotal = null,
    ) {
        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('Name bos olamaz.');
        }
        if ($this->grossWage < 0.0) {
            throw new \InvalidArgumentException('GrossWage negatif olamaz.');
        }
        if ($this->price < 0.0) {
            throw new \InvalidArgumentException('Price negatif olamaz.');
        }
    }

    public function toArray(): array
    {
        $data = $this->filterNulls([
            'Name'                 => $this->name,
            'GrossWage'            => $this->grossWage,
            'Price'                => $this->price,
            'KDVPercent'           => $this->kdvPercent !== 0.0 ? $this->kdvPercent : null,
            'KDVTotal'             => $this->kdvTotal !== 0.0 ? $this->kdvTotal : null,
            'GVWithholdingPercent' => $this->gvWithholdingPercent,
            'GVWithholdingTotal'   => $this->gvWithholdingTotal,
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
