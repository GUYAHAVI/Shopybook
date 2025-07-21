@props(['mobileCards' => []])

<div class="table-mobile-cards">
    <!-- Desktop Table -->
    <div class="table-responsive">
        {{ $table }}
    </div>
    
    <!-- Mobile Cards -->
    <div class="mobile-cards-container d-md-none">
        @if(count($mobileCards) > 0)
            @foreach($mobileCards as $card)
                <x-mobile-card 
                    :title="$card['title']"
                    :badge="$card['badge'] ?? null"
                    :fields="$card['fields'] ?? []"
                    :actions="$card['actions'] ?? []">
                    {!! $card['content'] ?? '' !!}
                </x-mobile-card>
            @endforeach
        @else
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No items found</p>
            </div>
        @endif
    </div>
</div>
