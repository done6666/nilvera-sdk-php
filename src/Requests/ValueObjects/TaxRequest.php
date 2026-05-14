<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Fatura kalemindeki ek vergiyi (KDV haricindeki vergiler) temsil eder — API'deki TaxDto.
 *
 * - TaxCode   : Zorunlu. Vergi veya tevkifat kodu (örn: "9015" KDV tevkifatı için).
 * - Total     : Vergi tutarı (vergi türüne göre zorunlu olabilir).
 * - Percent   : Vergi oranı (vergi türüne göre zorunlu olabilir).
 * - ReasonCode: Tevkifat vergilerinde ZORUNLU.
 * - ReasonDesc: Tevkifat vergilerinde ZORUNLU.
 *
 * Örnek (KDV tevkifatı):
 *   new TaxRequest(taxCode: '9015', total: 1.72, percent: 40.0,
 *                  reasonCode: '601', reasonDesc: 'Yapım İşleri...')
 */
readonly class TaxRequest extends AbstractRequest
{
    public function __construct(
        public string $taxCode,
        public ?float $total = null,
        public ?float $percent = null,
        public ?string $reasonCode = null,
        public ?string $reasonDesc = null,
    ) {
        if (trim($this->taxCode) === '') {
            throw new \InvalidArgumentException('TaxCode boş olamaz.');
        }

        if ($this->total !== null && $this->total < 0.0) {
            throw new \InvalidArgumentException('Vergi tutarı (Total) negatif olamaz.');
        }

        if ($this->percent !== null && $this->percent < 0.0) {
            throw new \InvalidArgumentException('Vergi oranı (Percent) negatif olamaz.');
        }

        // Tevkifat kodları (9015 vb.) ReasonCode+ReasonDesc gerektirir
        if (($this->reasonCode !== null) !== ($this->reasonDesc !== null)) {
            throw new \InvalidArgumentException('ReasonCode ve ReasonDesc birlikte girilmelidir.');
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'TaxCode'    => $this->taxCode,
            'Total'      => $this->total,
            'Percent'    => $this->percent,
            'ReasonCode' => $this->reasonCode,
            'ReasonDesc' => $this->reasonDesc,
        ]);
    }
}
