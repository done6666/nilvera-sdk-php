<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;
use Nilvera\Validation\TaxNumberValidator;

/**
 * Fatura alıcısı (CustomerInfo) bilgilerini taşır.
 *
 * Alan adları Nilvera API dokümantasyonundaki CustomerInfo nesnesine birebir uygundur.
 *
 * Zorunluluk notu:
 *  - TaxOffice: e-Fatura'da zorunlu, e-Arşiv'de seçimli
 *  - District  : her iki belge türünde zorunlu
 *  - İhracat faturalarında CustomerInfo yerine ExportCustomerInfo kullanılmalıdır
 */
readonly class ReceiverRequest extends AbstractRequest
{
    /**
     * @param array<array{ID: string, IDType: string}> $partyIdentifications      Diğer resmi kimlik bilgileri
     * @param array<array{ID: string, IDType: string}> $agentPartyIdentifications Aracı kuruma ait kimlik bilgileri
     */
    public function __construct(
        /** Alıcının Vergi/T.C. Kimlik Numarası (10 haneli VKN veya 11 haneli TCKN) */
        public string $taxNumber,
        /** Alıcının ünvanı veya adı soyadı */
        public string $name,
        /** Açık adres */
        public string $address,
        /** İlçe (zorunlu) */
        public string $district,
        /** Şehir */
        public string $city,
        /** Ülke kodu (varsayılan: TR) */
        public string $country = 'TR',
        /** Vergi dairesi — e-Fatura'da zorunlu, e-Arşiv'de seçimli */
        public ?string $taxOffice = null,
        public ?string $postalCode = null,
        public ?string $phone = null,
        public ?string $fax = null,
        public ?string $mail = null,
        public ?string $webSite = null,
        public array $partyIdentifications = [],
        public array $agentPartyIdentifications = [],
    ) {
        $this->validateTaxNumber();
        $this->validateRequiredStrings();
    }

    private function validateTaxNumber(): void
    {
        TaxNumberValidator::assertValid($this->taxNumber);
    }

    private function validateRequiredStrings(): void
    {
        foreach (['name' => 'Name', 'address' => 'Address', 'district' => 'District', 'city' => 'City'] as $prop => $label) {
            if (trim($this->$prop) === '') {
                throw new \InvalidArgumentException("{$label} alanı boş olamaz.");
            }
        }
    }

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
            'Mail'       => $this->mail,
            'WebSite'    => $this->webSite,
        ]);

        if ($this->partyIdentifications !== []) {
            $data['PartyIdentifications'] = $this->partyIdentifications;
        }

        if ($this->agentPartyIdentifications !== []) {
            $data['AgentPartyIdentifications'] = $this->agentPartyIdentifications;
        }

        return $data;
    }
}
