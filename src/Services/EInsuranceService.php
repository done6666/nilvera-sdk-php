<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-SKGB (Sigorta Komisyon Gider Belgesi — Insurance Commission Expense Document) API.
 *
 * NOTE: This service lives under the /einvoice namespace.
 * Insurance documents are accessed via /einvoice/Insurances/ paths,
 * and their drafts share /einvoice/Draft/ with e-invoice drafts.
 *
 * Base path for documents : /einvoice/Insurances
 * Base path for drafts    : /einvoice/Draft
 */
class EInsuranceService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Insurance Commission Expense Documents
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Insurances
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listInsurances(array $query = []): array
    {
        return $this->get('/einvoice/Insurances', $query)->json();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/Details
     *
     * @return array<string, mixed>
     */
    public function getInsuranceDetails(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/{$uuid}/Details")->json();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/pdf
     */
    public function getInsurancePdf(string $uuid): string
    {
        return $this->get("/einvoice/Insurances/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getInsuranceStatus(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/{$uuid}/Status")->json();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getInsuranceTags(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/{$uuid}/Tags")->json();
    }

    /**
     * PUT /einvoice/Insurances/{uuid}/Cancel
     */
    public function cancelInsurance(string $uuid): void
    {
        $this->put("/einvoice/Insurances/{$uuid}/Cancel");
    }

    /**
     * PUT /einvoice/Insurances/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToInsurances(array $data): void
    {
        $this->put('/einvoice/Insurances/Tags', $data);
    }

    /**
     * POST /einvoice/Insurances/Email/Send
     *
     * @param string[] $emailAddresses
     */
    public function sendByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post('/einvoice/Insurances/Email/Send', [
            'UUID'           => $uuid,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    /**
     * POST /einvoice/Insurances/Whatsapp/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendByWhatsapp(string $uuid, array $phoneNumbers): void
    {
        $this->post('/einvoice/Insurances/Whatsapp/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /einvoice/Insurances/{uuid}/CreateDraft
     *
     * @return array<string, mixed>
     */
    public function createDraftFromInsurance(string $uuid): array
    {
        return $this->post("/einvoice/Insurances/{$uuid}/CreateDraft")->json();
    }

    // -------------------------------------------------------------------------
    // Draft Documents (shared /einvoice/Draft namespace)
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/einvoice/Draft', $query)->json();
    }

    /**
     * POST /einvoice/Draft/Create
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/einvoice/Draft/Create', $data)->json();
    }

    /**
     * POST /einvoice/Draft/CreateBulk
     *
     * @param array<string, mixed> $data
     * @return array<string[]>
     */
    public function createDraftsBulk(array $data): array
    {
        return $this->post('/einvoice/Draft/CreateBulk', $data)->json();
    }

    /**
     * POST /einvoice/Draft/ConfirmAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function confirmAndSendDraft(array $data): array
    {
        return $this->post('/einvoice/Draft/ConfirmAndSend', $data)->json();
    }

    /**
     * DELETE /einvoice/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/einvoice/Draft/{$uuid}");
    }
}
