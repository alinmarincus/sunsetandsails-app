<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    protected static ?string $title = 'Participanți';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->label('Stare')
                ->options(['confirmed' => 'Confirmat', 'cancelled' => 'Anulat'])
                ->default('confirmed')
                ->required(),
            Textarea::make('notes')->label('Observații')->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('avatar_path')->label('')->circular()
                    ->defaultImageUrl(asset('images/avatar-placeholder.svg')),

                TextColumn::make('name')->label('Nume')->searchable()->weight('bold'),
                TextColumn::make('email')->label('Email')->searchable()->copyable(),
                TextColumn::make('phone')->label('Telefon'),

                TextColumn::make('pivot.status')
                    ->label('Stare')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state === 'cancelled' ? 'Anulat' : 'Confirmat')
                    ->color(fn (?string $state) => $state === 'cancelled' ? 'danger' : 'success'),

                TextColumn::make('pivot.notes')->label('Observații')->wrap()->toggleable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Adaugă participant')
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->schema(fn (AttachAction $action) => [
                        $action->getRecordSelect()->label('Membru'),
                        Select::make('status')
                            ->label('Stare')
                            ->options(['confirmed' => 'Confirmat', 'cancelled' => 'Anulat'])
                            ->default('confirmed'),
                    ])
                    // Participarea confirmata il face membru cu drepturi depline
                    ->after(function ($record) {
                        if ($record && $record->status !== 'member') {
                            $record->update(['status' => 'member']);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make()->label('Editează'),
                DetachAction::make()->label('Scoate'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
