<?php

declare(strict_types=1);

namespace Nilvera\Requests;

/**
 * Seri listesi sorgu parametreleri.
 *
 * GET /earchive/Series veya GET /einvoice/Series gibi seri listeleme
 * endpointlerinde query string parametresi olarak kullanılır.
 */
readonly class ListSeriesRequest extends AbstractRequest
{
    public function __construct(
        public ?string $search = null,
        public ?int $page = null,
        public ?int $pageSize = null,
        public ?string $sortColumn = null,
        /** 'ASC' veya 'DESC' */
        public ?string $sortType = null,
        public ?bool $isActive = null,
        public ?bool $isDefault = null,
    ) {}

    public function toArray(): array
    {
        // Guzzle sends PHP true as 1 in query strings; API requires 'true'/'false' strings.
        $data = $this->filterNulls([
            'Search'     => $this->search,
            'Page'       => $this->page,
            'PageSize'   => $this->pageSize,
            'SortColumn' => $this->sortColumn,
            'SortType'   => $this->sortType,
        ]);

        if ($this->isActive !== null) {
            $data['IsActive'] = $this->isActive ? 'true' : 'false';
        }

        if ($this->isDefault !== null) {
            $data['IsDefault'] = $this->isDefault ? 'true' : 'false';
        }

        return $data;
    }
}
