<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * e-İrsaliye taraf bilgisi — DespatchSupplierInfo, BuyerCustomerInfo,
 * SellerSupplierInfo ve OriginatorCustomerInfo alanlarında kullanılır.
 *
 * PartyIdentifications yalnızca DespatchSupplierInfo'da API tarafından desteklenir;
 * diğer taraflarda bu alan doldurulmamalıdır.
 *
 * @param array<array{SchemeID: string, Value: string}> $partyIdentifications
 */
readonly class WaybillPartyRequest extends AbstractRequest
{
    public function __construct(
        public ?string $taxNumber = null,
        public ?string $name = null,
        public ?string $taxOffice = null,
        public ?string $address = null,
        public ?string $district = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?string $postalCode = null,
        public ?string $phone = null,
        public ?string $fax = null,
        public ?string $email = null,
        public ?string $webSite = null,
        public array $partyIdentifications = [],
    ) {}

    public function toArray(): array
    {
        $data = $this->filterNulls([
            'TaxNumber'  => $this->taxNumber,
            'Name'       => $this->name,
            'TaxOffice'  => $this->taxOffice,
            'Address'    => $this->address,
            'District'   => $this->district,
            'City'       => $this->city,
            'Country'    => $this->country,
            'PostalCode' => $this->postalCode,
            'Phone'      => $this->phone,
            'Fax'        => $this->fax,
            'Email'      => $this->email,
            'WebSite'    => $this->webSite,
        ]);

        if ($this->partyIdentifications !== []) {
            $data['PartyIdentifications'] = $this->partyIdentifications;
        }

        return $data;
    }
}
