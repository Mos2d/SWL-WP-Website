/**
 * Broadcast JavaScript functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the countdown timer for scheduled broadcasts
    initCountdownTimer();
    
    // Initialize chat functionality if available
    initChatFunctionality();
});

/**
 * Initialize countdown timer for scheduled broadcasts
 */
function initCountdownTimer() {
    const countdownTimer = document.querySelector('.countdown-timer');
    
    if (countdownTimer) {
        const scheduledDate = countdownTimer.getAttribute('data-scheduled-date');
        const scheduledTimestamp = new Date(scheduledDate).getTime();
        
        const daysElement = document.querySelector('.countdown-days');
        const hoursElement = document.querySelector('.countdown-hours');
        const minutesElement = document.querySelector('.countdown-minutes');
        const secondsElement = document.querySelector('.countdown-seconds');
        
        // Update countdown every second
        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = scheduledTimestamp - now;
            
            if (distance < 0) {
                // Broadcast should be live now
                clearInterval(countdownInterval);
                
                daysElement.textContent = '0';
                hoursElement.textContent = '0';
                minutesElement.textContent = '0';
                secondsElement.textContent = '0';
                
                // Reload the page to show the live broadcast
                // (if the broadcast status was updated by the cron job)
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
                
                return;
            }
            
            // Calculate time units
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Update display
            daysElement.textContent = days;
            hoursElement.textContent = hours < 10 ? '0' + hours : hours;
            minutesElement.textContent = minutes < 10 ? '0' + minutes : minutes;
            secondsElement.textContent = seconds < 10 ? '0' + seconds : seconds;
        }, 1000);
    }
}

/**
 * Initialize chat functionality for live broadcasts
 */
function initChatFunctionality() {
    const chatForm = document.getElementById('chat-form');
    const chatMessages = document.getElementById('chat-messages');
    
    if (!chatForm || !chatMessages) {
        return;
    }
    
    // Clear any existing event listeners by cloning and replacing the form
    const newChatForm = chatForm.cloneNode(true);
    chatForm.parentNode.replaceChild(newChatForm, chatForm);
    
    // Set a flag to prevent duplicate submissions
    let isSubmitting = false;
    
    // Load initial chat messages
    loadChatMessages();
    
    // Poll for new messages every 5 seconds
    const messageInterval = setInterval(loadChatMessages, 5000);
    
    // Handle chat form submission
    newChatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Prevent multiple submissions
        if (isSubmitting) {
            return;
        }
        
        const messageInput = document.getElementById('chat-message');
        const message = messageInput.value.trim();
        const broadcastId = document.getElementById('broadcast-id').value;
        
        if (message !== '') {
            isSubmitting = true;
            
            // Disable form while submitting
            const submitButton = newChatForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }
            
            sendChatMessage(broadcastId, message)
                .then(() => {
                    messageInput.value = '';
                    isSubmitting = false;
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                })
                .catch(() => {
                    isSubmitting = false;
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                });
        }
    });
    
    // Clean up when page is unloaded
    window.addEventListener('beforeunload', function() {
        clearInterval(messageInterval);
    });
}

/**
 * Load chat messages via AJAX
 */
function loadChatMessages() {
    if (typeof sarah_loz_broadcast === 'undefined') {
        console.error('Broadcast script data not available');
        return;
    }
    
    const broadcastId = document.getElementById('broadcast-id').value;
    
    fetch(sarah_loz_broadcast.ajax_url + '?action=sarah_loz_get_chat_messages&broadcast_id=' + broadcastId + '&nonce=' + sarah_loz_broadcast.nonce)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.messages) {
                displayChatMessages(data.data.messages);
            }
        })
        .catch(error => {
            console.error('Error loading chat messages:', error);
        });
}

/**
 * Send a chat message via AJAX
 * @returns {Promise} A promise that resolves when the message is sent
 */
