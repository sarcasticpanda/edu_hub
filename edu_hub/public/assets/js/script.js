document.addEventListener('DOMContentLoaded', () => {
    const authButton = document.getElementById('authButton');
    authButton.addEventListener('click', () => {
        const authModal = new bootstrap.Modal(document.getElementById('authModal'));
        authModal.show();
    });
});