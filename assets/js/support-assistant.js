document.addEventListener('DOMContentLoaded', function() {
    var chatToggles = document.querySelectorAll('.chat-toggle, .close-chat-sm');
    chatToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            // Toggle chat icon visibility
            var chatToggle = document.querySelector('.chat-toggle');
            if (chatToggle) {
                var chatIcon = chatToggle.querySelector('.wp-spotlight-chat');
                var hideIcon = chatToggle.querySelector('.wp-spotlight-hide');
                if (chatIcon) chatIcon.style.display = (chatIcon.style.display === 'none' ? 'block' : 'none');
                if (hideIcon) hideIcon.style.display = (hideIcon.style.display === 'none' ? 'block' : 'none');
            }

            // Toggle show-chatbox class
            if (chatToggle) chatToggle.classList.toggle('show-chatbox');
            var closeChatSm = document.querySelector('.close-chat-sm');
            if (closeChatSm) closeChatSm.classList.toggle('show-chatbox');

            // Toggle iframe visibility
            var chatboxWrapper = document.querySelector('.chatbox-iframe-wraper');
            if (chatboxWrapper) {
                chatboxWrapper.classList.toggle('show-chatbox');
                if (!chatboxWrapper.classList.contains('show-chatbox')) {
                    chatboxWrapper.classList.remove('extended');
                }
            }
        });
    });

    window.addEventListener('message', function(event) {
        var data = event.data;
        var chatboxWrapper = document.querySelector('.chatbox-iframe-wraper');
        var chatToggle = document.querySelector('.chat-toggle');
        if (!chatboxWrapper) return;
        if (data === 'toggle_assistant_triggered') {
            chatboxWrapper.classList.toggle('show-chatbox');
            chatToggle.classList.toggle('show-chatbox');
            if (!chatboxWrapper.classList.contains('show-chatbox')) {
                chatboxWrapper.classList.remove('extended');
            }
        } else if (data === 'toggle_assistant_extend_triggered') {
            chatboxWrapper.classList.toggle('extended');
        } else if (data === 'toggle_assistant_exit_extend') {
            chatboxWrapper.classList.remove('extended');
        }
    });
});
