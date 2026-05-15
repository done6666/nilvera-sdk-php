<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * Report (Rapor) API — Muhasebe (Accounting) Reports.
 * Base path: /report
 */
class ReportService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Accounting Reports
    // -------------------------------------------------------------------------

    /**
     * GET /report/Accounting
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReports(array $query = []): array
    {
        return $this->get('/report/Accounting', $query)->json();
    }

    /**
     * POST /report/Accounting
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createReport(array $data): array
    {
        return $this->post('/report/Accounting', $data)->json();
    }

    /**
     * POST /report/Accounting/{uuid}/download
     */
    public function downloadReport(string $uuid): string
    {
        return $this->post("/report/Accounting/{$uuid}/download")->getBody();
    }

    // -------------------------------------------------------------------------
    // Report Templates
    // -------------------------------------------------------------------------

    /**
     * GET /report/Accounting/Template
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReportTemplates(array $query = []): array
    {
        return $this->get('/report/Accounting/Template', $query)->json();
    }

    /**
     * POST /report/Accounting/Template
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createReportTemplate(array $data): array
    {
        return $this->post('/report/Accounting/Template', $data)->json();
    }

    /**
     * PUT /report/Accounting/Template
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateReportTemplate(array $data): array
    {
        return $this->put('/report/Accounting/Template', $data)->json();
    }

    /**
     * DELETE /report/Accounting/Template/{id}
     */
    public function deleteReportTemplate(int $id): void
    {
        $this->delete("/report/Accounting/Template/{$id}");
    }

    // -------------------------------------------------------------------------
    // Template Columns
    // -------------------------------------------------------------------------

    /**
     * GET /report/Accounting/Columns/{type}/{reportType}
     *
     * @return array<string, mixed>
     */
    public function listReportTemplateColumns(int $type, string $reportType): array
    {
        return $this->get("/report/Accounting/Columns/{$type}/{$reportType}")->json();
    }
}
