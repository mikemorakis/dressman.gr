<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /** @var array<string, int> */
    private const SIZES = [
        'large' => 1200,
        'medium' => 600,
        'thumb' => 150,
    ];

    private const QUALITY = 80;

    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

    /** Max source dimensions to avoid memory issues on 2GB cPanel (4000×4000 ≈ 64 MB RAM for GD) */
    private const MAX_DIMENSION = 4000;

    /** Max images per batch upload to avoid sync timeout */
    public const MAX_BATCH = 10;

    /** @var list<string> */
    private const BASE_MIMES = ['image/jpeg', 'image/png'];

    /**
     * Process an uploaded image: validate, resize to 3 sizes, store on public disk.
     *
     * @param  string  $directory  Storage subdirectory (e.g. 'products', 'categories')
     * @return array{path_large: string, path_medium: string, path_thumb: string, width: int, height: int}
     *
     * @throws \InvalidArgumentException
     */
    public function process(UploadedFile $file, string $directory = 'products'): array
    {
        $this->validate($file);

        $baseName = Str::ulid()->toBase32();
        $extension = $this->resolveExtension($file);

        $image = Image::read($file->getPathname());

        // Capture original dimensions (before any resize) for the large variant
        $largeImage = $this->resize(clone $image, self::SIZES['large']);
        $width = $largeImage->width();
        $height = $largeImage->height();

        $paths = [];

        foreach (self::SIZES as $sizeName => $maxWidth) {
            $resized = ($sizeName === 'large') ? $largeImage : $this->resize(clone $image, $maxWidth);
            $fileName = "{$baseName}_{$sizeName}.{$extension}";
            $storagePath = "{$directory}/{$fileName}";

            Storage::disk('public')->put(
                $storagePath,
                $resized->encodeByExtension($extension, quality: self::QUALITY)->toString()
            );

            $paths["path_{$sizeName}"] = $storagePath;
        }

        $paths['width'] = $width;
        $paths['height'] = $height;

        return $paths;
    }

    /**
     * Delete all 3 sizes of a product image from storage.
     *
     * @param  array{path_large: string, path_medium: string, path_thumb: string}  $paths
     */
    public function delete(array $paths): void
    {
        $disk = Storage::disk('public');

        foreach (['path_large', 'path_medium', 'path_thumb'] as $key) {
            $path = $paths[$key];
            if ($path && $disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    /**
     * Check if the server's GD extension supports WebP.
     */
    public static function supportsWebP(): bool
    {
        if (! function_exists('gd_info')) {
            return false;
        }

        $info = gd_info();

        return ! empty($info['WebP Support']);
    }

    /**
     * Allowed MIME types (WebP only if GD supports it).
     *
     * @return list<string>
     */
    public function allowedMimes(): array
    {
        $mimes = self::BASE_MIMES;

        if (self::supportsWebP()) {
            $mimes[] = 'image/webp';
        }

        return $mimes;
    }

    /**
     * Validate file type and size.
     *
     * @throws \InvalidArgumentException
     */
    private function validate(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), $this->allowedMimes(), true)) {
            $allowed = self::supportsWebP() ? 'JPEG, PNG, WebP' : 'JPEG, PNG';

            throw new \InvalidArgumentException(
                "Invalid image type. Allowed: {$allowed}."
            );
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException(
                'Image file too large. Maximum: 5 MB.'
            );
        }

        // Check dimensions to avoid memory exhaustion on shared hosting
        $imageSize = @getimagesize($file->getPathname());
        if ($imageSize !== false) {
            [$w, $h] = $imageSize;
            if ($w > self::MAX_DIMENSION || $h > self::MAX_DIMENSION) {
                throw new \InvalidArgumentException(
                    'Image dimensions too large. Maximum: '.self::MAX_DIMENSION.'×'.self::MAX_DIMENSION.'px.'
                );
            }
        }
    }

    private function resize(ImageInterface $image, int $maxWidth): ImageInterface
    {
        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        return $image;
    }

    private function resolveExtension(UploadedFile $file): string
    {
        return match ($file->getMimeType()) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }
}
