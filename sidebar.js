

document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.querySelector('.sidebar');
  const toggleBtn = document.querySelector('.sidebar-toggle-btn');

  if (!sidebar || !toggleBtn) return;

  toggleBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    sidebar.classList.toggle('collapsed');
    
    if (sidebar.classList.contains('collapsed')) {
      sessionStorage.setItem('sidebar-collapsed', '1');
    } else {
      sessionStorage.removeItem('sidebar-collapsed');
    }
  });

 
  if (sessionStorage.getItem('sidebar-collapsed')) {
    sidebar.classList.add('collapsed');
  }

  
  document.addEventListener('click', function(e) {
    if (window.innerWidth <= 992 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
      sidebar.classList.add('collapsed');
    }
  });
});
