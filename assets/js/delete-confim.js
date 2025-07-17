/**
 * Handles delete confirmation using SweetAlert
 */
document.addEventListener('DOMContentLoaded', function() {
    // Attach click handlers to all delete buttons
    document.querySelectorAll('.btn-danger').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            
            Swal.fire({
                title: 'Confirm Deletion',
                html: `Are you sure you want to delete <strong>${userName}</strong>? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});