<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Ürün ek kimlik bilgisi — API'deki AdditionalItemIdentificationDto.
 *
 * Fatura kalemi üzerinde ürüne ait ek kimlik (etiket, mal sahibi vb.) girilmesini sağlar.
 */
readonly class AdditionalItemIdentificationRequest extends AbstractRequest
{
    public function __construct(
        public ?string $tagNumber = null,
        public ?string $ownerName = null,
        public ?string $ownerTaxNumber = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'TagNumber'      => $this->tagNumber,
            'OwnerName'      => $this->ownerName,
            'OwnerTaxNumber' => $this->ownerTaxNumber,
        ]);
    }
}
