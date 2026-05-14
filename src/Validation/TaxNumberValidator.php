<?php

declare(strict_types=1);

namespace Nilvera\Validation;

/**
 * Turkiye Vergi Kimlik Numarasi (VKN) ve TC Kimlik No (TCKN) dogrulayici.
 *
 * VKN algoritmasini GIB resmi algoritmasi kullanir.
 * TCKN algoritmasini Nufus Mudurlugu mod-10 kurali kullanir.
 */
final class TaxNumberValidator
{
    /**
     * 10 haneli VKN icin checksum dogrulama (GIB algoritmasi).
     */
    public static function validateVkn(string $vkn): bool
    {
        if (!ctype_digit($vkn) || strlen($vkn) !== 10) {
            return false;
        }

        $digits = array_map('intval', str_split($vkn));
        $sum    = 0;

        for ($i = 0; $i < 9; $i++) {
            $v = ($digits[$i] + (9 - $i)) % 10;

            if ($v !== 0) {
                $k    = ($v * (int) (2 ** (9 - $i))) % 9;
                $sum += ($k === 0) ? 9 : $k;
            }
        }

        return ($sum % 10) === $digits[9];
    }

    /**
     * 11 haneli TCKN icin mod-10 dogrulama.
     */
    public static function validateTckn(string $tckn): bool
    {
        if (!ctype_digit($tckn) || strlen($tckn) !== 11) {
            return false;
        }

        $d = array_map('intval', str_split($tckn));

        // Birinci hane 0 olamaz
        if ($d[0] === 0) {
            return false;
        }

        // 10. hane kontrolu
        $oddSum  = $d[0] + $d[2] + $d[4] + $d[6] + $d[8];
        $evenSum = $d[1] + $d[3] + $d[5] + $d[7];

        if ((($oddSum * 7) - $evenSum) % 10 !== $d[9]) {
            return false;
        }

        // 11. hane kontrolu
        $total = array_sum(array_slice($d, 0, 10));

        return $total % 10 === $d[10];
    }

    /**
     * Hangi tipte oldugunu otomatik algilar (10 hane → VKN, 11 hane → TCKN).
     *
     * @throws \InvalidArgumentException Algoritma dogrulamasinda basarisiz olursa
     */
    public static function assertValid(string $taxNumber): void
    {
        if (!ctype_digit($taxNumber)) {
            throw new \InvalidArgumentException('TaxNumber yalnizca rakam icermelidir.');
        }

        $len = strlen($taxNumber);

        if ($len === 10) {
            if (!self::validateVkn($taxNumber)) {
                throw new \InvalidArgumentException(
                    "'{$taxNumber}' gecerli bir VKN (Vergi Kimlik Numarasi) degil."
                );
            }
            return;
        }

        if ($len === 11) {
            if (!self::validateTckn($taxNumber)) {
                throw new \InvalidArgumentException(
                    "'{$taxNumber}' gecerli bir TCKN (TC Kimlik Numarasi) degil."
                );
            }
            return;
        }

        throw new \InvalidArgumentException(
            "TaxNumber 10 haneli VKN veya 11 haneli TCKN olmalidir; {$len} hane girildi."
        );
    }
}
