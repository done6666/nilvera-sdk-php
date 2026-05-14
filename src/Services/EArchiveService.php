<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Archive Invoice (e-Arşiv) API.
 * Base path: /earchive
 *
 * Handles e-archive invoices, drafts, reports, series, templates, and more.
 */
class EArchiveService extends AbstractService
{
    // -------------------------------------------------------------------------
    // E-Archive Invoices
    // -------------------------------------------------------------------------

    /**
     * List e-archive invoices.
     *
     * GET /earchive/EArchiveInvoice
     *
     * @param array<string, mixed> $query  Supported: page, pageSize, startDate, endDate, ...
     * @return array<string, mixed>
     */
    public function listInvoices(array $query = []): array
    {
        return $this->get('/earchive/EArchiveInvoice', $query)->json();
    }

    /**
     * Get a single e-archive invoice by UUID.
     *
     * GET /earchive/EArchiveInvoice/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getInvoice(string $uuid): array
    {
        return $this->get("/earchive/EArchiveInvoice/{$uuid}")->json();
    }

    /**
     * Get the HTML representation of an e-archive invoice.
     *
     * GET /earchive/EArchiveInvoice/{uuid}/Html
     */
    public function getInvoiceHtml(string $uuid): string
    {
        return $this->get("/earchive/EArchiveInvoice/{$uuid}/Html")->getBody();
    }

    /**
     * Get the PDF binary of an e-archive invoice.
     *
     * GET /earchive/EArchiveInvoice/{uuid}/Pdf
     */
    public function getInvoicePdf(string $uuid): string
    {
        return $this->get("/earchive/EArchiveInvoice/{$uuid}/Pdf")->getBody();
    }

    /**
     * Cancel an e-archive invoice.
     *
     * DELETE /earchive/EArchiveInvoice/{uuid}
     */
    public function cancelInvoice(string $uuid): void
    {
        $this->delete("/earchive/EArchiveInvoice/{$uuid}");
    }

    /**
     * Send an e-archive invoice via email.
     *
     * POST /earchive/EArchiveInvoice/{uuid}/Email
     *
     * @param string[] $emailAddresses
     */
    public function sendInvoiceByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post("/earchive/EArchiveInvoice/{$uuid}/Email", [
            'emailAddresses' => $emailAddresses,
        ]);
    }

    // -------------------------------------------------------------------------
    // Draft Invoices
    // -------------------------------------------------------------------------

    /**
     * List e-archive draft invoices.
     *
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
     * Get a single draft by UUID.
     *
     * GET /earchive/Draft/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getDraft(string $uuid): array
    {
        return $this->get("/earchive/Draft/{$uuid}")->json();
    }

    /**
     * Create a new e-archive draft invoice.
     *
     * POST /earchive/Draft
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/earchive/Draft', $data)->json();
    }

    /**
     * Update an e-archive draft invoice.
     *
     * PUT /earchive/Draft/{uuid}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateDraft(string $uuid, array $data): array
    {
        return $this->put("/earchive/Draft/{$uuid}", $data)->json();
    }

    /**
     * Delete an e-archive draft invoice.
     *
     * DELETE /earchive/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/earchive/Draft/{$uuid}");
    }

    /**
     * Send a draft (convert to e-archive invoice).
     *
     * POST /earchive/Draft/{uuid}/Send
     *
     * @return array<string, mixed>
     */
    public function sendDraft(string $uuid): array
    {
        return $this->post("/earchive/Draft/{$uuid}/Send")->json();
    }

    // -------------------------------------------------------------------------
    // Reports
    // -------------------------------------------------------------------------

    /**
     * List e-archive reports.
     *
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
     * Submit e-archive report to GIB.
     *
     * POST /earchive/Report
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function submitReport(array $data): array
    {
        return $this->post('/earchive/Report', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    /**
     * List invoice series.
     *
     * GET /earchive/Series
     *
     * @return array<string, mixed>
     */
    public function listSeries(): array
    {
        return $this->get('/earchive/Series')->json();
    }

    /**
     * Create a new invoice series.
     *
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
     * List invoice templates.
     *
     * GET /earchive/Template
     *
     * @return array<string, mixed>
     */
    public function listTemplates(): array
    {
        return $this->get('/earchive/Template')->json();
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    /**
     * List tags.
     *
     * GET /earchive/Tag
     *
     * @return array<string, mixed>
     */
    public function listTags(): array
    {
        return $this->get('/earchive/Tag')->json();
    }

    /**
     * Assign a tag to an invoice.
     *
     * POST /earchive/Tag
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function assignTag(array $data): array
    {
        return $this->post('/earchive/Tag', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Notification Settings
    // -------------------------------------------------------------------------

    /**
     * Get notification settings.
     *
     * GET /earchive/NotificationSetting
     *
     * @return array<string, mixed>
     */
    public function getNotificationSettings(): array
    {
        return $this->get('/earchive/NotificationSetting')->json();
    }

    /**
     * Update notification settings.
     *
     * PUT /earchive/NotificationSetting
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateNotificationSettings(array $data): array
    {
        return $this->put('/earchive/NotificationSetting', $data)->json();
    }

    // -------------------------------------------------------------------------
    // Statistics
    // -------------------------------------------------------------------------

    /**
     * Get e-archive statistics.
     *
     * GET /earchive/Statistic
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function getStatistics(array $query = []): array
    {
        return $this->get('/earchive/Statistic', $query)->json();
    }

    // -------------------------------------------------------------------------
    // File Upload
    // -------------------------------------------------------------------------

    /**
     * Upload a file for attachment.
     *
     * POST /earchive/FileUpload
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function uploadFile(array $data): array
    {
        return $this->post('/earchive/FileUpload', $data)->json();
    }
}
