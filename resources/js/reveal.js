const items = document.querySelectorAll('.reveal-on-scroll');
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!reduce && 'IntersectionObserver' in window && items.length) {
      const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                  if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                  }
            });
      }, { threshold: 0.12 });
      items.forEach((el) => io.observe(el));
} else {
      items.forEach((el) => el.classList.add('is-visible'));
}