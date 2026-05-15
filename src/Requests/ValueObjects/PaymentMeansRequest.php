<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Ödeme şekli bilgileri — API'deki PaymentMeansDto.
 *
 * Code:                   Ödeme şekli kodu (Nilvera Ödeme Şekli Kodları listesine bakın).
 * ChannelCode:            Ödeme kanalı kodu.
 * DueDate:                Son ödeme tarihi.
 * PayeeFinancialAccountId: Ödeme yapılacak hesap bilgisi (IBAN vb.).
 * Note:                   Ödemeye ilişkin açıklama.
 */
readonly class PaymentMeansRequest extends AbstractRequest
{
    public function __construct(
        public ?string $code = null,
        public ?string $channelCode = null,
        public ?\DateTimeImmutable $dueDate = null,
        public ?string $payeeFinancialAccountId = null,
        public ?string $note = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'Code'                    => $this->code,
            'ChannelCode'             => $this->channelCode,
            'DueDate'                 => $this->dueDate?->format('Y-m-d\TH:i:s\Z'),
            'PayeeFinancialAccountID' => $this->payeeFinancialAccountId,
            'Note'                    => $this->note,
        ]);
    }
}
