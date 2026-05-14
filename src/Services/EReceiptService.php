<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Adisyon (Electronic Bill/Receipt) API.
 * Base path: /eadisyon
 */
class EReceiptService extends AbstractService
{
    /**
     * List bills.
     *
     * GET /eadisyon/Bill
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listBills(array $query = []): array
    {
        return $this->get('/eadisyon/Bill', $query)->json();
    }

    /**
     * Get a single bill by UUID.
     *
     * GET /eadisyon/Bill/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getBill(string $uuid): array
    {
        return $this->get("/eadisyon/Bill/{$uuid}")->json();
    }

    /**
     * Get HTML of a bill.
     *
     * GET /eadisyon/Bill/{uuid}/Html
     */
    public function getBillHtml(string $uuid): string
    {
        return $this->get("/eadisyon/Bill/{$uuid}/Html")->getBody();
    }

    /**
     * Get PDF of a bill.
     *
     * GET /eadisyon/Bill/{uuid}/Pdf
     */
    public function getBillPdf(string $uuid): string
    {
        return $this->get("/eadisyon/Bill/{$uuid}/Pdf")->getBody();
    }

    /**
     * Send a bill via email.
     *
     * POST /eadisyon/Bill/{uuid}/Email
     *
     * @param string[] $emailAddresses
     */
    public function sendBillByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post("/eadisyon/Bill/{$uuid}/Email", ['emailAddresses' => $emailAddresses]);
    }

    /**
     * Send a bill via SMS.
     *
     * POST /eadisyon/Bill/{uuid}/Sms
     *
     * @param string[] $phoneNumbers
     */
    public function sendBillBySms(string $uuid, array $phoneNumbers): void
    {
        $this->post("/eadisyon/Bill/{$uuid}/Sms", ['phoneNumbers' => $phoneNumbers]);
    }

    /**
     * Send a bill using a structured model.
     *
     * POST /eadisyon/Send/Model
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function send(array $data): array
    {
        return $this->post('/eadisyon/Send/Model', $data)->json();
    }

    /**
     * Cancel a bill.
     *
     * DELETE /eadisyon/Bill/{uuid}
     */
    public function cancelBill(string $uuid): void
    {
        $this->delete("/eadisyon/Bill/{$uuid}");
    }
}
