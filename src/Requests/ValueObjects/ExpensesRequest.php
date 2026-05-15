<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Enums\ExpenseType;
use Nilvera\Requests\AbstractRequest;

/**
 * HKS fatura masrafı — API'deki ExpensesDto.
 *
 * Yalnızca InvoiceType::HKSSales veya InvoiceType::HKSCommissioner faturalarında kullanılır.
 * Percent veya Amount alanlarından en az biri girilmelidir.
 */
readonly class ExpensesRequest extends AbstractRequest
{
    public function __construct(
        public ExpenseType $expenseType,
        public float $percent = 0.0,
        public float $amount = 0.0,
    ) {
        if ($this->percent < 0.0) {
            throw new \InvalidArgumentException('Masraf oranı (Percent) negatif olamaz.');
        }

        if ($this->amount < 0.0) {
            throw new \InvalidArgumentException('Masraf tutarı (Amount) negatif olamaz.');
        }
    }

    public function toArray(): array
    {
        return [
            'ExpenseType' => $this->expenseType->value,
            'Percent'     => $this->percent,
            'Amount'      => $this->amount,
        ];
    }
}
