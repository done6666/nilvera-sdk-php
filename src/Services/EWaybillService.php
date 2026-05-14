<?php

declare(strict_types=1);

namespace Nilvera\Services;

use Nilvera\Requests\SendWaybillRequest;
use Nilvera\Responses\SendDocumentResponse;

/**
 * E-Waybill (e-İrsaliye) API — base path: /edespatch
 *
 * Covers: sending, outgoing (sale), incoming (purchase), drafts,
 * answer series, answer templates, series, templates, tags,
 * notification settings, statistics, and file upload.
 */
class EWaybillService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /edespatch/Send/Model
     */
    public function send(SendWaybillRequest $waybill): SendDocumentResponse
    {
        return SendDocumentResponse::fromArray(
            $this->post('/edespatch/Send/Model', $waybill->toArray())->json()
        );
    }

    /**
     * POST /edespatch/Send/Model/Download/Pdf — returns PDF binary.
     *
     * @param array<string, mixed> $waybill
     */
    public function downloadPdf(array $waybill): string
    {
        return $this->post('/edespatch/Send/Model/Download/Pdf', $waybill)->getBody();
    }

    /**
     * POST /edespatch/Upload — upload a UBL XML file.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function upload(array $data): array
    {
        return $this->post('/edespatch/Upload', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Sale (Outgoing) Waybills
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/Sale
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listSaleWaybills(array $query = []): array
    {
        return $this->get('/edespatch/Sale', $query)->json();
    }

    /**
     * GET /edespatch/Sale/{uuid}/html
     */
    public function getSaleWaybillHtml(string $uuid): string
    {
        return $this->get("/edespatch/Sale/{$uuid}/html")->getBody();
    }

    /**
     * GET /edespatch/Sale/{uuid}/pdf
     */
    public function getSaleWaybillPdf(string $uuid): string
    {
        return $this->get("/edespatch/Sale/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /edespatch/Sale/{uuid}/xml
     */
    public function getSaleWaybillXml(string $uuid): string
    {
        return $this->get("/edespatch/Sale/{$uuid}/xml")->getBody();
    }

    /**
     * GET /edespatch/Sale/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getSaleWaybillModel(string $uuid): array
    {
        return $this->get("/edespatch/Sale/{$uuid}/model")->json();
    }

    /**
     * GET /edespatch/Sale/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getSaleWaybillStatus(string $uuid): array
    {
        return $this->get("/edespatch/Sale/{$uuid}/Status")->json();
    }

    /**
     * GET /edespatch/Sale/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getSaleWaybillHistories(string $uuid): array
    {
        return $this->get("/edespatch/Sale/{$uuid}/Histories")->json();
    }

    /**
     * GET /edespatch/Sale/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getSaleWaybillTags(string $uuid): array
    {
        return $this->get("/edespatch/Sale/{$uuid}/Tags")->json();
    }

    /**
     * PUT /edespatch/Sale/{uuid}/Cancel
     */
    public function cancelSaleWaybill(string $uuid): void
    {
        $this->put("/edespatch/Sale/{$uuid}/Cancel");
    }

    /**
     * PUT /edespatch/Sale/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToSaleWaybills(array $data): void
    {
        $this->put('/edespatch/Sale/Tags', $data);
    }

    /**
     * PUT /edespatch/Sale/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setSaleWaybillSpecialCode(array $data): void
    {
        $this->put('/edespatch/Sale/SpecialCode', $data);
    }

    /**
     * POST /edespatch/Sale/Email/Send
     *
     * @param string[] $emailAddresses
     */
    public function sendSaleWaybillByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post('/edespatch/Sale/Email/Send', [
            'UUID'           => $uuid,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    /**
     * POST /edespatch/Sale/Sms/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendSaleWaybillBySms(string $uuid, array $phoneNumbers): void
    {
        $this->post('/edespatch/Sale/Sms/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /edespatch/Sale/Whatsapp/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendSaleWaybillByWhatsapp(string $uuid, array $phoneNumbers): void
    {
        $this->post('/edespatch/Sale/Whatsapp/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    // -------------------------------------------------------------------------
    // Purchase (Incoming) Waybills
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/Purchase
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listPurchaseWaybills(array $query = []): array
    {
        return $this->get('/edespatch/Purchase', $query)->json();
    }

    /**
     * GET /edespatch/Purchase/{uuid}/html
     */
    public function getPurchaseWaybillHtml(string $uuid): string
    {
        return $this->get("/edespatch/Purchase/{$uuid}/html")->getBody();
    }

    /**
     * GET /edespatch/Purchase/{uuid}/pdf
     */
    public function getPurchaseWaybillPdf(string $uuid): string
    {
        return $this->get("/edespatch/Purchase/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /edespatch/Purchase/{uuid}/xml
     */
    public function getPurchaseWaybillXml(string $uuid): string
    {
        return $this->get("/edespatch/Purchase/{$uuid}/xml")->getBody();
    }

    /**
     * GET /edespatch/Purchase/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getPurchaseWaybillModel(string $uuid): array
    {
        return $this->get("/edespatch/Purchase/{$uuid}/model")->json();
    }

    /**
     * GET /edespatch/Purchase/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getPurchaseWaybillStatus(string $uuid): array
    {
        return $this->get("/edespatch/Purchase/{$uuid}/Status")->json();
    }

    /**
     * GET /edespatch/Purchase/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getPurchaseWaybillTags(string $uuid): array
    {
        return $this->get("/edespatch/Purchase/{$uuid}/Tags")->json();
    }

    /**
     * PUT /edespatch/Purchase/{uuid}/Accept
     *
     * @return array<string, mixed>
     */
    public function acceptPurchaseWaybill(string $uuid): array
    {
        return $this->put("/edespatch/Purchase/{$uuid}/Accept")->json();
    }

    /**
     * PUT /edespatch/Purchase/{uuid}/Reject
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function rejectPurchaseWaybill(string $uuid, array $data = []): array
    {
        return $this->put("/edespatch/Purchase/{$uuid}/Reject", $data)->json();
    }

    /**
     * PUT /edespatch/Purchase/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToPurchaseWaybills(array $data): void
    {
        $this->put('/edespatch/Purchase/Tags', $data);
    }

    /**
     * POST /edespatch/Purchase/Email/Send
     *
     * @param string[] $emailAddresses
     */
    public function sendPurchaseWaybillByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post('/edespatch/Purchase/Email/Send', [
            'UUID'           => $uuid,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    /**
     * POST /edespatch/Purchase/Sms/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendPurchaseWaybillBySms(string $uuid, array $phoneNumbers): void
    {
        $this->post('/edespatch/Purchase/Sms/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /edespatch/Purchase/Whatsapp/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendPurchaseWaybillByWhatsapp(string $uuid, array $phoneNumbers): void
    {
        $this->post('/edespatch/Purchase/Whatsapp/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    // -------------------------------------------------------------------------
    // Draft Waybills
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/edespatch/Draft', $query)->json();
    }

    /**
     * GET /edespatch/Draft/{uuid}/html
     */
    public function getDraftHtml(string $uuid): string
    {
        return $this->get("/edespatch/Draft/{$uuid}/html")->getBody();
    }

    /**
     * GET /edespatch/Draft/{uuid}/pdf
     */
    public function getDraftPdf(string $uuid): string
    {
        return $this->get("/edespatch/Draft/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /edespatch/Draft/{uuid}/xml
     */
    public function getDraftXml(string $uuid): string
    {
        return $this->get("/edespatch/Draft/{$uuid}/xml")->getBody();
    }

    /**
     * GET /edespatch/Draft/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getDraftModel(string $uuid): array
    {
        return $this->get("/edespatch/Draft/{$uuid}/model")->json();
    }

    /**
     * GET /edespatch/Draft/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getDraftTags(string $uuid): array
    {
        return $this->get("/edespatch/Draft/{$uuid}/Tags")->json();
    }

    /**
     * POST /edespatch/Draft/Create
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/edespatch/Draft/Create', $data)->json();
    }

    /**
     * POST /edespatch/Draft/CreateBulk
     *
     * @param array<string, mixed> $data
     * @return array<string[]>
     */
    public function createDraftsBulk(array $data): array
    {
        return $this->post('/edespatch/Draft/CreateBulk', $data)->json();
    }

    /**
     * DELETE /edespatch/Draft — bulk delete.
     */
    public function deleteDraftsBulk(): void
    {
        $this->delete('/edespatch/Draft');
    }

    /**
     * DELETE /edespatch/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/edespatch/Draft/{$uuid}");
    }

    /**
     * POST /edespatch/Draft/EditAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function editAndSendDraft(array $data): array
    {
        return $this->post('/edespatch/Draft/EditAndSend', $data)->json();
    }

    /**
     * PUT /edespatch/Draft/Operation/{operationType}
     * Common values: Approve, Reject, Cancel.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function draftOperation(string $operationType, array $data = []): array
    {
        return $this->put("/edespatch/Draft/Operation/{$operationType}", $data)->json();
    }

    /**
     * PUT /edespatch/Draft/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToDrafts(array $data): void
    {
        $this->put('/edespatch/Draft/Tags', $data);
    }

    /**
     * PUT /edespatch/Draft/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setDraftSpecialCode(array $data): void
    {
        $this->put('/edespatch/Draft/SpecialCode', $data);
    }

    /**
     * POST /edespatch/Draft/Whatsapp/Send
     *
     * @param array<string, mixed> $data
     */
    public function sendDraftByWhatsapp(array $data): void
    {
        $this->post('/edespatch/Draft/Whatsapp/Send', $data);
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/Series
     *
     * @return array<string, mixed>
     */
    public function listSeries(): array
    {
        return $this->get('/edespatch/Series')->json();
    }

    /**
     * POST /edespatch/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createSeries(array $data): array
    {
        return $this->post('/edespatch/Series', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Answer Series (response waybill series)
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/AnswerSeries
     *
     * @return array<string, mixed>
     */
    public function listAnswerSeries(): array
    {
        return $this->get('/edespatch/AnswerSeries')->json();
    }

    /**
     * GET /edespatch/AnswerSeries/{id}
     *
     * @return array<string, mixed>
     */
    public function getAnswerSeries(int $id): array
    {
        return $this->get("/edespatch/AnswerSeries/{$id}")->json();
    }

    /**
     * POST /edespatch/AnswerSeries
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createAnswerSeries(array $data): array
    {
        return $this->post('/edespatch/AnswerSeries', $data)->json();
    }

    /**
     * PUT /edespatch/AnswerSeries
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateAnswerSeries(array $data): array
    {
        return $this->put('/edespatch/AnswerSeries', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/Templates
     *
     * @return array<string, mixed>
     */
    public function listTemplates(): array
    {
        return $this->get('/edespatch/Templates')->json();
    }

    /**
     * GET /edespatch/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/edespatch/Templates/Preview/{$uuid}")->getBody();
    }

    // -------------------------------------------------------------------------
    // Answer Templates (response waybill templates)
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/AnswerTemplates
     *
     * @return array<string, mixed>
     */
    public function listAnswerTemplates(): array
    {
        return $this->get('/edespatch/AnswerTemplates')->json();
    }

    /**
     * GET /edespatch/AnswerTemplates/Preview/{uuid}
     */
    public function previewAnswerTemplate(string $uuid): string
    {
        return $this->get("/edespatch/AnswerTemplates/Preview/{$uuid}")->getBody();
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/Tags
     *
     * @return array<string, mixed>
     */
    public function listTags(): array
    {
        return $this->get('/edespatch/Tags')->json();
    }

    /**
     * PUT /edespatch/Tags
     *
     * @param array<string, mixed> $data
     */
    public function updateTags(array $data): void
    {
        $this->put('/edespatch/Tags', $data);
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/Notification
     *
     * @return array<string, mixed>
     */
    public function listNotifications(): array
    {
        return $this->get('/edespatch/Notification')->json();
    }

    /**
     * POST /edespatch/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createNotification(array $data): array
    {
        return $this->post('/edespatch/Notification', $data)->json();
    }

    /**
     * PUT /edespatch/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateNotification(array $data): array
    {
        return $this->put('/edespatch/Notification', $data)->json();
    }

    /**
     * DELETE /edespatch/Notification/{id}
     */
    public function deleteNotification(int $id): void
    {
        $this->delete("/edespatch/Notification/{$id}");
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * GET /edespatch/Statistics
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getStatistics(array $query = []): array
    {
        return $this->get('/edespatch/Statistics', $query)->json();
    }

    /**
     * GET /edespatch/Statistics/Last
     *
     * @return array<string, mixed>
     */
    public function getLastStatistics(): array
    {
        return $this->get('/edespatch/Statistics/Last')->json();
    }
}
