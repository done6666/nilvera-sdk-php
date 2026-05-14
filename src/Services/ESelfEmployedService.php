<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-SMM (Serbest Meslek Makbuzu — Self-Employed Professional Receipt) API.
 * Base path: /evoucher
 *
 * Covers: sending, vouchers (receipts), drafts, series,
 * templates, tags, notification settings, statistics, and file upload.
 */
class ESelfEmployedService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /evoucher/Send/Xml
     *
     * @return array<string, mixed>
     */
    public function sendXml(string $xmlContent): array
    {
        return $this->post('/evoucher/Send/Xml', ['XmlContent' => $xmlContent])->json();
    }

    /**
     * POST /evoucher/Send/Xml/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewXml(array $data): array
    {
        return $this->post('/evoucher/Send/Xml/Preview', $data)->json();
    }

    /**
     * POST /evoucher/Send/Base64String/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewBase64(array $data): array
    {
        return $this->post('/evoucher/Send/Base64String/Preview', $data)->json();
    }

    /**
     * POST /evoucher/Upload — upload a UBL XML file.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function upload(array $data): array
    {
        return $this->post('/evoucher/Upload', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Vouchers (Receipts)
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Vouchers
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listVouchers(array $query = []): array
    {
        return $this->get('/evoucher/Vouchers', $query)->json();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/Details
     *
     * @return array<string, mixed>
     */
    public function getVoucherDetails(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Details")->json();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/html
     */
    public function getVoucherHtml(string $uuid): string
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/html")->getBody();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/pdf
     */
    public function getVoucherPdf(string $uuid): string
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/xml
     */
    public function getVoucherXml(string $uuid): string
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/xml")->getBody();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getVoucherStatus(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Status")->json();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getVoucherHistories(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Histories")->json();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/Taxes
     *
     * @return array<string, mixed>
     */
    public function getVoucherTaxes(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Taxes")->json();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getVoucherTags(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Tags")->json();
    }

    /**
     * PUT /evoucher/Vouchers/{uuid}/Cancel
     */
    public function cancelVoucher(string $uuid): void
    {
        $this->put("/evoucher/Vouchers/{$uuid}/Cancel");
    }

    /**
     * PUT /evoucher/Vouchers/{uuid}/RevertCancel
     */
    public function revertCancelVoucher(string $uuid): void
    {
        $this->put("/evoucher/Vouchers/{$uuid}/RevertCancel");
    }

    /**
     * PUT /evoucher/Vouchers/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToVouchers(array $data): void
    {
        $this->put('/evoucher/Vouchers/Tags', $data);
    }

    /**
     * PUT /evoucher/Vouchers/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setVoucherSpecialCode(array $data): void
    {
        $this->put('/evoucher/Vouchers/SpecialCode', $data);
    }

    /**
     * POST /evoucher/Vouchers/Email/Send
     *
     * @param string[] $emailAddresses
     */
    public function sendVoucherByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post('/evoucher/Vouchers/Email/Send', [
            'UUID'           => $uuid,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    /**
     * POST /evoucher/Vouchers/Sms/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendVoucherBySms(string $uuid, array $phoneNumbers): void
    {
        $this->post('/evoucher/Vouchers/Sms/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /evoucher/Vouchers/Whatsapp/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendVoucherByWhatsapp(string $uuid, array $phoneNumbers): void
    {
        $this->post('/evoucher/Vouchers/Whatsapp/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /evoucher/Vouchers/{uuid}/CreateDraft
     *
     * @return array<string, mixed>
     */
    public function createDraftFromVoucher(string $uuid): array
    {
        return $this->post("/evoucher/Vouchers/{$uuid}/CreateDraft")->json();
    }

    // -------------------------------------------------------------------------
    // Draft Receipts
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/evoucher/Draft', $query)->json();
    }

    /**
     * GET /evoucher/Draft/{uuid}/html
     */
    public function getDraftHtml(string $uuid): string
    {
        return $this->get("/evoucher/Draft/{$uuid}/html")->getBody();
    }

    /**
     * GET /evoucher/Draft/{uuid}/pdf
     */
    public function getDraftPdf(string $uuid): string
    {
        return $this->get("/evoucher/Draft/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /evoucher/Draft/{uuid}/xml
     */
    public function getDraftXml(string $uuid): string
    {
        return $this->get("/evoucher/Draft/{$uuid}/xml")->getBody();
    }

    /**
     * GET /evoucher/Draft/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getDraftModel(string $uuid): array
    {
        return $this->get("/evoucher/Draft/{$uuid}/model")->json();
    }

    /**
     * GET /evoucher/Draft/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getDraftTags(string $uuid): array
    {
        return $this->get("/evoucher/Draft/{$uuid}/Tags")->json();
    }

    /**
     * POST /evoucher/Draft/Create
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/evoucher/Draft/Create', $data)->json();
    }

    /**
     * DELETE /evoucher/Draft — bulk delete.
     */
    public function deleteDraftsBulk(): void
    {
        $this->delete('/evoucher/Draft');
    }

    /**
     * DELETE /evoucher/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/evoucher/Draft/{$uuid}");
    }

    /**
     * POST /evoucher/Draft/EditAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function editAndSendDraft(array $data): array
    {
        return $this->post('/evoucher/Draft/EditAndSend', $data)->json();
    }

    /**
     * PUT /evoucher/Draft/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToDrafts(array $data): void
    {
        $this->put('/evoucher/Draft/Tags', $data);
    }

    /**
     * PUT /evoucher/Draft/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setDraftSpecialCode(array $data): void
    {
        $this->put('/evoucher/Draft/SpecialCode', $data);
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Series
     *
     * @return array<string, mixed>
     */
    public function listSeries(): array
    {
        return $this->get('/evoucher/Series')->json();
    }

    /**
     * POST /evoucher/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createSeries(array $data): array
    {
        return $this->post('/evoucher/Series', $data)->json();
    }

    /**
     * PUT /evoucher/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateSeries(array $data): array
    {
        return $this->put('/evoucher/Series', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Templates
     *
     * @return array<string, mixed>
     */
    public function listTemplates(): array
    {
        return $this->get('/evoucher/Templates')->json();
    }

    /**
     * GET /evoucher/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/evoucher/Templates/Preview/{$uuid}")->getBody();
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Tags
     *
     * @return array<string, mixed>
     */
    public function listTags(): array
    {
        return $this->get('/evoucher/Tags')->json();
    }

    /**
     * PUT /evoucher/Tags
     *
     * @param array<string, mixed> $data
     */
    public function updateTags(array $data): void
    {
        $this->put('/evoucher/Tags', $data);
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Notification
     *
     * @return array<string, mixed>
     */
    public function listNotifications(): array
    {
        return $this->get('/evoucher/Notification')->json();
    }

    /**
     * GET /evoucher/Notification/{id}
     *
     * @return array<string, mixed>
     */
    public function getNotification(int $id): array
    {
        return $this->get("/evoucher/Notification/{$id}")->json();
    }

    /**
     * POST /evoucher/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createNotification(array $data): array
    {
        return $this->post('/evoucher/Notification', $data)->json();
    }

    /**
     * PUT /evoucher/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateNotification(array $data): array
    {
        return $this->put('/evoucher/Notification', $data)->json();
    }

    /**
     * DELETE /evoucher/Notification/{id}
     */
    public function deleteNotification(int $id): void
    {
        $this->delete("/evoucher/Notification/{$id}");
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Statistics
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getStatistics(array $query = []): array
    {
        return $this->get('/evoucher/Statistics', $query)->json();
    }

    /**
     * GET /evoucher/Statistics/Last
     *
     * @return array<string, mixed>
     */
    public function getLastStatistics(): array
    {
        return $this->get('/evoucher/Statistics/Last')->json();
    }
}
