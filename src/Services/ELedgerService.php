<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Defter (Electronic Ledger) API.
 * Base path: /eledger
 */
class ELedgerService extends AbstractService
{
    /**
     * List e-ledger books.
     *
     * GET /eledger/Book
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listBooks(array $query = []): array
    {
        return $this->get('/eledger/Book', $query)->json();
    }

    /**
     * Get a single e-ledger book by UUID.
     *
     * GET /eledger/Book/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getBook(string $uuid): array
    {
        return $this->get("/eledger/Book/{$uuid}")->json();
    }

    /**
     * Create an e-ledger book.
     *
     * POST /eledger/Book
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createBook(array $data): array
    {
        return $this->post('/eledger/Book', $data)->json();
    }

    /**
     * Submit (seal) an e-ledger book to GIB.
     *
     * POST /eledger/Book/{uuid}/Submit
     *
     * @return array<string, mixed>
     */
    public function submitBook(string $uuid): array
    {
        return $this->post("/eledger/Book/{$uuid}/Submit")->json();
    }

    /**
     * Get the status of an e-ledger submission.
     *
     * GET /eledger/Book/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getBookStatus(string $uuid): array
    {
        return $this->get("/eledger/Book/{$uuid}/Status")->json();
    }

    /**
     * Download the e-ledger book file.
     *
     * GET /eledger/Book/{uuid}/Download
     */
    public function downloadBook(string $uuid): string
    {
        return $this->get("/eledger/Book/{$uuid}/Download")->getBody();
    }

    /**
     * List e-ledger reports.
     *
     * GET /eledger/Report
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReports(array $query = []): array
    {
        return $this->get('/eledger/Report', $query)->json();
    }
}
