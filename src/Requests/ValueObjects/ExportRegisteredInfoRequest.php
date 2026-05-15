<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * İhraç kayıtlı fatura satır bilgisi — API'deki ExportRegisteredInfoDto.
 *
 * Fatura tipi IHRACKAYITLI olduğunda her satır için doldurulabilir.
 * DIIBLineCode: Dahilde İşleme İzin Belgesi (DİİB) satır kodu.
 * GTIPNo:       Gümrük Tarife İstatistik Pozisyon numarası.
 */
readonly class ExportRegisteredInfoRequest extends AbstractRequest
{
    public function __construct(
        public ?string $diibLineCode = null,
        public ?string $gtipNo = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'DIIBLineCode' => $this->diibLineCode,
            'GTIPNo'       => $this->gtipNo,
        ]);
    }
}
