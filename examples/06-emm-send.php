<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Enums\UnitType;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\ValidationException;
use Nilvera\NilveraClient;
use Nilvera\Requests\SendProducerReceiptRequest;
use Nilvera\Requests\ValueObjects\ProducerLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

$client = NilveraClient::test('TEST-API-KEY-BURAYA');

// -------------------------------------------------------------------
// 1. Alıcı bilgileri (ürünü satın alan çiftçi/üretici)
//    e-MM'de alıcı genellikle bireysel (TCKN) olur
// -------------------------------------------------------------------
$customerInfo = new ReceiverRequest(
    taxNumber: '12345678901',    // TCKN
    name:      'Mehmet Çiftçi',
    address:   'Tarım Mah. No:3',
    district:  'Merkez',
    city:      'Konya',
);

// -------------------------------------------------------------------
// 2. Makbuz kalemleri
//    GVWithholdingPercent: Gelir Vergisi Stopajı oranı
// -------------------------------------------------------------------
$lines = [
    new ProducerLineRequest(
        name:                 'Buğday',
        quantity:             1000.0,
        unitType:             UnitType::Kilogram,
        price:                5.50,
        gvWithholdingPercent: 4.0,    // %4 GV stopajı
        gvWithholdingAmount:  220.00,
    ),
    new ProducerLineRequest(
        name:     'Arpa',
        quantity: 500.0,
        unitType: UnitType::Kilogram,
        price:    4.00,
    ),
];

// -------------------------------------------------------------------
// 3. e-MM isteği
// -------------------------------------------------------------------
$request = new SendProducerReceiptRequest(
    customerInfo:  $customerInfo,
    producerLines: $lines,
    issueDate:     new DateTimeImmutable('2026-05-14T09:00:00'),
    deliveryDate:  new DateTimeImmutable('2026-05-14T09:00:00'),
    currencyCode:  'TRY',
);

try {
    // -------------------------------------------------------------------
    // 4. Gönder
    // -------------------------------------------------------------------
    $response = $client->eProducerReceipt()->send($request);

    echo 'e-MM gönderildi.' . PHP_EOL;
    echo 'UUID      : ' . $response->uuid . PHP_EOL;
    echo 'Makbuz No : ' . ($response->invoiceNumber ?? '—') . PHP_EOL;

    $uuid = $response->uuid;

    // -------------------------------------------------------------------
    // 5. PDF al
    // -------------------------------------------------------------------
    $pdf = $client->eProducerReceipt()->getProducerPdf($uuid);
    file_put_contents('/tmp/emm.pdf', $pdf);
    echo 'PDF kaydedildi: /tmp/emm.pdf' . PHP_EOL;

    // -------------------------------------------------------------------
    // 6. İptal et (gerekirse)
    // -------------------------------------------------------------------
    // $client->eProducerReceipt()->cancelProducer($uuid);

    // -------------------------------------------------------------------
    // 7. E-posta ile gönder
    // -------------------------------------------------------------------
    // $client->eProducerReceipt()->sendProducerByEmail($uuid, ['muhasebe@sirket.com']);

} catch (ValidationException $e) {
    echo 'Doğrulama hatası:' . PHP_EOL;
    foreach ($e->getErrors() as $field => $messages) {
        echo "  [{$field}] " . implode(', ', (array) $messages) . PHP_EOL;
    }
} catch (ApiException $e) {
    echo 'API hatası (' . $e->getStatusCode() . '): ' . $e->getMessage() . PHP_EOL;
}
