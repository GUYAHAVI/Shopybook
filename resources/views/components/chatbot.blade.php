<!-- Shopybook Customer Support Chatbot -->
<div id="shopybook-chatbot">
    <!-- Chat Toggle Button -->
    <button id="chat-toggle-btn" class="chat-toggle-button" type="button">
        <i class="bi bi-chat-dots-fill"></i>
        <span class="chat-badge" id="chat-badge">AI</span>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" class="chat-window" style="display: none;">
        <div class="chat-header">
            <div class="d-flex align-items-center">
                <div class="chat-avatar">
                    <i class="bi bi-robot"></i>
                </div>
                <div class="ms-2">
                    <h6 class="mb-0">Shopybook Support</h6>
                    <small class="text-muted">AI-Powered Assistant</small>
                </div>
            </div>
            <button class="btn-close-chat" id="chat-close-btn" type="button">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="chat-messages" id="chat-messages">
            <div class="message bot-message">
                <div class="message-avatar">
                    <i class="bi bi-robot"></i>
                </div>
                <div class="message-content">
                    <p>👋 Hello! I'm your Shopybook AI assistant. I can help you with:</p>
                    <ul class="mb-0">
                        <li>Features and pricing information</li>
                        <li>Getting started with Shopybook</li>
                        <li>Product and inventory management</li>
                        <li>M-Pesa integration</li>
                        <li>AI Website Builder</li>
                        <li>And much more!</li>
                    </ul>
                    <p class="mb-0 mt-2"><strong>How can I help you today?</strong></p>
                </div>
            </div>
        </div>

        <div class="chat-input-container">
            <form id="chat-form" class="d-flex gap-2">
                <input 
                    type="text" 
                    id="chat-input" 
                    class="form-control chat-input" 
                    placeholder="Ask me anything about Shopybook..."
                    autocomplete="off"
                    required
                />
                <button type="submit" class="btn btn-primary chat-send-btn" id="chat-send-btn">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
            <small class="text-muted mt-1 d-block text-center">Powered by Claude AI</small>
        </div>
    </div>
</div>

<style>
#shopybook-chatbot {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.chat-toggle-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #13e8e9 0%, #020258 100%);
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(19, 232, 233, 0.4);
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-toggle-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(19, 232, 233, 0.6);
}

.chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: #fff;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: bold;
}

