<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use App\Services\ImageService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->processImage($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function processImage(array $data): array
    {
        if (! empty($data['featured_image'])) {
            $tempPath = $data['featured_image'];
            $disk = Storage::disk('public');
            $fullPath = $disk->path($tempPath);

            $uploadedFile = new UploadedFile(
                $fullPath,
                basename($fullPath),
                mime_content_type($fullPath) ?: 'image/jpeg',
                null,
                true
            );

            /** @var ImageService $imageService */
            $imageService = app(ImageService::class);
            $result = $imageService->process($uploadedFile, 'blog');

            $disk->delete($tempPath);

            $data['featured_image_path_large'] = $result['path_large'];
            $data['featured_image_path_medium'] = $result['path_medium'];
            $data['featured_image_path_thumb'] = $result['path_thumb'];
            $data['featured_image_width'] = $result['width'];
            $data['featured_image_height'] = $result['height'];
        }

        unset($data['featured_image']);

        return $data;
    }
}
