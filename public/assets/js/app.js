/**
 * Vanilla JS Application Scripts - ICP Informatics Company of Perú
 */
document.addEventListener('DOMContentLoaded', () => {
    // Mobile Sidebar Toggle
    const sidebar = document.getElementById('app-sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (toggleBtn && sidebar && backdrop) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        });

        backdrop.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        });
    }

    // Auto-dismiss success alert after 6 seconds
    const successAlerts = document.querySelectorAll('.bg-emerald-50');
    successAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 6000);
    });

    // Close modals on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-"]').forEach(modal => {
                modal.classList.add('hidden');
            });
        }
    });
});

