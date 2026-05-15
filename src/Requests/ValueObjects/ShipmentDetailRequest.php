<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Sevkiyat detay bilgisi — EDespatch.ShipmentDetail.
 *
 * @param string[] $transportEquipment Konteyner/dorse numaraları gibi taşıma ekipmanı kimlikleri
 */
readonly class ShipmentDetailRequest extends AbstractRequest
{
    public function __construct(
        public ShipmentInfoRequest $shipmentInfo,
        public WaybillDeliveryRequest $delivery,
        public array $transportEquipment = [],
    ) {}

    public function toArray(): array
    {
        $data = [
            'ShipmentInfo' => $this->shipmentInfo->toArray(),
            'Delivery'     => $this->delivery->toArray(),
        ];

        if ($this->transportEquipment !== []) {
            $data['TransportEquipment'] = $this->transportEquipment;
        }

        return $data;
    }
}
