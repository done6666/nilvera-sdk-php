<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Exception\ApiException;
use Nilvera\NilveraClient;
use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\SendByEmailRequest;
use Nilvera\Requests\SendBySmsRequest;

$client = NilveraClient::test('TEST-API-KEY-BURAYA');

// -------------------------------------------------------------------
// Giden (satış) faturaları listele
// -------------------------------------------------------------------
$params = new ListInvoicesRequest(
    startDate: new DateTimeImmutable('2026-01-01'),
    endDate:   new DateTimeImmutable('2026-05-14'),
    page:      1,
    pageSize:  20,
);

try {
    $result = $client->eInvoice()->listSaleInvoices($params);

    echo 'Toplam fatura: ' . ($result['TotalCount'] ?? '?') . PHP_EOL;
    foreach (($result['Data'] ?? []) as $invoice) {
        echo '  ' . $invoice['UUID'] . ' — ' . $invoice['InvoiceNumber'] . PHP_EOL;
    }

    // -------------------------------------------------------------------
    // Belirli bir faturayı HTML / PDF / XML olarak al
    // -------------------------------------------------------------------
    $uuid = $result['Data'][0]['UUID'] ?? null;

    if ($uuid !== null) {
        $html = $client->eInvoice()->getSaleInvoiceHtml($uuid);
        file_put_contents('/tmp/fatura.html', $html);
        echo 'HTML kaydedildi: /tmp/fatura.html' . PHP_EOL;

        $pdf = $client->eInvoice()->getSaleInvoicePdf($uuid);
        file_put_contents('/tmp/fatura.pdf', $pdf);
        echo 'PDF  kaydedildi: /tmp/fatura.pdf' . PHP_EOL;

        // -------------------------------------------------------------------
        // E-posta ile gönder
        // -------------------------------------------------------------------
        $client->eInvoice()->sendSaleInvoiceByEmail(
            new SendByEmailRequest($uuid, ['muhasebe@musteri.com'])
        );
        echo 'E-posta gönderildi.' . PHP_EOL;

        // -------------------------------------------------------------------
        // SMS ile gönder
        // -------------------------------------------------------------------
        $client->eInvoice()->sendSaleInvoiceBySms(
            new SendBySmsRequest($uuid, ['+905001234567'])
        );
        echo 'SMS gönderildi.' . PHP_EOL;

        // -------------------------------------------------------------------
        // Zarfı iptal et
        // -------------------------------------------------------------------
        // $client->eInvoice()->cancelSaleInvoice($uuid);
    }

    // -------------------------------------------------------------------
    // Gelen (alış) faturaları listele
    // -------------------------------------------------------------------
    $purchaseParams = new ListInvoicesRequest(
        startDate: new DateTimeImmutable('2026-01-01'),
        endDate:   new DateTimeImmutable('2026-05-14'),
        page:      1,
        pageSize:  10,
    );

    $purchases = $client->eInvoice()->listPurchaseInvoices($purchaseParams);
    echo 'Gelen fatura sayısı: ' . ($purchases['TotalCount'] ?? '?') . PHP_EOL;

    // GIB'ten gelen faturaları senkronize et
    $client->eInvoice()->syncPurchaseFromGib();
    echo 'GIB senkronizasyonu tamamlandı.' . PHP_EOL;

    // -------------------------------------------------------------------
    // Taslaklar
    // -------------------------------------------------------------------
    $drafts = $client->eInvoice()->listDrafts(['Page' => 1, 'PageSize' => 5]);
    echo 'Taslak sayısı: ' . count($drafts['Data'] ?? []) . PHP_EOL;

} catch (ApiException $e) {
    echo 'API hatası (' . $e->getStatusCode() . '): ' . $e->getMessage() . PHP_EOL;
}
