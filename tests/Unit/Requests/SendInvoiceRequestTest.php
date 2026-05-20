<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Requests;

use Nilvera\Enums\ExpenseType;
use Nilvera\Enums\InvestmentIncentiveExpenseType;
use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\ProductType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\AdditionalDocumentReferenceRequest;
use Nilvera\Requests\ValueObjects\AttachmentRequest;
use Nilvera\Requests\ValueObjects\ESUReportInfoRequest;
use Nilvera\Requests\ValueObjects\ExpensesRequest;
use Nilvera\Requests\ValueObjects\ExportCustomerInfoRequest;
use Nilvera\Requests\ValueObjects\InvestmentIncentiveRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\InvoicePeriodRequest;
use Nilvera\Requests\ValueObjects\MedicalDeviceRequest;
use Nilvera\Requests\ValueObjects\MedicineAndMedicalDeviceRequest;
use Nilvera\Requests\ValueObjects\MedicineRequest;
use Nilvera\Requests\ValueObjects\OKCInfoRequest;
use Nilvera\Requests\ValueObjects\PaymentMeansRequest;
use Nilvera\Requests\ValueObjects\PaymentTermsRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ReturnInvoiceInfoRequest;
use Nilvera\Requests\ValueObjects\SGKInfoRequest;
use Nilvera\Requests\ValueObjects\TaxExemptionReasonInfoRequest;
use PHPUnit\Framework\TestCase;

class SendInvoiceRequestTest extends TestCase
{
    private ReceiverRequest $receiver;
    private InvoiceLineRequest $line;

