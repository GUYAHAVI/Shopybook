<style>
/* Modern Business Cards Design */
.business-card {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    position: relative;
    height: 100%;
}

.business-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(2, 2, 88, 0.15);
}

.business-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #020258, #13e8e9);
    z-index: 2;
}

.card-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

.card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.business-card:hover .card-image {
    transform: scale(1.1);
}

.card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        transparent 0%,
        rgba(2, 2, 88, 0.1) 50%,
        rgba(2, 2, 88, 0.3) 100%
    );
    opacity: 0;
    transition: opacity 0.3s ease;
}

.business-card:hover .card-overlay {
    opacity: 1;
}

.card-body {
    padding: 1.5rem;
    position: relative;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #020258;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.card-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.card-location i {
    color: #13e8e9;
    font-size: 1rem;
}

.card-description {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.card-badge {
    background: linear-gradient(135deg, #020258, #13e8e9);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-button {
    background: linear-gradient(135deg, #020258, #13e8e9);
    border: none;
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(2, 2, 88, 0.2);
}

.card-button:hover {
    background: linear-gradient(135deg, #13e8e9, #020258);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(2, 2, 88, 0.3);
    color: white;
}

.business-type-header {
    margin: 3rem 0 2rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid;
    border-image: linear-gradient(90deg, #020258, #13e8e9) 1;
    color: #020258;
    font-weight: 700;
    font-size: 1.75rem;
    position: relative;
}

.business-type-header::before {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #020258, #13e8e9);
}

.business-type-header i {
    margin-right: 0.75rem;
    color: #13e8e9;
}

.sort-controls {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.sort-button {
    background: linear-gradient(135deg, #020258, #13e8e9);
    border: none;
    color: white;
    border-radius: 10px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.sort-button:hover {
    background: linear-gradient(135deg, #13e8e9, #020258);
    transform: translateY(-1px);
    color: white;
}

.empty-message {
    background: white;
    border-radius: 20px;
    padding: 3rem 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin: 2rem 0;
}

.empty-message h4 {
    color: #020258;
    font-weight: 700;
    margin-bottom: 1rem;
}

.empty-message p {
    color: #6c757d;
    font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .business-card {
        margin-bottom: 1.5rem;
    }
    
    .card-image-container {
        height: 180px;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .card-title {
        font-size: 1.1rem;
    }
    
    .business-type-header {
        font-size: 1.5rem;
        margin: 2rem 0 1.5rem;
    }
}

@media (max-width: 576px) {
    .card-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .card-button {
        width: 100%;
        text-align: center;
    }
}

/* Animation for cards appearing */
.business-card {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stagger animation for multiple cards */
.business-card:nth-child(1) { animation-delay: 0.1s; }
.business-card:nth-child(2) { animation-delay: 0.2s; }
.business-card:nth-child(3) { animation-delay: 0.3s; }
.business-card:nth-child(4) { animation-delay: 0.4s; }
</style>

<div class="sort-controls">
  <div class="d-flex justify-content-end">
    <div class="btn-group">
      <button type="button" class="btn sort-button dropdown-toggle" data-bs-toggle="dropdown">
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
      <div class="business-type-section">
        <h3 class="business-type-header">
          <i class="fas fa-building"></i> {{ $businessType }}
        </h3>
        <div class="row">
          @foreach($businesses as $business)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
              <div class="business-card">
                <div class="card-image-container">
                  <img src="{{ $business->logo_path ? asset('storage/'.$business->logo_path) : asset('img/default-business.png') }}" 
                       class="card-image" 
                       alt="{{ $business->name }}">
                  <div class="card-overlay"></div>
                </div>
                <div class="card-body">
                  <h5 class="card-title">{{ $business->name }}</h5>
                  <div class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $business->city }}, {{ $business->country }}</span>
                  </div>
                  <p class="card-description">{{ Str::limit($business->description, 100) }}</p>
                  <div class="card-footer">
                    <span class="card-badge">{{ $business->business_category ?? 'Business' }}</span>
                    <a href="{{ route('business.show', $business->slug) }}" 
                       class="card-button">
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
  <div class="empty-message">
    <i class="fas fa-search fa-3x mb-3" style="color: #13e8e9;"></i>
    <h4>No businesses found</h4>
    <p>There are currently no businesses to display. Check back later for new additions!</p>
  </div>
@endif