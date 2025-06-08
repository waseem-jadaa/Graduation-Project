function openJobAnnouncement() {
  document.getElementById('job-announcement-modal').style.display = 'block';
}

function closeJobAnnouncement() {
  document.getElementById('job-announcement-modal').style.display = 'none';
}

function toggleDetails(button) {
  const detailsDiv = button.closest('.job-card').querySelector('.job-details-full');
  detailsDiv.classList.toggle('show');
  button.textContent = detailsDiv.classList.contains('show') ? 'إخفاء التفاصيل' : 'التفاصيل';
}

function renderActivityItem(activity) {
  let icon = activity.icon || 'fas fa-bell';
  let msg = activity.message || '';
  let time = activity.time_ago || activity.created_at || '';
  let imgSrc = activity.photo || 'image/avatar.png';
  return `<div class="activity-item">
    <div class="activity-icon">
      <img src="${imgSrc}" alt="notification" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
    </div>
    <div class="activity-content">
      <p>${msg}</p>
      <span class="activity-time">${time}</span>
    </div>
  </div>`;
}

document.addEventListener('DOMContentLoaded', function() {
  // Job Save Functionality
  document.querySelectorAll('.job-save').forEach(btn => {
    if (btn.dataset.saved === '1') {
      btn.classList.add('saved');
      btn.querySelector('i').classList.remove('far');
      btn.querySelector('i').classList.add('fas');
    }
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      var jobId = btn.getAttribute('data-job-id');
      if (!jobId) {
        alert('معرف الوظيفة غير متوفر.');
        return;
      }
      if (btn.classList.contains('saved')) {
        // Remove save
        fetch('remove_saved_job.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'job_id=' + encodeURIComponent(jobId)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            btn.classList.remove('saved');
            btn.querySelector('i').classList.remove('fas');
            btn.querySelector('i').classList.add('far');
          } else {
            alert('حدث خطأ أثناء إزالة الحفظ.');
          }
        })
        .catch(() => {
          alert('حدث خطأ في الاتصال بالخادم.');
        });
      } else {
        // Add save
        fetch('save_job.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'job_id=' + encodeURIComponent(jobId)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            btn.classList.add('saved');
            btn.querySelector('i').classList.remove('far');
            btn.querySelector('i').classList.add('fas');
          } else if (data.error === 'already_saved') {
            alert('هذه الوظيفة محفوظة بالفعل.');
          } else if (data.error === 'unauthorized') {
            alert('يجب تسجيل الدخول لحفظ الوظيفة.');
          } else {
            alert('حدث خطأ أثناء الحفظ.');
          }
        })
        .catch(() => {
          alert('حدث خطأ في الاتصال بالخادم.');
        });
      }
    });
  });

  // Job Apply Functionality
  document.querySelectorAll('.btn-apply').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var jobCard = btn.closest('.job-card');
      if (!jobCard) return;
      var jobId = btn.getAttribute('data-job-id');
      if (!jobId) {
        alert('معرف الوظيفة غير متوفر. يرجى مراجعة الإدارة.');
        return;
      }
      btn.disabled = true;
      fetch('apply_job.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'job_id=' + encodeURIComponent(jobId)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          btn.textContent = 'تم التقديم';
          btn.classList.add('applied');
          alert('تم إرسال طلبك بنجاح! سيتم إشعار صاحب العمل.');
        } else if (data.error === 'already_applied') {
          btn.textContent = 'تم التقديم مسبقاً';
          alert('لقد تقدمت لهذه الوظيفة مسبقاً.');
        } else {
          alert('حدث خطأ أثناء التقديم.');
        }
      })
      .catch(() => {
        alert('حدث خطأ في الاتصال بالخادم.');
      })
      .finally(() => {
        btn.disabled = false;
      });
    });
  });

  // Professional Request Functionality
  document.querySelectorAll('.btn-request-professional').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var professionalId = btn.getAttribute('data-professional-id');
      if (professionalId) {
        fetch('request_professional.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'professional_id=' + encodeURIComponent(professionalId)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('تم إرسال طلبك لهذا المهني!');
          } else if (data.error === 'already_requested') {
            alert('لقد أرسلت طلباً لهذا المهني مسبقاً.');
          } else {
            alert('حدث خطأ أثناء إرسال الطلب.');
          }
        })
        .catch(() => {
          alert('حدث خطأ في الاتصال بالخادم.');
        });
      }
    });
  });

  // Rating System
  document.querySelectorAll('.rating-stars-dashboard').forEach(function(div) {
    var pid = div.getAttribute('data-professional-id');
    fetch('get_user_role.php')
      .then(res => res.json())
      .then(userInfo => {
        fetch('get_professional_rating.php?professional_id=' + pid)
          .then(res => res.json())
          .then(data => {
            let avg = data && typeof data.avg_rating !== 'undefined' ? Math.round(data.avg_rating) : 0;
            let stars = '';
            for (let i = 1; i <= 5; i++) {
              stars += `<span class="star" data-star="${i}" style="color:${i <= avg ? '#FFD700' : '#ccc'};font-size:1.3em;cursor:pointer;">★</span>`;
            }
            div.innerHTML = `<span class="stars-container">${stars}</span>`;

            if (userInfo.role === 'employer' && userInfo.user_id != pid) {
              const starSpans = div.querySelectorAll('.star');
              let selected = 0;
              starSpans.forEach((star, idx) => {
                star.addEventListener('mouseenter', function() {
                  starSpans.forEach((s, i) => {
                    s.style.color = i <= idx ? '#FFD700' : '#ccc';
                  });
                });
                star.addEventListener('mouseleave', function() {
                  starSpans.forEach((s, i) => {
                    s.style.color = i < selected ? '#FFD700' : '#ccc';
                  });
                });
                star.addEventListener('click', function() {
                  selected = idx + 1;
                  fetch('rate_professional.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `professional_id=${pid}&rating=${selected}`
                  })
                  .then(res => res.json())
                  .then(result => {
                    if (result.success) {
                      starSpans.forEach((s, i) => {
                        s.style.color = i < selected ? '#FFD700' : '#ccc';
                      });
                      alert('تم حفظ تقييمك بنجاح!');
                    } else if(result.error === 'already_rated') {
                      alert('لقد قمت بتقييم هذا المهني مسبقاً.');
                    } else if(result.error === 'not_allowed') {
                      alert('يمكنك تقييم المهني فقط بعد التعامل معه.');
                    } else {
                      alert('حدث خطأ أثناء حفظ التقييم.');
                    }
                  });
                });
              });
              div.addEventListener('mouseleave', function() {
                starSpans.forEach((s, i) => {
                  s.style.color = i < avg ? '#FFD700' : '#ccc';
                });
              });
            }
          })
          .catch(() => {
            div.innerHTML = '<span style="color:#888;">لا يوجد تقييم</span>';
          });
      });
  });

  // Explore Jobs Button
  var btnExplore = document.getElementById('btn-explore-jobs');
  if (btnExplore) {
    btnExplore.addEventListener('click', function() {
      fetch('get_jobs.php?latest=1')
        .then(res => res.json())
        .then(jobs => {
          var html = '';
          if (jobs.length === 0) {
            html = '<p>لا توجد وظائف جديدة حالياً.</p>';
          } else {
            jobs.forEach(job => {
              html += `<div style='border-bottom:1px solid #eee;padding:10px 0;'>
                <strong>${job.title}</strong><br>
                <span style='color:#888;'>${job.location}</span><br>
                <span style='font-size:0.9em;'>${job.created_at || ''}</span>
                <p style='margin:5px 0 0;'>${job.description || ''}</p>
              </div>`;
            });
          }
          document.getElementById('new-jobs-list').innerHTML = html;
          document.getElementById('new-jobs-modal').style.display = 'flex';
        })
        .catch(() => {
          document.getElementById('new-jobs-list').innerHTML = '<p>تعذر جلب الوظائف الجديدة.</p>';
          document.getElementById('new-jobs-modal').style.display = 'flex';
        });
    });
  }

  // Load Activities
  fetch('get_notifications.php')
    .then(res => res.json())
    .then(list => {
      let html = '';
      if (Array.isArray(list) && list.length > 0) {
        html = list.map(renderActivityItem).join('');
      } else {
        html = '<div style="text-align:center;color:#888;">لا يوجد أنشطة حديثة.</div>';
      }
      document.getElementById('activity-timeline').innerHTML = html;
    })
    .catch(() => {
      document.getElementById('activity-timeline').innerHTML = '<div style="text-align:center;color:#888;">تعذر جلب الأنشطة.</div>';
    });

  // Fetch and display AI job recommendations
  const aiRecommendLoading = document.getElementById('ai-recommend-loading');
  const aiRecommendJobs = document.getElementById('ai-recommend-jobs');
  const aiHelpBtn = document.getElementById('ai-help-btn');
  const aiHelpModal = document.getElementById('ai-help-modal');

  if (aiRecommendLoading && aiRecommendJobs) {
    fetch('ai_job_match.php')
      .then(response => response.json())
      .then(data => {
        aiRecommendLoading.style.display = 'none';
        if (data.recommended_jobs && data.recommended_jobs.length > 0) {
          let html = '';
          data.recommended_jobs.forEach(job => {
            html += `
              <div class="job-card">
                <div class="job-header">
                  <img src="https://img.icons8.com/color/48/briefcase--v1.png" alt="وظائف عامة" class="company-logo">
                  <div class="job-title">
                    <h3>${job.title}</h3>
                  </div>
                  <div class="job-save${job.saved ? ' saved' : ''}" data-job-id="${job.job_ID}" data-saved="${job.saved ? '1' : '0'}">
                    <i class="${job.saved ? 'fas' : 'far'} fa-bookmark"></i>
                  </div>
                </div>
                <div class="job-details">
                  <p><i class="fas fa-map-marker-alt"></i> ${job.location}</p>
                  <p><i class="fas fa-money-bill-wave"></i> ${job.salary}</p>
                  <p><i class="fas fa-building"></i> ${job.employer_name}</p>
                </div>
                <div class="job-details-full">
                  <h4>تفاصيل الوظيفة:</h4>
                  <p>${job.description}</p>
                </div>
                <div class="job-actions">
                  <button class="btn-apply" data-job-id="${job.job_ID}">تقدم الآن</button>
                  <button class="btn-details" onclick="toggleDetails(this)">التفاصيل</button>
                </div>
              </div>
            `;
          });
          aiRecommendJobs.innerHTML = html;
          // Attach event listeners to the newly added apply buttons
          attachApplyButtonListeners();
          // Attach save button listeners
          attachSaveButtonListeners();
        } else {
          aiRecommendJobs.innerHTML = '<p style="text-align:center;color:#888;">لا توجد توصيات حالياً بناءً على ملفك الشخصي.</p>';
        }
      })
      .catch(error => {
        console.error('Error fetching AI job recommendations:', error);
        aiRecommendLoading.style.display = 'none';
        aiRecommendJobs.innerHTML = '<p style="text-align:center;color:#888;">حدث خطأ أثناء جلب التوصيات.</p>';
      });
  }

  // AI Help Modal functionality
  if (aiHelpBtn && aiHelpModal) {
    aiHelpBtn.addEventListener('click', function() {
      aiHelpModal.style.display = 'flex';
    });
  }
});

