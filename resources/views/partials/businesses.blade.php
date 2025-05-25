<style>
.cardx {
  width: 100%;
  height: 100%;
  border-radius: 10px;
  overflow: hidden;
  z-index: 10;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1), 0 0 15px rgba(19, 232, 233, 0.3);
  transition: transform 0.3s ease;
}
.cardx:hover {
  transform: rotateY(20deg);
}
.business-type-header {
  margin: 2rem 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #13e8e9;
  color: #2c3e50;
  font-weight: 600;
}
.empty-message {
  padding: 2rem;
  text-align: center;
  color: #7f8c8d;
}
</style>

<div class="mb-3 text-end">
  <div class="btn-group">
    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
      <i class="bx bx-sort"></i> Sort: {{ ucfirst($sort) }} ({{ $order }})
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

@if(count($groupedBusinesses) > 0)
  @foreach($groupedBusinesses as $businessType => $businesses)
    @if(count($businesses) > 0)
      <div class="business-type-section">
        <h3 class="business-type-header">
          <i class="bx bx-category"></i> {{ $businessType }}
        </h3>
        <div class="row">
          @foreach($businesses as $business)
            <div class="col-md-3 mb-4">
              <div class="cardx">
                <div class="card-img">
                  <img src="{{ $business->logo_path ? asset('storage/'.$business->logo_path) : asset('img/default-business.png') }}" 
                       class="card-img-top" 
                       alt="{{ $business->name }}"
                       style="height: 180px; object-fit: cover;">
                </div>
                <div class="card-body py-4 px-4">
                  <h5 class="card-title">{{ $business->name }}</h5>
                  <p class="card-text">
                    <small>
                      <i class="bx bx-map"></i> {{ $business->city }}, {{ $business->country }}
                    </small>
                  </p>
                  <p class="card-text">{{ Str::limit($business->description, 80) }}</p>
                  <a href="{{ route('businesses', $business->id) }}" 
                     class="btn btn-primary btn-sm">
                    <i class="bx bx-show"></i> View
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  @endforeach
@else
  <div class="empty-message">
    <h4>No businesses found</h4>
    <p>There are currently no businesses to display.</p>
  </div>
@endif