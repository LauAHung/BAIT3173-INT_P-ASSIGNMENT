// public/js/Refund.js

document.addEventListener('DOMContentLoaded', function () {
    // Success popup
    if (window.refundSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: window.refundSuccess,
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = refundRedirectUrl; // redirect after OK
        });
    }

    // Error popup
    if (window.refundError) {
        Swal.fire({
            icon: 'error',
            title: 'Refund Blocked',
            text: window.refundError,
            confirmButtonColor: '#d33'
        }).then(() => {
            window.location.href = refundRedirectUrl; // redirect after OK
        });
    }
});
