<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\VoucherLineRequest;

/**
 * e-SMM (Serbest Meslek Makbuzu) gonderme istegi — POST /evoucher/Send/Model
 *
 * SendType: KAGIT | ELEKTRONIK
 */
readonly class SendVoucherRequest extends AbstractRequest
{
    /**
     * @param VoucherLineRequest[] $voucherLines
     * @param string[]             $notes
     */
    public function __construct(
        public ReceiverRequest $customerInfo,
        public array $voucherLines,
        public \DateTimeImmutable $issueDate,
        public string $currencyCode = 'TRY',
        public ?float $exchangeRate = null,
        /** KAGIT veya ELEKTRONIK */
        public string $sendType = 'ELEKTRONIK',
        public ?string $voucherSerieOrNumber = null,
        public ?string $uuid = null,
        public ?string $templateUuid = null,
        public array $notes = [],
    ) {
        if ($this->voucherLines === []) {
            throw new \InvalidArgumentException('Makbuzda en az bir kalem bulunmalidir.');
        }

        foreach ($this->voucherLines as $i => $line) {
            if (!$line instanceof VoucherLineRequest) {
                throw new \InvalidArgumentException(
                    "voucherLines[{$i}] bir VoucherLineRequest nesnesi olmalidir."
                );
            }
        }

        if (!in_array($this->sendType, ['KAGIT', 'ELEKTRONIK'], true)) {
            throw new \InvalidArgumentException("SendType 'KAGIT' veya 'ELEKTRONIK' olmalidir.");
        }
    }

    public function toArray(): array
    {
        $voucherInfo = $this->filterNulls([
            'UUID'                 => $this->uuid,
            'TemplateUUID'         => $this->templateUuid,
            'VoucherSerieOrNumber' => $this->voucherSerieOrNumber,
            'IssueDate'            => $this->issueDate->format('Y-m-d\TH:i:s\Z'),
            'CurrencyCode'         => $this->currencyCode,
            'ExchangeRate'         => $this->exchangeRate,
            'SendType'             => $this->sendType,
        ]);

        $payload = [
            'VoucherInfo'  => $voucherInfo,
            'CustomerInfo' => $this->customerInfo->toArray(),
            'VoucherLines' => array_map(
                static fn (VoucherLineRequest $l) => $l->toArray(),
                $this->voucherLines,
            ),
        ];

        if ($this->notes !== []) {
            $payload['Notes'] = $this->notes;
        }

        return $payload;
    }
}
