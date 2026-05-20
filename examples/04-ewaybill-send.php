<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Enums\DespatchProfile;
use Nilvera\Enums\DespatchType;
use Nilvera\Enums\UnitType;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\ValidationException;
use Nilvera\NilveraClient;
use Nilvera\Requests\SendWaybillRequest;
use Nilvera\Requests\ValueObjects\AddressInfoRequest;
use Nilvera\Requests\ValueObjects\CarrierInfoRequest;
use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\DriverPersonRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ShipmentDetailRequest;
use Nilvera\Requests\ValueObjects\ShipmentInfoRequest;
use Nilvera\Requests\ValueObjects\WaybillDeliveryRequest;
use Nilvera\Requests\ValueObjects\WaybillOrderReferenceRequest;

$apiKey = $_SERVER['NILVERA_API_KEY'] ?? $_ENV['NILVERA_API_KEY'] ?? 'GECERSIZ-API-KEY';
$client = NilveraClient::test($apiKey);

// -------------------------------------------------------------------
// 1. Alıcı bilgileri
// -------------------------------------------------------------------
$customerInfo = new ReceiverRequest(
    taxNumber: '6310540565',
    name:      'XYZ Dağıtım Ltd. Şti.',
    address:   'Sanayi Cad. No:12',
    district:  'Pendik',
    city:      'İstanbul',
    country:   'Türkiye',
    taxOffice: 'Pendik',
    postalCode: '34890',
);

// -------------------------------------------------------------------
// 2. İrsaliye kalemleri
// -------------------------------------------------------------------
$lines = [
    new DespatchLineRequest(
        name:              'Ürün A',
        deliveredUnitType: UnitType::Piece,
        deliveredQuantity: 2.0,
        sellerCode:        'STK-001',
        deliveredUnitName: 'Adet',
        quantityPrice:     150.00,
        lineTotal:         300.00,
    ),
    new DespatchLineRequest(
        name:              'Ürün B',
        deliveredUnitType: UnitType::Piece,
        deliveredQuantity: 5.0,
        sellerCode:        'STK-002',
        deliveredUnitName: 'Adet',
        quantityPrice:     50.00,
        lineTotal:         250.00,
    ),
];

// -------------------------------------------------------------------
// 3. Sevkiyat detayı (şoför + araç + teslimat adresi)
// -------------------------------------------------------------------
$shipmentDetail = new ShipmentDetailRequest(
    shipmentInfo: new ShipmentInfoRequest(
        licensePlateId: '34ABC123',
        driverPersons: [
            new DriverPersonRequest(
                firstName: 'Ahmet',
                lastName:  'Yılmaz',
                taxNumber: '11111111111',
            ),
        ],
    ),
    delivery: new WaybillDeliveryRequest(
        addressInfo: new AddressInfoRequest(
            address:    'Sanayi Cad. No:12',
            district:   'Pendik',
            city:       'İstanbul',
            country:    'Türkiye',
            postalCode: '34890',
        ),
        // Taşıyıcı firma opsiyoneldir; eklenirse tüm alanları zorunludur:
        // carrierInfo: new CarrierInfoRequest(
        //     taxNumber:  '1288331521',
        //     name:       'Örnek Kargo Lojistik A.Ş.',
        //     address:    'Kargo Merkezi No:1',
        //     district:   'Pendik',
        //     city:       'İstanbul',
        //     country:    'Türkiye',
        //     postalCode: '34890',
        // ),
    ),
);

// -------------------------------------------------------------------
// 4. e-İrsaliye isteği
// -------------------------------------------------------------------
$request = new SendWaybillRequest(
    customerAlias:          'urn:mail:defaultpk@nilvera.com',
    customerInfo:           $customerInfo,
    despatchLines:          $lines,
    issueDate:              new DateTimeImmutable(),
    despatchSerieOrNumber:  'EIT',
    despatchType:           DespatchType::Sevk,
    despatchProfile:        DespatchProfile::TemelIrsaliye,
    actualDespatchDateTime: new DateTimeImmutable(),
    shipmentDetail:         $shipmentDetail,
    // Sipariş referansı opsiyoneldir:
    // orderReference: new WaybillOrderReferenceRequest(
    //     id:        'SIP-2026-001',
    //     issueDate: new DateTimeImmutable('2026-05-14'),
    // ),
    notes: ['Kırılgan ürün, dikkatli taşıyınız.'],
);

try {
    // -------------------------------------------------------------------
    // 5. Gönder
    // -------------------------------------------------------------------
    $response = $client->eWaybill()->send($request);

    echo 'e-İrsaliye gönderildi.' . PHP_EOL;
    echo 'UUID          : ' . $response->uuid . PHP_EOL;
    echo 'İrsaliye No   : ' . $response->despatchNumber . PHP_EOL;

    $uuid = $response->uuid;

    // -------------------------------------------------------------------
    // 6. PDF al
    // -------------------------------------------------------------------
    $pdf = $client->eWaybill()->getSaleWaybillPdf($uuid);
    file_put_contents('/tmp/irsaliye.pdf', $pdf);
    echo 'PDF kaydedildi: /tmp/irsaliye.pdf' . PHP_EOL;

    // -------------------------------------------------------------------
    // 7. İptal et
    // -------------------------------------------------------------------
    // $client->eWaybill()->cancelSaleWaybill($uuid);

    // -------------------------------------------------------------------
    // 8. Gelen irsaliyeleri kabul / reddet
    // -------------------------------------------------------------------
    // $purchaseWaybills = $client->eWaybill()->listPurchaseWaybills();
    // $incomingUuid = $purchaseWaybills['Data'][0]['UUID'] ?? null;
    // if ($incomingUuid) {
    //     $client->eWaybill()->acceptPurchaseWaybill($incomingUuid);
    //     // veya reddetmek için:
    //     // $client->eWaybill()->rejectPurchaseWaybill($incomingUuid, ['Reason' => 'Eksik ürün']);
    // }

} catch (ValidationException $e) {
    echo $e->getSummary() . PHP_EOL . PHP_EOL;
    echo $e->toDebugString() . PHP_EOL;
} catch (ApiException $e) {
    echo $e->toDebugString() . PHP_EOL;
}