.chat-window {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 600px;
    max-height: calc(100vh - 120px);
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chat-header {
    background: linear-gradient(135deg, #020258 0%, #13e8e9 100%);
    color: #fff;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.chat-header h6 {
    color: #fff !important;
    font-weight: 600;
}

.chat-header small {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 11px;
}

.btn-close-chat {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    transition: all 0.2s;
}

.btn-close-chat:hover {
    transform: scale(1.1);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bot-message {
    align-self: flex-start;
}

.user-message {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.bot-message .message-avatar {
    background: linear-gradient(135deg, #020258 0%, #13e8e9 100%);
    color: #fff;
}

.user-message .message-avatar {
    background: #13e8e9;
    color: #020258;
}

.message-content {
    background: #fff;
    padding: 12px 15px;
    border-radius: 15px;
    max-width: 75%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.bot-message .message-content {
    border-bottom-left-radius: 5px;
}

.user-message .message-content {
    background: #13e8e9;
    color: #020258;
    border-bottom-right-radius: 5px;
}

.message-content p {
    margin: 0;
    color: #000;
    font-size: 14px;
    line-height: 1.5;
}

.user-message .message-content p {
    color: #020258;
}

.bot-message .message-content p {
    color: #000 !important;
}

.message-content ul {
    margin: 8px 0 0 0;
    padding-left: 20px;
    font-size: 13px;
    color: #000;
}

.message-content li {
    margin-bottom: 4px;
    color: #000;
}

.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 10px;
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #13e8e9;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

.chat-input-container {
    padding: 15px;
    background: #fff;
    border-top: 1px solid #e0e0e0;
}

.chat-input {
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.3s;
}

.chat-input:focus {
    border-color: #13e8e9;
    box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1);
    outline: none;
}

.chat-send-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #13e8e9 0%, #020258 100%);
    border: none;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    flex-shrink: 0;
}

.chat-send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(19, 232, 233, 0.4);
}

.chat-send-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Mobile Responsiveness */
@media (max-width: 480px) {
    .chat-window {
        width: calc(100vw - 20px);
        height: calc(100vh - 100px);
        bottom: 80px;
        right: 10px;
    }

    #shopybook-chatbot {
        bottom: 10px;
        right: 10px;
    }

    .message-content {
        max-width: 80%;
    }
}

/* Scrollbar Styling */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #13e8e9;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #020258;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggleBtn = document.getElementById('chat-toggle-btn');
    const chatCloseBtn = document.getElementById('chat-close-btn');
    const chatWindow = document.getElementById('chat-window');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const chatSendBtn = document.getElementById('chat-send-btn');

    // Toggle chat window
    chatToggleBtn.addEventListener('click', function() {
        if (chatWindow.style.display === 'none') {
            chatWindow.style.display = 'flex';
            chatInput.focus();
        } else {
            chatWindow.style.display = 'none';
        }
    });

    chatCloseBtn.addEventListener('click', function() {
        chatWindow.style.display = 'none';
    });

    // Handle form submission
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = chatInput.value.trim();
        if (!message) return;

        console.log('[Chatbot] Sending message:', message);

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Disable input while processing
        chatSendBtn.disabled = true;
        chatInput.disabled = true;

        // Show typing indicator
        const typingIndicator = addTypingIndicator();

        try {
            console.log('[Chatbot] Fetching response from:', '{{ route("chatbot.message") }}');
            console.log('[Chatbot] CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
            
            // Send message to backend
            const response = await fetch('{{ route("chatbot.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: message })
            });

            console.log('[Chatbot] Response status:', response.status, response.statusText);
            
            const data = await response.json();
            console.log('[Chatbot] Response data:', data);

            // Remove typing indicator
            typingIndicator.remove();

            if (data.success) {
                console.log('[Chatbot] Success - displaying response');
                addMessage(data.response, 'bot');
            } else {
                console.error('[Chatbot] Error response:', data);
                const errorMsg = data.message || 'Sorry, I encountered an error. Please try again or contact us at info@shopybook.com';
                addMessage(errorMsg, 'bot');
            }
        } catch (error) {
            console.error('[Chatbot] Fetch error:', error);
            console.error('[Chatbot] Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            typingIndicator.remove();
            addMessage('Sorry, I\'m having trouble connecting. Please try again later or contact us at info@shopybook.com', 'bot');
        } finally {
            // Re-enable input
            chatSendBtn.disabled = false;
            chatInput.disabled = false;
            chatInput.focus();
        }
    });

    function addMessage(text, sender) {
        console.log('[Chatbot] Adding message:', { text, sender });
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;

        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.innerHTML = sender === 'bot' ? '<i class="bi bi-robot"></i>' : '<i class="bi bi-person-fill"></i>';

        const content = document.createElement('div');
        content.className = 'message-content';
        
        // Convert markdown-like formatting
        const formattedText = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
        
        content.innerHTML = `<p style="color: ${sender === 'bot' ? '#000' : '#020258'} !important;">${formattedText}</p>`;

        messageDiv.appendChild(avatar);
        messageDiv.appendChild(content);
        chatMessages.appendChild(messageDiv);

        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        console.log('[Chatbot] Message added successfully');
    }

    function addTypingIndicator() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message';
        messageDiv.id = 'typing-indicator';

        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.innerHTML = '<i class="bi bi-robot"></i>';

        const content = document.createElement('div');
        content.className = 'message-content';
        content.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';

        messageDiv.appendChild(avatar);
        messageDiv.appendChild(content);
        chatMessages.appendChild(messageDiv);

        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;

        return messageDiv;
    }
});
</script>
