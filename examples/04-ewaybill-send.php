<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Enums\UnitType;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\ValidationException;
use Nilvera\NilveraClient;
use Nilvera\Requests\SendWaybillRequest;
use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

$client = NilveraClient::test('TEST-API-KEY-BURAYA');

// -------------------------------------------------------------------
// 1. Alıcı bilgileri
// -------------------------------------------------------------------
$customerInfo = new ReceiverRequest(
    taxNumber: '3230456015',
    name:      'XYZ Dağıtım Ltd. Şti.',
    address:   'Sanayi Cad. No:12',
    district:  'Pendik',
    city:      'İstanbul',
    taxOffice: 'Pendik',
);

// -------------------------------------------------------------------
// 2. İrsaliye kalemleri
// -------------------------------------------------------------------
$lines = [
    new DespatchLineRequest(
        sellerCode:        'STK-001',
        quantityPrice:     150.00,
        lineTotal:         300.00,
        name:              'Ürün A',
        deliveredUnitType: UnitType::Piece,
        deliveredQuantity: 2.0,
    ),
    new DespatchLineRequest(
        sellerCode:        'STK-002',
        quantityPrice:     50.00,
        lineTotal:         250.00,
        name:              'Ürün B',
        deliveredUnitType: UnitType::Piece,
        deliveredQuantity: 5.0,
    ),
];

// -------------------------------------------------------------------
// 3. e-İrsaliye isteği
//    despatchType: 1 = SEVK (varsayılan), 0 = MATBUDAN
//    despatchProfile: 1 = TEMELIRSALIYE (varsayılan), 2 = HKSIRSALIYE
// -------------------------------------------------------------------
$request = new SendWaybillRequest(
    customerInfo:          $customerInfo,
    despatchLines:         $lines,
    issueDate:             new DateTimeImmutable('2026-05-14'),
    despatchType:          1,
    despatchProfile:       1,
    actualDespatchDateTime: new DateTimeImmutable('2026-05-14T08:30:00'),
    notes:                 ['Kırılgan ürün, dikkatli taşıyınız.'],
);

try {
    // -------------------------------------------------------------------
    // 4. Gönder
    // -------------------------------------------------------------------
    $response = $client->eWaybill()->send($request);

    echo 'e-İrsaliye gönderildi.' . PHP_EOL;
    echo 'UUID         : ' . $response->uuid . PHP_EOL;
    echo 'İrsaliye No  : ' . ($response->invoiceNumber ?? '—') . PHP_EOL;

    $uuid = $response->uuid;

    // -------------------------------------------------------------------
    // 5. PDF al
    // -------------------------------------------------------------------
    $pdf = $client->eWaybill()->getSaleWaybillPdf($uuid);
    file_put_contents('/tmp/irsaliye.pdf', $pdf);
    echo 'PDF kaydedildi: /tmp/irsaliye.pdf' . PHP_EOL;

    // -------------------------------------------------------------------
    // 6. İptal et
    // -------------------------------------------------------------------
    // $client->eWaybill()->cancelSaleWaybill($uuid);

    // -------------------------------------------------------------------
    // 7. Gelen irsaliyeleri kabul / reddet
    // -------------------------------------------------------------------
    // $purchaseWaybills = $client->eWaybill()->listPurchaseWaybills();
    // $incomingUuid = $purchaseWaybills['Data'][0]['UUID'] ?? null;
    // if ($incomingUuid) {
    //     $client->eWaybill()->acceptPurchaseWaybill($incomingUuid);
    //     // veya reddetmek için:
    //     // $client->eWaybill()->rejectPurchaseWaybill($incomingUuid, ['Reason' => 'Eksik ürün']);
    // }

} catch (ValidationException $e) {
    echo 'Doğrulama hatası:' . PHP_EOL;
    foreach ($e->getErrors() as $field => $messages) {
        echo "  [{$field}] " . implode(', ', (array) $messages) . PHP_EOL;
    }
} catch (ApiException $e) {
    echo 'API hatası (' . $e->getStatusCode() . '): ' . $e->getMessage() . PHP_EOL;
}
