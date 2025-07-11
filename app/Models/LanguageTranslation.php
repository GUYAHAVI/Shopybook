<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LanguageTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_code',
        'translation_key',
        'translation_value',
        'context',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getTranslation($key, $languageCode = 'en', $context = null)
    {
        $translation = static::where('translation_key', $key)
            ->where('language_code', $languageCode)
            ->where('is_active', true);

        if ($context) {
            $translation->where('context', $context);
        }

        $result = $translation->first();

        return $result ? $result->translation_value : $key;
    }

    public static function getTranslations($keys, $languageCode = 'en', $context = null)
    {
        $translations = static::whereIn('translation_key', $keys)
            ->where('language_code', $languageCode)
            ->where('is_active', true);

        if ($context) {
            $translations->where('context', $context);
        }

        return $translations->pluck('translation_value', 'translation_key')->toArray();
    }
} 