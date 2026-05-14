<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Adisyon (Electronic Bill/Receipt) API.
 * Base path: /ebill
 *
 * Covers: sending, bills, drafts, series, templates,
 * tags, notification settings, statistics, and file upload.
 */
class EReceiptService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /ebill/Send/Base64String
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendBase64(array $data): array
    {
        return $this->post('/ebill/Send/Base64String', $data)->json();
    }

    /**
     * POST /ebill/Upload — upload a file.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function upload(array $data): array
    {
        return $this->post('/ebill/Upload', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Bills
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Bills
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listBills(array $query = []): array
    {
        return $this->get('/ebill/Bills', $query)->json();
    }

    /**
     * GET /ebill/Bills/{uuid}/Details
     *
     * @return array<string, mixed>
     */
    public function getBillDetails(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/Details")->json();
    }

    /**
     * GET /ebill/Bills/{uuid}/html
     */
    public function getBillHtml(string $uuid): string
    {
        return $this->get("/ebill/Bills/{$uuid}/html")->getBody();
    }

    /**
     * GET /ebill/Bills/{uuid}/pdf
     */
    public function getBillPdf(string $uuid): string
    {
        return $this->get("/ebill/Bills/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /ebill/Bills/{uuid}/xml
     */
    public function getBillXml(string $uuid): string
    {
        return $this->get("/ebill/Bills/{$uuid}/xml")->getBody();
    }

    /**
     * GET /ebill/Bills/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getBillStatus(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/Status")->json();
    }

    /**
     * GET /ebill/Bills/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getBillHistories(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/Histories")->json();
    }

    /**
     * GET /ebill/Bills/{uuid}/Smshistories
     *
     * @return array<string, mixed>
     */
    public function getBillSmsHistories(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/Smshistories")->json();
    }

    /**
     * GET /ebill/Bills/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getBillTags(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/Tags")->json();
    }

    /**
     * PUT /ebill/Bills/{uuid}/Cancel
     */
    public function cancelBill(string $uuid): void
    {
        $this->put("/ebill/Bills/{$uuid}/Cancel");
    }

    /**
     * PUT /ebill/Bills/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToBills(array $data): void
    {
        $this->put('/ebill/Bills/Tags', $data);
    }

    /**
     * PUT /ebill/Bills/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setBillSpecialCode(array $data): void
    {
        $this->put('/ebill/Bills/SpecialCode', $data);
    }

    /**
     * POST /ebill/Bills/Email/Send
     *
     * @param string[] $emailAddresses
     */
    public function sendBillByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post('/ebill/Bills/Email/Send', [
            'UUID'           => $uuid,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    /**
     * POST /ebill/Bills/Sms/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendBillBySms(string $uuid, array $phoneNumbers): void
    {
        $this->post('/ebill/Bills/Sms/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /ebill/Bills/Whatsapp/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendBillByWhatsapp(string $uuid, array $phoneNumbers): void
    {
        $this->post('/ebill/Bills/Whatsapp/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /ebill/Bills/{uuid}/CreateDraft
     *
     * @return array<string, mixed>
     */
    public function createDraftFromBill(string $uuid): array
    {
        return $this->post("/ebill/Bills/{$uuid}/CreateDraft")->json();
    }

    // -------------------------------------------------------------------------
    // Draft Bills
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/ebill/Draft', $query)->json();
    }

    /**
     * GET /ebill/Draft/{uuid}/html
     */
    public function getDraftHtml(string $uuid): string
    {
        return $this->get("/ebill/Draft/{$uuid}/html")->getBody();
    }

    /**
     * GET /ebill/Draft/{uuid}/pdf
     */
    public function getDraftPdf(string $uuid): string
    {
        return $this->get("/ebill/Draft/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /ebill/Draft/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getDraftModel(string $uuid): array
    {
        return $this->get("/ebill/Draft/{$uuid}/model")->json();
    }

    /**
     * POST /ebill/Draft/CreateBulk
     *
     * @param array<string, mixed> $data
     * @return array<string[]>
     */
    public function createDraftsBulk(array $data): array
    {
        return $this->post('/ebill/Draft/CreateBulk', $data)->json();
    }

    /**
     * DELETE /ebill/Draft — bulk delete drafts.
     */
    public function deleteDraftsBulk(): void
    {
        $this->delete('/ebill/Draft');
    }

    /**
     * DELETE /ebill/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/ebill/Draft/{$uuid}");
    }

    /**
     * POST /ebill/Draft/EditAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function editAndSendDraft(array $data): array
    {
        return $this->post('/ebill/Draft/EditAndSend', $data)->json();
    }

    /**
     * PUT /ebill/Draft/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToDrafts(array $data): void
    {
        $this->put('/ebill/Draft/Tags', $data);
    }

    /**
     * PUT /ebill/Draft/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setDraftSpecialCode(array $data): void
    {
        $this->put('/ebill/Draft/SpecialCode', $data);
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Series
     *
     * @return array<string, mixed>
     */
    public function listSeries(): array
    {
        return $this->get('/ebill/Series')->json();
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Templates
     *
     * @return array<string, mixed>
     */
    public function listTemplates(): array
    {
        return $this->get('/ebill/Templates')->json();
    }

    /**
     * GET /ebill/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/ebill/Templates/Preview/{$uuid}")->getBody();
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Tags
     *
     * @return array<string, mixed>
     */
    public function listTags(): array
    {
        return $this->get('/ebill/Tags')->json();
    }

    /**
     * PUT /ebill/Tags
     *
     * @param array<string, mixed> $data
     */
    public function updateTags(array $data): void
    {
        $this->put('/ebill/Tags', $data);
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Notification
     *
     * @return array<string, mixed>
     */
    public function listNotifications(): array
    {
        return $this->get('/ebill/Notification')->json();
    }

    /**
     * GET /ebill/Notification/{id}
     *
     * @return array<string, mixed>
     */
    public function getNotification(int $id): array
    {
        return $this->get("/ebill/Notification/{$id}")->json();
    }

    /**
     * POST /ebill/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createNotification(array $data): array
    {
        return $this->post('/ebill/Notification', $data)->json();
    }

    /**
     * PUT /ebill/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateNotification(array $data): array
    {
        return $this->put('/ebill/Notification', $data)->json();
    }

    /**
     * DELETE /ebill/Notification/{id}
     */
    public function deleteNotification(int $id): void
    {
        $this->delete("/ebill/Notification/{$id}");
    }

    // -------------------------------------------------------------------------
    // Reports
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Report
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReports(array $query = []): array
    {
        return $this->get('/ebill/Report', $query)->json();
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Statistics
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getStatistics(array $query = []): array
    {
        return $this->get('/ebill/Statistics', $query)->json();
    }

    /**
     * GET /ebill/Statistics/Last
     *
     * @return array<string, mixed>
     */
    public function getLastStatistics(): array
    {
        return $this->get('/ebill/Statistics/Last')->json();
    }
}
