<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Filament\Resources\BlogPostResource\RelationManagers;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Blog Post')
                    ->tabs([
                        self::generalTab(),
                        self::imageTab(),
                        self::publishingTab(),
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
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => auth()->id()),

                Forms\Components\Textarea::make('excerpt')
                    ->maxLength(500)
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('body')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function imageTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Featured Image')
            ->icon('heroicon-o-photo')
            ->schema([
                Forms\Components\FileUpload::make('featured_image')
                    ->image()
                    ->directory('temp-uploads')
                    ->disk('public')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->columnSpanFull(),
            ]);
    }

    private static function publishingTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Publishing')
            ->icon('heroicon-o-clock')
            ->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(false),

                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publish date'),
            ]);
    }

    private static function seoTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('SEO')
            ->icon('heroicon-o-magnifying-glass')
            ->schema([
                Forms\Components\TextInput::make('meta_title')
                    ->maxLength(255)
                    ->helperText('Leave blank to use post title'),

                Forms\Components\Textarea::make('meta_description')
                    ->maxLength(500)
                    ->rows(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('comments'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('author.name')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('categories.name')
                    ->badge(),

                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comments')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),

                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
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
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\TagsRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
