<?php
namespace App\Http\Controllers;

use App\Payments\Gateways\PaymobGateway;
use App\Payments\Gateways\PaypalGateway;
use Illuminate\Http\Request;

class WebhookController extends Controller
{


    public function paymob(Request $request)
    {
        app(PaymobGateway::class)->handleWebhook($request->all(), $request->header('hmac'));
        return response()->json(['ok' => true]);
    }

    public function paypal(Request $request)
    {
        app(PaypalGateway::class)->handleWebhook($request->all(), $request->header('Paypal-Transmission-Sig'));
        return response()->json(['ok' => true]);
    }
}
