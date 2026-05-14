<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum DocumentFormat: string
{
    case Html = 'html';
    case Pdf  = 'pdf';
    case Xml  = 'xml';
}
