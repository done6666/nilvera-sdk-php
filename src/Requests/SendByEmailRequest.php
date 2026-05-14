<?php

declare(strict_types=1);

namespace Nilvera\Requests;

/**
 * Belgeyi e-posta ile gönderme isteği.
 *
 * e-Fatura, e-Arşiv, e-İrsaliye vb. servislerdeki
 * `/Email/Send` endpointleri için kullanılır.
 */
readonly class SendByEmailRequest extends AbstractRequest
{
    /**
     * @param string[] $emailAddresses En az bir e-posta adresi zorunludur.
     */
    public function __construct(
        public string $uuid,
        public array $emailAddresses,
    ) {
        if (trim($this->uuid) === '') {
            throw new \InvalidArgumentException('UUID boş olamaz.');
        }

        if ($this->emailAddresses === []) {
            throw new \InvalidArgumentException('En az bir e-posta adresi girilmelidir.');
        }

        foreach ($this->emailAddresses as $i => $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException(
                    "emailAddresses[{$i}] geçersiz bir e-posta adresi: '{$email}'"
                );
            }
        }
    }

    public function toArray(): array
    {
        return [
            'UUID'           => $this->uuid,
            'emailAddresses' => $this->emailAddresses,
        ];
    }
}
