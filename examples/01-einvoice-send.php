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
$apiKey = $_SERVER['NILVERA_API_KEY'] ?? $_ENV['NILVERA_API_KEY'] ?? 'GECERSIZ-API-KEY';
$client = NilveraClient::test($apiKey);

// -------------------------------------------------------------------
// 2. DTO'ları doldur
// -------------------------------------------------------------------

// Alıcı bilgileri
$customerInfo = new ReceiverRequest(
    taxNumber: '6310540565',                    // 10 haneli VKN veya 11 haneli TCKN
    name:      'Nilvera E-Fatura Test Alicisi',
    address:   'Test Mah. No:1',
    district:  'Kadikoy',
    city:      'Istanbul',
    taxOffice: 'Kadikoy',                       // e-Fatura'da zorunlu
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
];

// Fatura isteği
$request = new SendInvoiceRequest(
    customerInfo:         $customerInfo,
    invoiceLines:         $lines,
    issueDate:            new DateTimeImmutable(),
    customerAlias:        'urn:mail:defaultpk@nilvera.com', // GIB'e kayıtlı alıcı alias'ı
    invoiceSerieOrNumber: 'ABB',                           // 3 haneli seri kodu veya 16 haneli fatura numarası
    invoiceProfile:       InvoiceProfile::Basic,
    invoiceType:          InvoiceType::Sales,
    currencyCode:         'TRY',
    notes:                ['Ödeme vadesi 30 gündür.'],
);

// -------------------------------------------------------------------
// 3. Gönder ve yanıtı işle
// -------------------------------------------------------------------
try {
    // Göndermeden önce HTML önizleme (opsiyonel)
    $preview = $client->eInvoice()->preview($request);
    file_put_contents('/tmp/einvoice_preview.html', $preview);
    echo 'Önizleme kaydedildi: /tmp/einvoice_preview.html' . PHP_EOL;

    $response = $client->eInvoice()->send($request);

    echo 'Fatura gönderildi.' . PHP_EOL;
    echo 'UUID          : ' . $response->uuid . PHP_EOL;
    echo 'Fatura No     : ' . ($response->invoiceNumber ?? '—') . PHP_EOL;

} catch (ValidationException $e) {
    echo $e->getSummary() . PHP_EOL . PHP_EOL;
    echo $e->toDebugString() . PHP_EOL;
} catch (ApiException $e) {
    echo $e->toDebugString() . PHP_EOL;
}
