<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Tasiyici firma bilgisi — ShipmentDetail.Delivery.CarrierInfo.
 * Eklenmesi halinde tum alanlar zorunludur.
 */
readonly class CarrierInfoRequest extends AbstractRequest
{
    public function __construct(
        public string $taxNumber,
        public string $name,
        public string $address,
        public string $district,
        public string $city,
        public string $country,
        public string $postalCode,
    ) {}

    public function toArray(): array
    {
        return [
            'TaxNumber'  => $this->taxNumber,
            'Name'       => $this->name,
            'Address'    => $this->address,
            'District'   => $this->district,
            'City'       => $this->city,
            'Country'    => $this->country,
            'PostalCode' => $this->postalCode,
        ];
    }
}
