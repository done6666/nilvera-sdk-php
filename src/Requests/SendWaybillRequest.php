<?php

declare(strict_types=1);

namespace Nilvera\Requests;

use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

/**
 * e-Irsaliye gonderme istegi — POST /edespatch/Send/Model
 *
 * DespatchType: MATBUDAN(0) | SEVK(1)
 * DespatchProfile: TEMELIRSALIYE(1) | HKSIRSALIYE(2)
 */
readonly class SendWaybillRequest extends AbstractRequest
{
    /**
     * @param DespatchLineRequest[] $despatchLines
     * @param string[]              $notes
     */
    public function __construct(
        public ReceiverRequest $customerInfo,
        public array $despatchLines,
        public \DateTimeImmutable $issueDate,
        /** MATBUDAN=0, SEVK=1 */
        public int $despatchType = 1,
        /** TEMELIRSALIYE=1, HKSIRSALIYE=2 */
        public int $despatchProfile = 1,
        public ?string $despatchSerieOrNumber = null,
        public ?\DateTimeImmutable $actualDespatchDateTime = null,
        public ?float $payableAmount = null,
        public string $currencyCode = 'TRY',
        public ?string $uuid = null,
        public ?string $templateUuid = null,
        public array $notes = [],
        public ?string $shipmentNumber = null,
    ) {
        if ($this->despatchLines === []) {
            throw new \InvalidArgumentException('Irsaliyede en az bir kalem bulunmalidir.');
        }

        foreach ($this->despatchLines as $i => $line) {
            if (!$line instanceof DespatchLineRequest) {
                throw new \InvalidArgumentException(
                    "despatchLines[{$i}] bir DespatchLineRequest nesnesi olmalidir."
                );
            }
        }

        if (!in_array($this->despatchType, [0, 1], true)) {
            throw new \InvalidArgumentException('DespatchType 0 (MATBUDAN) veya 1 (SEVK) olmalidir.');
        }

        if (!in_array($this->despatchProfile, [1, 2], true)) {
            throw new \InvalidArgumentException('DespatchProfile 1 (TEMELIRSALIYE) veya 2 (HKSIRSALIYE) olmalidir.');
        }
    }

    public function toArray(): array
    {
        $despatchInfo = $this->filterNulls([
            'UUID'                   => $this->uuid,
            'TemplateUUID'           => $this->templateUuid,
            'DespatchType'           => $this->despatchType,
            'DespatchProfile'        => $this->despatchProfile,
            'DespatchSerieOrNumber'  => $this->despatchSerieOrNumber,
            'IssueDate'              => $this->issueDate->format('Y-m-d'),
            'ActualDespatchDateTime' => $this->actualDespatchDateTime?->format('H:i:s.000\Z'),
            'PayableAmount'          => $this->payableAmount,
            'CurrencyCode'           => $this->currencyCode,
            'ShipmentNumber'         => $this->shipmentNumber,
        ]);

        $payload = [
            'DespatchInfo'  => $despatchInfo,
            'CustomerInfo'  => $this->customerInfo->toArray(),
            'DespatchLines' => array_map(
                static fn (DespatchLineRequest $l) => $l->toArray(),
                $this->despatchLines,
            ),
        ];

        if ($this->notes !== []) {
            $payload['Notes'] = $this->notes;
        }

        return $payload;
    }
}
