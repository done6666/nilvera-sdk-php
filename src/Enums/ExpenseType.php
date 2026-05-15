<?php

declare(strict_types=1);

namespace Nilvera\Enums;

/**
 * HKS (Hal Kayıt Sistemi) faturalarındaki masraf tipleri.
 *
 * Yalnızca InvoiceType::HKSSales veya InvoiceType::HKSCommissioner faturalarında kullanılır.
 */
enum ExpenseType: string
{
    case HKSCommission            = 'HKSKOMISYON';
    case HKSCommissionVAT         = 'HKSKOMISYONKDV';
    case HKSFreight               = 'HKSNAVLUN';
    case HKSFreightVAT            = 'HKSNAVLUNKDV';
    case HKSPorterage             = 'HKSHAMMALIYE';
    case HKSPorterageVAT          = 'HKSHAMMALIYEKDV';
    case HKSTransport             = 'HKSNAKLIYE';
    case HKSTransportVAT          = 'HKSNAKLIYEKDV';
    case HKSIncomeTaxWithholding  = 'HKSGVTEVKIFAT';
    case HKSSSIPremiumWithholding = 'HKSBAGKURTEVKIFAT';
    case HKSDuty                  = 'HKSRUSUM';
    case HKSDutyVAT               = 'HKSRUSUMKDV';
    case HKSTradeChamber          = 'HKSTICBORSASI';
    case HKSTradeChamberVAT       = 'HKSTICBORSASIKDV';
    case HKSDefenceFund           = 'HKSMILLISAVUNMAFON';
    case HKSDefenceFundVAT        = 'HKSMSFONKDV';
    case HKSOtherExpenses         = 'HKSDIGERMASRAFLAR';
    case HKSOtherExpensesVAT      = 'HKSDIGERKDV';
}
