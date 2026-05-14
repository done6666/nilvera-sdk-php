<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Requests;

use Nilvera\Enums\UnitType;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\TaxRequest;
use PHPUnit\Framework\TestCase;

class InvoiceLineRequestTest extends TestCase
{
    // -------------------------------------------------------------------------
    // make() factory — KDV hesaplama
    // -------------------------------------------------------------------------

    public function test_make_calculates_kdv_total(): void
    {
        $line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20);

        $this->assertSame(200.0, $line->kdvTotal);
    }

    public function test_make_calculates_kdv_total_with_allowance(): void
    {
        // (1 * 1000) - 100 = 900; KDV = 900 * 0.20 = 180
        $line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20, allowanceTotal: 100.0);

        $this->assertSame(180.0, $line->kdvTotal);
        $this->assertSame(100.0, $line->allowanceTotal);
    }

    public function test_make_calculates_allowance_from_percent(): void
    {
        // 10% of (1 * 1000) = 100
        $line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20, allowancePercent: 10.0);

        $this->assertSame(100.0, $line->allowanceTotal);
    }

    public function test_make_calculates_kdv_for_multi_quantity(): void
    {
        // 3 * 500 = 1500; KDV = 1500 * 0.10 = 150
        $line = InvoiceLineRequest::make('Hizmet', 3, UnitType::Piece, 500, 10);

        $this->assertSame(150.0, $line->kdvTotal);
    }

    public function test_make_zero_kdv_percent(): void
    {
        $line = InvoiceLineRequest::make('Istisna Urun', 1, UnitType::Piece, 5000, 0);

        $this->assertSame(0.0, $line->kdvTotal);
        $this->assertSame(0.0, $line->kdvPercent);
    }

    public function test_make_accepts_string_unit_type(): void
    {
        $line = InvoiceLineRequest::make('Urun', 1, 'C62', 100, 20);

        $this->assertSame('C62', $line->unitType);
    }

    // -------------------------------------------------------------------------
    // toArray() structure
    // -------------------------------------------------------------------------

    public function test_to_array_price_is_string(): void
    {
        $line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000.5, 20);

        $this->assertSame('1000.5', $line->toArray()['Price']);
    }

    public function test_to_array_unit_type_enum_is_value(): void
    {
        $line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 100, 20);

        $this->assertSame(UnitType::Piece->value, $line->toArray()['UnitType']);
    }

    public function test_to_array_includes_taxes_when_set(): void
    {
        $tax  = new TaxRequest(taxCode: '0015', total: 10.0, percent: 10.0);
        $line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 100, 20, taxes: [$tax]);

        $data = $line->toArray();
        $this->assertArrayHasKey('Taxes', $data);
        $this->assertCount(1, $data['Taxes']);
    }

    public function test_to_array_omits_taxes_when_empty(): void
    {
        $line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 100, 20);

        $this->assertArrayNotHasKey('Taxes', $line->toArray());
    }

    public function test_to_array_omits_null_optional_fields(): void
    {
        $line = InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 100, 20);
        $data = $line->toArray();

        $this->assertArrayNotHasKey('SellerCode', $data);
        $this->assertArrayNotHasKey('Description', $data);
        $this->assertArrayNotHasKey('GTIPNo', $data);
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

    public function test_throws_on_empty_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InvoiceLineRequest::make('', 1, UnitType::Piece, 100, 20);
    }

    public function test_throws_on_zero_quantity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InvoiceLineRequest::make('Urun', 0, UnitType::Piece, 100, 20);
    }

    public function test_throws_on_negative_price(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InvoiceLineRequest::make('Urun', 1, UnitType::Piece, -1.0, 20);
    }

    public function test_throws_on_invalid_kdv_percent(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/KDVPercent/');
        InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 100, 15);
    }

    public function test_throws_when_taxes_array_contains_non_tax_request(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new InvoiceLineRequest(
            name: 'Urun',
            quantity: 1,
            unitType: UnitType::Piece,
            price: 100,
            allowanceTotal: 0,
            kdvPercent: 20,
            kdvTotal: 20,
            taxes: ['not-a-tax-request'],
        );
    }
}
