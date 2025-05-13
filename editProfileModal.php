<link rel="stylesheet" href="editmodal.css">

<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>تعديل الملف الشخصي</h2>
        <form method="POST" action="updateProfile.php">
            <label for="first_name">الاسم الأول:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>

            <label for="last_name">اسم العائلة:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>

            <label for="bio">نبذة:</label>
            <textarea id="bio" name="bio" required><?php echo $bio; ?></textarea>

            <label for="skills">المهارات:</label>
            <input type="text" id="skills" name="skills" value="<?php echo $skills; ?>" required>

            <label for="location">الموقع:</label>
            <input type="text" id="location" name="location" value="<?php echo $location; ?>" required>

            <label for="experience">سنوات الخبرة:</label>
            <input type="text" id="experience" name="experience" value="<?php echo $experience; ?>" required>

            <label for="commercial_license">الرخصة التجارية:</label>
            <input type="text" id="commercial_license" name="commercial_license" value="<?php echo $commercial_license; ?>" required>

            <button type="submit">حفظ التعديلات</button>
        </form>
    </div>
</div>

<script>
// Modal functionality
const modal = document.getElementById('editProfileModal');
const closeButton = document.querySelector('.close-button');

function openModal() {
    modal.style.display = 'block';
}

function closeModal() {
    modal.style.display = 'none';
}

closeButton.addEventListener('click', closeModal);
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        closeModal();
    }
});
</script>
