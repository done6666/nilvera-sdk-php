<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

/**
 * e-Fatura gönderme isteği — POST /einvoice/Send/Model
 *
 * Aynı DTO; önizleme (preview) ve PDF indirme (downloadPdf) işlemleri için de kullanılır.
 *
 * Satır toplamları (SubTotal, TotalVAT, GrandTotal) otomatik hesaplanır;
 * manuel geçersiz kılmak istiyorsanız alt sınıf oluşturup toArray() metodunu override edin.
 *
 * Kullanım örneği:
 * ```php
 * $invoice = new SendInvoiceRequest(
 *     receiver: new ReceiverRequest(
 *         taxNumber: '3230456015',
 *         title:     'ABC Yazılım A.Ş.',
 *         taxOffice: 'Kadıköy',
 *         address:   'Atatürk Cad. No:1',
 *         city:      'İstanbul',
 *     ),
 *     lines: [
 *         InvoiceLineRequest::make(
 *             name:      'Yazılım Lisansı',
 *             quantity:  1.0,
 *             unit:      UnitType::Piece,
 *             unitPrice: 10000.00,
 *             vatRate:   20.0,
 *         ),
 *     ],
 *     invoiceDate:    new \DateTimeImmutable('2026-05-14'),
 *     receiverAlias:  'urn:mail:muhasebe@abc.com.tr',
 * );
 * ```
 */
readonly class SendInvoiceRequest extends AbstractRequest
{
    /**
     * @param InvoiceLineRequest[] $lines          En az bir fatura kalemi zorunludur.
     * @param string[]             $notes          Fatura notları (birden fazla satır)
     */
    public function __construct(
        public ReceiverRequest $receiver,
        public array $lines,
        public \DateTimeImmutable $invoiceDate,
        public InvoiceProfile $invoiceProfile = InvoiceProfile::Basic,
        public InvoiceType $invoiceType = InvoiceType::Sales,
        /** ISO 4217 para birimi kodu (TRY, USD, EUR …) */
        public string $currency = 'TRY',
        /** GİB sisteminde kayıtlı alıcı için e-posta veya ETTN alias */
        public ?string $receiverAlias = null,
        /** TRY dışı para birimlerinde döviz kuru */
        public float $currencyRate = 1.0,
        public array $notes = [],
        /** Fatura saati; belirtilmezse API varsayılanı kullanılır */
        public ?\DateTimeImmutable $invoiceTime = null,
        /** Sipariş numarası */
        public ?string $orderNumber = null,
        /** Sipariş tarihi (YYYY-MM-DD) */
        public ?string $orderDate = null,
        /** Önden belirlenmiş UUID; boş bırakılırsa API üretir */
        public ?string $uuid = null,
        /** Seri kodu (örn: "NLV") */
        public ?string $series = null,
    ) {
        if ($this->lines === []) {
            throw new \InvalidArgumentException('Faturada en az bir kalem (line) bulunmalıdır.');
        }

        foreach ($this->lines as $i => $line) {
            if (!$line instanceof InvoiceLineRequest) {
                throw new \InvalidArgumentException(
                    "lines[{$i}] bir InvoiceLineRequest nesnesi olmalıdır."
                );
            }
        }

        if ($this->currencyRate <= 0.0) {
            throw new \InvalidArgumentException('Döviz kuru (currencyRate) sıfırdan büyük olmalıdır.');
        }
    }

    /** Kalemlerin KDV hariç toplam tutarı */
    public function subTotal(): float
    {
        return round(
            array_sum(array_map(static fn (InvoiceLineRequest $l) => $l->lineTotal, $this->lines)),
            2,
        );
    }

    /** Tüm kalemlerin toplam KDV tutarı */
    public function totalVat(): float
    {
        return round(
            array_sum(array_map(static fn (InvoiceLineRequest $l) => $l->vatAmount, $this->lines)),
            2,
        );
    }

    /** Ödenecek genel toplam (KDV dahil) */
    public function grandTotal(): float
    {
        return round($this->subTotal() + $this->totalVat(), 2);
    }

    public function toArray(): array
    {
        $data = [
            'InvoiceProfile' => $this->invoiceProfile->value,
            'InvoiceType'    => $this->invoiceType->value,
            'InvoiceDate'    => $this->invoiceDate->format('Y-m-d'),
            'Currency'       => $this->currency,
            'CurrencyRate'   => $this->currencyRate,
            'Receiver'       => $this->receiver->toArray(),
            'Lines'          => array_map(
                static fn (InvoiceLineRequest $l) => $l->toArray(),
                $this->lines,
            ),
            'SubTotal'   => $this->subTotal(),
            'TotalVAT'   => $this->totalVat(),
            'GrandTotal' => $this->grandTotal(),
        ];

        if ($this->uuid !== null) {
            $data['UUID'] = $this->uuid;
        }

        if ($this->receiverAlias !== null) {
            $data['ReceiverAlias'] = $this->receiverAlias;
        }

        if ($this->invoiceTime !== null) {
            $data['InvoiceTime'] = $this->invoiceTime->format('H:i:s');
        }

        if ($this->notes !== []) {
            $data['Notes'] = $this->notes;
        }

        if ($this->orderNumber !== null) {
            $data['OrderNumber'] = $this->orderNumber;
        }

        if ($this->orderDate !== null) {
            $data['OrderDate'] = $this->orderDate;
        }

        if ($this->series !== null) {
            $data['Series'] = $this->series;
        }

        return $data;
    }
}
