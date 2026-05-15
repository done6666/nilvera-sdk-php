<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Ödeme koşulları bilgisi — API'deki PaymentTermsDto.
 *
 * Percent: Ödemenin gecikmesi durumunda uygulanacak ceza oranı.
 * Amount:  Ödeme tutarı.
 * Note:    Ödeme koşullarına ilişkin açıklama.
 */
readonly class PaymentTermsRequest extends AbstractRequest
{
    public function __construct(
        public ?float $percent = null,
        public ?float $amount = null,
        public ?string $note = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'Percent' => $this->percent,
            'Amount'  => $this->amount,
            'Note'    => $this->note,
        ]);
    }
}
