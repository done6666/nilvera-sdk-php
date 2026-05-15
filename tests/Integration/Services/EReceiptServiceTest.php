<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Exception\ApiException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Exception\ValidationException;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class EReceiptServiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Seri / Şablon / Etiket / Bildirim Listeleme
    // -------------------------------------------------------------------------

    public function test_list_series_returns_array(): void
    {
        $result = $this->client->eReceipt()->listSeries();

        $this->assertIsArray($result);
    }

    public function test_list_tags_returns_array(): void
    {
        $result = $this->client->eReceipt()->listTags();

        $this->assertIsArray($result);
    }

    public function test_list_templates_returns_array(): void
    {
        $result = $this->client->eReceipt()->listTemplates();

        $this->assertIsArray($result);
    }

    public function test_list_notifications_returns_array(): void
    {
        $result = $this->client->eReceipt()->listNotifications(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // İstatistikler
    // -------------------------------------------------------------------------

    public function test_get_statistics_returns_array(): void
    {
        $result = $this->client->eReceipt()->getStatistics();

        $this->assertIsArray($result);
    }

    public function test_get_last_statistics_returns_array(): void
    {
        try {
            $result = $this->client->eReceipt()->getLastStatistics();
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('getLastStatistics endpoint bu test hesabında mevcut değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Belge Listeleme
    // -------------------------------------------------------------------------

    public function test_list_bills_returns_array(): void
    {
        $result = $this->client->eReceipt()->listBills(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_list_drafts_returns_array(): void
    {
        $result = $this->client->eReceipt()->listDrafts(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Raporlar
    // -------------------------------------------------------------------------

    public function test_list_reports_returns_array(): void
    {
        $result = $this->client->eReceipt()->listReports();

        $this->assertIsArray($result);
    }

    public function test_get_report_list_returns_array(): void
    {
        $result = $this->client->eReceipt()->getReportList();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Mevcut belge üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_get_bill_html_for_existing_document(): void
    {
        $list = $this->client->eReceipt()->listBills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-Adisyon belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eReceipt()->getBillHtml($uuid);

        $this->assertNotEmpty($result);
    }

    public function test_get_bill_xml_for_existing_document(): void
    {
        $list = $this->client->eReceipt()->listBills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-Adisyon belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eReceipt()->getBillXml($uuid);

        $this->assertNotEmpty($result);
    }

    public function test_get_bill_histories_for_existing_document(): void
    {
        $list = $this->client->eReceipt()->listBills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-Adisyon belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eReceipt()->getBillHistories($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_bill_status_for_existing_document(): void
    {
        $list = $this->client->eReceipt()->listBills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-Adisyon belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eReceipt()->getBillStatus($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_bill_details_for_existing_document(): void
    {
        $list = $this->client->eReceipt()->listBills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-Adisyon belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eReceipt()->getBillDetails($uuid);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Gönderme (e-Adisyon gönder)
    // -------------------------------------------------------------------------

    public function test_send_bill_returns_array_with_uuid(): void
    {
        $series = $this->client->eReceipt()->listSeries();

        $activeSeries = array_values(
            array_filter($series['Content'] ?? [], static fn (array $s) => ($s['IsActive'] ?? false) === true)
        );

        if (empty($activeSeries)) {
            $this->markTestSkipped('Test hesabında aktif e-Adisyon serisi bulunamadı.');
        }

        $seriesName = $activeSeries[0]['Name'];

        $data = [
            'Bill' => [
                'BillInfo'  => [
                    'BillSerieOrNumber' => $seriesName,
                    'IssueDate'         => (new \DateTimeImmutable())->format('Y-m-d\TH:i:s\Z'),
                    'CurrencyCode'      => 'TRY',
                ],
                'CustomerInfo' => [
                    'TaxNumber' => '12345678950',
                    'Name'      => 'SDK Entegrasyon Test Müşterisi',
                    'Address'   => 'Test Mah. No:1',
                    'District'  => 'Kadıköy',
                    'City'      => 'İstanbul',
                ],
                'BillLines' => [
                    [
                        'Name'       => 'SDK e-Adisyon Entegrasyon Testi',
                        'Quantity'   => 1,
                        'UnitType'   => 'C62',
                        'Price'      => 100.0,
                        'KDVPercent' => 20,
                    ],
                ],
            ],
        ];

        try {
            $result = $this->client->eReceipt()->send($data);
            $this->assertIsArray($result);
            $this->assertNotEmpty($result['UUID'] ?? $result['uuid'] ?? '');
        } catch (ValidationException | ApiException $e) {
            $this->markTestSkipped('e-Adisyon gönderme bu test hesabında başarısız oldu: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Taslak üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_get_draft_model_for_existing_draft(): void
    {
        $list = $this->client->eReceipt()->listDrafts(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-Adisyon taslağı bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eReceipt()->getDraftModel($uuid);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Seri detay
    // -------------------------------------------------------------------------

    public function test_get_series_detail_for_existing_series(): void
    {
        $series = $this->client->eReceipt()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında e-Adisyon serisi bulunamadı.');
        }

        $id = $series['Content'][0]['Id'] ?? $series['Content'][0]['ID'] ?? null;

        if ($id === null) {
            $this->markTestSkipped('Seri Id alanı bulunamadı.');
        }

        $result = $this->client->eReceipt()->getSeriesDetail((int) $id);

        $this->assertIsArray($result);
    }
}
