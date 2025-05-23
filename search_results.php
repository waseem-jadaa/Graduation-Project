<?php
session_start();
include 'admin_only_redirect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';
// جلب بيانات المستخدم
if (!isset($user_name) || !isset($profile_photo)) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        try {
            $stmt = $conn->prepare('SELECT u.name, p.profile_photo FROM user u LEFT JOIN profile p ON u.User_ID = p.User_ID WHERE u.User_ID = :user_id');
            $stmt->execute([':user_id' => $user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_name = $row && isset($row['name']) ? htmlspecialchars($row['name']) : 'مستخدم';
            $profile_photo = $row && !empty($row['profile_photo']) ? $row['profile_photo'] : 'image/p.png';
        } catch (PDOException $e) {
            $user_name = 'مستخدم';
            $profile_photo = 'image/p.png';
        }
    } else {
        $user_name = 'مستخدم';
        $profile_photo = 'image/p.png';
    }
}
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchTerm = htmlspecialchars($searchTerm);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج البحث</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/headerDash.css">
    <link rel="stylesheet" href="css/project.css">
    <link rel="stylesheet" href="css/search_result.css">
    <script src="notification.js"></script>
    
    <script>
        async function fetchSearchResults() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const searchTerm = urlParams.get('search') || '';
                const response = await fetch(`search_all.php?search=${encodeURIComponent(searchTerm)}`);
                const results = await response.json();
                const jobsContainer = document.getElementById('jobs-results');
                const professionalsContainer = document.getElementById('professionals-results');
                const jobsSection = document.getElementById('jobs-section');
                const professionalsSection = document.getElementById('professionals-section');
                jobsContainer.innerHTML = '';
                professionalsContainer.innerHTML = '';

                const jobResults = results.filter(r => r.type === 'job');
                const professionalResults = results.filter(r => r.type === 'professional');

                if (jobResults.length === 0 && professionalResults.length === 0) {
                    document.getElementById('no-results').style.display = 'block';
                } else {
                    document.getElementById('no-results').style.display = 'none';
                }

                // إظهار/إخفاء أقسام النتائج حسب النتائج
                if (jobResults.length === 0) {
                    jobsSection.style.display = 'none';
                } else {
                    jobsSection.style.display = '';
                    jobResults.forEach(job => {
                        const jobBox = document.createElement('div');
                        jobBox.className = 'job-box';
                        jobBox.innerHTML = `
                            <h3><i class='fas fa-briefcase'></i> ${job.title}</h3>
                            <p>${job.description}</p>
                            <p><strong>الموقع:</strong> ${job.location}</p>
                            <p><strong>الراتب:</strong> ${job.salary}</p>
                        `;
                        jobsContainer.appendChild(jobBox);
                    });
                }

                if (professionalResults.length === 0) {
                    professionalsSection.style.display = 'none';
                } else {
                    professionalsSection.style.display = '';
                    professionalResults.forEach(professional => {
                        const professionalBox = document.createElement('div');
                        professionalBox.className = 'job-box';
                        professionalBox.innerHTML = `
                            <h3><i class='fas fa-user-tie'></i> ${professional.name}</h3>
                            <p>${professional.profession}</p>
                            <p><strong>الموقع:</strong> ${professional.location}</p>
                            <p><strong>الخبرة:</strong> ${professional.experience}</p>
                        `;
                        professionalsContainer.appendChild(professionalBox);
                    });
                }
            } catch (error) {
                console.error('Error fetching search results:', error);
            }
        }
        document.addEventListener('DOMContentLoaded', fetchSearchResults);
    </script>
