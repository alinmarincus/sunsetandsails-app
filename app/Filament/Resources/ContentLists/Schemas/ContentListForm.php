<?php

namespace App\Filament\Resources\ContentLists\Schemas;

use App\Models\ContentList;
use App\Support\ImageProcessor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ContentListForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Lista')
                ->schema([
                    Select::make('type')
                        ->label('Tip')
                        ->options(ContentList::TIPURI)
                        ->required()
                        ->helperText('Hotărăște unde poate fi atribuită lista.'),

                    Tabs::make('Denumire')->tabs([
                        Tab::make('Română')->schema([
                            TextInput::make('name.ro')->label('Denumire')->required(),
                            Textarea::make('intro.ro')->label('Text introductiv')->rows(2),
                        ]),
                        Tab::make('English')->schema([
                            TextInput::make('name.en')->label('Name'),
                            Textarea::make('intro.en')->label('Intro text')->rows(2),
                        ]),
                    ]),
                ]),

            Section::make('Conținut')
                ->description('Secțiuni, subsecțiuni și elemente. Se pot reordona prin tragere.')
                ->schema([
                    Repeater::make('sections')
                        ->relationship()
                        ->label('')
                        ->orderColumn('position')
                        ->collapsed()
                        ->cloneable()
                        ->defaultItems(0)
                        ->addActionLabel('Adaugă secțiune')
                        ->itemLabel(fn (array $state): ?string => $state['title']['ro'] ?? 'Secțiune nouă')
                        ->schema([
                            Tabs::make('Titlu')->tabs([
                                Tab::make('Română')->schema([
                                    TextInput::make('title.ro')->label('Titlul secțiunii')->required(),
                                    Textarea::make('note.ro')->label('Notă')->rows(2),
                                ]),
                                Tab::make('English')->schema([
                                    TextInput::make('title.en')->label('Section title'),
                                    Textarea::make('note.en')->label('Note')->rows(2),
                                ]),
                            ]),

                            Repeater::make('items')
                                ->relationship()
                                ->label('Elemente')
                                ->orderColumn('position')
                                ->collapsed()
                                ->cloneable()
                                ->defaultItems(0)
                                ->addActionLabel('Adaugă element')
                                ->itemLabel(fn (array $state): ?string => $state['title']['ro'] ?? 'Element nou')
                                ->schema(self::campuriElement()),

                            Repeater::make('children')
                                ->relationship()
                                ->label('Subsecțiuni')
                                ->orderColumn('position')
                                ->collapsed()
                                ->defaultItems(0)
                                ->addActionLabel('Adaugă subsecțiune')
                                ->itemLabel(fn (array $state): ?string => $state['title']['ro'] ?? 'Subsecțiune nouă')
                                ->schema([
                                    TextInput::make('title.ro')->label('Titlu (RO)')->required(),
                                    TextInput::make('title.en')->label('Titlu (EN)'),

                                    Repeater::make('items')
                                        ->relationship()
                                        ->label('Elemente')
                                        ->orderColumn('position')
                                        ->collapsed()
                                        ->cloneable()
                                        ->defaultItems(0)
                                        ->addActionLabel('Adaugă element')
                                        ->itemLabel(fn (array $state): ?string => $state['title']['ro'] ?? 'Element nou')
                                        ->schema(self::campuriElement()),
                                ]),
                        ]),
                ]),
        ]);
    }

    /**
     * Câmpurile unui element, aceleași pentru toate tipurile de listă:
     * la necesar folosești linkul către magazin, la meniu poza preparatului,
     * la informații utile linkul video. Ce nu-ți trebuie, lași gol.
     */
    private static function campuriElement(): array
    {
        return [
            Tabs::make('Text')->tabs([
                Tab::make('Română')->schema([
                    TextInput::make('title.ro')->label('Titlu')->required(),
                    Textarea::make('body.ro')->label('Descriere')->rows(2),
                ]),
                Tab::make('English')->schema([
                    TextInput::make('title.en')->label('Title'),
                    Textarea::make('body.en')->label('Description')->rows(2),
                ]),
            ]),

            FileUpload::make('image_path')
                ->label('Imagine')
                ->image()
                ->imageEditor()
                ->helperText('Se comprimă și se convertește automat în WebP.')
                ->saveUploadedFileUsing(
                    fn (TemporaryUploadedFile $file) => ImageProcessor::store(
                        $file->getRealPath(), 'continut', maxWidth: 1200, quality: 80
                    )
                ),

            TextInput::make('link_url')
                ->label('Link')
                ->url()
                ->helperText('De exemplu, pagina de unde se cumpără.'),

            TextInput::make('link_label.ro')
                ->label('Textul linkului')
                ->placeholder('De unde cumperi'),

            TextInput::make('video_url')
                ->label('Link video')
                ->url()
                ->helperText('YouTube sau orice altă adresă.'),
        ];
    }
}
