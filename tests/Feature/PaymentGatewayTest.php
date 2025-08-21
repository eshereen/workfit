<?php


namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Payments\Gateways\PaypalGateway;
use CodGateway;
use App\Services\Payments\PaymentGatewayManager;
use App\Payments\Gateways\PaymobGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentGatewayManager $manager;
    protected Order $order;


    protected function setUp(): void
    {
        parent::setUp();



        // Mock Paymob
        $this->app->bind(PaymobGateway::class, function () {
            return new class {
                public function charge(array $data) {
                    $order = $data['order'] ?? null;
                    if ($order) {
                        $order->update(['status' => 'paid']);
                    }
                    return ['success' => true, 'gateway' => 'paymob'];
                }
                public function isAvailableForCountry(string $countryCode): bool {
                    return $countryCode === 'EG';
                }
            };
        });

        // Mock Paypal
        $this->app->bind(PaypalGateway::class, function () {
            return new class {
                public function charge(array $data) {
                    $order = $data['order'] ?? null;
                    if ($order) {
                        $order->update(['status' => 'paid']);
                    }
                    return ['success' => true, 'gateway' => 'paypal'];
                }
                public function isAvailableForCountry(string $countryCode): bool {
                    return $countryCode !== 'EG';
                }
            };
        });

        // Mock COD
        $this->app->bind(CodGateway::class, function () {
            return new class {
                public function charge(array $data) {
                    $order = $data['order'] ?? null;
                    if ($order) {
                        $order->update(['status' => 'pending_payment']);
                    }
                    return ['success' => true, 'gateway' => 'cod'];
                }
                public function isAvailableForCountry(string $countryCode): bool {
                    return $countryCode === 'EG';
                }
            };
        });

        $this->manager = app(PaymentGatewayManager::class);

        $this->order = Order::factory()->create([
            'country_id' => 48,
            'total_amount' => 500,
            'status' => 'pending'
        ]);
    }


    /** @test */
    public function it_shows_correct_gateways_for_egypt()
    {
        $available = $this->manager->getAvailableGateways($this->order);

        $this->assertEquals(['paymob', 'cod'], $available);
    }

    /** @test */
    public function it_can_charge_with_paymob_fake()
    {
        $result = $this->manager->charge($this->order, 'paymob', 500);

        $this->assertTrue($result['success']);
        $this->assertEquals('paymob', $result['gateway']);

        $this->order->refresh();
        $this->assertEquals('paid', $this->order->status);
    }

    /** @test */
    public function it_can_mark_cod_as_pending_payment()
    {
        $result = $this->manager->charge($this->order, 'cod', 500);

        $this->assertTrue($result['success']);
        $this->assertEquals('cod', $result['gateway']);

        $this->order->refresh();
        $this->assertEquals('pending_payment', $this->order->status);
    }

    /** @test */


    /** @test */
    public function it_can_charge_with_paypal_fake()
    {
        $result = $this->manager->charge($this->order, 'paypal', 500);

        $this->assertTrue($result['success']);
        $this->assertEquals('paypal', $result['gateway']);

        $this->order->refresh();
        $this->assertEquals('paid', $this->order->status);
    }
    public function test_it_can_charge_with_paymob()
{
    $mock = $this->createMock(\App\Payments\Contracts\PaymentGatewayInterface::class);
    $mock->method('charge')->willReturn(['status' => 'success']);

    $manager = new PaymentGatewayManager($mock, $mock, $mock);

    $response = $manager->gateway('paymob')->charge(['amount' => 100]);

    $this->assertEquals('success', $response['status']);
}

}
