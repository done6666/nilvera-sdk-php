<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Exception\ApiException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class ELedgerServiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Belgeler
    // -------------------------------------------------------------------------

    public function test_list_documents_returns_array(): void
    {
        try {
            $result = $this->client->eLedger()->listDocuments(['page' => 1, 'pageSize' => 5]);
            $this->assertIsArray($result);
        } catch (ApiException | NotFoundException $e) {
            $this->markTestSkipped('listDocuments bu test hesabında desteklenmiyor: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // GIB Durumu
    // -------------------------------------------------------------------------

    public function test_get_gib_status_returns_array(): void
    {
        try {
            $result = $this->client->eLedger()->getGibStatus();
            $this->assertIsArray($result);
        } catch (ApiException | NotFoundException $e) {
            $this->markTestSkipped('getGibStatus bu test hesabında desteklenmiyor: ' . $e->getMessage());
        }
    }

    public function test_get_gib_report_status_returns_array(): void
    {
        try {
            $result = $this->client->eLedger()->getGibReportStatus();
            $this->assertIsArray($result);
        } catch (ApiException | NotFoundException $e) {
            $this->markTestSkipped('getGibReportStatus bu test hesabında desteklenmiyor: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Belge Geçmişi
    // -------------------------------------------------------------------------

    public function test_get_document_history_for_existing_document(): void
    {
        try {
            $list = $this->client->eLedger()->listDocuments(['page' => 1, 'pageSize' => 1]);
        } catch (ApiException | NotFoundException $e) {
            $this->markTestSkipped('listDocuments bu test hesabında desteklenmiyor: ' . $e->getMessage());
        }

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-Defter belgesi bulunamadı.');
        }

        $uuid = $list['Content'][0]['UUID'];

        try {
            $result = $this->client->eLedger()->getDocumentHistory($uuid);
            $this->assertIsArray($result);
        } catch (ApiException | NotFoundException $e) {
            $this->markTestSkipped("getDocumentHistory bu UUID için çalışmadı ({$uuid}): " . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Şirket Sorguları
    // -------------------------------------------------------------------------

    public function test_query_companies_returns_array(): void
    {
        try {
            $result = $this->client->eLedger()->queryCompanies([
                'TaxNumbers' => ['1234567801'],
            ]);
            $this->assertIsArray($result);
        } catch (ApiException | NotFoundException $e) {
            $this->markTestSkipped('queryCompanies bu test hesabında desteklenmiyor: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // İmzalı belge Base64
    // -------------------------------------------------------------------------

    public function test_get_sign_base64_string_returns_string_or_is_accessible(): void
    {
        try {
            $result = $this->client->eLedger()->getSignBase64String();
            $this->assertIsString($result);
        } catch (ApiException | NotFoundException $e) {
            $this->markTestSkipped('getSignBase64String bu test hesabında mevcut değil: ' . $e->getMessage());
        }
    }
}
