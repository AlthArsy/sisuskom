<link rel="stylesheet" href="../assets/CSS/bot-chat.css">

<div id="sisuskom-bot" class="sisuskom-bot" aria-live="polite">
    <div id="sisuskom-bot-panel" class="sisuskom-bot-panel" hidden>
        <div class="sisuskom-bot-header">
            <div class="sisuskom-bot-header-info">
                <div>
                    <strong>Asisten SISUSKOM</strong>
                    <small>Tanya panduan sistem</small>
                </div>
            </div>
            <button type="button" class="sisuskom-bot-close" id="sisuskom-bot-close" aria-label="Tutup chat">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="sisuskom-bot-messages" id="sisuskom-bot-messages">
            <div class="sisuskom-bot-message sisuskom-bot-message-bot">
                <div class="sisuskom-bot-bubble">
                    Halo! Saya asisten SISUSKOM. Tanyakan apa saja, misalnya: <em>bagaimana cara mengisi KUK?</em>
                </div>
            </div>
        </div>

        <form class="sisuskom-bot-form no-loader" id="sisuskom-bot-form">
            <input
                type="text"
                id="sisuskom-bot-input"
                placeholder="Ketik pertanyaan..."
                autocomplete="off"
                maxlength="500"
            >
            <button type="submit" id="sisuskom-bot-send" aria-label="Kirim pesan">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <button type="button" class="sisuskom-bot-toggle" id="sisuskom-bot-toggle" aria-label="Buka chat bantuan">
        <i class="fas fa-comments"></i>
    </button>
</div>

<script src="../assets/JS/bot-chat.js"></script>
