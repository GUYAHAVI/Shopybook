@extends('layouts.dash')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-brain"></i> AI Learning Dashboard</h4>
                    <p class="text-muted">Manually trigger AI learning processes for your business</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body">
                                    <h5><i class="fas fa-lightbulb"></i> Basic Learning</h5>
                                    <p>Gather basic knowledge and insights</p>
                                    <button class="btn btn-light" onclick="triggerLearning('basic')" id="basic-btn">
                                        <i class="fas fa-play"></i> Start Basic Learning
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body">
                                    <h5><i class="fas fa-chart-line"></i> Daily Learning</h5>
                                    <p>Comprehensive market analysis</p>
                                    <button class="btn btn-light" onclick="triggerLearning('daily')" id="daily-btn">
                                        <i class="fas fa-play"></i> Start Daily Learning
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white mb-3">
                                <div class="card-body">
                                    <h5><i class="fas fa-cogs"></i> Weekly Learning</h5>
                                    <p>Deep analysis and reports</p>
                                    <button class="btn btn-light" onclick="triggerLearning('weekly')" id="weekly-btn">
                                        <i class="fas fa-play"></i> Start Weekly Learning
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-chart-bar"></i> Learning Statistics</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="stat-card bg-info text-white p-3 rounded">
                                        <h6><i class="fas fa-database"></i> Knowledge Items</h6>
                                        <span class="stat-number h3">{{ $stats['knowledge_items'] ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card bg-success text-white p-3 rounded">
                                        <h6><i class="fas fa-fire"></i> Trending Topics</h6>
                                        <span class="stat-number h3">{{ $stats['trending_topics'] ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card bg-warning text-white p-3 rounded">
                                        <h6><i class="fas fa-lightbulb"></i> Business Insights</h6>
                                        <span class="stat-number h3">{{ $stats['business_insights'] ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card bg-secondary text-white p-3 rounded">
                                        <h6><i class="fas fa-clock"></i> Last Updated</h6>
                                        <span class="stat-date">{{ $stats['last_updated'] ? \Carbon\Carbon::parse($stats['last_updated'])->diffForHumans() : 'Never' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-info-circle"></i> Learning Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>What Each Learning Type Does:</h6>
                                            <ul>
                                                <li><strong>Basic Learning:</strong> Gathers fundamental market data and business insights</li>
                                                <li><strong>Daily Learning:</strong> Performs comprehensive market analysis and trend identification</li>
                                                <li><strong>Weekly Learning:</strong> Conducts deep analysis, generates reports, and cleans old data</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>When to Use:</h6>
                                            <ul>
                                                <li><strong>Basic:</strong> When you want quick insights</li>
                                                <li><strong>Daily:</strong> For comprehensive market understanding</li>
                                                <li><strong>Weekly:</strong> For detailed analysis and reporting</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function triggerLearning(type) {
    const button = document.getElementById(type + '-btn');
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    button.disabled = true;
    
    fetch('/learning/trigger', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Learning process started successfully!', 'success');
            
            // Update statistics after a short delay
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification('Error: ' + data.message, 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        showNotification('Error triggering learning: ' + error, 'error');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Add to sidebar navigation
document.addEventListener('DOMContentLoaded', function() {
    // Add learning dashboard to sidebar if it doesn't exist
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        const aiSection = sidebar.querySelector('.nav-section:has(.nav-link[href*="ai"])');
        if (aiSection) {
            const learningLink = document.createElement('div');
            learningLink.className = 'nav-item';
            learningLink.innerHTML = `
                <a href="{{ route('learning.dashboard') }}" class="nav-link">
                    <i class="fas fa-brain"></i>
                    <span>Learning Dashboard</span>
                </a>
            `;
            aiSection.appendChild(learningLink);
        }
    }
});
</script>

<style>
.stat-card {
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-number {
    font-weight: bold;
}

.stat-date {
    font-size: 0.9em;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn {
    transition: all 0.2s;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection

