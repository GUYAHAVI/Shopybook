@extends('layouts.app')

@section('title', 'Knowledge Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-brain text-primary"></i>
                        Continuous Knowledge System
                    </h4>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" id="startLearning">
                            <i class="fas fa-play"></i> Start Learning
                        </button>
                        <button type="button" class="btn btn-danger" id="stopLearning">
                            <i class="fas fa-stop"></i> Stop Learning
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- System Status -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>System Status</h5>
                                    <div id="systemStatus" class="mt-2">
                                        <span class="badge bg-warning">Checking...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>Knowledge Items</h5>
                                    <div id="knowledgeCount" class="mt-2">
                                        <span class="badge bg-light text-dark">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>Today's Updates</h5>
                                    <div id="todayUpdates" class="mt-2">
                                        <span class="badge bg-light text-dark">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>Last Update</h5>
                                    <div id="lastUpdate" class="mt-2">
                                        <span class="badge bg-light text-dark">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trending Topics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-trending-up text-primary"></i> Trending Topics</h5>
                                </div>
                                <div class="card-body">
                                    <div id="trendingTopics">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Knowledge by Type -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie text-primary"></i> Knowledge by Type</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="knowledgeChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-list text-primary"></i> Latest Knowledge</h5>
                                </div>
                                <div class="card-body">
                                    <div id="latestKnowledge" style="max-height: 300px; overflow-y: auto;">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Knowledge Search -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-search text-primary"></i> Search Knowledge</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="searchQuery" placeholder="Search knowledge...">
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" id="dataTypeFilter">
                                                <option value="">All Types</option>
                                                <option value="news">News</option>
                                                <option value="social_media">Social Media</option>
                                                <option value="market_data">Market Data</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary" id="searchKnowledge">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-secondary" id="exportKnowledge">
                                                <i class="fas fa-download"></i> Export
                                            </button>
                                        </div>
                                    </div>
                                    <div id="searchResults" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Knowledge Item Modal -->
<div class="modal fade" id="knowledgeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Knowledge Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="knowledgeModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let knowledgeChart;

    // Initialize dashboard
    loadDashboard();

    // Start learning system
    $('#startLearning').click(function() {
        $.ajax({
            url: '{{ route("knowledge.start-learning") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    loadDashboard();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                showAlert('error', 'Error starting learning system');
            }
        });
    });

    // Stop learning system
    $('#stopLearning').click(function() {
        $.ajax({
            url: '{{ route("knowledge.stop-learning") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    loadDashboard();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                showAlert('error', 'Error stopping learning system');
            }
        });
    });

    // Search knowledge
    $('#searchKnowledge').click(function() {
        const query = $('#searchQuery').val();
        const dataType = $('#dataTypeFilter').val();
        
        if (!query) {
            showAlert('error', 'Please enter a search query');
            return;
        }

        $.ajax({
            url: '{{ route("knowledge.search") }}',
            method: 'GET',
            data: {
                query: query,
                data_type: dataType,
                limit: 20
            },
            success: function(response) {
                if (response.success) {
                    displaySearchResults(response.data);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                showAlert('error', 'Error searching knowledge');
            }
        });
    });

    // Export knowledge
    $('#exportKnowledge').click(function() {
        const dataType = $('#dataTypeFilter').val();
        window.open(`{{ route("knowledge.export") }}?data_type=${dataType}&format=csv`, '_blank');
    });

    function loadDashboard() {
        // Load system status
        loadSystemStatus();
        
        // Load trending topics
        loadTrendingTopics();
        
        // Load knowledge stats
        loadKnowledgeStats();
        
        // Load latest knowledge
        loadLatestKnowledge();
    }

    function loadSystemStatus() {
        $.ajax({
            url: '{{ route("knowledge.system-status") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const status = response.data;
                    const statusBadge = status.is_running ? 
                        '<span class="badge bg-success">Running</span>' : 
                        '<span class="badge bg-danger">Stopped</span>';
                    
                    $('#systemStatus').html(statusBadge);
                    $('#todayUpdates').html(`<span class="badge bg-light text-dark">${status.knowledge_items_today || 0}</span>`);
                    $('#lastUpdate').html(`<span class="badge bg-light text-dark">${status.last_check || 'Unknown'}</span>`);
                }
            }
        });
    }

    function loadTrendingTopics() {
        $.ajax({
            url: '{{ route("knowledge.trending-topics") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    displayTrendingTopics(response.data);
                } else {
                    $('#trendingTopics').html('<p class="text-muted">No trending topics available</p>');
                }
            }
        });
    }

    function loadKnowledgeStats() {
        $.ajax({
            url: '{{ route("knowledge.stats") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const stats = response.data;
                    $('#knowledgeCount').html(`<span class="badge bg-light text-dark">${stats.total_knowledge_items || 0}</span>`);
                    
                    // Create chart
                    createKnowledgeChart(stats.knowledge_by_type || []);
                }
            }
        });
    }

    function loadLatestKnowledge() {
        $.ajax({
            url: '{{ route("knowledge.latest") }}',
            method: 'GET',
            data: { limit: 10 },
            success: function(response) {
                if (response.success) {
                    displayLatestKnowledge(response.data);
                }
            }
        });
    }

    function displayTrendingTopics(data) {
        const analysis = data.analysis;
        let html = '<div class="row">';
        
        if (analysis.trending_keywords && analysis.trending_keywords.length > 0) {
            analysis.trending_keywords.slice(0, 10).forEach((item, index) => {
                html += `
                    <div class="col-md-3 mb-2">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">${item[0]}</h6>
                                <span class="badge bg-primary">${item[1]} mentions</span>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<p class="text-muted">No trending topics available</p>';
        }
        
        html += '</div>';
        $('#trendingTopics').html(html);
    }

    function createKnowledgeChart(knowledgeByType) {
        const ctx = document.getElementById('knowledgeChart').getContext('2d');
        
        if (knowledgeChart) {
            knowledgeChart.destroy();
        }

        const labels = knowledgeByType.map(item => item.data_type);
        const data = knowledgeByType.map(item => item.count);

        knowledgeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    function displayLatestKnowledge(data) {
        let html = '';
        
        data.forEach(item => {
            const knowledge = item.data;
            const sentimentClass = knowledge.sentiment === 'positive' ? 'text-success' : 
                                 knowledge.sentiment === 'negative' ? 'text-danger' : 'text-muted';
            
            html += `
                <div class="card mb-2 knowledge-item" data-knowledge='${JSON.stringify(item)}'>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">${knowledge.title || 'No Title'}</h6>
                                <p class="card-text small text-muted mb-1">${knowledge.description || 'No description'}</p>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">${item.data_type}</span>
                                    <span class="badge bg-info me-2">${knowledge.source || 'Unknown'}</span>
                                    <span class="badge ${sentimentClass}">${knowledge.sentiment || 'neutral'}</span>
                                </div>
                            </div>
                            <small class="text-muted">${formatDate(item.created_at)}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#latestKnowledge').html(html);
        
        // Add click handlers
        $('.knowledge-item').click(function() {
            const knowledge = $(this).data('knowledge');
            showKnowledgeModal(knowledge);
        });
    }

    function displaySearchResults(data) {
        let html = '<h6>Search Results:</h6>';
        
        if (data.length === 0) {
            html += '<p class="text-muted">No results found</p>';
        } else {
            data.forEach(item => {
                const knowledge = item.data;
                html += `
                    <div class="card mb-2 knowledge-item" data-knowledge='${JSON.stringify(item)}'>
                        <div class="card-body">
                            <h6 class="card-title">${knowledge.title || 'No Title'}</h6>
                            <p class="card-text small">${knowledge.description || 'No description'}</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">${item.data_type}</span>
                                <span class="badge bg-info me-2">${knowledge.source || 'Unknown'}</span>
                                <small class="text-muted">${formatDate(item.created_at)}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        
        $('#searchResults').html(html);
        
        // Add click handlers
        $('.knowledge-item').click(function() {
            const knowledge = $(this).data('knowledge');
            showKnowledgeModal(knowledge);
        });
    }

    function showKnowledgeModal(knowledge) {
        const data = knowledge.data;
        let html = `
            <div class="row">
                <div class="col-12">
                    <h5>${data.title || 'No Title'}</h5>
                    <p class="text-muted">${data.description || 'No description'}</p>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Source:</strong> ${data.source || 'Unknown'}
                        </div>
                        <div class="col-md-6">
                            <strong>Type:</strong> ${knowledge.data_type}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Sentiment:</strong> 
                            <span class="badge ${data.sentiment === 'positive' ? 'bg-success' : 
                                               data.sentiment === 'negative' ? 'bg-danger' : 'bg-secondary'}">
                                ${data.sentiment || 'neutral'}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Created:</strong> ${formatDate(knowledge.created_at)}
                        </div>
                    </div>
        `;
        
        if (data.content) {
            html += `
                <div class="row">
                    <div class="col-12">
                        <strong>Content:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            ${data.content}
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (data.url) {
            html += `
                <div class="row mt-3">
                    <div class="col-12">
                        <a href="${data.url}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> View Source
                        </a>
                    </div>
                </div>
            `;
        }
        
        html += '</div>';
        
        $('#knowledgeModalBody').html(html);
        $('#knowledgeModal').modal('show');
    }

    function formatDate(dateString) {
        if (!dateString) return 'Unknown';
        return new Date(dateString).toLocaleString();
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Auto-refresh dashboard every 30 seconds
    setInterval(loadDashboard, 30000);
});
</script>
@endpush

@push('styles')
<style>
.knowledge-item {
    cursor: pointer;
    transition: all 0.2s ease;
}

.knowledge-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush
