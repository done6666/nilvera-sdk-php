<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class GeneralServiceTest extends IntegrationTestCase
{
    public function test_get_company_returns_company_data(): void
    {
        $result = $this->client->general()->getCompany();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('TaxNumber', $result);
        $this->assertArrayHasKey('Name', $result);
    }

    public function test_get_credits_returns_credit_info(): void
    {
        $result = $this->client->general()->getCredits();

        $this->assertIsArray($result);
    }

    public function test_get_exchange_rates_returns_rates(): void
    {
        $result = $this->client->general()->getExchangeRates();

        $this->assertIsArray($result);
    }

    public function test_list_customers_returns_array(): void
    {
        $result = $this->client->general()->listCustomers();

        $this->assertIsArray($result);
    }

    public function test_check_taxpayer_by_tax_number(): void
    {
        // 3230456015 is Nilvera's own VKN, safe to check in test env
        $result = $this->client->general()->checkTaxpayer('3230456015');

        $this->assertIsArray($result);
    }
}
