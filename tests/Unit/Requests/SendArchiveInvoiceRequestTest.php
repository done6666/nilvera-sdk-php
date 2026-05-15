<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Requests;

use Nilvera\Enums\ExpenseType;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\SalesPlatform;
use Nilvera\Enums\SendType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\SendArchiveInvoiceRequest;
use Nilvera\Requests\ValueObjects\AdditionalDocumentReferenceRequest;
use Nilvera\Requests\ValueObjects\AdditionalItemIdentificationRequest;
use Nilvera\Requests\ValueObjects\AttachmentRequest;
use Nilvera\Requests\ValueObjects\ESUReportInfoRequest;
use Nilvera\Requests\ValueObjects\ExpensesRequest;
use Nilvera\Requests\ValueObjects\ExportRegisteredInfoRequest;
use Nilvera\Requests\ValueObjects\InternetInfoRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\InvoicePeriodRequest;
use Nilvera\Requests\ValueObjects\OKCInfoRequest;
use Nilvera\Requests\ValueObjects\PaymentMeansRequest;
use Nilvera\Requests\ValueObjects\PaymentTermsRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ReturnInvoiceInfoRequest;
use Nilvera\Requests\ValueObjects\TaxExemptionReasonInfoRequest;
use Nilvera\Requests\ValueObjects\TechSupportRequest;
use PHPUnit\Framework\TestCase;

class SendArchiveInvoiceRequestTest extends TestCase
{
    private ReceiverRequest $receiver;
    private InvoiceLineRequest $line;

