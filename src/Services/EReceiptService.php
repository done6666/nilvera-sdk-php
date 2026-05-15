<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Adisyon (Electronic Bill/Receipt) API.
 * Base path: /ebill
 *
 * Covers: sending, bills, old bills, drafts, reports, series, templates,
 * tags, notification settings, statistics, and file upload.
 */
class EReceiptService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /ebill/Send/Model
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function send(array $data): array
    {
        return $this->post('/ebill/Send/Model', $data)->json();
    }

    /**
     * POST /ebill/Send/Model/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewModel(array $data): array
    {
        return $this->post('/ebill/Send/Model/Preview', $data)->json();
    }

    /**
     * POST /ebill/Send/Xml
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendXml(array $data): array
    {
        return $this->post('/ebill/Send/Xml', $data)->json();
    }

    /**
     * POST /ebill/Send/Xml/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewXml(array $data): array
    {
        return $this->post('/ebill/Send/Xml/Preview', $data)->json();
    }

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
     * POST /ebill/Send/Base64String/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewBase64(array $data): array
    {
        return $this->post('/ebill/Send/Base64String/Preview', $data)->json();
    }

    /**
     * GET /ebill/Send/Xml/Preview
     *
     * @param array<string, mixed> $query
     * @return string
     */
    public function getPreviewedXml(array $query = []): string
    {
        return $this->get('/ebill/Send/Xml/Preview', $query)->getBody();
    }

    /**
     * POST /ebill/Send/Report
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendReport(array $data): array
    {
        return $this->post('/ebill/Send/Report', $data)->json();
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
     * GET /ebill/Bills/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getBillTags(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/Tags")->json();
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
     * GET /ebill/Bills/{uuid}/Details
     *
     * @return array<string, mixed>
     */
    public function getBillDetails(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/Details")->json();
    }

    /**
     * GET /ebill/Bills/{messageId}/MailActivityhistories
     *
     * @return array<string, mixed>
     */
    public function getBillMailHistories(string $messageId): array
    {
        return $this->get("/ebill/Bills/{$messageId}/MailActivityhistories")->json();
    }

    /**
     * GET /ebill/Bills/{uuid}/Whatsapphistories
     *
     * @return array<string, mixed>
     */
    public function getBillWhatsappHistories(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/Whatsapphistories")->json();
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
     * GET /ebill/Bills/{uuid}/EmailActivities
     *
     * @return array<string, mixed>
     */
    public function getBillEmailActivities(string $uuid): array
    {
        return $this->get("/ebill/Bills/{$uuid}/EmailActivities")->json();
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
     * PUT /ebill/Bills/{uuid}/Cancel
     */
    public function cancelBill(string $uuid): void
    {
        $this->put("/ebill/Bills/{$uuid}/Cancel");
    }

    /**
     * PUT /ebill/Bills/{uuid}/RevertCancel
     */
    public function revertCancelBill(string $uuid): void
    {
        $this->put("/ebill/Bills/{$uuid}/RevertCancel");
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
     * PUT /ebill/Bills/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignStatusToBills(string $operationType, array $data = []): array
    {
        return $this->put("/ebill/Bills/Operation/{$operationType}", $data)->json();
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
     * POST /ebill/Bills/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportBills(string $fileType, array $data = []): array
    {
        return $this->post("/ebill/Bills/Export/{$fileType}", $data)->json();
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
     * GET /ebill/Draft/{uuid}/xml
     */
    public function getDraftXml(string $uuid): string
    {
        return $this->get("/ebill/Draft/{$uuid}/xml")->getBody();
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
     * GET /ebill/Draft/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getDraftTags(string $uuid): array
    {
        return $this->get("/ebill/Draft/{$uuid}/Tags")->json();
    }

    /**
     * GET /ebill/Draft/{uuid}/Whatsapphistories
     *
     * @return array<string, mixed>
     */
    public function getDraftWhatsappHistories(string $uuid): array
    {
        return $this->get("/ebill/Draft/{$uuid}/Whatsapphistories")->json();
    }

    /**
     * POST /ebill/Draft/Create
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/ebill/Draft/Create', $data)->json();
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
     * POST /ebill/Draft/ConfirmAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function confirmAndSendDraft(array $data): array
    {
        return $this->post('/ebill/Draft/ConfirmAndSend', $data)->json();
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
     * POST /ebill/Draft/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportDrafts(string $fileType, array $data = []): array
    {
        return $this->post("/ebill/Draft/Export/{$fileType}", $data)->json();
    }

    /**
     * POST /ebill/Draft/Whatsapp/Send
     *
     * @param array<string, mixed> $data
     */
    public function sendDraftByWhatsapp(array $data): void
    {
        $this->post('/ebill/Draft/Whatsapp/Send', $data);
    }

    /**
     * PUT /ebill/Draft/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignStatusToDrafts(string $operationType, array $data = []): array
    {
        return $this->put("/ebill/Draft/Operation/{$operationType}", $data)->json();
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

    /**
     * GET /ebill/Report/List
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getReportList(array $query = []): array
    {
        return $this->get('/ebill/Report/List', $query)->json();
    }

    /**
     * GET /ebill/Report/{uuid}/Xml
     */
    public function getReportXml(string $uuid): string
    {
        return $this->get("/ebill/Report/{$uuid}/Xml")->getBody();
    }

    /**
     * GET /ebill/Report/{uuid}/Documents
     *
     * @return array<string, mixed>
     */
    public function listReportDocuments(string $uuid): array
    {
        return $this->get("/ebill/Report/{$uuid}/Documents")->json();
    }

    /**
     * GET /ebill/Report/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getReportHistories(string $uuid): array
    {
        return $this->get("/ebill/Report/{$uuid}/Histories")->json();
    }

    /**
     * GET /ebill/Report/{uuid}/GibStatus
     *
     * @return array<string, mixed>
     */
    public function queryReportGibStatus(string $uuid): array
    {
        return $this->get("/ebill/Report/{$uuid}/GibStatus")->json();
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

    /**
     * GET /ebill/Series/{id}
     *
     * @return array<string, mixed>
     */
    public function getSeriesDetail(int $id): array
    {
        return $this->get("/ebill/Series/{$id}")->json();
    }

    /**
     * POST /ebill/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createSeries(array $data): array
    {
        return $this->post('/ebill/Series', $data)->json();
    }

    /**
     * PUT /ebill/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateSeries(array $data): array
    {
        return $this->put('/ebill/Series', $data)->json();
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
     * GET /ebill/Templates/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getTemplateDetail(string $uuid): array
    {
        return $this->get("/ebill/Templates/{$uuid}")->json();
    }

    /**
     * GET /ebill/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/ebill/Templates/Preview/{$uuid}")->getBody();
    }

    /**
     * PUT /ebill/Templates
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateTemplate(array $data): array
    {
        return $this->put('/ebill/Templates', $data)->json();
    }

    /**
     * DELETE /ebill/Templates/{uuid}
     */
    public function deleteTemplate(string $uuid): void
    {
        $this->delete("/ebill/Templates/{$uuid}");
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
     * POST /ebill/Tags
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createTag(array $data): array
    {
        return $this->post('/ebill/Tags', $data)->json();
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

    /**
     * DELETE /ebill/Tags/{uuid}
     */
    public function deleteTag(string $uuid): void
    {
        $this->delete("/ebill/Tags/{$uuid}");
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /ebill/Notification
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listNotifications(array $query = []): array
    {
        return $this->get('/ebill/Notification', $query)->json();
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
