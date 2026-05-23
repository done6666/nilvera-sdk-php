<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Nilvera\Config;
use Nilvera\Http\HttpClient;
use Nilvera\Requests\CreateSeriesRequest;
use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\ListSeriesRequest;
use Nilvera\Requests\SendByEmailRequest;
use Nilvera\Requests\SendInvoiceRequest;
use Nilvera\Requests\UpdateSeriesRequest;
use Nilvera\Requests\ValueObjects\InvoiceLineRequest;
use Nilvera\Requests\ValueObjects\ReceiverRequest;
use Nilvera\Enums\UnitType;
use Nilvera\Services\EInvoiceService;
use PHPUnit\Framework\TestCase;

class EInvoiceServiceTest extends TestCase
{
    private EInvoiceService $service;
    private ClientInterface $guzzle;

    protected function setUp(): void
    {
        $this->guzzle  = $this->createMock(ClientInterface::class);
        $httpClient    = new HttpClient($this->guzzle, Config::test('test-key'));
        $this->service = new EInvoiceService($httpClient);
    }

    public function test_preview_posts_to_correct_endpoint(): void
    {
        $expectedHtml = '<html><body>Fatura Önizleme</body></html>';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/einvoice/Send/Model/Preview', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], json_encode($expectedHtml)));

        $receiver = new ReceiverRequest(
            taxNumber: '1234567890',
            name:      'Test Sirket',
            address:   'Test Mah. No:1',
            district:  'Kadikoy',
            city:      'Istanbul',
        );
        $invoice = new SendInvoiceRequest(
            customerInfo:         $receiver,
            invoiceLines:         [InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20)],
            issueDate:            new \DateTimeImmutable('2026-05-19'),
            customerAlias:        'urn:mail:test@sirket.com.tr',
            invoiceSerieOrNumber: 'EFT',
        );

        $result = $this->service->preview($invoice);

