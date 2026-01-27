<?php
    $user = Auth::user();
?>
<?php if($user && $user->profile_created): ?>
    <div
        id="live-chat-root"
        class="live-chat-root"
        data-bootstrap-url="<?php echo e(route('chat.bootstrap')); ?>"
        data-send-url="<?php echo e(route('chat.send')); ?>"
        data-typing-url="<?php echo e(route('chat.typing')); ?>"
        data-online-url-template="/chat/group/__GROUP_ID__/online-users"
        data-messages-url-template="/chat/group/__GROUP_ID__/messages"
        data-user-id="<?php echo e($user->id); ?>"
        data-user-name="<?php echo e($user->name); ?>"
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
                        <ul data-role="user-list" class="live-chat-list"></ul>
                    </div>
                </aside>
                <section class="live-chat-content">
                    <div class="live-chat-messages" data-role="messages"></div>
                    <div class="live-chat-typing" data-role="typing" hidden></div>
                    <form class="live-chat-composer" data-role="composer">
                        <!-- group_id removed: direct chat only -->
                        <!-- <input type="text" name="message" placeholder="Type a message" autocomplete="off">
                        <input type="file" name="attachment" class="live-chat-file" accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt" hidden>
                        <button type="button" class="live-chat-attach" data-role="attach" title="Attach file">📎</button>
                        <span class="live-chat-file-preview" style="display:none;"></span>
                        <button type="submit" class="live-chat-send" title="Send message">➤</button> -->
                        <button type="button" class="live-chat-plus" title="More options">＋</button>
<button type="button" class="live-chat-emoji" title="Emoji">😊</button>
<input type="text" name="message" placeholder="Type a message" autocomplete="off">
<input type="file" name="file" class="live-chat-file" accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt" hidden>
<button type="button" class="live-chat-attach" data-role="attach" title="Attach file">📎</button>
<span class="live-chat-file-preview" style="display:none;"></span>
<button type="button" class="live-chat-mic" title="Voice message">🎤</button>
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
        .live-chat-list li.private-chat-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .live-chat-list li.private-chat-user:hover,
        .live-chat-list li.private-chat-user.selected {
            background: #e0e7ff;
            font-weight: 600;
        }
        .live-chat-list li.private-chat-user .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #22c55e;
            display: inline-block;
        }
        .live-chat-list li.private-chat-user[data-online="true"] {
            font-weight: 600;
            color: #22c55e !important;
        }
        .live-chat-list li.private-chat-user[data-online="true"]::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e !important;
            margin-right: 6px;
            vertical-align: middle;
        }
        .live-chat-list li.private-chat-user[data-online="false"] {
            color: #222 !important;
        }
        .live-chat-list li.private-chat-user[data-online="false"]::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #bbb !important;
            margin-right: 6px;
            vertical-align: middle;
        }
            .live-chat-list li.private-chat-user[data-online="true"] .last-seen {
                display: none;
            }
            .live-chat-list li.private-chat-user[data-online="false"] .last-seen {
                display: inline;
                font-size: 11px;
                color: #888;
                margin-left: 4px;
            }
        .live-chat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            height: 100%;
        }
        .live-chat-messages {
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            flex: 1;
            min-height: 80px;
            max-height: 320px;
            background: #f1f5f9;
            border-radius: 0 0 12px 12px;
        }
        .private-chat-message {
            max-width: 70%;
            padding: 10px 16px;
            border-radius: 18px;
            background: #f1f5f9;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
            position: relative;
            font-size: 15px;
            margin-bottom: 10px;
            clear: both;
            word-break: break-word;
            min-height: 36px;
            display: flex;
            align-items: flex-end;
        }
        .private-chat-message.mine {
            margin-left: auto;
            margin-right: 8px;
            background: #e3f0ff;
            color: #222;
            text-align: right;
            border-bottom-right-radius: 8px;
            border-bottom-left-radius: 18px;
            border-top-right-radius: 18px;
            border-top-left-radius: 18px;
            float: right;
        }
        .private-chat-message.theirs {
            background: #fff;
            color: #222;
            margin-right: auto;
            margin-left: 8px;
            text-align: left;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 18px;
            border-top-right-radius: 18px;
            border-top-left-radius: 18px;
            float: left;
        }
        .private-chat-sender {
            font-size: 13px;
            color: #222;
            font-weight: 600;
            margin-bottom: 2px;
            margin-right: 8px;
        }
        .private-chat-meta {
            font-size: 12px;
            color: #888;
            margin-left: 8px;
            margin-right: 8px;
            margin-bottom: 2px;
            display: inline-block;
        }
        .private-chat-message.mine .private-chat-meta {
            text-align: right;
            float: right;
        }
        .private-chat-message.theirs .private-chat-meta {
            text-align: left;
            float: left;
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
            min-height: 56px;
        }
        .live-chat-composer input[type="text"] {
            flex: 1;
            border: 1px solid #cbd5f5;
            border-radius: 999px;
            padding: 8px 14px;
            background: #f8fafc;
            min-height: 38px;
            font-size: 15px;
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

        .live-chat-plus, .live-chat-emoji, .live-chat-mic {
    border: none;
    background: none;
    cursor: pointer;
    font-size: 20px;
    margin-right: 2px;
}
.live-chat-list li.private-chat-user[data-online="true"] {
    font-weight: 600;
    color: #22c55e !important;
    background: #eaffea;
}
.live-chat-list li.private-chat-user.selected {
    background: #c7d2fe !important;
    font-weight: 700;
    color: #1e40af !important;
}
.live-chat-list li.private-chat-user.selected[data-online="true"] {
    background: #b6f4c6 !important;
    color: #166534 !important;
}

    </style>
<?php endif; ?>
<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('public/js/private-chat.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\layouts\partials\internal-chat.blade.php ENDPATH**/ ?>