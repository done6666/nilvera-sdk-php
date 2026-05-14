<?php

declare(strict_types=1);

namespace Nilvera\Requests;

/**
 * Belgeyi SMS ile gönderme isteği.
 *
 * e-Fatura, e-Arşiv vb. servislerdeki `/Sms/Send` endpointleri için kullanılır.
 */
readonly class SendBySmsRequest extends AbstractRequest
{
    /**
     * @param string[] $phoneNumbers En az bir telefon numarası zorunludur.
     *                               Uluslararası format önerilir: "+905551234567"
     */
    public function __construct(
        public string $uuid,
        public array $phoneNumbers,
    ) {
        if (trim($this->uuid) === '') {
            throw new \InvalidArgumentException('UUID boş olamaz.');
        }

        if ($this->phoneNumbers === []) {
            throw new \InvalidArgumentException('En az bir telefon numarası girilmelidir.');
        }

        foreach ($this->phoneNumbers as $i => $phone) {
            if (!preg_match('/^\+?[\d\s\-()]{7,20}$/', $phone)) {
                throw new \InvalidArgumentException(
                    "phoneNumbers[{$i}] geçersiz bir telefon numarası formatı: '{$phone}'"
                );
            }
        }
    }

    public function toArray(): array
    {
        return [
            'UUID'         => $this->uuid,
            'phoneNumbers' => $this->phoneNumbers,
        ];
    }
}
