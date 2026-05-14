<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum InvoiceType: string
{
    case Sales          = 'SATIS';
    case Return         = 'IADE';
    case Stoppage       = 'TEVKIFAT';
    case SpecialBase    = 'OZELMATRAH';
    case Export         = 'IHRACAT';
    case ExciseDuty     = 'IHRACKAYITLI';
}
