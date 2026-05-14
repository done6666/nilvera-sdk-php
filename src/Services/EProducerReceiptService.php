<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-MM (Müstahsil Makbuzu / Producer Receipt) API.
 * Base path: /emm
 */
class EProducerReceiptService extends AbstractService
{
    /**
     * List outgoing producer receipts.
     *
     * GET /emm/Sale
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listSaleReceipts(array $query = []): array
    {
        return $this->get('/emm/Sale', $query)->json();
    }

    /**
     * Get a single outgoing producer receipt by UUID.
     *
     * GET /emm/Sale/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getSaleReceipt(string $uuid): array
    {
        return $this->get("/emm/Sale/{$uuid}")->json();
    }

    /**
     * Get HTML of a producer receipt.
     *
     * GET /emm/Sale/{uuid}/Html
     */
    public function getSaleReceiptHtml(string $uuid): string
    {
        return $this->get("/emm/Sale/{$uuid}/Html")->getBody();
    }

    /**
     * Get PDF of a producer receipt.
     *
     * GET /emm/Sale/{uuid}/Pdf
     */
    public function getSaleReceiptPdf(string $uuid): string
    {
        return $this->get("/emm/Sale/{$uuid}/Pdf")->getBody();
    }

    /**
     * Send a producer receipt via email.
     *
     * POST /emm/Sale/{uuid}/Email
     *
     * @param string[] $emailAddresses
     */
    public function sendSaleReceiptByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post("/emm/Sale/{$uuid}/Email", ['emailAddresses' => $emailAddresses]);
    }

    /**
     * Send a producer receipt using a structured model.
     *
     * POST /emm/Send/Model
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function send(array $data): array
    {
        return $this->post('/emm/Send/Model', $data)->json();
    }

    /**
     * List draft producer receipts.
     *
     * GET /emm/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/emm/Draft', $query)->json();
    }

    /**
     * Create a draft producer receipt.
     *
     * POST /emm/Draft
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/emm/Draft', $data)->json();
    }

    /**
     * Delete a draft producer receipt.
     *
     * DELETE /emm/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/emm/Draft/{$uuid}");
    }

    /**
     * Send a draft producer receipt.
     *
     * POST /emm/Draft/{uuid}/Send
     *
     * @return array<string, mixed>
     */
    public function sendDraft(string $uuid): array
    {
        return $this->post("/emm/Draft/{$uuid}/Send")->json();
    }
}
