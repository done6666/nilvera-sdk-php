<?php

declare(strict_types=1);

namespace Nilvera\Services;

use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\SendArchiveInvoiceRequest;
use Nilvera\Requests\SendByEmailRequest;
use Nilvera\Requests\SendBySmsRequest;
use Nilvera\Responses\SendDocumentResponse;

/**
 * E-Archive Invoice (e-Arşiv) API — base path: /earchive
 *
 * Covers: sending, invoices, incoming GIB invoices, drafts, reports,
 * old invoices, series, templates, tags, notification settings, statistics,
 * and file upload.
 */
class EArchiveService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /earchive/Send/Model
     */
    public function send(SendArchiveInvoiceRequest $invoice): SendDocumentResponse
    {
        return SendDocumentResponse::fromArray(
            $this->post('/earchive/Send/Model', $invoice->toArray())->json()
        );
    }

    /**
     * POST /earchive/Send/Xml
     *
     * @return array<string, mixed>
     */
    public function sendXml(string $xmlContent): array
    {
        return $this->post('/earchive/Send/Xml', ['XmlContent' => $xmlContent])->json();
    }

    /**
     * POST /earchive/Send/Report — submit e-archive report to GIB.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendReport(array $data = []): array
    {
        return $this->post('/earchive/Send/Report', $data)->json();
    }

    /**
     * POST /earchive/Upload — upload a UBL XML file.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function upload(array $data): array
    {
        return $this->post('/earchive/Upload', $data)->json();
    }

    // -------------------------------------------------------------------------
    // E-Archive Invoices
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Invoices
     *
     * @return array<string, mixed>
     */
    public function listInvoices(ListInvoicesRequest $query = new ListInvoicesRequest()): array
    {
        return $this->get('/earchive/Invoices', $query->toArray())->json();
    }

    /**
     * POST /earchive/Invoices — convert an order to an e-archive invoice.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function convertOrderToInvoice(array $data): array
    {
        return $this->post('/earchive/Invoices', $data)->json();
    }

    /**
     * GET /earchive/Invoices/{uuid}/html
     */
    public function getInvoiceHtml(string $uuid): string
    {
        return $this->get("/earchive/Invoices/{$uuid}/html")->getBody();
    }

    /**
     * GET /earchive/Invoices/{uuid}/pdf
     */
    public function getInvoicePdf(string $uuid): string
    {
        return $this->get("/earchive/Invoices/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /earchive/Invoices/{uuid}/xml
     */
    public function getInvoiceXml(string $uuid): string
    {
        return $this->get("/earchive/Invoices/{$uuid}/xml")->getBody();
    }

    /**
     * GET /earchive/Invoices/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getInvoiceHistories(string $uuid): array
    {
        return $this->get("/earchive/Invoices/{$uuid}/Histories")->json();
    }

    /**
     * GET /earchive/Invoices/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getInvoiceTags(string $uuid): array
    {
        return $this->get("/earchive/Invoices/{$uuid}/Tags")->json();
    }

    /**
     * PUT /earchive/Invoices/{uuid}/Cancel
     */
    public function cancelInvoice(string $uuid): void
    {
        $this->put("/earchive/Invoices/{$uuid}/Cancel");
    }

    /**
     * PUT /earchive/Invoices/Tags — assign tags to multiple invoices.
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToInvoices(array $data): void
    {
        $this->put('/earchive/Invoices/Tags', $data);
    }

    /**
     * PUT /earchive/Invoices/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setInvoiceSpecialCode(array $data): void
    {
        $this->put('/earchive/Invoices/SpecialCode', $data);
    }

    /**
     * POST /earchive/Invoices/Email/Send
     */
    public function sendInvoiceByEmail(SendByEmailRequest $request): void
    {
        $this->post('/earchive/Invoices/Email/Send', $request->toArray());
    }

    /**
     * POST /earchive/Invoices/Sms/Send
     */
    public function sendInvoiceBySms(SendBySmsRequest $request): void
    {
        $this->post('/earchive/Invoices/Sms/Send', $request->toArray());
    }

    /**
     * POST /earchive/Invoices/Whatsapp/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendInvoiceByWhatsapp(string $uuid, array $phoneNumbers): void
    {
        $this->post('/earchive/Invoices/Whatsapp/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /earchive/Invoices/Bulk/Draft — save multiple invoices as drafts.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function bulkCreateDraftFromInvoices(array $data): array
    {
        return $this->post('/earchive/Invoices/Bulk/Draft', $data)->json();
    }

    /**
     * POST /earchive/Invoices/{uuid}/CreateDraft
     *
     * @return array<string, mixed>
     */
    public function createDraftFromInvoice(string $uuid): array
    {
        return $this->post("/earchive/Invoices/{$uuid}/CreateDraft")->json();
    }

    /**
     * GET /earchive/Gib/Purchase — sync incoming e-archive invoices from GIB.
     *
     * @return array<string, mixed>
     */
    public function syncPurchaseFromGib(): array
    {
        return $this->get('/earchive/Gib/Purchase')->json();
    }

    // -------------------------------------------------------------------------
    // Draft Invoices
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/earchive/Draft', $query)->json();
    }

    /**
     * GET /earchive/Draft/{uuid}/html
     */
    public function getDraftHtml(string $uuid): string
    {
        return $this->get("/earchive/Draft/{$uuid}/html")->getBody();
    }

    /**
     * GET /earchive/Draft/{uuid}/pdf
     */
    public function getDraftPdf(string $uuid): string
    {
        return $this->get("/earchive/Draft/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /earchive/Draft/{uuid}/xml
     */
    public function getDraftXml(string $uuid): string
    {
        return $this->get("/earchive/Draft/{$uuid}/xml")->getBody();
    }

    /**
     * GET /earchive/Draft/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getDraftModel(string $uuid): array
    {
        return $this->get("/earchive/Draft/{$uuid}/model")->json();
    }

    /**
     * GET /earchive/Draft/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getDraftTags(string $uuid): array
    {
        return $this->get("/earchive/Draft/{$uuid}/Tags")->json();
    }

    /**
     * POST /earchive/Draft/Create
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/earchive/Draft/Create', $data)->json();
    }

    /**
     * DELETE /earchive/Draft — bulk delete drafts.
     */
    public function deleteDraftsBulk(): void
    {
        $this->delete('/earchive/Draft');
    }

    /**
     * DELETE /earchive/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/earchive/Draft/{$uuid}");
    }

    /**
     * POST /earchive/Draft/EditAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function editAndSendDraft(array $data): array
    {
        return $this->post('/earchive/Draft/EditAndSend', $data)->json();
    }

    /**
     * POST /earchive/Draft/Whatsapp/Send
     *
     * @param array<string, mixed> $data
     */
    public function sendDraftByWhatsapp(array $data): void
    {
        $this->post('/earchive/Draft/Whatsapp/Send', $data);
    }

    /**
     * PUT /earchive/Draft/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToDrafts(array $data): void
    {
        $this->put('/earchive/Draft/Tags', $data);
    }

    /**
     * PUT /earchive/Draft/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setDraftSpecialCode(array $data): void
    {
        $this->put('/earchive/Draft/SpecialCode', $data);
    }

    // -------------------------------------------------------------------------
    // Reports
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Report
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReports(array $query = []): array
    {
        return $this->get('/earchive/Report', $query)->json();
    }

    /**
     * GET /earchive/Report/ToReport — invoices waiting to be reported.
     *
     * @return array<string, mixed>
     */
    public function listInvoicesToReport(): array
    {
        return $this->get('/earchive/Report/ToReport')->json();
    }

    /**
     * GET /earchive/Report/{uuid}/xml
     */
    public function getReportXml(string $uuid): string
    {
        return $this->get("/earchive/Report/{$uuid}/xml")->getBody();
    }

    /**
     * GET /earchive/Report/{uuid}/CheckFromGib — query report status from GIB.
     *
     * @return array<string, mixed>
     */
    public function checkReportFromGib(string $uuid): array
    {
        return $this->get("/earchive/Report/{$uuid}/CheckFromGib")->json();
    }

    /**
     * GET /earchive/Report/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getReportHistories(string $uuid): array
    {
        return $this->get("/earchive/Report/{$uuid}/Histories")->json();
    }

    // -------------------------------------------------------------------------
    // Old Invoices
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Old
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listOldInvoices(array $query = []): array
    {
        return $this->get('/earchive/Old', $query)->json();
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Series
     *
     * @return array<string, mixed>
     */
    public function listSeries(): array
    {
        return $this->get('/earchive/Series')->json();
    }

    /**
     * POST /earchive/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createSeries(array $data): array
    {
        return $this->post('/earchive/Series', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Templates
     *
     * @return array<string, mixed>
     */
    public function listTemplates(): array
    {
        return $this->get('/earchive/Templates')->json();
    }

    /**
     * GET /earchive/Templates/{id}
     *
     * @return array<string, mixed>
     */
    public function getTemplate(int $id): array
    {
        return $this->get("/earchive/Templates/{$id}")->json();
    }

    /**
     * PUT /earchive/Templates
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateTemplate(array $data): array
    {
        return $this->put('/earchive/Templates', $data)->json();
    }

    /**
     * GET /earchive/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/earchive/Templates/Preview/{$uuid}")->getBody();
    }

    /**
     * GET /earchive/Templates/Download/{id}
     */
    public function downloadTemplate(int $id): string
    {
        return $this->get("/earchive/Templates/Download/{$id}")->getBody();
    }

    /**
     * DELETE /earchive/Templates/{id}
     */
    public function deleteTemplate(int $id): void
    {
        $this->delete("/earchive/Templates/{$id}");
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Tags
     *
     * @return array<string, mixed>
     */
    public function listTags(): array
    {
        return $this->get('/earchive/Tags')->json();
    }

    /**
     * PUT /earchive/Tags
     *
     * @param array<string, mixed> $data
     */
    public function updateTags(array $data): void
    {
        $this->put('/earchive/Tags', $data);
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Notification
     *
     * @return array<string, mixed>
     */
    public function listNotifications(): array
    {
        return $this->get('/earchive/Notification')->json();
    }

    /**
     * POST /earchive/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createNotification(array $data): array
    {
        return $this->post('/earchive/Notification', $data)->json();
    }

    /**
     * PUT /earchive/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateNotification(array $data): array
    {
        return $this->put('/earchive/Notification', $data)->json();
    }

    /**
     * DELETE /earchive/Notification/{id}
     */
    public function deleteNotification(int $id): void
    {
        $this->delete("/earchive/Notification/{$id}");
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * GET /earchive/Statistics
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getStatistics(array $query = []): array
    {
        return $this->get('/earchive/Statistics', $query)->json();
    }

    /**
     * GET /earchive/Statistics/Last
     *
     * @return array<string, mixed>
     */
    public function getLastStatistics(): array
    {
        return $this->get('/earchive/Statistics/Last')->json();
    }
}
