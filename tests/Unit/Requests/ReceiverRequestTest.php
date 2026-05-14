<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Requests;

use Nilvera\Requests\ValueObjects\ReceiverRequest;
use PHPUnit\Framework\TestCase;

class ReceiverRequestTest extends TestCase
{
    private function makeValid(array $overrides = []): ReceiverRequest
    {
        return new ReceiverRequest(...array_merge([
            'taxNumber' => '1234567890',
            'name'      => 'Test Sirket A.S.',
            'address'   => 'Test Caddesi No:1',
            'district'  => 'Kadikoy',
            'city'      => 'Istanbul',
        ], $overrides));
    }

    public function test_to_array_contains_required_keys(): void
    {
        $receiver = $this->makeValid();
        $data = $receiver->toArray();

        $this->assertArrayHasKey('TaxNumber', $data);
        $this->assertArrayHasKey('Name', $data);
        $this->assertArrayHasKey('Address', $data);
        $this->assertArrayHasKey('District', $data);
        $this->assertArrayHasKey('City', $data);
        $this->assertArrayHasKey('Country', $data);
    }

    public function test_to_array_default_country_is_tr(): void
    {
        $receiver = $this->makeValid();
        $this->assertSame('TR', $receiver->toArray()['Country']);
    }

    public function test_to_array_omits_null_optional_fields(): void
    {
        $receiver = $this->makeValid();
        $data = $receiver->toArray();

        $this->assertArrayNotHasKey('TaxOffice', $data);
        $this->assertArrayNotHasKey('PostalCode', $data);
        $this->assertArrayNotHasKey('Phone', $data);
        $this->assertArrayNotHasKey('Mail', $data);
    }

    public function test_to_array_includes_optional_fields_when_set(): void
    {
        $receiver = $this->makeValid([
            'taxOffice'  => 'Kadikoy VD',
            'postalCode' => '34710',
            'mail'       => 'info@test.com',
        ]);
        $data = $receiver->toArray();

        $this->assertSame('Kadikoy VD', $data['TaxOffice']);
        $this->assertSame('34710', $data['PostalCode']);
        $this->assertSame('info@test.com', $data['Mail']);
    }

    public function test_to_array_includes_party_identifications_when_set(): void
    {
        $receiver = $this->makeValid([
            'partyIdentifications' => [['ID' => 'ABC', 'IDType' => 'MERSISNO']],
        ]);
        $data = $receiver->toArray();

        $this->assertArrayHasKey('PartyIdentifications', $data);
        $this->assertCount(1, $data['PartyIdentifications']);
    }

    public function test_to_array_omits_party_identifications_when_empty(): void
    {
        $receiver = $this->makeValid();
        $this->assertArrayNotHasKey('PartyIdentifications', $receiver->toArray());
    }

    public function test_throws_on_invalid_vkn(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeValid(['taxNumber' => '1234567891']);
    }

    public function test_throws_on_empty_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Name/');
        $this->makeValid(['name' => '   ']);
    }

    public function test_throws_on_empty_district(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/District/');
        $this->makeValid(['district' => '']);
    }

    public function test_throws_on_empty_city(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/City/');
        $this->makeValid(['city' => '']);
    }

    public function test_accepts_valid_tckn(): void
    {
        $this->expectNotToPerformAssertions();
        $this->makeValid(['taxNumber' => '12345678950']);
    }
}
