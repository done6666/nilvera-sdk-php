# Nilvera PHP SDK

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

[Nilvera](https://www.nilvera.com) e-belge API'si için PHP SDK. E-Fatura, E-Arşiv, E-İrsaliye, E-SMM, E-MM, E-SKGB, E-Adisyon, E-Saklama ve E-Defter servislerini destekler.

## İçindekiler

- [Gereksinimler](#gereksinimler)
- [Kurulum](#kurulum)
- [Hızlı Başlangıç](#hızlı-başlangıç)
- [Servisler](#servisler)
- [E-Fatura (eInvoice)](#e-fatura-einvoice)
  - [InvoiceBuilder ile Fluent API](#invoicebuilder-ile-fluent-api)
  - [E-Fatura Gönderme](#e-fatura-gönderme)
  - [Gelen Faturalar](#gelen-faturalar)
  - [Taslak Faturalar](#taslak-faturalar)
  - [XML / Base64 ile Gönderme](#xml--base64-ile-gönderme)
  - [Önizleme ve PDF İndirme](#önizleme-ve-pdf-i̇ndirme)
  - [İletişim Kanalları](#i̇letişim-kanalları)
  - [Seri & Şablon Yönetimi](#seri--şablon-yönetimi)
  - [İstatistikler](#i̇statistikler)
- [E-Arşiv (eArchive)](#e-arşiv-earchive)
- [E-İrsaliye (eWaybill)](#e-i̇rsaliye-ewaybill)
- [E-SMM (eSelfEmployed)](#e-smm-eselfemployed)
- [E-MM (eProducerReceipt)](#e-mm-eproducerreceipt)
- [Genel (general)](#genel-general)
- [Hata Yönetimi](#hata-yönetimi)
- [İstek Sınıfları](#i̇stek-sınıfları)
- [Enum Referansı](#enum-referansı)
- [Gelişmiş Yapılandırma](#gelişmiş-yapılandırma)
- [Test](#test)

---

## Gereksinimler

- PHP 8.1+
- Guzzle 7.5+

## Kurulum

```bash
composer require done6666/nilvera-sdk-php
```

## Hızlı Başlangıç

```php
use Nilvera\NilveraClient;

// Canlı ortam
$client = NilveraClient::live('your-api-key');

// Test / sandbox ortamı
$client = NilveraClient::test('your-test-api-key');
```

---

## Servisler

| Erişim | Servis | Kapsam |
|---|---|---|
| `$client->general()` | Genel | Şirket, mükellef, müşteri, stok |
| `$client->eInvoice()` | E-Fatura | Giden/gelen fatura, taslak, seri, şablon |
| `$client->eArchive()` | E-Arşiv | E-arşiv faturası, rapor, seri, şablon |
| `$client->eWaybill()` | E-İrsaliye | İrsaliye gönderme, kabul/red |
| `$client->eSelfEmployed()` | E-SMM | Serbest meslek makbuzu |
| `$client->eProducerReceipt()` | E-MM | Müstahsil makbuzu |
| `$client->eInsurance()` | E-SKGB | Sigorta komisyon gider belgesi |
| `$client->eReceipt()` | E-Adisyon | Elektronik adisyon |
| `$client->eStorage()` | E-Saklama | Belge saklama |
| `$client->eLedger()` | E-Defter | Elektronik defter |
| `$client->report()` | Rapor | Raporlar |

---

## E-Fatura (eInvoice)

### InvoiceBuilder ile Fluent API

`InvoiceBuilder` zincirleme (fluent) sözdizimi sunarak fatura oluşturmayı kolaylaştırır. KDV toplamları otomatik hesaplanır.

```php
use Nilvera\Builders\InvoiceBuilder;
use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

$receiver = new ReceiverRequest(
    taxNumber: '1234567890',
    name:      'Müşteri A.Ş.',
    address:   'Atatürk Cad. No:1',
    district:  'Kadıköy',
    city:      'İstanbul',
    taxOffice: 'Kadıköy',
);

$request = InvoiceBuilder::for($receiver)
    ->issueDate(new DateTimeImmutable('2024-06-01'))
    ->profile(InvoiceProfile::Basic)
    ->type(InvoiceType::Sales)
    ->alias('urn:mail:muhasebe@musteri.com.tr')  // GIB'de kayıtlı alıcı alias'ı
    ->addLine('Yazılım Lisansı', 1, UnitType::Piece, 10_000, 20)
    ->addLine('Yıllık Destek',  12, UnitType::Month,   500, 20)
    ->note('Ödeme vadesi: 30 gün')
    ->orderReference('2024-05-15', 'SIP-2024-001')
    ->build();

$response = $client->eInvoice()->send($request);

echo $response->uuid;          // Fatura UUID
echo $response->invoiceNumber; // örn. "GIB2024000000001"
```

### E-Fatura Gönderme

`InvoiceBuilder` yerine `SendInvoiceRequest` doğrudan da kullanılabilir:

```php
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;

$request = new SendInvoiceRequest(
    customerInfo:   $receiver,
    invoiceLines:   [
        // KDVTotal otomatik hesaplanır
        InvoiceLineRequest::make('Ürün A', 2, UnitType::Piece, 100.00, 20),

        // Yüzdesel iskonto ile
        InvoiceLineRequest::make(
            name:             'Ürün B',
            quantity:         10,
            unitType:         UnitType::Piece,
            price:            500.00,
            kdvPercent:       10,
            allowancePercent: 5,   // %5 iskonto — AllowanceTotal otomatik hesaplanır
        ),

        // Sabit iskonto tutarı ile
        InvoiceLineRequest::make(
            name:           'Ürün C',
            quantity:       1,
            unitType:       UnitType::Piece,
            price:          1000.00,
            kdvPercent:     20,
            allowanceTotal: 50.00,
        ),
    ],
    issueDate:      new DateTimeImmutable('2024-06-01T10:00:00'),
    invoiceProfile: InvoiceProfile::Basic,
    invoiceType:    InvoiceType::Sales,
    // GIB'de kayıtlı alıcılar için alias zorunludur
    // customerAlias: 'urn:mail:muhasebe@musteri.com.tr',
);

$response = $client->eInvoice()->send($request);
```

### Giden Faturaları Listeleme ve Sorgulama

```php
use Nilvera\Requests\ListInvoicesRequest;

$params = new ListInvoicesRequest(
    startDate: new DateTimeImmutable('2024-01-01'),
    endDate:   new DateTimeImmutable('2024-12-31'),
    page:      1,
    pageSize:  20,
);

$invoices = $client->eInvoice()->listSaleInvoices($params);

// Fatura içeriği
$html  = $client->eInvoice()->getSaleInvoiceHtml($uuid);
$pdf   = $client->eInvoice()->getSaleInvoicePdf($uuid);  // binary
$xml   = $client->eInvoice()->getSaleInvoiceXml($uuid);
$model = $client->eInvoice()->getSaleInvoiceModel($uuid);

// Durum ve zarf bilgisi
$status       = $client->eInvoice()->getSaleInvoiceStatus($uuid);
$envelopeInfo = $client->eInvoice()->getSaleInvoiceEnvelopeInfo($uuid);
$histories    = $client->eInvoice()->getSaleInvoiceHistories($uuid);

// İptal
$client->eInvoice()->cancelSaleInvoice($uuid);
```

### Gelen Faturalar

```php
// Gelen faturaları listele
$incoming = $client->eInvoice()->listPurchaseInvoices($params);

// GIB'den yeni gelen faturaları senkronize et
$client->eInvoice()->syncPurchaseFromGib();

// Okundu işaretle
$client->eInvoice()->markPurchaseInvoiceAsRead($uuid);

// Gelen faturadan iade faturası oluştur
$return = $client->eInvoice()->createReturnFromPurchaseInvoice($uuid);
echo $return['UUID'];
```

### Taslak Faturalar

```php
// Taslak oluştur
$draft = $client->eInvoice()->createDraft([...]);

// Mevcut taslağı gönder
$response = $client->eInvoice()->sendDraft($uuid);

// Düzenle ve gönder
$response = $client->eInvoice()->editAndSendDraft([...]);

// Taslak içeriği
$html = $client->eInvoice()->getDraftHtml($uuid);
$pdf  = $client->eInvoice()->getDraftPdf($uuid);

// Tek taslak sil
$client->eInvoice()->deleteDraft($uuid);
```

### XML / Base64 ile Gönderme

```php
// Ham UBL XML gönder
$result = $client->eInvoice()->sendXml($xmlContent);

// Base64 kodlu XML gönder
$result = $client->eInvoice()->sendBase64(base64_encode($xmlContent));

echo $result['UUID'];
echo $result['InvoiceNumber'];

// UBL XML dosyası yükle
$client->eInvoice()->upload([...]);
```

### Önizleme ve PDF İndirme

```php
// HTML önizleme (kaydetmeden)
$preview = $client->eInvoice()->preview($request);

// PDF binary olarak indir (kaydetmeden)
$pdfBinary = $client->eInvoice()->downloadPdf($request);
file_put_contents('fatura.pdf', $pdfBinary);
```

### İletişim Kanalları

```php
use Nilvera\Requests\SendByEmailRequest;
use Nilvera\Requests\SendBySmsRequest;

// E-posta
$client->eInvoice()->sendSaleInvoiceByEmail(
    new SendByEmailRequest($uuid, ['musteri@example.com', 'muhasebe@example.com'])
);

// SMS
$client->eInvoice()->sendSaleInvoiceBySms(
    new SendBySmsRequest($uuid, ['+905001234567'])
);

// WhatsApp
$client->eInvoice()->sendSaleInvoiceByWhatsapp(
    new SendBySmsRequest($uuid, ['+905001234567'])
);

// Gelen fatura için
$client->eInvoice()->sendPurchaseInvoiceByEmail(new SendByEmailRequest($uuid, ['muhasebe@example.com']));
```

### Seri & Şablon Yönetimi

```php
// Seriler
$series = $client->eInvoice()->listSeries();
$client->eInvoice()->createSeries([...]);

// Şablonlar
$templates = $client->eInvoice()->listTemplates();
$template  = $client->eInvoice()->getTemplate($id);
$client->eInvoice()->updateTemplate([...]);
$preview   = $client->eInvoice()->previewTemplate($uuid);
$client->eInvoice()->deleteTemplate($id);
```

### İstatistikler

```php
$saleStats     = $client->eInvoice()->getSaleStatistics(['StartDate' => '2024-01-01', 'EndDate' => '2024-12-31']);
$purchaseStats = $client->eInvoice()->getPurchaseStatistics(['StartDate' => '2024-01-01', 'EndDate' => '2024-12-31']);
```

---

## E-Arşiv (eArchive)

```php
use Nilvera\Requests\SendArchiveInvoiceRequest;

$request = new SendArchiveInvoiceRequest(
    customerInfo: new ReceiverRequest(
        taxNumber: '9876543210',
        name:      'Bireysel Müşteri',
        address:   'Bağcılar Cad. No:5',
        district:  'Bağcılar',
        city:      'İstanbul',
    ),
    invoiceLines: [
        InvoiceLineRequest::make('Hizmet', 1, UnitType::Piece, 500.00, 20),
    ],
    issueDate: new DateTimeImmutable('2024-06-01T09:00:00'),
);

$response = $client->eArchive()->send($request);

// İptal
$client->eArchive()->cancelInvoice($uuid);

// GIB'e e-arşiv raporu bildir
$client->eArchive()->sendReport();

// Listeleme ve içerik
$invoices = $client->eArchive()->listInvoices($params);
$pdf      = $client->eArchive()->getInvoicePdf($uuid);
$html     = $client->eArchive()->getInvoiceHtml($uuid);
$xml      = $client->eArchive()->getInvoiceXml($uuid);
```

---

## E-İrsaliye (eWaybill)

```php
use Nilvera\Enums\DespatchProfile;
use Nilvera\Enums\DespatchType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\SendWaybillRequest;
use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

$request = new SendWaybillRequest(
    customerAlias: 'urn:mail:defaultpk@nilvera.com', // GIB'de kayıtlı alıcı alias'ı
    customerInfo:  new ReceiverRequest(
        taxNumber: '1234567890',
        name:      'Alıcı Firma A.Ş.',
        address:   'Sanayi Cad. No:10',
        district:  'Gebze',
        city:      'Kocaeli',
        taxOffice: 'Gebze',
    ),
    despatchLines: [
        new DespatchLineRequest(
            name:              'Ürün A',
            deliveredUnitType: UnitType::Piece,
            deliveredQuantity: 100,
            sellerCode:        'STK-001',
            quantityPrice:     50.00,
            lineTotal:         5000.00,
        ),
    ],
    issueDate:      new DateTimeImmutable('2024-06-01T08:00:00'),
    despatchType:   DespatchType::Sevk,
    despatchProfile: DespatchProfile::TemelIrsaliye,
);

$response = $client->eWaybill()->send($request);
echo $response->uuid;

// Listeleme
$waybills = $client->eWaybill()->listSaleWaybills($params);
$html     = $client->eWaybill()->getSaleWaybillHtml($uuid);
$pdf      = $client->eWaybill()->getSaleWaybillPdf($uuid);

// Gelen irsaliye kabul/red
$client->eWaybill()->acceptPurchaseWaybill($uuid);
$client->eWaybill()->rejectPurchaseWaybill($uuid, 'Red gerekçesi');
```

---

## E-SMM (eSelfEmployed)

Serbest meslek makbuzu gönderimi için `SendVoucherRequest` ve `VoucherLineRequest` kullanılır.

```php
use Nilvera\Requests\SendVoucherRequest;
use Nilvera\Requests\ValueObjects\VoucherLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

$request = new SendVoucherRequest(
    customerInfo: new ReceiverRequest(
        taxNumber: '1234567890',
        name:      'İşveren A.Ş.',
        address:   'Meclis-i Mebusan Cad. No:1',
        district:  'Beyoğlu',
        city:      'İstanbul',
        taxOffice: 'Beyoğlu',
    ),
    voucherLines: [
        new VoucherLineRequest(
            name:                 'Danışmanlık Hizmeti',
            grossWage:            10_000.00,
            price:                10_000.00,
            kdvPercent:           20,
            kdvTotal:             2_000.00,
            gvWithholdingPercent: 20,    // GV stopaj yüzdesi
            gvWithholdingTotal:   2_000.00,
        ),
    ],
    issueDate: new DateTimeImmutable('2024-06-01T09:00:00'),
    sendType:  'ELEKTRONIK', // veya 'KAGIT'
);

$response = $client->eSelfEmployed()->send($request);

// Listeleme ve içerik
$vouchers = $client->eSelfEmployed()->listVouchers($params);
$pdf      = $client->eSelfEmployed()->getVoucherPdf($uuid);
```

---

## E-MM (eProducerReceipt)

Müstahsil makbuzu gönderimi için `SendProducerReceiptRequest` ve `ProducerLineRequest` kullanılır.

```php
use Nilvera\Requests\SendProducerReceiptRequest;
use Nilvera\Requests\ValueObjects\ProducerLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

$request = new SendProducerReceiptRequest(
    customerInfo: new ReceiverRequest(
        taxNumber: '12345678901',  // TC kimlik numarası
        name:      'Ahmet Yılmaz',
        address:   'Köy Mah. No:5',
        district:  'Merkez',
        city:      'Antalya',
    ),
    producerLines: [
        new ProducerLineRequest(
            name:                 'Domates',
            quantity:             500,
            unitType:             UnitType::Kilogram,
            price:                8.50,
            gvWithholdingPercent: 4,
            gvWithholdingAmount:  170.00,
        ),
    ],
    issueDate:    new DateTimeImmutable('2024-06-01T00:00:00'),
    deliveryDate: new DateTimeImmutable('2024-06-01T00:00:00'),
);

$response = $client->eProducerReceipt()->send($request);
```

---

## Genel (general)

```php
// Şirket bilgileri
$company = $client->general()->getCompany();
$client->general()->updateCompany([...]);
$modules = $client->general()->getCompanyModules();

// Mükellef sorgulama
$info    = $client->general()->checkTaxpayer('1234567890');    // VKN ile
$results = $client->general()->searchTaxpayers('Acme');         // Ad ile arama
$list    = $client->general()->listTaxpayersByType('GB', 'Invoice'); // Alias tipine göre

// Müşteri yönetimi
$customers = $client->general()->listCustomers();
$client->general()->createCustomer([...]);
$client->general()->updateCustomer([...]);
$client->general()->deleteCustomer($id);

// Stok yönetimi
$stocks = $client->general()->listStocks();
$client->general()->createStock([...]);
$client->general()->deleteStock($id);
```

---

## Hata Yönetimi

```php
use Nilvera\Exception\ApiException;
use Nilvera\Exception\AuthenticationException;
use Nilvera\Exception\ConflictException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Exception\ValidationException;

try {
    $response = $client->eInvoice()->send($request);
} catch (ValidationException $e) {
    // HTTP 422 — iş kuralı veya alan doğrulama hatası
    $errors = $e->getErrors(); // ['AlanAdı' => ['hata mesajı']]
    foreach ($errors as $field => $messages) {
        echo "{$field}: " . implode(', ', $messages) . PHP_EOL;
    }
} catch (AuthenticationException $e) {
    // HTTP 401/403 — geçersiz veya süresi dolmuş API anahtarı
} catch (NotFoundException $e) {
    // HTTP 404 — kayıt bulunamadı
} catch (ConflictException $e) {
    // HTTP 409 — yinelenen istek (aynı UUID tekrar gönderildi)
} catch (ApiException $e) {
    // Diğer tüm API veya bağlantı hataları
    $statusCode = $e->getStatusCode();
    $body       = $e->getResponseBody();
}
```

---

## İstek Sınıfları

| Sınıf | Kullanım |
|---|---|
| `SendInvoiceRequest` | E-Fatura gönderme (POST /einvoice/Send/Model) |
| `SendArchiveInvoiceRequest` | E-Arşiv fatura gönderme (POST /earchive/Send/Model) |
| `SendWaybillRequest` | E-İrsaliye gönderme (POST /edespatch/Send/Model) |
| `SendVoucherRequest` | E-SMM gönderme (POST /evoucher/Send/Model) |
| `SendProducerReceiptRequest` | E-MM gönderme (POST /eproducer/Send/Model) |
| `ListInvoicesRequest` | Listeleme endpoint'leri için sayfalama/filtre |
| `SendByEmailRequest` | E-posta ile iletim |
| `SendBySmsRequest` | SMS / WhatsApp ile iletim |
| `CreateCustomerRequest` | Müşteri oluşturma |

### Value Objects

| Sınıf | Kullanım |
|---|---|
| `ReceiverRequest` | Fatura alıcı bilgisi |
| `InvoiceLineRequest` | E-Fatura / E-Arşiv kalemi (`::make()` KDV'yi otomatik hesaplar) |
| `DespatchLineRequest` | E-İrsaliye kalemi |
| `VoucherLineRequest` | E-SMM kalemi |
| `ProducerLineRequest` | E-MM kalemi |
| `TaxRequest` | Ek vergi bilgisi |
| `PaymentMeansRequest` | Ödeme yöntemi |
| `PaymentTermsRequest` | Ödeme koşulları |
| `ShipmentDetailRequest` | İrsaliye sevkiyat detayı |
| `WaybillPartyRequest` | İrsaliye taraf bilgisi |
| `AdditionalDocumentReferenceRequest` | Ek belge referansı |
| `AttachmentRequest` | Ek dosya |

---

## Enum Referansı

### InvoiceProfile

```php
use Nilvera\Enums\InvoiceProfile;

InvoiceProfile::Basic               // 'TEMELFATURA'
InvoiceProfile::Commercial          // 'TICARIFATURA'
InvoiceProfile::Export              // 'IHRACAT'
InvoiceProfile::TravellerBag        // 'YOLCUBERABERFATURA'
InvoiceProfile::EArchive            // 'EARSIVFATURA'
InvoiceProfile::Public              // 'KAMU'
InvoiceProfile::HKS                 // 'HKS'
InvoiceProfile::Energy              // 'ENERJI'
InvoiceProfile::Medical             // 'ILAC_TIBBICIHAZ'
InvoiceProfile::Special             // 'OZELFATURA'
InvoiceProfile::InvestmentIncentive // 'YATIRIMTESVIK'
InvoiceProfile::IDIS                // 'IDIS'
```

### InvoiceType

```php
use Nilvera\Enums\InvoiceType;

InvoiceType::Sales               // 'SATIS'
InvoiceType::Return              // 'IADE'
InvoiceType::Exemption           // 'ISTISNA'
InvoiceType::Stoppage            // 'TEVKIFAT'
InvoiceType::ExciseDuty          // 'IHRACKAYITLI'
InvoiceType::Cancel              // 'IPTAL'
InvoiceType::SpecialBase         // 'OZELMATRAH'
InvoiceType::SGK                 // 'SGK'
InvoiceType::StoppageReturn      // 'TEVKIFATIADE'
InvoiceType::Commissioner        // 'KOMISYONCU'
InvoiceType::HKSSales            // 'HKSSATIS'
InvoiceType::HKSCommissioner     // 'HKSKOMISYONCU'
InvoiceType::AccommodationTax    // 'KONAKLAMAVERGISI'
InvoiceType::EVCharging          // 'SARJ'
InvoiceType::EVChargingStation   // 'SARJANLIK'
InvoiceType::TechSupport         // 'TEKNOLOJIDESTEK'
InvoiceType::YTBSales            // 'YTBSATIS'
InvoiceType::YTBExemption        // 'YTBISTISNA'
InvoiceType::YTBReturn           // 'YTBIADE'
InvoiceType::YTBStoppage         // 'YTBTEVKIFAT'
InvoiceType::YTBStoppageReturn   // 'YTBTEVKIFATIADE'
```

### UnitType

```php
use Nilvera\Enums\UnitType;

UnitType::Piece       // 'C62' — Adet
UnitType::Kilogram    // 'KGM'
UnitType::Gram        // 'GRM'
UnitType::Liter       // 'LTR'
UnitType::Meter       // 'MTR'
UnitType::SquareMeter // 'MTK'
UnitType::CubicMeter  // 'MTQ'
UnitType::Hour        // 'HUR'
UnitType::Day         // 'DAY'
UnitType::Month       // 'MON'
UnitType::Year        // 'ANN'
UnitType::Box         // 'BX'
UnitType::Dozen       // 'DZN'
UnitType::Ton         // 'TNE'
UnitType::Package     // 'PK'
UnitType::Set         // 'SET'
```

### Diğer Enum'lar

```php
use Nilvera\Enums\DespatchProfile;
use Nilvera\Enums\DespatchType;
use Nilvera\Enums\SendType;
use Nilvera\Enums\SalesPlatform;
use Nilvera\Enums\ProductType;

DespatchProfile::TemelIrsaliye  // 'TEMELIRSALIYE'
DespatchProfile::HKSIrsaliye    // 'HKSIRSALIYE'

DespatchType::Sevk              // 'SEVK'
DespatchType::Matbudan          // 'MATBUDAN'

SendType::Electronic            // 'ELEKTRONIK'
SendType::Paper                 // 'KAGIT'

SalesPlatform::Normal           // 'NORMAL'
SalesPlatform::Internet         // 'INTERNET'

ProductType::Medicine           // 'MEDICINE'
ProductType::MedicalDevice      // 'MEDICALDEVICE'
ProductType::Other              // 'OTHER'
```

---

## Gelişmiş Yapılandırma

```php
use Nilvera\Config;
use Nilvera\NilveraClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('nilvera');
$logger->pushHandler(new StreamHandler('nilvera.log'));

$config = Config::live('your-api-key')
    ->withTimeout(60)           // saniye
    ->withConnectTimeout(10)    // saniye
    ->withLogger($logger)       // PSR-3 uyumlu logger
    ->withRetry(3, 500);        // 3 deneme, 500ms başlangıç gecikmesi (exponential backoff)

$client = NilveraClient::fromConfig($config);
```

| Seçenek | Varsayılan | Açıklama |
|---|---|---|
| `withTimeout(int)` | 30s | İstek timeout süresi |
| `withConnectTimeout(int)` | 10s | Bağlantı timeout süresi |
| `withLogger(LoggerInterface)` | NullLogger | PSR-3 uyumlu logger |
| `withRetry(int, int)` | 2 deneme, 500ms | Otomatik yeniden deneme (exponential backoff) |

---

## Test

```bash
composer install
./vendor/bin/phpunit
```

Entegrasyon testleri için `.env` dosyası oluşturun:

```bash
cp .env.example .env
# NILVERA_TEST_API_KEY değerini .env dosyasına girin
./vendor/bin/phpunit --testsuite integration
```

---

## API Dokümantasyonu

Tam API referansı: [developer.nilvera.com](https://developer.nilvera.com/en)

## Lisans

MIT
