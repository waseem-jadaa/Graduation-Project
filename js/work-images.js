console.log('work-images.js loaded');

class WorkImagesManager {
    constructor() {
        console.log('WorkImagesManager constructor called');
        this.uploadForm = document.getElementById('uploadWorkImages');
        this.fileInput = document.getElementById('workImages');
        this.chooseImagesBtn = document.getElementById('chooseImagesBtn');
        
        console.log('Attempting to find form with ID uploadWorkImages:', this.uploadForm);
        console.log('Attempting to find file input with ID workImages:', this.fileInput);
        console.log('Attempting to find choose images button with ID chooseImagesBtn:', this.chooseImagesBtn);

        if (!this.uploadForm || !this.fileInput || !this.chooseImagesBtn) {
            console.error('Error: Work images form, file input, or choose button not found during initialization.');
            // Do NOT return here. Let the script continue to potentially find the form later.
            // We will rely on checks before adding event listeners.
        }

        this.currentImages = document.querySelectorAll('.current-images img').length;
        
        // Initialize listeners only if all elements are found
        if (this.uploadForm && this.fileInput && this.chooseImagesBtn) {
            this.initializeEventListeners();
        } else {
            console.log('Form, file input, or choose button not found on init, event listeners not attached yet.');
        }
    }
    
    initializeEventListeners() {
        console.log('Initializing work image event listeners');
        this.fileInput.addEventListener('change', this.handleFileSelect.bind(this));
        this.uploadForm.addEventListener('submit', this.handleUpload.bind(this));
        this.chooseImagesBtn.addEventListener('click', this.handleChooseImagesClick.bind(this));
        
        // Add listeners to existing delete/change buttons
        document.querySelectorAll('.delete-image').forEach(button => {
            button.addEventListener('click', this.handleDelete.bind(this));
        });
        
        document.querySelectorAll('.change-image').forEach(button => {
            button.addEventListener('click', this.handleChange.bind(this));
        });
    }
    
    handleChooseImagesClick() {
        console.log('Choose Images button clicked, triggering file input click.');
        this.fileInput.click(); // Trigger the hidden file input
    }
    
    handleFileSelect(e) {
        const files = Array.from(e.target.files); // Convert FileList to Array
        const currentImagesCount = document.querySelectorAll('.current-images img').length; // Recalculate each time
        
        // If this is a 'change' operation, files.length should be 1
        // If it's a new upload, check against remaining slots
        if (!e.target.dataset.isChange && (files.length + currentImagesCount > 5)) {
            showNotification('يمكنك رفع 5 صور كحد أقصى', 'error');
            e.target.value = ''; // Clear the input
            return;
        }
        // Optional: add preview functionality here if desired
    }
    
    async handleUpload(e) {
        e.preventDefault();
        console.log('Upload form submitted.');
        const formData = new FormData(this.uploadForm);
        
        try {
            // Add a loading indicator
            this.uploadForm.classList.add('loading');

            const response = await fetch('/forsa-pal/api/upload_work_images.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Raw response status:', response.status);
            const result = await response.json();
            console.log('Parsed JSON response:', result);

            if (result.error) {
                showNotification(result.error, 'error');
            } else if (result.success) {
                showNotification('تم رفع الصور بنجاح!', 'success');
                setTimeout(() => location.reload(), 1500); // Reload after 1.5 seconds to show notification
            } else {
                 showNotification('استجابة غير متوقعة من الخادم.', 'error');
            }
        } catch (error) {
            console.error('Error during file upload:', error);
            showNotification('حدث خطأ أثناء رفع الصور. يرجى مراجعة وحدة تحكم المتصفح للمزيد من التفاصيل.', 'error');
        } finally {
            this.uploadForm.classList.remove('loading');
        }
    }
    
