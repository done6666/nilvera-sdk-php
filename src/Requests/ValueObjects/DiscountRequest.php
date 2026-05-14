<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Fatura satırındaki iskonto bilgisini taşır.
 */
readonly class DiscountRequest extends AbstractRequest
{
    public function __construct(
        /** İskonto oranı (0–100 arası yüzde) */
        public float $percent,
        /** İskonto tutarı (negatif olamaz) */
        public float $amount,
        /** İskonto nedeni / açıklaması */
        public ?string $reason = null,
    ) {
        if ($this->percent < 0.0 || $this->percent > 100.0) {
            throw new \InvalidArgumentException('İskonto oranı (percent) 0 ile 100 arasında olmalıdır.');
        }

        if ($this->amount < 0.0) {
            throw new \InvalidArgumentException('İskonto tutarı (amount) negatif olamaz.');
        }
    }

    /**
     * Tutar üzerinden iskonto oluşturur (oran sıfır kalır).
     */
    public static function fromAmount(float $amount, ?string $reason = null): self
    {
        return new self(percent: 0.0, amount: $amount, reason: $reason);
    }

    /**
     * Yüzde üzerinden iskonto oluşturur; birim fiyat ve miktar bilgisiyle tutar hesaplanır.
     */
    public static function fromPercent(float $percent, float $unitPrice, float $quantity, ?string $reason = null): self
    {
        $amount = round($unitPrice * $quantity * ($percent / 100), 2);

        return new self(percent: $percent, amount: $amount, reason: $reason);
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'Percent' => $this->percent,
            'Amount'  => $this->amount,
            'Reason'  => $this->reason,
        ]);
    }
}
