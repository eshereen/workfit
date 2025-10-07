<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends Page
{
    protected static \BackedEnum | string | null $navigationIcon = 'heroicon-o-user-circle';
    protected static string | \UnitEnum | null $navigationGroup = 'Account';
    protected static ?int $navigationSort = 50;
    protected static ?string $title = 'Profile';

    protected string $view = 'filament.pages.profile';

    public ?string $name = '';
    public ?string $email = '';
    public ?string $current_password = '';
    public ?string $password = '';
    public ?string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . auth()->id()],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        if (! empty($this->password)) {
            if (empty($this->current_password)) {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('Current password is required to set a new password.')
                    ->send();
                return;
            }

            if (! Hash::check($this->current_password, $user->password)) {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('The current password is incorrect.')
                    ->send();
                return;
            }

            $user->password = Hash::make($this->password);
        }

        $user->name = $this->name;
        $user->email = $this->email;
        $user->save();

        // Clear password fields
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';

        Notification::make()
            ->success()
            ->title('Success')
            ->body('Profile updated successfully.')
            ->send();
    }
}
