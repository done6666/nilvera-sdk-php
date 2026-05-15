<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Exception\NotFoundException;
use Nilvera\Requests\SendVoucherRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\VoucherLineRequest;
use Nilvera\Responses\SendDocumentResponse;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class ESelfEmployedServiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Seri / Şablon / Etiket / Bildirim Listeleme
    // -------------------------------------------------------------------------

    public function test_list_series_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->listSeries();

        $this->assertIsArray($result);
    }

    public function test_list_tags_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->listTags();

        $this->assertIsArray($result);
    }

    public function test_list_templates_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->listTemplates();

        $this->assertIsArray($result);
    }

    public function test_list_notifications_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->listNotifications(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // İstatistikler
    // -------------------------------------------------------------------------

    public function test_get_statistics_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->getStatistics();

        $this->assertIsArray($result);
    }

    public function test_get_last_statistics_returns_array(): void
    {
        try {
            $result = $this->client->eSelfEmployed()->getLastStatistics();
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('getLastStatistics endpoint bu test hesabında mevcut değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Belge Listeleme
    // -------------------------------------------------------------------------

    public function test_list_vouchers_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->listVouchers(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_list_drafts_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->listDrafts(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Raporlar
    // -------------------------------------------------------------------------

    public function test_list_reports_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->listReports();

        $this->assertIsArray($result);
    }

    public function test_get_report_list_returns_array(): void
    {
        $result = $this->client->eSelfEmployed()->getReportList();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Mevcut belge üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_get_voucher_html_for_existing_document(): void
    {
        $list = $this->client->eSelfEmployed()->listVouchers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SMM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eSelfEmployed()->getVoucherHtml($uuid);

        $this->assertNotEmpty($result);
    }

    public function test_get_voucher_xml_for_existing_document(): void
    {
        $list = $this->client->eSelfEmployed()->listVouchers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SMM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eSelfEmployed()->getVoucherXml($uuid);

        $this->assertNotEmpty($result);
    }

    public function test_get_voucher_histories_for_existing_document(): void
    {
        $list = $this->client->eSelfEmployed()->listVouchers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SMM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eSelfEmployed()->getVoucherHistories($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_voucher_status_for_existing_document(): void
    {
        $list = $this->client->eSelfEmployed()->listVouchers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SMM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eSelfEmployed()->getVoucherStatus($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_voucher_details_for_existing_document(): void
    {
        $list = $this->client->eSelfEmployed()->listVouchers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SMM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eSelfEmployed()->getVoucherDetails($uuid);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Gönderme (e-SMM gönder)
    // -------------------------------------------------------------------------

    public function test_send_voucher_returns_uuid(): void
    {
        $series = $this->client->eSelfEmployed()->listSeries();

        $activeSeries = array_values(
            array_filter($series['Content'] ?? [], static fn (array $s) => ($s['IsActive'] ?? false) === true)
        );

        if (empty($activeSeries)) {
            $this->markTestSkipped('Test hesabında aktif e-SMM serisi bulunamadı.');
        }

        $seriesName = $activeSeries[0]['Name'];

        $request = new SendVoucherRequest(
            customerInfo: new ReceiverRequest(
                taxNumber: '12345678950',
                name:      'SDK Entegrasyon Test Müşterisi',
                address:   'Test Mah. No:1',
                district:  'Kadıköy',
                city:      'İstanbul',
                taxOffice: 'Kadıköy',
            ),
            voucherLines: [
                new VoucherLineRequest(
                    name:      'SDK e-SMM Entegrasyon Test Hizmeti',
                    grossWage: 1000.0,
                    price:     1000.0,
                    kdvPercent: 20.0,
                    kdvTotal:   200.0,
                ),
            ],
            issueDate:            new \DateTimeImmutable(),
            sendType:             'ELEKTRONIK',
            voucherSerieOrNumber: $seriesName,
        );

        $response = $this->client->eSelfEmployed()->send($request);

        $this->assertInstanceOf(SendDocumentResponse::class, $response);
        $this->assertNotEmpty($response->uuid);
    }

    // -------------------------------------------------------------------------
    // Taslak üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_get_draft_model_for_existing_draft(): void
    {
        $list = $this->client->eSelfEmployed()->listDrafts(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SMM taslağı bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eSelfEmployed()->getDraftModel($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_draft_html_for_existing_draft(): void
    {
        $list = $this->client->eSelfEmployed()->listDrafts(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SMM taslağı bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eSelfEmployed()->getDraftHtml($uuid);

        $this->assertNotEmpty($result);
    }

    // -------------------------------------------------------------------------
    // Seri detay
    // -------------------------------------------------------------------------

    public function test_get_series_detail_for_existing_series(): void
    {
        $series = $this->client->eSelfEmployed()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında e-SMM serisi bulunamadı.');
        }

        $id = $series['Content'][0]['Id'] ?? $series['Content'][0]['ID'] ?? null;

        if ($id === null) {
            $this->markTestSkipped('Seri Id alanı bulunamadı.');
        }

        $result = $this->client->eSelfEmployed()->getSeriesDetail((int) $id);

        $this->assertIsArray($result);
    }
}
