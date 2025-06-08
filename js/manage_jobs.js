function toggleEditForm(jobId) {
    const form = document.getElementById(`edit-form-${jobId}`);
    form.classList.toggle('show');
}

function confirmDelete(jobId) {
    if (confirm('هل أنت متأكد من حذف هذه الوظيفة؟ سيتم إخطار جميع المتقدمين.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="job_id" value="${jobId}">
            <input type="hidden" name="delete_job" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
} 