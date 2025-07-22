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
                @if($action['type'] === 'delete-form')
                    <form action="{{ $action['url'] }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" 
                                class="btn {{ $action['class'] ?? 'btn-outline-danger' }}"
                                onclick="return confirm('{{ $action['confirm'] ?? 'Are you sure?' }}')">
                            @if(isset($action['icon']))
                                <i class="{{ $action['icon'] }} me-1"></i>
                            @endif
                            {{ $action['text'] }}
                        </button>
                    </form>
                @elseif($action['type'] === 'button')
                    <button type="button"
                            class="btn {{ $action['class'] ?? 'btn-outline-primary' }}"
                            @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif>
                        @if(isset($action['icon']))
                            <i class="{{ $action['icon'] }} me-1"></i>
                        @endif
                        {{ $action['text'] }}
                    </button>
                @else
                    <a href="{{ $action['url'] }}" 
                       class="btn {{ $action['class'] ?? 'btn-outline-primary' }}"
                       @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif>
                        @if(isset($action['icon']))
                            <i class="{{ $action['icon'] }} me-1"></i>
                        @endif
                        {{ $action['text'] }}
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</div>
