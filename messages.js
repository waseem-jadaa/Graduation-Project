
// messages.js
let selectedUserName = '';
let selectedUserImg = 'image/p.png';
let currentUserId = null;
const usersList = document.getElementById('usersList');
const chatsList = document.getElementById('chatsList');
const chatHeader = document.getElementById('chatHeader');
const chatMessages = document.getElementById('chatMessages');
const chatForm = document.getElementById('chatForm');
const messageInput = document.getElementById('messageInput');
const userSearch = document.getElementById('userSearch');
const sendBtn = chatForm.querySelector('button[type="submit"]');

// --- فتح محادثة تلقائياً إذا كان هناك user في الرابط ---
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const userIdFromUrl = urlParams.get('user');
    if (userIdFromUrl) {
        // ابحث عن اسم وصورة المستخدم من قائمة المستخدمين (usersList)
        let userName = '';
        let userImg = 'image/p.png';
        const userLi = document.querySelector(`#usersList li[data-user-id="${userIdFromUrl}"]`);
        if (userLi) {
            userName = userLi.querySelector('span') ? userLi.querySelector('span').textContent.trim() : '';
            userImg = userLi.querySelector('img') ? userLi.querySelector('img').src : 'image/p.png';
            userLi.classList.add('active');
        }
        currentUserId = userIdFromUrl;
        selectedUserName = userName;
        selectedUserImg = userImg;
        setChatHeader(userName, userImg);
        chatForm.style.display = 'flex';
        loadMessages(userIdFromUrl, userName, userImg);
        // مرر الرسائل للأسفل بعد التحميل (يتم ذلك تلقائياً في loadMessages)
    }
});

// فلترة المستخدمين
userSearch.addEventListener('input', function() {
    const val = this.value.trim();
    document.querySelectorAll('#usersList ul li').forEach(li => {
        li.style.display = li.textContent.includes(val) ? '' : 'none';
    });
});

// تفعيل زر الإرسال فقط عند وجود نص
messageInput.addEventListener('input', function() {
    sendBtn.disabled = !this.value.trim();
});

