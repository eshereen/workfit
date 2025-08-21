<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\App;
use App\Payments\Gateways\PaypalGateway;
use App\Payments\Gateways\PaymobGateway;
use App\Payments\Gateways\CodGateway;


class PaymentService
{
    public function createPayment(Order $order, PaymentMethod $method, ?string $returnUrl, string $cancelUrl, string $paymentType = 'paypal_account'): array
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => $method->value,
            'status' => 'initiated',
            'currency' => $order->currency,
            'amount_minor' => $this->toMinor($order->total_amount, $order->currency),
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
        ]);

        $gateway = $this->gateway($method);

        // Pass payment type for PayPal gateway
        if ($method === PaymentMethod::PAYPAL && $gateway instanceof PaypalGateway) {
            $useCreditCard = ($paymentType === 'credit_card');
            $result = $gateway->initiate($order, $payment, $useCreditCard);
        } else {
            $result = $gateway->initiate($order, $payment);
        }

        // Always include the payment object in the result
        $result['payment'] = $payment;

        return $result;
    }

    public function gateway(PaymentMethod $method)
    {
        return match($method) {
            PaymentMethod::PAYMOB => App::make(PaymobGateway::class),
            PaymentMethod::PAYPAL => App::make(PaypalGateway::class),
            PaymentMethod::COD => App::make(CodGateway::class),
        };
    }

    private function toMinor(float $amount, string $currency): int
    {
        $zeroDecimal = ['JPY','KRW'];
        return (int) round($amount * (in_array(strtoupper($currency), $zeroDecimal, true) ? 1 : 100));
    }
}


