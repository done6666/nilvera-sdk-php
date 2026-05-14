<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class EInvoiceServiceTest extends IntegrationTestCase
{
    public function test_list_sale_invoices_returns_paginated_array(): void
    {
        $result = $this->client->eInvoice()->listSaleInvoices(
            new ListInvoicesRequest(page: 1, pageSize: 5)
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Page', $result);
        $this->assertArrayHasKey('Content', $result);
        $this->assertArrayHasKey('TotalCount', $result);
    }

    public function test_list_purchase_invoices_returns_paginated_array(): void
    {
        $result = $this->client->eInvoice()->listPurchaseInvoices(
            new ListInvoicesRequest(page: 1, pageSize: 5)
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Page', $result);
    }

    public function test_list_series_returns_array(): void
    {
        $result = $this->client->eInvoice()->listSeries();

        $this->assertIsArray($result);
    }

    public function test_list_tags_returns_array(): void
    {
        $result = $this->client->eInvoice()->listTags();

        $this->assertIsArray($result);
    }

    public function test_send_invoice_returns_uuid(): void
    {
        $series = $this->client->eInvoice()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında aktif seri bulunamadı.');
        }

        $seriesName = $series['Content'][0]['Name'];

        $request = new SendInvoiceRequest(
            customerInfo: new ReceiverRequest(
                taxNumber: '6310540565',
                name:      'Nilvera E-Fatura Test Alicisi',
                address:   'Test Mah. No:1',
                district:  'Kadikoy',
                city:      'Istanbul',
                taxOffice: 'Kadikoy',
            ),
            invoiceLines: [
                InvoiceLineRequest::make('SDK Integration Test', 1, UnitType::Piece, 100.0, 20),
            ],
            issueDate:            new \DateTimeImmutable(),
            invoiceProfile:       InvoiceProfile::Basic,
            invoiceType:          InvoiceType::Sales,
            currencyCode:         'TRY',
            customerAlias:        'urn:mail:defaultpk@nilvera.com',
            invoiceSerieOrNumber: $seriesName,
        );

        $response = $this->client->eInvoice()->send($request);

        $this->assertNotEmpty($response->uuid);
        $this->assertNotEmpty($response->invoiceNumber);
    }

    public function test_get_sale_invoice_envelope_info_for_existing_invoice(): void
    {
        $list = $this->client->eInvoice()->listSaleInvoices(
            new ListInvoicesRequest(page: 1, pageSize: 1)
        );

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut satış faturası bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eInvoice()->getSaleInvoiceEnvelopeInfo($uuid);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('GIBCode', $result);
    }

    public function test_get_sale_invoice_status_for_existing_invoice(): void
    {
        $list = $this->client->eInvoice()->listSaleInvoices(
            new ListInvoicesRequest(page: 1, pageSize: 1)
        );

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut satış faturası bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eInvoice()->getSaleInvoiceStatus($uuid);

        $this->assertIsArray($result);
    }
}
