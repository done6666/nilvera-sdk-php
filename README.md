# Nilvera PHP SDK

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

PHP SDK for the [Nilvera](https://www.nilvera.com) e-document API. Supports e-Invoice, e-Archive, e-Waybill, e-SMM, e-MM, e-SKGB, e-Adisyon, e-Saklama and e-Defter services.

## Requirements

- PHP 8.1+
- Guzzle 7.5+

## Installation

```bash
composer require done6666/nilvera-sdk-php
```

## Quick Start

```php
use Nilvera\NilveraClient;

// Production
$client = NilveraClient::live('your-api-key');

// Test / sandbox
$client = NilveraClient::test('your-test-api-key');
```

## Services

| Method | Service | API |
|---|---|---|
| `$client->general()` | General | Company, taxpayer, customer, stock |
| `$client->eInvoice()` | E-Fatura | Outgoing/incoming e-invoices, drafts, series, templates |
| `$client->eArchive()` | E-Arşiv | E-archive invoices, reports, series, templates |
| `$client->eWaybill()` | E-İrsaliye | E-waybills, acceptance/rejection |
| `$client->eSelfEmployed()` | E-SMM | Self-employed professional receipts |
| `$client->eProducerReceipt()` | E-MM | Producer receipts |
| `$client->eInsurance()` | E-SKGB | Insurance commission expense docs |
| `$client->eReceipt()` | E-Adisyon | Electronic bills/receipts |
| `$client->eStorage()` | E-Saklama | Document storage |
| `$client->eLedger()` | E-Defter | Electronic ledger |
| `$client->report()` | Rapor | Reports |

## Usage Examples

### Send an e-Invoice

```php
use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;

$request = new SendInvoiceRequest(
    customerInfo: new ReceiverRequest(
        taxNumber: '1234567890',
        name:      'Customer Co.',
        address:   'Atatürk Cad. No:1',
        district:  'Kadıköy',
        city:      'İstanbul',
        taxOffice: 'Kadıköy',
    ),
    invoiceLines: [
        InvoiceLineRequest::make(
            name:       'Product A',
            quantity:   2,
            unitType:   UnitType::Piece,
            price:      100.00,
            kdvPercent: 20,
        ),
    ],
    issueDate:      new DateTimeImmutable('2024-01-15T10:00:00'),
    invoiceProfile: InvoiceProfile::Basic,
    invoiceType:    InvoiceType::Sales,
    // customerAlias: 'urn:mail:muhasebe@customer.com', // for registered e-invoice recipients
);

$response = $client->eInvoice()->send($request);

echo $response->uuid;          // Invoice UUID
echo $response->invoiceNumber; // e.g. "GIB2024000000001"
```

### InvoiceLineRequest — factory method

`InvoiceLineRequest::make()` automatically calculates `KDVTotal` from price, quantity and KDV rate. Use `$allowancePercent` for percentage-based discounts:

```php
// With discount percentage
$line = InvoiceLineRequest::make(
    name:             'Product B',
    quantity:         10,
    unitType:         UnitType::Piece,
    price:            500.00,
    kdvPercent:       10,
    allowancePercent: 5,   // 5% discount, AllowanceTotal calculated automatically
);

// With fixed discount amount
$line = InvoiceLineRequest::make(
    name:          'Product C',
    quantity:      1,
    unitType:      UnitType::Piece,
    price:         1000.00,
    kdvPercent:    20,
    allowanceTotal: 50.00,
);
```

### List Outgoing Invoices

```php
use Nilvera\Requests\ListInvoicesRequest;

$params = new ListInvoicesRequest(
    startDate: new DateTimeImmutable('2024-01-01'),
    endDate:   new DateTimeImmutable('2024-12-31'),
    page:      1,
    pageSize:  20,
);

$invoices = $client->eInvoice()->listSaleInvoices($params);
```

### Get Invoice HTML / PDF / XML

```php
$html = $client->eInvoice()->getSaleInvoiceHtml($uuid);
$pdf  = $client->eInvoice()->getSaleInvoicePdf($uuid);  // binary
$xml  = $client->eInvoice()->getSaleInvoiceXml($uuid);
```

### Send Invoice via Email / SMS / WhatsApp

```php
use Nilvera\Requests\SendByEmailRequest;
use Nilvera\Requests\SendBySmsRequest;

// Email
$client->eInvoice()->sendSaleInvoiceByEmail(
    new SendByEmailRequest($uuid, ['customer@example.com', 'accounts@example.com'])
);

// SMS
$client->eInvoice()->sendSaleInvoiceBySms(
    new SendBySmsRequest($uuid, ['+905001234567'])
);

// WhatsApp
$client->eInvoice()->sendSaleInvoiceByWhatsapp(
    new SendBySmsRequest($uuid, ['+905001234567'])
);
```

### Send e-Invoice via XML or Base64

```php
// Send raw UBL XML
$result = $client->eInvoice()->sendXml($xmlContent);

// Send base64-encoded XML
$result = $client->eInvoice()->sendBase64(base64_encode($xmlContent));

echo $result['UUID'];
echo $result['InvoiceNumber'];
```

### Draft Invoices

```php
// Create a draft
$draft = $client->eInvoice()->createDraft([...]);

// Send an existing draft
$response = $client->eInvoice()->sendDraft($uuid);

// Delete a draft
$client->eInvoice()->deleteDraft($uuid);
```

### Create Return Invoice from Incoming Invoice

```php
$return = $client->eInvoice()->createReturnFromPurchaseInvoice($uuid);
echo $return['UUID'];
```

### E-Archive Invoice

```php
use Nilvera\Requests\SendArchiveInvoiceRequest;

$request = new SendArchiveInvoiceRequest(
    customerInfo:  new ReceiverRequest(
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

// Cancel an issued e-archive invoice
$client->eArchive()->cancelInvoice($uuid);

// Submit e-archive report to GIB
$client->eArchive()->sendReport();
```

### Check Taxpayer

```php
// Check by tax number
$info = $client->general()->checkTaxpayer('1234567890');

// Search by company name
$results = $client->general()->searchTaxpayers('Acme');

// Check by alias type (e-invoice vs e-despatch)
$list = $client->general()->listTaxpayersByType('GB', 'Invoice');
```

### Customer & Stock Management

```php
// Customers
$customers = $client->general()->listCustomers();
$client->general()->createCustomer([...]);
$client->general()->updateCustomer([...]);
$client->general()->deleteCustomer($id);

// Stocks
$stocks = $client->general()->listStocks();
$client->general()->createStock([...]);
$client->general()->deleteStock($id);
```

## Error Handling

```php
use Nilvera\Exception\ApiException;
use Nilvera\Exception\AuthenticationException;
use Nilvera\Exception\ConflictException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Exception\ValidationException;

try {
    $response = $client->eInvoice()->send($request);
} catch (ValidationException $e) {
    // HTTP 422 — business rule or field validation failure
    $errors = $e->getErrors(); // ['FieldName' => ['error message']]
} catch (AuthenticationException $e) {
    // HTTP 401/403 — invalid or expired API key
} catch (NotFoundException $e) {
    // HTTP 404 — record not found
} catch (ConflictException $e) {
    // HTTP 409 — duplicate request
} catch (ApiException $e) {
    // Any other API or connection error
    $statusCode = $e->getStatusCode();
    $body       = $e->getResponseBody();
}
```

## Request Classes

| Class | Used for |
|---|---|
| `SendInvoiceRequest` | POST /einvoice/Send/Model |
| `SendArchiveInvoiceRequest` | POST /earchive/Send/Model |
| `ListInvoicesRequest` | GET listing endpoints (e-invoice, e-archive, etc.) |
| `SendByEmailRequest` | Email delivery endpoints |
| `SendBySmsRequest` | SMS / WhatsApp delivery endpoints |
| `ReceiverRequest` | Customer info in invoice requests |
| `InvoiceLineRequest` | Invoice line items (use `::make()` for auto KDV calc) |

## Enums

```php
use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;

InvoiceProfile::Basic->value;      // 'TEMELFATURA'
InvoiceProfile::Commercial->value; // 'TICARIFATURA'
InvoiceProfile::Export->value;     // 'IHRACAT'
InvoiceProfile::EArchive->value;   // 'EARSIVFATURA'

InvoiceType::Sales->value;         // 'SATIS'
InvoiceType::Return->value;        // 'IADE'

UnitType::Piece->value;            // 'C62'
```

## Custom Configuration

```php
use Nilvera\Config;
use Nilvera\NilveraClient;

$config = Config::live('your-api-key')
    ->withTimeout(60)
    ->withConnectTimeout(5);

$client = NilveraClient::fromConfig($config);
```

## Testing

```bash
composer install
./vendor/bin/phpunit
```

## API Documentation

Full API reference: [developer.nilvera.com](https://developer.nilvera.com/en)

## License

MIT
