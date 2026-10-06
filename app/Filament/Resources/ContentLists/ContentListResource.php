<?php

namespace App\Filament\Resources\ContentLists;

use App\Filament\Resources\ContentLists\Pages\CreateContentList;
use App\Filament\Resources\ContentLists\Pages\EditContentList;
use App\Filament\Resources\ContentLists\Pages\ListContentLists;
use App\Filament\Resources\ContentLists\Schemas\ContentListForm;
use App\Filament\Resources\ContentLists\Tables\ContentListsTable;
use App\Models\ContentList;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentListResource extends Resource
{
    protected static ?string $model = ContentList::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Liste';

    protected static ?string $modelLabel = 'listă';

    protected static ?string $pluralModelLabel = 'liste';

    protected static ?int $navigationSort = 2;

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): ?string
    {
        return $record?->getTranslation('name', 'ro');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentListForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentListsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentLists::route('/'),
            'create' => CreateContentList::route('/create'),
            'edit' => EditContentList::route('/{record}/edit'),
        ];
    }
}
