document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('live-chat-root');
    if (!root) {
        return;
    }

    var toggleBtn = root.querySelector('[data-role="toggle"]');
    var panel = root.querySelector('[data-role="panel"]');
    var closeBtn = root.querySelector('[data-role="close"]');

    if (!toggleBtn || !panel || !closeBtn) {
        return;
    }

    toggleBtn.addEventListener('click', function () {
        panel.hidden = false;
        toggleBtn.style.display = 'none';
    });

    closeBtn.addEventListener('click', function () {
        panel.hidden = true;
        toggleBtn.style.display = '';
    });
});
