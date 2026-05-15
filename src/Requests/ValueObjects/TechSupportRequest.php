<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Teknoloji desteği bilgisi — API'deki TechSupportDto.
 *
 * Fatura kalemi cep telefonu, tablet veya bilgisayar olduğunda kullanılır.
 * IsTabletOrPc: true ise IMEI alanı kullanılmaz.
 *
 * @param string[] $imeiNumbers IMEI numaraları listesi (telefon kalemleri için)
 */
readonly class TechSupportRequest extends AbstractRequest
{
    public function __construct(
        public array $imeiNumbers = [],
        public bool $isTabletOrPc = false,
    ) {}

    public function toArray(): array
    {
        $data = ['IsTabletOrPc' => $this->isTabletOrPc];

        if ($this->imeiNumbers !== []) {
            $data['IMEINumbers'] = $this->imeiNumbers;
        }

        return $data;
    }
}
