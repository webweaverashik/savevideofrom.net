const btn = document.getElementById('scrollTop');

if (btn) {
    const toggle = () => btn.classList.toggle('show', window.scrollY > 400);
    toggle();
    window.addEventListener('scroll', toggle, { passive: true });
    btn.addEventListener('click', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
    });
}