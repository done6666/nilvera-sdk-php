<?php

declare(strict_types=1);

namespace Nilvera\Requests;

/**
 * Fatura listesi sorgu parametreleri.
 *
 * e-Fatura, e-Arşiv ve benzeri servislerin GET listeleme endpointlerinde
 * query string parametresi olarak kullanılır.
 *
 * Kullanım örneği:
 * ```php
 * $params = new ListInvoicesRequest(
 *     startDate: new \DateTimeImmutable('2026-01-01'),
 *     endDate:   new \DateTimeImmutable('2026-05-14'),
 *     page:      1,
 *     pageSize:  50,
 * );
 *
 * $client->eInvoice()->listSaleInvoices($params->toArray());
 * ```
 */
readonly class ListInvoicesRequest extends AbstractRequest
{
    public function __construct(
        public ?\DateTimeImmutable $startDate = null,
        public ?\DateTimeImmutable $endDate = null,
        public ?int $page = null,
        public ?int $pageSize = null,
        /** Duruma göre filtrele (API'nin kabul ettiği değerler servis bazında değişir) */
        public ?string $status = null,
        /** Alıcı / gönderici VKN veya TCKN'ye göre filtrele */
        public ?string $taxNumber = null,
        /** Fatura numarasına göre filtrele */
        public ?string $invoiceNumber = null,
    ) {
        if ($this->page !== null && $this->page < 1) {
            throw new \InvalidArgumentException('Sayfa numarası (page) 1 veya daha büyük olmalıdır.');
        }

        if ($this->pageSize !== null && ($this->pageSize < 1 || $this->pageSize > 500)) {
            throw new \InvalidArgumentException('Sayfa boyutu (pageSize) 1–500 arasında olmalıdır.');
        }

        if ($this->startDate !== null && $this->endDate !== null && $this->startDate > $this->endDate) {
            throw new \InvalidArgumentException('Başlangıç tarihi (startDate) bitiş tarihinden sonra olamaz.');
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'StartDate'     => $this->startDate?->format('Y-m-d'),
            'EndDate'       => $this->endDate?->format('Y-m-d'),
            'Page'          => $this->page,
            'PageSize'      => $this->pageSize,
            'Status'        => $this->status,
            'TaxNumber'     => $this->taxNumber,
            'InvoiceNumber' => $this->invoiceNumber,
        ]);
    }
}
