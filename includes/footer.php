</main>

<?php
// Top categories for footer links (safe even if table is empty)
$footerCats = [];
try { $footerCats = db()->query("SELECT name, slug FROM categories ORDER BY name LIMIT 5")->fetchAll(); } catch (Throwable $e) {}
?>

<!-- CTA strip -->
<section>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-6">
    <div class="reveal relative overflow-hidden rounded-[28px] obsidian px-6 py-14 sm:px-14 sm:py-16">
      <div class="hero-aurora"><span></span><span></span><span></span></div>
      <div class="absolute inset-0 bg-grid"></div>
      <div class="relative grid lg:grid-cols-2 gap-8 items-center">
        <div>
          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full obsidian-inset text-sm font-semibold text-brand-300"><?= icon('rocket','w-4 h-4') ?> Start today</span>
          <h2 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-white tracking-tightest">Ready to upgrade your career?</h2>
          <p class="mt-3 text-slate-300 max-w-md">Book a free counselling session and find the program that fits your goals.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 lg:justify-end">
          <a href="<?= url('contact.php') ?>" class="btn-glass btn-shine inline-flex items-center justify-center gap-2 px-7 py-3.5">Book Free Demo <?= icon('arrow-right','w-4 h-4') ?></a>
          <a href="tel:<?= e(setting('phone')) ?>" class="btn3d-dark inline-flex items-center justify-center gap-2 px-7 py-3.5"><?= icon('phone','w-4 h-4') ?> Call Now</a>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="relative obsidian text-slate-400 overflow-hidden">
  <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-brand-500/60 to-transparent"></div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 pt-16 pb-10">
    <!-- Newsletter -->
    <div class="grid lg:grid-cols-2 gap-8 items-center pb-12 border-b border-white/10">
      <div>
        <h3 class="font-display text-2xl font-bold text-white tracking-tightest">Get course updates &amp; offers</h3>
        <p class="mt-2 text-sm text-slate-400 max-w-md">Join our newsletter for new batches, scholarships and free workshops. No spam, ever.</p>
      </div>
      <form id="newsletterForm" class="flex flex-col sm:flex-row gap-3 lg:justify-end" novalidate>
        <div class="relative flex-1 max-w-md">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 z-10"><?= icon('mail','w-5 h-5') ?></span>
          <input type="email" name="email" required placeholder="Enter your email" class="w-full pl-12 pr-4 py-3.5 rounded-full obsidian-inset text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
        </div>
        <button class="btn-glass btn-shine inline-flex items-center justify-center gap-2 px-6 py-3.5 whitespace-nowrap">Subscribe <?= icon('send','w-4 h-4') ?></button>
      </form>
    </div>

    <!-- Columns -->
    <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-12 py-12">
      <div class="lg:col-span-4">
        <a href="<?= url('index.php') ?>" class="flex items-center gap-2.5 mb-4">
          <span class="grid place-items-center w-10 h-10 rounded-2xl tile-accent text-white font-display font-bold text-lg">N</span>
          <span class="font-display font-bold text-xl text-white tracking-tightest"><?= e(setting('site_name')) ?></span>
        </a>
        <p class="text-sm leading-relaxed text-slate-400 max-w-xs"><?= e(setting('about_short')) ?></p>
        <div class="flex flex-wrap gap-2 mt-6">
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full obsidian-inset text-xs text-slate-300"><?= icon('shield-check','w-3.5 h-3.5 text-emerald-400') ?> Verified certificates</span>
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full obsidian-inset text-xs text-slate-300"><?= icon('star','w-3.5 h-3.5 text-amber-400') ?> <?= e(setting('hero_stat_rating')) ?> rated</span>
        </div>
      </div>

      <div class="lg:col-span-2">
        <h4 class="text-white font-semibold mb-4 text-sm">Courses</h4>
        <ul class="space-y-3 text-sm">
          <?php foreach ($footerCats as $fc): ?>
            <li><a href="<?= url('courses.php?cat=' . urlencode($fc['slug'])) ?>" class="hover:text-white transition"><?= e($fc['name']) ?></a></li>
          <?php endforeach; ?>
          <li><a href="<?= url('courses.php') ?>" class="text-brand-300 hover:text-brand-200 transition font-medium">All courses →</a></li>
        </ul>
      </div>

      <div class="lg:col-span-2">
        <h4 class="text-white font-semibold mb-4 text-sm">Company</h4>
        <ul class="space-y-3 text-sm">
          <li><a href="<?= url('about.php') ?>" class="hover:text-white transition">About Us</a></li>
          <li><a href="<?= url('projects.php') ?>" class="hover:text-white transition">Student Projects</a></li>
          <li><a href="<?= url('verify.php') ?>" class="hover:text-white transition">Verify Certificate</a></li>
          <li><a href="<?= url('contact.php') ?>" class="hover:text-white transition">Contact</a></li>
        </ul>
      </div>

      <div class="lg:col-span-2">
        <h4 class="text-white font-semibold mb-4 text-sm">Portals</h4>
        <ul class="space-y-3 text-sm">
          <li><a href="<?= url('student/login.php') ?>" class="hover:text-white transition">Student Login</a></li>
          <li><a href="<?= url('admin/login.php') ?>" class="hover:text-white transition">Admin Login</a></li>
        </ul>
      </div>

      <div class="lg:col-span-2">
        <h4 class="text-white font-semibold mb-4 text-sm">Contact</h4>
        <ul class="space-y-3 text-sm">
          <li><a href="tel:<?= e(setting('phone')) ?>" class="flex items-start gap-2.5 hover:text-white transition"><span class="text-brand-400 mt-0.5"><?= icon('phone','w-4 h-4') ?></span><?= e(setting('phone')) ?></a></li>
          <li><a href="mailto:<?= e(setting('email')) ?>" class="flex items-start gap-2.5 hover:text-white transition break-all"><span class="text-brand-400 mt-0.5"><?= icon('mail','w-4 h-4') ?></span><?= e(setting('email')) ?></a></li>
          <li class="flex items-start gap-2.5"><span class="text-brand-400 mt-0.5"><?= icon('map-pin','w-4 h-4') ?></span><span><?= e(setting('address')) ?></span></li>
        </ul>
      </div>
    </div>

    <!-- Bottom bar -->
    <div class="pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-xs text-slate-500 order-2 sm:order-1">&copy; <?= date('Y') ?> <?= e(setting('site_name')) ?>. All rights reserved.</p>
      <div class="flex items-center gap-2.5 order-1 sm:order-2">
        <?php foreach (['facebook','instagram','linkedin','youtube'] as $k): ?>
          <a href="<?= e(setting($k, '#')) ?>" target="_blank" rel="noopener" aria-label="<?= e($k) ?>" class="grid place-items-center w-10 h-10 rounded-xl obsidian-panel text-slate-300 hover:text-white hover:-translate-y-0.5 transition"><?= icon($k,'w-[18px] h-[18px]') ?></a>
        <?php endforeach; ?>
      </div>
      <p class="text-xs text-slate-500 order-3 flex items-center gap-1.5">Built with <span class="text-rose-400"><?= icon('heart','w-3.5 h-3.5') ?></span> on PHP &amp; MySQL</p>
    </div>
  </div>
</footer>

<!-- Floating WhatsApp -->
<a href="https://wa.me/<?= e(preg_replace('/\D/', '', setting('whatsapp'))) ?>" target="_blank" rel="noopener"
   class="fixed bottom-5 right-5 z-40 grid place-items-center w-14 h-14 rounded-full text-white hover:scale-110 transition" style="background:linear-gradient(160deg,#2af36e,#128c43);box-shadow:inset 0 1px 0 rgba(255,255,255,.5),inset 0 -3px 8px rgba(4,80,30,.5),0 12px 26px -6px rgba(18,140,67,.6)" aria-label="Chat on WhatsApp">
  <?= icon('whatsapp','w-7 h-7') ?>
</a>

<!-- Back to top -->
<button id="toTop" class="fixed bottom-5 right-[5.5rem] z-40 grid place-items-center w-12 h-12 rounded-full btn3d-light text-ink opacity-0 pointer-events-none transition" aria-label="Back to top">
  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
</button>

<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
