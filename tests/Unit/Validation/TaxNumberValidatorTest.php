<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Validation;

use Nilvera\Validation\TaxNumberValidator;
use PHPUnit\Framework\TestCase;

class TaxNumberValidatorTest extends TestCase
{
    // -------------------------------------------------------------------------
    // VKN
    // -------------------------------------------------------------------------

    /** @dataProvider validVknProvider */
    public function test_valid_vkn_returns_true(string $vkn): void
    {
        $this->assertTrue(TaxNumberValidator::validateVkn($vkn));
    }

    public static function validVknProvider(): array
    {
        return [
            'all-zeros-checksum' => ['1234567890'],
            'computed-valid'     => ['0000000009'],
        ];
    }

    /** @dataProvider invalidVknProvider */
    public function test_invalid_vkn_returns_false(string $vkn): void
    {
        $this->assertFalse(TaxNumberValidator::validateVkn($vkn));
    }

    public static function invalidVknProvider(): array
    {
        return [
            'wrong-checksum'    => ['1234567891'],
            'too-short'         => ['123456789'],
            'too-long'          => ['12345678901'],
            'contains-letters'  => ['123456789A'],
            'empty-string'      => [''],
        ];
    }

    // -------------------------------------------------------------------------
    // TCKN
    // -------------------------------------------------------------------------

    /** @dataProvider validTcknProvider */
    public function test_valid_tckn_returns_true(string $tckn): void
    {
        $this->assertTrue(TaxNumberValidator::validateTckn($tckn));
    }

    public static function validTcknProvider(): array
    {
        return [
            'known-valid' => ['12345678950'],
            'other-valid' => ['10000000146'],
        ];
    }

    /** @dataProvider invalidTcknProvider */
    public function test_invalid_tckn_returns_false(string $tckn): void
    {
        $this->assertFalse(TaxNumberValidator::validateTckn($tckn));
    }

    public static function invalidTcknProvider(): array
    {
        return [
            'wrong-checksum'    => ['12345678951'],
            'starts-with-zero'  => ['01234567890'],
            'too-short'         => ['1234567890'],
            'too-long'          => ['123456789501'],
            'contains-letters'  => ['1234567895A'],
        ];
    }

    // -------------------------------------------------------------------------
    // assertValid
    // -------------------------------------------------------------------------

    public function test_assert_valid_passes_for_valid_vkn(): void
    {
        $this->expectNotToPerformAssertions();
        TaxNumberValidator::assertValid('1234567890');
    }

    public function test_assert_valid_passes_for_valid_tckn(): void
    {
        $this->expectNotToPerformAssertions();
        TaxNumberValidator::assertValid('12345678950');
    }

    public function test_assert_valid_throws_for_wrong_length(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/10 haneli.*11 haneli/');
        TaxNumberValidator::assertValid('123456789');
    }

    public function test_assert_valid_throws_for_non_numeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/yalnizca rakam/');
        TaxNumberValidator::assertValid('ABC1234567');
    }

    public function test_assert_valid_throws_for_invalid_vkn_checksum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/VKN/');
        TaxNumberValidator::assertValid('1234567891');
    }

    public function test_assert_valid_throws_for_invalid_tckn_checksum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/TCKN/');
        TaxNumberValidator::assertValid('12345678951');
    }
}
