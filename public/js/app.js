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
});