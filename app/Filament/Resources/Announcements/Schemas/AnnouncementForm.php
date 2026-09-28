<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Models\Trip;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Anunț')
                    ->schema([
                        Tabs::make('Limba')->tabs([
                            Tab::make('Română')->schema([
                                TextInput::make('title.ro')->label('Titlu')->required(),
                                Textarea::make('body.ro')->label('Text')->rows(5)->required(),
                            ]),
                            Tab::make('English')->schema([
                                TextInput::make('title.en')->label('Title'),
                                Textarea::make('body.en')->label('Text')->rows(5),
                            ]),
                        ]),
                    ]),

                Section::make('Cine îl vede')
                    ->columns(2)
                    ->schema([
                        Select::make('trip_id')
                            ->label('Croazieră')
                            ->options(fn () => Trip::all()->mapWithKeys(fn (Trip $t) => [
                                $t->id => $t->getTranslation('title', 'ro') . ' · ' . $t->start_date->format('m.Y'),
                            ]))
                            ->searchable()
                            ->placeholder('Toți membrii')
                            ->helperText('Gol = anunț general. Altfel, doar participanții croazierei.'),

                        DateTimePicker::make('published_at')
                            ->label('Publicat la')
                            ->native(false)
                            ->default(now())
                            ->helperText('Gol = ciornă, nu se vede încă.'),

                        Toggle::make('pinned')->label('Important (apare primul)'),
                    ]),
            ]);
    }
}
