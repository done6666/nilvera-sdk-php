<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Teslim ve odeme yeri adresi — API'deki AddressInfoDto.
 * Ihracat faturalarinda DeliveryInfoRequest icinde kullanilir.
 */
readonly class AddressInfoRequest extends AbstractRequest
{
    public function __construct(
        public string $address,
        public string $district,
        public string $city,
        public string $country,
        public ?string $postalCode = null,
        public ?string $phone = null,
        public ?string $fax = null,
        public ?string $mail = null,
        public ?string $webSite = null,
    ) {
        if (trim($this->address) === '') {
            throw new \InvalidArgumentException('Address alani bos olamaz.');
        }
        if (trim($this->district) === '') {
            throw new \InvalidArgumentException('District alani bos olamaz.');
        }
        if (trim($this->city) === '') {
            throw new \InvalidArgumentException('City alani bos olamaz.');
        }
        if (trim($this->country) === '') {
            throw new \InvalidArgumentException('Country alani bos olamaz.');
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'Address'    => $this->address,
            'District'   => $this->district,
            'City'       => $this->city,
            'Country'    => $this->country,
            'PostalCode' => $this->postalCode,
            'Phone'      => $this->phone,
            'Fax'        => $this->fax,
            'Mail'       => $this->mail,
            'WebSite'    => $this->webSite,
        ]);
    }
}
