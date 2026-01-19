@php
    $user = Auth::user();
@endphp
@if($user && $user->profile_created)
    <div
        id="live-chat-root"
        class="live-chat-root"
        data-bootstrap-url="{{ route('chat.bootstrap') }}"
        data-send-url="{{ route('chat.send') }}"
        data-typing-url="{{ route('chat.typing') }}"
        data-online-url-template="/chat/group/__GROUP_ID__/online-users"
        data-messages-url-template="/chat/group/__GROUP_ID__/messages"
        data-user-id="{{ $user->id }}"
        data-user-name="{{ $user->name }}"
    >
        <button type="button" class="live-chat-toggle" data-role="toggle" aria-label="Open internal chat">
            <span class="live-chat-pill">Chat</span>
        </button>
        <div class="live-chat-panel" data-role="panel" hidden>
            <div class="live-chat-header">
                <div>
                    <strong>Team Chat</strong>
                </div>
                <button type="button" class="live-chat-close" data-role="close" aria-label="Close chat">×</button>
            </div>
            <div class="live-chat-body">
                <aside class="live-chat-sidebar">
                    <div class="live-chat-section">
                        <h6>Online</h6>
                        <ul data-role="online-list" class="live-chat-list"></ul>
                    </div>
                </aside>
                <section class="live-chat-content">
                    <div class="live-chat-messages" data-role="messages"></div>
                    <div class="live-chat-typing" data-role="typing" hidden></div>
                    <form class="live-chat-composer" data-role="composer">
                        <!-- group_id removed: direct chat only -->
                        <input type="text" name="message" placeholder="Type a message" autocomplete="off">
                        <input type="file" name="attachment" class="live-chat-file" hidden>
                        <button type="button" class="live-chat-attach" data-role="attach" title="Attach file">📎</button>
                        <button type="submit" class="live-chat-send" title="Send message">➤</button>
                    </form>
                </section>
            </div>
        </div>
    </div>
    <style>
        .live-chat-root {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 1600;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .live-chat-toggle {
            background: #0d6efd;
            color: #fff;
            border-radius: 999px;
            border: none;
            padding: 12px 20px;
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.25);
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .live-chat-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(13, 110, 253, 0.35);
        }
        .live-chat-panel {
            width: min(420px, calc(100vw - 32px));
            height: min(520px, calc(100vh - 96px));
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .live-chat-header {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .live-chat-close {
            background: rgba(255, 255, 255, 0.18);
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            color: #fff;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
        }
        .live-chat-body {
            display: flex;
            flex: 1;
            min-height: 0;
        }
        .live-chat-sidebar {
            width: 140px;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .live-chat-section h6 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #334155;
            margin-bottom: 8px;
        }
        .live-chat-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 180px;
            overflow-y: auto;
        }
        .live-chat-list li {
            padding: 6px 8px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #0f172a;
        }
        .live-chat-list li[data-active="true"] {
            background: #e0e7ff;
            font-weight: 600;
        }
        .live-chat-list li span.dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            display: inline-block;
        }
        .live-chat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .live-chat-messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            background: #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .live-chat-message {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
            position: relative;
            font-size: 14px;
        }
        .live-chat-message[data-mine="true"] {
            margin-left: auto;
            background: #0d6efd;
            color: #fff;
        }
        .live-chat-meta {
            font-size: 11px;
            color: rgba(15, 23, 42, 0.6);
            margin-top: 6px;
        }
        .live-chat-message[data-mine="true"] .live-chat-meta {
            color: rgba(255, 255, 255, 0.75);
        }
        .live-chat-files a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: inherit;
            text-decoration: underline;
        }
        .live-chat-typing {
            padding: 0 16px 12px;
            font-size: 12px;
            color: #64748b;
        }
        .live-chat-composer {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: #fff;
            border-top: 1px solid #e2e8f0;
        }
        .live-chat-composer input[type="text"] {
            flex: 1;
            border: 1px solid #cbd5f5;
            border-radius: 999px;
            padding: 8px 14px;
            background: #f8fafc;
        }
        .live-chat-send,
        .live-chat-attach {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
        }
        @media (max-width: 640px) {
            .live-chat-root {
                right: 12px;
                bottom: 12px;
            }
            .live-chat-panel {
                width: calc(100vw - 24px);
                height: calc(100vh - 120px);
            }
            .live-chat-sidebar {
                display: none;
            }
        }
    </style>
@endif
