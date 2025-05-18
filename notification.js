// notification.js
// جلب وعرض الإشعارات في الهيدر

document.addEventListener('DOMContentLoaded', function() {
    const bell = document.querySelector('.notification-bell');
    const notifCount = document.querySelector('.notification-count');
    let notifMenu = document.getElementById('notifMenu');

    if (!bell) return;

    // إنشاء قائمة الإشعارات إذا لم تكن موجودة
    if (!notifMenu) {
        notifMenu = document.createElement('div');
        notifMenu.id = 'notifMenu';
        notifMenu.className = 'notif-menu';
        notifMenu.style.position = 'absolute';
        notifMenu.style.top = '120%';
        notifMenu.style.right = '0';
        notifMenu.style.background = '#fff';
        notifMenu.style.minWidth = '220px';
        notifMenu.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
        notifMenu.style.borderRadius = '8px';
        notifMenu.style.zIndex = '1004';
        notifMenu.style.display = 'none';
        bell.parentElement.appendChild(notifMenu);
    }

    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        if (notifMenu.style.display === 'block') {
            notifMenu.style.display = 'none';
            return;
        }
        fetch('get_notifications.php')
            .then(res => res.json())
            .then(data => {
                notifMenu.innerHTML = '';
                if (data.length === 0) {
                    notifMenu.innerHTML = '<div style="padding:1rem;">لا توجد إشعارات جديدة</div>';
                } else {
                    data.forEach(n => {
                        const item = document.createElement('div');
                        item.className = 'notif-item' + (n.is_read == 0 ? ' unread' : '');
                        item.style.padding = '0.7rem 1rem';
                        item.style.cursor = 'pointer';
                        item.style.borderBottom = '1px solid #f0f0f0';
                        item.innerHTML = `<div>${n.message}</div><small style='color:#888;'>${n.created_at}</small>`;
                        item.onclick = function() {
                            fetch('mark_notification_read.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: 'id=' + n.id
                            }).then(() => {
                                if (n.link) window.location = n.link;
                                else item.classList.remove('unread');
                            });
                        };
                        notifMenu.appendChild(item);
                    });
                }
                notifMenu.style.display = 'block';
            });
    });

    // إغلاق القائمة عند الضغط خارجها
    document.addEventListener('click', function() {
        notifMenu.style.display = 'none';
    });
});
