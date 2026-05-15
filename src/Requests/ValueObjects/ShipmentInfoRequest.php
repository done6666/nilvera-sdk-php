<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Sevkiyat bilgisi — ShipmentDetail.ShipmentInfo.
 */
readonly class ShipmentInfoRequest extends AbstractRequest
{
    /**
     * @param DriverPersonRequest[] $driverPersons
     */
    public function __construct(
        public string $licensePlateId,
        public array $driverPersons = [],
    ) {}

    public function toArray(): array
    {
        $data = ['LicensePlateID' => $this->licensePlateId];

        if ($this->driverPersons !== []) {
            $data['DriverPerson'] = array_map(
                static fn (DriverPersonRequest $d) => $d->toArray(),
                $this->driverPersons,
            );
        }

        return $data;
    }
}
