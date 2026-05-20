<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Nilvera\Config;
use Nilvera\Enums\DespatchProfile;
use Nilvera\Enums\DespatchType;
use Nilvera\Enums\UnitType;
use Nilvera\Http\HttpClient;
use Nilvera\Requests\CreateSeriesRequest;
use Nilvera\Requests\ListSeriesRequest;
use Nilvera\Requests\SendWaybillRequest;
use Nilvera\Requests\UpdateSeriesRequest;
use Nilvera\Requests\ValueObjects\DespatchLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Services\EWaybillService;
use PHPUnit\Framework\TestCase;

class EWaybillServiceTest extends TestCase
{
    private EWaybillService $service;
    private ClientInterface $guzzle;

    protected function setUp(): void
    {
        $this->guzzle  = $this->createMock(ClientInterface::class);
        $httpClient    = new HttpClient($this->guzzle, Config::test('test-key'));
        $this->service = new EWaybillService($httpClient);
    }

    private function makeRequest(): SendWaybillRequest
    {
        return new SendWaybillRequest(
            customerAlias:        'urn:mail:defaultpk@nilvera.com',
            customerInfo:         new ReceiverRequest(
                taxNumber: '1234567890',
                name:      'Test Firma',
                address:   'Test Mah. No:1',
                district:  'Kadıköy',
                city:      'İstanbul',
            ),
            despatchLines:        [
                new DespatchLineRequest(
                    name:              'Test Ürün',
                    deliveredUnitType: UnitType::Piece,
                    deliveredQuantity: 1.0,
                ),
            ],
            issueDate:            new \DateTimeImmutable('2026-05-19'),
            despatchSerieOrNumber: 'EIT',
            despatchType:         DespatchType::Sevk,
            despatchProfile:      DespatchProfile::TemelIrsaliye,
        );
    }

    public function test_send_posts_to_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/edespatch/Send/Model', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"UUID":"abc-123","DespatchNumber":"EIT2026000000001"}'));

        $response = $this->service->send($this->makeRequest());

        $this->assertSame('abc-123', $response->uuid);
        $this->assertSame('EIT2026000000001', $response->despatchNumber);
    }

    public function test_preview_send_posts_to_correct_endpoint(): void
    {
        $expectedHtml = '<html><body>İrsaliye Önizleme</body></html>';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/edespatch/Send/Model/Preview', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], $expectedHtml));

        $result = $this->service->previewSend($this->makeRequest());

        $this->assertSame($expectedHtml, $result);
    }

    public function test_preview_send_returns_string(): void
    {
        $this->guzzle->method('request')
            ->willReturn(new GuzzleResponse(200, [], '<html>preview</html>'));

        $result = $this->service->previewSend($this->makeRequest());

        $this->assertIsString($result);
    }

    public function test_list_sale_waybills_hits_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/edespatch/Sale', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"Content":[],"TotalCount":0}'));

        $result = $this->service->listSaleWaybills();

        $this->assertIsArray($result);
    }

    public function test_list_purchase_waybills_hits_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/edespatch/Purchase', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"Content":[],"TotalCount":0}'));

        $result = $this->service->listPurchaseWaybills();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // Seri (Series) testleri
    // -------------------------------------------------------------------------

    public function test_list_series_calls_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/edespatch/Series', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"Content":[],"Page":1,"TotalCount":0}'));

        $result = $this->service->listSeries();

        $this->assertSame([], $result['Content']);
    }

    public function test_list_series_with_query_params(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/edespatch/Series', $this->callback(
                fn ($opts) => ($opts['query']['IsActive'] ?? null) === 'true'
                    && ($opts['query']['PageSize'] ?? null) === 5
            ))
            ->willReturn(new GuzzleResponse(200, [], '{"Content":[],"Page":1,"TotalCount":0}'));

        $this->service->listSeries(new ListSeriesRequest(isActive: true, pageSize: 5));
    }

    public function test_get_series_uses_id_in_path(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/edespatch/Series/7', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"ID":7,"Name":"EIT","IsActive":true,"IsDefault":false}'));

        $result = $this->service->getSeries(7);

        $this->assertSame(7, $result['ID']);
        $this->assertSame('EIT', $result['Name']);
    }

    public function test_create_series_sends_correct_payload(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/edespatch/Series', $this->callback(
                fn ($opts) => ($opts['json']['Name'] ?? null) === 'EIT'
                    && ($opts['json']['IsActive'] ?? null) === true
                    && ($opts['json']['IsDefault'] ?? null) === false
            ))
            ->willReturn(new GuzzleResponse(200, [], '{"ID":7,"Name":"EIT","IsActive":true,"IsDefault":false}'));

        $result = $this->service->createSeries(
            new CreateSeriesRequest(name: 'EIT', isActive: true, isDefault: false)
        );

        $this->assertSame(7, $result['ID']);
        $this->assertSame('EIT', $result['Name']);
    }

    public function test_update_series_returns_true_on_success(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('PUT', 'https://apitest.nilvera.com/edespatch/Series', $this->callback(
                fn ($opts) => ($opts['json']['ID'] ?? null) === 7
                    && ($opts['json']['IsActive'] ?? null) === false
                    && ($opts['json']['IsDefault'] ?? null) === false
            ))
            ->willReturn(new GuzzleResponse(200, [], 'true'));

        $result = $this->service->updateSeries(
            new UpdateSeriesRequest(id: 7, isDefault: false, isActive: false)
        );

        $this->assertTrue($result);
    }

    public function test_update_series_returns_false_on_failure(): void
    {
        $this->guzzle->method('request')
            ->willReturn(new GuzzleResponse(200, [], 'false'));

        $result = $this->service->updateSeries(
            new UpdateSeriesRequest(id: 99, isDefault: false, isActive: true)
        );

        $this->assertFalse($result);
    }
}
