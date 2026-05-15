<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum InvestmentIncentiveExpenseType: string
{
    case MachineAndSoftware = 'MachineAndSoftware';
    case Construction       = 'Construction';
    case LandAndPlotSale    = 'LandAndPlotSale';
    case Other              = 'Other';
}
