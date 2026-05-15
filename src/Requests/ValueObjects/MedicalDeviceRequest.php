<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Tıbbi cihaz kalem bilgisi — MedicalDeviceDto.
 *
 * InvoiceProfile::Medical (ILAC_TIBBICIHAZ) faturalarında
 * InvoiceLineRequest.medicineAndMedicalDevice.medicalDevice[] içinde kullanılır.
 */
readonly class MedicalDeviceRequest extends AbstractRequest
{
    public function __construct(
        public ?string $productNumber = null,
        public ?string $lotNumber = null,
        public ?string $serialNumber = null,
        public ?\DateTimeImmutable $productionDate = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'ProductNumber'  => $this->productNumber,
            'LotNumber'      => $this->lotNumber,
            'SerialNumber'   => $this->serialNumber,
            'ProductionDate' => $this->productionDate?->format('Y-m-d\TH:i:s\Z'),
        ]);
    }
}