    async handleDelete(e) {
        console.log('Delete button clicked');
        const imageNumber = e.target.dataset.imageNumber;
        const imageContainer = e.target.closest('.image-container');
        
        if (!confirm('هل أنت متأكد من حذف هذه الصورة؟')) return;
        
        // Add loading state
        imageContainer.classList.add('loading');
        e.target.disabled = true;
        
        try {
            const response = await fetch('/forsa-pal/api/delete_work_image.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ image_number: imageNumber })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Remove the image container with a fade effect
                imageContainer.style.opacity = '0';
                imageContainer.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    imageContainer.remove();
                    showNotification('تم حذف الصورة بنجاح.', 'success');
                }, 300);
            } else if (result.error) {
                showNotification(result.error, 'error');
                imageContainer.classList.remove('loading');
                e.target.disabled = false;
            } else {
                showNotification('استجابة حذف غير متوقعة من الخادم.', 'error');
                imageContainer.classList.remove('loading');
                e.target.disabled = false;
            }
        } catch (error) {
            console.error('Error during image deletion:', error);
            showNotification('حدث خطأ أثناء حذف الصورة. يرجى المحاولة مرة أخرى.', 'error');
            imageContainer.classList.remove('loading');
            e.target.disabled = false;
        }
    }
    
    handleChange(e) {
        console.log('Change button clicked');
        const imageNumber = e.target.dataset.imageNumber;
        
        // Create a new file input for this specific change
        const tempFileInput = document.createElement('input');
        tempFileInput.type = 'file';
        tempFileInput.accept = 'image/*';
        tempFileInput.style.display = 'none'; // Keep it hidden
        tempFileInput.multiple = false; // Only allow one file for change
        tempFileInput.dataset.isChange = 'true'; // Mark it as a change operation
        document.body.appendChild(tempFileInput);
        
        tempFileInput.click();
        
        tempFileInput.onchange = async (event) => {
            const file = event.target.files[0];
            if (file) {
                console.log('New file selected for change:', file.name);
                const formData = new FormData();
                formData.append('work_images[]', file); // Still append as an array for consistency on server
                formData.append('image_number', imageNumber);
                formData.append('is_change', 'true'); // Explicitly tell the server this is a change
                
                // Add loading state to the specific image container being changed
                const imageContainer = e.target.closest('.image-container');
                imageContainer.classList.add('loading');
                e.target.disabled = true; // Disable change button
                imageContainer.querySelector('.delete-image').disabled = true; // Disable delete button
                
                try {
                    const response = await fetch('/forsa-pal/api/upload_work_images.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.error) {
                        showNotification(result.error, 'error');
                    } else if (result.success) {
                        showNotification('تم تغيير الصورة بنجاح!', 'success');
                        setTimeout(() => location.reload(), 1500); // Reload after 1.5 seconds
                    } else {
                         showNotification('استجابة تغيير غير متوقعة من الخادم.', 'error');
                    }
                } catch (error) {
                    console.error('Error during image change upload:', error);
                    showNotification('حدث خطأ أثناء تغيير الصورة. يرجى المحاولة مرة أخرى.', 'error');
                } finally {
                    // Clean up loading state and re-enable buttons
                    imageContainer.classList.remove('loading');
                    e.target.disabled = false;
                    imageContainer.querySelector('.delete-image').disabled = false;
                }
            }
            // Clean up the temporary file input
            document.body.removeChild(tempFileInput);
            // Ensure no lingering onchange listener
            tempFileInput.onchange = null;
        };
    }
}

let workImagesManagerInstance = null; // Ensure only one instance is created

// Custom Notification System (replacing alerts)
function showNotification(message, type = 'info') {
    const notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        console.error('Notification container not found. Falling back to alert:', message);
        alert(message);
        return;
    }

    const notification = document.createElement('div');
    notification.classList.add('notification', type);
    notification.textContent = message;

    notificationContainer.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('hide');
        notification.addEventListener('transitionend', () => {
            notification.remove();
        });
    }, 5000); // 5 seconds
}

// Function to attempt finding the form and initializing the manager
function initializeWorkImages() {
    console.log('Attempting to initialize WorkImagesManager...');
    if (workImagesManagerInstance) {
        console.log('WorkImagesManager already initialized.');
        return; // Already initialized, do nothing
    }

    const uploadForm = document.getElementById('uploadWorkImages');
    const fileInput = document.getElementById('workImages');
    const chooseImagesBtn = document.getElementById('chooseImagesBtn');

    if (uploadForm && fileInput && chooseImagesBtn) {
        console.log('Form, file input, and choose button found. Initializing WorkImagesManager.');
        workImagesManagerInstance = new WorkImagesManager(); // Assign the instance
    } else {
        console.log('Form, file input, or choose button not found yet.');
    }
}

// Try initializing when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded fired');
    initializeWorkImages();
});

// Add a delayed check as a fallback (redundant with proper DOMContentLoaded, but kept as a safeguard)
setTimeout(() => {
    console.log('Delayed check for form after 500ms.');
    initializeWorkImages();
}, 500); 