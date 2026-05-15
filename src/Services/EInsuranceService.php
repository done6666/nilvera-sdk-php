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
 * Base path for sending   : /einvoice/Send
 */
class EInsuranceService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /einvoice/Send/Model
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function send(array $data): array
    {
        return $this->post('/einvoice/Send/Model', $data)->json();
    }

    /**
     * POST /einvoice/Send/Model/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewModel(array $data): array
    {
        return $this->post('/einvoice/Send/Model/Preview', $data)->json();
    }

    /**
     * POST /einvoice/Send/Xml
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendXml(array $data): array
    {
        return $this->post('/einvoice/Send/Xml', $data)->json();
    }

    /**
     * POST /einvoice/Send/Xml/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewXml(array $data): array
    {
        return $this->post('/einvoice/Send/Xml/Preview', $data)->json();
    }

    /**
     * POST /einvoice/Send/Base64String
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendBase64(array $data): array
    {
        return $this->post('/einvoice/Send/Base64String', $data)->json();
    }

    /**
     * POST /einvoice/Send/Base64String/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewBase64(array $data): array
    {
        return $this->post('/einvoice/Send/Base64String/Preview', $data)->json();
    }

    /**
     * GET /einvoice/Send/Xml/Preview
     *
     * @param array<string, mixed> $query
     * @return string
     */
    public function getPreviewedXml(array $query = []): string
    {
        return $this->get('/einvoice/Send/Xml/Preview', $query)->getBody();
    }

    /**
     * POST /einvoice/Send/Report
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendReport(array $data): array
    {
        return $this->post('/einvoice/Send/Report', $data)->json();
    }

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
     * GET /einvoice/Insurances/{uuid}/html
     */
    public function getInsuranceHtml(string $uuid): string
    {
        return $this->get("/einvoice/Insurances/{$uuid}/html")->getBody();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/pdf
     */
    public function getInsurancePdf(string $uuid): string
    {
        return $this->get("/einvoice/Insurances/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/xml
     */
    public function getInsuranceXml(string $uuid): string
    {
        return $this->get("/einvoice/Insurances/{$uuid}/xml")->getBody();
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
     * GET /einvoice/Insurances/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getInsuranceHistories(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/{$uuid}/Histories")->json();
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
     * GET /einvoice/Insurances/{messageId}/MailActivityhistories
     *
     * @return array<string, mixed>
     */
    public function getInsuranceMailHistories(string $messageId): array
    {
        return $this->get("/einvoice/Insurances/{$messageId}/MailActivityhistories")->json();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/Whatsapphistories
     *
     * @return array<string, mixed>
     */
    public function getInsuranceWhatsappHistories(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/{$uuid}/Whatsapphistories")->json();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/Smshistories
     *
     * @return array<string, mixed>
     */
    public function getInsuranceSmsHistories(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/{$uuid}/Smshistories")->json();
    }

    /**
     * GET /einvoice/Insurances/{uuid}/EmailActivities
     *
     * @return array<string, mixed>
     */
    public function getInsuranceEmailActivities(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/{$uuid}/EmailActivities")->json();
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
     * PUT /einvoice/Insurances/{uuid}/Cancel
     */
    public function cancelInsurance(string $uuid): void
    {
        $this->put("/einvoice/Insurances/{$uuid}/Cancel");
    }

    /**
     * PUT /einvoice/Insurances/{uuid}/RevertCancel
     */
    public function revertCancelInsurance(string $uuid): void
    {
        $this->put("/einvoice/Insurances/{$uuid}/RevertCancel");
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
     * PUT /einvoice/Insurances/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setInsuranceSpecialCode(array $data): void
    {
        $this->put('/einvoice/Insurances/SpecialCode', $data);
    }

    /**
     * PUT /einvoice/Insurances/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignStatusToInsurances(string $operationType, array $data = []): array
    {
        return $this->put("/einvoice/Insurances/Operation/{$operationType}", $data)->json();
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
     * POST /einvoice/Insurances/Sms/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendBySms(string $uuid, array $phoneNumbers): void
    {
        $this->post('/einvoice/Insurances/Sms/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /einvoice/Insurances/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportInsurances(string $fileType, array $data = []): array
    {
        return $this->post("/einvoice/Insurances/Export/{$fileType}", $data)->json();
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
     * GET /einvoice/Draft/{uuid}/html
     */
    public function getDraftHtml(string $uuid): string
    {
        return $this->get("/einvoice/Draft/{$uuid}/html")->getBody();
    }

    /**
     * GET /einvoice/Draft/{uuid}/pdf
     */
    public function getDraftPdf(string $uuid): string
    {
        return $this->get("/einvoice/Draft/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /einvoice/Draft/{uuid}/xml
     */
    public function getDraftXml(string $uuid): string
    {
        return $this->get("/einvoice/Draft/{$uuid}/xml")->getBody();
    }

    /**
     * GET /einvoice/Draft/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getDraftModel(string $uuid): array
    {
        return $this->get("/einvoice/Draft/{$uuid}/model")->json();
    }

    /**
     * GET /einvoice/Draft/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getDraftTags(string $uuid): array
    {
        return $this->get("/einvoice/Draft/{$uuid}/Tags")->json();
    }

    /**
     * GET /einvoice/Draft/{uuid}/Whatsapphistories
     *
     * @return array<string, mixed>
     */
    public function getDraftWhatsappHistories(string $uuid): array
    {
        return $this->get("/einvoice/Draft/{$uuid}/Whatsapphistories")->json();
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
     * POST /einvoice/Draft/EditAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function editAndSendDraft(array $data): array
    {
        return $this->post('/einvoice/Draft/EditAndSend', $data)->json();
    }

    /**
     * POST /einvoice/Draft/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportDrafts(string $fileType, array $data = []): array
    {
        return $this->post("/einvoice/Draft/Export/{$fileType}", $data)->json();
    }

    /**
     * POST /einvoice/Draft/Whatsapp/Send
     *
     * @param array<string, mixed> $data
     */
    public function sendDraftByWhatsapp(array $data): void
    {
        $this->post('/einvoice/Draft/Whatsapp/Send', $data);
    }

    /**
     * PUT /einvoice/Draft/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignStatusToDrafts(string $operationType, array $data = []): array
    {
        return $this->put("/einvoice/Draft/Operation/{$operationType}", $data)->json();
    }

    /**
     * PUT /einvoice/Draft/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToDrafts(array $data): void
    {
        $this->put('/einvoice/Draft/Tags', $data);
    }

    /**
     * PUT /einvoice/Draft/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setDraftSpecialCode(array $data): void
    {
        $this->put('/einvoice/Draft/SpecialCode', $data);
    }

    /**
     * DELETE /einvoice/Draft
     */
    public function deleteDraftsBulk(): void
    {
        $this->delete('/einvoice/Draft');
    }

    /**
     * DELETE /einvoice/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/einvoice/Draft/{$uuid}");
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Insurances/Notification
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listNotifications(array $query = []): array
    {
        return $this->get('/einvoice/Insurances/Notification', $query)->json();
    }

    /**
     * GET /einvoice/Insurances/Notification/{id}
     *
     * @return array<string, mixed>
     */
    public function getNotification(int $id): array
    {
        return $this->get("/einvoice/Insurances/Notification/{$id}")->json();
    }

    /**
     * POST /einvoice/Insurances/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createNotification(array $data): array
    {
        return $this->post('/einvoice/Insurances/Notification', $data)->json();
    }

    /**
     * PUT /einvoice/Insurances/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateNotification(array $data): array
    {
        return $this->put('/einvoice/Insurances/Notification', $data)->json();
    }

    /**
     * DELETE /einvoice/Insurances/Notification/{id}
     */
    public function deleteNotification(int $id): void
    {
        $this->delete("/einvoice/Insurances/Notification/{$id}");
    }

    // -------------------------------------------------------------------------
    // Reports
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Insurances/Report
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReports(array $query = []): array
    {
        return $this->get('/einvoice/Insurances/Report', $query)->json();
    }

    /**
     * GET /einvoice/Insurances/Report/List
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getReportList(array $query = []): array
    {
        return $this->get('/einvoice/Insurances/Report/List', $query)->json();
    }

    /**
     * GET /einvoice/Insurances/Report/{uuid}/Xml
     */
    public function getReportXml(string $uuid): string
    {
        return $this->get("/einvoice/Insurances/Report/{$uuid}/Xml")->getBody();
    }

    /**
     * GET /einvoice/Insurances/Report/{uuid}/Documents
     *
     * @return array<string, mixed>
     */
    public function listReportDocuments(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/Report/{$uuid}/Documents")->json();
    }

    /**
     * GET /einvoice/Insurances/Report/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getReportHistories(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/Report/{$uuid}/Histories")->json();
    }

    /**
     * GET /einvoice/Insurances/Report/{uuid}/GibStatus
     *
     * @return array<string, mixed>
     */
    public function queryReportGibStatus(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/Report/{$uuid}/GibStatus")->json();
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Insurances/Series
     *
     * @return array<string, mixed>
     */
    public function listSeries(): array
    {
        return $this->get('/einvoice/Insurances/Series')->json();
    }

    /**
     * GET /einvoice/Insurances/Series/{id}
     *
     * @return array<string, mixed>
     */
    public function getSeriesDetail(int $id): array
    {
        return $this->get("/einvoice/Insurances/Series/{$id}")->json();
    }

    /**
     * POST /einvoice/Insurances/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createSeries(array $data): array
    {
        return $this->post('/einvoice/Insurances/Series', $data)->json();
    }

    /**
     * PUT /einvoice/Insurances/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateSeries(array $data): array
    {
        return $this->put('/einvoice/Insurances/Series', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Insurances/Statistics
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getStatistics(array $query = []): array
    {
        return $this->get('/einvoice/Insurances/Statistics', $query)->json();
    }

    /**
     * GET /einvoice/Insurances/Statistics/Last
     *
     * @return array<string, mixed>
     */
    public function getLastStatistics(): array
    {
        return $this->get('/einvoice/Insurances/Statistics/Last')->json();
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Insurances/Tags
     *
     * @return array<string, mixed>
     */
    public function listTags(): array
    {
        return $this->get('/einvoice/Insurances/Tags')->json();
    }

    /**
     * POST /einvoice/Insurances/Tags
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createTag(array $data): array
    {
        return $this->post('/einvoice/Insurances/Tags', $data)->json();
    }

    /**
     * PUT /einvoice/Insurances/Tags
     *
     * @param array<string, mixed> $data
     */
    public function updateTags(array $data): void
    {
        $this->put('/einvoice/Insurances/Tags', $data);
    }

    /**
     * DELETE /einvoice/Insurances/Tags/{uuid}
     */
    public function deleteTag(string $uuid): void
    {
        $this->delete("/einvoice/Insurances/Tags/{$uuid}");
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Insurances/Templates
     *
     * @return array<string, mixed>
     */
    public function listTemplates(): array
    {
        return $this->get('/einvoice/Insurances/Templates')->json();
    }

    /**
     * GET /einvoice/Insurances/Templates/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getTemplateDetail(string $uuid): array
    {
        return $this->get("/einvoice/Insurances/Templates/{$uuid}")->json();
    }

    /**
     * GET /einvoice/Insurances/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/einvoice/Insurances/Templates/Preview/{$uuid}")->getBody();
    }

    /**
     * PUT /einvoice/Insurances/Templates
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateTemplate(array $data): array
    {
        return $this->put('/einvoice/Insurances/Templates', $data)->json();
    }

    /**
     * DELETE /einvoice/Insurances/Templates/{uuid}
     */
    public function deleteTemplate(string $uuid): void
    {
        $this->delete("/einvoice/Insurances/Templates/{$uuid}");
    }

    // -------------------------------------------------------------------------
    // File Upload
    // -------------------------------------------------------------------------

    /**
     * POST /einvoice/Insurances/Upload
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function uploadXml(array $data): array
    {
        return $this->post('/einvoice/Insurances/Upload', $data)->json();
    }
}
