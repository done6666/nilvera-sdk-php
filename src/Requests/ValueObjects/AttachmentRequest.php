<?php

declare(strict_types=1);

namespace Nilvera\Requests\ValueObjects;

use Nilvera\Requests\AbstractRequest;

/**
 * Belgeye eklenecek dosya — API'deki AttachmentDto.
 *
 * AdditionalDocumentReferenceRequest içinde kullanılır.
 * Desteklenen dosya tipleri: doc, docx, ppt, pptx, pdf, jpg, jpeg, png
 */
readonly class AttachmentRequest extends AbstractRequest
{
    public function __construct(
        public ?string $base64Data = null,
        public ?string $mimeCode = null,
        public ?string $fileName = null,
    ) {}

    public function toArray(): array
    {
        return $this->filterNulls([
            'Base64Data' => $this->base64Data,
            'MimeCode'   => $this->mimeCode,
            'FileName'   => $this->fileName,
        ]);
    }
}
