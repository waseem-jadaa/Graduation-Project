document.addEventListener('DOMContentLoaded', function() {
  // Sidebar Drawer Logic (ابق كما هو)
  const sidebarDrawer = document.getElementById('sidebarDrawer');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
  const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

  function openSidebar() {
    sidebarDrawer.classList.add('open');
    sidebarOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebarDrawer.classList.remove('open');
    sidebarOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }
  if (sidebarToggleBtn) sidebarToggleBtn.addEventListener('click', openSidebar);
  if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
  if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
  // Close sidebar on ESC
  window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSidebar();
  });

  // إظهار/إخفاء قائمة الإشعارات عند الضغط على الجرس
  const bell = document.getElementById('notificationBell');
  const dropdown = document.getElementById('notificationDropdown');
  // مستمع واحد فقط لإغلاق القوائم المنبثقة بشكل منظم
  // منطق موحد لإدارة القوائم المنبثقة للرسائل والإشعارات
  document.addEventListener('click', function(e) {
    const notificationBell = document.getElementById('notificationBell');
    const messagesBell = document.getElementById('messagesBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const messagesDropdown = document.getElementById('messagesDropdown');
    const notifMenu = document.getElementById('notifMenu');
    
    if (!notificationBell || !messagesBell) return;

    const clickedElement = e.target;
    const clickedNotificationBell = notificationBell.contains(clickedElement);
    const clickedMessagesBell = messagesBell.contains(clickedElement);
    const clickedInsideNotification = (notificationDropdown && notificationDropdown.contains(clickedElement)) || 
                                    (notifMenu && notifMenu.contains(clickedElement));
    const clickedInsideMessages = messagesDropdown && messagesDropdown.contains(clickedElement);
    
    if (clickedNotificationBell) {
        // عند النقر على زر الإشعارات، أغلق الرسائل وقائمة المستخدم دائماً
        if (messagesDropdown) messagesDropdown.style.display = 'none';
        closeUserMenu(); // أغلق قائمة المستخدم
        return;
    }

    if (clickedMessagesBell) {
        // عند النقر على زر الرسائل، أغلق الإشعارات وقائمة المستخدم دائماً
        if (notificationDropdown) notificationDropdown.style.display = 'none';
        if (notifMenu) notifMenu.style.display = 'none';
        closeUserMenu(); // أغلق قائمة المستخدم
        return;
    }

    // إذا تم النقر خارج القوائم، أغلق الجميع
    if (!clickedInsideNotification && !clickedInsideMessages) {
        if (notificationDropdown) notificationDropdown.style.display = 'none';
        if (notifMenu) notifMenu.style.display = 'none';
        if (messagesDropdown) messagesDropdown.style.display = 'none';
        // لا تغلق قائمة المستخدم هنا، سيتم التعامل معها بمنطقها الخاص
    }
  });

  // --------- البحث الذكي والبحث الصوتي ---------
  // زر البحث الصوتي
  const voiceBtn = document.getElementById('voice-search-btn');
  if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();
    recognition.lang = 'ar-SA';
    recognition.continuous = false;
    recognition.interimResults = false;
    let isListening = false;
    voiceBtn.onclick = function(e) {
      e.preventDefault();
      if (!isListening) {
        recognition.start();
        isListening = true;
        voiceBtn.querySelector('i').style.color = '#007bff';
      } else {
        recognition.stop();
        isListening = false;
        voiceBtn.querySelector('i').style.color = '';
      }
    };
    recognition.onstart = function() {
      isListening = true;
      voiceBtn.querySelector('i').style.color = '#007bff';
    };
    recognition.onresult = function(event) {
      const transcript = event.results[0][0].transcript;
      searchInput.value = transcript;
      showSuggestions();
      isListening = false;
      voiceBtn.querySelector('i').style.color = '';
    };
    recognition.onerror = function() {
      isListening = false;
      voiceBtn.querySelector('i').style.color = '';
    };
    recognition.onend = function() {
      isListening = false;
      voiceBtn.querySelector('i').style.color = '';
    };
  } else {
    voiceBtn.style.display = 'none';
  }
  // --- البحث الذكي مع الاقتراحات ---
  const searchBar = document.querySelector('.search-bar');
  const searchInput = searchBar.querySelector('input');
  const suggestionsBox = searchBar.querySelector('.search-suggestions');

  // استرجاع آخر عمليات البحث من LocalStorage
  function getRecentSearches() {
    return JSON.parse(localStorage.getItem('recentSearches') || '[]');
  }
  // حفظ عملية بحث جديدة
  function saveRecentSearch(term) {
    let searches = getRecentSearches();
    searches = searches.filter(s => s !== term);
    searches.unshift(term);
    if (searches.length > 5) searches = searches.slice(0, 5);
    localStorage.setItem('recentSearches', JSON.stringify(searches));
  }

  // متغير لمتابعة وجود الماوس فوق suggestionsBox
  let isMouseOnSuggestions = false;

  // عرض الاقتراحات
  async function showSuggestions() {
    const term = searchInput.value.trim();
    suggestionsBox.innerHTML = '';
    if (term.length === 0) {
      // عرض آخر عمليات البحث مع إمكانية الحذف
      let recent = getRecentSearches();
      // تجاهل العناصر الفارغة أو التي تحتوي فقط على مسافات
      recent = recent.filter(r => r && r.trim() !== '');
      if (recent.length) {
        suggestionsBox.innerHTML = `
          <div style="padding:8px 12px;color:#888;display:flex;align-items:center;background:#f7f7f7;border-bottom:1px solid #eee;">
            <span style="font-size:15px;">عمليات البحث الأخيرة</span>
          </div>
          <div style="max-height:260px;overflow-y:auto;">
            ${recent.map((r, idx) => `
              <div class="suggestion-item recent-search-row" style="display:flex;align-items:center;justify-content:space-between;padding:0 8px 0 0;height:44px;border-bottom:1px solid #2221;transition:background 0.2s;position:relative;cursor:pointer;">
                <span style="margin-left:10px;color:#888;font-size:18px;display:flex;align-items:center;">
                  <i class="fas fa-history"></i>
                </span>
                <span class="recent-search-text" data-idx="${idx}" style="flex:1;text-align:right;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:16px;">${r}</span>
                <button class="delete-recent" data-idx="${idx}" title="إزالة" style="background:none;border:none;color:#aaa;cursor:pointer;font-size:15px;padding:0 12px;opacity:0;transition:opacity 0.2s;">إزالة</button>
              </div>
            `).join('')}
          </div>
        `;
        suggestionsBox.style.display = 'block';
        // متابعة دخول وخروج الماوس من suggestionsBox
        suggestionsBox.onmouseenter = () => { isMouseOnSuggestions = true; };
        suggestionsBox.onmouseleave = () => { isMouseOnSuggestions = false; };
        // إظهار زر الإزالة عند المرور فقط
        Array.from(suggestionsBox.querySelectorAll('.recent-search-row')).forEach(row => {
          row.addEventListener('mouseenter', function() {
            const btn = row.querySelector('.delete-recent');
            if(btn) btn.style.opacity = 1;
          });
          row.addEventListener('mouseleave', function() {
            const btn = row.querySelector('.delete-recent');
            if(btn) btn.style.opacity = 0;
          });
        });
        // تنفيذ البحث عند الضغط على النص فقط وليس عند الضغط على زر الإزالة
        const recentItems = Array.from(suggestionsBox.querySelectorAll('.recent-search-text'));
        recentItems.forEach(item => {
          item.onclick = (e) => {
            // إذا كان الضغط على زر الإزالة، تجاهل
            if (e.target.closest('.delete-recent')) return;
            searchInput.value = item.textContent;
            suggestionsBox.style.display = 'none';
            handleSearch();
          };
        });
        // حذف عملية بحث واحدة فقط عند الضغط على زر الحذف وليس على العنصر كله
        Array.from(suggestionsBox.querySelectorAll('.delete-recent')).forEach(btn => {
          btn.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            const idx = parseInt(btn.getAttribute('data-idx'));
            let searches = getRecentSearches();
            searches.splice(idx, 1);
            localStorage.setItem('recentSearches', JSON.stringify(searches));
            showSuggestions();
          };
        });
        // دعم التنقل بين عمليات البحث الأخيرة باستخدام الأسهم ولوحة المفاتيح
        let selectedIdx = -1;
        function updateSelection(newIdx) {
          // إزالة التحديد السابق
          recentItems.forEach((el, i) => {
            el.parentElement.style.background = (i === newIdx) ? '#e9ecef' : '';
          });
          if (newIdx >= 0 && newIdx < recentItems.length) {
            searchInput.value = recentItems[newIdx].textContent;
          }
        }
        searchInput.onkeydown = function(e) {
          if (recentItems.length === 0 || suggestionsBox.style.display === 'none') return;
          if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIdx = (selectedIdx + 1) % recentItems.length;
            updateSelection(selectedIdx);
          } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIdx = (selectedIdx - 1 + recentItems.length) % recentItems.length;
            updateSelection(selectedIdx);
          } else if (e.key === 'Enter' && selectedIdx !== -1) {
            e.preventDefault();
            recentItems[selectedIdx].click();
          } else {
            selectedIdx = -1;
            updateSelection(selectedIdx);
          }
        };
        // إعادة تعيين selectedIdx عند إظهار الاقتراحات
        selectedIdx = -1;
        updateSelection(selectedIdx);
        // حذف الكل
        const clearBtn = document.getElementById('clear-recent-searches');
        if (clearBtn) {
          clearBtn.onclick = (e) => {
            e.stopPropagation();
            localStorage.removeItem('recentSearches');
            showSuggestions();
          };
        }
      } else {
        suggestionsBox.style.display = 'none';
      }
      return;
    }
    // جلب الاقتراحات من السيرفر
    try {
      const response = await fetch(`search_all.php?search=${encodeURIComponent(term)}`);
      const results = await response.json();
      const jobResults = results.filter(r => r.type === 'job');
      const professionalResults = results.filter(r => r.type === 'professional');
      let html = '';
      let suggestionItems = [];
      if (jobResults.length) {
        html += `<div style="padding:8px;color:#007bff;">وظائف (${jobResults.length})</div>`;
        html += jobResults.slice(0, 3).map(j => `<div class="suggestion-item instant-suggestion" data-type="job" style="padding:8px;cursor:pointer;">${j.title}</div>`).join('');
      }
      if (professionalResults.length) {
        html += `<div style="padding:8px;color:#28a745;">مهنيون (${professionalResults.length})</div>`;
        html += professionalResults.slice(0, 3).map(p => `<div class="suggestion-item instant-suggestion" data-type="professional" style="padding:8px;cursor:pointer;">${p.name}</div>`).join('');
      }
      if (!html) {
        // اقتراح نتائج مشابهة (تصحيح إملائي بسيط)
        html = `<div style="padding:8px;color:#888;">لا توجد نتائج مطابقة. جرب كلمة أخرى أو تحقق من الإملاء.</div>`;
      }
      suggestionsBox.innerHTML = html;
      suggestionsBox.style.display = 'block';
      // دعم التنقل بالأسهم والاختيار بالإنتر للاقتراحات الذكية
      suggestionItems = Array.from(suggestionsBox.querySelectorAll('.instant-suggestion'));
      let selectedIdx = -1;
      function updateSelection(newIdx) {
        suggestionItems.forEach((el, i) => {
          el.style.background = (i === newIdx) ? '#e9ecef' : '';
        });
        if (newIdx >= 0 && newIdx < suggestionItems.length) {
          searchInput.value = suggestionItems[newIdx].textContent;
        }
      }
      searchInput.onkeydown = function(e) {
        if (suggestionItems.length === 0 || suggestionsBox.style.display === 'none') return;
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          selectedIdx = (selectedIdx + 1) % suggestionItems.length;
          updateSelection(selectedIdx);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          selectedIdx = (selectedIdx - 1 + suggestionItems.length) % suggestionItems.length;
          updateSelection(selectedIdx);
        } else if (e.key === 'Enter' && selectedIdx !== -1) {
          e.preventDefault();
          suggestionItems[selectedIdx].click();
        } else {
          selectedIdx = -1;
          updateSelection(selectedIdx);
        }
      };
      // إعادة تعيين selectedIdx عند إظهار الاقتراحات
      selectedIdx = -1;
      updateSelection(selectedIdx);
      suggestionItems.forEach(item => {
        item.onclick = () => {
          searchInput.value = item.textContent;
          suggestionsBox.style.display = 'none';
          handleSearch();
        };
      });
    } catch {
      suggestionsBox.style.display = 'none';
    }
  }

  // إخفاء الاقتراحات عند فقدان التركيز فقط إذا لم يكن الماوس فوق القائمة
  searchInput.addEventListener('blur', () => {
    setTimeout(() => {
      if (!isMouseOnSuggestions) suggestionsBox.style.display = 'none';
    }, 200);
  });

  // إغلاق القائمة عند النقر خارج مربع البحث أو suggestionsBox
  document.addEventListener('mousedown', function(e) {
    if (
      suggestionsBox.style.display === 'block' &&
      !suggestionsBox.contains(e.target) &&
      !searchInput.contains(e.target)
    ) {
      suggestionsBox.style.display = 'none';
    }
  });
  searchInput.addEventListener('focus', showSuggestions);
  searchInput.addEventListener('input', showSuggestions);

  // حفظ البحث عند التنفيذ
  async function handleSearch() {
    const searchInputValue = searchInput.value.trim();
    if (searchInputValue !== '') {
      saveRecentSearch(searchInputValue);
      try {
        // تحديد الصفحة الحالية
        const currentPath = window.location.pathname;
        if (currentPath.includes('jobs.php')) {
          window.location.href = `jobs.php?search=${encodeURIComponent(searchInputValue)}`;
          return;
        } else if (currentPath.includes('professionals.php')) {
          window.location.href = `professionals.php?search=${encodeURIComponent(searchInputValue)}`;
          return;
        }
        // إذا كان في أي صفحة أخرى (مثلاً dashboard)
        const response = await fetch(`search_all.php?search=${encodeURIComponent(searchInputValue)}`);
        const results = await response.json();
        const jobResults = results.filter(r => r.type === 'job');
        const professionalResults = results.filter(r => r.type === 'professional');
        if (jobResults.length > 0 || professionalResults.length > 0) {
          window.location.href = `search_results.php?search=${encodeURIComponent(searchInputValue)}`;
        } else {
          alert('لا توجد نتائج مطابقة.');
        }
      } catch (error) {
        console.error('Error during search:', error);
        alert('حدث خطأ أثناء البحث.');
      }
    }
  }

  document.querySelector('.search-bar button').addEventListener('click', handleSearch);
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      handleSearch();
    }
  });

  // توحيد منطق قائمة المستخدم المنسدلة لمنع التضارب
  const userProfileMenuBtn = document.getElementById('userProfileMenuBtn');
  const userProfileMenu = document.getElementById('userProfileMenu');
  const logoutMenuBtn = document.getElementById('logoutMenuBtn');

  let userMenuOpen = false;

  function closeUserMenu() {
    if (userProfileMenu) {
      userProfileMenu.classList.remove('show');
      userMenuOpen = false;
      // تغيير اتجاه السهم إلى الأسفل
      const arrow = document.getElementById('userProfileArrow');
      if (arrow) {
        arrow.querySelector('i').style.transform = 'rotate(0deg)';
      }
    }
  }

  function openUserMenu() {
    if (userProfileMenu) {
      userProfileMenu.classList.add('show');
      userMenuOpen = true;
      // تغيير اتجاه السهم إلى الأعلى
      const arrow = document.getElementById('userProfileArrow');
      if (arrow) {
        arrow.querySelector('i').style.transform = 'rotate(180deg)';
      }
    }
  }

  if (userProfileMenuBtn && userProfileMenu) {
    // إضافة مستمع حدث النقر على منطقة المستخدم بالكامل
    userProfileMenuBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      // إغلاق جميع القوائم المنبثقة الأخرى
      const notificationDropdown = document.getElementById('notificationDropdown');
      const messagesDropdown = document.getElementById('messagesDropdown');
      const notifMenu = document.getElementById('notifMenu');
      
      if (notificationDropdown) notificationDropdown.style.display = 'none';
      if (messagesDropdown) messagesDropdown.style.display = 'none';
      if (notifMenu) notifMenu.style.display = 'none';

      if (userMenuOpen) {
        closeUserMenu();
      } else {
        openUserMenu();
      }
    });

    // إغلاق القائمة عند النقر خارجها
    document.addEventListener('click', function(e) {
      if (userMenuOpen && !userProfileMenu.contains(e.target) && !userProfileMenuBtn.contains(e.target)) {
        closeUserMenu();
      }
    });

    // إغلاق القائمة عند الضغط على Escape
    document.addEventListener('keydown', function(e) {
      if (userMenuOpen && e.key === 'Escape') {
        closeUserMenu();
      }
    });
  }
  if (logoutMenuBtn) {
    logoutMenuBtn.addEventListener('click', function(e) {
      e.preventDefault();
      window.location.href = 'main.php';
    });
  }
}); 