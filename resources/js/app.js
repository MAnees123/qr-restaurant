import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

// Styled confirm dialog; resolves true when the user confirms.
window.confirmDialog = (message, { title = 'Are you sure?', confirmText = 'Yes, continue', danger = true } = {}) =>
    Swal.fire({
        title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancel',
        confirmButtonColor: danger ? '#dc2626' : '#059669',
        reverseButtons: true,
        focusCancel: danger,
    }).then((result) => result.isConfirmed);

window.alertDialog = (message, icon = 'error', title = null) =>
    Swal.fire({ title: title ?? (icon === 'error' ? 'Oops...' : 'Done'), text: message, icon, confirmButtonColor: '#4f46e5' });

// Any <form data-confirm="..."> or submit button with data-confirm="..." asks before submitting.
document.addEventListener('submit', (event) => {
    const form = event.target;
    const message = event.submitter?.dataset.confirm ?? form.dataset.confirm;
    if (!message || form.dataset.confirmed === '1') {
        return;
    }
    event.preventDefault();
    const danger = !/^add\b/i.test(message);
    window.confirmDialog(message, { danger, confirmText: danger ? 'Yes, do it' : 'Yes, add it' }).then((ok) => {
        if (ok) {
            form.dataset.confirmed = '1';
            form.requestSubmit(event.submitter ?? undefined);
        }
    });
}, true);
