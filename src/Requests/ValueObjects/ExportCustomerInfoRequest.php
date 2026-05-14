<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Ihracat faturalarinda kullanilan yabanci alici bilgisi — ExportCustomerInfoDto.
 *
 * DIKKAT: Ihracat faturalarinda (InvoiceProfile::Export) CustomerInfo degil
 * bu nesne kullanilmalidir; ikisi birlikte doldurulamaz.
 *
 * PersonName ve PersonSurname sahis alicilar icin birbirine bagimlidir:
 * biri doluysa digeri de zorunludur.
 */
readonly class ExportCustomerInfoRequest extends AbstractRequest
{
    public function __construct(
        /** Yabanci alicinin ulkesindeki vergi/kimlik numarasi */
        public string $taxNumber,
        /** Yabanci alicinin resmi unvani */
        public string $legalRegistrationName,
        /** Acik adres */
        public string $address,
        /** Ilce */
        public string $district,
        /** Sehir */
        public string $city,
        /** Ulke kodu (orn: DE, US, FR) */
        public string $country,
        /** Sahis alici: ad (PersonSurname ile birlikte zorunlu) */
        public ?string $personName = null,
        /** Sahis alici: soyad (PersonName ile birlikte zorunlu) */
        public ?string $personSurname = null,
        public ?string $postalCode = null,
        public ?string $phone = null,
        public ?string $fax = null,
        public ?string $mail = null,
        public ?string $webSite = null,
    ) {
        if (trim($this->taxNumber) === '') {
            throw new \InvalidArgumentException('TaxNumber bos olamaz.');
        }
        if (trim($this->legalRegistrationName) === '') {
            throw new \InvalidArgumentException('LegalRegistrationName bos olamaz.');
        }
        if (trim($this->address) === '') {
            throw new \InvalidArgumentException('Address bos olamaz.');
        }
        if (trim($this->country) === '') {
            throw new \InvalidArgumentException('Country bos olamaz.');
        }

        // PersonName ve PersonSurname birlikte dolu veya birlikte bos olmalidir
        if (($this->personName === null) !== ($this->personSurname === null)) {
            throw new \InvalidArgumentException(
                'PersonName ve PersonSurname birlikte girilmelidir (sahis alici icin).'
            );
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'TaxNumber'             => $this->taxNumber,
            'LegalRegistrationName' => $this->legalRegistrationName,
            'PersonName'            => $this->personName,
            'PersonSurname'         => $this->personSurname,
            'Address'               => $this->address,
            'District'              => $this->district,
            'City'                  => $this->city,
            'Country'               => $this->country,
            'PostalCode'            => $this->postalCode,
            'Phone'                 => $this->phone,
            'Fax'                   => $this->fax,
            'Mail'                  => $this->mail,
            'WebSite'               => $this->webSite,
        ]);
    }
}
