document.addEventListener('DOMContentLoaded', () => {
    const notices = document.querySelectorAll('[data-auto-hide]');
    notices.forEach((el) => {
        setTimeout(() => el.remove(), 5000);
    });
});
