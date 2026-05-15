<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Irsaliyede sofor bilgisi — ShipmentInfo.DriverPerson dizisinin bir elemani.
 */
readonly class DriverPersonRequest extends AbstractRequest
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $taxNumber,
    ) {}

    public function toArray(): array
    {
        return [
            'FirstName' => $this->firstName,
            'LastName'  => $this->lastName,
            'TaxNumber' => $this->taxNumber,
        ];
    }
}
