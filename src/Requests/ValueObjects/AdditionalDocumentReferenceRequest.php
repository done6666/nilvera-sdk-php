<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Faturaya ek belge veya referans — API'deki AdditionalDocumentReferenceDto.
 *
 * OrderReferenceDocument ve AdditionalDocumentReferences alanlarında kullanılır.
 * Attachment alanı ile dosya (PDF, PNG vb.) eklenebilir.
 */
readonly class AdditionalDocumentReferenceRequest extends AbstractRequest
{
    public function __construct(
        public ?string $id = null,
        public ?\DateTimeImmutable $issueDate = null,
        public ?string $documentType = null,
        public ?string $documentTypeCode = null,
        public ?string $documentDescription = null,
        public ?AttachmentRequest $attachment = null,
    ) {}

    public function toArray(): array
    {
        $data = $this->filterNulls([
            'ID'                  => $this->id,
            'IssueDate'           => $this->issueDate?->format('Y-m-d\TH:i:s\Z'),
            'DocumentType'        => $this->documentType,
            'DocumentTypeCode'    => $this->documentTypeCode,
            'DocumentDescription' => $this->documentDescription,
        ]);

        if ($this->attachment !== null) {
            $data['Attachment'] = $this->attachment->toArray();
        }

        return $data;
    }
}
