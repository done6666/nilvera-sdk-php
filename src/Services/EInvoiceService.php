<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Invoice (e-Fatura) API.
 * Base path: /einvoice
 *
 * Handles outgoing (sale) invoices, incoming (purchase) invoices, and drafts.
 */
class EInvoiceService extends AbstractService
{
    // -------------------------------------------------------------------------
    // Sending Invoices
    // -------------------------------------------------------------------------

    /**
     * Send an e-invoice using a structured model.
     *
     * POST /einvoice/Send/Model
     *
     * $invoice example:
     * [
     *   'EInvoice' => [
     *     'InvoiceInfo'   => [...],
     *     'CompanyInfo'   => [...],
     *     'CustomerInfo'  => [...],
     *     'InvoiceLines'  => [...],
     *     'Notes'         => [],
     *   ],
     *   'CustomerAlias' => null,
     * ]
     *
     * @param array<string, mixed> $invoice
     * @return array{UUID: string, InvoiceNumber: ?string}
     */
    public function send(array $invoice): array
    {
        return $this->post('/einvoice/Send/Model', $invoice)->json();
    }

    /**
     * Send an e-invoice using raw UBL XML content.
     *
     * POST /einvoice/Send/Xml
     *
     * @return array{UUID: string, InvoiceNumber: ?string}
     */
    public function sendXml(string $xmlContent): array
    {
        return $this->post('/einvoice/Send/Xml', ['XmlContent' => $xmlContent])->json();
    }

    // -------------------------------------------------------------------------
    // Sale (Outgoing) Invoices
    // -------------------------------------------------------------------------

    /**
     * List outgoing (sale) invoices.
     *
     * GET /einvoice/Sale
     *
     * @param array<string, mixed> $query  Supported: page, pageSize, startDate, endDate, invoiceNumber, ...
     * @return array<string, mixed>
     */
    public function listSaleInvoices(array $query = []): array
    {
        return $this->get('/einvoice/Sale', $query)->json();
    }

    /**
     * Get a single outgoing invoice by UUID.
     *
     * GET /einvoice/Sale/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getSaleInvoice(string $uuid): array
    {
        return $this->get("/einvoice/Sale/{$uuid}")->json();
    }

    /**
     * Get the HTML representation of an outgoing invoice.
     *
     * GET /einvoice/Sale/{uuid}/Html
     */
    public function getSaleInvoiceHtml(string $uuid): string
    {
        return $this->get("/einvoice/Sale/{$uuid}/Html")->getBody();
    }

    /**
     * Get the PDF binary of an outgoing invoice.
     *
     * GET /einvoice/Sale/{uuid}/Pdf
     */
    public function getSaleInvoicePdf(string $uuid): string
    {
        return $this->get("/einvoice/Sale/{$uuid}/Pdf")->getBody();
    }

    /**
     * Get the UBL XML of an outgoing invoice.
     *
     * GET /einvoice/Sale/{uuid}/Xml
     */
    public function getSaleInvoiceXml(string $uuid): string
    {
        return $this->get("/einvoice/Sale/{$uuid}/Xml")->getBody();
    }

    /**
     * Retrieve GIB envelope information for an outgoing invoice.
     *
     * GET /einvoice/Sale/{uuid}/EnvelopeInfo
     *
     * @return array{GIBCode: ?string, GIBDescription: ?string, EnvelopeUUID: ?string}
     */
    public function getSaleInvoiceEnvelopeInfo(string $uuid): array
    {
        return $this->get("/einvoice/Sale/{$uuid}/EnvelopeInfo")->json();
    }

