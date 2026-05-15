<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\SalesPlatform;
use Nilvera\Enums\SendType;
use Nilvera\Requests\ValueObjects\AdditionalDocumentReferenceRequest;
use Nilvera\Requests\ValueObjects\ESUReportInfoRequest;
use Nilvera\Requests\ValueObjects\ExpensesRequest;
use Nilvera\Requests\ValueObjects\InternetInfoRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\InvoicePeriodRequest;
use Nilvera\Requests\ValueObjects\OKCInfoRequest;
use Nilvera\Requests\ValueObjects\PaymentMeansRequest;
use Nilvera\Requests\ValueObjects\PaymentTermsRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ReturnInvoiceInfoRequest;
use Nilvera\Requests\ValueObjects\TaxExemptionReasonInfoRequest;

/**
 * e-Arşiv fatura gönderme isteği — POST /earchive/Send/Model
 *
 * JSON çıktı yapısı:
 * {
 *   "ArchiveInvoice": {
 *     "InvoiceInfo":   { ... },
 *     "CompanyInfo":   { ... },   (opsiyonel — gönderen şirket)
 *     "CustomerInfo":  { ... },
 *     "InvoiceLines":  [ ... ],
 *     "Notes":         [ "..." ]
 *   }
 * }
 *
 * Önemli kurallar:
 * - SalesPlatform::Internet ise SendType::Electronic zorunludur.
 * - SalesPlatform::Internet ise InternetInfo doldurulmalıdır.
 * - InvoiceSerieOrNumber: 3 harfli seri kodu (ör. "EAR") veya 16 haneli tam numara.
 */
