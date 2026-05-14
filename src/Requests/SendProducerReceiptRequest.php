<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Requests\ValueObjects\ProducerLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

/**
 * e-MM (Mustahsil Makbuzu) gonderme istegi — POST /eproducer/Send/Model
 */
readonly class SendProducerReceiptRequest extends AbstractRequest
{
    /**
     * @param ProducerLineRequest[] $producerLines
     * @param string[]              $notes
     */
    public function __construct(
        public ReceiverRequest $customerInfo,
        public array $producerLines,
        public \DateTimeImmutable $issueDate,
        public \DateTimeImmutable $deliveryDate,
        public string $currencyCode = 'TRY',
        public ?float $exchangeRate = null,
        public ?string $producerSerieOrNumber = null,
        public ?string $uuid = null,
        public ?string $templateUuid = null,
        public array $notes = [],
    ) {
        if ($this->producerLines === []) {
            throw new \InvalidArgumentException('Makbuzda en az bir kalem bulunmalidir.');
        }

        foreach ($this->producerLines as $i => $line) {
            if (!$line instanceof ProducerLineRequest) {
                throw new \InvalidArgumentException(
                    "producerLines[{$i}] bir ProducerLineRequest nesnesi olmalidir."
                );
            }
        }
    }

    public function toArray(): array
    {
        $producerInfo = $this->filterNulls([
            'UUID'                  => $this->uuid,
            'TemplateUUID'          => $this->templateUuid,
            'ProducerSerieOrNumber' => $this->producerSerieOrNumber,
            'IssueDate'             => $this->issueDate->format('Y-m-d\TH:i:s\Z'),
            'DeliveryDate'          => $this->deliveryDate->format('Y-m-d\TH:i:s\Z'),
            'CurrencyCode'          => $this->currencyCode,
            'ExchangeRate'          => $this->exchangeRate,
        ]);

        $payload = [
            'ProducerInfo'  => $producerInfo,
            'CustomerInfo'  => $this->customerInfo->toArray(),
            'ProducerLines' => array_map(
                static fn (ProducerLineRequest $l) => $l->toArray(),
                $this->producerLines,
            ),
        ];

        if ($this->notes !== []) {
            $payload['Notes'] = $this->notes;
        }

        return $payload;
    }
}
