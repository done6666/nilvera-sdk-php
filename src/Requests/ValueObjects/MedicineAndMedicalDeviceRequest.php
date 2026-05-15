<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Enums\ProductType;
use Nilvera\Requests\AbstractRequest;

/**
 * İlaç ve tıbbi cihaz kalem bilgisi — MedicineAndMedicalDeviceDto.
 *
 * InvoiceProfile::Medical (ILAC_TIBBICIHAZ) faturalarında
 * InvoiceLineRequest.medicineAndMedicalDevice alanında kullanılır.
 *
 * @param MedicineRequest[]     $medicine
 * @param MedicalDeviceRequest[] $medicalDevice
 */
readonly class MedicineAndMedicalDeviceRequest extends AbstractRequest
{
    public function __construct(
        public ProductType $productType,
        public array $medicine = [],
        public array $medicalDevice = [],
    ) {
        foreach ($this->medicine as $i => $item) {
            if (!$item instanceof MedicineRequest) {
                throw new \InvalidArgumentException("medicine[{$i}] bir MedicineRequest nesnesi olmalıdır.");
            }
        }
        foreach ($this->medicalDevice as $i => $item) {
            if (!$item instanceof MedicalDeviceRequest) {
                throw new \InvalidArgumentException("medicalDevice[{$i}] bir MedicalDeviceRequest nesnesi olmalıdır.");
            }
        }
    }

    public function toArray(): array
    {
        $data = ['ProductType' => $this->productType->value];

        if ($this->medicine !== []) {
            $data['Medicine'] = array_map(
                static fn (MedicineRequest $m) => $m->toArray(),
                $this->medicine,
            );
        }

        if ($this->medicalDevice !== []) {
            $data['MedicalDevice'] = array_map(
                static fn (MedicalDeviceRequest $d) => $d->toArray(),
                $this->medicalDevice,
            );
        }

        return $data;
    }
}
