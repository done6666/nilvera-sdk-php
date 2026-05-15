<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * SGK fatura tipi için şirket kayıt bilgileri — SGKInfoDto.
 *
 * InvoiceType::SGK olan faturalarda InvoiceInfo.SGKInfo alanında kullanılır.
 */
readonly class SGKInfoRequest extends AbstractRequest
{
    public function __construct(
        /** SGK şirket adı */
        public string $registerName,
        /** Doküman numarası */
        public string $documentNumber,
        /** SGK şirket kodu */
        public string $registerCode,
    ) {
        if (trim($this->registerName) === '') {
            throw new \InvalidArgumentException('RegisterName boş olamaz.');
        }
        if (trim($this->documentNumber) === '') {
            throw new \InvalidArgumentException('DocumentNumber boş olamaz.');
        }
        if (trim($this->registerCode) === '') {
            throw new \InvalidArgumentException('RegisterCode boş olamaz.');
        }
    }

    public function toArray(): array
    {
        return [
            'RegisterName'   => $this->registerName,
            'DocumentNumber' => $this->documentNumber,
            'RegisterCode'   => $this->registerCode,
        ];
    }
}
