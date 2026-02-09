<?php

namespace App\Filament\Resources\ShippingZoneResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PostalPrefixesRelationManager extends RelationManager
{
    protected static string $relationship = 'postalPrefixes';

    protected static ?string $title = 'Postal Prefixes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('postal_prefix')
                    ->label('Postal prefix')
                    ->required()
                    ->maxLength(10)
                    ->helperText('e.g. "10", "11" — matches postal codes starting with this prefix'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('postal_prefix')
                    ->label('Prefix')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('postal_prefix')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