// اختيار مستخدم
usersList.addEventListener('click', function(e) {
    const li = e.target.closest('li[data-user-id]');
    if (!li) return;
    document.querySelectorAll('#usersList li').forEach(l => l.classList.remove('active'));
    li.classList.add('active');
    const userId = li.getAttribute('data-user-id');
    // استخراج اسم المستخدم فقط من العنصر span
    let userName = li.querySelector('span') ? li.querySelector('span').textContent.trim() : '';
    // استخراج صورة المستخدم
    let userImg = li.querySelector('img') ? li.querySelector('img').src : 'image/p.png';
    currentUserId = userId;
    selectedUserName = userName;
    selectedUserImg = userImg;
    setChatHeader(userName, userImg);
    chatForm.style.display = 'flex';
    // جلب الرسائل، وإذا لم توجد رسائل، عرض واجهة فارغة مع اسم وصورة المستخدم
    chatMessages.innerHTML = '<div style="text-align:center;color:#888;">جاري التحميل...</div>';
    fetch('messages_api.php?user_id=' + encodeURIComponent(userId))
        .then(res => {
            if (!res.ok) throw new Error('network');
            return res.json();
        })
        .then(data => {
            chatMessages.innerHTML = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(msg => {
                    const row = document.createElement('div');
                    row.className = 'message-row ' + (msg.sent_by_me ? 'sent' : 'received');
                    row.innerHTML = `
                        <div style="display:flex;align-items:flex-end;gap:8px;${msg.sent_by_me ? 'flex-direction:row-reverse;' : ''}">
                            <img src="${msg.profile_photo || 'image/p.png'}" alt="avatar" class="msg-avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #eee;box-shadow:0 1px 4px #0001;">
                            <div>
                                <div class=\"message-bubble\">${msg.message}</div>
                                <div class=\"message-meta\">
                                    <span class=\"message-time\">${msg.time}</span><br>
                                    <span class=\"message-sender\">${msg.sender_name}</span>
                                    ${msg.sent_by_me ? `
                                        <div class=\"message-seen-status ${msg.seen ? '' : 'unseen'}\">
                                            <i class=\"fas fa-check-double\"></i>
                                            ${msg.seen ? 'تم القراءة' : 'تم الإرسال'}
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    chatMessages.appendChild(row);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            } else {
                // لا توجد رسائل بعد، عرض واجهة فارغة مع صورة واسم المستخدم
                chatMessages.innerHTML = `<div style=\"text-align:center;color:#888;padding:40px 0;\">
                    <img src=\"${userImg}\" alt=\"avatar\" class=\"msg-avatar\" style=\"width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid #eee;box-shadow:0 1px 4px #0001;display:block;margin:0 auto 10px;\">
                    <div style=\"font-size:1.1em;\">${userName}</div>
                    <div style=\"margin-top:10px;\">لا توجد رسائل بعد. ابدأ المحادثة الآن!</div>
                </div>`;
            }
        })
        .catch(() => {
            chatMessages.innerHTML = '<div style="text-align:center;color:#e74c3c;">تعذر تحميل الرسائل.</div>';
        });
    // لا تضف للمحادثات إلا عند إرسال رسالة
    // إغلاق قائمة المستخدمين في الوضع المتنقل
    if (window.innerWidth <= 768) {
        usersList.style.display = 'none';
    }
});

// اختيار محادثة من recent chats
chatsList.addEventListener('click', function(e) {
    const li = e.target.closest('li[data-user-id]');
    if (!li) return;
    document.querySelectorAll('#chatsList li').forEach(l => l.classList.remove('active'));
    li.classList.add('active');
    const userId = li.getAttribute('data-user-id');
    // استخراج اسم المستخدم فقط من العنصر span
    let userName = li.querySelector('span') ? li.querySelector('span').textContent.trim() : '';
    let userImg = li.querySelector('img') ? li.querySelector('img').src : 'image/p.png';
    currentUserId = userId;
    setChatHeader(userName, userImg);
    chatForm.style.display = 'flex';
    loadMessages(userId, userName, userImg);
});

// تحميل المحادثات الأخيرة من السيرفر وعرضها في قائمة المحادثات
function loadChats() {
    fetch('messages_api.php?recent=1')
        .then(res => res.json())
        .then(data => {
            const ul = chatsList.querySelector('ul');
            ul.innerHTML = '';
            if (!data.length) {
                ul.innerHTML = '<li style="color:#888;text-align:center;">لا توجد محادثات</li>';
                return;
            }
            data.forEach(chat => {
                const isUnread = chat.seen == 0 && chat.sender_ID != currentUserId;
                const lastMsg = chat.content ? chat.content.substring(0, 30) : '';
                let time = '';
                if (chat.created_at) {
                    const dateObj = new Date(chat.created_at.replace(' ', 'T'));
                    let hours = dateObj.getHours();
                    const minutes = dateObj.getMinutes().toString().padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // 0 => 12
                    time = hours + ':' + minutes + ' ' + ampm;
                }
                ul.innerHTML += `<li data-user-id="${chat.user_id}" class="${isUnread ? 'unread-chat' : ''}">
                    <div style="display:flex;flex-direction:column;gap:5px;width:100%;position:relative;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="${chat.profile_photo || 'image/p.png'}" alt="avatar" class="msg-avatar" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #eee;box-shadow:0 1px 4px #0001;">
                            <span style="font-weight:bold;font-size:1.05em;max-width:120px;display:flex;flex-direction:column;line-height:1.1;align-items:flex-start;">
                                ${(() => {
                                    const nameParts = chat.name.trim().split(' ');
                                    if (chat.name.length > 16 && nameParts.length > 1) {
                                        return `<span style=\"font-weight:bold;font-size:1.05em;display:flex;flex-direction:column;line-height:1.1;align-items:flex-start;max-width:120px;\">\n                                            <span>${nameParts[0]}</span>\n                                            <span style=\"color:#888;font-size:0.98em;\">${nameParts.slice(1).join(' ')}</span>\n                                        </span>`;
                                    } else {
                                        return `<span style=\"font-weight:bold;font-size:1.05em;white-space:normal;overflow:hidden;text-overflow:ellipsis;vertical-align:middle;max-width:120px;display:inline-block;\" title=\"${chat.name}\">\n                                            ${chat.name}\n                                        </span>`;
                                    }
                                })()}
                            </span>
                            <div class="chat-options-menu" style="margin-right:auto;position:relative;">
                                <button class="chat-options-btn" title="خيارات" style="background:none;border:none;cursor:pointer;padding:4px 8px;font-size:1.3em;line-height:1;display:flex;align-items:center;">
                                    <span style="display:inline-block;font-size:1.5em;vertical-align:middle;">&#8942;</span>
                                </button>
                                <div class="chat-options-dropdown" style="display:none;position:absolute;left:0;top:120%;background:#fff;border:1px solid #eee;box-shadow:0 2px 8px #0002;border-radius:8px;z-index:10;min-width:120px;">
                                    <div class="delete-chat-btn" style="padding:10px 18px;cursor:pointer;color:#e74c3c;font-size:1em;white-space:nowrap;">حذف المحادثة</div>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <span class="last-msg" style="color:#444;font-size:0.97em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${lastMsg}</span>
                            <span class="msg-time" style="color:#888;font-size:0.93em;">${time}</span>
                            ${isUnread ? '<span class="unread-dot"></span>' : 
                              chat.sender_ID == currentUserId ? 
                                `<span class="message-seen-status ${chat.seen ? '' : 'unseen'}">
                                    <i class="fas fa-check-double"></i>
                                </span>` : ''}
                        </div>
                    </div>
                </li>`;
            });
        });
}
window.addEventListener('DOMContentLoaded', loadChats);

// تحميل الرسائل
function loadMessages(userId) {
    // userName/userImg are optional for empty chat UI
    let userName = arguments[1] || '';
    let userImg = arguments[2] || 'image/p.png';
    chatMessages.innerHTML = '<div style="text-align:center;color:#888;">جاري التحميل...</div>';
    fetch('messages_api.php?user_id=' + encodeURIComponent(userId))
        .then(res => {
            if (!res.ok) throw new Error('network');
            return res.json();
        })
        .then(data => {
            chatMessages.innerHTML = '';
            if (Array.isArray(data)) {
                if (data.length === 0) {
                    if (userName) {
                        chatMessages.innerHTML = `<div style=\"text-align:center;color:#888;padding:40px 0;\">
                            <img src=\"${userImg}\" alt=\"avatar\" class=\"msg-avatar\" style=\"width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid #eee;box-shadow:0 1px 4px #0001;display:block;margin:0 auto 10px;\">
                            <div style=\"font-size:1.1em;\">${userName}</div>
                            <div style=\"margin-top:10px;\">لا توجد رسائل بعد. ابدأ المحادثة الآن!</div>
                        </div>`;
                    } else {
                        chatMessages.innerHTML = '<div style="text-align:center;color:#888;">لا توجد رسائل بعد.</div>';
                    }
                } else {
                    data.forEach(msg => {
                        const row = document.createElement('div');
                        row.className = 'message-row ' + (msg.sent_by_me ? 'sent' : 'received');
                        row.innerHTML = `
                            <div style="display:flex;align-items:flex-end;gap:8px;${msg.sent_by_me ? 'flex-direction:row-reverse;' : ''}">
                                <img src="${msg.profile_photo || 'image/p.png'}" alt="avatar" class="msg-avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #eee;box-shadow:0 1px 4px #0001;">
                                <div>
                                    <div class=\"message-bubble\">${msg.message}</div>
                                    <div class=\"message-meta\">
                                        <span class=\"message-time\">${msg.time}</span><br>
                                        <span class=\"message-sender\">${msg.sender_name}</span>
                                        ${msg.sent_by_me ? `
                                            <div class=\"message-seen-status ${msg.seen ? '' : 'unseen'}\">
                                                <i class=\"fas fa-check-double\"></i>
                                                ${msg.seen ? 'تم القراءة' : 'تم الإرسال'}
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                        chatMessages.appendChild(row);
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            } else {
                chatMessages.innerHTML = '<div style="text-align:center;color:#e74c3c;">تعذر تحميل الرسائل.</div>';
            }
        })
        .catch(() => {
            chatMessages.innerHTML = '<div style="text-align:center;color:#e74c3c;">تعذر تحميل الرسائل.</div>';
        });
}

// إرسال رسالة
chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const msg = messageInput.value.trim();
    if (!msg || !currentUserId) return;
    sendBtn.disabled = true;
    fetch('messages_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'to_user_id=' + encodeURIComponent(currentUserId) + '&message=' + encodeURIComponent(msg)
    })
    .then(res => {
        if (!res.ok) throw new Error('network');
        return res.json();
    })
    .then(data => {
        messageInput.value = '';
        sendBtn.disabled = false;
        if (data && data.success === true) {
            loadMessages(currentUserId, selectedUserName, selectedUserImg);
            addToChatsList(currentUserId, selectedUserName);
            if (typeof afterMessageSentOrReceived === 'function') afterMessageSentOrReceived();
        } // لا تظهر أي alert إذا لم يكن success، فقط تجاهل
    })
    .catch((err) => {
        sendBtn.disabled = false;
        if (err.message === 'network') {
            alert('تعذر الاتصال بالخادم. تحقق من الإنترنت أو أعد المحاولة لاحقاً.');
        } else {
            alert('تعذر إرسال الرسالة.');
        }
    });
});

// زر إظهار/إخفاء قائمة المستخدمين
const toggleUsersBtn = document.getElementById('toggleUsersBtn');
toggleUsersBtn.onclick = function() {
    usersList.style.display = usersList.style.display === 'none' ? '' : 'none';
};

// إضافة محادثة لقائمة المحادثات إذا لم تكن موجودة
function addToChatsList(userId, userName) {
    const ul = chatsList.querySelector('ul');
    if ([...ul.children].some(li => li.getAttribute('data-user-id') === userId)) return;
    const li = document.createElement('li');
    li.setAttribute('data-user-id', userId);
    li.innerHTML = `<i class="fas fa-user"></i> ${userName} <button class="delete-chat-btn" title="حذف المحادثة" style="float:left;background:none;border:none;color:#e74c3c;font-size:1em;cursor:pointer;"><i class="fas fa-trash"></i></button>`;
    ul.appendChild(li);
}

// خيارات المحادثة (ثلاث نقاط وحذف)
chatsList.addEventListener('click', function(e) {
    // فتح القائمة المنسدلة
    if (e.target.closest('.chat-options-btn')) {
        const btn = e.target.closest('.chat-options-btn');
        const menu = btn.parentElement.querySelector('.chat-options-dropdown');
        // إغلاق جميع القوائم الأخرى أولاً
        document.querySelectorAll('.chat-options-dropdown').forEach(d => { if (d !== menu) d.style.display = 'none'; });
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        e.stopPropagation();
        return;
    }
    // حذف المحادثة
    if (e.target.classList.contains('delete-chat-btn')) {
        const li = e.target.closest('li[data-user-id]');
        if (!li) return;
        const userId = li.getAttribute('data-user-id');
        if (confirm('هل تريد حذف هذه المحادثة نهائياً؟')) {
            fetch('messages_api.php?delete_chat=1&user_id=' + encodeURIComponent(userId), {method:'POST'})
                .then(res => res.json())
                .then(data => {
                    if (data.success) li.remove();
                });
        }
        // إغلاق القائمة بعد الحذف
        const menu = e.target.closest('.chat-options-dropdown');
        if (menu) menu.style.display = 'none';
        e.stopPropagation();
        return;
    }
});

// إغلاق القائمة المنسدلة عند النقر خارجها
document.addEventListener('click', function(e) {
    document.querySelectorAll('.chat-options-dropdown').forEach(d => d.style.display = 'none');
});

// تحديث عداد الرسائل غير المقروءة في الهيدر
function updateMessagesCount() {
    fetch('messages_api.php?unread_count=1')
        .then(res => res.json())
        .then(data => {
            const count = data.count || 0;
            const badge = document.getElementById('messagesCount');
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
            // تفعيل لون الأيقونة إذا كان هناك رسائل جديدة
            const bell = document.getElementById('messagesBell');
            if (bell) bell.classList.toggle('has-unread', count > 0);
        });
}
setInterval(updateMessagesCount, 10000);
window.addEventListener('DOMContentLoaded', updateMessagesCount);

// تحديث تلقائي للرسائل عند الطرفين
setInterval(() => {
    if (currentUserId) loadMessages(currentUserId, selectedUserName, selectedUserImg);
    loadChats();
    updateMessagesCount();
}, 10000);

// تفعيل قائمة الرسائل المنبثقة في الهيدر
const messagesBell = document.getElementById('messagesBell');
const messagesDropdown = document.getElementById('messagesDropdown');
messagesBell.addEventListener('click', function(e) {
    e.stopPropagation();
    if (messagesDropdown.style.display === 'block') {
        messagesDropdown.style.display = 'none';
        return;
    }
    // جلب المحادثات وعرضها في القائمة المنبثقة
    fetch('messages_api.php?recent=1')
        .then(res => res.json())
        .then(data => {
            let html = '';
            if (!data.length) {
                html = '<div style="color:#888;text-align:center;padding:20px;">لا توجد محادثات</div>';
            } else {
                html = data.map(chat => `<div class="popup-chat-item ${chat.seen == 0 && chat.sender_ID != currentUserId ? 'unread-chat' : ''}" data-user-id="${chat.user_id}">
                    <i class=\"fas fa-user\"></i> ${chat.name}
                    <span class=\"last-msg\">${chat.content ? chat.content.substring(0, 30) : ''}</span>
                    <span class=\"msg-time\">${chat.created_at ? chat.created_at.substring(11, 16) : ''}</span>
                    ${chat.seen == 0 && chat.sender_ID != currentUserId ? '<span class=\"unread-dot\"></span>' : ''}
                </div>`).join('');
            }
            messagesDropdown.innerHTML = html;
            messagesDropdown.style.display = 'block';
        });
});
document.addEventListener('click', function(e) {
    if (messagesDropdown && !messagesDropdown.contains(e.target) && e.target !== messagesBell) {
        messagesDropdown.style.display = 'none';
    }
});
// عند النقر على محادثة من القائمة المنبثقة
messagesDropdown.addEventListener('click', function(e) {
    const item = e.target.closest('.popup-chat-item');
    if (!item) return;
    const userId = item.getAttribute('data-user-id');
    // استخراج اسم المستخدم فقط من العنصر span
    let userName = item.querySelector('span') ? item.querySelector('span').textContent.trim() : '';
    // لا يوجد صورة في القائمة المنبثقة غالباً، استخدم الافتراضية
    let userImg = 'image/p.png';
    currentUserId = userId;
    selectedUserName = userName;
selectedUserImg = userImg;
    setChatHeader(userName, userImg);
    chatForm.style.display = 'flex';
    loadMessages(userId, userName, userImg);
    messagesDropdown.style.display = 'none';
});

// تأكد أن chatHeader لا يعرض أي محتوى أو وقت رسالة، فقط اسم الطرف:
function setChatHeader(userName, userImg) {
    // عرض اسم وصورة المستخدم في الهيدر
    chatHeader.innerHTML = `<div style="display:flex;align-items:center;gap:10px;">
        <img src="${userImg || 'image/p.png'}" alt="avatar" class="msg-avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #eee;box-shadow:0 1px 4px #0001;">
        <span>${userName}</span>
    </div>`;
}