    protected function setUp(): void
    {
        $this->receiver = new ReceiverRequest(
            taxNumber: '1234567890',
            name:      'Test Sirket',
            address:   'Test Mah. No:1',
            district:  'Kadikoy',
            city:      'Istanbul',
        );

        $this->line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20);
    }

    private function makeRequest(array $overrides = []): SendInvoiceRequest
    {
        return new SendInvoiceRequest(...array_merge([
            'customerInfo'        => $this->receiver,
            'invoiceLines'        => [$this->line],
            'issueDate'           => new \DateTimeImmutable('2026-05-14T10:00:00'),
            'customerAlias'       => 'urn:mail:test@sirket.com.tr',
            'invoiceSerieOrNumber' => 'EFT',
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // Top-level structure
    // -------------------------------------------------------------------------

    public function test_to_array_has_required_top_level_keys(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertArrayHasKey('EInvoice', $data);
        $this->assertArrayHasKey('InvoiceInfo', $data['EInvoice']);
        $this->assertArrayHasKey('CustomerInfo', $data['EInvoice']);
        $this->assertArrayHasKey('InvoiceLines', $data['EInvoice']);
    }

    public function test_root_key_is_EInvoice_not_ArchiveInvoice(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertArrayHasKey('EInvoice', $data);
        $this->assertArrayNotHasKey('ArchiveInvoice', $data);
    }

    public function test_to_array_always_includes_customer_alias(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertArrayHasKey('CustomerAlias', $data);
        $this->assertSame('urn:mail:test@sirket.com.tr', $data['CustomerAlias']);
    }

    public function test_to_array_includes_customer_alias_when_set(): void
    {
        $data = $this->makeRequest([
            'customerAlias' => 'urn:mail:muhasebe@sirket.com.tr',
        ])->toArray();

        $this->assertSame('urn:mail:muhasebe@sirket.com.tr', $data['CustomerAlias']);
    }

    public function test_to_array_omits_notes_when_empty(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertArrayNotHasKey('Notes', $data['EInvoice']);
    }

    public function test_to_array_includes_notes_when_set(): void
    {
        $data = $this->makeRequest(['notes' => ['30 gun vadeli', 'KDV dahildir']])->toArray();

        $this->assertSame(['30 gun vadeli', 'KDV dahildir'], $data['EInvoice']['Notes']);
    }

    // -------------------------------------------------------------------------
    // InvoiceInfo — defaults
    // -------------------------------------------------------------------------

    public function test_invoice_info_has_correct_defaults(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame(InvoiceType::Sales->value, $info['InvoiceType']);
        $this->assertSame(InvoiceProfile::Basic->value, $info['InvoiceProfile']);
        $this->assertSame('TRY', $info['CurrencyCode']);
    }

    public function test_invoice_info_issue_date_is_iso8601(): void
    {
        $info = $this->makeRequest([
            'issueDate' => new \DateTimeImmutable('2026-05-14T10:00:00'),
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame('2026-05-14T10:00:00Z', $info['IssueDate']);
    }

    public function test_invoice_info_omits_uuid_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('UUID', $info);
    }

    public function test_invoice_info_includes_uuid_when_set(): void
    {
        $uuid = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
        $info = $this->makeRequest(['uuid' => $uuid])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame($uuid, $info['UUID']);
    }

    public function test_invoice_info_includes_exchange_rate_when_set(): void
    {
        $info = $this->makeRequest([
            'currencyCode' => 'USD',
            'exchangeRate' => 32.5,
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame('USD', $info['CurrencyCode']);
        $this->assertSame(32.5, $info['ExchangeRate']);
    }

    public function test_invoice_info_includes_serie_or_number(): void
    {
        $info = $this->makeRequest(['invoiceSerieOrNumber' => 'EFT'])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame('EFT', $info['InvoiceSerieOrNumber']);
    }

    public function test_invoice_info_includes_template_uuid(): void
    {
        $uuid = '94e8b735-1361-4d6f-a4a6-3745b62239c8';
        $info = $this->makeRequest(['templateUuid' => $uuid])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame($uuid, $info['TemplateUUID']);
    }

    // -------------------------------------------------------------------------
    // InvoiceLines
    // -------------------------------------------------------------------------

    public function test_invoice_lines_are_mapped_to_array(): void
    {
        $data = $this->makeRequest()->toArray()['EInvoice'];

        $this->assertCount(1, $data['InvoiceLines']);
        $this->assertSame('Urun', $data['InvoiceLines'][0]['Name']);
    }

    public function test_multiple_lines_are_included(): void
    {
        $line2 = InvoiceLineRequest::make('Hizmet', 2, UnitType::Piece, 500, 10);
        $data  = $this->makeRequest(['invoiceLines' => [$this->line, $line2]])->toArray()['EInvoice'];

        $this->assertCount(2, $data['InvoiceLines']);
    }

    // -------------------------------------------------------------------------
    // OrderReference & DespatchDocumentReference
    // -------------------------------------------------------------------------

    public function test_order_reference_is_included_when_set(): void
    {
        $ref  = ['IssueDate' => '2026-01-01T00:00:00Z', 'Value' => 'SIP-001'];
        $info = $this->makeRequest(['orderReference' => $ref])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame($ref, $info['OrderReference']);
    }

    public function test_order_reference_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('OrderReference', $info);
    }

    public function test_despatch_document_reference_is_included(): void
    {
        $refs = [
            ['IssueDate' => '2026-01-01T00:00:00Z', 'Value' => 'IRS-001'],
            ['IssueDate' => '2026-01-02T00:00:00Z', 'Value' => 'IRS-002'],
        ];
        $info = $this->makeRequest(['despatchDocumentReference' => $refs])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame($refs, $info['DespatchDocumentReference']);
    }

    public function test_despatch_document_reference_omitted_when_empty(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('DespatchDocumentReference', $info);
    }

    // -------------------------------------------------------------------------
    // OrderReferenceDocument & AdditionalDocumentReferences
    // -------------------------------------------------------------------------

    public function test_order_reference_document_is_included(): void
    {
        $doc  = new AdditionalDocumentReferenceRequest(
            id:               'DOC-001',
            issueDate:        new \DateTimeImmutable('2026-01-01'),
            documentType:     'ORDER',
            documentTypeCode: 'ORDER',
        );
        $info = $this->makeRequest(['orderReferenceDocument' => $doc])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('OrderReferenceDocument', $info);
        $this->assertSame('DOC-001', $info['OrderReferenceDocument']['ID']);
    }

    public function test_order_reference_document_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('OrderReferenceDocument', $info);
    }

    public function test_additional_document_references_are_included(): void
    {
        $doc  = new AdditionalDocumentReferenceRequest(
            id:               'EK-001',
            issueDate:        new \DateTimeImmutable('2026-01-01'),
            documentType:     'DOSYA',
            documentTypeCode: 'DOSYA',
        );
        $info = $this->makeRequest(['additionalDocumentReferences' => [$doc]])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('AdditionalDocumentReferences', $info);
        $this->assertCount(1, $info['AdditionalDocumentReferences']);
        $this->assertSame('EK-001', $info['AdditionalDocumentReferences'][0]['ID']);
    }

    public function test_additional_document_references_omitted_when_empty(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('AdditionalDocumentReferences', $info);
    }

    public function test_additional_document_reference_with_attachment(): void
    {
        $attachment = new AttachmentRequest(
            base64Data: base64_encode('dosya-içeriği'),
            mimeCode:   'application/pdf',
            fileName:   'belge.pdf',
        );
        $doc = new AdditionalDocumentReferenceRequest(
            id:               'EK-001',
            issueDate:        new \DateTimeImmutable('2026-01-01'),
            documentType:     'DOSYA',
            documentTypeCode: 'DOSYA',
            attachment:       $attachment,
        );
        $info = $this->makeRequest(['additionalDocumentReferences' => [$doc]])->toArray()['EInvoice']['InvoiceInfo'];

        $ref = $info['AdditionalDocumentReferences'][0];
        $this->assertArrayHasKey('Attachment', $ref);
        $this->assertSame('application/pdf', $ref['Attachment']['MimeCode']);
        $this->assertSame('belge.pdf', $ref['Attachment']['FileName']);
    }

    // -------------------------------------------------------------------------
    // TaxExemptionReasonInfo
    // -------------------------------------------------------------------------

    public function test_tax_exemption_reason_info_is_included(): void
    {
        $exemption = new TaxExemptionReasonInfoRequest(kdvExemptionReasonCode: '351');
        $info      = $this->makeRequest([
            'invoiceType'            => InvoiceType::Exemption,
            'taxExemptionReasonInfo' => $exemption,
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('TaxExemptionReasonInfo', $info);
        $this->assertSame('351', $info['TaxExemptionReasonInfo']['KDVExemptionReasonCode']);
    }

    public function test_tax_exemption_reason_info_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('TaxExemptionReasonInfo', $info);
    }

    public function test_tax_exemption_with_otv_code(): void
    {
        $exemption = new TaxExemptionReasonInfoRequest(
            kdvExemptionReasonCode: '351',
            otvExemptionReasonCode: '151',
        );
        $info = $this->makeRequest(['taxExemptionReasonInfo' => $exemption])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame('151', $info['TaxExemptionReasonInfo']['OTVExemptionReasonCode']);
    }

    // -------------------------------------------------------------------------
    // PaymentTermsInfo & PaymentMeansInfo
    // -------------------------------------------------------------------------

    public function test_payment_terms_info_is_included(): void
    {
        $terms = new PaymentTermsRequest(note: '30 gun vadeli');
        $info  = $this->makeRequest(['paymentTermsInfo' => $terms])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('PaymentTermsInfo', $info);
        $this->assertSame('30 gun vadeli', $info['PaymentTermsInfo']['Note']);
    }

    public function test_payment_terms_info_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('PaymentTermsInfo', $info);
    }

    public function test_payment_means_info_is_included(): void
    {
        $means = new PaymentMeansRequest(
            code:        '42',
            channelCode: 'TR',
            dueDate:     new \DateTimeImmutable('2026-06-14'),
        );
        $info = $this->makeRequest(['paymentMeansInfo' => $means])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('PaymentMeansInfo', $info);
        $this->assertSame('42', $info['PaymentMeansInfo']['Code']);
    }

    public function test_payment_means_info_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('PaymentMeansInfo', $info);
    }

    // -------------------------------------------------------------------------
    // OKCInfo
    // -------------------------------------------------------------------------

    public function test_okc_info_is_included(): void
    {
        $okc  = new OKCInfoRequest(
            id:        'FISSNO001',
            issueDate: new \DateTimeImmutable('2026-05-14'),
            time:      '10:30:00',
            zNo:       'Z001',
        );
        $info = $this->makeRequest(['okcInfo' => $okc])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('OKCInfo', $info);
        $this->assertSame('FISSNO001', $info['OKCInfo']['ID']);
        $this->assertSame('Z001', $info['OKCInfo']['ZNo']);
    }

    public function test_okc_info_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('OKCInfo', $info);
    }

    // -------------------------------------------------------------------------
    // ESUReportInfo
    // -------------------------------------------------------------------------

    public function test_esu_report_info_is_included(): void
    {
        $esu  = new ESUReportInfoRequest(
            id:        'ESU-001',
            issueDate: new \DateTimeImmutable('2026-05-14'),
        );
        $info = $this->makeRequest([
            'invoiceType'   => InvoiceType::EVCharging,
            'esuReportInfo' => $esu,
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('ESUReportInfo', $info);
        $this->assertSame('ESU-001', $info['ESUReportInfo']['ID']);
    }

    public function test_esu_report_info_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('ESUReportInfo', $info);
    }

    // -------------------------------------------------------------------------
    // ReturnInvoiceInfo
    // -------------------------------------------------------------------------

    public function test_return_invoice_info_is_included(): void
    {
        $ret  = new ReturnInvoiceInfoRequest(
            invoiceNumber: 'EFT2026000000001',
            issueDate:     new \DateTimeImmutable('2026-01-01'),
        );
        $info = $this->makeRequest([
            'invoiceType'       => InvoiceType::Return,
            'returnInvoiceInfo' => [$ret],
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('ReturnInvoiceInfo', $info);
        $this->assertCount(1, $info['ReturnInvoiceInfo']);
        $this->assertSame('EFT2026000000001', $info['ReturnInvoiceInfo'][0]['InvoiceNumber']);
    }

    public function test_return_invoice_info_omitted_when_empty(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('ReturnInvoiceInfo', $info);
    }

    public function test_multiple_return_invoices_are_included(): void
    {
        $ret1 = new ReturnInvoiceInfoRequest('EFT2026000000001', new \DateTimeImmutable('2026-01-01'));
        $ret2 = new ReturnInvoiceInfoRequest('EFT2026000000002', new \DateTimeImmutable('2026-01-02'));
        $info = $this->makeRequest([
            'invoiceType'       => InvoiceType::Return,
            'returnInvoiceInfo' => [$ret1, $ret2],
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertCount(2, $info['ReturnInvoiceInfo']);
    }

    // -------------------------------------------------------------------------
    // AccountingCost & InvoicePeriod & SGKInfo
    // -------------------------------------------------------------------------

    public function test_accounting_cost_is_included_for_sgk(): void
    {
        $info = $this->makeRequest([
            'invoiceType'    => InvoiceType::SGK,
            'accountingCost' => 'SAGLIK_MED',
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame('SAGLIK_MED', $info['AccountingCost']);
    }

    public function test_accounting_cost_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('AccountingCost', $info);
    }

    public function test_invoice_period_is_included(): void
    {
        $period = new InvoicePeriodRequest(
            startDate: new \DateTimeImmutable('2026-01-01'),
            endDate:   new \DateTimeImmutable('2026-01-31'),
        );
        $info = $this->makeRequest([
            'invoiceType'   => InvoiceType::SGK,
            'invoicePeriod' => $period,
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('InvoicePeriod', $info);
        $this->assertStringContainsString('2026-01-01', $info['InvoicePeriod']['StartDate']);
        $this->assertStringContainsString('2026-01-31', $info['InvoicePeriod']['EndDate']);
    }

    public function test_invoice_period_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('InvoicePeriod', $info);
    }

    public function test_sgk_info_is_included(): void
    {
        $sgk  = new SGKInfoRequest(
            registerName:   'SGK Merkez',
            documentNumber: 'SGK-DOC-001',
            registerCode:   'SGK-001',
        );
        $info = $this->makeRequest([
            'invoiceType' => InvoiceType::SGK,
            'sgkInfo'     => $sgk,
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('SGKInfo', $info);
        $this->assertSame('SGK Merkez', $info['SGKInfo']['RegisterName']);
        $this->assertSame('SGK-DOC-001', $info['SGKInfo']['DocumentNumber']);
        $this->assertSame('SGK-001', $info['SGKInfo']['RegisterCode']);
    }

    public function test_sgk_info_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('SGKInfo', $info);
    }

    // -------------------------------------------------------------------------
    // Expenses
    // -------------------------------------------------------------------------

    public function test_expenses_are_included(): void
    {
        $expense = new ExpensesRequest(
            expenseType: ExpenseType::HKSCommission,
            percent:     5.0,
            amount:      250.0,
        );
        $info = $this->makeRequest([
            'invoiceType' => InvoiceType::HKSSales,
            'expenses'    => [$expense],
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('Expenses', $info);
        $this->assertCount(1, $info['Expenses']);
        $this->assertSame(ExpenseType::HKSCommission->value, $info['Expenses'][0]['ExpenseType']);
    }

    public function test_expenses_omitted_when_empty(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('Expenses', $info);
    }

    // -------------------------------------------------------------------------
    // InvestmentIncentive
    // -------------------------------------------------------------------------

    public function test_investment_incentive_is_included(): void
    {
        $incentive = new InvestmentIncentiveRequest(
            documentNumber: 'YT-2026-001',
            documentDate:   new \DateTimeImmutable('2026-01-15'),
        );
        $info = $this->makeRequest([
            'invoiceProfile'      => InvoiceProfile::InvestmentIncentive,
            'investmentIncentive' => $incentive,
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('InvestmentIncentive', $info);
        $this->assertSame('YT-2026-001', $info['InvestmentIncentive']['DocumentNumber']);
        $this->assertStringContainsString('2026-01-15', $info['InvestmentIncentive']['DocumentDate']);
    }

    public function test_investment_incentive_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('InvestmentIncentive', $info);
    }

    // -------------------------------------------------------------------------
    // ShipmentNumber (IDIS)
    // -------------------------------------------------------------------------

    public function test_shipment_number_is_included(): void
    {
        $info = $this->makeRequest([
            'invoiceProfile' => InvoiceProfile::IDIS,
            'shipmentNumber' => 'SE-1234567',
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame('SE-1234567', $info['ShipmentNumber']);
    }

    public function test_shipment_number_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('ShipmentNumber', $info);
    }

    // -------------------------------------------------------------------------
    // InsuranceValueAmount & DeclaredForCarriageValueAmount (ihracat)
    // -------------------------------------------------------------------------

    public function test_insurance_value_amount_is_included(): void
    {
        $info = $this->makeRequest([
            'invoiceProfile'        => InvoiceProfile::Export,
            'insuranceValueAmount'  => 500.0,
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame(500.0, $info['InsuranceValueAmount']);
    }

    public function test_insurance_value_amount_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('InsuranceValueAmount', $info);
    }

    public function test_declared_for_carriage_value_amount_is_included(): void
    {
        $info = $this->makeRequest([
            'invoiceProfile'                 => InvoiceProfile::Export,
            'declaredForCarriageValueAmount' => 1200.0,
        ])->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame(1200.0, $info['DeclaredForCarriageValueAmount']);
    }

    public function test_declared_for_carriage_value_amount_omitted_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertArrayNotHasKey('DeclaredForCarriageValueAmount', $info);
    }

    // -------------------------------------------------------------------------
    // CompanyInfo & BuyerCustomerInfo
    // -------------------------------------------------------------------------

    public function test_company_info_is_included_when_set(): void
    {
        $company = new ReceiverRequest(
            taxNumber: '6310540565',
            name:      'Nilvera Yazilim',
            address:   'Adres',
            district:  'Melikgazi',
            city:      'Kayseri',
            taxOffice: 'Erciyes',
        );
        $data = $this->makeRequest(['companyInfo' => $company])->toArray()['EInvoice'];

        $this->assertArrayHasKey('CompanyInfo', $data);
        $this->assertSame('6310540565', $data['CompanyInfo']['TaxNumber']);
    }

    public function test_company_info_omitted_when_null(): void
    {
        $data = $this->makeRequest()->toArray()['EInvoice'];

        $this->assertArrayNotHasKey('CompanyInfo', $data);
    }

    public function test_buyer_customer_info_is_included_when_set(): void
    {
        $buyer = new ReceiverRequest(
            taxNumber: '1234567890',
            name:      'Gercek Alici AS',
            address:   'Alici Adres',
            district:  'Sisli',
            city:      'Istanbul',
        );
        $data = $this->makeRequest([
            'invoiceType'       => InvoiceType::Commissioner,
            'buyerCustomerInfo' => $buyer,
        ])->toArray()['EInvoice'];

        $this->assertArrayHasKey('BuyerCustomerInfo', $data);
        $this->assertSame('1234567890', $data['BuyerCustomerInfo']['TaxNumber']);
    }

    public function test_buyer_customer_info_omitted_when_null(): void
    {
        $data = $this->makeRequest()->toArray()['EInvoice'];

        $this->assertArrayNotHasKey('BuyerCustomerInfo', $data);
    }

    // -------------------------------------------------------------------------
    // ExportCustomerInfo
    // -------------------------------------------------------------------------

    public function test_export_customer_info_is_included_when_set(): void
    {
        $exportCustomer = new ExportCustomerInfoRequest(
            taxNumber:             'DE123456789',
            legalRegistrationName: 'German GmbH',
            address:               'Musterstraße 1',
            district:              'Mitte',
            city:                  'Berlin',
            country:               'DE',
        );
        $data = $this->makeRequest([
            'invoiceProfile'     => InvoiceProfile::Export,
            'exportCustomerInfo' => $exportCustomer,
        ])->toArray()['EInvoice'];

        $this->assertArrayHasKey('ExportCustomerInfo', $data);
        $this->assertSame('DE123456789', $data['ExportCustomerInfo']['TaxNumber']);
        $this->assertSame('German GmbH', $data['ExportCustomerInfo']['LegalRegistrationName']);
    }

    public function test_export_customer_info_omitted_when_null(): void
    {
        $data = $this->makeRequest()->toArray()['EInvoice'];

        $this->assertArrayNotHasKey('ExportCustomerInfo', $data);
    }

    // -------------------------------------------------------------------------
    // InvoiceLine — InvestmentIncentiveExpenseType & MedicineAndMedicalDevice
    // -------------------------------------------------------------------------

    public function test_line_includes_investment_incentive_expense_type(): void
    {
        $line = new InvoiceLineRequest(
            name:                           'Makina',
            quantity:                       1,
            unitType:                       UnitType::Piece,
            price:                          50000.0,
            allowanceTotal:                 0.0,
            kdvPercent:                     20.0,
            kdvTotal:                       10000.0,
            investmentIncentiveExpenseType: InvestmentIncentiveExpenseType::MachineAndSoftware,
        );

        $data = $line->toArray();

        $this->assertSame(
            InvestmentIncentiveExpenseType::MachineAndSoftware->value,
            $data['InvestmentIncentiveExpenseType']
        );
    }

    public function test_line_investment_incentive_expense_type_omitted_when_null(): void
    {
        $data = $this->line->toArray();

        $this->assertArrayNotHasKey('InvestmentIncentiveExpenseType', $data);
    }

    public function test_line_includes_medicine_and_medical_device(): void
    {
        $med = new MedicineAndMedicalDeviceRequest(
            productType: ProductType::Medicine,
            medicine:    [
                new MedicineRequest(gtin: 'GTIN-001', batchNumber: 'BATCH-A'),
            ],
        );
        $line = new InvoiceLineRequest(
            name:                    'Aspirin',
            quantity:                10,
            unitType:                UnitType::Piece,
            price:                   5.0,
            allowanceTotal:          0.0,
            kdvPercent:              10.0,
            kdvTotal:                5.0,
            medicineAndMedicalDevice: $med,
        );

        $data = $line->toArray();

        $this->assertArrayHasKey('MedicineAndMedicalDevice', $data);
        $this->assertSame(ProductType::Medicine->value, $data['MedicineAndMedicalDevice']['ProductType']);
        $this->assertCount(1, $data['MedicineAndMedicalDevice']['Medicine']);
        $this->assertSame('GTIN-001', $data['MedicineAndMedicalDevice']['Medicine'][0]['GTIN']);
    }

    public function test_line_medicine_omitted_when_null(): void
    {
        $data = $this->line->toArray();

        $this->assertArrayNotHasKey('MedicineAndMedicalDevice', $data);
    }

    public function test_medical_device_in_line(): void
    {
        $med = new MedicineAndMedicalDeviceRequest(
            productType:   ProductType::MedicalDevice,
            medicalDevice: [
                new MedicalDeviceRequest(productNumber: 'PN-001', lotNumber: 'LOT-A'),
            ],
        );
        $data = $med->toArray();

        $this->assertSame(ProductType::MedicalDevice->value, $data['ProductType']);
        $this->assertArrayHasKey('MedicalDevice', $data);
        $this->assertSame('PN-001', $data['MedicalDevice'][0]['ProductNumber']);
    }

    // -------------------------------------------------------------------------
    // Validation errors
    // -------------------------------------------------------------------------

    public function test_throws_when_customer_alias_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/CustomerAlias/');
        $this->makeRequest(['customerAlias' => '']);
    }

    public function test_throws_when_invoice_serie_or_number_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/InvoiceSerieOrNumber/');
        $this->makeRequest(['invoiceSerieOrNumber' => '']);
    }

    public function test_throws_when_invoice_lines_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/InvoiceLines/');
        $this->makeRequest(['invoiceLines' => []]);
    }

    public function test_throws_when_line_is_not_invoice_line_request(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeRequest(['invoiceLines' => ['not-a-line']]);
    }

    public function test_throws_when_exchange_rate_is_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ExchangeRate/');
        $this->makeRequest(['exchangeRate' => 0.0]);
    }

    public function test_throws_when_exchange_rate_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ExchangeRate/');
        $this->makeRequest(['exchangeRate' => -1.0]);
    }

    public function test_throws_when_insurance_value_amount_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/InsuranceValueAmount/');
        $this->makeRequest(['insuranceValueAmount' => -100.0]);
    }

    public function test_throws_when_declared_carriage_amount_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/DeclaredForCarriageValueAmount/');
        $this->makeRequest(['declaredForCarriageValueAmount' => -50.0]);
    }

    public function test_throws_when_return_invoice_info_contains_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/returnInvoiceInfo/');
        $this->makeRequest(['returnInvoiceInfo' => ['not-a-return-info']]);
    }

    public function test_throws_when_expenses_contains_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/expenses/');
        $this->makeRequest(['expenses' => ['not-an-expense']]);
    }

    public function test_throws_when_additional_doc_references_contains_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/additionalDocumentReferences/');
        $this->makeRequest(['additionalDocumentReferences' => ['not-a-doc-ref']]);
    }

    // -------------------------------------------------------------------------
    // SGKInfoRequest validation
    // -------------------------------------------------------------------------

    public function test_sgk_info_throws_when_register_name_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/RegisterName/');
        new SGKInfoRequest(registerName: '', documentNumber: 'DOC-001', registerCode: 'CODE-001');
    }

    public function test_sgk_info_throws_when_document_number_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/DocumentNumber/');
        new SGKInfoRequest(registerName: 'SGK', documentNumber: '', registerCode: 'CODE-001');
    }

    public function test_sgk_info_throws_when_register_code_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/RegisterCode/');
        new SGKInfoRequest(registerName: 'SGK', documentNumber: 'DOC-001', registerCode: '');
    }

    // -------------------------------------------------------------------------
    // InvestmentIncentiveRequest validation
    // -------------------------------------------------------------------------

    public function test_investment_incentive_throws_when_document_number_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/DocumentNumber/');
        new InvestmentIncentiveRequest(documentNumber: '', documentDate: new \DateTimeImmutable());
    }

    // -------------------------------------------------------------------------
    // MedicineAndMedicalDeviceRequest validation
    // -------------------------------------------------------------------------

    public function test_medicine_and_medical_device_throws_when_medicine_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/medicine/');
        new MedicineAndMedicalDeviceRequest(
            productType: ProductType::Medicine,
            medicine:    ['not-a-medicine'],
        );
    }

    public function test_medicine_and_medical_device_throws_when_device_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/medicalDevice/');
        new MedicineAndMedicalDeviceRequest(
            productType:   ProductType::MedicalDevice,
            medicalDevice: ['not-a-device'],
        );
    }

    // -------------------------------------------------------------------------
    // Full payload scenario — ticarifatura + ödeme bilgileri
    // -------------------------------------------------------------------------

    public function test_full_commercial_invoice_payload(): void
    {
        $line1 = InvoiceLineRequest::make('Yazilim Lisansi', 2, UnitType::Piece, 5000.0, 20);
        $line2 = InvoiceLineRequest::make('Destek Hizmeti', 10, UnitType::Hour, 500.0, 20, allowancePercent: 10);

        $request = new SendInvoiceRequest(
            customerInfo:         $this->receiver,
            invoiceLines:         [$line1, $line2],
            issueDate:            new \DateTimeImmutable('2026-05-14T10:00:00'),
            invoiceProfile:       InvoiceProfile::Commercial,
            invoiceType:          InvoiceType::Sales,
            currencyCode:         'TRY',
            customerAlias:        'urn:mail:muhasebe@test.com',
            notes:                ['Odeme 30 gun icinde yapilmalidir.'],
            invoiceSerieOrNumber: 'EFT',
            paymentTermsInfo:     new PaymentTermsRequest(note: '30 gun vadeli'),
            paymentMeansInfo:     new PaymentMeansRequest(code: '42', channelCode: 'TR'),
            additionalDocumentReferences: [
                new AdditionalDocumentReferenceRequest(
                    id:               'PO-2026-001',
                    issueDate:        new \DateTimeImmutable('2026-05-01'),
                    documentType:     'SIPARIS',
                    documentTypeCode: 'ORDER',
                ),
            ],
        );

        $data = $request->toArray();

        $this->assertSame('urn:mail:muhasebe@test.com', $data['CustomerAlias']);
        $this->assertCount(2, $data['EInvoice']['InvoiceLines']);
        $this->assertSame('TICARIFATURA', $data['EInvoice']['InvoiceInfo']['InvoiceProfile']);
        $this->assertArrayHasKey('PaymentTermsInfo', $data['EInvoice']['InvoiceInfo']);
        $this->assertArrayHasKey('PaymentMeansInfo', $data['EInvoice']['InvoiceInfo']);
        $this->assertArrayHasKey('AdditionalDocumentReferences', $data['EInvoice']['InvoiceInfo']);
        $this->assertSame(['Odeme 30 gun icinde yapilmalidir.'], $data['EInvoice']['Notes']);
    }

    // -------------------------------------------------------------------------
    // Full payload scenario — SGK fatura
    // -------------------------------------------------------------------------

    public function test_sgk_invoice_payload(): void
    {
        $request = new SendInvoiceRequest(
            customerInfo:         $this->receiver,
            invoiceLines:         [$this->line],
            issueDate:            new \DateTimeImmutable('2026-05-14T10:00:00'),
            customerAlias:        'urn:mail:test@sirket.com.tr',
            invoiceSerieOrNumber: 'EFT',
            invoiceType:          InvoiceType::SGK,
            accountingCost:       'SAGLIK_MED',
            invoicePeriod:        new InvoicePeriodRequest(
                startDate: new \DateTimeImmutable('2026-04-01'),
                endDate:   new \DateTimeImmutable('2026-04-30'),
            ),
            sgkInfo: new SGKInfoRequest(
                registerName:   'Nilvera Hastane',
                documentNumber: 'HAD-001',
                registerCode:   'HAD',
            ),
        );

        $info = $request->toArray()['EInvoice']['InvoiceInfo'];

        $this->assertSame('SGK', $info['InvoiceType']);
        $this->assertSame('SAGLIK_MED', $info['AccountingCost']);
        $this->assertArrayHasKey('InvoicePeriod', $info);
        $this->assertArrayHasKey('SGKInfo', $info);
    }

    // -------------------------------------------------------------------------
    // Full payload scenario — ihracat fatura
    // -------------------------------------------------------------------------

    public function test_export_invoice_payload(): void
    {
        $exportCustomer = new ExportCustomerInfoRequest(
            taxNumber:             'DE123456789',
            legalRegistrationName: 'German GmbH',
            address:               'Musterstraße 1',
            district:              'Mitte',
            city:                  'Berlin',
            country:               'DE',
        );

        $request = new SendInvoiceRequest(
            customerInfo:                    $this->receiver,
            invoiceLines:                    [$this->line],
            issueDate:                       new \DateTimeImmutable('2026-05-14T10:00:00'),
            customerAlias:                   'urn:mail:test@sirket.com.tr',
            invoiceSerieOrNumber:            'EFT',
            invoiceProfile:                  InvoiceProfile::Export,
            invoiceType:                     InvoiceType::ExciseDuty,
            taxExemptionReasonInfo:          new TaxExemptionReasonInfoRequest(kdvExemptionReasonCode: '301'),
            insuranceValueAmount:            500.0,
            declaredForCarriageValueAmount:  1200.0,
            exportCustomerInfo:              $exportCustomer,
        );

        $data = $request->toArray()['EInvoice'];
        $info = $data['InvoiceInfo'];

        $this->assertSame('IHRACAT', $info['InvoiceProfile']);
        $this->assertSame('IHRACKAYITLI', $info['InvoiceType']);
        $this->assertSame(500.0, $info['InsuranceValueAmount']);
        $this->assertSame(1200.0, $info['DeclaredForCarriageValueAmount']);
        $this->assertArrayHasKey('TaxExemptionReasonInfo', $info);
        $this->assertArrayHasKey('ExportCustomerInfo', $data);
        $this->assertSame('German GmbH', $data['ExportCustomerInfo']['LegalRegistrationName']);
    }
}
