@php
    $currentLanguage = session('language', 'en');
    $languages = [
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
@endphp

<div class="dropdown me-3">
    <a class="d-flex align-items-center text-decoration-none" href="#" role="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="me-2">{{ $languages[$currentLanguage]['flag'] }}</span>
        <span class="d-none d-md-inline">{{ $languages[$currentLanguage]['name'] }}</span>
        <i class="fas fa-chevron-down ms-1"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li><h6 class="dropdown-header">{{ t('select_language') }}</h6></li>
        @foreach($languages as $code => $language)
            <li>
                <a class="dropdown-item d-flex align-items-center {{ $code === $currentLanguage ? 'active' : '' }}" 
                   href="{{ route('language.switch', $code) }}">
                    <span class="me-2">{{ $language['flag'] }}</span>
                    <div>
                        <div class="fw-bold">{{ $language['name'] }}</div>
                        <small class="text-muted">{{ $language['native_name'] }}</small>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
</div> 