        $this->assertSame($expectedHtml, $result);
    }

    public function test_preview_decodes_json_wrapped_html(): void
    {
        $html = '<!DOCTYPE html><html><body>Önizleme</body></html>';

        $this->guzzle->method('request')
            ->willReturn(new GuzzleResponse(200, [], json_encode($html)));

        $receiver = new ReceiverRequest(
            taxNumber: '1234567890',
            name:      'Test Sirket',
            address:   'Test Mah. No:1',
            district:  'Kadikoy',
            city:      'Istanbul',
        );
        $invoice = new SendInvoiceRequest(
            customerInfo:         $receiver,
            invoiceLines:         [InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20)],
            issueDate:            new \DateTimeImmutable('2026-05-19'),
            customerAlias:        'urn:mail:test@sirket.com.tr',
            invoiceSerieOrNumber: 'EFT',
        );

        $result = $this->service->preview($invoice);

        $this->assertIsString($result);
        $this->assertStringStartsWith('<!DOCTYPE html>', $result);
        $this->assertStringNotContainsString('\\"', $result);
    }

    public function test_send_posts_to_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/einvoice/Send/Model', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"UUID":"abc-123","InvoiceNumber":"INV-001"}'));

        $receiver = new ReceiverRequest(
            taxNumber: '1234567890',
            name:      'Test Sirket',
            address:   'Test Mah. No:1',
            district:  'Kadikoy',
            city:      'Istanbul',
        );
        $invoice = new SendInvoiceRequest(
            customerInfo:         $receiver,
            invoiceLines:         [InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20)],
            issueDate:            new \DateTimeImmutable('2026-05-14'),
            customerAlias:        'urn:mail:test@sirket.com.tr',
            invoiceSerieOrNumber: 'EFT',
        );

        $result = $this->service->send($invoice);

        $this->assertSame('abc-123', $result->uuid);
        $this->assertSame('INV-001', $result->invoiceNumber);
    }

    public function test_list_sale_invoices_with_query_params(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/einvoice/Sale', $this->callback(
                fn ($opts) => ($opts['query']['Page'] ?? null) === 1
            ))
            ->willReturn(new GuzzleResponse(200, [], '{"items":[],"total":0}'));

        $result = $this->service->listSaleInvoices(new ListInvoicesRequest(page: 1));

        $this->assertSame([], $result['items']);
    }

    public function test_get_sale_invoice_model_uses_uuid_in_path(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', "https://apitest.nilvera.com/einvoice/Sale/{$uuid}/model", $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"UUID":"' . $uuid . '"}'));

        $result = $this->service->getSaleInvoiceModel($uuid);

        $this->assertSame($uuid, $result['UUID']);
    }

    public function test_get_sale_invoice_envelope_info(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', "https://apitest.nilvera.com/einvoice/Sale/{$uuid}/EnvelopeInfo", $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"GIBCode":"0","GIBDescription":"Basarili","EnvelopeUUID":"env-uuid"}'));

        $result = $this->service->getSaleInvoiceEnvelopeInfo($uuid);

        $this->assertSame('0', $result['GIBCode']);
    }

    public function test_send_sale_invoice_by_email(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/einvoice/Sale/Email/Send', $this->callback(
                fn ($opts) => ($opts['json']['UUID'] ?? null) === $uuid
                    && in_array('test@example.com', $opts['json']['emailAddresses'] ?? [])
            ))
            ->willReturn(new GuzzleResponse(200, [], ''));

        $this->service->sendSaleInvoiceByEmail(
            new SendByEmailRequest($uuid, ['test@example.com'])
        );
    }

    public function test_create_draft_sends_correct_payload(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/einvoice/Draft/Create', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"UUID":"draft-uuid"}'));

        $result = $this->service->createDraft(['InvoiceInfo' => []]);

        $this->assertSame('draft-uuid', $result['UUID']);
    }

    public function test_delete_draft_calls_delete_method(): void
    {
        $uuid = 'draft-uuid';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('DELETE', "https://apitest.nilvera.com/einvoice/Draft/{$uuid}", $this->anything())
            ->willReturn(new GuzzleResponse(204, [], ''));

        $this->service->deleteDraft($uuid);
    }

    public function test_create_return_from_purchase_invoice(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', "https://apitest.nilvera.com/einvoice/Purchase/{$uuid}/CreateReturn", $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"UUID":"return-uuid","InvoiceNumber":null}'));

        $result = $this->service->createReturnFromPurchaseInvoice($uuid);

        $this->assertSame('return-uuid', $result['UUID']);
    }

    // -------------------------------------------------------------------------
    // Seri (Series) testleri
    // -------------------------------------------------------------------------

    public function test_list_series_calls_correct_endpoint(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/einvoice/Series', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"Content":[],"Page":1,"TotalCount":0}'));

        $result = $this->service->listSeries();

        $this->assertSame([], $result['Content']);
    }

    public function test_list_series_with_query_params(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', 'https://apitest.nilvera.com/einvoice/Series', $this->callback(
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
            ->with('GET', 'https://apitest.nilvera.com/einvoice/Series/42', $this->anything())
            ->willReturn(new GuzzleResponse(200, [], '{"ID":42,"Name":"EFT","IsActive":true,"IsDefault":false}'));

        $result = $this->service->getSeries(42);

        $this->assertSame(42, $result['ID']);
        $this->assertSame('EFT', $result['Name']);
    }

    public function test_create_series_sends_correct_payload(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/einvoice/Series', $this->callback(
                fn ($opts) => ($opts['json']['Name'] ?? null) === 'EFT'
                    && ($opts['json']['IsActive'] ?? null) === true
                    && ($opts['json']['IsDefault'] ?? null) === false
            ))
            ->willReturn(new GuzzleResponse(200, [], '{"ID":10,"Name":"EFT","IsActive":true,"IsDefault":false}'));

        $result = $this->service->createSeries(
            new CreateSeriesRequest(name: 'EFT', isActive: true, isDefault: false)
        );

        $this->assertSame(10, $result['ID']);
        $this->assertSame('EFT', $result['Name']);
    }

    public function test_update_series_returns_true_on_success(): void
    {
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('PUT', 'https://apitest.nilvera.com/einvoice/Series', $this->callback(
                fn ($opts) => ($opts['json']['ID'] ?? null) === 10
                    && ($opts['json']['IsActive'] ?? null) === false
                    && ($opts['json']['IsDefault'] ?? null) === false
            ))
            ->willReturn(new GuzzleResponse(200, [], 'true'));

        $result = $this->service->updateSeries(
            new UpdateSeriesRequest(id: 10, isDefault: false, isActive: false)
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

    // -------------------------------------------------------------------------
    // acceptInvoice / rejectInvoice / getPurchaseInvoiceStatus
    // -------------------------------------------------------------------------

    public function test_accept_invoice_posts_approved_answer_code(): void
    {
        $uuid = '3fa85f64-5717-4562-b3fc-2c963f66afa6';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/einvoice/Purchase/SendAnswer', $this->callback(
                fn ($opts) => ($opts['json']['UUID'] ?? null) === $uuid
                    && ($opts['json']['AnswerCode'] ?? null) === 'approved'
                    && !isset($opts['json']['RejectNote'])
            ))
            ->willReturn(new GuzzleResponse(200, [], '"Islem basarili"'));

        $result = $this->service->acceptInvoice($uuid);

        $this->assertSame('"Islem basarili"', $result);
    }

    public function test_reject_invoice_posts_rejected_answer_code_without_note(): void
    {
        $uuid = '3fa85f64-5717-4562-b3fc-2c963f66afa6';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/einvoice/Purchase/SendAnswer', $this->callback(
                fn ($opts) => ($opts['json']['UUID'] ?? null) === $uuid
                    && ($opts['json']['AnswerCode'] ?? null) === 'rejected'
                    && !isset($opts['json']['RejectNote'])
            ))
            ->willReturn(new GuzzleResponse(200, [], '"Islem basarili"'));

        $result = $this->service->rejectInvoice($uuid);

        $this->assertSame('"Islem basarili"', $result);
    }

    public function test_reject_invoice_includes_reject_note_when_provided(): void
    {
        $uuid = '3fa85f64-5717-4562-b3fc-2c963f66afa6';
        $note = 'Fatura tutarı hatalıdır';

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', 'https://apitest.nilvera.com/einvoice/Purchase/SendAnswer', $this->callback(
                fn ($opts) => ($opts['json']['UUID'] ?? null) === $uuid
                    && ($opts['json']['AnswerCode'] ?? null) === 'rejected'
                    && ($opts['json']['RejectNote'] ?? null) === $note
            ))
            ->willReturn(new GuzzleResponse(200, [], '"Islem basarili"'));

        $result = $this->service->rejectInvoice($uuid, $note);

        $this->assertSame('"Islem basarili"', $result);
    }

    public function test_get_purchase_invoice_status_uses_uuid_in_path(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $responseBody = json_encode([
            'InvoiceProfile' => 'TICARIFATURA',
            'IssueDate'      => '2026-05-01T00:00:00Z',
            'Answer'         => [
                'AnswerCode'  => 'waitingForApproval',
                'AnswerNote'  => null,
                'Description' => null,
            ],
            'InvoiceStatus'  => [
                'Code'              => 'succeed',
                'Description'       => 'Başarılı',
                'DetailDescription' => null,
            ],
            'EnvelopeInfo'   => [
                'UUID'           => 'env-uuid',
                'GIBCode'        => 0,
                'GIBDescription' => 'Başarılı',
                'CreatedDate'    => '2026-05-01T10:00:00Z',
            ],
        ]);

        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', "https://apitest.nilvera.com/einvoice/Purchase/{$uuid}/Status", $this->anything())
            ->willReturn(new GuzzleResponse(200, [], $responseBody));

        $result = $this->service->getPurchaseInvoiceStatus($uuid);

        $this->assertSame('TICARIFATURA', $result['InvoiceProfile']);
        $this->assertSame('waitingForApproval', $result['Answer']['AnswerCode']);
        $this->assertSame('succeed', $result['InvoiceStatus']['Code']);
        $this->assertSame(0, $result['EnvelopeInfo']['GIBCode']);
    }
}
