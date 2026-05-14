<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Builders;

use Nilvera\Builders\InvoiceBuilder;
use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use PHPUnit\Framework\TestCase;

class InvoiceBuilderTest extends TestCase
{
    private ReceiverRequest $receiver;

    protected function setUp(): void
    {
        $this->receiver = new ReceiverRequest(
            taxNumber: '1234567890',
            name:      'Alici Sirket A.S.',
            address:   'Ornek Mah. No:5',
            district:  'Besiktas',
            city:      'Istanbul',
        );
    }

    private function builderWithOneLine(): InvoiceBuilder
    {
        return InvoiceBuilder::for($this->receiver)
            ->addLine('Urun', 1, UnitType::Piece, 1000, 20);
    }

    // -------------------------------------------------------------------------
    // build() returns correct type
    // -------------------------------------------------------------------------

    public function test_build_returns_send_invoice_request(): void
    {
        $result = $this->builderWithOneLine()->build();

        $this->assertInstanceOf(SendInvoiceRequest::class, $result);
    }

    public function test_build_throws_when_no_lines_added(): void
    {
        $this->expectException(\LogicException::class);
        InvoiceBuilder::for($this->receiver)->build();
    }

    // -------------------------------------------------------------------------
    // Default values
    // -------------------------------------------------------------------------

    public function test_defaults_to_basic_profile(): void
    {
        $request = $this->builderWithOneLine()->build();

        $this->assertSame(InvoiceProfile::Basic, $request->invoiceProfile);
    }

    public function test_defaults_to_sales_type(): void
    {
        $request = $this->builderWithOneLine()->build();

        $this->assertSame(InvoiceType::Sales, $request->invoiceType);
    }

    public function test_defaults_currency_to_try(): void
    {
        $request = $this->builderWithOneLine()->build();

        $this->assertSame('TRY', $request->currencyCode);
    }

    // -------------------------------------------------------------------------
    // Fluent setters
    // -------------------------------------------------------------------------

    public function test_profile_sets_invoice_profile(): void
    {
        $request = $this->builderWithOneLine()
            ->profile(InvoiceProfile::Commercial)
            ->build();

        $this->assertSame(InvoiceProfile::Commercial, $request->invoiceProfile);
    }

    public function test_type_sets_invoice_type(): void
    {
        $request = $this->builderWithOneLine()
            ->type(InvoiceType::Return)
            ->build();

        $this->assertSame(InvoiceType::Return, $request->invoiceType);
    }

    public function test_alias_sets_customer_alias(): void
    {
        $request = $this->builderWithOneLine()
            ->alias('urn:mail:test@sirket.com.tr')
            ->build();

        $this->assertSame('urn:mail:test@sirket.com.tr', $request->customerAlias);
    }

    public function test_currency_sets_code_and_exchange_rate(): void
    {
        $request = $this->builderWithOneLine()
            ->currency('USD', 32.5)
            ->build();

        $this->assertSame('USD', $request->currencyCode);
        $this->assertSame(32.5, $request->exchangeRate);
    }

    public function test_note_adds_to_notes_array(): void
    {
        $request = $this->builderWithOneLine()
            ->note('Odeme vadesi: 30 gun')
            ->note('KDV dahildir')
            ->build();

        $this->assertCount(2, $request->notes);
        $this->assertContains('Odeme vadesi: 30 gun', $request->notes);
        $this->assertContains('KDV dahildir', $request->notes);
    }

    public function test_issue_date_is_applied(): void
    {
        $date    = new \DateTimeImmutable('2026-01-01T09:00:00');
        $request = $this->builderWithOneLine()
            ->issueDate($date)
            ->build();

        $this->assertSame('2026-01-01T09:00:00Z', $request->issueDate->format('Y-m-d\TH:i:s\Z'));
    }

    public function test_serie_or_number_is_applied(): void
    {
        $request = $this->builderWithOneLine()
            ->serieOrNumber('ABC2026000000001')
            ->build();

        $this->assertSame('ABC2026000000001', $request->invoiceSerieOrNumber);
    }

    public function test_order_reference_is_appended(): void
    {
        $request = $this->builderWithOneLine()
            ->orderReference('2026-01-01', 'PO-001')
            ->orderReference('2026-01-02', 'PO-002')
            ->build();

        $this->assertCount(2, $request->orderReference);
        $this->assertSame('PO-001', $request->orderReference[0]['Value']);
    }

    // -------------------------------------------------------------------------
    // Immutability
    // -------------------------------------------------------------------------

    public function test_each_method_returns_new_instance(): void
    {
        $original = InvoiceBuilder::for($this->receiver);
        $modified = $original->profile(InvoiceProfile::Commercial);

        $this->assertNotSame($original, $modified);
    }

    public function test_add_line_does_not_mutate_original(): void
    {
        $base     = InvoiceBuilder::for($this->receiver);
        $withLine = $base->addLine('Urun', 1, UnitType::Piece, 100, 20);

        // original has no lines, so build() must throw
        $this->expectException(\LogicException::class);
        $base->build();
    }

    // -------------------------------------------------------------------------
    // addLineRequest
    // -------------------------------------------------------------------------

    public function test_add_line_request_accepts_pre_built_line(): void
    {
        $line    = InvoiceLineRequest::make('Ozel Urun', 1, UnitType::Piece, 5000, 20);
        $request = InvoiceBuilder::for($this->receiver)
            ->addLineRequest($line)
            ->build();

        $this->assertCount(1, $request->invoiceLines);
        $this->assertSame('Ozel Urun', $request->invoiceLines[0]->name);
    }

    public function test_multiple_add_line_calls_accumulate(): void
    {
        $request = InvoiceBuilder::for($this->receiver)
            ->addLine('Urun A', 1, UnitType::Piece, 100, 20)
            ->addLine('Urun B', 2, UnitType::Piece, 200, 10)
            ->addLine('Urun C', 3, UnitType::Piece, 300, 0)
            ->build();

        $this->assertCount(3, $request->invoiceLines);
    }
}
