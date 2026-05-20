<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Requests\ValueObjects\AdditionalDocumentReferenceRequest;
use Nilvera\Requests\ValueObjects\ESUReportInfoRequest;
use Nilvera\Requests\ValueObjects\ExpensesRequest;
use Nilvera\Requests\ValueObjects\ExportCustomerInfoRequest;
use Nilvera\Requests\ValueObjects\InvestmentIncentiveRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\InvoicePeriodRequest;
use Nilvera\Requests\ValueObjects\OKCInfoRequest;
use Nilvera\Requests\ValueObjects\PaymentMeansRequest;
use Nilvera\Requests\ValueObjects\PaymentTermsRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ReturnInvoiceInfoRequest;
use Nilvera\Requests\ValueObjects\SGKInfoRequest;
use Nilvera\Requests\ValueObjects\TaxExemptionReasonInfoRequest;

/**
 * e-Fatura gönderme isteği — POST /einvoice/Send/Model
 *
 * Üretilen toArray() çıktısı şu üst düzey JSON yapısını oluşturur:
 * {
 *   "EInvoice": {
 *     "InvoiceInfo":        { ... },
 *     "CompanyInfo":        { ... },   (opsiyonel — gönderen şirket)
 *     "CustomerInfo":       { ... },
 *     "BuyerCustomerInfo":  { ... },   (opsiyonel — komisyoncu senaryosunda asıl alıcı)
 *     "ExportCustomerInfo": { ... },   (opsiyonel — ihracat faturalarında yabancı alıcı)
 *     "InvoiceLines":       [ ... ],
 *     "Notes":              [ "..." ]
 *   },
 *   "CustomerAlias": "urn:mail:..."   (opsiyonel — GIB'de kayıtlı alıcı için)
 * }
 *
 * Önemli notlar:
 * - İhracat faturalarında (InvoiceProfile::Export) CustomerInfo yerine ExportCustomerInfo kullanın.
 * - InvoiceType::SGK olduğunda accountingCost, invoicePeriod ve sgkInfo doldurun.
 * - InvoiceType::Return olduğunda returnInvoiceInfo doldurun.
 * - InvoiceType::Exemption veya ExciseDuty olduğunda taxExemptionReasonInfo.KDVExemptionReasonCode zorunludur.
 */
