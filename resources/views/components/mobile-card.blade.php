@props(['title', 'badge' => null, 'fields' => [], 'actions' => []])

<div class="mobile-card-item">
    <!-- Card Header -->
    <div class="mobile-card-header">
        <h6 class="mobile-card-title">{{ $title }}</h6>
        @if($badge)
            <span class="badge mobile-card-badge {{ $badge['class'] ?? 'bg-secondary' }}">
                {{ $badge['text'] }}
            </span>
        @endif
    </div>
    
    <!-- Card Content -->
    @if(count($fields) > 0)
        <div class="mobile-card-content">
            @foreach($fields as $field)
                <div class="mobile-card-field">
                    <label>{{ $field['label'] }}</label>
                    <span>{!! $field['value'] !!}</span>
                </div>
            @endforeach
        </div>
    @endif
    
    <!-- Custom Content Slot -->
    {{ $slot }}
    
    <!-- Actions -->
    @if(count($actions) > 0)
        <div class="mobile-card-actions">
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" 
                   class="btn {{ $action['class'] ?? 'btn-outline-primary' }}"
                   @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif>
                    @if(isset($action['icon']))
                        <i class="{{ $action['icon'] }} me-1"></i>
                    @endif
                    {{ $action['text'] }}
                </a>
            @endforeach
        </div>
    @endif
</div>
