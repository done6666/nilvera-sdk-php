<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * İnternet satış bilgileri — API'deki InternetInfoDto.
 *
 * SalesPlatform::Internet olan faturalarda ZORUNLUDUR.
 *
 * PaymentMethod için geçerli değerler:
 *   KREDIKARTI/BANKAKARTI, EFT/HAVALE, KAPIDAODEME, ODEMEARACISI, DIGER
 *
 * PaymentDate:
 *   - PaymentMethod 'DIGER' ise opsiyonel, diğer durumlarda zorunludur.
 *
 * TransporterName, TransporterRegisterNumber, TransportDate:
 *   - Kalemler hizmet değil taşıma ise zorunludur.
 */
readonly class InternetInfoRequest extends AbstractRequest
{
    public const VALID_PAYMENT_METHODS = [
        'KREDIKARTI/BANKAKARTI',
        'EFT/HAVALE',
        'KAPIDAODEME',
        'ODEMEARACISI',
        'DIGER',
    ];

    public function __construct(
        public ?string $webSite = null,
        public ?string $paymentMethod = null,
        public ?string $paymentMethodName = null,
        public ?string $paymentAgentName = null,
        public ?\DateTimeImmutable $paymentDate = null,
        public ?string $transporterName = null,
        public ?string $transporterRegisterNumber = null,
        public ?\DateTimeImmutable $transportDate = null,
    ) {
        if ($this->paymentMethod !== null && !in_array($this->paymentMethod, self::VALID_PAYMENT_METHODS, true)) {
            throw new \InvalidArgumentException(
                'PaymentMethod şu değerlerden biri olmalıdır: ' . implode(', ', self::VALID_PAYMENT_METHODS)
            );
        }
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'WebSite'                   => $this->webSite,
            'PaymentMethod'             => $this->paymentMethod,
            'PaymentMethodName'         => $this->paymentMethodName,
            'PaymentAgentName'          => $this->paymentAgentName,
            'PaymentDate'               => $this->paymentDate?->format('Y-m-d\TH:i:s\Z'),
            'TransporterName'           => $this->transporterName,
            'TransporterRegisterNumber' => $this->transporterRegisterNumber,
            'TransportDate'             => $this->transportDate?->format('Y-m-d\TH:i:s\Z'),
        ]);
    }
}
