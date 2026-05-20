<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Exception\ApiException;
use Nilvera\NilveraClient;
use Nilvera\Requests\CreateSeriesRequest;
use Nilvera\Requests\ListSeriesRequest;
use Nilvera\Requests\UpdateSeriesRequest;

$apiKey = $_SERVER['NILVERA_API_KEY'] ?? $_ENV['NILVERA_API_KEY'] ?? 'GECERSIZ-API-KEY';
$client = NilveraClient::test($apiKey);

try {
    // -------------------------------------------------------------------
    // 1. Serileri listele
    // -------------------------------------------------------------------
    $all = $client->eInvoice()->listSeries();
    echo 'Toplam seri: ' . ($all['TotalCount'] ?? '?') . PHP_EOL;

    // Sadece aktif serileri listele (filtreli)
    $active = $client->eInvoice()->listSeries(
        new ListSeriesRequest(isActive: true, pageSize: 10)
    );
    echo 'Aktif seri: ' . count($active['Content'] ?? []) . PHP_EOL;

    // -------------------------------------------------------------------
    // 2. Yeni seri oluştur (benzersiz 3 harfli isim)
    // -------------------------------------------------------------------
    $uniqueName = strtoupper(substr(md5((string) microtime(true)), 0, 3));
    $newSeries  = $client->eInvoice()->createSeries(
        new CreateSeriesRequest(
            name:      $uniqueName,
            isActive:  true,
            isDefault: false,
        )
    );

    $seriesId = $newSeries['ID'];
    echo 'Yeni seri oluşturuldu — ID: ' . $seriesId . ', Ad: ' . $newSeries['Name'] . PHP_EOL;

    // -------------------------------------------------------------------
    // 3. Serinin detayını getir
    // -------------------------------------------------------------------
    $detail = $client->eInvoice()->getSeries($seriesId);
    echo 'Seri detayı:' . PHP_EOL;
    echo '  ID        : ' . $detail['ID'] . PHP_EOL;
    echo '  Ad        : ' . $detail['Name'] . PHP_EOL;
    echo '  Aktif     : ' . ($detail['IsActive'] ? 'Evet' : 'Hayır') . PHP_EOL;
    echo '  Varsayılan: ' . ($detail['IsDefault'] ? 'Evet' : 'Hayır') . PHP_EOL;
    echo '  Detay sayısı: ' . count($detail['Details'] ?? []) . PHP_EOL;

    // -------------------------------------------------------------------
    // 4. Seriyi düzenle (pasif yap)
    //    isDefault ve isActive her ikisi de zorunludur.
    // -------------------------------------------------------------------
    $updated = $client->eInvoice()->updateSeries(
        new UpdateSeriesRequest(id: $seriesId, isDefault: false, isActive: false)
    );
    echo 'Seri güncellendi: ' . ($updated ? 'başarılı' : 'başarısız') . PHP_EOL;

    // Değişikliği doğrula
    $after = $client->eInvoice()->getSeries($seriesId);
    echo 'Güncel aktiflik durumu: ' . ($after['IsActive'] ? 'Aktif' : 'Pasif') . PHP_EOL;

} catch (ApiException $e) {
    echo $e->toDebugString() . PHP_EOL;
}
