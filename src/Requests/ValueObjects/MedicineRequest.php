<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * İlaç kalem bilgisi — MedicineDto.
 *
 * InvoiceProfile::Medical (ILAC_TIBBICIHAZ) faturalarında
 * InvoiceLineRequest.medicineAndMedicalDevice.medicine[] içinde kullanılır.
 */
readonly class MedicineRequest extends AbstractRequest
{
    public function __construct(
        /** Global Trade Item Number */
        public ?string $gtin = null,
        public ?string $batchNumber = null,
        public ?string $serialNumber = null,
        public ?\DateTimeImmutable $expirationDate = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'GTIN'           => $this->gtin,
            'BatchNumber'    => $this->batchNumber,
            'SerialNumber'   => $this->serialNumber,
            'ExpirationDate' => $this->expirationDate?->format('Y-m-d\TH:i:s\Z'),
        ]);
    }
}
