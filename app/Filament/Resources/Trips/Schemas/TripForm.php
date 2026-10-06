<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\ContentList;
use App\Support\ImageProcessor;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Croaziera')
                    ->schema([
                        Tabs::make('Limba')
                            ->tabs([
                                Tab::make('Română')->schema([
                                    TextInput::make('title.ro')
                                        ->label('Titlu')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            if (blank($get('slug'))) {
                                                $set('slug', Str::slug($state));
                                            }
                                        }),
                                    TextInput::make('destination.ro')->label('Destinație'),
                                    Textarea::make('summary.ro')->label('Pe scurt')->rows(2),
                                    Textarea::make('description.ro')->label('Descriere')->rows(5),
                                ]),
                                Tab::make('English')->schema([
                                    TextInput::make('title.en')->label('Title'),
                                    TextInput::make('destination.en')->label('Destination'),
                                    Textarea::make('summary.en')->label('Summary')->rows(2),
                                    Textarea::make('description.en')->label('Description')->rows(5),
                                ]),
                            ]),
                    ]),

                Section::make('Detalii')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('start_date')->label('Data plecării')->required()->native(false),
                        DatePicker::make('end_date')->label('Data întoarcerii')->required()->native(false),
                        TextInput::make('capacity')->label('Număr de locuri')->numeric()->minValue(1),
                        TextInput::make('boat')->label('Barca'),
                        TextInput::make('slug')
                            ->label('Adresă (slug)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Se completează singur din titlu.'),
                        Select::make('status')
                            ->label('Stare')
                            ->options(['draft' => 'Ciornă', 'published' => 'Publicată'])
                            ->default('draft')
                            ->required(),
                        FileUpload::make('cover_image')
                            ->label('Imagine de copertă')
                            ->image()
                            ->imageEditor()
                            ->helperText('Se redimensionează la 1600px și se convertește automat în WebP.')
                            ->saveUploadedFileUsing(
                                fn (TemporaryUploadedFile $file) => ImageProcessor::storeCover($file->getRealPath())
                            )
                            ->columnSpanFull(),
                        TextInput::make('drive_folder_id')
                            ->label('Folder Google Drive')
                            ->helperText('ID-ul folderului cu pozele croazierei. Se folosește la sincronizare.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Itinerar')
                    ->description('Zi cu zi. Apare în pagina croazierei.')
                    ->collapsed()
                    ->schema([
                        Repeater::make('days')
                            ->relationship()
                            ->hiddenLabel()
                            ->orderColumn('day_number')
                            ->columns(3)
                            ->schema([
                                TextInput::make('day_number')->label('Ziua')->numeric()->required(),
                                TextInput::make('port.ro')->label('Port (RO)')->required(),
                                TextInput::make('port.en')->label('Port (EN)'),
                                Textarea::make('note.ro')->label('Notă (RO)')->rows(2)->columnSpan(3),
                                Textarea::make('note.en')->label('Notă (EN)')->rows(2)->columnSpan(3),
                            ])
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['day_number']) ? 'Ziua ' . $state['day_number'] : null),
                    ]),

                Section::make('Liste atribuite')
                    ->description('Se administrează separat, la Liste. Aceeași listă poate fi folosită de mai multe croaziere.')
                    ->columns(3)
                    ->schema([
                        Select::make('packing_list_id')
                            ->label('Necesar de bagaj')
                            ->options(fn () => ContentList::optiuni('packing'))
                            ->searchable()
                            ->placeholder('Fără listă'),

                        Select::make('menu_list_id')
                            ->label('Meniu')
                            ->options(fn () => ContentList::optiuni('menu'))
                            ->searchable()
                            ->placeholder('Fără meniu'),

                        Select::make('info_list_id')
                            ->label('Informații utile')
                            ->options(fn () => ContentList::optiuni('info'))
                            ->searchable()
                            ->placeholder('Fără informații'),
                    ]),
            ]);
    }
}
