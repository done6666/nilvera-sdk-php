<?php

declare(strict_types=1);

namespace Nilvera\Services;

/**
 * E-Saklama (Document Storage/Archiving) API.
 * Base path: /estorage
 */
class EStorageService extends AbstractService
{
    /**
     * List stored documents.
     *
     * GET /estorage/Document
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listDocuments(array $query = []): array
    {
        return $this->get('/estorage/Document', $query)->json();
    }

    /**
     * Get a stored document by UUID.
     *
     * GET /estorage/Document/{uuid}
     *
     * @return array<string, mixed>
     */
    public function getDocument(string $uuid): array
    {
        return $this->get("/estorage/Document/{$uuid}")->json();
    }

    /**
     * Upload a document for storage.
     *
     * POST /estorage/Document
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function uploadDocument(array $data): array
    {
        return $this->post('/estorage/Document', $data)->json();
    }

    /**
     * Get the status of a stored document.
     *
     * GET /estorage/Document/{uuid}/Status
     *
     * @return array<string, mixed>
     */
    public function getDocumentStatus(string $uuid): array
    {
        return $this->get("/estorage/Document/{$uuid}/Status")->json();
    }

    /**
     * Delete a stored document.
     *
     * DELETE /estorage/Document/{uuid}
     */
    public function deleteDocument(string $uuid): void
    {
        $this->delete("/estorage/Document/{$uuid}");
    }
}
