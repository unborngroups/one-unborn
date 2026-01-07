// WebSocket/Echo is optional; chat works over plain HTTP.
function ensureEchoInstance() {
    // Intentionally left blank: we don't require a WebSocket connection
    // for sending / receiving messages or listing online users.
}

function formatTimestamp(value) {
    if (!value) {
        return '';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return new Intl.DateTimeFormat(undefined, {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: 'short',
    }).format(date);
}

function createMessageElement(message, currentUserId) {
    const wrapper = document.createElement('div');

    wrapper.className = 'live-chat-message';
    if (message && message.id) {
        wrapper.dataset.messageId = String(message.id);
    }
    wrapper.dataset.mine = message.sender && message.sender.id === currentUserId ? 'true' : 'false';

    const body = document.createElement('div');
    body.className = 'live-chat-text';
    body.textContent = message.message || '';

    wrapper.appendChild(body);

    if (Array.isArray(message.attachments) && message.attachments.length) {
        const filesBlock = document.createElement('div');

        filesBlock.className = 'live-chat-files';
        message.attachments.forEach((file) => {
            const link = document.createElement('a');

            link.href = file.url;
            link.target = '_blank';
            link.rel = 'noopener';

            link.textContent = file.name || 'Attachment';
            filesBlock.appendChild(link);
        });

        wrapper.appendChild(filesBlock);
    }

    const meta = document.createElement('div');
    meta.className = 'live-chat-meta';
    const senderName = message.sender ? message.sender.name : 'User';
    meta.textContent = `${senderName} • ${formatTimestamp(message.created_at)}`;

    wrapper.appendChild(meta);

    return wrapper;
}

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('live-chat-root');
    if (!root) {

        return;
    }

    // Best-effort: try to initialize Echo if available, but don't
    // let failures prevent HTTP-based chat from working.
    try {
        ensureEchoInstance();
    } catch (e) {
        // Ignore Echo errors; chat will fall back to polling-style HTTP.
        console && console.warn && console.warn('Echo disabled for chat:', e);
    }

    const toggleBtn = root.querySelector('[data-role="toggle"]');
    const panel = root.querySelector('[data-role="panel"]');
    const closeBtn = root.querySelector('[data-role="close"]');

    const groupList = root.querySelector('[data-role="group-list"]');
    const onlineList = root.querySelector('[data-role="online-list"]');
    const messagesBox = root.querySelector('[data-role="messages"]');

    const typingLabel = root.querySelector('[data-role="typing"]');
    const composer = root.querySelector('[data-role="composer"]');
    const messageInput = composer.querySelector('input[name="message"]');

    const fileInput = composer.querySelector('input[name="attachment"]');
    const attachBtn = composer.querySelector('[data-role="attach"]');
    const sendButton = composer.querySelector('.live-chat-send');

    const groupLabel = root.querySelector('[data-role="active-group-label"]');

    const dataset = root.dataset;
    const currentUserId = Number(dataset.userId);
    const currentUserName = dataset.userName || 'You';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const state = {
        bootstrapped: false,
        activeGroupId: null,
        groups: [],
        channelName: null,
        typingTimer: null,
        onlineUsers: new Map(),
        typingNotifiedAt: 0,
        onlinePollTimer: null,
        messagesPollTimer: null,
        lastMessageId: 0,
        pendingMessageEl: null,
        isSending: false,
    };

    function applyTypingIndicator(payload) {
        if (!payload || payload.userId === currentUserId) {
            return;
        }
        typingLabel.textContent = `${payload.userName} is typing...`;
        typingLabel.hidden = false;
        clearTimeout(state.typingTimer);
        state.typingTimer = setTimeout(() => {
            typingLabel.hidden = true;
        }, 2000);
    }

    function buildEndpoint(template, groupId) {
        return template.replace('__GROUP_ID__', groupId);
    }

    function clearMessages() {
        messagesBox.innerHTML = '';
        state.lastMessageId = 0;
    }

    function renderMessages(items) {
        clearMessages();
        items.forEach((item) => appendMessage(item));
    }

    function appendMessage(item) {
        // Avoid duplicates when the same message arrives from
        // multiple paths (optimistic send, polling, Echo).
        if (item && item.id) {
            const existing = messagesBox.querySelector(
                '[data-message-id="' + String(item.id) + '"]',
            );
            if (existing) {
                return existing;
            }
        }

        const el = createMessageElement(item, currentUserId);
        messagesBox.appendChild(el);

        if (item && item.id) {
            const idNum = Number(item.id) || 0;
            if (idNum > state.lastMessageId) {
                state.lastMessageId = idNum;
            }
        }
        messagesBox.scrollTop = messagesBox.scrollHeight;
        return el;
    }

    function updateGroupLabel() {
        const active = state.groups.find((group) => group.id === state.activeGroupId);
        if (!active) {
            groupLabel.textContent = 'No group available';
            return;
        }
        groupLabel.textContent = active.company_name ? `${active.name} · ${active.company_name}` : active.name;
    }

    function renderGroups() {
        groupList.innerHTML = '';
        state.groups.forEach((group) => {
            const li = document.createElement('li');
            li.textContent = group.name;
            li.dataset.groupId = group.id;
            li.dataset.active = group.id === state.activeGroupId ? 'true' : 'false';
            li.addEventListener('click', () => switchGroup(group.id));
            groupList.appendChild(li);
        });
    }

    function renderOnlineUsers() {
        onlineList.innerHTML = '';
        state.onlineUsers.forEach((user, id) => {
            const li = document.createElement('li');
            li.dataset.userId = id;

            const dot = document.createElement('span');
            dot.className = 'dot';
            // Green for online, grey for offline
            dot.style.backgroundColor = user.is_online ? '#22c55e' : '#9ca3af';
            li.appendChild(dot);

            const text = document.createElement('span');
            text.textContent = user.name + (user.is_online ? ' (Online)' : ' (Offline)');
            li.appendChild(text);

            onlineList.appendChild(li);
        });
    }

    function setOnlineUsers(users) {
        state.onlineUsers.clear();
        users.forEach((user) => {
            state.onlineUsers.set(user.id, user);
        });
        renderOnlineUsers();
    }

    function addOnlineUser(user) {
        state.onlineUsers.set(user.id, user);
        renderOnlineUsers();
    }

    function removeOnlineUser(user) {
        state.onlineUsers.delete(user.id);
        renderOnlineUsers();
    }

    async function fetchOnlineSnapshot(groupId) {
        try {
            const url = buildEndpoint(dataset.onlineUrlTemplate, groupId);
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            if (!response.ok) {
                return;
            }
            const payload = await response.json();
            if (Array.isArray(payload)) {
                setOnlineUsers(payload);
            }
        } catch (error) {
            console.error('Failed to fetch online users', error);
        }
    }

    function leaveCurrentChannel() {
        if (state.channelName && window.Echo) {
            window.Echo.leave(state.channelName);
        }
        state.channelName = null;
        if (state.onlinePollTimer) {
            clearInterval(state.onlinePollTimer);
            state.onlinePollTimer = null;
        }
        if (state.messagesPollTimer) {
            clearInterval(state.messagesPollTimer);
            state.messagesPollTimer = null;
        }
        state.onlineUsers.clear();
        renderOnlineUsers();
    }

    async function pollNewMessages() {
        if (!state.activeGroupId) {
            return;
        }

        try {
            const baseUrl = buildEndpoint(dataset.messagesUrlTemplate, state.activeGroupId);
            const sep = baseUrl.includes('?') ? '&' : '?';
            const url = state.lastMessageId
                ? `${baseUrl}${sep}since_id=${state.lastMessageId}`
                : baseUrl;

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            if (!response.ok) {
                return;
            }
            const payload = await response.json();
            if (!Array.isArray(payload) || !payload.length) {
                return;
            }
            payload.forEach((item) => appendMessage(item));
        } catch (error) {
            console.error('Failed to poll new messages', error);
        }
    }

    function joinChannel(groupId) {
        // Leave any existing channel and refresh the online snapshot.
        leaveCurrentChannel();

        // If Echo is available, use it for real-time updates.
        if (window.Echo) {
            const channelName = `chat-group.${groupId}`;
            state.channelName = channelName;

            window.Echo.join(channelName)
                .here((users) => {
                    setOnlineUsers(users);
                })
                .joining((user) => {
                    addOnlineUser(user);
                })
                .leaving((user) => {
                    removeOnlineUser(user);
                })
                .listen('ChatMessageSent', (event) => {
                    const message = event?.message || event;
                    if (!message) {
                        return;
                    }

                    // Ignore broadcasts for messages sent by this
                    // same browser session; the sender already sees
                    // their own message via the HTTP response and
                    // optimistic UI, and processing the broadcast
                    // again would create a duplicate bubble.
                    if (message.sender && Number(message.sender.id) === Number(currentUserId)) {
                        return;
                    }

                    if (Number(message.chat_group_id) === Number(state.activeGroupId)) {
                        appendMessage(message);
                    }
                })
                .listen('TypingEvent', applyTypingIndicator);
        }

        // Always fetch an initial snapshot of online users.
        fetchOnlineSnapshot(groupId);

        // Start periodic polling to keep the online list fresh.
        if (state.onlinePollTimer) {
            clearInterval(state.onlinePollTimer);
        }
        state.onlinePollTimer = setInterval(() => {
            if (state.activeGroupId === groupId) {
                fetchOnlineSnapshot(groupId);
            }
        }, 15000);

        // Start lightweight polling for new messages so that
        // other open browsers see updates without a full reload,
        // even when Echo/WebSockets are not configured.
        if (state.messagesPollTimer) {
            clearInterval(state.messagesPollTimer);
        }
        state.messagesPollTimer = setInterval(() => {
            if (state.activeGroupId === groupId) {
                pollNewMessages();
            }
        }, 3000);
    }

    async function loadMessages(groupId) {
        const url = buildEndpoint(dataset.messagesUrlTemplate, groupId);
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Unable to load messages');
        }
        const payload = await response.json();
        return Array.isArray(payload) ? payload : [];
    }

    async function switchGroup(groupId) {
        if (state.activeGroupId === groupId) {
            return;
        }

        state.activeGroupId = groupId;
        composer.querySelector('input[name="group_id"]').value = groupId;
        typingLabel.hidden = true;
        state.typingNotifiedAt = 0;
        renderGroups();
        updateGroupLabel();
        clearMessages();

        try {
            const messages = await loadMessages(groupId);
            renderMessages(messages);
        } catch (error) {
            console.error(error);
        }

        joinChannel(groupId);
    }

    async function bootstrap() {
        try {
            const response = await fetch(dataset.bootstrapUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Unable to bootstrap internal chat');
            }

            const payload = await response.json();
            state.groups = Array.isArray(payload.groups) ? payload.groups : [];
            state.bootstrapped = true;

            if (!state.groups.length) {
                groupLabel.textContent = 'Chat not available';
                composer.hidden = true;
                return;
            }

            composer.hidden = false;
            renderGroups();
            const targetGroupId = payload.active_group_id || state.groups[0].id;
            await switchGroup(targetGroupId);
        } catch (error) {
            console.error(error);
            groupLabel.textContent = 'Chat unavailable';
        }
    }

    function openPanel() {
        panel.hidden = false;
        toggleBtn.style.display = 'none';
        if (!state.bootstrapped) {
            bootstrap();
        }
    }

    function closePanel() {
        panel.hidden = true;
        toggleBtn.style.display = '';
        typingLabel.hidden = true;
    }

    toggleBtn.addEventListener('click', openPanel);
    closeBtn.addEventListener('click', closePanel);

    composer.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!state.activeGroupId) {
            return;
        }

        const message = messageInput.value.trim();
        const file = fileInput.files[0];

        if (!message && !file) {
            return;
        }

        // Prevent accidental double-submit (double click or rapid Enter)
        // while a previous send is still in flight.
        if (state.isSending) {
            return;
        }
        state.isSending = true;

        // Generate a client-side token used for idempotency on the
        // server so that the same logical send cannot create more
        // than one database row even if multiple HTTP requests are
        // fired.
        const clientToken = `msg-${Date.now()}-${Math.random().toString(16).slice(2)}`;

        // Clear the input immediately after send.
        messageInput.value = '';

        const formData = new FormData();
        formData.append('group_id', state.activeGroupId);
        formData.append('client_token', clientToken);
        if (message) {
            formData.append('message', message);
        }
        if (file) {
            formData.append('attachment', file);
        }

        try {
            sendButton.disabled = true;
            const response = await fetch(dataset.sendUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            if (response.ok) {
                const payload = await response.json();
                if (payload && payload.id) {
                    // Append the confirmed message from the server.
                    // If polling or Echo already delivered it, the
                    // DOM-level dedupe in appendMessage will prevent
                    // any duplicate bubbles.
                    appendMessage(payload);
                }
                fileInput.value = '';
            }
        } catch (error) {
            console.error('Failed to send message', error);
            // On failure, restore the text so it isn't lost.
            if (!messageInput.value) {
                messageInput.value = message;
            }
        } finally {
            sendButton.disabled = false;
            state.isSending = false;
        }
    });

    attachBtn.addEventListener('click', () => fileInput.click());

    messageInput.addEventListener('input', () => {
        if (!state.activeGroupId) {
            return;
        }
        const now = Date.now();
        if (now - state.typingNotifiedAt < 1200) {
            return;
        }
        state.typingNotifiedAt = now;
        fetch(dataset.typingUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ group_id: state.activeGroupId }),
        }).catch(() => {});
    });

    if (document.visibilityState === 'visible') {
        bootstrap();
    }
});
