<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Exception\ApiException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class EStorageServiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // İstatistikler
    // -------------------------------------------------------------------------

    public function test_get_statistics_returns_array(): void
    {
        try {
            $result = $this->client->eStorage()->getStatistics();
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('getStatistics endpoint bu test hesabında mevcut değil (404).');
        }
    }

    public function test_get_last_statistics_returns_array(): void
    {
        try {
            $result = $this->client->eStorage()->getLastStatistics();
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('getLastStatistics endpoint bu test hesabında mevcut değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Defter Listeleme
    // -------------------------------------------------------------------------

    public function test_list_ledgers_returns_array(): void
    {
        $result = $this->client->eStorage()->listLedgers(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_list_ledgers_by_tax_number_returns_array(): void
    {
        $company   = $this->client->general()->getCompany();
        $taxNumber = $company['TaxNumber'] ?? '1234567801';

        try {
            $result = $this->client->eStorage()->listLedgersByTaxNumber($taxNumber);
            $this->assertIsArray($result);
        } catch (ApiException | NotFoundException $e) {
            $this->markTestSkipped('listLedgersByTaxNumber bu test hesabında desteklenmiyor: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Mevcut defter üzerinde işlemler
    // -------------------------------------------------------------------------

    public function test_preview_ledger_for_existing_ledger(): void
    {
        $list = $this->client->eStorage()->listLedgers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında e-Saklama defteri bulunamadı.');
        }

        $uuid = $list['Content'][0]['UUID'];

        try {
            $result = $this->client->eStorage()->previewLedger($uuid);
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped("previewLedger endpoint bu UUID için mevcut değil ({$uuid}).");
        }
    }
}
