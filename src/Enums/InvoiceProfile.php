<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum InvoiceProfile: string
{
    case Basic               = 'TEMELFATURA';
    case Commercial          = 'TICARIFATURA';
    case Export              = 'IHRACAT';
    case TravellerBag        = 'YOLCUBERABERFATURA';
    case EArchive            = 'EARSIVFATURA';
    case Public              = 'KAMU';
    case HKS                 = 'HKS';
    case Energy              = 'ENERJI';
    case Medical             = 'ILAC_TIBBICIHAZ';
    case Special             = 'OZELFATURA';
    case InvestmentIncentive = 'YATIRIMTESVIK';
}