    protected function setUp(): void
    {
        $this->receiver = new ReceiverRequest(
            taxNumber: '12345678950',
            name:      'Test Musteri',
            address:   'Test Mah. No:1',
            district:  'Kadikoy',
            city:      'Istanbul',
        );

        $this->line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20);
    }

    private function makeRequest(array $overrides = []): SendArchiveInvoiceRequest
    {
        return new SendArchiveInvoiceRequest(...array_merge([
            'customerInfo'         => $this->receiver,
            'invoiceLines'         => [$this->line],
            'issueDate'            => new \DateTimeImmutable('2026-05-15T10:00:00'),
            'invoiceSerieOrNumber' => 'EAR',
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // Üst düzey yapı
    // -------------------------------------------------------------------------

    public function test_to_array_has_archive_invoice_root_key(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertArrayHasKey('ArchiveInvoice', $data);
        $this->assertArrayHasKey('InvoiceInfo', $data['ArchiveInvoice']);
        $this->assertArrayHasKey('CustomerInfo', $data['ArchiveInvoice']);
        $this->assertArrayHasKey('InvoiceLines', $data['ArchiveInvoice']);
    }

    public function test_to_array_omits_notes_when_empty(): void
    {
        $this->assertArrayNotHasKey('Notes', $this->makeRequest()->toArray()['ArchiveInvoice']);
    }

    public function test_to_array_includes_notes_when_set(): void
    {
        $data = $this->makeRequest(['notes' => ['30 gun vadeli']])->toArray();

        $this->assertSame(['30 gun vadeli'], $data['ArchiveInvoice']['Notes']);
    }

    // -------------------------------------------------------------------------
    // InvoiceInfo varsayılanlar
    // -------------------------------------------------------------------------

    public function test_invoice_info_has_correct_defaults(): void
    {
        $info = $this->makeRequest()->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('SATIS', $info['InvoiceType']);
        $this->assertSame('ELEKTRONIK', $info['SendType']);
        $this->assertSame('NORMAL', $info['SalesPlatform']);
        $this->assertSame('TRY', $info['CurrencyCode']);
        $this->assertSame('EAR', $info['InvoiceSerieOrNumber']);
    }

    public function test_issue_date_is_iso8601(): void
    {
        $info = $this->makeRequest()->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('2026-05-15T10:00:00Z', $info['IssueDate']);
    }

    public function test_uuid_omitted_when_null(): void
    {
        $this->assertArrayNotHasKey('UUID', $this->makeRequest()->toArray()['ArchiveInvoice']['InvoiceInfo']);
    }

    public function test_uuid_included_when_set(): void
    {
        $uuid = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
        $info = $this->makeRequest(['uuid' => $uuid])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame($uuid, $info['UUID']);
    }

    public function test_exchange_rate_included_when_set(): void
    {
        $info = $this->makeRequest([
            'currencyCode' => 'USD',
            'exchangeRate' => 34.5,
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('USD', $info['CurrencyCode']);
        $this->assertSame(34.5, $info['ExchangeRate']);
    }

    public function test_accounting_cost_included_when_set(): void
    {
        $info = $this->makeRequest(['accountingCost' => 'PROJE-001'])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('PROJE-001', $info['AccountingCost']);
    }

    public function test_is_despatch_omitted_when_false(): void
    {
        $this->assertArrayNotHasKey('ISDespatch', $this->makeRequest()->toArray()['ArchiveInvoice']['InvoiceInfo']);
    }

    public function test_is_despatch_included_when_true(): void
    {
        $info = $this->makeRequest(['isDespatch' => true])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertTrue($info['ISDespatch']);
    }

    // -------------------------------------------------------------------------
    // CompanyInfo
    // -------------------------------------------------------------------------

    public function test_company_info_omitted_when_null(): void
    {
        $this->assertArrayNotHasKey('CompanyInfo', $this->makeRequest()->toArray()['ArchiveInvoice']);
    }

    public function test_company_info_included_when_set(): void
    {
        $company = new ReceiverRequest(
            taxNumber: '1234567890',
            name:      'Gonderen Sirket AS',
            address:   'Levent Cad. No:10',
            district:  'Besiktas',
            city:      'Istanbul',
        );

        $data = $this->makeRequest(['companyInfo' => $company])->toArray()['ArchiveInvoice'];

        $this->assertArrayHasKey('CompanyInfo', $data);
        $this->assertSame('Gonderen Sirket AS', $data['CompanyInfo']['Name']);
    }

    // -------------------------------------------------------------------------
    // TaxExemptionReasonInfo
    // -------------------------------------------------------------------------

    public function test_tax_exemption_omitted_when_null(): void
    {
        $this->assertArrayNotHasKey(
            'TaxExemptionReasonInfo',
            $this->makeRequest()->toArray()['ArchiveInvoice']['InvoiceInfo']
        );
    }

    public function test_tax_exemption_included_when_set(): void
    {
        $info = $this->makeRequest([
            'taxExemptionReasonInfo' => new TaxExemptionReasonInfoRequest(
                kdvExemptionReasonCode: '351',
            ),
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('351', $info['TaxExemptionReasonInfo']['KDVExemptionReasonCode']);
    }

    // -------------------------------------------------------------------------
    // PaymentTermsInfo
    // -------------------------------------------------------------------------

    public function test_payment_terms_included_when_set(): void
    {
        $info = $this->makeRequest([
            'paymentTermsInfo' => new PaymentTermsRequest(percent: 2.5, note: 'Gecikme faizi'),
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame(2.5, $info['PaymentTermsInfo']['Percent']);
        $this->assertSame('Gecikme faizi', $info['PaymentTermsInfo']['Note']);
    }

    // -------------------------------------------------------------------------
    // PaymentMeansInfo
    // -------------------------------------------------------------------------

    public function test_payment_means_included_when_set(): void
    {
        $info = $this->makeRequest([
            'paymentMeansInfo' => new PaymentMeansRequest(
                code:                    '42',
                payeeFinancialAccountId: 'TR330006100519786457841326',
            ),
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('42', $info['PaymentMeansInfo']['Code']);
        $this->assertSame('TR330006100519786457841326', $info['PaymentMeansInfo']['PayeeFinancialAccountID']);
    }

    // -------------------------------------------------------------------------
    // OKCInfo
    // -------------------------------------------------------------------------

    public function test_okc_info_included_when_set(): void
    {
        $info = $this->makeRequest([
            'okcInfo' => new OKCInfoRequest(id: 'FISK-001', zNo: 'Z0042'),
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('FISK-001', $info['OKCInfo']['ID']);
        $this->assertSame('Z0042', $info['OKCInfo']['ZNo']);
    }

    // -------------------------------------------------------------------------
    // ESUReportInfo
    // -------------------------------------------------------------------------

    public function test_esu_report_info_included_when_set(): void
    {
        $date = new \DateTimeImmutable('2026-05-15T00:00:00');
        $info = $this->makeRequest([
            'esuReportInfo' => new ESUReportInfoRequest(id: 'ESU-2026-001', issueDate: $date),
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('ESU-2026-001', $info['ESUReportInfo']['ID']);
    }

    // -------------------------------------------------------------------------
    // InvoicePeriod
    // -------------------------------------------------------------------------

    public function test_invoice_period_included_when_set(): void
    {
        $info = $this->makeRequest([
            'invoicePeriod' => new InvoicePeriodRequest(
                startDate:   new \DateTimeImmutable('2026-05-01T00:00:00'),
                endDate:     new \DateTimeImmutable('2026-05-31T00:00:00'),
                description: 'Mayis 2026 abonelik donemi',
            ),
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertArrayHasKey('InvoicePeriod', $info);
        $this->assertSame('Mayis 2026 abonelik donemi', $info['InvoicePeriod']['Description']);
    }

    // -------------------------------------------------------------------------
    // OrderReferenceDocument & AdditionalDocumentReferences
    // -------------------------------------------------------------------------

    public function test_order_reference_document_included_when_set(): void
    {
        $info = $this->makeRequest([
            'orderReferenceDocument' => new AdditionalDocumentReferenceRequest(
                id:           'SIP-2026-001',
                documentType: 'OrderDocument',
            ),
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('SIP-2026-001', $info['OrderReferenceDocument']['ID']);
    }

    public function test_additional_document_references_included_when_set(): void
    {
        $attachment = new AttachmentRequest(
            base64Data: base64_encode('dummy-pdf'),
            mimeCode:   'application/pdf',
            fileName:   'fatura-eki.pdf',
        );

        $info = $this->makeRequest([
            'additionalDocumentReferences' => [
                new AdditionalDocumentReferenceRequest(
                    id:         'EK-001',
                    attachment: $attachment,
                ),
            ],
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertCount(1, $info['AdditionalDocumentReferences']);
        $this->assertSame('EK-001', $info['AdditionalDocumentReferences'][0]['ID']);
        $this->assertSame('application/pdf', $info['AdditionalDocumentReferences'][0]['Attachment']['MimeCode']);
    }

    // -------------------------------------------------------------------------
    // ReturnInvoiceInfo
    // -------------------------------------------------------------------------

    public function test_return_invoice_info_included_when_set(): void
    {
        $info = $this->makeRequest([
            'invoiceType'      => InvoiceType::Return,
            'returnInvoiceInfo' => [
                new ReturnInvoiceInfoRequest(
                    invoiceNumber: 'EAR2026000000001',
                    issueDate:     new \DateTimeImmutable('2026-04-01'),
                ),
            ],
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertCount(1, $info['ReturnInvoiceInfo']);
        $this->assertSame('EAR2026000000001', $info['ReturnInvoiceInfo'][0]['InvoiceNumber']);
    }

    // -------------------------------------------------------------------------
    // Expenses
    // -------------------------------------------------------------------------

    public function test_expenses_included_when_set(): void
    {
        $info = $this->makeRequest([
            'invoiceType' => InvoiceType::HKSSales,
            'expenses'    => [
                new ExpensesRequest(ExpenseType::HKSCommission, percent: 3.0, amount: 150.0),
            ],
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertCount(1, $info['Expenses']);
        $this->assertSame('HKSKOMISYON', $info['Expenses'][0]['ExpenseType']);
        $this->assertSame(3.0, $info['Expenses'][0]['Percent']);
    }

    // -------------------------------------------------------------------------
    // InternetInfo & SalesPlatform
    // -------------------------------------------------------------------------

    public function test_internet_info_included_when_set(): void
    {
        $info = $this->makeRequest([
            'salesPlatform' => SalesPlatform::Internet,
            'sendType'      => SendType::Electronic,
            'internetInfo'  => new InternetInfoRequest(
                webSite:       'https://ornek.com',
                paymentMethod: 'EFT/HAVALE',
                paymentDate:   new \DateTimeImmutable('2026-05-15T10:00:00'),
            ),
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertSame('INTERNET', $info['SalesPlatform']);
        $this->assertSame('https://ornek.com', $info['InternetInfo']['WebSite']);
        $this->assertSame('EFT/HAVALE', $info['InternetInfo']['PaymentMethod']);
    }

    // -------------------------------------------------------------------------
    // InvoiceLines — archive-specific fields
    // -------------------------------------------------------------------------

    public function test_invoice_line_with_tech_support(): void
    {
        $line = new InvoiceLineRequest(
            name:         'iPhone 15 Pro',
            quantity:     1,
            unitType:     UnitType::Piece,
            price:        60000.0,
            allowanceTotal: 0.0,
            kdvPercent:   20,
            kdvTotal:     12000.0,
            techSupport:  new TechSupportRequest(imeiNumbers: ['123456789012345']),
        );

        $lineData = $this->makeRequest(['invoiceLines' => [$line]])->toArray()['ArchiveInvoice']['InvoiceLines'][0];

        $this->assertArrayHasKey('TechSupport', $lineData);
        $this->assertSame(['123456789012345'], $lineData['TechSupport']['IMEINumbers']);
    }

    public function test_invoice_line_with_export_registered_info(): void
    {
        $line = new InvoiceLineRequest(
            name:                 'Ihracat Urunu',
            quantity:             10,
            unitType:             UnitType::Piece,
            price:                500.0,
            allowanceTotal:       0.0,
            kdvPercent:           0,
            kdvTotal:             0.0,
            exportRegisteredInfo: new ExportRegisteredInfoRequest(
                diibLineCode: 'DIIB-001',
                gtipNo:       '8471.30.00.00',
            ),
        );

        $lineData = $this->makeRequest(['invoiceLines' => [$line]])->toArray()['ArchiveInvoice']['InvoiceLines'][0];

        $this->assertSame('DIIB-001', $lineData['ExportRegisteredInfo']['DIIBLineCode']);
        $this->assertSame('8471.30.00.00', $lineData['ExportRegisteredInfo']['GTIPNo']);
    }

    public function test_invoice_line_with_additional_item_identification(): void
    {
        $line = new InvoiceLineRequest(
            name:                         'Etiketli Urun',
            quantity:                     1,
            unitType:                     UnitType::Piece,
            price:                        200.0,
            allowanceTotal:               0.0,
            kdvPercent:                   20,
            kdvTotal:                     40.0,
            additionalItemIdentification: new AdditionalItemIdentificationRequest(
                tagNumber:      'TAG-001',
                ownerName:      'Mal Sahibi Ltd.',
                ownerTaxNumber: '1234567890',
            ),
        );

        $lineData = $this->makeRequest(['invoiceLines' => [$line]])->toArray()['ArchiveInvoice']['InvoiceLines'][0];

        $this->assertSame('TAG-001', $lineData['AdditionalItemIdentification']['TagNumber']);
        $this->assertSame('Mal Sahibi Ltd.', $lineData['AdditionalItemIdentification']['OwnerName']);
    }

    // -------------------------------------------------------------------------
    // Doğrulama hataları
    // -------------------------------------------------------------------------

    public function test_throws_when_invoice_lines_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/InvoiceLines/');
        $this->makeRequest(['invoiceLines' => []]);
    }

    public function test_throws_when_exchange_rate_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ExchangeRate/');
        $this->makeRequest(['exchangeRate' => 0.0]);
    }

    public function test_throws_when_internet_platform_with_paper_send_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Internet/');
        $this->makeRequest([
            'salesPlatform' => SalesPlatform::Internet,
            'sendType'      => SendType::Paper,
        ]);
    }

    public function test_throws_when_invalid_payment_method_in_internet_info(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new InternetInfoRequest(paymentMethod: 'GECERSIZ_METOD');
    }

    public function test_throws_when_return_invoice_info_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeRequest(['returnInvoiceInfo' => ['not-a-return-invoice']]);
    }

    public function test_throws_when_expenses_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeRequest(['expenses' => ['not-an-expense']]);
    }

    public function test_throws_when_additional_doc_references_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeRequest(['additionalDocumentReferences' => ['not-a-doc-ref']]);
    }

    // -------------------------------------------------------------------------
    // DespatchDocumentReference
    // -------------------------------------------------------------------------

    public function test_despatch_document_reference_included_when_set(): void
    {
        $info = $this->makeRequest([
            'despatchDocumentReference' => [
                ['IssueDate' => '2026-05-14', 'Value' => 'EIR2026000000001'],
            ],
        ])->toArray()['ArchiveInvoice']['InvoiceInfo'];

        $this->assertCount(1, $info['DespatchDocumentReference']);
        $this->assertSame('EIR2026000000001', $info['DespatchDocumentReference'][0]['Value']);
    }
}
