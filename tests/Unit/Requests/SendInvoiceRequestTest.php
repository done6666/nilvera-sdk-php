<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Requests;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use PHPUnit\Framework\TestCase;

class SendInvoiceRequestTest extends TestCase
{
    private ReceiverRequest $receiver;
    private InvoiceLineRequest $line;

    protected function setUp(): void
    {
        $this->receiver = new ReceiverRequest(
            taxNumber: '1234567890',
            name:      'Test Sirket',
            address:   'Test Mah. No:1',
            district:  'Kadikoy',
            city:      'Istanbul',
        );

        $this->line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20);
    }

    private function makeRequest(array $overrides = []): SendInvoiceRequest
    {
        return new SendInvoiceRequest(...array_merge([
            'customerInfo' => $this->receiver,
            'invoiceLines' => [$this->line],
            'issueDate'    => new \DateTimeImmutable('2026-05-14T10:00:00'),
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // Top-level structure
    // -------------------------------------------------------------------------

    public function test_to_array_has_required_top_level_keys(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertArrayHasKey('InvoiceInfo', $data);
        $this->assertArrayHasKey('CustomerInfo', $data);
        $this->assertArrayHasKey('InvoiceLines', $data);
    }

    public function test_to_array_omits_customer_alias_when_null(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertArrayNotHasKey('CustomerAlias', $data);
    }

    public function test_to_array_includes_customer_alias_when_set(): void
    {
        $data = $this->makeRequest([
            'customerAlias' => 'urn:mail:muhasebe@sirket.com.tr',
        ])->toArray();

        $this->assertSame('urn:mail:muhasebe@sirket.com.tr', $data['CustomerAlias']);
    }

    public function test_to_array_omits_notes_when_empty(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertArrayNotHasKey('Notes', $data);
    }

    public function test_to_array_includes_notes_when_set(): void
    {
        $data = $this->makeRequest(['notes' => ['30 gun vadeli']])->toArray();

        $this->assertSame(['30 gun vadeli'], $data['Notes']);
    }

    // -------------------------------------------------------------------------
    // InvoiceInfo block
    // -------------------------------------------------------------------------

    public function test_invoice_info_has_correct_defaults(): void
    {
        $info = $this->makeRequest()->toArray()['InvoiceInfo'];

        $this->assertSame(InvoiceType::Sales->value, $info['InvoiceType']);
        $this->assertSame(InvoiceProfile::Basic->value, $info['InvoiceProfile']);
        $this->assertSame('TRY', $info['CurrencyCode']);
    }

    public function test_invoice_info_issue_date_is_iso8601(): void
    {
        $info = $this->makeRequest([
            'issueDate' => new \DateTimeImmutable('2026-05-14T10:00:00'),
        ])->toArray()['InvoiceInfo'];

        $this->assertSame('2026-05-14T10:00:00Z', $info['IssueDate']);
    }

    public function test_invoice_info_omits_uuid_when_null(): void
    {
        $info = $this->makeRequest()->toArray()['InvoiceInfo'];

        $this->assertArrayNotHasKey('UUID', $info);
    }

    public function test_invoice_info_includes_uuid_when_set(): void
    {
        $uuid = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
        $info = $this->makeRequest(['uuid' => $uuid])->toArray()['InvoiceInfo'];

        $this->assertSame($uuid, $info['UUID']);
    }

    public function test_invoice_info_includes_exchange_rate_when_set(): void
    {
        $info = $this->makeRequest([
            'currencyCode' => 'USD',
            'exchangeRate' => 32.5,
        ])->toArray()['InvoiceInfo'];

        $this->assertSame('USD', $info['CurrencyCode']);
        $this->assertSame(32.5, $info['ExchangeRate']);
    }

    // -------------------------------------------------------------------------
    // InvoiceLines block
    // -------------------------------------------------------------------------

    public function test_invoice_lines_are_mapped_to_array(): void
    {
        $data = $this->makeRequest()->toArray();

        $this->assertCount(1, $data['InvoiceLines']);
        $this->assertSame('Urun', $data['InvoiceLines'][0]['Name']);
    }

    public function test_multiple_lines_are_included(): void
    {
        $line2 = InvoiceLineRequest::make('Hizmet', 2, UnitType::Piece, 500, 10);
        $data  = $this->makeRequest(['invoiceLines' => [$this->line, $line2]])->toArray();

        $this->assertCount(2, $data['InvoiceLines']);
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

    public function test_throws_when_invoice_lines_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/InvoiceLines/');
        $this->makeRequest(['invoiceLines' => []]);
    }

    public function test_throws_when_line_is_not_invoice_line_request(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeRequest(['invoiceLines' => ['not-a-line']]);
    }

    public function test_throws_when_exchange_rate_is_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ExchangeRate/');
        $this->makeRequest(['exchangeRate' => 0.0]);
    }
}