    /**
     * Send an outgoing invoice via email.
     *
     * POST /einvoice/Sale/{uuid}/Email
     *
     * @param string[] $emailAddresses
     */
    public function sendSaleInvoiceByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post("/einvoice/Sale/{$uuid}/Email", ['emailAddresses' => $emailAddresses]);
    }

    // -------------------------------------------------------------------------
    // Purchase (Incoming) Invoices
    // -------------------------------------------------------------------------

    /**
     * List incoming (purchase) invoices.
     *
     * GET /einvoice/Purchase
     *
     * @param array<string, mixed> $query  Supported: page, pageSize, startDate, endDate, ...
     * @return array<string, mixed>
     */
    public function listPurchaseInvoices(array $query = []): array
    {
        return $this->get('/einvoice/Purchase', $query)->json();
    }

    /**
     * Get a single incoming invoice by UUID.
     *
     * GET /einvoice/Purchase/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getPurchaseInvoice(string $uuid): array
    {
        return $this->get("/einvoice/Purchase/{$uuid}")->json();
    }

    /**
     * Get the HTML representation of an incoming invoice.
     *
     * GET /einvoice/Purchase/{uuid}/Html
     */
    public function getPurchaseInvoiceHtml(string $uuid): string
    {
        return $this->get("/einvoice/Purchase/{$uuid}/Html")->getBody();
    }

    /**
     * Get the PDF binary of an incoming invoice.
     *
     * GET /einvoice/Purchase/{uuid}/Pdf
     */
    public function getPurchaseInvoicePdf(string $uuid): string
    {
        return $this->get("/einvoice/Purchase/{$uuid}/Pdf")->getBody();
    }

    /**
     * Get the UBL XML of an incoming invoice.
     *
     * GET /einvoice/Purchase/{uuid}/Xml
     */
    public function getPurchaseInvoiceXml(string $uuid): string
    {
        return $this->get("/einvoice/Purchase/{$uuid}/Xml")->getBody();
    }

    /**
     * Mark an incoming invoice as read.
     *
     * PUT /einvoice/Purchase/{uuid}/Read
     */
    public function markPurchaseInvoiceAsRead(string $uuid): void
    {
        $this->put("/einvoice/Purchase/{$uuid}/Read");
    }

    /**
     * Create a return (irsaliye iade) invoice from an incoming invoice.
     *
     * POST /einvoice/Purchase/{uuid}/CreateReturn
     *
     * @return array{UUID: string, InvoiceNumber: ?string}
     */
    public function createReturnFromPurchaseInvoice(string $uuid): array
    {
        return $this->post("/einvoice/Purchase/{$uuid}/CreateReturn")->json();
    }

    /**
     * Send an incoming invoice via email.
     *
     * POST /einvoice/Purchase/Email/Send
     *
     * @param string[] $emailAddresses
     */
    public function sendPurchaseInvoiceByEmail(string $uuid, array $emailAddresses): void
    {
        $this->post('/einvoice/Purchase/Email/Send', [
            'UUID'           => $uuid,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    // -------------------------------------------------------------------------
    // Draft Invoices
    // -------------------------------------------------------------------------

    /**
     * List draft invoices.
     *
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
     * Get a single draft invoice by UUID.
     *
     * GET /einvoice/Draft/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getDraft(string $uuid): array
    {
        return $this->get("/einvoice/Draft/{$uuid}")->json();
    }

    /**
     * Create a new draft invoice.
     *
     * POST /einvoice/Draft
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createDraft(array $data): array
    {
        return $this->post('/einvoice/Draft', $data)->json();
    }

    /**
     * Update an existing draft invoice.
     *
     * PUT /einvoice/Draft/{uuid}
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateDraft(string $uuid, array $data): array
    {
        return $this->put("/einvoice/Draft/{$uuid}", $data)->json();
    }

    /**
     * Delete a draft invoice.
     *
     * DELETE /einvoice/Draft/{uuid}
     */
    public function deleteDraft(string $uuid): void
    {
        $this->delete("/einvoice/Draft/{$uuid}");
    }

    /**
     * Send a draft invoice (convert draft to actual invoice).
     *
     * POST /einvoice/Draft/{uuid}/Send
     *
     * @return array{UUID: string, InvoiceNumber: ?string}
     */
    public function sendDraft(string $uuid): array
    {
        return $this->post("/einvoice/Draft/{$uuid}/Send")->json();
    }
}
