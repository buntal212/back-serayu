<?php

namespace App\Helpers;

class Helper
{
    /**
     * Generate full URL for storage files
     *
     * @param string $path
     * @return string|null
     */
    public static function storageUrl($path)
    {
        if (empty($path)) {
            return null;
        }

        // If path already starts with http, return as is
        if (strpos($path, 'http') === 0) {
            return $path;
        }

        // Remove leading 'storage/' if present
        $cleanPath = ltrim($path, 'storage/');

        return asset('storage/' . $cleanPath);
    }

    /**
     * Generate API response with formatted data
     *
     * @param array $data
     * @return array
     */
    public static function formatUserData($data)
    {
        if (isset($data['foto'])) {
            $data['foto_url'] = self::storageUrl($data['foto']);
        }

        return $data;
    }
}