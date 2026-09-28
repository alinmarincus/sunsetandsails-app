<?php

namespace App\Filament\Resources\Trips\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')->label('')->circular(),

                TextColumn::make('title')
                    ->label('Croazieră')
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', 'ro'))
                    ->searchable(query: fn ($query, string $search) => $query->where('title', 'like', "%{$search}%"))
                    ->weight('bold'),

                TextColumn::make('destination')
                    ->label('Destinație')
                    ->getStateUsing(fn ($record) => $record->getTranslation('destination', 'ro'))
                    ->badge(),

                TextColumn::make('start_date')->label('Plecare')->date('d.m.Y')->sortable(),
                TextColumn::make('end_date')->label('Întoarcere')->date('d.m.Y')->sortable(),

                TextColumn::make('participants_count')
                    ->label('Participanți')
                    ->counts('participants')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Stare')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? 'Publicată' : 'Ciornă')
                    ->color(fn (string $state) => $state === 'published' ? 'success' : 'gray'),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stare')
                    ->options(['draft' => 'Ciornă', 'published' => 'Publicată']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
