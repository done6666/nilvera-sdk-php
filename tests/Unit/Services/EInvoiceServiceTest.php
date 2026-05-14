<?php

declare(strict_types=1);

namespace Nilvera\Tests\Unit\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Nilvera\Config;
use Nilvera\Http\HttpClient;
use Nilvera\Requests\ListInvoicesRequest;
use Nilvera\Requests\SendByEmailRequest;
use Nilvera\Requests\SendInvoiceRequest;
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
            customerInfo: $receiver,
            invoiceLines: [InvoiceLineRequest::make('Urun', 1, UnitType::Piece, 1000, 20)],
            issueDate:    new \DateTimeImmutable('2026-05-14'),
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
}