readonly class SendInvoiceRequest extends AbstractRequest
{
    /**
     * @param InvoiceLineRequest[]                         $invoiceLines              En az bir kalem zorunludur
     * @param string[]                                     $notes                     Fatura notları
     * @param array<array{IssueDate:string,Value:string}>  $despatchDocumentReference İrsaliye referansları (birden fazla)
     * @param AdditionalDocumentReferenceRequest[]         $additionalDocumentReferences Ek belgeler/dosyalar
     * @param ReturnInvoiceInfoRequest[]                   $returnInvoiceInfo         İade fatura bilgileri (IADE tipinde)
     * @param ExpensesRequest[]                            $expenses                  Masraf kalemleri (HKS)
     */
    public function __construct(
        public ReceiverRequest $customerInfo,
        public array $invoiceLines,
        public \DateTimeImmutable $issueDate,
        /** GIB sisteminde kayıtlı alıcı için e-posta veya ETTN alias */
        public string $customerAlias,
        /** 16 haneli fatura numarası veya 3 haneli seri kodu */
        public string $invoiceSerieOrNumber,
        public InvoiceProfile $invoiceProfile = InvoiceProfile::Basic,
        public InvoiceType $invoiceType = InvoiceType::Sales,
        /** ISO 4217 para birimi kodu */
        public string $currencyCode = 'TRY',
        /** TRY dışındaki para birimlerinde döviz kuru */
        public ?float $exchangeRate = null,
        public array $notes = [],
        public ?string $uuid = null,
        public ?string $templateUuid = null,
        public ?string $templateBase64String = null,
        /** Tek sipariş referansı */
        public ?array $orderReference = null,
        public array $despatchDocumentReference = [],
        /** Sipariş belgesi eki */
        public ?AdditionalDocumentReferenceRequest $orderReferenceDocument = null,
        public array $additionalDocumentReferences = [],
        /** KDV/ÖTV muafiyet sebebi (ISTISNA, IHRACKAYITLI tiplerinde gerekli) */
        public ?TaxExemptionReasonInfoRequest $taxExemptionReasonInfo = null,
        /** Ödeme koşulları */
        public ?PaymentTermsRequest $paymentTermsInfo = null,
        /** Ödeme şekli */
        public ?PaymentMeansRequest $paymentMeansInfo = null,
        /** ÖKC fiş bilgisi */
        public ?OKCInfoRequest $okcInfo = null,
        /** ESU rapor bilgisi (SARJ/enerji faturaları) */
        public ?ESUReportInfoRequest $esuReportInfo = null,
        public array $returnInvoiceInfo = [],
        /** SGK fatura tipi için SGK fatura alt tipi (ör: SAGLIK_MED) */
        public ?string $accountingCost = null,
        /** SGK fatura dönem bilgisi */
        public ?InvoicePeriodRequest $invoicePeriod = null,
        /** SGK şirket kayıt bilgileri */
        public ?SGKInfoRequest $sgkInfo = null,
        public array $expenses = [],
        /** Yatırım teşvik belgesi bilgisi */
        public ?InvestmentIncentiveRequest $investmentIncentive = null,
        /** IDIS (İnşaat Demiri İzleme Sistemi) sevkiyat numarası */
        public ?string $shipmentNumber = null,
        /** İhracat faturasında sigorta bedeli */
        public ?float $insuranceValueAmount = null,
        /** İhracat faturasında navlun bedeli */
        public ?float $declaredForCarriageValueAmount = null,
        /** Gönderen şirket bilgisi (varsayılan: API hesabına bağlı şirket) */
        public ?ReceiverRequest $companyInfo = null,
        /** Komisyoncu senaryosunda asıl alıcı bilgisi */
        public ?ReceiverRequest $buyerCustomerInfo = null,
        /** İhracat faturalarında yabancı alıcı bilgisi (CustomerInfo yerine kullanın) */
        public ?ExportCustomerInfoRequest $exportCustomerInfo = null,
    ) {
        if (trim($this->customerAlias) === '') {
            throw new \InvalidArgumentException('CustomerAlias boş olamaz.');
        }

        if (trim($this->invoiceSerieOrNumber) === '') {
            throw new \InvalidArgumentException('InvoiceSerieOrNumber boş olamaz.');
        }

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

        if ($this->insuranceValueAmount !== null && $this->insuranceValueAmount < 0.0) {
            throw new \InvalidArgumentException('Sigorta bedeli (InsuranceValueAmount) negatif olamaz.');
        }

        if ($this->declaredForCarriageValueAmount !== null && $this->declaredForCarriageValueAmount < 0.0) {
            throw new \InvalidArgumentException('Navlun bedeli (DeclaredForCarriageValueAmount) negatif olamaz.');
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
            'UUID'                          => $this->uuid,
            'TemplateUUID'                  => $this->templateUuid,
            'TemplateBase64String'          => $this->templateBase64String,
            'InvoiceType'                   => $this->invoiceType->value,
            'InvoiceProfile'                => $this->invoiceProfile->value,
            'InvoiceSerieOrNumber'          => $this->invoiceSerieOrNumber,
            'IssueDate'                     => $this->issueDate->format('Y-m-d\TH:i:s\Z'),
            'CurrencyCode'                  => $this->currencyCode,
            'ExchangeRate'                  => $this->exchangeRate,
            'OrderReference'                => $this->orderReference,
            'DespatchDocumentReference'     => $this->despatchDocumentReference !== [] ? $this->despatchDocumentReference : null,
            'AccountingCost'                => $this->accountingCost,
            'ShipmentNumber'                => $this->shipmentNumber,
            'InsuranceValueAmount'          => $this->insuranceValueAmount,
            'DeclaredForCarriageValueAmount' => $this->declaredForCarriageValueAmount,
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

        if ($this->returnInvoiceInfo !== []) {
            $invoiceInfo['ReturnInvoiceInfo'] = array_map(
                static fn (ReturnInvoiceInfoRequest $r) => $r->toArray(),
                $this->returnInvoiceInfo,
            );
        }

        if ($this->invoicePeriod !== null) {
            $invoiceInfo['InvoicePeriod'] = $this->invoicePeriod->toArray();
        }

        if ($this->sgkInfo !== null) {
            $invoiceInfo['SGKInfo'] = $this->sgkInfo->toArray();
        }

        if ($this->expenses !== []) {
            $invoiceInfo['Expenses'] = array_map(
                static fn (ExpensesRequest $e) => $e->toArray(),
                $this->expenses,
            );
        }

        if ($this->investmentIncentive !== null) {
            $invoiceInfo['InvestmentIncentive'] = $this->investmentIncentive->toArray();
        }

        $eInvoice = [
            'InvoiceInfo'  => $invoiceInfo,
            'CustomerInfo' => $this->customerInfo->toArray(),
            'InvoiceLines' => array_map(
                static fn (InvoiceLineRequest $l) => $l->toArray(),
                $this->invoiceLines,
            ),
        ];

        if ($this->companyInfo !== null) {
            $eInvoice['CompanyInfo'] = $this->companyInfo->toArray();
        }

        if ($this->buyerCustomerInfo !== null) {
            $eInvoice['BuyerCustomerInfo'] = $this->buyerCustomerInfo->toArray();
        }

        if ($this->exportCustomerInfo !== null) {
            $eInvoice['ExportCustomerInfo'] = $this->exportCustomerInfo->toArray();
        }

        if ($this->notes !== []) {
            $eInvoice['Notes'] = $this->notes;
        }

        $payload = [
            'EInvoice'      => $eInvoice,
            'CustomerAlias' => $this->customerAlias,
        ];

        return $payload;
    }
}
