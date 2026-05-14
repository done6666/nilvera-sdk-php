<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * Report (Rapor) API.
 * Base path: /report
 */
class ReportService extends AbstractService
{
    /**
     * List available reports.
     *
     * GET /report
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listReports(array $query = []): array
    {
        return $this->get('/report', $query)->json();
    }

    /**
     * Get a specific report by ID.
     *
     * GET /report/{id}
     *
     * @return array<string, mixed>
     */
    public function getReport(string $id): array
    {
        return $this->get("/report/{$id}")->json();
    }

    /**
     * Generate and download a report.
     *
     * POST /report/{id}/Generate
     *
     * @param array<string, mixed> $parameters
     * @return array<string, mixed>
     */
    public function generateReport(string $id, array $parameters = []): array
    {
        return $this->post("/report/{$id}/Generate", $parameters)->json();
    }
}
