<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Teslimat bilgisi — ShipmentDetail.Delivery.
 */
readonly class WaybillDeliveryRequest extends AbstractRequest
{
    public function __construct(
        public AddressInfoRequest $addressInfo,
        public ?CarrierInfoRequest $carrierInfo = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'AddressInfo' => $this->addressInfo->toArray(),
            'CarrierInfo' => $this->carrierInfo?->toArray(),
        ]);
    }
}
