<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

/**
 * e-Arsiv fatura gonderme istegi — POST /earchive/Send/Model
 *
 * e-Arsiv faturalar GIB sistemine degil dogrudan aliciya iletilir;
 * bu nedenle CustomerAlias zorunlu degildir.
 * InvoiceProfile her zaman InvoiceProfile::EArchive (EARSIVFATURA) olmalidir.
 *
 * JSON ciktisi yapisi:
 * {
 *   "InvoiceInfo":   { ... },
 *   "CustomerInfo":  { ... },
 *   "InvoiceLines":  [ ... ],
 *   "Notes":         [ "..." ]
 * }
 */
readonly class SendArchiveInvoiceRequest extends AbstractRequest
{
    /**
     * @param InvoiceLineRequest[]                        $invoiceLines En az bir kalem zorunludur
     * @param string[]                                    $notes
     * @param array<array{IssueDate:string,Value:string}> $orderReference
     * @param array<array{IssueDate:string,Value:string}> $despatchDocumentReference
     */
    public function __construct(
        public ReceiverRequest $customerInfo,
        public array $invoiceLines,
        public \DateTimeImmutable $issueDate,
        public InvoiceType $invoiceType = InvoiceType::Sales,
        public string $currencyCode = 'TRY',
        public ?float $exchangeRate = null,
        public array $notes = [],
        public ?string $invoiceSerieOrNumber = null,
        public ?string $uuid = null,
        public ?string $templateUuid = null,
        public ?string $templateBase64String = null,
        public array $orderReference = [],
        public array $despatchDocumentReference = [],
    ) {
        if ($this->invoiceLines === []) {
            throw new \InvalidArgumentException('Faturada en az bir kalem (InvoiceLines) bulunmalidir.');
        }

        foreach ($this->invoiceLines as $i => $line) {
            if (!$line instanceof InvoiceLineRequest) {
                throw new \InvalidArgumentException(
                    "invoiceLines[{$i}] bir InvoiceLineRequest nesnesi olmalidir."
                );
            }
        }

        if ($this->exchangeRate !== null && $this->exchangeRate <= 0.0) {
            throw new \InvalidArgumentException('Doviz kuru (ExchangeRate) sifirdan buyuk olmalidir.');
        }
    }

    public function toArray(): array
    {
        $invoiceInfo = $this->filterNulls([
            'UUID'                      => $this->uuid,
            'TemplateUUID'              => $this->templateUuid,
            'TemplateBase64String'      => $this->templateBase64String,
            'InvoiceType'               => $this->invoiceType->value,
            'InvoiceProfile'            => InvoiceProfile::EArchive->value,
            'InvoiceSerieOrNumber'      => $this->invoiceSerieOrNumber,
            'IssueDate'                 => $this->issueDate->format('Y-m-d\TH:i:s\Z'),
            'CurrencyCode'              => $this->currencyCode,
            'ExchangeRate'              => $this->exchangeRate,
            'OrderReference'            => $this->orderReference !== [] ? $this->orderReference : null,
            'DespatchDocumentReference' => $this->despatchDocumentReference !== [] ? $this->despatchDocumentReference : null,
        ]);

        $payload = [
            'InvoiceInfo'  => $invoiceInfo,
            'CustomerInfo' => $this->customerInfo->toArray(),
            'InvoiceLines' => array_map(
                static fn (InvoiceLineRequest $l) => $l->toArray(),
                $this->invoiceLines,
            ),
        ];

        if ($this->notes !== []) {
            $payload['Notes'] = $this->notes;
        }

        return $payload;
    }
}
