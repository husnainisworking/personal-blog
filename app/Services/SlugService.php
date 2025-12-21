<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SlugService
{
    /**
     * Generate a unique slug with retry logic and database locking.
     */
    public static function generateUniqueSlug(

        string $title,
        string $model,
        ?int $excludeId = null,
        int $maxAttempts = 10
    ): string {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $attempt = 1;

        // Use database transaction with pessimistic locking
        return DB::transaction(function () use ($slug, $baseSlug, $model, $excludeId, $maxAttempts, &$attempt) {
            /**
             * This closures capture the variables by reference to allow modification within the transaction.
             */
            while ($attempt <= $maxAttempts) {
                // Check if slug exists with pessimistic lock (prevents race condition)
                $query = $model::lockForUpdate()->where('slug', $slug);

                if ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                }

                $exists = $query->exists();

                if (! $exists) {
                    return $slug;
                }

                // Slug exists, increment attempt and modify slug
                $slug = $baseSlug.'-'.$attempt;
                $attempt++;
            }

            // If we've exhausted attempts, add random string to ensure uniqueness
            return $baseSlug.'-'.Str::random(8);
        });

    }

    /**
     * Generate slug with automatic retry on duplicate key error
     *
     * This is a fallback method that catches database unique constraint violations
     */
    public static function generateWithRetry(

        string $title,
        string $model,
        ?int $excludeId = null,
        ?callable $saveCallback = null
    ): string {
        $maxRetries = 5;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $slug = self::generateUniqueSlug($title, $model, $excludeId);

                // If a save callback is provided, use it to save the model with the generated slug
                if ($saveCallback) {
                    $saveCallback($slug);
                }

                return $slug;
            } catch (QueryException $e) {
                // Check if it's a duplicate key error (error code 23000)
                if ($e->getCode() === 23000 && Str::contains($e->getMessage(), 'slug')) {
                    $attempt++;

                    if ($attempt >= $maxRetries) {
                        throw new \RunTimeException(
                            "Failed to generate a unique slug after {$maxRetries} attempts"
                        );
                    }

                    // Add random string and retry
                    $title .= '-'.Str::random(4);

                    continue;
                }

                // If it's a different error, rethrow it
                throw $e;
            }
        }
        throw new \RunTimeException('Failed to generate unique slug');
    }

    /**
     * Update slug atomically (for existing records)
     */
    public static function updateSlug(
        $model,
        string $newTitle,
        string $modelClass
    ): string {
        return DB::transaction(function () use ($model, $newTitle, $modelClass) {
            // Lock the record for update
            $locked = $modelClass::lockForUpdate()->find($model->id);

            if (! $locked) {
                throw new \RuntimeException('Model not found');
            }

            // Generate a new unique slug
            $newSlug = self::generateUniqueSlug($newTitle, $modelClass, $model->id);

            return $newSlug;
        });
    }

    /**
     * Batch check if slugs exist (useful for bulk operations
     */
    public static function slugsExist(array $slugs, string $model): array
    {
        return $model::whereIn('slug', $slugs)
            ->pluck('slug')
            ->toArray();
    }

    /**
     * Generate slug with custom separator
     */
    public static function generateWithSeparator(

        string $title,
        string $separator = '-'
    ): string {
        return Str::slug($title, $separator);
    }
}
