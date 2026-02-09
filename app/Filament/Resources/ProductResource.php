<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product')
                    ->tabs([
                        self::generalTab(),
                        self::pricingTab(),
                        self::relationshipsTab(),
                        self::seoTab(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function generalTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('General')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('short_description')
                    ->maxLength(500)
                    ->rows(2)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function pricingTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Pricing & Stock')
            ->icon('heroicon-o-currency-euro')
            ->schema([
                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01')
                            ->minValue(0),

                        Forms\Components\TextInput::make('compare_price')
                            ->label('Compare-at price')
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01')
                            ->minValue(0),

                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01')
                            ->minValue(0),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Inventory')
                    ->schema([
                        Forms\Components\Toggle::make('has_variants')
                            ->label('This product has variants')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, bool $state): void {
                                if (! $state) {
                                    $set('stock', 0);
                                }
                            })
                            ->helperText('Enable to manage stock per variant. Disabling resets product stock to 0.'),

                        Forms\Components\TextInput::make('stock')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->visible(fn (Forms\Get $get): bool => ! $get('has_variants')),

                        Forms\Components\TextInput::make('low_stock_threshold')
                            ->numeric()
                            ->default(5)
                            ->minValue(0),

                        Forms\Components\TextInput::make('weight')
                            ->label('Weight (g)')
                            ->numeric()
                            ->step('0.01')
                            ->minValue(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured'),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish date'),
                    ])
                    ->columns(3),
            ]);
    }

    private static function relationshipsTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Relationships')
            ->icon('heroicon-o-link')
            ->schema([
                Forms\Components\Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('No brand'),

                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('labels')
                    ->relationship('labels', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->columns(2);
    }

    private static function seoTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('SEO')
            ->icon('heroicon-o-magnifying-glass')
            ->schema([
                Forms\Components\TextInput::make('meta_title')
                    ->maxLength(255)
                    ->helperText('Leave blank to use product name'),

                Forms\Components\Textarea::make('meta_description')
                    ->maxLength(500)
                    ->rows(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withSum('variants as variants_stock_sum', 'stock')
                ->withSum('variants as variants_reserved_sum', 'reserved_stock')
                ->withCount('variants', 'images'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->formatStateUsing(function (Product $record): string {
                        if ($record->has_variants) {
                            return $record->available_stock.' (variants)';
                        }

                        return (string) $record->available_stock;
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('brand.name')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Variants')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('images_count')
                    ->label('Images')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),

                Tables\Filters\TernaryFilter::make('has_variants')
                    ->label('Has variants'),

                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('All'),

                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('labels')
                    ->relationship('labels', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ImagesRelationManager::class,
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
