<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Validation\TaxNumberValidator;

/**
 * Musteri olusturma istegi — POST /Customers
 *
 * Musteri kartini Nilvera'ya kaydeder; sonraki fatura gonderimlerde
 * TaxNumber ile otomatik alici bilgisi doldurmak icin kullanilir.
 */
readonly class CreateCustomerRequest extends AbstractRequest
{
    public function __construct(
        /** 10 haneli VKN veya 11 haneli TCKN */
        public string $taxNumber,
        /** Unvan veya ad soyad */
        public string $name,
        public string $address,
        public string $district,
        public string $city,
        public string $country = 'TR',
        public ?string $taxOffice = null,
        public ?string $postalCode = null,
        public ?string $phone = null,
        public ?string $fax = null,
        public ?string $mail = null,
        public ?string $webSite = null,
    ) {
        TaxNumberValidator::assertValid($this->taxNumber);

        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('Name bos olamaz.');
        }
        if (trim($this->address) === '') {
            throw new \InvalidArgumentException('Address bos olamaz.');
        }
        if (trim($this->district) === '') {
            throw new \InvalidArgumentException('District bos olamaz.');
        }
        if (trim($this->city) === '') {
            throw new \InvalidArgumentException('City bos olamaz.');
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
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
            'Mail'       => $this->mail,
            'WebSite'    => $this->webSite,
        ]);
    }
}
