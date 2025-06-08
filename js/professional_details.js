document.addEventListener('DOMContentLoaded', function() {
    const requestBtn = document.querySelector('.btn-request');
    if (requestBtn) {
        requestBtn.addEventListener('click', function() {
            const professionalId = this.getAttribute('data-professional-id');
            fetch('request_professional.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'professional_id=' + encodeURIComponent(professionalId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('تم إرسال طلبك لهذا المهني!');
                    this.disabled = true;
                    this.textContent = 'تم إرسال الطلب';
                } else if (data.error === 'already_requested') {
                    alert('لقد أرسلت طلباً لهذا المهني مسبقاً.');
                } else {
                    alert('حدث خطأ أثناء إرسال الطلب.');
                }
            })
            .catch(() => {
                alert('حدث خطأ في الاتصال بالخادم.');
            });
        });
    }

    // Get the modal
    var modal = document.getElementById("imageModal");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.querySelectorAll(".work-image");
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");

    img.forEach(item => {
        item.onclick = function(){
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        }
    });

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    if (span) {
        span.onclick = function() {
          modal.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal content, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}); 