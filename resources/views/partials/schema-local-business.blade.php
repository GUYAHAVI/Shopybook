{{-- schema.org LocalBusiness JSON-LD for a Shopybook business. Expects: $business, optional $url --}}
@php
    $schemaTypes = [
        'retail' => 'Store', 'online' => 'OnlineStore', 'fashion' => 'ClothingStore', 'electronics' => 'ElectronicsStore',
        'grocery' => 'GroceryStore', 'beauty' => 'HealthAndBeautyBusiness', 'wholesale' => 'WholesaleStore',
        'consulting' => 'ProfessionalService', 'beauty_service' => 'BeautySalon', 'repair' => 'HomeAndConstructionBusiness',
        'cleaning' => 'ProfessionalService', 'education' => 'EducationalOrganization', 'healthcare' => 'MedicalBusiness',
        'professional' => 'ProfessionalService', 'restaurant' => 'Restaurant', 'salon' => 'BeautySalon',
        'auto_service' => 'AutoRepair', 'tech_service' => 'ElectronicsStore',
        'residential' => 'RealEstateAgent', 'commercial' => 'RealEstateAgent', 'hostels' => 'Hostel',
        'short_stay' => 'LodgingBusiness', 'other_rental' => 'RealEstateAgent',
    ];
    $categoryFallback = ['product' => 'Store', 'rentals' => 'RealEstateAgent'];
    $schemaType = $schemaTypes[$business->business_type] ?? $categoryFallback[$business->business_category] ?? 'LocalBusiness';

    $phone = $business->phone ? preg_replace('/\D/', '', $business->phone) : null;
    if ($phone && str_starts_with($phone, '0')) { $phone = '254' . substr($phone, 1); }

    $schema = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => $schemaType,
        '@id'         => ($url ?? route('business.show', $business->slug)) . '#business',
        'name'        => $business->name,
        'description' => $business->description ? \Illuminate\Support\Str::limit(strip_tags($business->description), 300) : null,
        'url'         => $url ?? route('business.show', $business->slug),
        'logo'        => $business->logo_path ? asset('storage/' . $business->logo_path) : null,
        'image'       => $business->cover_path ? asset('storage/' . $business->cover_path)
                            : ($business->logo_path ? asset('storage/' . $business->logo_path) : null),
        'telephone'   => $phone ? '+' . $phone : null,
        'email'       => $business->email,
        'address'     => array_filter([
            '@type'           => 'PostalAddress',
            'streetAddress'   => $business->address,
            'addressLocality' => $business->city,
            'addressCountry'  => 'KE',
        ]),
        'areaServed'  => $business->city ?: 'Kenya',
        'sameAs'      => array_values(array_filter([
            $business->facebook_url, $business->instagram_url, $business->twitter_url,
            $business->linkedin_url, $business->youtube_url,
        ])) ?: null,
    ], fn($v) => $v !== null && $v !== '' && $v !== []);
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
