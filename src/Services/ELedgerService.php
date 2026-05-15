<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Defter (Electronic Ledger) API.
 * Base path: /eledger
 *
 * Covers: uploads, documents, signing (UUID/HSM),
 * GIB integration, company queries, and properties.
 */
class ELedgerService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Uploads
    // -------------------------------------------------------------------------

    /**
     * POST /eledger/Uploads/Base64String
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function uploadBase64(array $data): array
    {
        return $this->post('/eledger/Uploads/Base64String', $data)->json();
    }

    /**
     * POST /eledger/Uploads/UUID
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function uploadByUuid(array $data): array
    {
        return $this->post('/eledger/Uploads/UUID', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Documents
    // -------------------------------------------------------------------------

    /**
     * GET /eledger/Documents
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDocuments(array $query = []): array
    {
        return $this->get('/eledger/Documents', $query)->json();
    }

    /**
     * GET /eledger/Documents/History/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getDocumentHistory(string $uuid): array
    {
        return $this->get("/eledger/Documents/History/{$uuid}")->json();
    }

    /**
     * POST /eledger/Documents/Html
     *
     * @param array<string, mixed> $data
     * @return string
     */
    public function getDocumentHtml(array $data): string
    {
        return $this->post('/eledger/Documents/Html', $data)->getBody();
    }

    // -------------------------------------------------------------------------
    // Sign
    // -------------------------------------------------------------------------

    /**
     * POST /eledger/Sign/UUID
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function signByUuid(array $data): array
    {
        return $this->post('/eledger/Sign/UUID', $data)->json();
    }

    /**
     * POST /eledger/Sign/Hsm
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function signByHsm(array $data): array
    {
        return $this->post('/eledger/Sign/Hsm', $data)->json();
    }

    /**
     * GET /eledger/Sign/Base64String
     *
     * @param array<string, mixed> $query
     * @return string
     */
    public function getSignBase64String(array $query = []): string
    {
        return $this->get('/eledger/Sign/Base64String', $query)->getBody();
    }

    /**
     * POST /eledger/Sign/Upload
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function uploadSignedDocument(array $data): array
    {
        return $this->post('/eledger/Sign/Upload', $data)->json();
    }

    // -------------------------------------------------------------------------
    // GIB Integration
    // -------------------------------------------------------------------------

    /**
     * POST /eledger/Gib/Send
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendToGib(array $data): array
    {
        return $this->post('/eledger/Gib/Send', $data)->json();
    }

    /**
     * POST /eledger/Gib/Receive
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function receiveFromGib(array $data): array
    {
        return $this->post('/eledger/Gib/Receive', $data)->json();
    }

    /**
     * POST /eledger/Gib/Send/Report
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendReportToGib(array $data): array
    {
        return $this->post('/eledger/Gib/Send/Report', $data)->json();
    }

    /**
     * GET /eledger/Gib/Status
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getGibStatus(array $query = []): array
    {
        return $this->get('/eledger/Gib/Status', $query)->json();
    }

    /**
     * GET /eledger/Gib/Status/Report
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getGibReportStatus(array $query = []): array
    {
        return $this->get('/eledger/Gib/Status/Report', $query)->json();
    }

    /**
     * POST /eledger/Gib/ReSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function resendToGib(array $data): array
    {
        return $this->post('/eledger/Gib/ReSend', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Companies
    // -------------------------------------------------------------------------

    /**
     * POST /eledger/Companies/Query
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function queryCompanies(array $data): array
    {
        return $this->post('/eledger/Companies/Query', $data)->json();
    }

    /**
     * GET /eledger/Companies/Erp/Parameters/{companyErpId}
     *
     * @return array<string, mixed>
     */
    public function getCompanyErpParameters(string $companyErpId): array
    {
        return $this->get("/eledger/Companies/Erp/Parameters/{$companyErpId}")->json();
    }

    // -------------------------------------------------------------------------
    // Properties
    // -------------------------------------------------------------------------

    /**
     * PUT /eledger/Properties
     *
     * @param array<string, mixed> $data
     */
    public function updateProperties(array $data): void
    {
        $this->put('/eledger/Properties', $data);
    }
}
