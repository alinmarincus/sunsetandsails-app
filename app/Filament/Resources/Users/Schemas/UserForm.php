<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Support\ImageProcessor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Membru')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nume')->required(),
                        TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                        TextInput::make('phone')->label('Telefon')->tel(),
                        Select::make('locale')
                            ->label('Limbă')
                            ->options(['ro' => 'Română', 'en' => 'English'])
                            ->default('ro'),

                        TextInput::make('password')
                            ->label('Parolă')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText('La editare, lasă gol ca să rămână parola actuală.'),

                        FileUpload::make('avatar_path')
                            ->label('Poză de profil')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->helperText('Se comprimă și se convertește automat în WebP.')
                            // Conversia in WebP se face la salvare, nu in browser
                            ->saveUploadedFileUsing(
                                fn (TemporaryUploadedFile $file) => ImageProcessor::storeAvatar($file->getRealPath())
                            ),
                    ]),

                Section::make('Statut în club')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options(['prospect' => 'Înscris', 'member' => 'Membru'])
                            ->default('prospect')
                            ->helperText('„Membru" înseamnă că a fost cel puțin într-o croazieră.'),

                        DateTimePicker::make('photo_consent_at')
                            ->label('Acord pentru fotografii')
                            ->native(false)
                            ->helperText('Gol = nu și-a dat acordul.'),

                        Toggle::make('wall_public')->label('Perete de poze public'),
                        Toggle::make('is_admin')->label('Administrator')
                            ->helperText('Are acces în acest panou.'),
                    ]),
            ]);
    }
}
