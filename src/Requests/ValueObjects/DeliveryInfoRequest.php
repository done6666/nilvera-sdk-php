<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Ihracat faturalarinda fatura kalemi basina teslim bilgisi — API'deki DeliveryInfoDto.
 * Yalnizca InvoiceProfile::Export (IHRACAT) faturalarinda kullanilir.
 *
 * DeliveryTermCode icin: https://developer.nilvera.com/kod-listeleri (Teslim Sarti Kodlari)
 * TransportModeCode icin: https://developer.nilvera.com/kod-listeleri (Gonderim Sekli Kodlari)
 * PackageTypeCode  icin: https://developer.nilvera.com/kod-listeleri (Kab Cinsleri)
 */
readonly class DeliveryInfoRequest extends AbstractRequest
{
    public function __construct(
        /** GTIP numarasi (Gumruk Tarife Istatistik Pozisyonu) */
        public string $gtipNo,
        /** Teslim sarti kodu — kod listesinden */
        public string $deliveryTermCode,
        /** Gonderim sekli kodu — kod listesinden */
        public string $transportModeCode,
        /** Gumruk takip numarasi */
        public string $productTraceId,
        /** Teslim ve odeme yeri adresi */
        public AddressInfoRequest $deliveryAddress,
        public ?string $packageBrandName = null,
        public ?string $packageId = null,
        public ?float $packageQuantity = null,
        /** Kab cinsi kodu — kod listesinden */
        public ?string $packageTypeCode = null,
    ) {
        if (trim($this->gtipNo) === '') {
            throw new \InvalidArgumentException('GTIPNo bos olamaz.');
        }
        if (trim($this->deliveryTermCode) === '') {
            throw new \InvalidArgumentException('DeliveryTermCode bos olamaz.');
        }
        if (trim($this->transportModeCode) === '') {
            throw new \InvalidArgumentException('TransportModeCode bos olamaz.');
        }
        if (trim($this->productTraceId) === '') {
            throw new \InvalidArgumentException('ProductTraceID bos olamaz.');
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'GTIPNo'            => $this->gtipNo,
            'DeliveryTermCode'  => $this->deliveryTermCode,
            'TransportModeCode' => $this->transportModeCode,
            'ProductTraceID'    => $this->productTraceId,
            'DeliveryAddress'   => $this->deliveryAddress->toArray(),
            'PackageBrandName'  => $this->packageBrandName,
            'PackageID'         => $this->packageId,
            'PackageQuantity'   => $this->packageQuantity,
            'PackageTypeCode'   => $this->packageTypeCode,
        ]);
    }
}
