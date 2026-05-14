<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Saklama (Document Storage/Archiving) API.
 * Base path: /estorage
 *
 * Covers: listing stored ledgers, download, preview, upload,
 * base64 upload by tax number, statistics.
 */
class EStorageService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Ledgers
    // -------------------------------------------------------------------------

    /**
     * GET /estorage/Ledgers
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listLedgers(array $query = []): array
    {
        return $this->get('/estorage/Ledgers', $query)->json();
    }

    /**
     * GET /estorage/Ledgers/List/{taxNumber}
     *
     * @return array<string, mixed>
     */
    public function listLedgersByTaxNumber(string $taxNumber): array
    {
        return $this->get("/estorage/Ledgers/List/{$taxNumber}")->json();
    }

    /**
     * POST /estorage/Ledgers/Download
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function downloadLedger(array $data): array
    {
        return $this->post('/estorage/Ledgers/Download', $data)->json();
    }

    /**
     * POST /estorage/Ledgers/{uuid}/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewLedger(string $uuid, array $data = []): array
    {
        return $this->post("/estorage/Ledgers/{$uuid}/Preview", $data)->json();
    }

    /**
     * POST /estorage/Ledgers/Upload
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function upload(array $data): array
    {
        return $this->post('/estorage/Ledgers/Upload', $data)->json();
    }

    /**
     * POST /estorage/Ledgers/Base64String/{taxNumber}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function uploadBase64(string $taxNumber, array $data): array
    {
        return $this->post("/estorage/Ledgers/Base64String/{$taxNumber}", $data)->json();
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * GET /estorage/Ledgers/Statistics
     *
     * @return array<string, mixed>
     */
    public function getStatistics(): array
    {
        return $this->get('/estorage/Ledgers/Statistics')->json();
    }

    /**
     * GET /estorage/Ledgers/Statistics/Last
     *
     * @return array<string, mixed>
     */
    public function getLastStatistics(): array
    {
        return $this->get('/estorage/Ledgers/Statistics/Last')->json();
    }
}
