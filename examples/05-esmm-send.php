<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Exception\ApiException;
use Nilvera\Exception\ValidationException;
use Nilvera\NilveraClient;
use Nilvera\Requests\SendVoucherRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\VoucherLineRequest;

$client = NilveraClient::test('TEST-API-KEY-BURAYA');

// -------------------------------------------------------------------
// 1. Alıcı bilgileri (makbuzu ödeyen kurum)
// -------------------------------------------------------------------
$customerInfo = new ReceiverRequest(
    taxNumber: '3230456015',
    name:      'ABC Yazılım A.Ş.',
    address:   'Atatürk Cad. No:1',
    district:  'Kadıköy',
    city:      'İstanbul',
    taxOffice: 'Kadıköy',
);

// -------------------------------------------------------------------
// 2. Makbuz kalemleri
//    GrossWage: brüt ücret, Price: net ücret
//    GVWithholdingPercent/Total: Gelir Vergisi Stopajı
// -------------------------------------------------------------------
$lines = [
    new VoucherLineRequest(
        name:                  'Yazılım Danışmanlığı',
        grossWage:             5000.00,
        price:                 4200.00,   // brüt - GV stopajı
        kdvPercent:            0.0,       // e-SMM genellikle KDV'siz
        kdvTotal:              0.0,
        gvWithholdingPercent:  16.0,      // %16 GV stopajı
        gvWithholdingTotal:    800.00,
    ),
];

// -------------------------------------------------------------------
// 3. e-SMM isteği
//    sendType: 'ELEKTRONIK' (varsayılan) veya 'KAGIT'
// -------------------------------------------------------------------
$request = new SendVoucherRequest(
    customerInfo: $customerInfo,
    voucherLines: $lines,
    issueDate:    new DateTimeImmutable('2026-05-14T10:00:00'),
    sendType:     'ELEKTRONIK',
    currencyCode: 'TRY',
);

try {
    // -------------------------------------------------------------------
    // 4. Gönder
    // -------------------------------------------------------------------
    $response = $client->eSelfEmployed()->send($request);

    echo 'e-SMM gönderildi.' . PHP_EOL;
    echo 'UUID      : ' . $response->uuid . PHP_EOL;
    echo 'Makbuz No : ' . ($response->invoiceNumber ?? '—') . PHP_EOL;

    $uuid = $response->uuid;

    // -------------------------------------------------------------------
    // 5. PDF al
    // -------------------------------------------------------------------
    $pdf = $client->eSelfEmployed()->getVoucherPdf($uuid);
    file_put_contents('/tmp/esmm.pdf', $pdf);
    echo 'PDF kaydedildi: /tmp/esmm.pdf' . PHP_EOL;

    // -------------------------------------------------------------------
    // 6. İptal et (gerekirse)
    // -------------------------------------------------------------------
    // $client->eSelfEmployed()->cancelVoucher($uuid);

    // -------------------------------------------------------------------
    // 7. E-posta ile gönder
    // -------------------------------------------------------------------
    // $client->eSelfEmployed()->sendVoucherByEmail($uuid, ['muhasebe@abc.com']);

} catch (ValidationException $e) {
    echo 'Doğrulama hatası:' . PHP_EOL;
    foreach ($e->getErrors() as $field => $messages) {
        echo "  [{$field}] " . implode(', ', (array) $messages) . PHP_EOL;
    }
} catch (ApiException $e) {
    echo 'API hatası (' . $e->getStatusCode() . '): ' . $e->getMessage() . PHP_EOL;
}
