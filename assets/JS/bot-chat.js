(function () {
    const botRoot = document.getElementById('sisuskom-bot');
    if (!botRoot) return;

    const toggleBtn = document.getElementById('sisuskom-bot-toggle');
    const closeBtn = document.getElementById('sisuskom-bot-close');
    const panel = document.getElementById('sisuskom-bot-panel');
    const form = document.getElementById('sisuskom-bot-form');
    const input = document.getElementById('sisuskom-bot-input');
    const sendBtn = document.getElementById('sisuskom-bot-send');
    const messages = document.getElementById('sisuskom-bot-messages');

    function openPanel() {
        panel.hidden = false;
        input.focus();
    }

    function closePanel() {
        panel.hidden = true;
    }

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function appendMessage(text, type) {
        const wrapper = document.createElement('div');
        wrapper.className = 'sisuskom-bot-message sisuskom-bot-message-' + type;

        const bubble = document.createElement('div');
        bubble.className = 'sisuskom-bot-bubble';
        bubble.textContent = text;

        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);
        scrollToBottom();
        return wrapper;
    }

    function showTyping() {
        const wrapper = document.createElement('div');
        wrapper.className = 'sisuskom-bot-message sisuskom-bot-message-bot';
        wrapper.id = 'sisuskom-bot-typing';

        const bubble = document.createElement('div');
        bubble.className = 'sisuskom-bot-bubble';
        bubble.innerHTML = '<span class="sisuskom-bot-typing"><span></span><span></span><span></span></span>';

        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);
        scrollToBottom();
    }

    function hideTyping() {
        const typing = document.getElementById('sisuskom-bot-typing');
        if (typing) typing.remove();
    }

    async function sendMessage(message) {
        appendMessage(message, 'user');
        input.value = '';
        sendBtn.disabled = true;
        showTyping();

        try {
            const response = await fetch('../bot/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message }),
            });

            const data = await response.json();
            hideTyping();

            if (!response.ok || !data.success) {
                appendMessage(data.message || 'Terjadi kesalahan. Silakan coba lagi.', 'bot');
                return;
            }

            appendMessage(data.reply, 'bot');
        } catch (error) {
            hideTyping();
            appendMessage('Koneksi gagal. Periksa jaringan Anda lalu coba lagi.', 'bot');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    }

    toggleBtn.addEventListener('click', function () {
        if (panel.hidden) {
            openPanel();
        } else {
            closePanel();
        }
    });

    closeBtn.addEventListener('click', closePanel);

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const message = input.value.trim();
        if (!message) return;
        sendMessage(message);
    });
})();
