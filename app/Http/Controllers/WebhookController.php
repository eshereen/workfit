<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{


    public function paymob(Request $request)
    {
        app(\App\Payments\Gateways\PaymobGateway::class)->handleWebhook($request->all(), $request->header('hmac'));
        return response()->json(['ok' => true]);
    }

    public function paypal(Request $request)
    {
        app(\App\Payments\Gateways\PaypalGateway::class)->handleWebhook($request->all(), $request->header('Paypal-Transmission-Sig'));
        return response()->json(['ok' => true]);
    }
}
