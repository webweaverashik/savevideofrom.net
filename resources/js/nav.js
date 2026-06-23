// Mobile menu toggle
const burger = document.getElementById('navBurger');
const mobile = document.getElementById('mobileNav');
burger?.addEventListener('click', () => {
    const open = mobile.classList.toggle('hidden');
    burger.setAttribute('aria-expanded', open ? 'false' : 'true');
});

// Mobile submenu toggles
document.querySelectorAll('[data-mobile-sub]').forEach((btn) => {
    btn.addEventListener('click', () => btn.nextElementSibling?.classList.toggle('hidden'));
});

// Desktop dropdowns
const closeAll = () => document.querySelectorAll('[data-dropdown-panel]').forEach((p) => p.classList.add('hidden'));

document.querySelectorAll('[data-dropdown]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const panel = btn.nextElementSibling;
        const isHidden = panel.classList.contains('hidden');
        closeAll();
        if (isHidden) panel.classList.remove('hidden');
    });
});

document.addEventListener('click', closeAll);
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAll(); });