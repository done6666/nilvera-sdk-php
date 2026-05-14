<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Fatura alıcısının (vergi mükellefi veya bireysel kişi) bilgilerini taşır.
 *
 * - VKN  : 10 haneli vergi kimlik numarası (tüzel kişi)
 * - TCKN : 11 haneli TC kimlik numarası (gerçek kişi)
 */
readonly class ReceiverRequest extends AbstractRequest
{
    public function __construct(
        /** 10 haneli VKN veya 11 haneli TCKN */
        public string $taxNumber,
        /** Unvan (şirket adı) veya ad-soyad birleşimi */
        public string $title,
        /** Açık adres */
        public string $address,
        /** İl */
        public string $city,
        public string $country = 'TR',
        /** Vergi dairesi (tüzel kişiler için zorunlu) */
        public ?string $taxOffice = null,
        /** İlçe */
        public ?string $district = null,
        public ?string $postalCode = null,
        public ?string $email = null,
        public ?string $phone = null,
        /** Bireysel alıcılarda ad */
        public ?string $name = null,
        /** Bireysel alıcılarda soyad */
        public ?string $surname = null,
        public ?string $website = null,
    ) {
        $this->validateTaxNumber();
        $this->validateRequiredStrings();
    }

    private function validateTaxNumber(): void
    {
        if (!ctype_digit($this->taxNumber)) {
            throw new \InvalidArgumentException('TaxNumber yalnızca rakam içermelidir.');
        }

        $len = strlen($this->taxNumber);

        if ($len !== 10 && $len !== 11) {
            throw new \InvalidArgumentException(
                "TaxNumber 10 (VKN) veya 11 (TCKN) haneli olmalıdır; {$len} hane girildi."
            );
        }
    }

    private function validateRequiredStrings(): void
    {
        if (trim($this->title) === '') {
            throw new \InvalidArgumentException('Alıcı unvanı (title) boş olamaz.');
        }

        if (trim($this->address) === '') {
            throw new \InvalidArgumentException('Alıcı adresi boş olamaz.');
        }

        if (trim($this->city) === '') {
            throw new \InvalidArgumentException('Alıcı ili (city) boş olamaz.');
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'TaxNumber'  => $this->taxNumber,
            'Title'      => $this->title,
            'TaxOffice'  => $this->taxOffice,
            'Address'    => $this->address,
            'City'       => $this->city,
            'District'   => $this->district,
            'PostalCode' => $this->postalCode,
            'Country'    => $this->country,
            'Email'      => $this->email,
            'Phone'      => $this->phone,
            'Name'       => $this->name,
            'Surname'    => $this->surname,
            'Website'    => $this->website,
        ]);
    }
}
