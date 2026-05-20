<?php

declare(strict_types=1);

namespace Nilvera\Tests\Integration\Services;

use Nilvera\Enums\DespatchProfile;
use Nilvera\Enums\DespatchType;
use Nilvera\Enums\UnitType;
use Nilvera\Requests\CreateSeriesRequest;
use Nilvera\Requests\ListSeriesRequest;
use Nilvera\Requests\SendWaybillRequest;
use Nilvera\Requests\UpdateSeriesRequest;
use Nilvera\Requests\ValueObjects\AddressInfoRequest;
use Nilvera\Requests\ValueObjects\AdditionalDocumentReferenceRequest;
use Nilvera\Requests\ValueObjects\CarrierInfoRequest;
use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\DriverPersonRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Requests\ValueObjects\ShipmentDetailRequest;
use Nilvera\Requests\ValueObjects\ShipmentInfoRequest;
use Nilvera\Requests\ValueObjects\WaybillDeliveryRequest;
use Nilvera\Requests\ValueObjects\WaybillOrderReferenceRequest;
use Nilvera\Requests\ValueObjects\WaybillPartyRequest;
use Nilvera\Responses\SendWaybillResponse;
use Nilvera\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class EWaybillServiceTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Listele
    // -------------------------------------------------------------------------

    public function test_list_sale_waybills_returns_array(): void
    {
        $result = $this->client->eWaybill()->listSaleWaybills(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_list_purchase_waybills_returns_array(): void
    {
        $result = $this->client->eWaybill()->listPurchaseWaybills(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    public function test_list_series_returns_paginated_result(): void
    {
        $result = $this->client->eWaybill()->listSeries();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Content', $result);
        $this->assertArrayHasKey('Page', $result);
        $this->assertArrayHasKey('TotalCount', $result);
    }

    public function test_list_series_with_active_filter(): void
    {
        $result = $this->client->eWaybill()->listSeries(
            new ListSeriesRequest(isActive: true, pageSize: 5)
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Content', $result);

        foreach ($result['Content'] ?? [] as $serie) {
            $this->assertTrue($serie['IsActive'], 'IsActive filtresi çalışmıyor.');
        }
    }

    public function test_create_and_get_series(): void
    {
        $uniqueName = strtoupper(substr(md5((string) microtime(true)), 0, 3));

        $created = $this->client->eWaybill()->createSeries(
            new CreateSeriesRequest(name: $uniqueName, isActive: true, isDefault: false)
        );

        $this->assertArrayHasKey('ID', $created);
        $this->assertSame($uniqueName, $created['Name']);
        $this->assertTrue($created['IsActive']);
        $this->assertFalse($created['IsDefault']);

        $detail = $this->client->eWaybill()->getSeries($created['ID']);

        $this->assertSame($created['ID'], $detail['ID']);
        $this->assertSame($uniqueName, $detail['Name']);
        $this->assertArrayHasKey('Details', $detail);
    }

    public function test_update_series_active_status(): void
    {
        $seriesList = $this->client->eWaybill()->listSeries();
        $series     = $seriesList['Content'] ?? [];

        if (empty($series)) {
            $this->markTestSkipped('Test hesabında e-İrsaliye serisi bulunamadı.');
        }

        $target         = $series[0];
        $newActiveState = !$target['IsActive'];

        $result = $this->client->eWaybill()->updateSeries(
            new UpdateSeriesRequest(
                id:        $target['ID'],
                isDefault: $target['IsDefault'],
                isActive:  $newActiveState,
            )
        );

        $this->assertTrue($result);

        $detail = $this->client->eWaybill()->getSeries($target['ID']);
        $this->assertSame($newActiveState, $detail['IsActive']);

        // Orijinal duruma geri al
        $this->client->eWaybill()->updateSeries(
            new UpdateSeriesRequest(
                id:        $target['ID'],
                isDefault: $target['IsDefault'],
                isActive:  $target['IsActive'],
            )
        );
    }

    public function test_get_series_detail_has_expected_keys(): void
    {
        $seriesList = $this->client->eWaybill()->listSeries();

        if (empty($seriesList['Content'])) {
            $this->markTestSkipped('Test hesabında e-İrsaliye serisi bulunamadı.');
        }

        $id     = $seriesList['Content'][0]['ID'];
        $detail = $this->client->eWaybill()->getSeries($id);

        $this->assertArrayHasKey('ID', $detail);
        $this->assertArrayHasKey('Name', $detail);
        $this->assertArrayHasKey('IsActive', $detail);
        $this->assertArrayHasKey('IsDefault', $detail);
        $this->assertArrayHasKey('Details', $detail);
    }

    public function test_list_tags_returns_array(): void
    {
        $result = $this->client->eWaybill()->listTags();

        $this->assertIsArray($result);
    }

    public function test_list_answer_series_returns_array(): void
    {
        $result = $this->client->eWaybill()->listAnswerSeries();

        $this->assertIsArray($result);
    }

    public function test_list_templates_returns_array(): void
    {
        $result = $this->client->eWaybill()->listTemplates();

        $this->assertIsArray($result);
    }

    public function test_list_answer_templates_returns_array(): void
    {
        $result = $this->client->eWaybill()->listAnswerTemplates();

        $this->assertIsArray($result);
    }

    public function test_get_last_statistics_returns_array(): void
    {
        try {
            $result = $this->client->eWaybill()->getLastStatistics();
            $this->assertIsArray($result);
        } catch (\Nilvera\Exception\NotFoundException) {
            $this->markTestSkipped('getLastStatistics ucu bu test hesabında mevcut değil (404).');
        }
    }

    // -------------------------------------------------------------------------
    // Önizleme
    // -------------------------------------------------------------------------

    public function test_preview_send_returns_html_string(): void
    {
        $series = $this->client->eWaybill()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında aktif e-irsaliye serisi bulunamadı.');
        }

        $seriesName = $series['Content'][0]['Name'];

        $request = new SendWaybillRequest(
            customerAlias:        'urn:mail:defaultpk@nilvera.com',
            customerInfo:         new ReceiverRequest(
                taxNumber:  '6310540565',
                name:       'Nilvera e-İrsaliye Test Alıcısı',
                address:    'Test Mah. No:1',
                district:   'Kadıköy',
                city:       'İstanbul',
                country:    'Türkiye',
                taxOffice:  'Kadıköy',
                postalCode: '34710',
            ),
            despatchLines:        [
                new DespatchLineRequest(
                    name:              'SDK Test Ürünü',
                    deliveredUnitType: UnitType::Piece,
                    deliveredQuantity: 1.0,
                    deliveredUnitName: 'Adet',
                    quantityPrice:     100.0,
                    lineTotal:         100.0,
                ),
            ],
            issueDate:             new \DateTimeImmutable(),
            despatchSerieOrNumber: $seriesName,
            despatchType:          DespatchType::Sevk,
            despatchProfile:       DespatchProfile::TemelIrsaliye,
            actualDespatchDateTime: new \DateTimeImmutable(),
            shipmentDetail:        new ShipmentDetailRequest(
                shipmentInfo: new ShipmentInfoRequest(
                    licensePlateId: '34 SDK 001',
                    driverPersons:  [
                        new DriverPersonRequest(
                            firstName: 'Test',
                            lastName:  'Sürücü',
                            taxNumber: '11111111111',
                        ),
                    ],
                ),
                delivery: new WaybillDeliveryRequest(
                    addressInfo: new AddressInfoRequest(
                        address:    'Test Mah. No:1',
                        district:   'Kadıköy',
                        city:       'İstanbul',
                        country:    'Türkiye',
                        postalCode: '34710',
                    ),
                ),
            ),
        );

        $html = $this->client->eWaybill()->previewSend($request);

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsStringIgnoringCase('html', $html);
    }

    // -------------------------------------------------------------------------
    // Temel irsaliye gönder (PostalCode + ShipmentDetail zorunlu)
    // -------------------------------------------------------------------------

    public function test_send_minimal_waybill_returns_uuid_and_number(): void
    {
        $series = $this->client->eWaybill()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında aktif e-irsaliye serisi bulunamadı.');
        }

        $seriesName = $series['Content'][0]['Name'];

        $request = new SendWaybillRequest(
            customerAlias:         'urn:mail:defaultpk@nilvera.com',
            customerInfo:          new ReceiverRequest(
                taxNumber:  '6310540565',
                name:       'Nilvera e-İrsaliye Test Alıcısı',
                address:    'Test Mah. No:1',
                district:   'Kadıköy',
                city:       'İstanbul',
                country:    'Türkiye',
                taxOffice:  'Kadıköy',
                postalCode: '34710',
            ),
            despatchLines:         [
                new DespatchLineRequest(
                    name:              'SDK Integration Test Ürünü',
                    deliveredUnitType: UnitType::Piece,
                    deliveredQuantity: 2.0,
                    deliveredUnitName: 'Adet',
                    quantityPrice:     100.0,
                    lineTotal:         200.0,
                ),
            ],
            issueDate:              new \DateTimeImmutable(),
            despatchType:           DespatchType::Sevk,
            despatchProfile:        DespatchProfile::TemelIrsaliye,
            despatchSerieOrNumber:  $seriesName,
            actualDespatchDateTime: new \DateTimeImmutable(),
            shipmentDetail:         new ShipmentDetailRequest(
                shipmentInfo: new ShipmentInfoRequest(
                    licensePlateId: '34 SDK 001',
                    driverPersons:  [
                        new DriverPersonRequest(
                            firstName: 'Test',
                            lastName:  'Sürücü',
                            taxNumber: '11111111111',
                        ),
                    ],
                ),
                delivery:     new WaybillDeliveryRequest(
                    addressInfo: new AddressInfoRequest(
                        address:    'Test Mah. No:1',
                        district:   'Kadıköy',
                        city:       'İstanbul',
                        country:    'Türkiye',
                        postalCode: '34710',
                    ),
                ),
            ),
            notes:                  ['SDK integration testi'],
        );

        $response = $this->client->eWaybill()->send($request);

        $this->assertInstanceOf(SendWaybillResponse::class, $response);
        $this->assertNotEmpty($response->uuid);
        $this->assertNotEmpty($response->despatchNumber);
    }

    // -------------------------------------------------------------------------
    // Tam irsaliye gönder (tüm opsiyonel alanlar)
    // -------------------------------------------------------------------------

    public function test_send_full_waybill_with_all_optional_fields(): void
    {
        $series = $this->client->eWaybill()->listSeries();

        if (empty($series['Content'])) {
            $this->markTestSkipped('Test hesabında aktif e-irsaliye serisi bulunamadı.');
        }

        // Aktif seri seç
        $seriesName = $series['Content'][0]['Name'];

        $request = new SendWaybillRequest(
            customerAlias:  'urn:mail:defaultpk@nilvera.com',
            customerInfo:   new ReceiverRequest(
                taxNumber:  '6310540565',
                name:       'Nilvera e-İrsaliye Test Alıcısı',
                address:    'Papatya Cad. Yasemin Sok. No:21',
                district:   'Melikgazi',
                city:       'Kayseri',
                country:    'Türkiye',
                taxOffice:  'Melikgazi',
                postalCode: '38038',
            ),
            uuid:           $this->generateUuid(),
            despatchLines:  [
                new DespatchLineRequest(
                    name:                         'Laptop',
                    deliveredUnitType:            UnitType::Piece,
                    deliveredQuantity:            1.0,
                    sellerCode:                   'STK-001',
                    buyerCode:                    'ALI-001',
                    deliveredUnitName:            'Adet',
                    quantityPrice:                25000.0,
                    lineTotal:                    25000.0,
                    manufacturerCode:             'MNF-001',
                    brandName:                    'TestBrand',
                    modelName:                    'TestModel X1',
                    additionalItemIdentification: 'IDENT-12345',
                ),
                new DespatchLineRequest(
                    name:              'Klavye',
                    deliveredUnitType: UnitType::Piece,
                    deliveredQuantity: 2.0,
                    deliveredUnitName: 'Adet',
                    quantityPrice:     500.0,
                    lineTotal:         1000.0,
                ),
            ],
            issueDate:             new \DateTimeImmutable(),
            despatchType:          DespatchType::Sevk,
            despatchProfile:       DespatchProfile::TemelIrsaliye,
            despatchSerieOrNumber: $seriesName,
            actualDespatchDateTime: new \DateTimeImmutable(),
            payableAmount:  26000.0,
            currencyCode:   'TRY',
            shipmentDetail: new ShipmentDetailRequest(
                shipmentInfo: new ShipmentInfoRequest(
                    licensePlateId: '34 ABC 123',
                    driverPersons:  [
                        new DriverPersonRequest(
                            firstName: 'Ahmet',
                            lastName:  'Yılmaz',
                            taxNumber: '11111111111',
                        ),
                    ],
                ),
                delivery: new WaybillDeliveryRequest(
                    addressInfo: new AddressInfoRequest(
                        address:    'Papatya Cad. Yasemin Sok. No:21',
                        district:   'Melikgazi',
                        city:       'Kayseri',
                        country:    'Türkiye',
                        postalCode: '38038',
                    ),
                    carrierInfo: new CarrierInfoRequest(
                        taxNumber:  '1288331521',
                        name:       'Test Kargo Lojistik A.Ş.',
                        address:    'Lojistik Merkezi No:1',
                        district:   'Pendik',
                        city:       'İstanbul',
                        country:    'Türkiye',
                        postalCode: '34890',
                    ),
                ),
                transportEquipment: ['CONT-001', 'CONT-002'],
            ),
            orderReference: new WaybillOrderReferenceRequest(
                id:        'SIP-2026-001',
                issueDate: new \DateTimeImmutable('-1 day'),
                documentReference: new AdditionalDocumentReferenceRequest(
                    id:               'DOC-001',
                    issueDate:        new \DateTimeImmutable('-1 day'),
                    documentType:     'PurchaseOrder',
                    documentTypeCode: '130',
                ),
            ),
            additionalDocumentReferences: [
                new AdditionalDocumentReferenceRequest(
                    id:                  'EK-001',
                    issueDate:           new \DateTimeImmutable(),
                    documentType:        'AdditionalDocument',
                    documentDescription: 'SDK test ek belgesi',
                ),
            ],
            notes: ['Kırılgan ürün, dikkatli taşıyınız.', 'SDK integration testi — tam alan'],
        );

        $response = $this->client->eWaybill()->send($request);

        $this->assertInstanceOf(SendWaybillResponse::class, $response);
        $this->assertNotEmpty($response->uuid);
        $this->assertNotEmpty($response->despatchNumber);
    }

    // -------------------------------------------------------------------------
    // Gönderilen irsaliyede işlemler
    // -------------------------------------------------------------------------

    public function test_get_sale_waybill_model_returns_array(): void
    {
        $list = $this->client->eWaybill()->listSaleWaybills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut satış irsaliyesi bulunamadı.');
        }

        $uuid = $list['Content'][0]['UUID'];

        try {
            $result = $this->client->eWaybill()->getSaleWaybillModel($uuid);
            $this->assertIsArray($result);
        } catch (\Nilvera\Exception\NotFoundException) {
            $this->markTestSkipped("getSaleWaybillModel ucu bu kayıt için mevcut değil ({$uuid}).");
        }
    }

    public function test_get_sale_waybill_status_returns_array(): void
    {
        $list = $this->client->eWaybill()->listSaleWaybills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut satış irsaliyesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eWaybill()->getSaleWaybillStatus($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_sale_waybill_histories_returns_array(): void
    {
        $list = $this->client->eWaybill()->listSaleWaybills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut satış irsaliyesi bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eWaybill()->getSaleWaybillHistories($uuid);

        $this->assertIsArray($result);
    }

    public function test_get_sale_waybill_pdf_returns_non_empty_string(): void
    {
        $list = $this->client->eWaybill()->listSaleWaybills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut satış irsaliyesi bulunamadı.');
        }

        $uuid = $list['Content'][0]['UUID'];
        $pdf  = $this->client->eWaybill()->getSaleWaybillPdf($uuid);

        $this->assertNotEmpty($pdf);
    }

    public function test_get_sale_waybill_xml_returns_non_empty_string(): void
    {
        $list = $this->client->eWaybill()->listSaleWaybills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında mevcut satış irsaliyesi bulunamadı.');
        }

        $uuid = $list['Content'][0]['UUID'];
        $xml  = $this->client->eWaybill()->getSaleWaybillXml($uuid);

        $this->assertNotEmpty($xml);
        $this->assertStringContainsString('DespatchAdvice', $xml);
    }

    // -------------------------------------------------------------------------
    // Gelen irsaliye işlemleri
    // -------------------------------------------------------------------------

    public function test_get_purchase_waybill_model_returns_array(): void
    {
        $list = $this->client->eWaybill()->listPurchaseWaybills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında gelen irsaliye bulunamadı.');
        }

        $uuid = $list['Content'][0]['UUID'];

        try {
            $result = $this->client->eWaybill()->getPurchaseWaybillModel($uuid);
            $this->assertIsArray($result);
        } catch (\Nilvera\Exception\NotFoundException) {
            $this->markTestSkipped("getPurchaseWaybillModel ucu bu kayıt için mevcut değil ({$uuid}).");
        }
    }

    public function test_get_purchase_waybill_status_returns_array(): void
    {
        $list = $this->client->eWaybill()->listPurchaseWaybills(['page' => 1, 'pageSize' => 1]);

        if (empty($list['Content'])) {
            $this->markTestSkipped('Test hesabında gelen irsaliye bulunamadı.');
        }

        $uuid   = $list['Content'][0]['UUID'];
        $result = $this->client->eWaybill()->getPurchaseWaybillStatus($uuid);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Taslak irsaliye işlemleri
    // -------------------------------------------------------------------------

    public function test_list_drafts_returns_array(): void
    {
        $result = $this->client->eWaybill()->listDrafts(['page' => 1, 'pageSize' => 5]);

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function generateUuid(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