readonly class SendArchiveInvoiceRequest extends AbstractRequest
{
    /**
     * @param InvoiceLineRequest[]                   $invoiceLines            En az bir kalem zorunludur
     * @param string[]                               $notes                   Fatura notları
     * @param array<array{IssueDate:string,Value:string}> $despatchDocumentReference İrsaliye referansları
     * @param AdditionalDocumentReferenceRequest[]   $additionalDocumentReferences  Ek belgeler/dosyalar
     * @param ReturnInvoiceInfoRequest[]             $returnInvoiceInfo        İade fatura bilgileri (IADE tipinde)
     * @param ExpensesRequest[]                      $expenses                 HKS masrafları
     */
    public function __construct(
        public ReceiverRequest $customerInfo,
        public array $invoiceLines,
        public \DateTimeImmutable $issueDate,
        public string $invoiceSerieOrNumber,
        public InvoiceType $invoiceType = InvoiceType::Sales,
        public SendType $sendType = SendType::Electronic,
        public SalesPlatform $salesPlatform = SalesPlatform::Normal,
        public string $currencyCode = 'TRY',
        public ?float $exchangeRate = null,
        public array $notes = [],
        public ?string $uuid = null,
        public ?string $templateUuid = null,
        public ?string $templateBase64String = null,
        public ?string $accountingCost = null,
        public bool $isDespatch = false,
        public ?ReceiverRequest $companyInfo = null,
        /** Tek sipariş referansı — {IssueDate: 'YYYY-MM-DD', Value: 'siparis-no'} */
        public ?array $orderReference = null,
        public array $despatchDocumentReference = [],
        public ?AdditionalDocumentReferenceRequest $orderReferenceDocument = null,
        public array $additionalDocumentReferences = [],
        public ?TaxExemptionReasonInfoRequest $taxExemptionReasonInfo = null,
        public ?PaymentTermsRequest $paymentTermsInfo = null,
        public ?PaymentMeansRequest $paymentMeansInfo = null,
        public ?OKCInfoRequest $okcInfo = null,
        public ?ESUReportInfoRequest $esuReportInfo = null,
        public ?InvoicePeriodRequest $invoicePeriod = null,
        public ?InternetInfoRequest $internetInfo = null,
        public array $returnInvoiceInfo = [],
        public array $expenses = [],
    ) {
        if ($this->invoiceLines === []) {
            throw new \InvalidArgumentException('Faturada en az bir kalem (InvoiceLines) bulunmalıdır.');
        }

        foreach ($this->invoiceLines as $i => $line) {
            if (!$line instanceof InvoiceLineRequest) {
                throw new \InvalidArgumentException(
                    "invoiceLines[{$i}] bir InvoiceLineRequest nesnesi olmalıdır."
                );
            }
        }

        if ($this->exchangeRate !== null && $this->exchangeRate <= 0.0) {
            throw new \InvalidArgumentException('Döviz kuru (ExchangeRate) sıfırdan büyük olmalıdır.');
        }

        if ($this->salesPlatform === SalesPlatform::Internet && $this->sendType !== SendType::Electronic) {
            throw new \InvalidArgumentException(
                'SalesPlatform::Internet olduğunda SendType::Electronic zorunludur.'
            );
        }

        foreach ($this->returnInvoiceInfo as $i => $item) {
            if (!$item instanceof ReturnInvoiceInfoRequest) {
                throw new \InvalidArgumentException(
                    "returnInvoiceInfo[{$i}] bir ReturnInvoiceInfoRequest nesnesi olmalıdır."
                );
            }
        }

        foreach ($this->expenses as $i => $item) {
            if (!$item instanceof ExpensesRequest) {
                throw new \InvalidArgumentException(
                    "expenses[{$i}] bir ExpensesRequest nesnesi olmalıdır."
                );
            }
        }

        foreach ($this->additionalDocumentReferences as $i => $item) {
            if (!$item instanceof AdditionalDocumentReferenceRequest) {
                throw new \InvalidArgumentException(
                    "additionalDocumentReferences[{$i}] bir AdditionalDocumentReferenceRequest nesnesi olmalıdır."
                );
            }
        }
    }

    public function toArray(): array
    {
        $invoiceInfo = $this->filterNulls([
            'UUID'                      => $this->uuid,
            'TemplateUUID'              => $this->templateUuid,
            'TemplateBase64String'      => $this->templateBase64String,
            'InvoiceType'               => $this->invoiceType->value,
            'InvoiceSerieOrNumber'      => $this->invoiceSerieOrNumber,
            'IssueDate'                 => $this->issueDate->format('Y-m-d\TH:i:s\Z'),
            'CurrencyCode'              => $this->currencyCode,
            'ExchangeRate'              => $this->exchangeRate,
            'SendType'                  => $this->sendType->value,
            'SalesPlatform'             => $this->salesPlatform->value,
            'AccountingCost'            => $this->accountingCost,
            'ISDespatch'                => $this->isDespatch ?: null,
            'OrderReference'            => $this->orderReference,
            'DespatchDocumentReference' => $this->despatchDocumentReference !== [] ? $this->despatchDocumentReference : null,
        ]);

        if ($this->orderReferenceDocument !== null) {
            $invoiceInfo['OrderReferenceDocument'] = $this->orderReferenceDocument->toArray();
        }

        if ($this->additionalDocumentReferences !== []) {
            $invoiceInfo['AdditionalDocumentReferences'] = array_map(
                static fn (AdditionalDocumentReferenceRequest $r) => $r->toArray(),
                $this->additionalDocumentReferences,
            );
        }

        if ($this->taxExemptionReasonInfo !== null) {
            $invoiceInfo['TaxExemptionReasonInfo'] = $this->taxExemptionReasonInfo->toArray();
        }

        if ($this->paymentTermsInfo !== null) {
            $invoiceInfo['PaymentTermsInfo'] = $this->paymentTermsInfo->toArray();
        }

        if ($this->paymentMeansInfo !== null) {
            $invoiceInfo['PaymentMeansInfo'] = $this->paymentMeansInfo->toArray();
        }

        if ($this->okcInfo !== null) {
            $invoiceInfo['OKCInfo'] = $this->okcInfo->toArray();
        }

        if ($this->esuReportInfo !== null) {
            $invoiceInfo['ESUReportInfo'] = $this->esuReportInfo->toArray();
        }

        if ($this->invoicePeriod !== null) {
            $invoiceInfo['InvoicePeriod'] = $this->invoicePeriod->toArray();
        }

        if ($this->returnInvoiceInfo !== []) {
            $invoiceInfo['ReturnInvoiceInfo'] = array_map(
                static fn (ReturnInvoiceInfoRequest $r) => $r->toArray(),
                $this->returnInvoiceInfo,
            );
        }

        if ($this->expenses !== []) {
            $invoiceInfo['Expenses'] = array_map(
                static fn (ExpensesRequest $e) => $e->toArray(),
                $this->expenses,
            );
        }

        if ($this->internetInfo !== null) {
            $invoiceInfo['InternetInfo'] = $this->internetInfo->toArray();
        }

        $archiveInvoice = [
            'InvoiceInfo'  => $invoiceInfo,
            'CustomerInfo' => $this->customerInfo->toArray(),
            'InvoiceLines' => array_map(
                static fn (InvoiceLineRequest $l) => $l->toArray(),
                $this->invoiceLines,
            ),
        ];

        if ($this->companyInfo !== null) {
            $archiveInvoice['CompanyInfo'] = $this->companyInfo->toArray();
        }

        if ($this->notes !== []) {
            $archiveInvoice['Notes'] = $this->notes;
        }

        return ['ArchiveInvoice' => $archiveInvoice];
    }
}
