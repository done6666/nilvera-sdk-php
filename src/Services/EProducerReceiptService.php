<?php

declare(strict_types=1);

namespace Nilvera\Services;

use Nilvera\Requests\SendProducerReceiptRequest;
use Nilvera\Responses\SendDocumentResponse;

/**
 * E-MM (Müstahsil Makbuzu — Producer Receipt) API.
 * Base path: /eproducer
 *
 * Covers: sending, producer receipts, old producers, drafts, series,
 * templates, tags, notification settings, statistics, reports, and file upload.
 */
class EProducerReceiptService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending
    // -------------------------------------------------------------------------

    /**
     * POST /eproducer/Send/Model
     */
    public function send(SendProducerReceiptRequest $receipt): SendDocumentResponse
    {
        return SendDocumentResponse::fromArray(
            $this->post('/eproducer/Send/Model', $receipt->toArray())->json()
        );
    }

    /**
     * POST /eproducer/Send/Model/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewModel(array $data): array
    {
        return $this->post('/eproducer/Send/Model/Preview', $data)->json();
    }

    /**
     * POST /eproducer/Send/Xml
     *
     * @return array<string, mixed>
     */
    public function sendXml(string $xmlContent): array
    {
        return $this->post('/eproducer/Send/Xml', ['XmlContent' => $xmlContent])->json();
    }

    /**
     * POST /eproducer/Send/Xml/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewXml(array $data): array
    {
        return $this->post('/eproducer/Send/Xml/Preview', $data)->json();
    }

    /**
     * POST /eproducer/Send/Base64String
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendBase64(array $data): array
    {
        return $this->post('/eproducer/Send/Base64String', $data)->json();
    }

    /**
     * POST /eproducer/Send/Base64String/Preview
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function previewBase64(array $data): array
    {
        return $this->post('/eproducer/Send/Base64String/Preview', $data)->json();
    }

    /**
     * GET /eproducer/Send/Xml/Preview
     *
     * @param array<string, mixed> $query
     * @return string
     */
    public function getPreviewedXml(array $query = []): string
    {
        return $this->get('/eproducer/Send/Xml/Preview', $query)->getBody();
    }

    /**
     * POST /eproducer/Send/Report
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendReport(array $data): array
    {
        return $this->post('/eproducer/Send/Report', $data)->json();
    }

    /**
     * POST /eproducer/Upload — upload a UBL XML file.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function upload(array $data): array
    {
        return $this->post('/eproducer/Upload', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Producer Receipts
    // -------------------------------------------------------------------------

    /**
     * GET /eproducer/Producers
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listProducers(array $query = []): array
    {
        return $this->get('/eproducer/Producers', $query)->json();
    }

    /**
     * GET /eproducer/Producers/{uuid}/html
     */
    public function getProducerHtml(string $uuid): string
    {
        return $this->get("/eproducer/Producers/{$uuid}/html")->getBody();
    }

    /**
     * GET /eproducer/Producers/{uuid}/pdf
     */
    public function getProducerPdf(string $uuid): string
    {
        return $this->get("/eproducer/Producers/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /eproducer/Producers/{uuid}/xml
     */
    public function getProducerXml(string $uuid): string
    {
        return $this->get("/eproducer/Producers/{$uuid}/xml")->getBody();
    }

    /**
     * GET /eproducer/Producers/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getProducerTags(string $uuid): array
    {
        return $this->get("/eproducer/Producers/{$uuid}/Tags")->json();
    }

    /**
     * GET /eproducer/Producers/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getProducerHistories(string $uuid): array
    {
        return $this->get("/eproducer/Producers/{$uuid}/Histories")->json();
    }

    /**
     * GET /eproducer/Producers/{uuid}/Details
     *
     * @return array<string, mixed>
     */
    public function getProducerDetails(string $uuid): array
    {
        return $this->get("/eproducer/Producers/{$uuid}/Details")->json();
    }

    /**
     * GET /eproducer/Producers/{uuid}/Taxes
     *
     * @return array<string, mixed>
     */
    public function getProducerTaxes(string $uuid): array
    {
        return $this->get("/eproducer/Producers/{$uuid}/Taxes")->json();
    }

    /**
     * GET /eproducer/Producers/{messageId}/MailActivityhistories
     *
     * @return array<string, mixed>
     */
    public function getProducerMailHistories(string $messageId): array
    {
        return $this->get("/eproducer/Producers/{$messageId}/MailActivityhistories")->json();
    }

    /**
     * GET /eproducer/Producers/{uuid}/Whatsapphistories
     *
     * @return array<string, mixed>
     */
    public function getProducerWhatsappHistories(string $uuid): array
    {
        return $this->get("/eproducer/Producers/{$uuid}/Whatsapphistories")->json();
    }

    /**
     * GET /eproducer/Producers/{uuid}/Smshistories
     *
     * @return array<string, mixed>
     */
    public function getProducerSmsHistories(string $uuid): array
    {
        return $this->get("/eproducer/Producers/{$uuid}/Smshistories")->json();
    }

    /**
     * GET /eproducer/Producers/{uuid}/EmailActivities
     *
     * @return array<string, mixed>
     */
    public function getProducerEmailActivities(string $uuid): array
    {
        return $this->get("/eproducer/Producers/{$uuid}/EmailActivities")->json();
    }

    /**
     * GET /eproducer/Producers/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getProducerStatus(string $uuid): array
    {
        return $this->get("/eproducer/Producers/{$uuid}/Status")->json();
    }

    /**
     * PUT /eproducer/Producers/{uuid}/Cancel
     */
    public function cancelProducer(string $uuid): void
    {
        $this->put("/eproducer/Producers/{$uuid}/Cancel");
    }

    /**
     * PUT /eproducer/Producers/{uuid}/RevertCancel
     */
    public function revertCancelProducer(string $uuid): void
    {
        $this->put("/eproducer/Producers/{$uuid}/RevertCancel");
    }

    /**
     * PUT /eproducer/Producers/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToProducers(array $data): void
    {
        $this->put('/eproducer/Producers/Tags', $data);
    }

    /**
     * PUT /eproducer/Producers/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setProducerSpecialCode(array $data): void
    {
        $this->put('/eproducer/Producers/SpecialCode', $data);
    }

    /**
     * PUT /eproducer/Producers/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignStatusToProducers(string $operationType, array $data = []): array
    {
        return $this->put("/eproducer/Producers/Operation/{$operationType}", $data)->json();
    }

    /**
     * POST /eproducer/Producers/Email/Send
     *
     * @param string[] $emailAddresses
     */
    public function sendProducerByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post('/eproducer/Producers/Email/Send', [
            'UUID'           => $uuid,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    /**
     * POST /eproducer/Producers/Whatsapp/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendProducerByWhatsapp(string $uuid, array $phoneNumbers): void
    {
        $this->post('/eproducer/Producers/Whatsapp/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /eproducer/Producers/Sms/Send
     *
     * @param string[] $phoneNumbers
     */
    public function sendProducerBySms(string $uuid, array $phoneNumbers): void
    {
        $this->post('/eproducer/Producers/Sms/Send', [
            'UUID'         => $uuid,
            'phoneNumbers' => $phoneNumbers,
        ]);
    }

    /**
     * POST /eproducer/Producers/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportProducers(string $fileType, array $data = []): array
    {
        return $this->post("/eproducer/Producers/Export/{$fileType}", $data)->json();
    }

    /**
     * POST /eproducer/Producers/{uuid}/CreateDraft
     *
     * @return array<string, mixed>
     */
    public function createDraftFromProducer(string $uuid): array
    {
        return $this->post("/eproducer/Producers/{$uuid}/CreateDraft")->json();
    }

    // -------------------------------------------------------------------------
    // Old Producers
    // -------------------------------------------------------------------------

    /**
     * POST /eproducer/Old — upload old producer receipts (multipart/form-data).
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function uploadOldProducer(array $data): array
    {
        return $this->post('/eproducer/Old', $data)->json();
    }

    /**
     * GET /eproducer/Old
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listOldProducers(array $query = []): array
    {
        return $this->get('/eproducer/Old', $query)->json();
    }

    /**
     * GET /eproducer/Old/{uuid}/html
     */
    public function getOldProducerHtml(string $uuid): string
    {
        return $this->get("/eproducer/Old/{$uuid}/html")->getBody();
    }

    /**
     * GET /eproducer/Old/{uuid}/pdf
     */
    public function getOldProducerPdf(string $uuid): string
    {
        return $this->get("/eproducer/Old/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /eproducer/Old/{uuid}/xml
     */
    public function getOldProducerXml(string $uuid): string
    {
        return $this->get("/eproducer/Old/{$uuid}/xml")->getBody();
    }

    /**
     * POST /eproducer/Old/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportOldProducers(string $fileType, array $data = []): array
    {
        return $this->post("/eproducer/Old/Export/{$fileType}", $data)->json();
    }

    /**
     * PUT /eproducer/Old/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignStatusToOldProducers(string $operationType, array $data = []): array
    {
        return $this->put("/eproducer/Old/Operation/{$operationType}", $data)->json();
    }

    // -------------------------------------------------------------------------
    // Draft Receipts
    // -------------------------------------------------------------------------

    /**
     * GET /eproducer/Draft
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDrafts(array $query = []): array
    {
        return $this->get('/eproducer/Draft', $query)->json();
    }

    /**
     * GET /eproducer/Draft/{uuid}/html
     */
    public function getDraftHtml(string $uuid): string
    {
        return $this->get("/eproducer/Draft/{$uuid}/html")->getBody();
    }

    /**
     * GET /eproducer/Draft/{uuid}/pdf
     */
    public function getDraftPdf(string $uuid): string
    {
        return $this->get("/eproducer/Draft/{$uuid}/pdf")->getBody();
    }

    /**
     * GET /eproducer/Draft/{uuid}/xml
     */
    public function getDraftXml(string $uuid): string
    {
        return $this->get("/eproducer/Draft/{$uuid}/xml")->getBody();
    }

    /**
     * GET /eproducer/Draft/{uuid}/model
     *
     * @return array<string, mixed>
     */
    public function getDraftModel(string $uuid): array
    {
        return $this->get("/eproducer/Draft/{$uuid}/model")->json();
    }

    /**
     * GET /eproducer/Draft/{uuid}/Tags
     *
     * @return array<string, mixed>
     */
    public function getDraftTags(string $uuid): array
    {
        return $this->get("/eproducer/Draft/{$uuid}/Tags")->json();
    }

    /**
     * GET /eproducer/Draft/{uuid}/Whatsapphistories
     *
     * @return array<string, mixed>
     */
    public function getDraftWhatsappHistories(string $uuid): array
    {
        return $this->get("/eproducer/Draft/{$uuid}/Whatsapphistories")->json();
    }

    /**
     * POST /eproducer/Draft/Create
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/eproducer/Draft/Create', $data)->json();
    }

    /**
     * POST /eproducer/Draft/CreateBulk
     *
     * @param array<string, mixed> $data
     * @return array<string[]>
     */
    public function createDraftsBulk(array $data): array
    {
        return $this->post('/eproducer/Draft/CreateBulk', $data)->json();
    }

    /**
     * POST /eproducer/Draft/ConfirmAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function confirmAndSendDraft(array $data): array
    {
        return $this->post('/eproducer/Draft/ConfirmAndSend', $data)->json();
    }

    /**
     * POST /eproducer/Draft/EditAndSend
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function editAndSendDraft(array $data): array
    {
        return $this->post('/eproducer/Draft/EditAndSend', $data)->json();
    }

    /**
     * POST /eproducer/Draft/Export/{fileType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function exportDrafts(string $fileType, array $data = []): array
    {
        return $this->post("/eproducer/Draft/Export/{$fileType}", $data)->json();
    }

    /**
     * POST /eproducer/Draft/Whatsapp/Send
     *
     * @param array<string, mixed> $data
     */
    public function sendDraftByWhatsapp(array $data): void
    {
        $this->post('/eproducer/Draft/Whatsapp/Send', $data);
    }

    /**
     * PUT /eproducer/Draft/Operation/{operationType}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function draftOperation(string $operationType, array $data = []): array
    {
        return $this->put("/eproducer/Draft/Operation/{$operationType}", $data)->json();
    }

    /**
     * PUT /eproducer/Draft/Tags
     *
     * @param array<string, mixed> $data
     */
    public function assignTagsToDrafts(array $data): void
    {
        $this->put('/eproducer/Draft/Tags', $data);
    }

    /**
     * PUT /eproducer/Draft/SpecialCode
     *
     * @param array<string, mixed> $data
     */
    public function setDraftSpecialCode(array $data): void
    {
        $this->put('/eproducer/Draft/SpecialCode', $data);
    }

    /**
     * DELETE /eproducer/Draft — bulk delete.
     */
    public function deleteDraftsBulk(): void
    {
        $this->delete('/eproducer/Draft');
    }

    /**
     * DELETE /eproducer/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/eproducer/Draft/{$uuid}");
    }

    // -------------------------------------------------------------------------
    // Reports
    // -------------------------------------------------------------------------

    /**
     * GET /eproducer/Report
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReports(array $query = []): array
    {
        return $this->get('/eproducer/Report', $query)->json();
    }

    /**
     * GET /eproducer/Report/List
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getReportList(array $query = []): array
    {
        return $this->get('/eproducer/Report/List', $query)->json();
    }

    /**
     * GET /eproducer/Report/{uuid}/Xml
     */
    public function getReportXml(string $uuid): string
    {
        return $this->get("/eproducer/Report/{$uuid}/Xml")->getBody();
    }

    /**
     * GET /eproducer/Report/{uuid}/Documents
     *
     * @return array<string, mixed>
     */
    public function listReportDocuments(string $uuid): array
    {
        return $this->get("/eproducer/Report/{$uuid}/Documents")->json();
    }

    /**
     * GET /eproducer/Report/{uuid}/Histories
     *
     * @return array<string, mixed>
     */
    public function getReportHistories(string $uuid): array
    {
        return $this->get("/eproducer/Report/{$uuid}/Histories")->json();
    }

    /**
     * GET /eproducer/Report/{uuid}/GibStatus
     *
     * @return array<string, mixed>
     */
    public function queryReportGibStatus(string $uuid): array
    {
        return $this->get("/eproducer/Report/{$uuid}/GibStatus")->json();
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    /**
     * GET /eproducer/Series
     *
     * @return array<string, mixed>
     */
    public function listSeries(): array
    {
        return $this->get('/eproducer/Series')->json();
    }

    /**
     * GET /eproducer/Series/{id}
     *
     * @return array<string, mixed>
     */
    public function getSeriesDetail(int $id): array
    {
        return $this->get("/eproducer/Series/{$id}")->json();
    }

    /**
     * POST /eproducer/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createSeries(array $data): array
    {
        return $this->post('/eproducer/Series', $data)->json();
    }

    /**
     * PUT /eproducer/Series
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateSeries(array $data): array
    {
        return $this->put('/eproducer/Series', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------

    /**
     * GET /eproducer/Templates
     *
     * @return array<string, mixed>
     */
    public function listTemplates(): array
    {
        return $this->get('/eproducer/Templates')->json();
    }

    /**
     * GET /eproducer/Templates/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getTemplateDetail(string $uuid): array
    {
        return $this->get("/eproducer/Templates/{$uuid}")->json();
    }

    /**
     * GET /eproducer/Templates/Preview/{uuid}
     */
    public function previewTemplate(string $uuid): string
    {
        return $this->get("/eproducer/Templates/Preview/{$uuid}")->getBody();
    }

    /**
     * PUT /eproducer/Templates
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateTemplate(array $data): array
    {
        return $this->put('/eproducer/Templates', $data)->json();
    }

    /**
     * DELETE /eproducer/Templates/{uuid}
     */
    public function deleteTemplate(string $uuid): void
    {
        $this->delete("/eproducer/Templates/{$uuid}");
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * GET /eproducer/Tags
     *
     * @return array<string, mixed>
     */
    public function listTags(): array
    {
        return $this->get('/eproducer/Tags')->json();
    }

    /**
     * POST /eproducer/Tags
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createTag(array $data): array
    {
        return $this->post('/eproducer/Tags', $data)->json();
    }

    /**
     * PUT /eproducer/Tags
     *
     * @param array<string, mixed> $data
     */
    public function updateTags(array $data): void
    {
        $this->put('/eproducer/Tags', $data);
    }

    /**
     * DELETE /eproducer/Tags/{uuid}
     */
    public function deleteTag(string $uuid): void
    {
        $this->delete("/eproducer/Tags/{$uuid}");
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * GET /eproducer/Notification
     *
     * @return array<string, mixed>
     */
    public function listNotifications(): array
    {
        return $this->get('/eproducer/Notification')->json();
    }

    /**
     * GET /eproducer/Notification/{id}
     *
     * @return array<string, mixed>
     */
    public function getNotification(int $id): array
    {
        return $this->get("/eproducer/Notification/{$id}")->json();
    }

    /**
     * POST /eproducer/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createNotification(array $data): array
    {
        return $this->post('/eproducer/Notification', $data)->json();
    }

    /**
     * PUT /eproducer/Notification
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateNotification(array $data): array
    {
        return $this->put('/eproducer/Notification', $data)->json();
    }

    /**
     * DELETE /eproducer/Notification/{id}
     */
    public function deleteNotification(int $id): void
    {
        $this->delete("/eproducer/Notification/{$id}");
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * GET /eproducer/Statistics
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getStatistics(array $query = []): array
    {
        return $this->get('/eproducer/Statistics', $query)->json();
    }

    /**
     * GET /eproducer/Statistics/Last
     *
     * @return array<string, mixed>
     */
    public function getLastStatistics(): array
    {
        return $this->get('/eproducer/Statistics/Last')->json();
    }
}
