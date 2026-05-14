<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nilvera\Exception\ApiException;
use Nilvera\Exception\NotFoundException;
use Nilvera\NilveraClient;
use Nilvera\Requests\CreateCustomerRequest;

$client = NilveraClient::test('TEST-API-KEY-BURAYA');

try {
    // -------------------------------------------------------------------
    // Şirket bilgilerini al
    // -------------------------------------------------------------------
    $company = $client->general()->getCompany();
    echo 'Şirket: ' . $company['Name'] . PHP_EOL;

    // -------------------------------------------------------------------
    // Mükellef sorgula (e-Fatura'ya kayıtlı mı?)
    // -------------------------------------------------------------------
    $taxpayer = $client->general()->checkTaxpayer('3230456015');
    $registered = $taxpayer['IsEInvoiceUser'] ?? false;
    echo 'e-Fatura kullanıcısı: ' . ($registered ? 'Evet' : 'Hayır') . PHP_EOL;

    // İsme göre mükellef ara
    $results = $client->general()->searchTaxpayers('Nilvera');
    echo 'Arama sonucu: ' . count($results) . ' kayıt' . PHP_EOL;

    // VKN ile detay al
    $info = $client->general()->getTaxpayerByTaxNumber('3230456015');
    echo 'Alıcı adı: ' . ($info['Name'] ?? '—') . PHP_EOL;

    // -------------------------------------------------------------------
    // Döviz kurları
    // -------------------------------------------------------------------
    $rates = $client->general()->getExchangeRates();
    echo 'Döviz kuru sayısı: ' . count($rates) . PHP_EOL;

    // -------------------------------------------------------------------
    // Müşteri yönetimi
    // -------------------------------------------------------------------

    // Yeni müşteri oluştur
    $newCustomer = $client->general()->createCustomer(
        new CreateCustomerRequest(
            taxNumber: '3230456015',
            name:      'ABC Yazılım A.Ş.',
            address:   'Atatürk Cad. No:1',
            district:  'Kadıköy',
            city:      'İstanbul',
            taxOffice: 'Kadıköy',
            mail:      'muhasebe@abc.com.tr',
            phone:     '+902121234567',
        )
    );
    echo 'Müşteri oluşturuldu, ID: ' . ($newCustomer['Id'] ?? '—') . PHP_EOL;

    // Müşterileri listele
    $customers = $client->general()->listCustomers(['Page' => 1, 'PageSize' => 10]);
    echo 'Toplam müşteri: ' . ($customers['TotalCount'] ?? '?') . PHP_EOL;

    // VKN ile müşteri getir
    $customer = $client->general()->getCustomerByTaxNumber('3230456015');
    echo 'Müşteri: ' . ($customer['Name'] ?? '—') . PHP_EOL;

    // -------------------------------------------------------------------
    // Stok / Ürün yönetimi
    // -------------------------------------------------------------------

    // Yeni ürün oluştur
    $stock = $client->general()->createStock([
        'Name'     => 'Yazılım Lisansı',
        'Code'     => 'LIC-001',
        'UnitType' => 'C62',
        'Price'    => 1000.00,
        'KDVRate'  => 20,
    ]);
    echo 'Stok oluşturuldu, ID: ' . ($stock['Id'] ?? '—') . PHP_EOL;

    // Ürün ara
    $found = $client->general()->searchStocks('Lisans');
    echo 'Ürün arama sonucu: ' . count($found) . ' kayıt' . PHP_EOL;

    // -------------------------------------------------------------------
    // Kalan kredi / bakiye
    // -------------------------------------------------------------------
    $credits = $client->general()->getCredits();
    echo 'Kalan kredi: ' . ($credits['RemainingCredit'] ?? '—') . PHP_EOL;

} catch (NotFoundException $e) {
    echo 'Kayıt bulunamadı.' . PHP_EOL;
} catch (ApiException $e) {
    echo 'API hatası (' . $e->getStatusCode() . '): ' . $e->getMessage() . PHP_EOL;
}
