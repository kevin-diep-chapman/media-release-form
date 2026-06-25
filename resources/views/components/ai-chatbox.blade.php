<div class="ai-chatbox" id="aiChatbox" data-endpoint="{{ route('chat.message') }}">
    <button
        type="button"
        class="ai-chatbox-toggle"
        id="aiChatboxToggle"
        aria-expanded="false"
        aria-controls="aiChatboxPanel"
        aria-label="Open Chapman Media assistant"
    >
        <span class="ai-chatbox-toggle-icon" aria-hidden="true">?</span>
    </button>

    <section
        class="ai-chatbox-panel"
        id="aiChatboxPanel"
        hidden
        aria-label="Chapman Media assistant"
    >
        <header class="ai-chatbox-header">
            <div>
                <h2>Chapman Media Assistant</h2>
                <p>Ask about events, submissions, and staff.</p>
            </div>
            <button type="button" class="ai-chatbox-close" id="aiChatboxClose" aria-label="Close chat">×</button>
        </header>

        <div class="ai-chatbox-messages" id="aiChatboxMessages" role="log" aria-live="polite">
            <div class="ai-chatbox-message ai-chatbox-message-assistant">
                <p>Hello! I can answer questions using Chapman Media data from the database.</p>
            </div>
        </div>

        <form class="ai-chatbox-form" id="aiChatboxForm">
            <label class="sr-only" for="aiChatboxInput">Your message</label>
            <textarea
                id="aiChatboxInput"
                rows="2"
                maxlength="{{ config('rag.max_message_length', 1000) }}"
                placeholder="Ask a question..."
                required
            ></textarea>
            <button type="submit" class="ai-chatbox-send" id="aiChatboxSend">Send</button>
        </form>
    </section>
</div>

<script src="{{ asset('js/ai-chatbox.js') }}" defer></script>
