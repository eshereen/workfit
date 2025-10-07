<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use BackedEnum;
use UnitEnum;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-user-circle';
    protected static string | UnitEnum | null $navigationGroup = 'Account';
    protected static ?int $navigationSort = 50;
    protected string $view = 'filament.pages.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique('users', 'email', auth()->id()),
                    ]),

                Section::make('Update Password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->requiredWith('password,password_confirmation')
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, $fail) {
                                        if ($value && ! Hash::check($value, auth()->user()->password)) {
                                            $fail('The current password is incorrect.');
                                        }
                                    };
                                }
                            ]),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->rules([Password::defaults()])
                            ->same('password_confirmation')
                            ->dehydrated(fn ($state) => filled($state)),

                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                    ])
                    ->description('Leave blank to keep current password.'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        if (! empty($data['password'])) {
            if (empty($data['current_password'])) {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('Current password is required to set a new password.')
                    ->send();
                return;
            }

            if (! Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('The current password is incorrect.')
                    ->send();
                return;
            }

            $user->password = Hash::make($data['password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        // Clear password fields
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        Notification::make()
            ->success()
            ->title('Success')
            ->body('Profile updated successfully.')
            ->send();
    }
}
