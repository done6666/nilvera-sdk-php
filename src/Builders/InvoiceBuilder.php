<?php

declare(strict_types=1);

namespace Nilvera\Builders;

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\TaxRequest;

/**
 * Fluent (zincirleme) e-Fatura olusturucu.
 *
 * Ornek kullanim:
 *   $invoice = InvoiceBuilder::for(new ReceiverRequest(...))
 *       ->issueDate(new \DateTimeImmutable())
 *       ->alias('urn:mail:muhasebe@sirket.com.tr')
 *       ->addLine('Yazilim Lisansi', 1, UnitType::Piece, 10_000, 20)
 *       ->addLine('Yillik Destek',   12, UnitType::Month,  500, 20)
 *       ->note('Odeme vadesi: 30 gun')
 *       ->build();
 */
class InvoiceBuilder
{
    private InvoiceProfile $profile   = InvoiceProfile::Basic;
    private InvoiceType    $type      = InvoiceType::Sales;
    private string         $currency  = 'TRY';
    private ?float         $exchangeRate = null;
    private ?string        $alias     = null;
    private ?string        $serieOrNumber = null;
    private ?string        $uuid      = null;
    private ?string        $templateUuid = null;

    /** @var InvoiceLineRequest[] */
    private array $lines = [];

    /** @var string[] */
    private array $notes = [];

    /** @var array<array{IssueDate:string,Value:string}> */
    private array $orderReference = [];

    private \DateTimeImmutable $issueDate;

    private function __construct(private readonly ReceiverRequest $receiver)
    {
        $this->issueDate = new \DateTimeImmutable();
    }

    public static function for(ReceiverRequest $receiver): self
    {
        return new self($receiver);
    }

    public function issueDate(\DateTimeImmutable $date): self
    {
        $clone = clone $this;
        $clone->issueDate = $date;
        return $clone;
    }

    public function profile(InvoiceProfile $profile): self
    {
        $clone = clone $this;
        $clone->profile = $profile;
        return $clone;
    }

    public function type(InvoiceType $type): self
    {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    public function currency(string $code, ?float $exchangeRate = null): self
    {
        $clone = clone $this;
        $clone->currency     = $code;
        $clone->exchangeRate = $exchangeRate;
        return $clone;
    }

    /** GIB'de kayitli alici alias'i (e-posta veya ETTN) */
    public function alias(string $alias): self
    {
        $clone = clone $this;
        $clone->alias = $alias;
        return $clone;
    }

    public function serieOrNumber(string $value): self
    {
        $clone = clone $this;
        $clone->serieOrNumber = $value;
        return $clone;
    }

    public function uuid(string $uuid): self
    {
        $clone = clone $this;
        $clone->uuid = $uuid;
        return $clone;
    }

    public function templateUuid(string $uuid): self
    {
        $clone = clone $this;
        $clone->templateUuid = $uuid;
        return $clone;
    }

    /**
     * Fatura kalemi ekler; KDVTotal otomatik hesaplanir.
     *
     * @param TaxRequest[] $taxes
     */
    public function addLine(
        string $name,
        float $quantity,
        UnitType|string $unitType,
        float $price,
        float $kdvPercent,
        float $allowanceTotal = 0.0,
        float $allowancePercent = 0.0,
        array $taxes = [],
        ?string $sellerCode = null,
        ?string $description = null,
    ): self {
        $clone = clone $this;
        $clone->lines = $this->lines;
        $clone->lines[] = InvoiceLineRequest::make(
            name: $name,
            quantity: $quantity,
            unitType: $unitType,
            price: $price,
            kdvPercent: $kdvPercent,
            allowanceTotal: $allowanceTotal,
            allowancePercent: $allowancePercent,
            taxes: $taxes,
            sellerCode: $sellerCode,
            description: $description,
        );
        return $clone;
    }

    /** Onceden olusturulmus bir InvoiceLineRequest ekler */
    public function addLineRequest(InvoiceLineRequest $line): self
    {
        $clone = clone $this;
        $clone->lines = $this->lines;
        $clone->lines[] = $line;
        return $clone;
    }

    public function note(string $note): self
    {
        $clone = clone $this;
        $clone->notes = $this->notes;
        $clone->notes[] = $note;
        return $clone;
    }

    /**
     * Siparis referansi ekler.
     * @param string $date YYYY-MM-DD formatinda
     */
    public function orderReference(string $date, string $number): self
    {
        $clone = clone $this;
        $clone->orderReference = $this->orderReference;
        $clone->orderReference[] = ['IssueDate' => $date, 'Value' => $number];
        return $clone;
    }

    /** @throws \LogicException Hic kalem eklenmemisse veya zorunlu alanlar eksikse */
    public function build(): SendInvoiceRequest
    {
        if ($this->lines === []) {
            throw new \LogicException('Fatura olusturmak icin en az bir kalem (addLine) gereklidir.');
        }

        if ($this->alias === null) {
            throw new \LogicException('customerAlias zorunludur; ->alias() cagrisini yapin.');
        }

        if ($this->serieOrNumber === null) {
            throw new \LogicException('invoiceSerieOrNumber zorunludur; ->serieOrNumber() cagrisini yapin.');
        }

        return new SendInvoiceRequest(
            customerInfo:         $this->receiver,
            invoiceLines:         $this->lines,
            issueDate:            $this->issueDate,
            customerAlias:        $this->alias,
            invoiceSerieOrNumber: $this->serieOrNumber,
            invoiceProfile:       $this->profile,
            invoiceType:          $this->type,
            currencyCode:         $this->currency,
            exchangeRate:         $this->exchangeRate,
            notes:                $this->notes,
            uuid:                 $this->uuid,
            templateUuid:         $this->templateUuid,
            orderReference:       $this->orderReference,
        );
    }
}
