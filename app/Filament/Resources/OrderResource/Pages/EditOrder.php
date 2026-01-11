<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;


class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Send email notification when order status changes
     */
    protected function afterSave(): void
    {
        $order = $this->record;
        
        // Check if status was changed
        if ($order->wasChanged('status')) {
            // Load order relationships for email
            $order->load(['items.product', 'items.variant', 'country', 'customer']);
            
            // Determine email address
            $emailAddress = $order->customer?->email ?? $order->email;
            
            if ($emailAddress) {
                try {
                    Mail::to($emailAddress)->send(new OrderShipped($order));
                    
                    // Optional: Add a success notification
                    \Filament\Notifications\Notification::make()
                        ->title('Email Sent')
                        ->body("Order status update email sent to {$emailAddress}")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    // Log error but don't fail the save
                    \Log::error('Failed to send order status email', [
                        'order_id' => $order->id,
                        'email' => $emailAddress,
                        'error' => $e->getMessage()
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Email Failed')
                        ->body('Failed to send status update email')
                        ->warning()
                        ->send();
                }
            }
        }
    }
}
