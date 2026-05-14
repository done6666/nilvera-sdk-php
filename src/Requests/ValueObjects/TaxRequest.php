<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * KDV dışındaki ek vergileri (ÖTV, Damga Vergisi vb.) temsil eder.
 *
 * Her fatura satırında birden fazla ek vergi bulunabilir.
 */
readonly class TaxRequest extends AbstractRequest
{
    public function __construct(
        /** Vergi adı (örn: "Özel Tüketim Vergisi") */
        public string $taxName,
        /** Vergi oranı (yüzde olarak, örn: 25.0) */
        public float $taxRate,
        /** Hesaplanmış vergi tutarı */
        public float $taxAmount,
        /** GİB vergi kodu (örn: "0015" ÖTV için) */
        public ?string $taxCode = null,
    ) {
        if ($this->taxRate < 0.0) {
            throw new \InvalidArgumentException('Vergi oranı (taxRate) negatif olamaz.');
        }

        if ($this->taxAmount < 0.0) {
            throw new \InvalidArgumentException('Vergi tutarı (taxAmount) negatif olamaz.');
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'TaxName'   => $this->taxName,
            'TaxRate'   => $this->taxRate,
            'TaxAmount' => $this->taxAmount,
            'TaxCode'   => $this->taxCode,
        ]);
    }
}
