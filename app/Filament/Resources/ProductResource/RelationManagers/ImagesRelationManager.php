<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\ProductImage;
use App\Services\ImageService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Images';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->required()
                    ->directory('temp-uploads')
                    ->disk('public')
                    ->maxSize(5120)
                    ->acceptedFileTypes((new ImageService)->allowedMimes())
                    ->hiddenOn('edit'),

                Forms\Components\TextInput::make('alt_text')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Describe the image for accessibility'),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ]);
    }

    /**
     * Process the uploaded image through ImageService on creation.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        /** @var string $tempPath */
        $tempPath = $data['image'];
        $disk = Storage::disk('public');
        $fullPath = $disk->path($tempPath);

        $uploadedFile = new UploadedFile(
            $fullPath,
            basename($fullPath),
            mime_content_type($fullPath) ?: 'image/jpeg',
            null,
            true
        );

        $imageService = app(ImageService::class);
        $result = $imageService->process($uploadedFile, 'products');

        // Clean up temp file
        $disk->delete($tempPath);

        return $this->getRelationship()->create([
            'path_large' => $result['path_large'],
            'path_medium' => $result['path_medium'],
            'path_thumb' => $result['path_thumb'],
            'width' => $result['width'],
            'height' => $result['height'],
            'alt_text' => $data['alt_text'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path_thumb')
                    ->label('Thumb')
                    ->disk('public')
                    ->width(60)
                    ->height(60),

                Tables\Columns\TextColumn::make('alt_text')
                    ->limit(40),

                Tables\Columns\TextColumn::make('width')
                    ->label('W×H')
                    ->formatStateUsing(fn (ProductImage $record): string => $record->width.'×'.$record->height),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (ProductImage $record): void {
                        app(ImageService::class)->delete([
                            'path_large' => $record->path_large,
                            'path_medium' => $record->path_medium,
                            'path_thumb' => $record->path_thumb,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
