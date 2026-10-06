<?php

namespace App\Filament\Resources\ContentLists\Tables;

use App\Filament\Resources\ContentLists\ContentListResource;
use App\Models\ContentList;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContentListsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Denumire')
                    ->getStateUsing(fn (ContentList $record) => $record->getTranslation('name', 'ro'))
                    ->searchable(query: fn ($query, string $search) => $query->where('name', 'like', "%{$search}%"))
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ContentList::TIPURI[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'packing' => 'warning',
                        'menu'    => 'success',
                        default   => 'info',
                    }),

                TextColumn::make('sections_count')
                    ->label('Secțiuni')
                    ->counts('sections')
                    ->alignCenter(),

                TextColumn::make('folosita')
                    ->label('Folosită la')
                    ->getStateUsing(fn (ContentList $record) => $record->trips()->count() . ' croaziere')
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Modificată')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Tip')
                    ->options(ContentList::TIPURI),
            ])
            ->recordActions([
                EditAction::make(),

                // Copie completă, cu secțiuni și elemente, de adaptat pentru altă ieșire
                Action::make('duplica')
                    ->label('Duplică')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Duplică lista')
                    ->modalDescription('Se creează o copie cu toate secțiunile și elementele. Imaginile sunt refolosite, nu dublate pe disc.')
                    ->action(function (ContentList $record) {
                        $copie = $record->duplicate(
                            $record->getTranslation('name', 'ro') . ' (copie)'
                        );

                        Notification::make()
                            ->title('Listă duplicată')
                            ->body('Poți edita acum copia.')
                            ->success()
                            ->send();

                        return redirect(ContentListResource::getUrl('edit', ['record' => $copie]));
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
