<?php

namespace Tests\Feature;

use App\Livewire\Newsletter\SubscribeForm;
use App\Mail\NewsletterVerificationMail;
use App\Models\Newsletter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_subscribe_and_receives_verification_email()
    {
        Mail::fake();

        Livewire::test(SubscribeForm::class)
            ->set('email', 'test@test.com') // ✅ domain with MX records or simple validation
            ->call('submit')
            ->assertSet('submitted', true);

        Mail::assertQueued(NewsletterVerificationMail::class, function ($mail) {
            return $mail->hasTo('test@test.com');
        });

        $this->assertDatabaseHas('newsletters', [
            'email'    => 'test@test.com',
            'verified' => false,
        ]);
    }

}
