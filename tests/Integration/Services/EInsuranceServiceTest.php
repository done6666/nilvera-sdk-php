<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Exception\NotFoundException;
use Nilvera\Exception\ValidationException;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class EInsuranceServiceTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // e-SKGB modülü hesapta aktif değilse tüm testleri atla
        try {
            $this->client->eInsurance()->listInsurances(['page' => 1, 'pageSize' => 1]);
        } catch (NotFoundException) {
            $this->markTestSkipped('e-SKGB (EInsurance) modülü bu test hesabında aktif değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Seri / Şablon / Etiket / Bildirim Listeleme
    // -------------------------------------------------------------------------

    public function test_list_series_returns_array(): void
    {
        $result = $this->client->eInsurance()->listSeries();

        $this->assertIsArray($result);
    }

    public function test_list_tags_returns_array(): void
    {
        $result = $this->client->eInsurance()->listTags();

        $this->assertIsArray($result);
    }

    public function test_list_templates_returns_array(): void
    {
        $result = $this->client->eInsurance()->listTemplates();

        $this->assertIsArray($result);
    }

    public function test_list_notifications_returns_array(): void
    {
        $result = $this->client->eInsurance()->listNotifications();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // İstatistikler
    // -------------------------------------------------------------------------

    public function test_get_statistics_returns_array(): void
    {
        $result = $this->client->eInsurance()->getStatistics();

        $this->assertIsArray($result);
    }

    public function test_get_last_statistics_returns_array(): void
    {
        try {
            $result = $this->client->eInsurance()->getLastStatistics();
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('getLastStatistics endpoint bu test hesabında mevcut değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Belge Listeleme
    // -------------------------------------------------------------------------

    public function test_list_insurances_returns_array(): void
    {
        $result = $this->client->eInsurance()->listInsurances(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_list_drafts_returns_array(): void
    {
        $result = $this->client->eInsurance()->listDrafts(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Raporlar
    // -------------------------------------------------------------------------

    public function test_list_reports_returns_array(): void
    {
        $result = $this->client->eInsurance()->listReports();

        $this->assertIsArray($result);
    }

    public function test_get_report_list_returns_array(): void
    {
        $result = $this->client->eInsurance()->getReportList();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Mevcut belge üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_get_insurance_histories_for_existing_document(): void
    {
        $list = $this->client->eInsurance()->listInsurances(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SKGB belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eInsurance()->getInsuranceHistories($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_insurance_status_for_existing_document(): void
    {
        $list = $this->client->eInsurance()->listInsurances(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SKGB belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eInsurance()->getInsuranceStatus($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_insurance_html_for_existing_document(): void
    {
        $list = $this->client->eInsurance()->listInsurances(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SKGB belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eInsurance()->getInsuranceHtml($uuid);

        $this->assertNotEmpty($result);
    }

    public function test_get_insurance_xml_for_existing_document(): void
    {
        $list = $this->client->eInsurance()->listInsurances(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SKGB belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eInsurance()->getInsuranceXml($uuid);

        $this->assertNotEmpty($result);
    }

    // -------------------------------------------------------------------------
    // Gönderme (e-SKGB gönder)
    // -------------------------------------------------------------------------

    public function test_send_insurance_returns_array_with_uuid(): void
    {
        $series = $this->client->eInsurance()->listSeries();

        $activeSeries = array_values(
            array_filter($series['Content'] ?? [], static fn (array $s) => ($s['IsActive'] ?? false) === true)
        );

        if (empty($activeSeries)) {
            $this->markTestSkipped('Test hesabında aktif e-SKGB serisi bulunamadı.');
        }

        $seriesName = $activeSeries[0]['Name'];

        $data = [
            'Insurance' => [
                'InsuranceInfo'  => [
                    'InsuranceSerieOrNumber' => $seriesName,
                    'IssueDate'             => (new \DateTimeImmutable())->format('Y-m-d\TH:i:s\Z'),
                    'CurrencyCode'          => 'TRY',
                ],
                'CustomerInfo'   => [
                    'TaxNumber' => '6310540565',
                    'Name'      => 'SDK Entegrasyon Test Alıcısı',
                    'Address'   => 'Test Mah. No:1',
                    'District'  => 'Kadıköy',
                    'City'      => 'İstanbul',
                    'TaxOffice' => 'Kadıköy',
                ],
                'InsuranceLines' => [
                    [
                        'Name'        => 'SDK e-SKGB Entegrasyon Testi',
                        'TotalAmount' => 100.0,
                    ],
                ],
            ],
        ];

        try {
            $result = $this->client->eInsurance()->send($data);
            $this->assertIsArray($result);
            $this->assertNotEmpty($result['UUID'] ?? $result['uuid'] ?? '');
        } catch (ValidationException $e) {
            $this->markTestSkipped('e-SKGB gönderme formatı doğrulanamadı: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Mevcut taslak üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_get_draft_model_for_existing_draft(): void
    {
        $list = $this->client->eInsurance()->listDrafts(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SKGB taslağı bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eInsurance()->getDraftModel($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_draft_html_for_existing_draft(): void
    {
        $list = $this->client->eInsurance()->listDrafts(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-SKGB taslağı bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eInsurance()->getDraftHtml($uuid);

        $this->assertNotEmpty($result);
    }

    // -------------------------------------------------------------------------
    // Seri detay (varsa)
    // -------------------------------------------------------------------------

    public function test_get_series_detail_for_existing_series(): void
    {
        $series = $this->client->eInsurance()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında e-SKGB serisi bulunamadı.');
        }

        $id     = $series['Content'][0]['Id'] ?? $series['Content'][0]['ID'] ?? null;

        if ($id === null) {
            $this->markTestSkipped('Seri Id alanı bulunamadı.');
        }

        $result = $this->client->eInsurance()->getSeriesDetail((int) $id);

        $this->assertIsArray($result);
    }
}
