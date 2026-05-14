<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Enums\UnitType;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\AuthenticationException;
use Nilvera\Exception\ConflictException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Exception\ValidationException;
use Nilvera\NilveraClient;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

// -------------------------------------------------------------------
// 1. DTO constructor validasyonu — API'ye gitmeden önce yakalanır
// -------------------------------------------------------------------
echo '=== DTO Validasyon Hataları ===' . PHP_EOL;

// Geçersiz VKN
try {
    new ReceiverRequest(
        taxNumber: '123',    // ne 10 ne 11 haneli
        name:      'Test',
        address:   'Adres',
        district:  'İlçe',
        city:      'Şehir',
    );
} catch (\InvalidArgumentException $e) {
    echo 'Beklenen hata — geçersiz VKN: ' . $e->getMessage() . PHP_EOL;
}

// Boş fatura kalemi dizisi
try {
    new SendInvoiceRequest(
        customerInfo: new ReceiverRequest('3230456015', 'Test A.Ş.', 'Adres', 'İlçe', 'Şehir'),
        invoiceLines: [],    // boş olamaz
        issueDate:    new DateTimeImmutable(),
    );
} catch (\InvalidArgumentException $e) {
    echo 'Beklenen hata — boş kalemler: ' . $e->getMessage() . PHP_EOL;
}

// Geçersiz KDV oranı
try {
    InvoiceLineRequest::make('Ürün', 1, UnitType::Piece, 100.0, 15); // 15 geçersiz
} catch (\InvalidArgumentException $e) {
    echo 'Beklenen hata — geçersiz KDV: ' . $e->getMessage() . PHP_EOL;
}

// -------------------------------------------------------------------
// 2. API hataları — sunucudan dönen HTTP hataları
// -------------------------------------------------------------------
echo PHP_EOL . '=== API Hata Yakalama ===' . PHP_EOL;

$client = NilveraClient::test('GECERSIZ-API-KEY');

$request = new SendInvoiceRequest(
    customerInfo: new ReceiverRequest('3230456015', 'Test A.Ş.', 'Adres', 'İlçe', 'Şehir', taxOffice: 'Merkez'),
    invoiceLines: [InvoiceLineRequest::make('Ürün', 1, UnitType::Piece, 100.0, 20)],
    issueDate:    new DateTimeImmutable(),
);

try {
    $client->eInvoice()->send($request);

} catch (AuthenticationException $e) {
    // HTTP 401 / 403 — API anahtarı geçersiz veya yetkisiz
    echo 'Kimlik doğrulama hatası: ' . $e->getMessage() . PHP_EOL;

} catch (ValidationException $e) {
    // HTTP 422 — API iş kuralı / alan hatası
    echo 'Doğrulama hatası:' . PHP_EOL;
    foreach ($e->getErrors() as $field => $messages) {
        echo "  [{$field}] " . implode(', ', (array) $messages) . PHP_EOL;
    }

} catch (NotFoundException $e) {
    // HTTP 404 — kayıt bulunamadı
    echo 'Kayıt bulunamadı: ' . $e->getMessage() . PHP_EOL;

} catch (ConflictException $e) {
    // HTTP 409 — aynı UUID ile tekrar gönderim
    echo 'Çakışma hatası (mükerrer istek): ' . $e->getMessage() . PHP_EOL;

} catch (ApiException $e) {
    // Diğer tüm HTTP ve bağlantı hataları
    echo 'API hatası' . PHP_EOL;
    echo '  HTTP status : ' . $e->getStatusCode() . PHP_EOL;
    echo '  Mesaj       : ' . $e->getMessage() . PHP_EOL;

    $body = $e->getResponseBody();
    if ($body !== '') {
        echo '  Yanıt gövdesi: ' . $body . PHP_EOL;
    }
}

// -------------------------------------------------------------------
// 3. Hata hiyerarşisi
// -------------------------------------------------------------------
// ApiException
// ├── AuthenticationException  (401, 403)
// ├── NotFoundException        (404)
// ├── ValidationException      (422) — getErrors(): array
// └── ConflictException        (409)
