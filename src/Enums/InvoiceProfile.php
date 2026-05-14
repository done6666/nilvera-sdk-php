<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum InvoiceProfile: string
{
    case Basic      = 'TEMELFATURA';
    case Commercial = 'TICARIFATURA';
    case Export     = 'IHRACAT';
}
