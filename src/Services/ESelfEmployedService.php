<?php

declare(strict_types=1);

namespace Nilvera\Services;

use Nilvera\Requests\SendVoucherRequest;
use Nilvera\Responses\SendDocumentResponse;

/**
 * E-SMM (Serbest Meslek Makbuzu — Self-Employed Professional Receipt) API.
 * Base path: /evoucher
 *
 * Covers: sending, vouchers (receipts), drafts, reports, series,
 * templates, tags, notification settings, statistics, and file upload.
 */
class ESelfEmployedService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /evoucher/Send/Model
     */
    public function send(SendVoucherRequest $voucher): SendDocumentResponse
    {
        return SendDocumentResponse::fromArray(
            $this->post('/evoucher/Send/Model', $voucher->toArray())->json()
        );
    }

    /**
     * POST /evoucher/Send/Model/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewModel(array $data): array
    {
        return $this->post('/evoucher/Send/Model/Preview', $data)->json();
    }

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
     * POST /evoucher/Send/Base64String
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendBase64(array $data): array
    {
        return $this->post('/evoucher/Send/Base64String', $data)->json();
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
     * GET /evoucher/Send/Xml/Preview
     *
     * @param array<string, mixed> $query
     * @return string
     */
    public function getPreviewedXml(array $query = []): string
    {
        return $this->get('/evoucher/Send/Xml/Preview', $query)->getBody();
    }

    /**
     * POST /evoucher/Send/Report
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendReport(array $data): array
    {
        return $this->post('/evoucher/Send/Report', $data)->json();
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
     * GET /evoucher/Vouchers/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getVoucherTags(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Tags")->json();
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
     * GET /evoucher/Vouchers/{uuid}/Details
     *
     * @return array<string, mixed>
     */
    public function getVoucherDetails(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Details")->json();
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
     * GET /evoucher/Vouchers/{messageId}/MailActivityhistories
     *
     * @return array<string, mixed>
     */
    public function getVoucherMailHistories(string $messageId): array
    {
        return $this->get("/evoucher/Vouchers/{$messageId}/MailActivityhistories")->json();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/Whatsapphistories
     *
     * @return array<string, mixed>
     */
    public function getVoucherWhatsappHistories(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Whatsapphistories")->json();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/Smshistories
     *
     * @return array<string, mixed>
     */
    public function getVoucherSmsHistories(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/Smshistories")->json();
    }

    /**
     * GET /evoucher/Vouchers/{uuid}/EmailActivities
     *
     * @return array<string, mixed>
     */
    public function getVoucherEmailActivities(string $uuid): array
    {
        return $this->get("/evoucher/Vouchers/{$uuid}/EmailActivities")->json();
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
     * PUT /evoucher/Vouchers/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignStatusToVouchers(string $operationType, array $data = []): array
    {
        return $this->put("/evoucher/Vouchers/Operation/{$operationType}", $data)->json();
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
     * POST /evoucher/Vouchers/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportVouchers(string $fileType, array $data = []): array
    {
        return $this->post("/evoucher/Vouchers/Export/{$fileType}", $data)->json();
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
     * GET /evoucher/Draft/{uuid}/Whatsapphistories
     *
     * @return array<string, mixed>
     */
    public function getDraftWhatsappHistories(string $uuid): array
    {
        return $this->get("/evoucher/Draft/{$uuid}/Whatsapphistories")->json();
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
     * POST /evoucher/Draft/CreateBulk
     *
     * @param array<string, mixed> $data
     * @return array<string[]>
     */
    public function createDraftsBulk(array $data): array
    {
        return $this->post('/evoucher/Draft/CreateBulk', $data)->json();
    }

    /**
     * POST /evoucher/Draft/ConfirmAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function confirmAndSendDraft(array $data): array
    {
        return $this->post('/evoucher/Draft/ConfirmAndSend', $data)->json();
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
     * POST /evoucher/Draft/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportDrafts(string $fileType, array $data = []): array
    {
        return $this->post("/evoucher/Draft/Export/{$fileType}", $data)->json();
    }

    /**
     * POST /evoucher/Draft/Whatsapp/Send
     *
     * @param array<string, mixed> $data
     */
    public function sendDraftByWhatsapp(array $data): void
    {
        $this->post('/evoucher/Draft/Whatsapp/Send', $data);
    }

    /**
     * PUT /evoucher/Draft/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignStatusToDrafts(string $operationType, array $data = []): array
    {
        return $this->put("/evoucher/Draft/Operation/{$operationType}", $data)->json();
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

    // -------------------------------------------------------------------------
    // Reports
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Report
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReports(array $query = []): array
    {
        return $this->get('/evoucher/Report', $query)->json();
    }

    /**
     * GET /evoucher/Report/List
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getReportList(array $query = []): array
    {
        return $this->get('/evoucher/Report/List', $query)->json();
    }

    /**
     * GET /evoucher/Report/{uuid}/Xml
     */
    public function getReportXml(string $uuid): string
    {
        return $this->get("/evoucher/Report/{$uuid}/Xml")->getBody();
    }

    /**
     * GET /evoucher/Report/{uuid}/Documents
     *
     * @return array<string, mixed>
     */
    public function listReportDocuments(string $uuid): array
    {
        return $this->get("/evoucher/Report/{$uuid}/Documents")->json();
    }

    /**
     * GET /evoucher/Report/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getReportHistories(string $uuid): array
    {
        return $this->get("/evoucher/Report/{$uuid}/Histories")->json();
    }

    /**
     * GET /evoucher/Report/{uuid}/GibStatus
     *
     * @return array<string, mixed>
     */
    public function queryReportGibStatus(string $uuid): array
    {
        return $this->get("/evoucher/Report/{$uuid}/GibStatus")->json();
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
     * GET /evoucher/Series/{id}
     *
     * @return array<string, mixed>
     */
    public function getSeriesDetail(int $id): array
    {
        return $this->get("/evoucher/Series/{$id}")->json();
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
     * GET /evoucher/Templates/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getTemplateDetail(string $uuid): array
    {
        return $this->get("/evoucher/Templates/{$uuid}")->json();
    }

    /**
     * GET /evoucher/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/evoucher/Templates/Preview/{$uuid}")->getBody();
    }

    /**
     * PUT /evoucher/Templates
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateTemplate(array $data): array
    {
        return $this->put('/evoucher/Templates', $data)->json();
    }

    /**
     * DELETE /evoucher/Templates/{uuid}
     */
    public function deleteTemplate(string $uuid): void
    {
        $this->delete("/evoucher/Templates/{$uuid}");
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
     * POST /evoucher/Tags
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createTag(array $data): array
    {
        return $this->post('/evoucher/Tags', $data)->json();
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

    /**
     * DELETE /evoucher/Tags/{uuid}
     */
    public function deleteTag(string $uuid): void
    {
        $this->delete("/evoucher/Tags/{$uuid}");
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /evoucher/Notification
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listNotifications(array $query = []): array
    {
        return $this->get('/evoucher/Notification', $query)->json();
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
