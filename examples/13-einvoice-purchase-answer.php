<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Exception\ApiException;
use Nilvera\Exception\ConflictException;
use Nilvera\NilveraClient;
use Nilvera\Requests\ListInvoicesRequest;

// -------------------------------------------------------------------
// 1. İstemciyi başlat
// -------------------------------------------------------------------
$apiKey = $_SERVER['NILVERA_API_KEY'] ?? $_ENV['NILVERA_API_KEY'] ?? 'GECERSIZ-API-KEY';
$client = NilveraClient::test($apiKey);

// -------------------------------------------------------------------
// 2. Onay bekleyen gelen faturaları listele
// -------------------------------------------------------------------
$list = $client->eInvoice()->listPurchaseInvoices(
    new ListInvoicesRequest(page: 1, pageSize: 20)
);

echo 'Toplam gelen fatura: ' . ($list['TotalCount'] ?? 0) . PHP_EOL;

// Onay bekleyen fatura bul
$waitingInvoice = null;
foreach ($list['Content'] ?? [] as $invoice) {
    if (($invoice['AnswerCode'] ?? '') === 'waitingForApproval') {
        $waitingInvoice = $invoice;
        break;
    }
}

if ($waitingInvoice === null) {
    echo 'Onay bekleyen (waitingForApproval) gelen fatura bulunamadı.' . PHP_EOL;
    exit(0);
}

$uuid = $waitingInvoice['UUID'];
echo PHP_EOL;
echo 'İşlenecek fatura:' . PHP_EOL;
echo '  UUID          : ' . $uuid . PHP_EOL;
echo '  Gönderen      : ' . ($waitingInvoice['SenderName'] ?? '—') . PHP_EOL;
echo '  Fatura No     : ' . ($waitingInvoice['InvoiceNumber'] ?? '—') . PHP_EOL;
echo '  Tutar         : ' . ($waitingInvoice['PayableAmount'] ?? '—') . ' ' . ($waitingInvoice['CurrencyCode'] ?? '') . PHP_EOL;
echo PHP_EOL;

// -------------------------------------------------------------------
// 3. Statü bilgisini getir — kabul/red kararı öncesi kontrol
// -------------------------------------------------------------------
try {
    $status = $client->eInvoice()->getPurchaseInvoiceStatus($uuid);

    echo 'Statü bilgisi:' . PHP_EOL;
    echo '  Profil        : ' . ($status['InvoiceProfile'] ?? '—') . PHP_EOL;
    echo '  Cevap Kodu    : ' . ($status['Answer']['AnswerCode'] ?? '—') . PHP_EOL;
    echo '  GIB Kodu      : ' . ($status['EnvelopeInfo']['GIBCode'] ?? '—') . PHP_EOL;
    echo '  GIB Açıklama  : ' . ($status['EnvelopeInfo']['GIBDescription'] ?? '—') . PHP_EOL;
    echo PHP_EOL;
} catch (ApiException $e) {
    echo 'Statü alınamadı: ' . $e->getMessage() . PHP_EOL;
}

// -------------------------------------------------------------------
// 4a. Faturayı kabul et
// -------------------------------------------------------------------
try {
    $acceptResult = $client->eInvoice()->acceptInvoice($uuid);
    echo 'Fatura KABUL edildi.' . PHP_EOL;
    echo '  Yanıt: ' . $acceptResult . PHP_EOL;
} catch (ConflictException $e) {
    // 409 — fatura daha önce yanıtlanmış
    echo 'Fatura zaten yanıtlanmış (409 Conflict): ' . $e->getMessage() . PHP_EOL;
} catch (ApiException $e) {
    echo 'Kabul işlemi başarısız: ' . $e->toDebugString() . PHP_EOL;
}

echo PHP_EOL;

// -------------------------------------------------------------------
// 4b. Faturayı reddet (aşağıdaki bloğu kullanmak için 4a'yı yorum satırına alın)
// -------------------------------------------------------------------
/*
try {
    $rejectResult = $client->eInvoice()->rejectInvoice(
        uuid:       $uuid,
        rejectNote: 'Fatura tutarı ve/veya kalemleri hatalıdır.',
    );
    echo 'Fatura REDDEDİLDİ.' . PHP_EOL;
    echo '  Yanıt: ' . $rejectResult . PHP_EOL;
} catch (ConflictException $e) {
    echo 'Fatura zaten yanıtlanmış (409 Conflict): ' . $e->getMessage() . PHP_EOL;
} catch (ApiException $e) {
    echo 'Red işlemi başarısız: ' . $e->toDebugString() . PHP_EOL;
}
*/
