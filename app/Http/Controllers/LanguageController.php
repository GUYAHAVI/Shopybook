<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocalizationService;

class LanguageController extends Controller
{
    protected $localizationService;

    public function __construct(LocalizationService $localizationService)
    {
        $this->localizationService = $localizationService;
    }

    public function switch($language)
    {
        $allowedLanguages = ['en', 'sw', 'sheng'];
        
        if (in_array($language, $allowedLanguages)) {
            $this->localizationService->setLanguage($language);
            
            return redirect()->back()->with('success', 'Language changed successfully!');
        }
        
        return redirect()->back()->with('error', 'Invalid language selected.');
    }
} 