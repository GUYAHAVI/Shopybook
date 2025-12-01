@extends('layouts.app')

@section('title', 'KENADA Business Assistant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Chat Interface -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>
                        Claude Business Assistant
                    </h5>
                    <small class="text-light">Powered by Claude Sonnet 4 AI - Your Intelligent Business Advisor</small>
                </div>
                <div class="card-body p-0">
                    <!-- Chat Messages -->
                    <div id="chat-messages" class="chat-container" style="height: 500px; overflow-y: auto; padding: 20px;">
                        <!-- Messages will be loaded here -->
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="chat-input-container p-3 border-top">
                        <div class="mb-2 d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted">Chatting about:</small>
                                <strong class="ms-2 text-primary">{{ $business->name }}</strong>
                            </div>
                            <input type="hidden" id="business-id" value="{{ $business->id }}">
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" id="message-input" class="form-control" placeholder="Ask Claude about your business..." maxlength="1000">
                            <button class="btn btn-primary" type="button" id="send-button">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        
                        <!-- Quick Suggestions -->
                        <div id="suggestions" class="mt-3">
                            <small class="text-muted">Quick questions:</small>
                            <div class="suggestion-buttons mt-1">
                                <button class="btn btn-sm btn-outline-primary suggestion-btn" data-message="What are my top selling products?">Top Products</button>
                                <button class="btn btn-sm btn-outline-primary suggestion-btn" data-message="Show me my total expenses including salaries and costs">Total Expenses</button>
                                <button class="btn btn-sm btn-outline-success suggestion-btn" data-message="What is my profit margin?">Profit Analysis</button>
                                <button class="btn btn-sm btn-outline-info suggestion-btn" data-message="How much am I spending on employees and staff?">Labor Costs</button>
                                <button class="btn btn-sm btn-outline-warning suggestion-btn" data-message="What items are low in stock?">Inventory Alert</button>
                                <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-message="Which products need price adjustments?">Pricing Strategy</button>
                                <button class="btn btn-sm btn-outline-danger suggestion-btn" data-message="Suggest better suppliers for my products">Find Suppliers</button>
                                <button class="btn btn-sm btn-outline-dark suggestion-btn" data-message="How can I reduce my operating costs?">Cut Costs</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- AI Status & Insights -->
        <div class="col-md-4">
            <!-- AI Status -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>AI Status</h6>
                </div>
                <div class="card-body">
                    <div id="ai-status">
                        <div class="d-flex align-items-center mb-2">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span>Checking AI system...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Insights -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Quick Insights</h6>
                </div>
                <div class="card-body">
                    <div id="quick-insights">
                        <p class="text-muted">Select a business to see insights</p>
                    </div>
                </div>
            </div>
            
            <!-- Conversation History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Conversation History</h6>
                    <button class="btn btn-sm btn-outline-danger" id="clear-history">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="conversation-history">
                        <p class="text-muted">No conversation history yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">AI is thinking...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.chat-container {
    background-color: #f8f9fa;
}

.message {
    margin-bottom: 15px;
    display: flex;
}

.message.user {
    justify-content: flex-end;
}

.message.ai {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 15px;
    word-wrap: break-word;
}

.message.user .message-content {
    background-color: #007bff;
    color: white;
}

.message.ai .message-content {
    background-color: white;
    border: 1px solid #dee2e6;
}

.message-time {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 5px;
}

.suggestion-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.suggestion-btn {
    font-size: 0.8rem;
}

.chat-input-container {
    background-color: white;
}

#message-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.status-online {
    background-color: #28a745;
}

.status-offline {
    background-color: #dc3545;
}

.insight-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
}

.insight-value {
    font-size: 1.5rem;
    font-weight: bold;
}

