<?php

use App\Services\LocalizationService;

if (!function_exists('t')) {
    function t($key, $context = 'ui', $parameters = []) {
        $service = app(LocalizationService::class);
        return $service->translate($key, $context, $parameters);
    }
} 