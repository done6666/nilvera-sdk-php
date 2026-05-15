<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * KDV ve ÖTV muafiyet sebebi bilgileri — API'deki TaxExemptionReasonInfoDto.
 *
 * Fatura tipi ISTISNA veya IHRACKAYITLI olduğunda KDVExemptionReasonCode zorunludur.
 * Alabileceği değerler için Nilvera dokümantasyonundaki KDV/ÖTV muafiyet listelerine bakın.
 */
readonly class TaxExemptionReasonInfoRequest extends AbstractRequest
{
    public function __construct(
        public ?string $kdvExemptionReasonCode = null,
        public ?string $otvExemptionReasonCode = null,
        public ?string $accommodationTaxExemptionReasonCode = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'KDVExemptionReasonCode'                => $this->kdvExemptionReasonCode,
            'OTVExemptionReasonCode'                => $this->otvExemptionReasonCode,
            'AccommodationTaxExemptionReasonCode'   => $this->accommodationTaxExemptionReasonCode,
        ]);
    }
}
