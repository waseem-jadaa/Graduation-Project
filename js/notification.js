// notification.js
// جلب وعرض الإشعارات في الهيدر

document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notificationBell');
    const notifDropdown = document.getElementById('notificationDropdown');
    const notifList = document.getElementById('notificationList');
    const notifTabs = document.querySelectorAll('.notif-tab');
    let notificationsData = [];
    let currentTab = 'all';

    if (!bell || !notifDropdown || !notifList) return;

    function formatTime(dateString) {
        const now = new Date();
        const notifDate = new Date(dateString.replace(' ', 'T'));
        const diffMs = now - notifDate;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);
        if (diffDay > 3) {
            // إذا تجاوزت المدة 3 أيام، أظهر التاريخ والوقت بصيغة 12 ساعة مع AM/PM
            return notifDate.toLocaleString('ar-EG', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        } else if (diffDay >= 1) {
            return `منذ ${diffDay} يوم${diffDay === 1 ? '' : 's'}`;
        } else if (diffHour >= 1) {
            return `منذ ${diffHour} ساعة`;
        } else if (diffMin >= 1) {
            return `منذ ${diffMin} دقيقة`;
        } else {
            return 'الآن';
        }
    }

    function updateNotifCount() {
        const notifCount = document.querySelector('.notification-count');
        const unreadCount = notificationsData.filter(n => n.is_read == 0).length;
        if (notifCount) {
            if (unreadCount > 0) {
                notifCount.textContent = unreadCount;
                notifCount.style.display = 'flex';
            } else {
                notifCount.style.display = 'none';
            }
        }
    }

    function renderNotifications(tab = 'all') {
        notifList.innerHTML = '';
        let filtered = notificationsData;
        if (tab === 'unread') {
            filtered = notificationsData.filter(n => n.is_read == 0);
        }
        if (filtered.length === 0) {
            notifList.innerHTML = '<div style="padding:20px;text-align:center;color:#888;">لا توجد إشعارات</div>';
            updateNotifCount();
            return;
        }
        filtered.forEach(n => {
            const item = document.createElement('div');
            item.className = 'notification-item' + (n.is_read == 0 ? ' unread' : ' read');
            // صورة المرسل
            // إذا كان الإشعار من الأدمن (sender_id == 25) استخدم صورة الأدمن
            let avatar = (n.sender_id == 25)
                ? 'PG/admin_photo.png'
                : (n.photo || n.sender_photo);
            item.innerHTML = `
                <div style=\"display:flex;align-items:flex-start;justify-content:space-between;width:100%;\">
                  <div style=\"flex:1;display:flex;align-items:flex-start;gap:12px;\">
                    <img src=\"${avatar}\" class=\"notification-avatar\" alt=\"avatar\">
                    <div class=\"notification-content\">
                      <span class=\"notification-text\">${n.message}</span>
                      <div class=\"notification-meta\">
                        <span class=\"notification-time\">${formatTime(n.created_at)}</span>
                      </div>
                    </div>
                  </div>
                  <div class=\"notif-actions\" style=\"position:relative;\">
                    <button class=\"notif-menu-btn\" title=\"خيارات\" style=\"background:none;border:none;cursor:pointer;font-size:20px;padding:0 4px;\">&#8942;</button>
                    <div class=\"notif-menu-popup\" style=\"display:none;position:absolute;left:0;top:28px;background:#fff;border:1px solid #eee;border-radius:6px;box-shadow:0 2px 8px #0001;z-index:10;min-width:90px;\">
                      <button class=\"notif-delete-btn\" style=\"background:none;border:none;color:#e74c3c;padding:8px 16px;width:100%;text-align:right;cursor:pointer;\">حذف الإشعار</button>
                    </div>
                  </div>
                </div>
                <span class=\"notif-dot\"></span>
            `;
            // حذف اسم FursaPal نهائياً
            // منطق حذف الإشعار
            const menuBtn = item.querySelector('.notif-menu-btn');
            const menuPopup = item.querySelector('.notif-menu-popup');
            const deleteBtn = item.querySelector('.notif-delete-btn');
            menuBtn.onclick = function(e) {
                e.stopPropagation();
                // إغلاق جميع القوائم الأخرى أولاً
                document.querySelectorAll('.notif-menu-popup').forEach(p => { if(p!==menuPopup) p.style.display='none'; });
                menuPopup.style.display = menuPopup.style.display === 'block' ? 'none' : 'block';
            };
            deleteBtn.onclick = function(e) {
                e.stopPropagation();
                fetch('delete_notification.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + n.id
                }).then(res => res.json()).then(resp => {
                    if(resp.success) item.remove();
                });
            };
            // إغلاق القائمة عند الضغط خارجها
            document.addEventListener('click', function(e) {
                if(menuPopup && !menuPopup.contains(e.target) && e.target !== menuBtn) {
                    menuPopup.style.display = 'none';
                }
            });
            item.onclick = function() {
                fetch('mark_notification_read.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + n.id
                }).then(() => {
                    // Always mark as read after click
                    n.is_read = 1;
                    item.classList.remove('unread');
                    updateNotifCount();

                    // Redirect logic
                    if (n.message.includes('تم إغلاق الوظيفة') || n.message.includes('تم تحديث تفاصيل الوظيفة')) {
                        // For job closed/updated notifications, go to the main jobs page
                        window.location = '/forsa-pal/jobs.php';
                    } else if (n.link) {
                        // For other notifications, go to the specified link
                        window.location = n.link;
                    }
                });
            };
            notifList.appendChild(item);
        });
        updateNotifCount();
    }

    function fetchNotifications() {
        notifList.innerHTML = '<div style="padding:20px;text-align:center;color:#888;">جاري التحميل...</div>';
        fetch('get_notifications.php')
            .then(res => res.json())
            .then(data => {
                notificationsData = data;
                renderNotifications(currentTab);
                updateNotifCount();
            });
    }

    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        notifDropdown.style.display = notifDropdown.style.display === 'block' ? 'none' : 'block';
        if (notifDropdown.style.display === 'block') {
            fetchNotifications();
        }
        // أغلق قائمة الرسائل إذا كانت مفتوحة
        const messagesDropdown = document.getElementById('messagesDropdown');
        if (messagesDropdown) messagesDropdown.style.display = 'none';
    });

    notifTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.stopPropagation();
            notifTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentTab = tab.dataset.tab;
            renderNotifications(currentTab);
        });
    });

    // زر عرض الكل (يمكنك ربطه بصفحة إشعارات كاملة لاحقاً)
    const showAllBtn = document.querySelector('.notif-show-all');
    if (showAllBtn) {
        showAllBtn.onclick = function() {
            window.location = 'notifications.php'; // عدل الرابط حسب الحاجة
        };
    }

    // زر تمييز الكل كمقروءة
    const markAllBtn = document.querySelector('.notif-mark-all');
    if (markAllBtn) {
        markAllBtn.onclick = function(e) {
            e.stopPropagation();
            fetch('mark_all_notifications_read.php', { method: 'POST' })
                .then(res => res.json())
                .then(() => {
                    notificationsData = notificationsData.map(n => ({...n, is_read: 1}));
                    renderNotifications(currentTab);
                    updateNotifCount();
                });
        };
    }

    // إغلاق القائمة عند الضغط خارجها
    document.addEventListener('mousedown', function(e) {
        if (
            notifDropdown.style.display === 'block' &&
            !notifDropdown.contains(e.target) &&
            !bell.contains(e.target)
        ) {
            // استثناء خاص إذا كان الضغط على .notif-header أو أحد أبنائه
            if (e.target.closest('.notif-header')) return;
            notifDropdown.style.display = 'none';
        }
    });
}); 