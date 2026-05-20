<?php

declare(strict_types=1);

namespace Nilvera\Services;

use Nilvera\Requests\CreateSeriesRequest;
use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\ListSeriesRequest;
use Nilvera\Requests\SendByEmailRequest;
use Nilvera\Requests\SendBySmsRequest;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\UpdateSeriesRequest;
use Nilvera\Responses\SendDocumentResponse;

/**
 * E-Invoice (e-Fatura) API — base path: /einvoice
 *
 * Covers: sending, outgoing (sale), incoming (purchase), drafts,
 * insurance documents (e-SKGB), series, templates, tags,
 * notification settings, statistics, old invoices, and file upload.
 */
class EInvoiceService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /einvoice/Send/Model
     */
    public function send(SendInvoiceRequest $invoice): SendDocumentResponse
    {
        return SendDocumentResponse::fromArray(
            $this->post('/einvoice/Send/Model', $invoice->toArray())->json()
        );
    }

    /**
     * POST /einvoice/Send/Model/Preview — returns HTML preview without sending.
     */
    public function preview(SendInvoiceRequest $invoice): string
    {
        $body = $this->post('/einvoice/Send/Model/Preview', $invoice->toArray())->getBody();

        try {
            $decoded = json_decode($body, flags: JSON_THROW_ON_ERROR);
            return is_string($decoded) ? $decoded : $body;
        } catch (\JsonException) {
            return $body;
        }
    }

    /**
     * POST /einvoice/Send/Model/Download/Pdf — returns PDF binary.
     */
    public function downloadPdf(SendInvoiceRequest $invoice): string
    {
        return $this->post('/einvoice/Send/Model/Download/Pdf', $invoice->toArray())->getBody();
    }

    /**
     * POST /einvoice/Send/Xml
     *
     * @return array{UUID: string, InvoiceNumber: ?string}
     */
    public function sendXml(string $xmlContent): array
    {
        return $this->post('/einvoice/Send/Xml', ['XmlContent' => $xmlContent])->json();
    }

    /**
     * POST /einvoice/Send/Base64String
     *
     * @return array{UUID: string, InvoiceNumber: ?string}
     */
    public function sendBase64(string $base64Content): array
    {
        return $this->post('/einvoice/Send/Base64String', ['Base64Content' => $base64Content])->json();
    }

    /**
     * POST /einvoice/Upload — upload a UBL XML file.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function upload(array $data): array
    {
        return $this->post('/einvoice/Upload', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Sale (Outgoing) Invoices
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Sale
     *
     * @return array<string, mixed>
     */
    public function listSaleInvoices(ListInvoicesRequest $query = new ListInvoicesRequest()): array
    {
        return $this->get('/einvoice/Sale', $query->toArray())->json();
    }

    /**
     * GET /einvoice/Sale/{uuid}/html
     */
    public function getSaleInvoiceHtml(string $uuid): string
    {
        return $this->get("/einvoice/Sale/{$uuid}/html")->getBody();
    }

    /**
     * GET /einvoice/Sale/{uuid}/pdf
     */
    public function getSaleInvoicePdf(string $uuid): string
    {
        return $this->get("/einvoice/Sale/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /einvoice/Sale/{uuid}/xml
     */
    public function getSaleInvoiceXml(string $uuid): string
    {
        return $this->get("/einvoice/Sale/{$uuid}/xml")->getBody();
    }

    /**
     * GET /einvoice/Sale/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getSaleInvoiceModel(string $uuid): array
    {
        return $this->get("/einvoice/Sale/{$uuid}/model")->json();
    }

    /**
     * GET /einvoice/Sale/{uuid}/EnvelopeInfo
     *
     * @return array{GIBCode: ?string, GIBDescription: ?string, EnvelopeUUID: ?string}
     */
    public function getSaleInvoiceEnvelopeInfo(string $uuid): array
    {
        return $this->get("/einvoice/Sale/{$uuid}/EnvelopeInfo")->json();
    }

    /**
     * GET /einvoice/Sale/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getSaleInvoiceStatus(string $uuid): array
    {
        return $this->get("/einvoice/Sale/{$uuid}/Status")->json();
    }

    /**
     * GET /einvoice/Sale/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getSaleInvoiceHistories(string $uuid): array
    {
        return $this->get("/einvoice/Sale/{$uuid}/Histories")->json();
    }

    /**
     * GET /einvoice/Sale/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getSaleInvoiceTags(string $uuid): array
    {
        return $this->get("/einvoice/Sale/{$uuid}/Tags")->json();
    }

    /**
     * PUT /einvoice/Sale/{uuid}/Cancel
     */
    public function cancelSaleInvoice(string $uuid): void
    {
        $this->put("/einvoice/Sale/{$uuid}/Cancel");
    }

    /**
     * PUT /einvoice/Sale/Tags — assign tags to outgoing invoices.
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToSaleInvoices(array $data): void
    {
        $this->put('/einvoice/Sale/Tags', $data);
    }

    /**
     * PUT /einvoice/Sale/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setSaleInvoiceSpecialCode(array $data): void
    {
        $this->put('/einvoice/Sale/SpecialCode', $data);
    }

    /**
     * POST /einvoice/Sale/Email/Send
     */
    public function sendSaleInvoiceByEmail(SendByEmailRequest $request): void
    {
        $this->post('/einvoice/Sale/Email/Send', $request->toArray());
    }

    /**
     * POST /einvoice/Sale/Sms/Send
     */
    public function sendSaleInvoiceBySms(SendBySmsRequest $request): void
    {
        $this->post('/einvoice/Sale/Sms/Send', $request->toArray());
    }

    /**
     * POST /einvoice/Sale/Whatsapp/Send
     */
    public function sendSaleInvoiceByWhatsapp(SendBySmsRequest $request): void
    {
        $this->post('/einvoice/Sale/Whatsapp/Send', $request->toArray());
    }

    // -------------------------------------------------------------------------
    // Purchase (Incoming) Invoices
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Purchase
     *
     * @return array<string, mixed>
     */
    public function listPurchaseInvoices(ListInvoicesRequest $query = new ListInvoicesRequest()): array
    {
        return $this->get('/einvoice/Purchase', $query->toArray())->json();
    }

    /**
     * GET /einvoice/Purchase/{uuid}/html
     */
    public function getPurchaseInvoiceHtml(string $uuid): string
    {
        return $this->get("/einvoice/Purchase/{$uuid}/html")->getBody();
    }

    /**
     * GET /einvoice/Purchase/{uuid}/pdf
     */
    public function getPurchaseInvoicePdf(string $uuid): string
    {
        return $this->get("/einvoice/Purchase/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /einvoice/Purchase/{uuid}/xml
     */
    public function getPurchaseInvoiceXml(string $uuid): string
    {
        return $this->get("/einvoice/Purchase/{$uuid}/xml")->getBody();
    }

    /**
     * GET /einvoice/Purchase/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getPurchaseInvoiceModel(string $uuid): array
    {
        return $this->get("/einvoice/Purchase/{$uuid}/model")->json();
    }

    /**
     * GET /einvoice/Purchase/{uuid}/EnvelopeInfo
     *
     * @return array{GIBCode: ?string, GIBDescription: ?string, EnvelopeUUID: ?string}
     */
    public function getPurchaseInvoiceEnvelopeInfo(string $uuid): array
    {
        return $this->get("/einvoice/Purchase/{$uuid}/EnvelopeInfo")->json();
    }

    /**
     * GET /einvoice/Purchase/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getPurchaseInvoiceTags(string $uuid): array
    {
        return $this->get("/einvoice/Purchase/{$uuid}/Tags")->json();
    }

    /**
     * PUT /einvoice/Purchase/{uuid}/Read — mark invoice as read.
     */
    public function markPurchaseInvoiceAsRead(string $uuid): void
    {
        $this->put("/einvoice/Purchase/{$uuid}/Read");
    }

    /**
     * POST /einvoice/Purchase/{uuid}/CreateReturn
     *
     * @return array{UUID: string, InvoiceNumber: ?string}
     */
    public function createReturnFromPurchaseInvoice(string $uuid): array
    {
        return $this->post("/einvoice/Purchase/{$uuid}/CreateReturn")->json();
    }

    /**
     * PUT /einvoice/Purchase/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToPurchaseInvoices(array $data): void
    {
        $this->put('/einvoice/Purchase/Tags', $data);
    }

    /**
     * POST /einvoice/Purchase/Email/Send
     */
    public function sendPurchaseInvoiceByEmail(SendByEmailRequest $request): void
    {
        $this->post('/einvoice/Purchase/Email/Send', $request->toArray());
    }

    /**
     * POST /einvoice/Purchase/Sms/Send
     */
    public function sendPurchaseInvoiceBySms(SendBySmsRequest $request): void
    {
        $this->post('/einvoice/Purchase/Sms/Send', $request->toArray());
    }

    /**
     * GET /einvoice/Gib/Purchase — sync incoming invoices from GIB.
     *
     * @return array<string, mixed>
     */
    public function syncPurchaseFromGib(): array
    {
        return $this->get('/einvoice/Gib/Purchase')->json();
    }

    // -------------------------------------------------------------------------
    // Draft Invoices
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
     * DELETE /einvoice/Draft — bulk delete drafts.
     *
     * @param array<string, mixed> $data
     */
    public function deleteDraftsBulk(array $data = []): void
    {
        $this->delete('/einvoice/Draft', $data);
    }

    /**
     * DELETE /einvoice/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/einvoice/Draft/{$uuid}");
    }

    /**
     * POST /einvoice/Draft/{uuid}/Send — send a specific draft.
     */
    public function sendDraft(string $uuid): SendDocumentResponse
    {
        return SendDocumentResponse::fromArray(
            $this->post("/einvoice/Draft/{$uuid}/Send")->json()
        );
    }

    /**
     * POST /einvoice/Draft/EditAndSend — update payload and send immediately.
     *
     * @param array<string, mixed> $data
     */
    public function editAndSendDraft(array $data): SendDocumentResponse
    {
        return SendDocumentResponse::fromArray(
            $this->post('/einvoice/Draft/EditAndSend', $data)->json()
        );
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
     * POST /einvoice/Draft/Whatsapp/Send
     *
     * @param array<string, mixed> $data
     */
    public function sendDraftByWhatsapp(array $data): void
    {
        $this->post('/einvoice/Draft/Whatsapp/Send', $data);
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Series
     *
     * @return array<string, mixed>
     */
    public function listSeries(ListSeriesRequest $query = new ListSeriesRequest()): array
    {
        return $this->get('/einvoice/Series', $query->toArray())->json();
    }

    /**
     * GET /einvoice/Series/{id}
     *
     * @return array<string, mixed>
     */
    public function getSeries(int $id): array
    {
        return $this->get("/einvoice/Series/{$id}")->json();
    }

    /**
     * POST /einvoice/Series
     *
     * @return array<string, mixed>
     */
    public function createSeries(CreateSeriesRequest $request): array
    {
        return $this->post('/einvoice/Series', $request->toArray())->json();
    }

    /**
     * PUT /einvoice/Series
     */
    public function updateSeries(UpdateSeriesRequest $request): bool
    {
        return json_decode($this->put('/einvoice/Series', $request->toArray())->getBody()) === true;
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Templates
     *
     * @return array<string, mixed>
     */
    public function listTemplates(): array
    {
        return $this->get('/einvoice/Templates')->json();
    }

    /**
     * GET /einvoice/Templates/{id}
     *
     * @return array<string, mixed>
     */
    public function getTemplate(int $id): array
    {
        return $this->get("/einvoice/Templates/{$id}")->json();
    }

    /**
     * PUT /einvoice/Templates
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateTemplate(array $data): array
    {
        return $this->put('/einvoice/Templates', $data)->json();
    }

    /**
     * GET /einvoice/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/einvoice/Templates/Preview/{$uuid}")->getBody();
    }

    /**
     * DELETE /einvoice/Templates/{id}
     */
    public function deleteTemplate(int $id): void
    {
        $this->delete("/einvoice/Templates/{$id}");
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Tags
     *
     * @return array<string, mixed>
     */
    public function listTags(): array
    {
        return $this->get('/einvoice/Tags')->json();
    }

    /**
     * PUT /einvoice/Tags
     *
     * @param array<string, mixed> $data
     */
    public function updateTags(array $data): void
    {
        $this->put('/einvoice/Tags', $data);
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Notification
     *
     * @return array<string, mixed>
     */
    public function listNotifications(): array
    {
        return $this->get('/einvoice/Notification')->json();
    }

    /**
     * POST /einvoice/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createNotification(array $data): array
    {
        return $this->post('/einvoice/Notification', $data)->json();
    }

    /**
     * PUT /einvoice/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateNotification(array $data): array
    {
        return $this->put('/einvoice/Notification', $data)->json();
    }

    /**
     * DELETE /einvoice/Notification/{id}
     */
    public function deleteNotification(int $id): void
    {
        $this->delete("/einvoice/Notification/{$id}");
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Statistics/Sale
     *
     * @param array<string, mixed> $query  Supported: StartDate, EndDate
     * @return array<string, mixed>
     */
    public function getSaleStatistics(array $query = []): array
    {
        return $this->get('/einvoice/Statistics/Sale', $query)->json();
    }

    /**
     * GET /einvoice/Statistics/Purchase
     *
     * @param array<string, mixed> $query  Supported: StartDate, EndDate
     * @return array<string, mixed>
     */
    public function getPurchaseStatistics(array $query = []): array
    {
        return $this->get('/einvoice/Statistics/Purchase', $query)->json();
    }

    // -------------------------------------------------------------------------
    // Old Invoices
    // -------------------------------------------------------------------------

    /**
     * GET /einvoice/Old
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listOldInvoices(array $query = []): array
    {
        return $this->get('/einvoice/Old', $query)->json();
    }

    // -------------------------------------------------------------------------
    // Insurance (e-SKGB) Documents — share the /einvoice namespace
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
    public function sendInsuranceByEmail(string $uuid, array $emailAddresses): void
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
    public function sendInsuranceByWhatsapp(string $uuid, array $phoneNumbers): void
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
    public function createInsuranceDraft(string $uuid): array
    {
        return $this->post("/einvoice/Insurances/{$uuid}/CreateDraft")->json();
    }
}
