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
| `$client->general()` | General | Company queries, customers, products |
| `$client->eInvoice()` | E-Fatura | Outgoing/incoming e-invoices, drafts |
| `$client->eArchive()` | E-Arşiv | E-archive invoices, reports, series |
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
$result = $client->eInvoice()->send([
    'EInvoice' => [
        'InvoiceInfo' => [
            'InvoiceType'    => 'SATIS',
            'InvoiceProfile' => 'TEMELFATURA',
            'InvoiceDate'    => '2024-01-15',
            'CurrencyCode'   => 'TRY',
        ],
        'CompanyInfo' => [
            'TaxNumber' => '1234567890',
            'Name'      => 'My Company Ltd.',
        ],
        'CustomerInfo' => [
            'TaxNumber' => '9876543210',
            'Name'      => 'Customer Co.',
        ],
        'InvoiceLines' => [
            [
                'Name'        => 'Product A',
                'Quantity'    => 2,
                'UnitCode'    => 'C62',
                'UnitPrice'   => 100.00,
                'VATRate'     => 20,
                'VATAmount'   => 40.00,
                'TotalAmount' => 240.00,
            ],
        ],
        'Notes' => [],
    ],
]);

echo $result['UUID'];          // Invoice UUID
echo $result['InvoiceNumber']; // e.g. "GIB2024000000001"
```

### List Outgoing Invoices

```php
$invoices = $client->eInvoice()->listSaleInvoices([
    'page'      => 1,
    'pageSize'  => 20,
    'startDate' => '2024-01-01',
    'endDate'   => '2024-12-31',
]);
```

### Get Invoice HTML / PDF / XML

```php
$html = $client->eInvoice()->getSaleInvoiceHtml($uuid);
$pdf  = $client->eInvoice()->getSaleInvoicePdf($uuid);   // binary
$xml  = $client->eInvoice()->getSaleInvoiceXml($uuid);
```

### Send Invoice via Email

```php
$client->eInvoice()->sendSaleInvoiceByEmail($uuid, [
    'customer@example.com',
    'accounts@example.com',
]);
```

### E-Archive Invoice

```php
// Create draft
$draft = $client->eArchive()->createDraft([...]);

// Send draft
$result = $client->eArchive()->sendDraft($draft['UUID']);

// Cancel issued invoice
$client->eArchive()->cancelInvoice($uuid);
```

### Check Taxpayer

```php
$info = $client->general()->checkTaxpayer('1234567890');
```

### Create Return Invoice from Incoming Invoice

```php
$return = $client->eInvoice()->createReturnFromPurchaseInvoice($uuid);
echo $return['UUID'];
```

## Error Handling

```php
use Nilvera\Exception\ApiException;
use Nilvera\Exception\AuthenticationException;
use Nilvera\Exception\NotFoundException;
use Nilvera\Exception\ValidationException;
use Nilvera\Exception\ConflictException;

try {
    $result = $client->eInvoice()->send([...]);
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

## Enums

```php
use Nilvera\Enums\Environment;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\InvoiceProfile;
use Nilvera\Enums\UnitType;

InvoiceType::Sales->value;         // 'SATIS'
InvoiceProfile::Commercial->value; // 'TICARIFATURA'
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
