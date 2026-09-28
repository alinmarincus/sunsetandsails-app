<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/**
 * Orice imagine urcata in club trece pe aici: se redimensioneaza,
 * se converteste in WebP si se comprima. Originalul nu se pastreaza.
 *
 * Motivul e simplu: spatiul de pe server e comun cu Home Cooking Adventure,
 * iar 90% din vizitatori sunt pe telefon, deci greutatea paginii conteaza.
 */
class ImageProcessor
{
    /** Latimi maxime, pe tip de imagine. */
    public const AVATAR_SIZE = 512;
    public const COVER_WIDTH = 1600;
    public const PHOTO_WIDTH = 1600;

    /**
     * Salveaza o imagine convertita in WebP si intoarce calea relativa.
     *
     * @param  int|null  $square  daca e dat, imaginea e decupata patrat (avatar)
     */
    public static function store(
        UploadedFile|string $file,
        string $directory,
        int $maxWidth = self::COVER_WIDTH,
        int $quality = 82,
        ?int $square = null,
    ): string {
        $image = ImageManager::usingDriver(new GdDriver())->decodePath(
            $file instanceof UploadedFile ? $file->getRealPath() : $file
        );

        if ($square) {
            // Decupaj din centru, fara sa deformeze fata omului
            $image->cover($square, $square);
        } else {
            // Doar micsoreaza; o imagine deja mica ramane cum e
            $image->scaleDown(width: $maxWidth);
        }

        $path = rtrim($directory, '/') . '/' . Str::lower(Str::random(24)) . '.webp';

        Storage::disk('public')->put(
            $path,
            (string) $image->encode(new WebpEncoder(quality: $quality))
        );

        return $path;
    }

    /** Avatar: patrat, mic, bun pentru afisare la 88px sau 34px. */
    public static function storeAvatar(UploadedFile|string $file): string
    {
        return self::store($file, 'avatars', square: self::AVATAR_SIZE, quality: 85);
    }

    /** Coperta unei croaziere: lata, dar comprimata. */
    public static function storeCover(UploadedFile|string $file): string
    {
        return self::store($file, 'trips', maxWidth: self::COVER_WIDTH, quality: 80);
    }

    /** Sterge o imagine, daca exista. */
    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
