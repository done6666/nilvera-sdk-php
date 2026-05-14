<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * General API: company/taxpayer queries, customer and product management.
 * Base path: /general
 */
class GeneralService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Taxpayer Operations
    // -------------------------------------------------------------------------

    /**
     * Query taxpayer information by tax number (VKN/TCKN).
     *
     * GET /general/GlobalCompany/Check/TaxNumber/{taxNumber}
     *
     * @return array<string, mixed>
     */
    public function checkTaxpayer(string $taxNumber): array
    {
        return $this->get("/general/GlobalCompany/Check/TaxNumber/{$taxNumber}")->json();
    }

    /**
     * List all registered e-invoice taxpayers.
     *
     * GET /general/GlobalCompany/List/EInvoice
     *
     * @return array<string, mixed>
     */
    public function listEInvoiceTaxpayers(): array
    {
        return $this->get('/general/GlobalCompany/List/EInvoice')->json();
    }

    // -------------------------------------------------------------------------
    // Customer Operations
    // -------------------------------------------------------------------------

    /**
     * List customers.
     *
     * GET /general/Customer
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listCustomers(array $query = []): array
    {
        return $this->get('/general/Customer', $query)->json();
    }

    /**
     * Get a single customer by ID.
     *
     * GET /general/Customer/{id}
     *
     * @return array<string, mixed>
     */
    public function getCustomer(string $id): array
    {
        return $this->get("/general/Customer/{$id}")->json();
    }

    /**
     * Create a new customer.
     *
     * POST /general/Customer
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createCustomer(array $data): array
    {
        return $this->post('/general/Customer', $data)->json();
    }

    /**
     * Update an existing customer.
     *
     * PUT /general/Customer/{id}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateCustomer(string $id, array $data): array
    {
        return $this->put("/general/Customer/{$id}", $data)->json();
    }

    /**
     * Delete a customer.
     *
     * DELETE /general/Customer/{id}
     */
    public function deleteCustomer(string $id): void
    {
        $this->delete("/general/Customer/{$id}");
    }

    // -------------------------------------------------------------------------
    // Product / Stock Operations
    // -------------------------------------------------------------------------

    /**
     * List products/stock items.
     *
     * GET /general/Stock
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listProducts(array $query = []): array
    {
        return $this->get('/general/Stock', $query)->json();
    }

    /**
     * Get a single product by ID.
     *
     * GET /general/Stock/{id}
     *
     * @return array<string, mixed>
     */
    public function getProduct(string $id): array
    {
        return $this->get("/general/Stock/{$id}")->json();
    }

    /**
     * Create a new product.
     *
     * POST /general/Stock
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createProduct(array $data): array
    {
        return $this->post('/general/Stock', $data)->json();
    }

    /**
     * Update a product.
     *
     * PUT /general/Stock/{id}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateProduct(string $id, array $data): array
    {
        return $this->put("/general/Stock/{$id}", $data)->json();
    }

    /**
     * Delete a product.
     *
     * DELETE /general/Stock/{id}
     */
    public function deleteProduct(string $id): void
    {
        $this->delete("/general/Stock/{$id}");
    }
}
