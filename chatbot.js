document.addEventListener("DOMContentLoaded", function() {
    // Create the HTML structure
    const chatHTML = `
    <div id="pntl-chatbot-container">
        <button id="pntl-chatbot-toggle">
            <i class="fas fa-robot"></i>
        </button>
        <div id="pntl-chatbot-window" class="hidden">
            <div class="chatbot-header">
                <h4><i class="fas fa-robot"></i> PNTL Chatbot</h4>
                <button id="pntl-chatbot-close"><i class="fas fa-times"></i></button>
            </div>
            <div id="pntl-chatbot-messages">
                <div class="chat-message bot">
                    <p>Olá! Ha'u PNTL Chatbot. Husu informasaun konaba edifísio ou infraestrutura PNTL nian.</p>
                </div>
            </div>
            <div class="chatbot-input">
                <input type="text" id="pntl-chatbot-input" placeholder="Hakerek pergunta..." autocomplete="off">
                <button id="pntl-chatbot-send"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
    <style>
        #pntl-chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            font-family: 'Inter', sans-serif;
        }
        #pntl-chatbot-toggle {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        #pntl-chatbot-toggle:hover {
            transform: scale(1.1);
        }
        #pntl-chatbot-window {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            height: 450px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        #pntl-chatbot-window.hidden {
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }
        .chatbot-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chatbot-header h4 {
            margin: 0;
            font-size: 16px;
        }
        .chatbot-header button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        #pntl-chatbot-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f8f9fa;
        }
        .chat-message {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            font-size: 14px;
            line-height: 1.4;
        }
        .chat-message p {
            margin: 0;
        }
        .chat-message.bot {
            background: #e9ecef;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 0;
        }
        .chat-message.user {
            background: #2a5298;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }
        .chatbot-input {
            padding: 15px;
            display: flex;
            gap: 10px;
            background: white;
            border-top: 1px solid #eee;
        }
        .chatbot-input input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
        }
        .chatbot-input input:focus {
            border-color: #2a5298;
        }
        .chatbot-input button {
            background: #2a5298;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .chatbot-input button:hover {
            background: #1e3c72;
        }
        .typing-indicator {
            display: flex;
            gap: 5px;
            padding: 5px 10px;
        }
        .typing-indicator span {
            width: 6px;
            height: 6px;
            background: #888;
            border-radius: 50%;
            animation: bounce 1.5s infinite;
        }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
    </style>
    `;

    // Append to body
    document.body.insertAdjacentHTML('beforeend', chatHTML);

    const toggleBtn = document.getElementById('pntl-chatbot-toggle');
    const closeBtn = document.getElementById('pntl-chatbot-close');
    const chatWindow = document.getElementById('pntl-chatbot-window');
    const sendBtn = document.getElementById('pntl-chatbot-send');
    const inputField = document.getElementById('pntl-chatbot-input');
    const messagesContainer = document.getElementById('pntl-chatbot-messages');

    // Toggle chat window
    toggleBtn.addEventListener('click', () => {
        chatWindow.classList.toggle('hidden');
        if(!chatWindow.classList.contains('hidden')) {
            inputField.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.classList.add('hidden');
    });

    // Add message to UI
    function addMessage(text, sender) {
        const div = document.createElement('div');
        div.className = `chat-message ${sender}`;
        div.innerHTML = `<p>${text}</p>`;
        messagesContainer.appendChild(div);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Add typing indicator
    function addTypingIndicator() {
        const div = document.createElement('div');
        div.className = `chat-message bot typing`;
        div.id = 'typing-indicator';
        div.innerHTML = `<div class="typing-indicator"><span></span><span></span><span></span></div>`;
        messagesContainer.appendChild(div);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Remove typing indicator
    function removeTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if(indicator) indicator.remove();
    }

    // Send message to server
    async function sendMessage() {
        const message = inputField.value.trim();
        if (!message) return;

        // Display user message
        addMessage(message, 'user');
        inputField.value = '';

        // Show typing
        addTypingIndicator();

        try {
            const response = await fetch('chat_handler', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            removeTypingIndicator();
            
            if (data && data.response) {
                addMessage(data.response, 'bot');
            } else {
                addMessage("Deskulpa, iha erro ruma.", 'bot');
            }
        } catch (error) {
            removeTypingIndicator();
            addMessage("Deskulpa, labele konekta ba servidór.", 'bot');
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    inputField.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
});
