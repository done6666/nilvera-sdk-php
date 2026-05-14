<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\ValidationException;
use Nilvera\NilveraClient;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\TaxRequest;

// -------------------------------------------------------------------
// 1. İstemciyi başlat
// -------------------------------------------------------------------
$client = NilveraClient::test('TEST-API-KEY-BURAYA');

// -------------------------------------------------------------------
// 2. DTO'ları doldur
// -------------------------------------------------------------------

// Alıcı bilgileri
$customerInfo = new ReceiverRequest(
    taxNumber: '3230456015',        // 10 haneli VKN veya 11 haneli TCKN
    name:      'ABC Yazılım A.Ş.',
    address:   'Atatürk Cad. No:1',
    district:  'Kadıköy',
    city:      'İstanbul',
    taxOffice: 'Kadıköy',           // e-Fatura'da zorunlu
);

// Fatura kalemleri — InvoiceLineRequest::make() KDV tutarını otomatik hesaplar
$lines = [
    // Standart kalem: 2 adet × 100 TL, %20 KDV
    InvoiceLineRequest::make(
        name:       'Yazılım Lisansı',
        quantity:   2,
        unitType:   UnitType::Piece,
        price:      100.00,
        kdvPercent: 20,
    ),

    // İskontolu kalem: %10 iskonto
    InvoiceLineRequest::make(
        name:             'Danışmanlık Hizmeti',
        quantity:         5,
        unitType:         UnitType::Hour,
        price:            200.00,
        kdvPercent:       10,
        allowancePercent: 10,       // %10 iskonto → AllowanceTotal otomatik hesaplanır
    ),

    // KDV tevkifatlı kalem
    InvoiceLineRequest::make(
        name:       'Yapım İşi',
        quantity:   1,
        unitType:   UnitType::Piece,
        price:      1000.00,
        kdvPercent: 20,
        taxes: [
            new TaxRequest(
                taxCode:    '9015',   // KDV tevkifatı
                total:      80.00,
                percent:    40.0,
                reasonCode: '601',
                reasonDesc: 'Yapım İşleri ile Bu İşlerle Birlikte İfa Edilen Mühendislik-Mimarlık ve Etüt-Proje Hizmetleri',
            ),
        ],
    ),
];

// Fatura isteği
$request = new SendInvoiceRequest(
    customerInfo:    $customerInfo,
    invoiceLines:    $lines,
    issueDate:       new DateTimeImmutable('2026-05-14T10:00:00'),
    invoiceProfile:  InvoiceProfile::Basic,
    invoiceType:     InvoiceType::Sales,
    currencyCode:    'TRY',
    // customerAlias: 'urn:mail:muhasebe@abc.com.tr', // GIB'e kayıtlı alıcı için
    notes: ['Ödeme vadesi 30 gündür.'],
);

// -------------------------------------------------------------------
// 3. Gönder ve yanıtı işle
// -------------------------------------------------------------------
try {
    $response = $client->eInvoice()->send($request);

    echo 'Fatura gönderildi.' . PHP_EOL;
    echo 'UUID          : ' . $response->uuid . PHP_EOL;
    echo 'Fatura No     : ' . ($response->invoiceNumber ?? '—') . PHP_EOL;

} catch (ValidationException $e) {
    // HTTP 422 — iş kuralı veya alan doğrulama hatası
    echo 'Doğrulama hatası:' . PHP_EOL;
    foreach ($e->getErrors() as $field => $messages) {
        echo "  [{$field}] " . implode(', ', (array) $messages) . PHP_EOL;
    }
} catch (ApiException $e) {
    echo 'API hatası (' . $e->getStatusCode() . '): ' . $e->getMessage() . PHP_EOL;
}
