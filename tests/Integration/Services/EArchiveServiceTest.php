<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\SalesPlatform;
use Nilvera\Enums\SendType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\SendArchiveInvoiceRequest;
use Nilvera\Requests\ValueObjects\AdditionalDocumentReferenceRequest;
use Nilvera\Requests\ValueObjects\InternetInfoRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\InvoicePeriodRequest;
use Nilvera\Requests\ValueObjects\OKCInfoRequest;
use Nilvera\Requests\ValueObjects\PaymentMeansRequest;
use Nilvera\Requests\ValueObjects\PaymentTermsRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\TaxExemptionReasonInfoRequest;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class EArchiveServiceTest extends IntegrationTestCase
{
    private ReceiverRequest $customer;
    private InvoiceLineRequest $line;
    private string $series;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = new ReceiverRequest(
            taxNumber: '12345678950',
            name:      'SDK Entegrasyon Test Musterisi',
            address:   'Bagcilar Cad. No:5',
            district:  'Bagcilar',
            city:      'Istanbul',
        );

        $this->line = InvoiceLineRequest::make(
            name:       'SDK Entegrasyon Test Urunu',
            quantity:   1,
            unitType:   UnitType::Piece,
            price:      100.0,
            kdvPercent: 20,
        );

        $seriesList = $this->client->eArchive()->listSeries();
        $activeSeries = array_values(
            array_filter($seriesList['Content'] ?? [], static fn (array $s) => $s['IsActive'] === true)
        );

        if (empty($activeSeries)) {
            $this->markTestSkipped('Test hesabında aktif e-Arşiv serisi bulunamadı.');
        }

        $this->series = $activeSeries[0]['Name'];
    }

    // -------------------------------------------------------------------------
    // Listeleme
    // -------------------------------------------------------------------------

    public function test_list_invoices_returns_paginated_result(): void
    {
        $result = $this->client->eArchive()->listInvoices(
            new ListInvoicesRequest(page: 1, pageSize: 5)
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Page', $result);
        $this->assertArrayHasKey('Content', $result);
    }

    public function test_list_series_returns_array(): void
    {
        $result = $this->client->eArchive()->listSeries();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Content', $result);
    }

    public function test_list_templates_returns_array(): void
    {
        $result = $this->client->eArchive()->listTemplates();

        $this->assertIsArray($result);
    }

    public function test_list_tags_returns_array(): void
    {
        $result = $this->client->eArchive()->listTags();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Temel gönderme
    // -------------------------------------------------------------------------

    public function test_send_basic_invoice_returns_uuid_and_number(): void
    {
        $request = new SendArchiveInvoiceRequest(
            customerInfo:         $this->customer,
            invoiceLines:         [$this->line],
            issueDate:            new \DateTimeImmutable(),
            invoiceSerieOrNumber: $this->series,
        );

        $response = $this->client->eArchive()->send($request);

        $this->assertNotEmpty($response->uuid);
        $this->assertNotEmpty($response->invoiceNumber);
    }

    // -------------------------------------------------------------------------
    // Ek ödeme bilgileri ile gönderme
    // -------------------------------------------------------------------------

    public function test_send_invoice_with_payment_info(): void
    {
        $request = new SendArchiveInvoiceRequest(
            customerInfo:         $this->customer,
            invoiceLines:         [$this->line],
            issueDate:            new \DateTimeImmutable(),
            invoiceSerieOrNumber: $this->series,
            notes:                ['30 gun odeme vadeli'],
            paymentTermsInfo:     new PaymentTermsRequest(percent: 2.0, note: 'Gecikme faizi uygulanir'),
            paymentMeansInfo:     new PaymentMeansRequest(
                code:                    '42',
                payeeFinancialAccountId: 'TR330006100519786457841326',
            ),
        );

        $response = $this->client->eArchive()->send($request);

        $this->assertNotEmpty($response->uuid);
    }

    // -------------------------------------------------------------------------
    // Dönemsel fatura (InvoicePeriod)
    // -------------------------------------------------------------------------

    public function test_send_invoice_with_period(): void
    {
        $request = new SendArchiveInvoiceRequest(
            customerInfo:         $this->customer,
            invoiceLines:         [$this->line],
            issueDate:            new \DateTimeImmutable(),
            invoiceSerieOrNumber: $this->series,
            invoicePeriod:        new InvoicePeriodRequest(
                startDate:   new \DateTimeImmutable('first day of this month'),
                endDate:     new \DateTimeImmutable('last day of this month'),
                description: 'Aylik abonelik donemi',
            ),
        );

        $response = $this->client->eArchive()->send($request);

        $this->assertNotEmpty($response->uuid);
    }

    // -------------------------------------------------------------------------
    // Ek belge referansı ile gönderme
    // -------------------------------------------------------------------------

    public function test_send_invoice_with_additional_document_reference(): void
    {
        $request = new SendArchiveInvoiceRequest(
            customerInfo:                 $this->customer,
            invoiceLines:                 [$this->line],
            issueDate:                    new \DateTimeImmutable(),
            invoiceSerieOrNumber:         $this->series,
            additionalDocumentReferences: [
                new AdditionalDocumentReferenceRequest(
                    id:           'SIP-2026-001',
                    issueDate:    new \DateTimeImmutable(),
                    documentType: 'OrderDocument',
                ),
            ],
        );

        $response = $this->client->eArchive()->send($request);

        $this->assertNotEmpty($response->uuid);
    }

    // -------------------------------------------------------------------------
    // İnternet satışı
    // -------------------------------------------------------------------------

    public function test_send_internet_sale_invoice(): void
    {
        $request = new SendArchiveInvoiceRequest(
            customerInfo:         $this->customer,
            invoiceLines:         [$this->line],
            issueDate:            new \DateTimeImmutable(),
            invoiceSerieOrNumber: $this->series,
            salesPlatform:        SalesPlatform::Internet,
            sendType:             SendType::Electronic,
            internetInfo:         new InternetInfoRequest(
                webSite:       'https://ornek-magaza.com',
                paymentMethod: 'EFT/HAVALE',
                paymentDate:   new \DateTimeImmutable(),
            ),
        );

        $response = $this->client->eArchive()->send($request);

        $this->assertNotEmpty($response->uuid);
    }

    // -------------------------------------------------------------------------
    // İstisna faturası (KDV muafiyeti)
    // -------------------------------------------------------------------------

    public function test_send_exemption_invoice_with_tax_exemption_reason(): void
    {
        $exemptLine = InvoiceLineRequest::make(
            name:       'KDV Muaf Hizmet',
            quantity:   1,
            unitType:   UnitType::Piece,
            price:      500.0,
            kdvPercent: 0,
        );

        $request = new SendArchiveInvoiceRequest(
            customerInfo:           $this->customer,
            invoiceLines:           [$exemptLine],
            issueDate:              new \DateTimeImmutable(),
            invoiceSerieOrNumber:   $this->series,
            invoiceType:            InvoiceType::Exemption,
            taxExemptionReasonInfo: new TaxExemptionReasonInfoRequest(
                kdvExemptionReasonCode: '201',
            ),
        );

        $response = $this->client->eArchive()->send($request);

        $this->assertNotEmpty($response->uuid);
    }

    // -------------------------------------------------------------------------
    // ÖKC bilgisi ile gönderme
    // -------------------------------------------------------------------------

    public function test_send_invoice_with_okc_info(): void
    {
        $request = new SendArchiveInvoiceRequest(
            customerInfo:         $this->customer,
            invoiceLines:         [$this->line],
            issueDate:            new \DateTimeImmutable(),
            invoiceSerieOrNumber: $this->series,
            okcInfo:              new OKCInfoRequest(
                id:                  'FISK-2026-001',
                issueDate:           new \DateTimeImmutable(),
                time:                '10:30:00',
                zNo:                 'Z0042',
                endPointId:          'OKC-SN-12345',
                documentDescription: 'E-ARSIV',
            ),
        );

        $response = $this->client->eArchive()->send($request);

        $this->assertNotEmpty($response->uuid);
    }

    // -------------------------------------------------------------------------
    // Taslak işlemleri
    // -------------------------------------------------------------------------

    public function test_list_drafts_returns_array(): void
    {
        $result = $this->client->eArchive()->listDrafts();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // HTML/PDF/XML çıktıları
    // -------------------------------------------------------------------------

    public function test_get_invoice_html_for_existing_invoice(): void
    {
        $list = $this->client->eArchive()->listInvoices(new ListInvoicesRequest(page: 1, pageSize: 1));

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut e-Arşiv faturası bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eArchive()->getInvoiceHtml($uuid);

        $this->assertNotEmpty($result);
        $this->assertStringContainsStringIgnoringCase('<html', $result);
    }

    public function test_get_invoice_xml_for_existing_invoice(): void
    {
        $list = $this->client->eArchive()->listInvoices(new ListInvoicesRequest(page: 1, pageSize: 1));

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut e-Arşiv faturası bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eArchive()->getInvoiceXml($uuid);

        $this->assertNotEmpty($result);
        $this->assertStringContainsStringIgnoringCase('Invoice', $result);
    }

    // -------------------------------------------------------------------------
    // İstatistikler
    // -------------------------------------------------------------------------

    public function test_get_statistics_returns_array(): void
    {
        $result = $this->client->eArchive()->getStatistics();

        $this->assertIsArray($result);
    }

    public function test_get_last_statistics_returns_array(): void
    {
        $result = $this->client->eArchive()->getLastStatistics();

        $this->assertIsArray($result);
    }
}
