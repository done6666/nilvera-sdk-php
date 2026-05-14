<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Waybill (e-İrsaliye) API.
 * Base path: /ewaybill
 */
class EWaybillService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Outgoing Waybills
    // -------------------------------------------------------------------------

    /**
     * List outgoing waybills.
     *
     * GET /ewaybill/Sale
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listSaleWaybills(array $query = []): array
    {
        return $this->get('/ewaybill/Sale', $query)->json();
    }

    /**
     * Get a single outgoing waybill by UUID.
     *
     * GET /ewaybill/Sale/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getSaleWaybill(string $uuid): array
    {
        return $this->get("/ewaybill/Sale/{$uuid}")->json();
    }

    /**
     * Get HTML of an outgoing waybill.
     *
     * GET /ewaybill/Sale/{uuid}/Html
     */
    public function getSaleWaybillHtml(string $uuid): string
    {
        return $this->get("/ewaybill/Sale/{$uuid}/Html")->getBody();
    }

    /**
     * Get PDF of an outgoing waybill.
     *
     * GET /ewaybill/Sale/{uuid}/Pdf
     */
    public function getSaleWaybillPdf(string $uuid): string
    {
        return $this->get("/ewaybill/Sale/{$uuid}/Pdf")->getBody();
    }

    /**
     * Send an outgoing waybill via email.
     *
     * POST /ewaybill/Sale/{uuid}/Email
     *
     * @param string[] $emailAddresses
     */
    public function sendSaleWaybillByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post("/ewaybill/Sale/{$uuid}/Email", ['emailAddresses' => $emailAddresses]);
    }

    // -------------------------------------------------------------------------
    // Incoming Waybills
    // -------------------------------------------------------------------------

    /**
     * List incoming waybills.
     *
     * GET /ewaybill/Purchase
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listPurchaseWaybills(array $query = []): array
    {
        return $this->get('/ewaybill/Purchase', $query)->json();
    }

    /**
     * Get a single incoming waybill by UUID.
     *
     * GET /ewaybill/Purchase/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getPurchaseWaybill(string $uuid): array
    {
        return $this->get("/ewaybill/Purchase/{$uuid}")->json();
    }

    /**
     * Accept an incoming waybill.
     *
     * POST /ewaybill/Purchase/{uuid}/Accept
     *
     * @return array<string, mixed>
     */
    public function acceptPurchaseWaybill(string $uuid): array
    {
        return $this->post("/ewaybill/Purchase/{$uuid}/Accept")->json();
    }

    /**
     * Reject an incoming waybill.
     *
     * POST /ewaybill/Purchase/{uuid}/Reject
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function rejectPurchaseWaybill(string $uuid, array $data = []): array
    {
        return $this->post("/ewaybill/Purchase/{$uuid}/Reject", $data)->json();
    }

    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * Send a waybill using a structured model.
     *
     * POST /ewaybill/Send/Model
     *
     * @param array<string, mixed> $waybill
     * @return array<string, mixed>
     */
    public function send(array $waybill): array
    {
        return $this->post('/ewaybill/Send/Model', $waybill)->json();
    }

    // -------------------------------------------------------------------------
    // Draft Waybills
    // -------------------------------------------------------------------------

    /**
     * List draft waybills.
     *
     * GET /ewaybill/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/ewaybill/Draft', $query)->json();
    }

    /**
     * Get a draft waybill by UUID.
     *
     * GET /ewaybill/Draft/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getDraft(string $uuid): array
    {
        return $this->get("/ewaybill/Draft/{$uuid}")->json();
    }

    /**
     * Create a draft waybill.
     *
     * POST /ewaybill/Draft
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/ewaybill/Draft', $data)->json();
    }

    /**
     * Update a draft waybill.
     *
     * PUT /ewaybill/Draft/{uuid}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateDraft(string $uuid, array $data): array
    {
        return $this->put("/ewaybill/Draft/{$uuid}", $data)->json();
    }

    /**
     * Delete a draft waybill.
     *
     * DELETE /ewaybill/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/ewaybill/Draft/{$uuid}");
    }

    /**
     * Send a draft waybill.
     *
     * POST /ewaybill/Draft/{uuid}/Send
     *
     * @return array<string, mixed>
     */
    public function sendDraft(string $uuid): array
    {
        return $this->post("/ewaybill/Draft/{$uuid}/Send")->json();
    }
}
