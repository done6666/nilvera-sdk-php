<?php

declare(strict_types=1);

namespace Nilvera\Services;

use Nilvera\Requests\CreateCustomerRequest;

/**
 * General API: company, taxpayer, customer, stock, GIB account, credits,
 * company identity, mailing settings.
 * Base path: /general
 */
class GeneralService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Company Operations
    // -------------------------------------------------------------------------

    /**
     * GET /general/Company
     *
     * @return array<string, mixed>
     */
    public function getCompany(): array
    {
        return $this->get('/general/Company')->json();
    }

    /**
     * PUT /general/Company
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateCompany(array $data): array
    {
        return $this->put('/general/Company', $data)->json();
    }

    /**
     * GET /general/Company/Modules
     *
     * @return array<string, mixed>
     */
    public function getCompanyModules(): array
    {
        return $this->get('/general/Company/Modules')->json();
    }

    /**
     * GET /general/Company/List — list companies assigned to the user.
     *
     * @return array<string, mixed>
     */
    public function getUserCompanies(): array
    {
        return $this->get('/general/Company/List')->json();
    }

    // -------------------------------------------------------------------------
    // Company Certificates
    // -------------------------------------------------------------------------

    /**
     * GET /general/Company/Certificate
     *
     * @return array<string, mixed>
     */
    public function getCertificate(): array
    {
        return $this->get('/general/Company/Certificate')->json();
    }

    /**
     * GET /general/Company/Certificate/{serialNumber}
     *
     * @return array<string, mixed>
     */
    public function getCertificateBySerial(string $serialNumber): array
    {
        return $this->get("/general/Company/Certificate/{$serialNumber}")->json();
    }

    /**
     * POST /general/Company/Certificate
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function addCertificate(array $data): array
    {
        return $this->post('/general/Company/Certificate', $data)->json();
    }

    /**
     * PUT /general/Company/Certificate
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateCertificate(array $data): array
    {
        return $this->put('/general/Company/Certificate', $data)->json();
    }

    /**
     * DELETE /general/Company/Certificate/{id}
     */
    public function deleteCertificate(int $id): void
    {
        $this->delete("/general/Company/Certificate/{$id}");
    }

    // -------------------------------------------------------------------------
    // Company Identity Information
    // -------------------------------------------------------------------------

    /**
     * GET /general/CompanyIdentification
     *
     * @return array<string, mixed>
     */
    public function getCompanyIdentifications(): array
    {
        return $this->get('/general/CompanyIdentification')->json();
    }

    /**
     * POST /general/CompanyIdentification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function addCompanyIdentification(array $data): array
    {
        return $this->post('/general/CompanyIdentification', $data)->json();
    }

    /**
     * DELETE /general/CompanyIdentification/{id}
     */
    public function deleteCompanyIdentification(int $id): void
    {
        $this->delete("/general/CompanyIdentification/{$id}");
    }

    // -------------------------------------------------------------------------
    // GIB e-Archive Account
    // -------------------------------------------------------------------------

    /**
     * GET /general/GibEArchiveAccount
     *
     * @return array<string, mixed>
     */
    public function getGibEArchiveAccount(): array
    {
        return $this->get('/general/GibEArchiveAccount')->json();
    }

    /**
     * PUT /general/GibEArchiveAccount
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateGibEArchiveAccount(array $data): array
    {
        return $this->put('/general/GibEArchiveAccount', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Credits & Campaigns
    // -------------------------------------------------------------------------

    /**
     * GET /general/Credits
     *
     * @return array<string, mixed>
     */
    public function getCredits(): array
    {
        return $this->get('/general/Credits')->json();
    }

    /**
     * GET /general/Campaigns
     *
     * @return array<string, mixed>
     */
    public function getCampaigns(): array
    {
        return $this->get('/general/Campaigns')->json();
    }

    // -------------------------------------------------------------------------
    // Exchange Rates
    // -------------------------------------------------------------------------

    /**
     * GET /general/ExchangeRate
     *
     * @return array<string, mixed>
     */
    public function getExchangeRates(): array
    {
        return $this->get('/general/ExchangeRate')->json();
    }

    // -------------------------------------------------------------------------
    // Mailing Settings
    // -------------------------------------------------------------------------

    /**
     * GET /general/Mailing/Setting
     *
     * @return array<string, mixed>
     */
    public function getMailSettings(): array
    {
        return $this->get('/general/Mailing/Setting')->json();
    }

    /**
     * PUT /general/Mailing/Setting/Whatsapp
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateWhatsappFeature(array $data): array
    {
        return $this->put('/general/Mailing/Setting/Whatsapp', $data)->json();
    }

    /**
     * PUT /general/Mailing/Setting/Mail
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateMailFeature(array $data): array
    {
        return $this->put('/general/Mailing/Setting/Mail', $data)->json();
    }

    /**
     * PUT /general/Mailing/Setting/Sms
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateSmsFeature(array $data): array
    {
        return $this->put('/general/Mailing/Setting/Sms', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Taxpayer / GlobalCompany Operations
    // -------------------------------------------------------------------------

    /**
     * List all registered companies / taxpayers.
     *
     * GET /general/GlobalCompany
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listTaxpayers(array $query = []): array
    {
        return $this->get('/general/GlobalCompany', $query)->json();
    }

    /**
     * Taxpayer list returned as a ZIP file.
     *
     * GET /general/GlobalCompany/{aliasType}/{globalUserType}
     *
     * @param string $aliasType       PK | GB
     * @param string $globalUserType  Invoice | DespatchAdvice
     */
    public function downloadTaxpayersZip(string $aliasType, string $globalUserType): string
    {
        return $this->get("/general/GlobalCompany/{$aliasType}/{$globalUserType}")->getBody();
    }

    /**
     * Search taxpayers by name.
     *
     * GET /general/GlobalCompany/Search/{searchText}
     *
     * @return array<string, mixed>
     */
    public function searchTaxpayers(string $searchText): array
    {
        return $this->get("/general/GlobalCompany/Search/{$searchText}")->json();
    }

    /**
     * Get taxpayer details by tax number (GET).
     *
     * GET /general/GlobalCompany/GetGlobalCustomerInfo/{taxNumber}
     *
     * @return array<string, mixed>
     */
    public function getTaxpayerByTaxNumber(string $taxNumber): array
    {
        return $this->get("/general/GlobalCompany/GetGlobalCustomerInfo/{$taxNumber}")->json();
    }

    /**
     * Get taxpayer details by tax number (POST — for batch or special use).
     *
     * POST /general/GlobalCompany/GetGlobalCustomerInfo
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function getTaxpayerInfo(array $data): array
    {
        return $this->post('/general/GlobalCompany/GetGlobalCustomerInfo', $data)->json();
    }

    /**
     * Check if a company is registered by name.
     *
     * GET /general/GlobalCompany/Check/Name/{name}
     *
     * @return array<string, mixed>
     */
    public function checkTaxpayerByName(string $name): array
    {
        return $this->get("/general/GlobalCompany/Check/Name/{$name}")->json();
    }

    /**
     * Check if a company is registered by tax number (VKN/TCKN).
     *
     * GET /general/GlobalCompany/Check/TaxNumber/{taxNumber}
     *
     * @return array<string, mixed>
     */
    public function checkTaxpayer(string $taxNumber): array
    {
        return $this->get("/general/GlobalCompany/Check/TaxNumber/{$taxNumber}")->json();
    }

    // -------------------------------------------------------------------------
    // Customer Operations
    // -------------------------------------------------------------------------

    /**
     * GET /general/Customers
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listCustomers(array $query = []): array
    {
        return $this->get('/general/Customers', $query)->json();
    }

    /**
     * GET /general/Customers/GetCustomerInfo/{taxNumber}
     *
     * @return array<string, mixed>
     */
    public function getCustomerByTaxNumber(string $taxNumber): array
    {
        return $this->get("/general/Customers/GetCustomerInfo/{$taxNumber}")->json();
    }

    /**
     * GET /general/Customers/Search/{searchText}
     *
     * @return array<string, mixed>
     */
    public function searchCustomers(string $searchText): array
    {
        return $this->get("/general/Customers/Search/{$searchText}")->json();
    }

    /**
     * POST /general/Customers
     *
     * @return array<string, mixed>
     */
    public function createCustomer(CreateCustomerRequest $customer): array
    {
        return $this->post('/general/Customers', $customer->toArray())->json();
    }

    /**
     * PUT /general/Customers
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateCustomer(array $data): array
    {
        return $this->put('/general/Customers', $data)->json();
    }

    /**
     * DELETE /general/Customers/{id}
     */
    public function deleteCustomer(int $id): void
    {
        $this->delete("/general/Customers/{$id}");
    }

    /**
     * DELETE /general/Customers/Bulk
     */
    public function deleteCustomersBulk(): void
    {
        $this->delete('/general/Customers/Bulk');
    }

    // -------------------------------------------------------------------------
    // Stock / Product Operations
    // -------------------------------------------------------------------------

    /**
     * GET /general/Stocks
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listStocks(array $query = []): array
    {
        return $this->get('/general/Stocks', $query)->json();
    }

    /**
     * GET /general/Stocks/{id}
     *
     * @return array<string, mixed>
     */
    public function getStock(int $id): array
    {
        return $this->get("/general/Stocks/{$id}")->json();
    }

    /**
     * GET /general/Stocks/SearchStock/{searchText}
     *
     * @return array<string, mixed>
     */
    public function searchStocks(string $searchText): array
    {
        return $this->get("/general/Stocks/SearchStock/{$searchText}")->json();
    }

    /**
     * POST /general/Stocks
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createStock(array $data): array
    {
        return $this->post('/general/Stocks', $data)->json();
    }

    /**
     * PUT /general/Stocks
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateStock(array $data): array
    {
        return $this->put('/general/Stocks', $data)->json();
    }

    /**
     * DELETE /general/Stocks/{id}
     */
    public function deleteStock(int $id): void
    {
        $this->delete("/general/Stocks/{$id}");
    }

    /**
     * DELETE /general/Stocks/Bulk
     */
    public function deleteStocksBulk(): void
    {
        $this->delete('/general/Stocks/Bulk');
    }
}
