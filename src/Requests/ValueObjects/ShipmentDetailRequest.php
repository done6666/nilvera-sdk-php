<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Sevkiyat detay bilgisi — EDespatch.ShipmentDetail.
 */
readonly class ShipmentDetailRequest extends AbstractRequest
{
    public function __construct(
        public ShipmentInfoRequest $shipmentInfo,
        public WaybillDeliveryRequest $delivery,
    ) {}

    public function toArray(): array
    {
        return [
            'ShipmentInfo' => $this->shipmentInfo->toArray(),
            'Delivery'     => $this->delivery->toArray(),
        ];
    }
}
