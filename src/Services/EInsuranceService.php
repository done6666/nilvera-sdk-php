<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-SKGB (Sigorta Komisyon Gider Belgesi / Insurance Commission Expense Document) API.
 * Base path: /eskgb
 */
class EInsuranceService extends AbstractService
{
    /**
     * List outgoing insurance commission expense documents.
     *
     * GET /eskgb/Sale
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listSaleDocuments(array $query = []): array
    {
        return $this->get('/eskgb/Sale', $query)->json();
    }

    /**
     * Get a single document by UUID.
     *
     * GET /eskgb/Sale/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getSaleDocument(string $uuid): array
    {
        return $this->get("/eskgb/Sale/{$uuid}")->json();
    }

    /**
     * Get HTML of a document.
     *
     * GET /eskgb/Sale/{uuid}/Html
     */
    public function getSaleDocumentHtml(string $uuid): string
    {
        return $this->get("/eskgb/Sale/{$uuid}/Html")->getBody();
    }

    /**
     * Get PDF of a document.
     *
     * GET /eskgb/Sale/{uuid}/Pdf
     */
    public function getSaleDocumentPdf(string $uuid): string
    {
        return $this->get("/eskgb/Sale/{$uuid}/Pdf")->getBody();
    }

    /**
     * Send a document via email.
     *
     * POST /eskgb/Sale/{uuid}/Email
     *
     * @param string[] $emailAddresses
     */
    public function sendSaleDocumentByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post("/eskgb/Sale/{$uuid}/Email", ['emailAddresses' => $emailAddresses]);
    }

    /**
     * Send a document using a structured model.
     *
     * POST /eskgb/Send/Model
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function send(array $data): array
    {
        return $this->post('/eskgb/Send/Model', $data)->json();
    }

    /**
     * List draft documents.
     *
     * GET /eskgb/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/eskgb/Draft', $query)->json();
    }

    /**
     * Create a draft document.
     *
     * POST /eskgb/Draft
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/eskgb/Draft', $data)->json();
    }

    /**
     * Delete a draft document.
     *
     * DELETE /eskgb/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/eskgb/Draft/{$uuid}");
    }

    /**
     * Send a draft document.
     *
     * POST /eskgb/Draft/{uuid}/Send
     *
     * @return array<string, mixed>
     */
    public function sendDraft(string $uuid): array
    {
        return $this->post("/eskgb/Draft/{$uuid}/Send")->json();
    }
}
