{{-- Sort controls --}}
<div class="sb-sort-controls mb-4">
    <div class="d-flex justify-content-end">
        <div class="btn-group">
            <button type="button" class="sb-sort-btn dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-sort"></i> Sort: {{ ucfirst($sort) }} ({{ $order }})
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="?sort=name&order=asc">Name (A-Z)</a></li>
                <li><a class="dropdown-item" href="?sort=name&order=desc">Name (Z-A)</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="?sort=created_at&order=desc">Newest First</a></li>
                <li><a class="dropdown-item" href="?sort=created_at&order=asc">Oldest First</a></li>
            </ul>
        </div>
    </div>
</div>

@if(count($groupedBusinesses) > 0)
    @foreach($groupedBusinesses as $businessType => $businesses)
        @if(count($businesses) > 0)
            <div class="mb-5">
                <h3 class="sb-type-header">
                    <i class="fas fa-building"></i> {{ $businessType }}
                </h3>
                <div class="row g-4">
                    @foreach($businesses as $business)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="sb-business-card">
                                <div class="card-img-wrap">
                                    <img src="{{ $business->logo_path ? asset('storage/'.$business->logo_path) : asset('img/default-business.png') }}"
                                         class="card-img-j"
                                         alt="{{ $business->name }}">
                                </div>
                                <div class="card-body-j">
                                    <h3>{{ $business->name }}</h3>
                                    <div class="sb-card-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $business->city }}, {{ $business->country }}</span>
                                    </div>
                                    <p>{{ Str::limit($business->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="sb-card-badge">{{ $business->business_category ?? 'Business' }}</span>
                                        <a href="{{ route('business.show', $business->slug) }}" class="btn1" style="padding:6px 14px; font-size:.8rem;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="sb-empty">
        <i class="fas fa-search fa-3x mb-3" style="color:#ff511a;"></i>
        <h4>No businesses found</h4>
        <p>There are currently no businesses to display. Check back later for new additions!</p>
    </div>
@endif