// Function to attach event listeners to apply buttons
function attachApplyButtonListeners() {
  document.querySelectorAll('.btn-apply').forEach(function(btn) {
    // Remove existing listeners to prevent duplicates
    btn.replaceWith(btn.cloneNode(true));
  });
  document.querySelectorAll('.btn-apply').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var jobCard = btn.closest('.job-card');
      if (!jobCard) return;
      var jobId = btn.getAttribute('data-job-id');
      if (!jobId) {
        alert('معرف الوظيفة غير متوفر. يرجى مراجعة الإدارة.');
        return;
      }
      btn.disabled = true;
      fetch('apply_job.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'job_id=' + encodeURIComponent(jobId)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          btn.textContent = 'تم التقديم';
          btn.classList.add('applied');
          alert('تم إرسال طلبك بنجاح! سيتم إشعار صاحب العمل.');
        } else if (data.error === 'already_applied') {
          btn.textContent = 'تم التقديم مسبقاً';
          alert('لقد تقدمت لهذه الوظيفة مسبقاً.');
        } else {
          alert('حدث خطأ أثناء التقديم.');
        }
      })
      .catch(() => {
        alert('حدث خطأ في الاتصال بالخادم.');
      })
      .finally(() => {
        btn.disabled = false;
      });
    });
  });
}

// Add this function after the existing code
function attachSaveButtonListeners() {
  document.querySelectorAll('.job-save').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      var jobId = btn.getAttribute('data-job-id');
      if (!jobId) {
        alert('معرف الوظيفة غير متوفر.');
        return;
      }
      if (btn.classList.contains('saved')) {
        // Remove save
        fetch('remove_saved_job.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'job_id=' + encodeURIComponent(jobId)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            btn.classList.remove('saved');
            btn.querySelector('i').classList.remove('fas');
            btn.querySelector('i').classList.add('far');
          } else {
            alert('حدث خطأ أثناء إزالة الحفظ.');
          }
        })
        .catch(() => {
          alert('حدث خطأ في الاتصال بالخادم.');
        });
      } else {
        // Add save
        fetch('save_job.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'job_id=' + encodeURIComponent(jobId)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            btn.classList.add('saved');
            btn.querySelector('i').classList.remove('far');
            btn.querySelector('i').classList.add('fas');
          } else {
            alert('حدث خطأ أثناء حفظ الوظيفة.');
          }
        })
        .catch(() => {
          alert('حدث خطأ في الاتصال بالخادم.');
        });
      }
    });
  });
}

// Initial attachment of listeners on DOMContentLoaded
attachApplyButtonListeners(); 