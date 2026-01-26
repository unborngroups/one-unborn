// resources/js/private-chat.js
// One-to-one chat logic for sidebar chat widget

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('live-chat-root');
    if (!root) return;

    const userList = root.querySelector('[data-role="user-list"]');
    const messagesBox = root.querySelector('[data-role="messages"]');
    const composer = root.querySelector('[data-role="composer"]');
    const messageInput = composer.querySelector('input[name="message"]');
    const sendButton = composer.querySelector('.live-chat-send');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const dataset = root.dataset;
    const currentUserId = Number(dataset.userId);

    let activeUserId = null;

    // Fetch and render online users (with online/offline status)
    async function fetchOnlineUsers() {
        const response = await fetch('/private-chat/users/online', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const users = await response.json();
        userList.innerHTML = '';
        if (users.length === 0) {
            const li = document.createElement('li');
            li.textContent = 'No users available';
            li.style.color = '#888';
            userList.appendChild(li);
            return;
        }
        users.forEach(user => {
            const li = document.createElement('li');
            li.textContent = user.name;
            li.dataset.userId = user.id;
            li.className = 'private-chat-user';
            li.dataset.online = user.is_online ? 'true' : 'false';
            li.onclick = () => selectUser(user.id, user.name);
            userList.appendChild(li);
        });
    }
        // Fetch and render online users (with online/offline status and last seen)
        async function fetchOnlineUsers() {
            const response = await fetch('/private-chat/users/online', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const users = await response.json();
            userList.innerHTML = '';
            if (users.length === 0) {
                const li = document.createElement('li');
                li.textContent = 'No users available';
                li.style.color = '#888';
                userList.appendChild(li);
                return;
            }
            users.forEach(user => {
                const li = document.createElement('li');
                li.className = 'private-chat-user';
                li.dataset.userId = user.id;
                li.dataset.online = user.is_online ? 'true' : 'false';
                li.onclick = () => selectUser(user.id, user.name);
                li.innerHTML = `${user.name} <span class="last-seen">${user.is_online ? '' : (user.last_seen ? '(last seen ' + formatLastSeen(user.last_seen) + ')' : '')}</span>`;
                userList.appendChild(li);
            });
        }

    // Fetch and render messages with selected user, including attachments
    async function fetchMessages(userId) {
        const response = await fetch(`/private-chat/messages/${userId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const messages = await response.json();
        messagesBox.innerHTML = '';
        messages.forEach(msg => {
            const div = document.createElement('div');
            div.className = 'private-chat-message' + (msg.sender_id === currentUserId ? ' mine' : ' theirs');
            // Show sender name for received messages
            if (msg.sender_id !== currentUserId) {
                const nameDiv = document.createElement('div');
                nameDiv.className = 'private-chat-sender';
                nameDiv.textContent = userList.querySelector('li.selected')?.textContent || 'User';
                div.appendChild(nameDiv);
            }
            // Add message text
            if (msg.message) {
                const msgSpan = document.createElement('span');
                msgSpan.textContent = msg.message;
                div.appendChild(msgSpan);
            }
            // Add file/image if present
            if (msg.file_path) {
                const attDiv = document.createElement('div');
                attDiv.className = 'private-chat-attachment';
                if (msg.file_type && msg.file_type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = '/' + msg.file_path;
                    img.alt = msg.original_name;
                    img.style.maxWidth = '120px';
                    img.style.maxHeight = '120px';
                    attDiv.appendChild(img);
                } else {
                    const link = document.createElement('a');
                    link.href = '/' + msg.file_path;
                    link.target = '_blank';
                    link.textContent = msg.original_name || 'Download';
                    attDiv.appendChild(link);
                }
                div.appendChild(attDiv);
            }
            // Add timestamp
            const metaDiv = document.createElement('div');
            metaDiv.className = 'private-chat-meta';
            metaDiv.textContent = formatChatTime(msg.created_at);
            div.appendChild(metaDiv);
            messagesBox.appendChild(div);
        });
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }
        // Fetch and render messages with selected user, including attachments
        async function fetchMessages(userId) {
            const response = await fetch(`/private-chat/messages/${userId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const messages = await response.json();
            messagesBox.innerHTML = '';
            messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = 'private-chat-message' + (msg.sender_id === currentUserId ? ' mine' : ' theirs');
                // Show sender name for received messages
                if (msg.sender_id !== currentUserId) {
                    const nameDiv = document.createElement('div');
                    nameDiv.className = 'private-chat-sender';
                    nameDiv.textContent = userList.querySelector('li.selected')?.textContent || 'User';
                    div.appendChild(nameDiv);
                }
                // Add message text
                if (msg.message) {
                    div.appendChild(document.createTextNode(msg.message));
                }
                // Add attachment if present
                if (msg.attachment || (msg.attachments && msg.attachments.length > 0)) {
                    let att = msg.attachment || (msg.attachments ? msg.attachments[0] : null);
                    if (att) {
                        const attDiv = document.createElement('div');
                        attDiv.className = 'private-chat-attachment';
                        if (att.file_type && att.file_type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = '/storage/' + att.file_path;
                            img.alt = att.original_name;
                            img.style.maxWidth = '120px';
                            img.style.maxHeight = '120px';
                            attDiv.appendChild(img);
                        } else {
                            const link = document.createElement('a');
                            link.href = '/storage/' + att.file_path;
                            link.target = '_blank';
                            link.textContent = att.original_name || 'Download';
                            attDiv.appendChild(link);
                        }
                        div.appendChild(attDiv);
                    }
                }
                // Add timestamp
                const metaDiv = document.createElement('div');
                metaDiv.className = 'private-chat-meta';
                metaDiv.textContent = formatChatTime(msg.created_at);
                div.appendChild(metaDiv);
                messagesBox.appendChild(div);
            });
            messagesBox.scrollTop = messagesBox.scrollHeight;
        }

    // Format time as h:mm AM/PM
    function formatChatTime(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        const hours = date.getHours();
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const hour12 = hours % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }

    // Select a user to chat with
    function selectUser(userId, userName) {
        activeUserId = userId;
        fetchMessages(userId);
        // Highlight selected user
        userList.querySelectorAll('li').forEach(li => li.classList.remove('selected'));
        const selectedLi = userList.querySelector('li[data-user-id="' + userId + '"]');
        if (selectedLi) selectedLi.classList.add('selected');
    }

    // File input and preview logic
    const fileInput = composer.querySelector('input[type="file"]');
    const attachBtn = composer.querySelector('.live-chat-attach');
    const filePreview = composer.querySelector('.live-chat-file-preview');
    const plusBtn = composer.querySelector('.live-chat-plus');
    const emojiBtn = composer.querySelector('.live-chat-emoji');

    plusBtn.addEventListener('click', () => {
        fileInput.value = '';
        filePreview.style.display = 'none';
        filePreview.textContent = '';
        fileInput.click();
    });
    emojiBtn.addEventListener('click', () => {
        messageInput.value += '😊';
        messageInput.focus();
    });

    attachBtn.addEventListener('click', () => {
        fileInput.value = '';
        filePreview.style.display = 'none';
        filePreview.textContent = '';
        fileInput.click();
    });
    fileInput.addEventListener('change', () => {
        if (fileInput.files && fileInput.files[0]) {
            filePreview.style.display = 'inline-block';
            filePreview.textContent = fileInput.files[0].name;
        } else {
            filePreview.style.display = 'none';
            filePreview.textContent = '';
        }
    });

    // Only use FormData-based message sending (with or without file)
    composer.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!activeUserId) return;
        const message = messageInput.value.trim();
        // Always get the file from the input at submit time
        const file = composer.querySelector('input[type="file"]').files[0];
        if (!message && !file) return;
        sendButton.disabled = true;
        const formData = new FormData();
        formData.append('receiver_id', activeUserId);
        formData.append('message', message);
        if (file) formData.append('file', file);
        formData.append('_token', csrfToken);
        const response = await fetch('/private-chat/send', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        });
        if (response.ok) {
            messageInput.value = '';
            fileInput.value = '';
            filePreview.style.display = 'none';
            filePreview.textContent = '';
            composer.reset && composer.reset();
            await fetchMessages(activeUserId); // Only one call, no setTimeout
        }
        sendButton.disabled = false;
    });

    // Poll for new messages every 3 seconds if a user is selected
    setInterval(() => {
        if (activeUserId) fetchMessages(activeUserId);
    }, 3000);

    // Initial load
    fetchOnlineUsers();
    // Optionally poll for online users
    setInterval(fetchOnlineUsers, 15000);
});
