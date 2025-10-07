<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use UnitEnum;

class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-user-circle';
    protected string $view = 'filament.pages.profile';
    protected static string | UnitEnum | null $navigationGroup = 'Account';
    protected static ?int $navigationSort = 50;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(auth()->user()->only(['name', 'email']));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique('users', 'email', ignoreRecord: true),

                Forms\Components\TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->dehydrated(false)
                    ->rule('required_with:password,password_confirmation')
                    ->rule(function () {
                        return function (string $attribute, $value, $fail) {
                            if ($value && ! Hash::check($value, auth()->user()->password)) {
                                $fail('The current password is incorrect.');
                            }
                        };
                    }),

                Forms\Components\TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->rule(Password::defaults())
                    ->confirmed(),

                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = auth()->user();

        $validated = $this->form->getState();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $this->notify('success', 'Profile updated successfully.');
    }
}
