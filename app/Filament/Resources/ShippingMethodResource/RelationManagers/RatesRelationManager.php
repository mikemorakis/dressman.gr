<?php

namespace App\Filament\Resources\ShippingMethodResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RatesRelationManager extends RelationManager
{
    protected static string $relationship = 'rates';

    protected static ?string $title = 'Shipping Rates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_code')
                    ->label('Country')
                    ->options([
                        'GR' => 'Greece',
                        'CY' => 'Cyprus',
                    ])
                    ->default('GR')
                    ->required(),

                Forms\Components\Select::make('shipping_zone_id')
                    ->label('Shipping Zone')
                    ->relationship('zone', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('None (country-level)'),

                Forms\Components\TextInput::make('region_code')
                    ->label('Region code')
                    ->maxLength(10)
                    ->helperText('Optional regional restriction'),

                Forms\Components\TextInput::make('min_subtotal')
                    ->label('Min subtotal')
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01')
                    ->minValue(0)
                    ->helperText('Leave blank for no minimum'),

                Forms\Components\TextInput::make('max_subtotal')
                    ->label('Max subtotal')
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01')
                    ->minValue(0)
                    ->helperText('Leave blank for no maximum'),

                Forms\Components\TextInput::make('flat_amount')
                    ->label('Flat rate')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01')
                    ->minValue(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country_code')
                    ->label('Country')
                    ->sortable(),

                Tables\Columns\TextColumn::make('zone.name')
                    ->label('Zone')
                    ->placeholder('--')
                    ->sortable(),

                Tables\Columns\TextColumn::make('region_code')
                    ->label('Region')
                    ->placeholder('--')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('min_subtotal')
                    ->label('Min')
                    ->money('EUR')
                    ->placeholder('--'),

                Tables\Columns\TextColumn::make('max_subtotal')
                    ->label('Max')
                    ->money('EUR')
                    ->placeholder('--'),

                Tables\Columns\TextColumn::make('flat_amount')
                    ->label('Rate')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('country_code')
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
