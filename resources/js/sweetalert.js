<!-- Di resources/views/layouts/app.blade.php sebelum </body> -->
<script>
// Global SweetAlert2 Configuration
const SwalModal = {
    // Delete Confirmation
    deleteConfirm: function(options) {
        const defaults = {
            title: 'Hapus Data?',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'px-4 py-2 text-sm font-medium',
                cancelButton: 'px-4 py-2 text-sm font-medium'
            }
        };

        const config = { ...defaults, ...options };
        
        return Swal.fire(config);
    },

    // Success Message
    success: function(message) {
        Swal.fire({
            title: 'Berhasil!',
            text: message,
            icon: 'success',
            confirmButtonColor: '#10B981',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'rounded-lg'
            }
        });
    },

    // Error Message
    error: function(message) {
        Swal.fire({
            title: 'Error!',
            text: message,
            icon: 'error',
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'rounded-lg'
            }
        });
    },

    // Loading
    loading: function(title = 'Memproses...') {
        Swal.fire({
            title: title,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
};

// Global Delete Handler
function initDeleteHandler(button, customOptions = {}) {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        const form = this.closest('form');
        const itemName = this.getAttribute('data-item-name') || 'data ini';
        const itemDetails = this.getAttribute('data-item-details') || '';
        
        const options = {
            title: `Hapus ${itemName}?`,
            html: `
                <div class="text-left">
                    <p class="mb-3">Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?</p>
                    ${itemDetails ? `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-2">
                        <div class="text-sm text-gray-700">
                            ${itemDetails}
                        </div>
                    </div>
                    ` : ''}
                    <p class="text-red-600 font-medium mt-3 text-sm">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
            `,
            ...customOptions
        };

        SwalModal.deleteConfirm(options).then((result) => {
            if (result.isConfirmed) {
                SwalModal.loading('Menghapus...');
                form.submit();
            }
        });
    });
}
</script>