</head>
<body class="dashboard-page">
    <!-- Header & Sidebar (copied from headerDash.php, customized for search page) -->
    <header class="dashboard-header">
      <div class="container">
        <nav style="width:100%; display:flex; align-items:center; justify-content:space-between; flex-direction: row;">
            <!-- Left: Sidebar toggle + Logo -->
            <div style="display:flex; align-items:center; gap:1rem;">
              <button class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="تبديل القائمة الجانبية"><i class="fas fa-bars"></i></button>
              <div class="logo">
                <i class="fas fa-handshake"></i>
                <span>Fursa<span style="color: var(--primary);">Pal</span></span>
              </div>
            </div>
            <!-- Center: Search bar with voice and suggestions -->
            <div style="display: flex; align-items: center; gap: 10px; width: 100%; max-width: 500px; margin: 0 1.5rem;">
              <!-- Voice search button -->
              <button id="voice-search-btn" title="بحث صوتي" 
                      style="background: none; border: none; cursor: pointer; padding: 8px; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-microphone"></i>
              </button>
              <!-- Search bar with suggestions -->
              <div class="search-bar" style="position: relative; flex-grow: 1;">
                  <input type="text" id="searchInput" placeholder="ابحث عن وظائف أو مهنيين..." 
                         style="width: 100%; padding: 8px 40px 8px 8px; border: 1px solid #ccc; border-radius: 4px;">
                  <button id="searchBtn" style="position: absolute; left: auto; right: 5px; top: 50%; transform: translateY(-50%); 
                               background: none; border: none; cursor: pointer;">
                      <i class="fas fa-search"></i>
                  </button>
                  <div class="search-suggestions" style="position:absolute;right:0;left:0;top:100%;background:#fff;z-index:1000;border:1px solid #ccc;display:none;"></div>
              </div>
            </div>
            <!-- Right: Notifications + User + Logout -->
            <div class="user-actions" style="display:flex; align-items:center; gap:1.2rem;">
              <div class="notification-bell">
                <i class="fas fa-bell"></i>
                <span class="notification-count">3</span>
              </div>
              <div class="user-profile">
                <?php 
                  $profile_photo_url = $profile_photo;
                  if (strpos($profile_photo_url, 'uploads/profile_photos/') === 0 && file_exists($profile_photo_url)) {
                      $profile_photo_url .= '?t=' . filemtime($profile_photo_url);
                  }
                ?>
                <img src="<?php echo $profile_photo_url; ?>" alt="صورة المستخدم">
                <span><?php echo $user_name; ?></span>
                <i class="fas fa-chevron-down"></i>
                <div class="user-profile-menu">
                  <a href="profile.php">الملف الشخصي</a>
                </div>
              </div>
              <div class="logout-icon" style="cursor: pointer; margin-left: 10px;" onclick="location.href='main.php'">
                <i class="fas fa-sign-out-alt"></i>
              </div>
            </div>
        </nav>
      </div>
    </header>
    <!-- Sidebar Drawer -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar-drawer" id="sidebarDrawer" dir="rtl">
      <div class="sidebar-header">
        <div class="logo">
              <i class="fas fa-handshake"></i>
              <span>Fursa<span style="color: var(--primary);">Pal</span></span>
            </div>
        <button class="sidebar-toggle-btn" id="sidebarCloseBtn" aria-label="إغلاق القائمة الجانبية"><i class="fas fa-times"></i></button>
      </div>
      <nav class="sidebar-menu">
       
        <a class="menu-item" href="dashboard.php">
          <i class="fas fa-home"></i>
          <span>الرئيسية</span>
        </a>
        <a class="menu-item" href="jobs.php">
          <i class="fas fa-briefcase"></i>
          <span>الوظائف</span>
        </a>
        <a class="menu-item" href="professionals.php">
          <i class="fas fa-user-tie"></i>
          <span>المهنيون</span>
        </a>
        
        <a class="menu-item" href="settings.php">
          <i class="fas fa-cog"></i>
          <span>الإعدادات</span>
        </a>
      </nav>
    </aside>
    <main class="main-content">
        <div class="search-results-container">
            <h1>نتائج البحث عن: <?php echo $searchTerm; ?></h1>
            <div id="no-results">لا توجد نتائج مطابقة.</div>
            <section id="jobs-section">
                <h2 id="jobs-title"><i class="fas fa-briefcase"></i> الوظائف</h2>
                <div id="jobs-results" class="jobs-container"></div>
            </section>
            <section id="professionals-section">
                <h2 id="professionals-title"><i class="fas fa-user-tie"></i> المهنيون</h2>
                <div id="professionals-results" class="jobs-container"></div>
            </section>
        </div>
    </main>
    <script>
    // Sidebar Drawer Logic
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
    sidebarToggleBtn.addEventListener('click', openSidebar);
    sidebarCloseBtn.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
    // Close sidebar on ESC
    window.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeSidebar();
    });
    // User profile menu toggle
    const userProfile = document.querySelector('.user-profile');
    userProfile.addEventListener('click', function() {
      userProfile.classList.toggle('active');
    });
    document.addEventListener('click', function(e) {
      if (!userProfile.contains(e.target)) {
        userProfile.classList.remove('active');
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
    const searchInput = document.getElementById('searchInput');
    const suggestionsBox = searchBar.querySelector('.search-suggestions');

    function getRecentSearches() {
      return JSON.parse(localStorage.getItem('recentSearches') || '[]');
    }
    function saveRecentSearch(term) {
      let searches = getRecentSearches();
      searches = searches.filter(s => s !== term);
      searches.unshift(term);
      if (searches.length > 5) searches = searches.slice(0, 5);
      localStorage.setItem('recentSearches', JSON.stringify(searches));
    }
    let isMouseOnSuggestions = false;
    async function showSuggestions() {
      const term = searchInput.value.trim();
      suggestionsBox.innerHTML = '';
      if (term.length === 0) {
        let recent = getRecentSearches();
        recent = recent.filter(r => r && r.trim() !== '');
        if (recent.length) {
          // عرض قائمة واحدة فقط لعمليات البحث الأخيرة (دمج نصي وصوتي)
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
          suggestionsBox.onmouseenter = () => { isMouseOnSuggestions = true; };
          suggestionsBox.onmouseleave = () => { isMouseOnSuggestions = false; };
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
          const recentItems = Array.from(suggestionsBox.querySelectorAll('.recent-search-text'));
          recentItems.forEach(item => {
            item.onclick = (e) => {
              if (e.target.closest('.delete-recent')) return;
              searchInput.value = item.textContent;
              suggestionsBox.style.display = 'none';
              handleSearch();
            };
          });
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
          let selectedIdx = -1;
          function updateSelection(newIdx) {
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
          selectedIdx = -1;
          updateSelection(selectedIdx);
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
          html = `<div style="padding:8px;color:#888;">لا توجد نتائج مطابقة. جرب كلمة أخرى أو تحقق من الإملاء.</div>`;
        }
        suggestionsBox.innerHTML = html;
        suggestionsBox.style.display = 'block';
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
    searchInput.addEventListener('blur', () => {
      setTimeout(() => {
        if (!isMouseOnSuggestions) suggestionsBox.style.display = 'none';
      }, 200);
    });
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
    async function handleSearch() {
      const searchInputValue = searchInput.value.trim();
      if (searchInputValue !== '') {
        saveRecentSearch(searchInputValue);
        try {
          const currentPath = window.location.pathname;
          if (currentPath.includes('jobs.php')) {
            window.location.href = `jobs.php?search=${encodeURIComponent(searchInputValue)}`;
            return;
          } else if (currentPath.includes('professionals.php')) {
            window.location.href = `professionals.php?search=${encodeURIComponent(searchInputValue)}`;
            return;
          }
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
    document.getElementById('searchBtn').addEventListener('click', handleSearch);
    searchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        handleSearch();
      }
    });
    </script>
</body>
</html>
