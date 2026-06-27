// ============================================================
//  Nexora Institute — premium frontend interactions
// ============================================================
(function () {
  'use strict';

  /* ---- Mobile menu (slide-in panel) ---- */
  const menuBtn   = document.getElementById('menuBtn');
  const menu      = document.getElementById('mobileMenu');
  const panel     = document.getElementById('mmPanel');
  const overlay   = document.getElementById('mmOverlay');
  const closeBtn  = document.getElementById('menuClose');

  function openMenu() {
    if (!menu) return;
    menu.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => panel && panel.classList.remove('translate-x-full'));
  }
  function closeMenu() {
    if (!menu) return;
    panel && panel.classList.add('translate-x-full');
    document.body.style.overflow = '';
    setTimeout(() => menu.classList.add('hidden'), 300);
  }
  menuBtn  && menuBtn.addEventListener('click', openMenu);
  closeBtn && closeBtn.addEventListener('click', closeMenu);
  overlay  && overlay.addEventListener('click', closeMenu);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });

  /* ---- Floating navbar: tighten on scroll ---- */
  const navbar    = document.getElementById('navbar');
  const navbarBar = document.getElementById('navbar-bar');
  const onScroll = () => {
    const scrolled = window.scrollY > 8;
    if (navbarBar) {
      navbarBar.classList.toggle('scale-[.99]', scrolled);
      navbarBar.classList.toggle('shadow-raised', scrolled);
    }
    const toTop = document.getElementById('toTop');
    if (toTop) {
      const show = window.scrollY > 600;
      toTop.classList.toggle('opacity-0', !show);
      toTop.classList.toggle('pointer-events-none', !show);
    }
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---- Back to top ---- */
  const toTop = document.getElementById('toTop');
  toTop && toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* ---- Reveal on scroll ---- */
  const reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && reveals.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) { entry.target.classList.add('in'); io.unobserve(entry.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach((el) => io.observe(el));
  } else {
    reveals.forEach((el) => el.classList.add('in'));
  }

  /* ---- Count-up stats ---- */
  const counters = document.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window && counters.length) {
    const cio = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const target = parseFloat(el.dataset.count);
        const suffix = el.dataset.suffix || '';
        const prefix = el.dataset.prefix || '';
        const dur = 1500, start = performance.now();
        const step = (now) => {
          const p = Math.min((now - start) / dur, 1);
          const eased = 1 - Math.pow(1 - p, 3);
          const val = target * eased;
          el.textContent = prefix + (target % 1 === 0 ? Math.floor(val).toLocaleString('en-IN') : val.toFixed(1)) + suffix;
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
        cio.unobserve(el);
      });
    }, { threshold: 0.5 });
    counters.forEach((el) => cio.observe(el));
  }

  /* ---- FAQ accordion ---- */
  document.querySelectorAll('[data-faq]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const panel = btn.nextElementSibling;
      const icon  = btn.querySelector('[data-faq-icon]');
      const open  = !panel.style.maxHeight || panel.style.maxHeight === '0px';
      // close siblings
      document.querySelectorAll('[data-faq] + div').forEach((p) => { p.style.maxHeight = '0px'; });
      document.querySelectorAll('[data-faq-icon]').forEach((i) => i.classList.remove('rotate-45'));
      if (open) {
        panel.style.maxHeight = panel.scrollHeight + 'px';
        icon && icon.classList.add('rotate-45');
      }
    });
  });

  /* ---- Newsletter (client-side success state) ---- */
  const nlForm = document.getElementById('newsletterForm');
  if (nlForm) {
    nlForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const email = nlForm.querySelector('input[type="email"]');
      if (!email || !email.value || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value)) {
        email && email.focus();
        return;
      }
      nlForm.innerHTML = '<div class="flex items-center gap-2.5 px-5 py-3.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm font-medium"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Thanks for subscribing! We\'ll be in touch.</div>';
    });
  }

  /* ---- Subtle pointer tilt on [data-tilt] ---- */
  if (window.matchMedia('(pointer:fine)').matches) {
    document.querySelectorAll('[data-tilt]').forEach((el) => {
      el.addEventListener('mousemove', (e) => {
        const r = el.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5;
        const y = (e.clientY - r.top) / r.height - 0.5;
        el.style.transform = `perspective(900px) rotateY(${x * 6}deg) rotateX(${-y * 6}deg)`;
      });
      el.addEventListener('mouseleave', () => { el.style.transform = ''; });
    });
  }
})();
