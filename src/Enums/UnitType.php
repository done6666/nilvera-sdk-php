<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum UnitType: string
{
    case Piece      = 'C62';
    case Kilogram   = 'KGM';
    case Gram       = 'GRM';
    case Liter      = 'LTR';
    case Meter      = 'MTR';
    case SquareMeter = 'MTK';
    case CubicMeter = 'MTQ';
    case Hour       = 'HUR';
    case Day        = 'DAY';
    case Month      = 'MON';
    case Year       = 'ANN';
    case Box        = 'BX';
    case Dozen      = 'DZN';
    case Ton        = 'TNE';
    case Package    = 'PK';
    case Set        = 'SET';
}
