<?php

declare(strict_types=1);

namespace Nilvera\Enums;

enum ProductType: string
{
    case Medicine      = 'MEDICINE';
    case MedicalDevice = 'MEDICALDEVICE';
    case Other         = 'OTHER';
}
