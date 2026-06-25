(function () {
    const chatbox = document.getElementById('aiChatbox');
    if (!chatbox) {
        return;
    }

    const endpoint = chatbox.dataset.endpoint;
    const toggleButton = document.getElementById('aiChatboxToggle');
    const closeButton = document.getElementById('aiChatboxClose');
    const panel = document.getElementById('aiChatboxPanel');
    const messages = document.getElementById('aiChatboxMessages');
    const form = document.getElementById('aiChatboxForm');
    const input = document.getElementById('aiChatboxInput');
    const sendButton = document.getElementById('aiChatboxSend');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function openPanel() {
        panel.hidden = false;
        toggleButton.setAttribute('aria-expanded', 'true');
        input.focus();
    }

    function closePanel() {
        panel.hidden = true;
        toggleButton.setAttribute('aria-expanded', 'false');
    }

    function isSafeUrl(url) {
        try {
            const parsed = new URL(url, window.location.origin);
            return parsed.protocol === 'http:' || parsed.protocol === 'https:';
        } catch (error) {
            return false;
        }
    }

    function appendTextWithMarkdownLinks(container, text) {
        const linkRegex = /\[([^\]]+)\]\(([^)]+)\)/g;
        let lastIndex = 0;
        let match;

        while ((match = linkRegex.exec(text)) !== null) {
            if (match.index > lastIndex) {
                container.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));
            }

            const href = match[2].trim();

            if (isSafeUrl(href)) {
                const link = document.createElement('a');
                link.href = href;
                link.textContent = match[1];
                link.className = 'ai-chatbox-link';
                container.appendChild(link);
            } else {
                container.appendChild(document.createTextNode(match[0]));
            }

            lastIndex = match.index + match[0].length;
        }

        if (lastIndex < text.length) {
            container.appendChild(document.createTextNode(text.slice(lastIndex)));
        }
    }

    function appendMessage(role, text, sources) {
        const wrapper = document.createElement('div');
        wrapper.className = 'ai-chatbox-message ai-chatbox-message-' + role;

        const paragraph = document.createElement('p');
        if (role === 'assistant') {
            appendTextWithMarkdownLinks(paragraph, text);
        } else {
            paragraph.textContent = text;
        }
        wrapper.appendChild(paragraph);

        if (Array.isArray(sources) && sources.length > 0) {
            const sourceList = document.createElement('ul');
            sourceList.className = 'ai-chatbox-sources';

            sources.forEach(function (source) {
                const item = document.createElement('li');
                const label = source.type + ': ' + source.label;

                if (source.url && isSafeUrl(source.url)) {
                    const link = document.createElement('a');
                    link.href = source.url;
                    link.textContent = label;
                    link.className = 'ai-chatbox-link';
                    item.appendChild(link);
                } else {
                    item.textContent = label;
                }

                sourceList.appendChild(item);
            });

            wrapper.appendChild(sourceList);
        }

        messages.appendChild(wrapper);
        messages.scrollTop = messages.scrollHeight;
    }

    function setLoading(isLoading) {
        sendButton.disabled = isLoading;
        input.disabled = isLoading;
        sendButton.textContent = isLoading ? 'Sending...' : 'Send';
    }

    toggleButton.addEventListener('click', function () {
        if (panel.hidden) {
            openPanel();
        } else {
            closePanel();
        }
    });

    closeButton.addEventListener('click', closePanel);

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const message = input.value.trim();
        if (!message) {
            return;
        }

        appendMessage('user', message);
        input.value = '';
        setLoading(true);

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({ message: message }),
            });

            const data = await response.json();

            if (!response.ok) {
                appendMessage('assistant', data.message || 'Unable to get a response right now.');
                return;
            }

            appendMessage('assistant', data.answer, data.sources);
        } catch (error) {
            appendMessage('assistant', 'Network error. Please try again.');
        } finally {
            setLoading(false);
            input.focus();
        }
    });
})();
