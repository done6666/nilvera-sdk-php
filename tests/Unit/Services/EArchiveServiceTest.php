<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Nilvera\Config;
use Nilvera\Enums\InvoiceType;
use Nilvera\Enums\UnitType;
use Nilvera\Http\HttpClient;
use Nilvera\Requests\CreateSeriesRequest;
use Nilvera\Requests\ListSeriesRequest;
use Nilvera\Requests\SendArchiveInvoiceRequest;
use Nilvera\Requests\UpdateSeriesRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Services\EArchiveService;
use PHPUnit\Framework\TestCase;

class EArchiveServiceTest extends TestCase
{
    private EArchiveService $service;
    private ClientInterface $guzzle;

    protected function setUp(): void
    {
        $this->guzzle  = $this->createMock(ClientInterface::class);
        $httpClient    = new HttpClient($this->guzzle, Config::test('test-key'));
        $this->service = new EArchiveService($httpClient);
    }

    private function makeRequest(): SendArchiveInvoiceRequest
    {
        return new SendArchiveInvoiceRequest(
            customerInfo:         new ReceiverRequest(
                taxNumber: '10000000146',
                name:      'Test Musteri',
                address:   'Test Mah. No:1',
                district:  'Kadikoy',
                city:      'Istanbul',
            ),
            invoiceLines:         [
                InvoiceLineRequest::make('Test Urun', 1, UnitType::Piece, 100.0, 20),
            ],
            issueDate:            new \DateTimeImmutable('2026-05-19'),
            invoiceSerieOrNumber: 'ABC',
            invoiceType:          InvoiceType::Sales,
        );
    }

    public function test_send_posts_to_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/earchive/Send/Model', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"UUID":"abc-123","InvoiceNumber":"ABC2026000000001"}'));

        $response = $this->service->send($this->makeRequest());

        $this->assertSame('abc-123', $response->uuid);
        $this->assertSame('ABC2026000000001', $response->invoiceNumber);
    }

    public function test_preview_send_posts_to_correct_endpoint(): void
    {
        $expectedHtml = '<html><body>Fatura Önizleme</body></html>';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/earchive/Send/Model/Preview', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], json_encode($expectedHtml)));

        $result = $this->service->previewSend($this->makeRequest());

        $this->assertSame($expectedHtml, $result);
    }

    public function test_preview_send_decodes_json_wrapped_html(): void
    {
        $html = '<!DOCTYPE html><html><body>Önizleme</body></html>';

        $this->guzzle->method('request')
            ->willReturn(new GuzzleResponse(200, [], json_encode($html)));

        $result = $this->service->previewSend($this->makeRequest());

        $this->assertStringStartsWith('<!DOCTYPE html>', $result);
        $this->assertStringNotContainsString('\\"', $result);
    }

    public function test_preview_send_returns_string(): void
    {
        $this->guzzle->method('request')
            ->willReturn(new GuzzleResponse(200, [], json_encode('<html>preview</html>')));

        $result = $this->service->previewSend($this->makeRequest());

        $this->assertIsString($result);
    }

    // -------------------------------------------------------------------------
    // Series
    // -------------------------------------------------------------------------

    public function test_list_series_gets_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/earchive/Series', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"Page":1,"PageSize":10,"TotalCount":2,"Content":[]}'));

        $result = $this->service->listSeries();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Content', $result);
    }

    public function test_list_series_sends_query_params(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/earchive/Series', $this->callback(function (array $options): bool {
                $query = $options['query'] ?? [];
                return ($query['IsActive'] ?? null) === 'true'
                    && ($query['Page'] ?? null) === 1;
            }))
            ->willReturn(new GuzzleResponse(200, [], '{"Content":[]}'));

        $this->service->listSeries(new ListSeriesRequest(page: 1, isActive: true));
    }

    public function test_get_series_gets_correct_endpoint(): void
    {
        $payload = '{"ID":5,"Name":"ABC","IsDefault":false,"IsActive":true,"CreatedDate":"2026-01-01T00:00:00","Details":[]}';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/earchive/Series/5', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], $payload));

        $result = $this->service->getSeries(5);

        $this->assertSame(5, $result['ID']);
        $this->assertSame('ABC', $result['Name']);
    }

    public function test_create_series_posts_to_correct_endpoint(): void
    {
        $payload = '{"ID":10,"Name":"YEN","IsDefault":false,"IsActive":true,"CreatedDate":"2026-05-20T00:00:00","Details":[]}';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/earchive/Series', $this->callback(function (array $options): bool {
                $body = $options['json'] ?? [];
                return ($body['Name'] ?? null) === 'YEN'
                    && ($body['IsActive'] ?? null) === true
                    && ($body['IsDefault'] ?? null) === false;
            }))
            ->willReturn(new GuzzleResponse(200, [], $payload));

        $request = new CreateSeriesRequest(name: 'YEN', isActive: true, isDefault: false);
        $result  = $this->service->createSeries($request);

        $this->assertSame(10, $result['ID']);
        $this->assertSame('YEN', $result['Name']);
    }

    public function test_create_series_rejects_name_not_three_chars(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CreateSeriesRequest(name: 'TOOLONG');
    }

    public function test_update_series_puts_to_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('PUT', 'https://apitest.nilvera.com/earchive/Series', $this->callback(function (array $options): bool {
                $body = $options['json'] ?? [];
                return ($body['ID'] ?? null) === 5
                    && ($body['IsActive'] ?? null) === false
                    && ($body['IsDefault'] ?? null) === false;
            }))
            ->willReturn(new GuzzleResponse(200, [], 'true'));

        $request = new UpdateSeriesRequest(id: 5, isDefault: false, isActive: false);
        $result  = $this->service->updateSeries($request);

        $this->assertTrue($result);
    }

    public function test_update_series_payload_includes_all_required_fields(): void
    {
        $captured = [];

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with(
                'PUT',
                'https://apitest.nilvera.com/earchive/Series',
                $this->callback(function (array $options) use (&$captured): bool {
                    $captured = $options['json'] ?? [];
                    return true;
                })
            )
            ->willReturn(new GuzzleResponse(200, [], 'true'));

        $this->service->updateSeries(new UpdateSeriesRequest(id: 3, isDefault: false, isActive: true));

        $this->assertArrayHasKey('ID', $captured);
        $this->assertArrayHasKey('IsDefault', $captured);
        $this->assertArrayHasKey('IsActive', $captured);
    }
}
