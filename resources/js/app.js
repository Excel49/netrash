// NetraTrash Custom JavaScript
console.log('NetraTrash loaded');

// Global function untuk SweetAlert
window.showAlert = function(type, title, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }
};
