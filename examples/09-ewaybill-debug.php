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
use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\DriverPersonRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ShipmentDetailRequest;
use Nilvera\Requests\ValueObjects\ShipmentInfoRequest;
use Nilvera\Requests\ValueObjects\WaybillDeliveryRequest;

// -------------------------------------------------------------------
// e-İrsaliye gönderme — başarısız yanıtta tam debug çıktısı
//
// Çalıştırmak için:
//   NILVERA_API_KEY=<anahtarınız> php examples/09-ewaybill-debug.php
// -------------------------------------------------------------------

$apiKey = $_SERVER['NILVERA_API_KEY'] ?? $_ENV['NILVERA_API_KEY'] ?? 'GECERSIZ-API-KEY';
$client = NilveraClient::test($apiKey);

$request = new SendWaybillRequest(
    customerAlias: 'urn:mail:defaultpk@nilvera.com',
    customerInfo: new ReceiverRequest(
        taxNumber:  '6310540565',
        name:       'XYZ Dağıtım Ltd. Şti.',
        address:    'Sanayi Cad. No:12',
        district:   'Pendik',
        city:       'İstanbul',
        country:    'TR',
        taxOffice:  'Pendik',
        postalCode: '34890',
    ),
    despatchLines: [
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
    ],
    issueDate:              new DateTimeImmutable('2026-05-19T08:30:00'),
    despatchType:           DespatchType::Sevk,
    despatchProfile:        DespatchProfile::TemelIrsaliye,
    despatchSerieOrNumber:  'EIT',
    actualDespatchDateTime: new DateTimeImmutable('2026-05-19T08:30:00'),
    shipmentDetail: new ShipmentDetailRequest(
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
                country:    'TR',
                postalCode: '34890',
            ),
        ),
    ),
    notes: ['Kırılgan ürün, dikkatli taşıyınız.'],
);

try {
    $response = $client->eWaybill()->send($request);

    echo 'e-İrsaliye gönderildi.' . PHP_EOL;
    echo 'UUID        : ' . $response->uuid . PHP_EOL;
    echo 'İrsaliye No : ' . $response->despatchNumber . PHP_EOL;

} catch (ValidationException $e) {
    echo '=== Alan Hataları (getSummary) ===' . PHP_EOL;
    $summary = $e->getSummary();
    echo ($summary !== '' ? $summary : '(Alan hatası detayı yok)') . PHP_EOL . PHP_EOL;
    echo $e->toDebugString() . PHP_EOL;

} catch (ApiException $e) {
    echo $e->toDebugString() . PHP_EOL;
}
