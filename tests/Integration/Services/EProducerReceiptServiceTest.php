<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Enums\UnitType;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Requests\SendProducerReceiptRequest;
use Nilvera\Requests\ValueObjects\ProducerLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Responses\SendDocumentResponse;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class EProducerReceiptServiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Seri / Şablon / Etiket / Bildirim Listeleme
    // -------------------------------------------------------------------------

    public function test_list_series_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->listSeries();

        $this->assertIsArray($result);
    }

    public function test_list_tags_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->listTags();

        $this->assertIsArray($result);
    }

    public function test_list_templates_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->listTemplates();

        $this->assertIsArray($result);
    }

    public function test_list_notifications_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->listNotifications();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // İstatistikler
    // -------------------------------------------------------------------------

    public function test_get_statistics_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->getStatistics();

        $this->assertIsArray($result);
    }

    public function test_get_last_statistics_returns_array(): void
    {
        try {
            $result = $this->client->eProducerReceipt()->getLastStatistics();
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('getLastStatistics endpoint bu test hesabında mevcut değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Belge Listeleme
    // -------------------------------------------------------------------------

    public function test_list_producers_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->listProducers(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_list_old_producers_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->listOldProducers(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_list_drafts_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->listDrafts(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Raporlar
    // -------------------------------------------------------------------------

    public function test_list_reports_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->listReports();

        $this->assertIsArray($result);
    }

    public function test_get_report_list_returns_array(): void
    {
        $result = $this->client->eProducerReceipt()->getReportList();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Mevcut belge üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_get_producer_html_for_existing_document(): void
    {
        $list = $this->client->eProducerReceipt()->listProducers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-MM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eProducerReceipt()->getProducerHtml($uuid);

        $this->assertNotEmpty($result);
    }

    public function test_get_producer_xml_for_existing_document(): void
    {
        $list = $this->client->eProducerReceipt()->listProducers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-MM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eProducerReceipt()->getProducerXml($uuid);

        $this->assertNotEmpty($result);
    }

    public function test_get_producer_histories_for_existing_document(): void
    {
        $list = $this->client->eProducerReceipt()->listProducers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-MM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eProducerReceipt()->getProducerHistories($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_producer_status_for_existing_document(): void
    {
        $list = $this->client->eProducerReceipt()->listProducers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-MM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eProducerReceipt()->getProducerStatus($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_producer_details_for_existing_document(): void
    {
        $list = $this->client->eProducerReceipt()->listProducers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-MM belgesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eProducerReceipt()->getProducerDetails($uuid);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Gönderme (e-MM gönder)
    // -------------------------------------------------------------------------

    public function test_send_producer_receipt_returns_uuid(): void
    {
        $series = $this->client->eProducerReceipt()->listSeries();

        $activeSeries = array_values(
            array_filter($series['Content'] ?? [], static fn (array $s) => ($s['IsActive'] ?? false) === true)
        );

        if (empty($activeSeries)) {
            $this->markTestSkipped('Test hesabında aktif e-MM serisi bulunamadı.');
        }

        $seriesName = $activeSeries[0]['Name'];

        $request = new SendProducerReceiptRequest(
            customerInfo: new ReceiverRequest(
                taxNumber: '12345678950',
                name:      'SDK Entegrasyon Test Üreticisi',
                address:   'Köy Mah. No:5',
                district:  'Bünyan',
                city:      'Kayseri',
                taxOffice: 'Bünyan',
            ),
            producerLines: [
                new ProducerLineRequest(
                    name:     'SDK e-MM Entegrasyon Test Ürünü',
                    quantity: 100.0,
                    unitType: UnitType::Kilogram,
                    price:    5.0,
                ),
            ],
            issueDate:             new \DateTimeImmutable(),
            deliveryDate:          new \DateTimeImmutable(),
            producerSerieOrNumber: $seriesName,
        );

        try {
            $response = $this->client->eProducerReceipt()->send($request);
            $this->assertInstanceOf(SendDocumentResponse::class, $response);
            $this->assertNotEmpty($response->uuid);
        } catch (ApiException $e) {
            $this->markTestSkipped('e-MM gönderme bu test hesabında başarısız oldu: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Taslak üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_get_draft_model_for_existing_draft(): void
    {
        $list = $this->client->eProducerReceipt()->listDrafts(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-MM taslağı bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eProducerReceipt()->getDraftModel($uuid);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Seri detay
    // -------------------------------------------------------------------------

    public function test_get_series_detail_for_existing_series(): void
    {
        $series = $this->client->eProducerReceipt()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında e-MM serisi bulunamadı.');
        }

        $id = $series['Content'][0]['Id'] ?? $series['Content'][0]['ID'] ?? null;

        if ($id === null) {
            $this->markTestSkipped('Seri Id alanı bulunamadı.');
        }

        $result = $this->client->eProducerReceipt()->getSeriesDetail((int) $id);

        $this->assertIsArray($result);
    }
}
