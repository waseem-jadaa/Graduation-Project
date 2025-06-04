document.addEventListener('DOMContentLoaded', function() {
    const messagesBell = document.getElementById('messagesBell');
    const messagesCount = document.getElementById('messagesCount');
    const messagesDropdown = document.getElementById('messagesDropdown');
    const notificationBell = document.getElementById('notificationBell');

    if (!messagesBell || !messagesDropdown) return;

    function formatTimeAgo(date) {
        const now = new Date();
        const messageDate = new Date(date);
        const diffInSeconds = Math.floor((now - messageDate) / 1000);
        if (diffInSeconds < 60) return 'الآن';
        if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' دقيقة';
        if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' ساعة';
        if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 86400) + ' يوم';
        if (diffInSeconds < 31536000) return Math.floor(diffInSeconds / 2592000) + ' شهر';
        return Math.floor(diffInSeconds / 31536000) + ' سنة';
    }

    // Function to fetch and update message count and dropdown content
    function fetchAndUpdateMessages() {
        fetch('get_recent_messages.php')
            .then(res => res.json())
            .then(data => {
                // Update messages count
                if (data.unreadCount > 0) {
                    messagesCount.textContent = data.unreadCount;
                    messagesCount.style.display = 'flex';
                    messagesBell.classList.add('has-unread');
                } else {
                    messagesCount.style.display = 'none';
                    messagesBell.classList.remove('has-unread');
                }
                // Build messages list HTML (only update dropdown if it's open)
                if (messagesDropdown.style.display === 'block') {
                    let html = `
                        <div class="messages-header">
                            <span class="messages-title">الرسائل</span>
                            <a href="messages.php" class="see-all-messages">عرض الكل</a>
                        </div>
                    `;
                    if (!data.messages || data.messages.length === 0) {
                        html += '<div class="messages-empty">لا توجد رسائل</div>';
                    } else {
                        html += data.messages.map(message => `
                            <div class="message-item ${!message.is_read ? 'unread' : ''}" 
                                 onclick="window.location.href='messages.php?user=${message.from_id}'">
                                <img src="${message.sender_photo || 'image/p.png'}" 
                                     alt="${message.sender_name}" 
                                     class="message-avatar">
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">${message.sender_name}</span>
                                        <span class="message-time">${formatTimeAgo(message.sent_at)}</span>
                                    </div>
                                    <div class="message-preview">${message.content}</div>
                                </div>
                            </div>
                        `).join('');
                    }
                    messagesDropdown.innerHTML = html;
                }
            })
            .catch(() => {
                 // Only show error in dropdown if it's open
                 if (messagesDropdown.style.display === 'block') {
                    messagesDropdown.innerHTML = '<div class="messages-empty">حدث خطأ في تحميل الرسائل</div>';
                 }
            });
    }

    // Fetch and update messages when the page loads
    fetchAndUpdateMessages();

    messagesBell.addEventListener('click', function(e) {
        e.stopPropagation();
        
        // أغلق جميع قوائم الإشعارات أولاً
        const notifMenu = document.getElementById('notifMenu');
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown) notificationDropdown.style.display = 'none';
        if (notifMenu) notifMenu.style.display = 'none';

        // تبديل حالة قائمة الرسائل
        if (messagesDropdown.style.display === 'block') {
            messagesDropdown.style.display = 'none';
            return;
        }

        // جلب وعرض الرسائل الجديدة (and update count)
        fetchAndUpdateMessages();
        messagesDropdown.style.display = 'block'; // Open the dropdown
    });

    // مستمع واحد فقط لإغلاق القوائم عند النقر خارجها
    document.addEventListener('click', function(e) {
        const notifMenu = document.getElementById('notifMenu');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const isMessagesOpen = messagesDropdown.style.display === 'block';
        const clickedInsideMessages = messagesDropdown.contains(e.target);
        const clickedMessagesBell = messagesBell.contains(e.target);
        const clickedNotificationBell = notificationBell && notificationBell.contains(e.target);

        // عند النقر على أيقونة الرسائل
        if (clickedMessagesBell) return;

        // عند النقر على أيقونة الإشعارات
        if (clickedNotificationBell) {
            // أغلق قائمة الرسائل
            messagesDropdown.style.display = 'none';
            return;
        }

        // إذا تم النقر خارج قائمة الرسائل
        if (isMessagesOpen && !clickedInsideMessages) {
            messagesDropdown.style.display = 'none';
        }
    });

    // إغلاق القائمة عند الضغط على ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && messagesDropdown.style.display === 'block') {
            messagesDropdown.style.display = 'none';
        }
    });
    // إضافة التأثيرات البصرية للرسائل غير المقروءة
  const style = document.createElement('style');
  style.innerHTML = `
    .messages-bell.has-unread i {
      color: #1abc5b !important;
      animation: bell-shake 0.7s cubic-bezier(.36,.07,.19,.97) both;
    }
    @keyframes bell-shake {
      10%, 90% { transform: rotate(-10deg); }
      20%, 80% { transform: rotate(12deg); }
      30%, 50%, 70% { transform: rotate(-15deg); }
      40%, 60% { transform: rotate(15deg); }
    }
    .messages-count {
      background: #1abc5b !important;
      color: #fff !important;
      border-radius: 50% !important;
      min-width: 22px !important;
      height: 22px !important;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 13px;
      position: absolute;
      top: -8px;
      right: -8px;
      z-index: 2;
    }
    .message-item.unread {
      background-color: #f0f0f0; /* تظليل خفيف للرسائل غير المقروءة */
    }
  `;
  document.head.appendChild(style);
});
