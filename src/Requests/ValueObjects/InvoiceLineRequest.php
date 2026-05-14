<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Enums\UnitType;
use Nilvera\Requests\AbstractRequest;

/**
 * Fatura kalemini (satırını) temsil eder.
 *
 * Doğrudan constructor ile tüm alanları geçirebilir veya
 * {@see self::make()} fabrika metodunu kullanarak KDV tutarı ve
 * satır toplamını otomatik hesaplatabilirsiniz.
 */
readonly class InvoiceLineRequest extends AbstractRequest
{
    /**
     * @param TaxRequest[]     $additionalTaxes KDV dışı ek vergiler (ÖTV, Damga vb.)
     */
    public function __construct(
        public string $name,
        public float $quantity,
        public UnitType $unit,
        public float $unitPrice,
        /** KDV oranı (yüzde, örn: 20.0) */
        public float $vatRate,
        /** Hesaplanmış KDV tutarı */
        public float $vatAmount,
        /** İskonto sonrası satır toplamı (KDV hariç) */
        public float $lineTotal,
        public ?DiscountRequest $discount = null,
        public array $additionalTaxes = [],
        /** Mal/hizmet açıklaması */
        public ?string $description = null,
        /** Ürün / stok kodu */
        public ?string $productCode = null,
        /** GTIP kodu (ihracat faturalarında kullanılır) */
        public ?string $gtip = null,
    ) {
        if ($this->quantity <= 0.0) {
            throw new \InvalidArgumentException('Miktar (quantity) sıfırdan büyük olmalıdır.');
        }

        if ($this->unitPrice < 0.0) {
            throw new \InvalidArgumentException('Birim fiyat (unitPrice) negatif olamaz.');
        }

        if ($this->vatRate < 0.0) {
            throw new \InvalidArgumentException('KDV oranı (vatRate) negatif olamaz.');
        }

        if ($this->vatAmount < 0.0) {
            throw new \InvalidArgumentException('KDV tutarı (vatAmount) negatif olamaz.');
        }

        if ($this->lineTotal < 0.0) {
            throw new \InvalidArgumentException('Satır toplamı (lineTotal) negatif olamaz.');
        }

        foreach ($this->additionalTaxes as $i => $tax) {
            if (!$tax instanceof TaxRequest) {
                throw new \InvalidArgumentException(
                    "additionalTaxes[{$i}] bir TaxRequest nesnesi olmalıdır."
                );
            }
        }
    }

    /**
     * KDV tutarını ve satır toplamını otomatik hesaplayarak bir kalem oluşturur.
     *
     * @param TaxRequest[] $additionalTaxes
     */
    public static function make(
        string $name,
        float $quantity,
        UnitType $unit,
        float $unitPrice,
        float $vatRate,
        ?DiscountRequest $discount = null,
        array $additionalTaxes = [],
        ?string $description = null,
        ?string $productCode = null,
        ?string $gtip = null,
    ): self {
        $discountAmount = $discount?->amount ?? 0.0;
        $lineTotal      = round(($quantity * $unitPrice) - $discountAmount, 2);
        $vatAmount      = round($lineTotal * ($vatRate / 100), 2);

        return new self(
            name: $name,
            quantity: $quantity,
            unit: $unit,
            unitPrice: $unitPrice,
            vatRate: $vatRate,
            vatAmount: $vatAmount,
            lineTotal: $lineTotal,
            discount: $discount,
            additionalTaxes: $additionalTaxes,
            description: $description,
            productCode: $productCode,
            gtip: $gtip,
        );
    }

    public function toArray(): array
    {
        $data = [
            'Name'      => $this->name,
            'Quantity'  => $this->quantity,
            'Unit'      => $this->unit->value,
            'UnitPrice' => $this->unitPrice,
            'VATRate'   => $this->vatRate,
            'VATAmount' => $this->vatAmount,
            'LineTotal' => $this->lineTotal,
        ];

        if ($this->discount !== null) {
            $data['Discount'] = $this->discount->toArray();
        }

        if ($this->additionalTaxes !== []) {
            $data['AdditionalTaxes'] = array_map(
                static fn (TaxRequest $t) => $t->toArray(),
                $this->additionalTaxes,
            );
        }

        if ($this->description !== null) {
            $data['Description'] = $this->description;
        }

        if ($this->productCode !== null) {
            $data['ProductCode'] = $this->productCode;
        }

        if ($this->gtip !== null) {
            $data['GTIP'] = $this->gtip;
        }

        return $data;
    }
}
