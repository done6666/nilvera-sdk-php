<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum InvoiceType: string
{
    case Sales               = 'SATIS';
    case Return              = 'IADE';
    case Exemption           = 'ISTISNA';
    case Stoppage            = 'TEVKIFAT';
    case ExciseDuty          = 'IHRACKAYITLI';
    case Cancel              = 'IPTAL';
    case SpecialBase         = 'OZELMATRAH';
    case SGK                 = 'SGK';
    case StoppageReturn      = 'TEVKIFATIADE';
    case Commissioner        = 'KOMISYONCU';
    case HKSSales            = 'HKSSATIS';
    case HKSCommissioner     = 'HKSKOMISYONCU';
    case AccommodationTax    = 'KONAKLAMAVERGISI';
    case EVCharging          = 'SARJ';
    case EVChargingStation   = 'SARJANLIK';
    case TechSupport         = 'TEKNOLOJIDESTEK';
    case YTBSales            = 'YTBSATIS';
    case YTBExemption        = 'YTBISTISNA';
    case YTBReturn           = 'YTBIADE';
    case YTBStoppage         = 'YTBTEVKIFAT';
    case YTBStoppageReturn   = 'YTBTEVKIFATIADE';
}
