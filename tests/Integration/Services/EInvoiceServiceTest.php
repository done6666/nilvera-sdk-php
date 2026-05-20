<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\CreateSeriesRequest;
use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\ListSeriesRequest;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\UpdateSeriesRequest;
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

    public function test_list_series_returns_paginated_result(): void
    {
        $result = $this->client->eInvoice()->listSeries();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Content', $result);
        $this->assertArrayHasKey('Page', $result);
        $this->assertArrayHasKey('TotalCount', $result);
    }

    public function test_list_series_with_active_filter(): void
    {
        $result = $this->client->eInvoice()->listSeries(
            new ListSeriesRequest(isActive: true, pageSize: 5)
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Content', $result);

        foreach ($result['Content'] ?? [] as $serie) {
            $this->assertTrue($serie['IsActive'], 'IsActive filtresi çalışmıyor.');
        }
    }

    public function test_create_and_get_series(): void
    {
        $uniqueName = strtoupper(substr(md5((string) microtime(true)), 0, 3));

        $created = $this->client->eInvoice()->createSeries(
            new CreateSeriesRequest(name: $uniqueName, isActive: true, isDefault: false)
        );

        $this->assertArrayHasKey('ID', $created);
        $this->assertSame($uniqueName, $created['Name']);
        $this->assertTrue($created['IsActive']);
        $this->assertFalse($created['IsDefault']);

        $detail = $this->client->eInvoice()->getSeries($created['ID']);

        $this->assertSame($created['ID'], $detail['ID']);
        $this->assertSame($uniqueName, $detail['Name']);
        $this->assertArrayHasKey('Details', $detail);
    }

    public function test_update_series_active_status(): void
    {
        $seriesList = $this->client->eInvoice()->listSeries();
        $series     = $seriesList['Content'] ?? [];

        if (empty($series)) {
            $this->markTestSkipped('Test hesabında e-Fatura serisi bulunamadı.');
        }

        $target         = $series[0];
        $newActiveState = !$target['IsActive'];

        $result = $this->client->eInvoice()->updateSeries(
            new UpdateSeriesRequest(
                id:        $target['ID'],
                isDefault: $target['IsDefault'],
                isActive:  $newActiveState,
            )
        );

        $this->assertTrue($result);

        $detail = $this->client->eInvoice()->getSeries($target['ID']);
        $this->assertSame($newActiveState, $detail['IsActive']);

        // Orijinal duruma geri al
        $this->client->eInvoice()->updateSeries(
            new UpdateSeriesRequest(
                id:        $target['ID'],
                isDefault: $target['IsDefault'],
                isActive:  $target['IsActive'],
            )
        );
    }

    public function test_get_series_detail_has_expected_keys(): void
    {
        $seriesList = $this->client->eInvoice()->listSeries();

        if (empty($seriesList['Content'])) {
            $this->markTestSkipped('Test hesabında e-Fatura serisi bulunamadı.');
        }

        $id     = $seriesList['Content'][0]['ID'];
        $detail = $this->client->eInvoice()->getSeries($id);

        $this->assertArrayHasKey('ID', $detail);
        $this->assertArrayHasKey('Name', $detail);
        $this->assertArrayHasKey('IsActive', $detail);
        $this->assertArrayHasKey('IsDefault', $detail);
        $this->assertArrayHasKey('Details', $detail);
    }

    public function test_list_tags_returns_array(): void
    {
        $result = $this->client->eInvoice()->listTags();

        $this->assertIsArray($result);
    }

    public function test_preview_returns_html_string(): void
    {
        $series = $this->client->eInvoice()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında aktif seri bulunamadı.');
        }

        $seriesName = $series['Content'][0]['Name'];

        $request = new SendInvoiceRequest(
            customerInfo:         new ReceiverRequest(
                taxNumber: '6310540565',
                name:      'Nilvera E-Fatura Test Alicisi',
                address:   'Test Mah. No:1',
                district:  'Kadikoy',
                city:      'Istanbul',
                taxOffice: 'Kadikoy',
            ),
            invoiceLines:         [
                InvoiceLineRequest::make('SDK Onizleme Test', 1, UnitType::Piece, 100.0, 20),
            ],
            issueDate:            new \DateTimeImmutable(),
            customerAlias:        'urn:mail:defaultpk@nilvera.com',
            invoiceSerieOrNumber: $seriesName,
        );

        $html = $this->client->eInvoice()->preview($request);

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsStringIgnoringCase('<!DOCTYPE html', $html);
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
