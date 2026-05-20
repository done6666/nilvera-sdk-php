<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Exception\ApiException;
use Nilvera\Exception\ValidationException;
use Nilvera\NilveraClient;
use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\SendArchiveInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

$apiKey = $_SERVER['NILVERA_API_KEY'] ?? $_ENV['NILVERA_API_KEY'] ?? 'GECERSIZ-API-KEY';
$client = NilveraClient::test($apiKey);

// -------------------------------------------------------------------
// 1. Alıcı bilgileri
//    e-Arşiv'de taxOffice zorunlu değildir (bireysel müşteri olabilir)
// -------------------------------------------------------------------
$customerInfo = new ReceiverRequest(
    taxNumber: '10000000146',   // 11 haneli geçerli TCKN (e-fatura mükellefi değil)
    name:      'Ahmet Yılmaz',
    address:   'Bağcılar Cad. No:5 Daire:3',
    district:  'Bağcılar',
    city:      'İstanbul',
);

// -------------------------------------------------------------------
// 2. Fatura kalemleri
// -------------------------------------------------------------------
$lines = [
    InvoiceLineRequest::make(
        name:       'Web Tasarım Hizmeti',
        quantity:   1,
        unitType:   UnitType::Piece,
        price:      2500.00,
        kdvPercent: 20,
    ),
    InvoiceLineRequest::make(
        name:       'Hosting (Yıllık)',
        quantity:   1,
        unitType:   UnitType::Piece,
        price:      500.00,
        kdvPercent: 20,
    ),
];

// -------------------------------------------------------------------
// 3. e-Arşiv fatura isteği
//    InvoiceProfile otomatik olarak EARSIVFATURA atanır
// -------------------------------------------------------------------
$request = new SendArchiveInvoiceRequest(
    customerInfo:          $customerInfo,
    invoiceLines:          $lines,
    issueDate:             new DateTimeImmutable('2026-05-19T09:00:00'),
    invoiceSerieOrNumber:  'ABC',
    invoiceType:           InvoiceType::Sales,
    currencyCode:          'TRY',
    notes:                 ['Lütfen ödemeyi banka havalesi ile yapınız.'],
);

try {
    // -------------------------------------------------------------------
    // 4. Önizle (opsiyonel — göndermeden HTML olarak kontrol et)
    // -------------------------------------------------------------------
    $preview = $client->eArchive()->previewSend($request);
    file_put_contents('/tmp/earsiv_preview.html', $preview);
    echo 'Önizleme kaydedildi: /tmp/earsiv_preview.html' . PHP_EOL;

    // -------------------------------------------------------------------
    // 5. Gönder
    // -------------------------------------------------------------------
    $response = $client->eArchive()->send($request);

    echo 'e-Arşiv fatura gönderildi.' . PHP_EOL;
    echo 'UUID      : ' . $response->uuid . PHP_EOL;
    echo 'Fatura No : ' . ($response->invoiceNumber ?? '—') . PHP_EOL;

    $uuid = $response->uuid;

    // -------------------------------------------------------------------
    // 5. Belgeyi al
    // -------------------------------------------------------------------
    $pdf = $client->eArchive()->getInvoicePdf($uuid);
    file_put_contents('/tmp/earsiv.pdf', $pdf);
    echo 'PDF kaydedildi: /tmp/earsiv.pdf' . PHP_EOL;

    // -------------------------------------------------------------------
    // 6. GIB'e rapor gönder (günlük toplu işlem sonunda)
    // -------------------------------------------------------------------
    // $client->eArchive()->sendReport();

    // -------------------------------------------------------------------
    // 7. İptal et (gerekirse)
    // -------------------------------------------------------------------
    // $client->eArchive()->cancelInvoice($uuid);

    // -------------------------------------------------------------------
    // 8. Listeleme
    // -------------------------------------------------------------------
    $params  = new ListInvoicesRequest(
        startDate: new DateTimeImmutable('2026-01-01'),
        endDate:   new DateTimeImmutable('2026-05-14'),
        page:      1,
        pageSize:  10,
    );
    $invoices = $client->eArchive()->listInvoices($params);
    echo 'Toplam e-Arşiv fatura: ' . ($invoices['TotalCount'] ?? '?') . PHP_EOL;

} catch (ValidationException $e) {
    echo $e->getSummary() . PHP_EOL . PHP_EOL;
    echo $e->toDebugString() . PHP_EOL;
} catch (ApiException $e) {
    echo $e->toDebugString() . PHP_EOL;
}
