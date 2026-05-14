<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Enums\UnitType;
use Nilvera\Requests\AbstractRequest;

/**
 * Fatura kalemi — API'deki EInvoiceLineDto.
 *
 * Alan adlari Nilvera API dokumantasyonuna (InvoiceLines sayfasi) birebir uygundur.
 *
 * Hizli kullanim icin {@see self::make()} fabrika metodunu kullanabilirsiniz;
 * bu metot KDVTotal degerini Price, Quantity, AllowanceTotal ve KDVPercent'ten
 * otomatik hesaplar.
 *
 * Ornek:
 *   InvoiceLineRequest::make('Yazilim Lisansi', 1, UnitType::Piece, 10000, 20)
 */
readonly class InvoiceLineRequest extends AbstractRequest
{
    /**
     * @param TaxRequest[]          $taxes        KDV disindaki ek vergiler (OTV, Damga vb.)
     * @param DeliveryInfoRequest|null $deliveryInfo Yalnizca IHRACAT faturalarinda doldurulur
     */
    public function __construct(
        /** Urun/hizmet adi */
        public string $name,
        /** Miktar */
        public float $quantity,
        /** Birim tipi — UnitType enum veya ham kod (orn: 'C62') */
        public UnitType|string $unitType,
        /** Birim fiyat (API'ye string olarak gonderilir) */
        public float $price,
        /** Iskonto tutari; iskonto yoksa 0.0 girin */
        public float $allowanceTotal,
        /** KDV orani: 0, 1, 10 veya 20 */
        public float $kdvPercent,
        /** Toplam KDV tutari */
        public float $kdvTotal,
        public array $taxes = [],
        public ?string $index = null,
        public ?string $sellerCode = null,
        public ?string $buyerCode = null,
        public ?string $description = null,
        public ?string $manufacturerCode = null,
        public ?string $brandName = null,
        public ?string $modelName = null,
        public ?string $note = null,
        public ?string $additionalInfoId = null,
        public ?string $serialId = null,
        public ?string $productTraceId = null,
        public ?string $labelNumber = null,
        public ?string $buyerDibLineCode = null,
        public ?string $sellerDibLineCode = null,
        public ?string $gtipNo = null,
        public ?string $ozelMatrahReason = null,
        public ?float $ozelMatrahTotal = null,
        public ?float $vatAmountWithoutTevkifat = null,
        public ?DeliveryInfoRequest $deliveryInfo = null,
    ) {
        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('Urun adi (Name) bos olamaz.');
        }
        if ($this->quantity <= 0.0) {
            throw new \InvalidArgumentException('Miktar (Quantity) sifirdan buyuk olmalidir.');
        }
        if ($this->price < 0.0) {
            throw new \InvalidArgumentException('Birim fiyat (Price) negatif olamaz.');
        }
        if ($this->allowanceTotal < 0.0) {
            throw new \InvalidArgumentException('Iskonto tutari (AllowanceTotal) negatif olamaz.');
        }
        if (!in_array($this->kdvPercent, [0.0, 1.0, 10.0, 20.0], true)) {
            throw new \InvalidArgumentException(
                'KDV orani (KDVPercent) 0, 1, 10 veya 20 olmalidir; ' . $this->kdvPercent . ' gecersiz.'
            );
        }
        if ($this->kdvTotal < 0.0) {
            throw new \InvalidArgumentException('KDV tutari (KDVTotal) negatif olamaz.');
        }
        foreach ($this->taxes as $i => $tax) {
            if (!$tax instanceof TaxRequest) {
                throw new \InvalidArgumentException("taxes[{$i}] bir TaxRequest nesnesi olmalidir.");
            }
        }
    }

    /**
     * KDVTotal'i otomatik hesaplayarak bir fatura kalemi olusturur.
     *
     * AllowanceTotal (iskonto) icin:
     *  - Tutar girmek: $allowanceTotal parametresini kullanin
     *  - Yuzde girmek: $allowancePercent parametresini kullanin (AllowanceTotal'i hesaplar)
     *
     * @param TaxRequest[] $taxes
     */
    public static function make(
        string $name,
        float $quantity,
        UnitType|string $unitType,
        float $price,
        float $kdvPercent,
        float $allowanceTotal = 0.0,
        float $allowancePercent = 0.0,
        array $taxes = [],
        ?string $index = null,
        ?string $sellerCode = null,
        ?string $buyerCode = null,
        ?string $description = null,
        ?string $gtipNo = null,
        ?DeliveryInfoRequest $deliveryInfo = null,
    ): self {
        if ($allowancePercent > 0.0) {
            $allowanceTotal = round($quantity * $price * ($allowancePercent / 100), 2);
        }

        $lineBase = round(($quantity * $price) - $allowanceTotal, 2);
        $kdvTotal = round($lineBase * ($kdvPercent / 100), 2);

        return new self(
            name: $name,
            quantity: $quantity,
            unitType: $unitType,
            price: $price,
            allowanceTotal: $allowanceTotal,
            kdvPercent: $kdvPercent,
            kdvTotal: $kdvTotal,
            taxes: $taxes,
            index: $index,
            sellerCode: $sellerCode,
            buyerCode: $buyerCode,
            description: $description,
            gtipNo: $gtipNo,
            deliveryInfo: $deliveryInfo,
        );
    }

    public function toArray(): array
    {
        $unitValue = $this->unitType instanceof UnitType
            ? $this->unitType->value
            : $this->unitType;

        $data = $this->filterNulls([
            'Index'           => $this->index,
            'SellerCode'      => $this->sellerCode,
            'BuyerCode'       => $this->buyerCode,
            'Name'            => $this->name,
            'Description'     => $this->description,
            'Quantity'        => $this->quantity,
            'UnitType'        => $unitValue,
            'Price'           => (string) $this->price,
            'AllowanceTotal'  => $this->allowanceTotal,
            'KDVPercent'      => $this->kdvPercent,
            'KDVTotal'        => $this->kdvTotal,
            'ManufacturerCode'          => $this->manufacturerCode,
            'BrandName'                 => $this->brandName,
            'ModelName'                 => $this->modelName,
            'Note'                      => $this->note,
            'AdditionalInfoId'          => $this->additionalInfoId,
            'SerialID'                  => $this->serialId,
            'ProductTraceID'            => $this->productTraceId,
            'LabelNumber'               => $this->labelNumber,
            'BuyerDIBLineCode'          => $this->buyerDibLineCode,
            'SellerDIBLineCode'         => $this->sellerDibLineCode,
            'GTIPNo'                    => $this->gtipNo,
            'OzelMatrahReason'          => $this->ozelMatrahReason,
            'OzelMatrahTotal'           => $this->ozelMatrahTotal,
            'VatAmountWithoutTevkifat'  => $this->vatAmountWithoutTevkifat,
        ]);

        if ($this->taxes !== []) {
            $data['Taxes'] = array_map(
                static fn (TaxRequest $t) => $t->toArray(),
                $this->taxes,
            );
        }

        if ($this->deliveryInfo !== null) {
            $data['DeliveryInfo'] = $this->deliveryInfo->toArray();
        }

        return $data;
    }
}
