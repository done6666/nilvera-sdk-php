<?php

declare(strict_types=1);

namespace Nilvera\Responses;

/**
 * Sayfalanmis liste yanitlarini sarar.
 * Nilvera API'sinin GET listeleme endpointleri tarafindan donus degeri olarak kullanilir.
 *
 * @template T
 */
readonly class PaginatedResponse
{
    /**
     * @param array<T> $items
     */
    public function __construct(
        public array $items,
        public int $totalCount,
        public int $page,
        public int $pageSize,
    ) {}

    /**
     * Ham API dizisinden PaginatedResponse olusturur.
     * Alan adlari Nilvera API'sinin tipik sayfalama yapisina gore belirlenmistir.
     *
     * @param array<string, mixed>                $data
     * @param callable(array<string,mixed>): T    $itemMapper  Her satiri T turune donusturan fonksiyon
     * @return self<T>
     */
    public static function fromArray(array $data, callable $itemMapper): self
    {
        $items = array_map($itemMapper, $data['Items'] ?? $data['Data'] ?? $data);

        return new self(
            items: $items,
            totalCount: (int) ($data['TotalCount'] ?? $data['Total'] ?? count($items)),
            page: (int) ($data['Page'] ?? $data['PageNumber'] ?? 1),
            pageSize: (int) ($data['PageSize'] ?? count($items)),
        );
    }

    public function hasNextPage(): bool
    {
        return ($this->page * $this->pageSize) < $this->totalCount;
    }

    public function totalPages(): int
    {
        return $this->pageSize > 0
            ? (int) ceil($this->totalCount / $this->pageSize)
            : 1;
    }
}
