<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

/**
 * e-Arşiv fatura gönderme isteği — POST /earchive/Send/Model
 *
 * e-Arşiv faturalar GİB sistemine değil, doğrudan alıcıya iletilir.
 * Bu nedenle ReceiverAlias zorunlu değildir ve bireysel (B2C) alıcılar desteklenir.
 *
 * Kullanım örneği:
 * ```php
 * $invoice = new SendArchiveInvoiceRequest(
 *     receiver: new ReceiverRequest(
 *         taxNumber: '12345678901',   // TCKN (bireysel)
 *         title:     'Ahmet Yılmaz',
 *         address:   'Bağcılar Mah. No:5',
 *         city:      'İstanbul',
 *     ),
 *     lines: [
 *         InvoiceLineRequest::make('Web Tasarım', 1, UnitType::Piece, 5000, 20),
 *     ],
 *     invoiceDate:  new \DateTimeImmutable('2026-05-14'),
 *     isInternetSale: true,
 * );
 * ```
 */
readonly class SendArchiveInvoiceRequest extends AbstractRequest
{
    /**
     * @param InvoiceLineRequest[] $lines
     * @param string[]             $notes
     */
    public function __construct(
        public ReceiverRequest $receiver,
        public array $lines,
        public \DateTimeImmutable $invoiceDate,
        public InvoiceProfile $invoiceProfile = InvoiceProfile::Basic,
        public InvoiceType $invoiceType = InvoiceType::Sales,
        public string $currency = 'TRY',
        public float $currencyRate = 1.0,
        public array $notes = [],
        public ?\DateTimeImmutable $invoiceTime = null,
        /** İnternet üzerinden yapılan satışlarda true */
        public bool $isInternetSale = false,
        public ?string $orderNumber = null,
        public ?string $orderDate = null,
        public ?string $uuid = null,
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

    public function subTotal(): float
    {
        return round(
            array_sum(array_map(static fn (InvoiceLineRequest $l) => $l->lineTotal, $this->lines)),
            2,
        );
    }

    public function totalVat(): float
    {
        return round(
            array_sum(array_map(static fn (InvoiceLineRequest $l) => $l->vatAmount, $this->lines)),
            2,
        );
    }

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
            'IsInternetSale' => $this->isInternetSale,
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
