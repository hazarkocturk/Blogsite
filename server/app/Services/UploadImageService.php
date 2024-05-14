<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadImageService
{
    private const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'tiff', 'psd', 'pdf', 'eps', 'ai', 'indd', 'raw'
    ];

    public function uploadMultipleImages(array $images, string $folder = 'uploads'): array
    {
        $uploadedImages = [];

        foreach ($images as $image) {
            $uploadedImages[] = $this->uploadImage($image, $folder);
        }

        return $uploadedImages;
    }

    private function uploadImage(object $image, string $folder): array
    {
        $validationErrors = $this->validateExtension($image);
        if (!empty($validationErrors)) {
            return ['errors' => $validationErrors];
        }

        $filename = $this->generateFilename($image);
        $extension = $image->getClientOriginalExtension();
        $path = $folder . '/';
        $uploadPath = $path . $filename;

        $this->createFolder($path);

        if (in_array($extension, ['pdf', 'svg'])) {
            $this->saveOriginalImage($image, $uploadPath . '.' . $extension);
            $newFilename = $filename . '.' . $extension;
        } else {
            $this->resizeAndSaveWebp($image, $uploadPath);
            $newFilename = $filename . '.webp';
        }

        return [
            'name' => $newFilename,
            'path' => $uploadPath,
            'url' => asset($uploadPath . '/' . $newFilename)
        ];
    }

    private function validateExtension(object $image): array
    {
        $extension = $image->getClientOriginalExtension();
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return ['message' => 'Invalid file extension'];
        }
        return [];
    }

    private function generateFilename(object $image): string
    {
        return time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
    }

    private function saveOriginalImage(object $image, string $path): void
    {
        Storage::put($path, file_get_contents($image));
    }

    private function resizeAndSaveWebp(object $image, string $path): void
    {
        try {
            $resizedImage = Image::make($image);
            $resizedImage->encode('webp', 75)->save($path . '.webp');
        } catch (\Exception $e) {
            throw new \Exception("Error Processing Request: " . $e->getMessage());
        }
    }

    public function deleteFile(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    private function createFolder(string $directoryPath, int $permission = 0777): void
    {
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, $permission, true);
        }
    }
}