function sendChatMessage(broadcastId, message) {
    if (typeof sarah_loz_broadcast === 'undefined') {
        console.error('Broadcast script data not available');
        return Promise.reject('Broadcast script data not available');
    }
    
    const formData = new FormData();
    formData.append('action', 'sarah_loz_send_chat_message');
    formData.append('broadcast_id', broadcastId);
    formData.append('message', message);
    formData.append('nonce', sarah_loz_broadcast.nonce);
    
    return fetch(sarah_loz_broadcast.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add user's message immediately for better UX
            const userMessage = {
                user_name: data.data.user_name,
                message: data.data.message,
                timestamp: data.data.timestamp,
                is_current_user: true,
                id: data.data.id || Date.now() // Add a unique ID to prevent duplicates
            };
            
            addMessageToChat(userMessage);
            return data;
        } else {
            throw new Error('Failed to send message');
        }
    });
}

/**
 * Display chat messages in the chat container
 */
function displayChatMessages(messages) {
    const chatMessages = document.getElementById('chat-messages');
    
    // Clear loading message if present
    if (chatMessages.querySelector('.text-center.text-gray-500')) {
        chatMessages.innerHTML = '';
    }
    
    // Handle empty messages list
    if (messages.length === 0) {
        chatMessages.innerHTML = `
            <div class="text-center text-gray-500 py-4">
                <i class="fas fa-comments mr-2"></i>
                لا توجد رسائل بعد. كن أول من يشارك!
            </div>
        `;
        return;
    }
    
    // Keep track of message IDs we've already displayed
    const displayedMessageIds = new Set();
    
    // Get all existing messages in the chat
    Array.from(chatMessages.children).forEach(element => {
        const messageId = element.getAttribute('data-message-id');
        if (messageId) {
            displayedMessageIds.add(messageId);
        }
    });
    
    // If chat is empty or only has loading message, add all messages
    if (chatMessages.children.length === 0 || chatMessages.querySelector('.text-center.text-gray-500')) {
        chatMessages.innerHTML = '';
        messages.forEach(message => {
            // Create a message ID if not present
            const messageId = message.id || `${message.user_name}-${message.timestamp}-${message.message.substring(0, 20)}`.replace(/[^a-z0-9]/gi, '');
            message.id = messageId;
            
            if (!displayedMessageIds.has(messageId)) {
                addMessageToChat(message);
                displayedMessageIds.add(messageId);
            }
        });
        
        // Scroll to bottom of chat
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return;
    }
    
    // If chat already has messages, only add new ones
    let hasNewMessages = false;
    
    messages.forEach(message => {
        // Create a message ID if not present
        const messageId = message.id || `${message.user_name}-${message.timestamp}-${message.message.substring(0, 20)}`.replace(/[^a-z0-9]/gi, '');
        message.id = messageId;
        
        if (!displayedMessageIds.has(messageId)) {
            addMessageToChat(message);
            displayedMessageIds.add(messageId);
            hasNewMessages = true;
        }
    });
    
    if (hasNewMessages) {
        // Scroll to bottom on new messages
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

/**
 * Add a single message to the chat container
 */
function addMessageToChat(message) {
    const chatMessages = document.getElementById('chat-messages');
    
    const messageElement = document.createElement('div');
    messageElement.className = message.is_current_user 
        ? 'flex flex-col items-end' 
        : 'flex flex-col items-start';
    
    // Create a message ID if not present
    const messageId = message.id || `${message.user_name}-${message.timestamp}-${message.message.substring(0, 20)}`.replace(/[^a-z0-9]/gi, '');
    
    // Add message ID as a data attribute
    messageElement.setAttribute('data-message-id', messageId);
    
    const bubbleClass = message.is_current_user
        ? 'bg-primary text-white rounded-tl-lg rounded-tr-lg rounded-bl-lg'
        : 'bg-gray-100 text-gray-800 rounded-tl-lg rounded-tr-lg rounded-br-lg';
    
    messageElement.innerHTML = `
        <div class="flex items-center mb-1">
            <span class="font-bold text-xs ${message.is_current_user ? 'text-primary' : 'text-gray-600'}">${message.user_name}</span>
            <span class="text-gray-500 text-xs ml-2">${message.timestamp}</span>
        </div>
        <div class="px-4 py-2 max-w-[80%] ${bubbleClass}">
            ${message.message}
        </div>
    `;
    
    chatMessages.appendChild(messageElement);
    chatMessages.scrollTop = chatMessages.scrollHeight;
} 