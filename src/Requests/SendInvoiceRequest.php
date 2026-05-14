<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

/**
 * e-Fatura gonderme istegi — POST /einvoice/Send/Model
 *
 * Uretilen toArray() ciktisi asagidaki ust duzey JSON yapisini olusturur:
 * {
 *   "CustomerAlias": "urn:mail:...",   (kayitli e-fatura alicilari icin)
 *   "InvoiceInfo":   { ... },
 *   "CustomerInfo":  { ... },
 *   "InvoiceLines":  [ ... ],
 *   "Notes":         [ "..." ]
 * }
 *
 * Ornek kullanim:
 *   $req = new SendInvoiceRequest(
 *       customerInfo: new ReceiverRequest(
 *           taxNumber: '3230456015',
 *           name:      'ABC Yazilim A.S.',
 *           taxOffice: 'Kadikoy',
 *           address:   'Ataturk Cad. No:1',
 *           district:  'Kadikoy',
 *           city:      'Istanbul',
 *       ),
 *       invoiceLines: [
 *           InvoiceLineRequest::make('Yazilim Lisansi', 1, UnitType::Piece, 10000, 20),
 *       ],
 *       issueDate:     new \DateTimeImmutable('2026-05-14T10:00:00'),
 *       customerAlias: 'urn:mail:muhasebe@abc.com.tr',
 *   );
 */
readonly class SendInvoiceRequest extends AbstractRequest
{
    /**
     * @param InvoiceLineRequest[]                      $invoiceLines En az bir kalem zorunludur
     * @param string[]                                  $notes        Fatura notlari
     * @param array<array{IssueDate:string,Value:string}> $orderReference  Siparis referanslari
     * @param array<array{IssueDate:string,Value:string}> $despatchDocumentReference  Irsaliye referanslari
     */
    public function __construct(
        public ReceiverRequest $customerInfo,
        public array $invoiceLines,
        public \DateTimeImmutable $issueDate,
        public InvoiceProfile $invoiceProfile = InvoiceProfile::Basic,
        public InvoiceType $invoiceType = InvoiceType::Sales,
        /** ISO 4217 para birimi kodu */
        public string $currencyCode = 'TRY',
        /** GIB sisteminde kayitli alici icin e-posta veya ETTN alias — ihracat icin bos birakin */
        public ?string $customerAlias = null,
        /** TRY disindaki para birimlerinde doviz kuru */
        public ?float $exchangeRate = null,
        public array $notes = [],
        /** 16 haneli fatura numarasi veya 3 haneli seri kodu */
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
            'UUID'                       => $this->uuid,
            'TemplateUUID'               => $this->templateUuid,
            'TemplateBase64String'       => $this->templateBase64String,
            'InvoiceType'                => $this->invoiceType->value,
            'InvoiceProfile'             => $this->invoiceProfile->value,
            'InvoiceSerieOrNumber'       => $this->invoiceSerieOrNumber,
            'IssueDate'                  => $this->issueDate->format('Y-m-d\TH:i:s\Z'),
            'CurrencyCode'               => $this->currencyCode,
            'ExchangeRate'               => $this->exchangeRate,
            'OrderReference'             => $this->orderReference !== [] ? $this->orderReference : null,
            'DespatchDocumentReference'  => $this->despatchDocumentReference !== [] ? $this->despatchDocumentReference : null,
        ]);

        $eInvoice = [
            'InvoiceInfo'  => $invoiceInfo,
            'CustomerInfo' => $this->customerInfo->toArray(),
            'InvoiceLines' => array_map(
                static fn (InvoiceLineRequest $l) => $l->toArray(),
                $this->invoiceLines,
            ),
        ];

        if ($this->notes !== []) {
            $eInvoice['Notes'] = $this->notes;
        }

        $payload = ['EInvoice' => $eInvoice];

        if ($this->customerAlias !== null) {
            $payload['CustomerAlias'] = $this->customerAlias;
        }

        return $payload;
    }
}
