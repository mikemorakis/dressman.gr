<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01')
                    ->minValue(0)
                    ->helperText('Leave blank to use parent product price'),

                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required(),

                Forms\Components\Select::make('attribute_values')
                    ->label('Attributes')
                    ->multiple()
                    ->options(
                        AttributeValue::with('attribute')
                            ->orderBy('attribute_id')
                            ->orderBy('sort_order')
                            ->get()
                            ->mapWithKeys(fn (AttributeValue $av) => [
                                $av->id => $av->attribute->name.': '.$av->value,
                            ])
                    )
                    ->required()
                    ->rules([
                        fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                            if (! is_array($value) || count($value) === 0) {
                                return;
                            }
                            // Check: at most one value per attribute (no two "Size" values)
                            $attrIds = AttributeValue::whereIn('id', $value)->pluck('attribute_id');
                            if ($attrIds->count() !== $attrIds->unique()->count()) {
                                $fail('Each attribute may only have one value per variant.');
                            }
                        },
                    ])
                    ->helperText('Select exactly one value per attribute (e.g. Size: M, Color: Red)'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])
            ->columns(2);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        /** @var list<int|string> $rawIds */
        $rawIds = $data['attribute_values'] ?? [];
        $attributeValueIds = array_map('intval', $rawIds);
        unset($data['attribute_values']);

        $data['signature'] = ProductVariant::generateSignature($attributeValueIds);

        /** @var ProductVariant $variant */
        $variant = $this->getRelationship()->create($data);
        $variant->attributeValues()->sync($attributeValueIds);

        return $variant;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var list<int|string> $rawIds */
        $rawIds = $data['attribute_values'] ?? [];
        $attributeValueIds = array_map('intval', $rawIds);
        unset($data['attribute_values']);

        $data['signature'] = ProductVariant::generateSignature($attributeValueIds);

        $record->update($data);

        /** @var ProductVariant $record */
        $record->attributeValues()->sync($attributeValueIds);

        return $record;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['id'])) {
            $variant = ProductVariant::find($data['id']);
            $data['attribute_values'] = $variant?->attributeValues()->pluck('attribute_values.id')->toArray() ?? [];
        }

        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attributeValues.value')
                    ->label('Attributes')
                    ->badge(),

                Tables\Columns\TextColumn::make('effective_price')
                    ->label('Price')
                    ->money('EUR'),

                Tables\Columns\TextColumn::make('stock')
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_stock')
                    ->label('Available'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
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