.insight-label {
    font-size: 0.9rem;
    opacity: 0.9;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let conversationHistory = [];
    
    // Initialize
    loadAIStatus();
    loadConversationHistory();
    
    // Auto-load insights for user's business
    const businessId = $('#business-id').val();
    if (businessId) {
        loadQuickInsights(businessId);
    }
    
    // Send message
    $('#send-button').click(sendMessage);
    $('#message-input').keypress(function(e) {
        if (e.which == 13) {
            sendMessage();
        }
    });
    
    // Suggestion buttons
    $('.suggestion-btn').click(function() {
        const message = $(this).data('message');
        $('#message-input').val(message);
        sendMessage();
    });
    
    // Clear history
    $('#clear-history').click(clearHistory);
    
    function sendMessage() {
        const message = $('#message-input').val().trim();
        
        if (!message) return;
        
        // Add user message to chat
        addMessage(message, 'user');
        $('#message-input').val('');
        
        // Show loading
        $('#loadingModal').modal('show');
        
        // Send to AI
        $.ajax({
            url: '{{ route("ai-comm.process-message") }}',
            method: 'POST',
            data: {
                message: message,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loadingModal').modal('hide');
                
                if (response.success) {
                    addMessage(response.response, 'ai');
                    
                    // Update suggestions
                    updateSuggestions(response.suggestions);
                    
                    // Reload conversation history
                    loadConversationHistory();
                } else {
                    addMessage('Sorry, I encountered an error. Please try again.', 'ai');
                }
            },
            error: function() {
                $('#loadingModal').modal('hide');
                addMessage('Sorry, I\'m having trouble connecting. Please try again.', 'ai');
            }
        });
    }
    
    function addMessage(text, type) {
        const messageHtml = `
            <div class="message ${type}">
                <div class="message-content">
                    ${text}
                    <div class="message-time">${new Date().toLocaleTimeString()}</div>
                </div>
            </div>
        `;
        
        $('#chat-messages').append(messageHtml);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }
    
    function updateSuggestions(suggestions) {
        if (suggestions && suggestions.length > 0) {
            let buttonsHtml = '';
            suggestions.forEach(suggestion => {
                buttonsHtml += `<button class="btn btn-sm btn-outline-secondary suggestion-btn" data-message="${suggestion}">${suggestion}</button>`;
            });
            $('.suggestion-buttons').html(buttonsHtml);
            
            // Reattach click handlers
            $('.suggestion-btn').click(function() {
                const message = $(this).data('message');
                $('#message-input').val(message);
                sendMessage();
            });
        }
    }
    
    function loadAIStatus() {
        $.ajax({
            url: '{{ route("ai-comm.status") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const status = response.status;
                    let statusHtml = `
                        <div class="d-flex align-items-center mb-2">
                            <span class="status-indicator status-${status.system_ready ? 'online' : 'offline'}"></span>
                            <span>AI System ${status.system_ready ? 'Online' : 'Offline'}</span>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Knowledge Items</small>
                                <div class="fw-bold">${status.knowledge_count}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Your Businesses</small>
                                <div class="fw-bold">${status.businesses_count}</div>
                            </div>
                        </div>
                    `;
                    $('#ai-status').html(statusHtml);
                }
            }
        });
    }
    
    function loadQuickInsights(businessId) {
        $.ajax({
            url: '{{ route("ai-comm.quick-insights") }}',
            method: 'POST',
            data: {
                business_id: businessId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    const insights = response.insights;
                    let insightsHtml = `
                        <div class="insight-card">
                            <div class="insight-value">$${insights.sales.total}</div>
                            <div class="insight-label">Total Sales</div>
                        </div>
                        <div class="insight-card">
                            <div class="insight-value">$${insights.sales.avg_order}</div>
                            <div class="insight-label">Avg Order Value</div>
                        </div>
                        <div class="insight-card">
                            <div class="insight-value">${insights.market.trend}</div>
                            <div class="insight-label">Market Sentiment</div>
                        </div>
                        <div class="mt-3">
                            <small class="text-white-50">Recommendations:</small>
                            <ul class="list-unstyled mt-1">
                    `;
                    
                    insights.recommendations.forEach(rec => {
                        insightsHtml += `<li class="small">• ${rec}</li>`;
                    });
                    
                    insightsHtml += '</ul></div>';
                    $('#quick-insights').html(insightsHtml);
                }
            }
        });
    }
    
    function loadConversationHistory() {
        $.ajax({
            url: '{{ route("ai-comm.history") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    conversationHistory = response.history;
                    displayConversationHistory();
                }
            }
        });
    }
    
    function displayConversationHistory() {
        if (conversationHistory.length === 0) {
            $('#conversation-history').html('<p class="text-muted">No conversation history yet</p>');
            return;
        }
        
        let historyHtml = '';
        conversationHistory.slice(-5).forEach(item => {
            const time = new Date(item.timestamp).toLocaleTimeString();
            const truncatedMessage = item.message.length > 50 ? item.message.substring(0, 50) + '...' : item.message;
            historyHtml += `
                <div class="small mb-2">
                    <div class="fw-bold">${item.type === 'user' ? 'You' : 'AI'}</div>
                    <div class="text-muted">${truncatedMessage}</div>
                    <div class="text-muted">${time}</div>
                </div>
            `;
        });
        
        $('#conversation-history').html(historyHtml);
    }
    
    function clearHistory() {
        if (confirm('Are you sure you want to clear the conversation history?')) {
            $.ajax({
                url: '{{ route("ai-comm.clear-history") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        conversationHistory = [];
                        displayConversationHistory();
                        $('#chat-messages').empty();
                    }
                }
            });
        }
    }
});
</script>
@endpush
