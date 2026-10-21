/* ==========================================================================
   BantuNet — small UI helpers (plain JavaScript, no build step)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {

    /* 1. Flash toasts: hide after 5 seconds, or when × is clicked */
    document.querySelectorAll('[data-toast]').forEach((toast) => {
        const hide = () => {
            toast.classList.add('is-hiding');
            setTimeout(() => toast.remove(), 300);
        };
        toast.querySelector('[data-toast-close]')?.addEventListener('click', hide);
        setTimeout(hide, 5000);
    });

    /* 2. Open/close an element:  <button data-toggle="#sidebar"> */
    document.querySelectorAll('[data-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.toggle);
            if (!target) return;
            const isOpen = target.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen);
        });
    });

    /* 3. Show / hide password:  <button data-password-toggle="password"> */
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.textContent = show ? 'Hide' : 'Show';
        });
    });

    /* 4. Ask before dangerous actions:  <form data-confirm="Delete this?"> */
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    /* 5. Preview a chosen photo before uploading:  <input type="file" data-image-preview="image"> */
    document.querySelectorAll('[data-image-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const preview = document.querySelector(`[data-image-preview-target="${input.dataset.imagePreview}"]`);
            const file = input.files[0];
            if (!preview || !file) return;
            preview.src = URL.createObjectURL(file);
            preview.hidden = false;
        });
    });

    /* 6. Submit a filter form as soon as a dropdown changes:  <select data-autosubmit> */
    document.querySelectorAll('select[data-autosubmit]').forEach((select) => {
        select.addEventListener('change', () => select.form?.requestSubmit());
    });

    /* 7. Fill a field with example text:  <button data-fill="#about" data-text="..."> */
    document.querySelectorAll('[data-fill]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.fill);
            if (!target) return;
            target.value = button.dataset.text;
            target.focus();
        });
    });

    /* 8. Show a "working" label while a slow form submits:  <button data-loading-text="Matching..."> */
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('[data-loading-text]');
            if (!button) return;
            button.textContent = button.dataset.loadingText;
            button.disabled = true;
        });
    });
});