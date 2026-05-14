<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit;

use Nilvera\Config;
use Nilvera\Enums\Environment;
use Nilvera\NilveraClient;
use Nilvera\Services\EArchiveService;
use Nilvera\Services\EInvoiceService;
use Nilvera\Services\EInsuranceService;
use Nilvera\Services\ELedgerService;
use Nilvera\Services\EProducerReceiptService;
use Nilvera\Services\EReceiptService;
use Nilvera\Services\ESelfEmployedService;
use Nilvera\Services\EStorageService;
use Nilvera\Services\EWaybillService;
use Nilvera\Services\GeneralService;
use Nilvera\Services\ReportService;
use PHPUnit\Framework\TestCase;

class NilveraClientTest extends TestCase
{
    public function test_live_factory_creates_live_client(): void
    {
        $client = NilveraClient::live('my-api-key');

        $this->assertSame(Environment::Live, $client->getConfig()->getEnvironment());
        $this->assertSame('my-api-key', $client->getConfig()->getApiKey());
    }

    public function test_test_factory_creates_test_client(): void
    {
        $client = NilveraClient::test('my-api-key');

        $this->assertSame(Environment::Test, $client->getConfig()->getEnvironment());
        $this->assertTrue($client->getConfig()->isTestMode());
    }

    public function test_from_config_uses_provided_config(): void
    {
        $config = Config::live('key');
        $client = NilveraClient::fromConfig($config);

        $this->assertSame($config, $client->getConfig());
    }

    public function test_service_accessors_return_correct_instances(): void
    {
        $client = NilveraClient::test('test-key');

        $this->assertInstanceOf(GeneralService::class, $client->general());
        $this->assertInstanceOf(EInvoiceService::class, $client->eInvoice());
        $this->assertInstanceOf(EArchiveService::class, $client->eArchive());
        $this->assertInstanceOf(EWaybillService::class, $client->eWaybill());
        $this->assertInstanceOf(ESelfEmployedService::class, $client->eSelfEmployed());
        $this->assertInstanceOf(EProducerReceiptService::class, $client->eProducerReceipt());
        $this->assertInstanceOf(EInsuranceService::class, $client->eInsurance());
        $this->assertInstanceOf(EReceiptService::class, $client->eReceipt());
        $this->assertInstanceOf(EStorageService::class, $client->eStorage());
        $this->assertInstanceOf(ELedgerService::class, $client->eLedger());
        $this->assertInstanceOf(ReportService::class, $client->report());
    }
}
