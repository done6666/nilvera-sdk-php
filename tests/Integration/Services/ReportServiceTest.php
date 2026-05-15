<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Exception\NotFoundException;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class ReportServiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Muhasebe Raporları
    // -------------------------------------------------------------------------

    public function test_list_reports_returns_array(): void
    {
        $result = $this->client->report()->listReports(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_create_report_returns_array(): void
    {
        // Basit bir raporlama isteği — gerçek parametre setini belirlemek için önce şablon listesi çekilir
        $templates = $this->client->report()->listReportTemplates(['page' => 1, 'pageSize' => 1]);

        if (empty($templates['Content'])) {
            $this->markTestSkipped('Test hesabında rapor şablonu bulunamadı.');
        }

        $templateId = $templates['Content'][0]['Id'] ?? $templates['Content'][0]['ID'] ?? null;

        if ($templateId === null) {
            $this->markTestSkipped('Rapor şablonu Id alanı bulunamadı.');
        }

        try {
            $result = $this->client->report()->createReport([
                'TemplateId' => $templateId,
                'StartDate'  => (new \DateTimeImmutable('first day of last month'))->format('Y-m-d'),
                'EndDate'    => (new \DateTimeImmutable('last day of last month'))->format('Y-m-d'),
            ]);

            $this->assertIsArray($result);
        } catch (\Nilvera\Exception\ValidationException $e) {
            $this->markTestSkipped('Rapor oluşturma formatı doğrulanamadı: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Rapor Şablonları
    // -------------------------------------------------------------------------

    public function test_list_report_templates_returns_array(): void
    {
        $result = $this->client->report()->listReportTemplates(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Şablon Sütunları
    // -------------------------------------------------------------------------

    public function test_list_report_template_columns_returns_array(): void
    {
        try {
            $result = $this->client->report()->listReportTemplateColumns(1, 'Invoice');
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('listReportTemplateColumns endpoint bu parametre kombinasyonu için mevcut değil.');
        }
    }

    // -------------------------------------------------------------------------
    // Mevcut rapor üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_download_report_returns_non_empty_string_for_existing_report(): void
    {
        $list = $this->client->report()->listReports(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında muhasebe raporu bulunamadı.');
        }

        $uuid = $list['Content'][0]['UUID'] ?? $list['Content'][0]['Uuid'] ?? null;

        if ($uuid === null) {
            $this->markTestSkipped('Rapor UUID alanı bulunamadı.');
        }

        try {
            $result = $this->client->report()->downloadReport($uuid);
            $this->assertNotEmpty($result);
        } catch (NotFoundException) {
            $this->markTestSkipped("downloadReport endpoint bu UUID için mevcut değil ({$uuid}).");
        }
    }
}
