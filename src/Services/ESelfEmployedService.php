<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-SMM (Serbest Meslek Makbuzu / Self-Employed Professional Receipt) API.
 * Base path: /esmm
 */
class ESelfEmployedService extends AbstractService
{
    /**
     * List outgoing SMM receipts.
     *
     * GET /esmm/Sale
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listSaleReceipts(array $query = []): array
    {
        return $this->get('/esmm/Sale', $query)->json();
    }

    /**
     * Get a single outgoing SMM receipt by UUID.
     *
     * GET /esmm/Sale/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getSaleReceipt(string $uuid): array
    {
        return $this->get("/esmm/Sale/{$uuid}")->json();
    }

    /**
     * Get HTML of an outgoing SMM receipt.
     *
     * GET /esmm/Sale/{uuid}/Html
     */
    public function getSaleReceiptHtml(string $uuid): string
    {
        return $this->get("/esmm/Sale/{$uuid}/Html")->getBody();
    }

    /**
     * Get PDF of an outgoing SMM receipt.
     *
     * GET /esmm/Sale/{uuid}/Pdf
     */
    public function getSaleReceiptPdf(string $uuid): string
    {
        return $this->get("/esmm/Sale/{$uuid}/Pdf")->getBody();
    }

    /**
     * Send an SMM receipt via email.
     *
     * POST /esmm/Sale/{uuid}/Email
     *
     * @param string[] $emailAddresses
     */
    public function sendSaleReceiptByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post("/esmm/Sale/{$uuid}/Email", ['emailAddresses' => $emailAddresses]);
    }

    /**
     * Send an SMM receipt using a structured model.
     *
     * POST /esmm/Send/Model
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function send(array $data): array
    {
        return $this->post('/esmm/Send/Model', $data)->json();
    }

    /**
     * List draft SMM receipts.
     *
     * GET /esmm/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/esmm/Draft', $query)->json();
    }

    /**
     * Create a draft SMM receipt.
     *
     * POST /esmm/Draft
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/esmm/Draft', $data)->json();
    }

    /**
     * Update a draft SMM receipt.
     *
     * PUT /esmm/Draft/{uuid}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateDraft(string $uuid, array $data): array
    {
        return $this->put("/esmm/Draft/{$uuid}", $data)->json();
    }

    /**
     * Delete a draft SMM receipt.
     *
     * DELETE /esmm/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/esmm/Draft/{$uuid}");
    }

    /**
     * Send a draft SMM receipt.
     *
     * POST /esmm/Draft/{uuid}/Send
     *
     * @return array<string, mixed>
     */
    public function sendDraft(string $uuid): array
    {
        return $this->post("/esmm/Draft/{$uuid}/Send")->json();
    }
}
