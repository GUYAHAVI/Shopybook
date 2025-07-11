<?php

namespace App\Services;

use App\Models\LanguageTranslation;
use Illuminate\Support\Facades\Cache;

class LocalizationService
{
    protected $currentLanguage = 'en';
    protected $translations = [];

    public function __construct()
    {
        $this->currentLanguage = session('language', 'en');
        $this->loadTranslations();
    }

    public function setLanguage($languageCode)
    {
        $this->currentLanguage = $languageCode;
        session(['language' => $languageCode]);
        $this->loadTranslations();
    }

    public function getLanguage()
    {
        return $this->currentLanguage;
    }

    public function translate($key, $context = null, $parameters = [])
    {
        $translation = $this->translations[$key] ?? $key;
        
        // Replace parameters in translation
        foreach ($parameters as $param => $value) {
            $translation = str_replace(":{$param}", $value, $translation);
        }
        
        return $translation;
    }

    public function translateAI($text, $targetLanguage = null)
    {
        $targetLanguage = $targetLanguage ?? $this->currentLanguage;
        
        // For now, return the text as-is
        // Later, this will integrate with translation API or custom model
        return $text;
    }

    protected function loadTranslations()
    {
        $cacheKey = "translations_{$this->currentLanguage}";
        
        $this->translations = Cache::remember($cacheKey, 3600, function () {
            return LanguageTranslation::where('language_code', $this->currentLanguage)
                ->where('is_active', true)
                ->pluck('translation_value', 'translation_key')
                ->toArray();
        });
    }

    public function getAvailableLanguages()
    {
        return [
            'en' => [
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇺🇸'
            ],
            'sw' => [
                'name' => 'Swahili',
                'native_name' => 'Kiswahili',
                'flag' => '🇹🇿'
            ],
            'sheng' => [
                'name' => 'Sheng',
                'native_name' => 'Sheng',
                'flag' => '🇰🇪'
            ]
        ];
    }

    public function getLanguageName($code)
    {
        $languages = $this->getAvailableLanguages();
        return $languages[$code]['name'] ?? $code;
    }

    public function getNativeLanguageName($code)
    {
        $languages = $this->getAvailableLanguages();
        return $languages[$code]['native_name'] ?? $code;
    }
} 