<?php

declare(strict_types=1);

namespace Nilvera;

use GuzzleHttp\Client as GuzzleClient;
use Nilvera\Http\HttpClient;
use Nilvera\Services\EArchiveService;
use Nilvera\Services\EInsuranceService;
use Nilvera\Services\ELedgerService;
use Nilvera\Services\EProducerReceiptService;
use Nilvera\Services\EReceiptService;
use Nilvera\Services\ESelfEmployedService;
use Nilvera\Services\EStorageService;
use Nilvera\Services\EWaybillService;
use Nilvera\Services\EInvoiceService;
use Nilvera\Services\GeneralService;
use Nilvera\Services\ReportService;

/**
 * Main entry point for the Nilvera PHP SDK.
 *
 * Usage:
 *   $client = NilveraClient::live('your-api-key');
 *   $client->eInvoice()->send([...]);
 */
class NilveraClient
{
    private readonly HttpClient $httpClient;

    public function __construct(private readonly Config $config)
    {
        $guzzle = new GuzzleClient([
            'timeout'         => $config->getTimeout(),
            'connect_timeout' => $config->getConnectTimeout(),
            'http_errors'     => false,
        ]);

        $this->httpClient = new HttpClient($guzzle, $config);
    }

    /**
     * Create a client pointing to the live (production) environment.
     */
    public static function live(string $apiKey): self
    {
        return new self(Config::live($apiKey));
    }

    /**
     * Create a client pointing to the test environment.
     */
    public static function test(string $apiKey): self
    {
        return new self(Config::test($apiKey));
    }

    /**
     * Create a client from a Config object.
     */
    public static function fromConfig(Config $config): self
    {
        return new self($config);
    }

    // -------------------------------------------------------------------------
    // Service accessors
    // -------------------------------------------------------------------------

    public function general(): GeneralService
    {
        return new GeneralService($this->httpClient);
    }

    public function eInvoice(): EInvoiceService
    {
        return new EInvoiceService($this->httpClient);
    }

    public function eArchive(): EArchiveService
    {
        return new EArchiveService($this->httpClient);
    }

    public function eWaybill(): EWaybillService
    {
        return new EWaybillService($this->httpClient);
    }

    /** E-SMM: Serbest Meslek Makbuzu */
    public function eSelfEmployed(): ESelfEmployedService
    {
        return new ESelfEmployedService($this->httpClient);
    }

    /** E-MM: Müstahsil Makbuzu */
    public function eProducerReceipt(): EProducerReceiptService
    {
        return new EProducerReceiptService($this->httpClient);
    }

    /** E-SKGB: Sigorta Komisyon Gider Belgesi */
    public function eInsurance(): EInsuranceService
    {
        return new EInsuranceService($this->httpClient);
    }

    /** E-Adisyon: Electronic Bill */
    public function eReceipt(): EReceiptService
    {
        return new EReceiptService($this->httpClient);
    }

    /** E-Saklama: Document Storage */
    public function eStorage(): EStorageService
    {
        return new EStorageService($this->httpClient);
    }

    /** E-Defter: Electronic Ledger */
    public function eLedger(): ELedgerService
    {
        return new ELedgerService($this->httpClient);
    }

    public function report(): ReportService
    {
        return new ReportService($this->httpClient);
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
