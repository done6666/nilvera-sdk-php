<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Exception\AuthenticationException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class GeneralServiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Firma Bilgileri
    // -------------------------------------------------------------------------

    public function test_get_company_returns_company_data(): void
    {
        $result = $this->client->general()->getCompany();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('TaxNumber', $result);
        $this->assertArrayHasKey('Name', $result);
    }

    public function test_get_company_modules_returns_array(): void
    {
        $result = $this->client->general()->getCompanyModules();

        $this->assertIsArray($result);
    }

    public function test_get_user_companies_returns_array(): void
    {
        $result = $this->client->general()->getUserCompanies();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Sertifikalar
    // -------------------------------------------------------------------------

    public function test_get_certificate_returns_array(): void
    {
        try {
            $result = $this->client->general()->getCertificate();
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('getCertificate endpoint bu test hesabında mevcut değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Firma Kimlik Bilgileri
    // -------------------------------------------------------------------------

    public function test_get_company_identifications_returns_array(): void
    {
        $result = $this->client->general()->getCompanyIdentifications();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // GIB e-Arşiv Hesabı
    // -------------------------------------------------------------------------

    public function test_get_gib_e_archive_account_returns_array(): void
    {
        try {
            $result = $this->client->general()->getGibEArchiveAccount();
            $this->assertIsArray($result);
        } catch (NotFoundException) {
            $this->markTestSkipped('getGibEArchiveAccount endpoint bu test hesabında mevcut değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Krediler & Kampanyalar
    // -------------------------------------------------------------------------

    public function test_get_credits_returns_credit_info(): void
    {
        $result = $this->client->general()->getCredits();

        $this->assertIsArray($result);
    }

    public function test_get_campaigns_returns_array(): void
    {
        $result = $this->client->general()->getCampaigns();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Döviz Kurları
    // -------------------------------------------------------------------------

    public function test_get_exchange_rates_returns_rates(): void
    {
        $result = $this->client->general()->getExchangeRates();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Mail Ayarları
    // -------------------------------------------------------------------------

    public function test_get_mail_settings_returns_array(): void
    {
        try {
            $result = $this->client->general()->getMailSettings();
            $this->assertIsArray($result);
        } catch (AuthenticationException) {
            $this->markTestSkipped('getMailSettings bu test hesabında yetki gerektiriyor (403).');
        }
    }

    // -------------------------------------------------------------------------
    // Mükellef (GlobalCompany) İşlemleri
    // -------------------------------------------------------------------------

    public function test_list_taxpayers_returns_paginated_array(): void
    {
        $result = $this->client->general()->listTaxpayers(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_search_taxpayers_returns_array(): void
    {
        $result = $this->client->general()->searchTaxpayers('Nilvera');

        $this->assertIsArray($result);
    }

    public function test_get_taxpayer_by_tax_number_returns_array(): void
    {
        // Nilvera'nın kendi VKN'si — test ortamında güvenli
        $result = $this->client->general()->getTaxpayerByTaxNumber('3230456015');

        $this->assertIsArray($result);
    }

    public function test_check_taxpayer_by_tax_number(): void
    {
        $result = $this->client->general()->checkTaxpayer('3230456015');

        $this->assertIsArray($result);
    }

    public function test_check_taxpayer_by_name_returns_array(): void
    {
        $result = $this->client->general()->checkTaxpayerByName('Nilvera');

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Müşteri İşlemleri
    // -------------------------------------------------------------------------

    public function test_list_customers_returns_array(): void
    {
        $result = $this->client->general()->listCustomers(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_search_customers_returns_array(): void
    {
        $result = $this->client->general()->searchCustomers('Test');

        $this->assertIsArray($result);
    }

    public function test_get_customer_by_tax_number_returns_array_or_skips(): void
    {
        $list = $this->client->general()->listCustomers(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında kayıtlı müşteri bulunamadı.');
        }

        $taxNumber = $list['Content'][0]['TaxNumber'] ?? null;

        if ($taxNumber === null) {
            $this->markTestSkipped('Müşteri TaxNumber alanı bulunamadı.');
        }

        $result = $this->client->general()->getCustomerByTaxNumber($taxNumber);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Stok İşlemleri
    // -------------------------------------------------------------------------

    public function test_list_stocks_returns_array(): void
    {
        $result = $this->client->general()->listStocks(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_search_stocks_returns_array(): void
    {
        $result = $this->client->general()->searchStocks('Test');

        $this->assertIsArray($result);
    }

    public function test_get_stock_for_existing_stock(): void
    {
        $list = $this->client->general()->listStocks(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında stok kaydı bulunamadı.');
        }

        $id     = $list['Content'][0]['Id'] ?? $list['Content'][0]['ID'] ?? null;

        if ($id === null) {
            $this->markTestSkipped('Stok Id alanı bulunamadı.');
        }

        $result = $this->client->general()->getStock((int) $id);

        $this->assertIsArray($result);
    }
}
