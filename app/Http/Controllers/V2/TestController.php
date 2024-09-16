<?php

namespace App\Http\Controllers\V2;
use App\Events\Usdt\UsdtTransactionEvent;
use Illuminate\Http\Request;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;
use Psy\Command\ListCommand\FunctionEnumerator;

class TestController extends Controller
{

    #[Get(
        tags: ['Test'],
        operationId: 'testWsNotifyTransaction',
        path: '/test/ws-notify-transaction',
        description: 'Test WebSocket notify transaction',
        parameters: [
            new Parameter(
                name: 'product',
                in: 'query',
                description: 'Product name',
            )
        ],
        responses: [
            new Response(response: 200, description: 'Success', content: new JsonContent(type: 'object')),
        ]
    )]
    public function testWsNotifyTransaction(Request $request): void
    {
        broadcast(new UsdtTransactionEvent(
            $request->get('product', 'Test product'),
            $request->user()->id
        ));
    }

}