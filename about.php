<?php
require_once __DIR__ . '/includes/functions.php';
$page = 'about';
$page_title = 'About Us';
include __DIR__ . '/includes/header.php';
?>

<section class="relative mx-3 sm:mx-6 mt-4 rounded-[28px] sm:rounded-[34px] obsidian text-white overflow-hidden">
  <div class="hero-aurora"><span></span><span></span><span></span></div>
  <div class="absolute inset-0 bg-grid"></div>
  <div class="relative max-w-7xl mx-auto px-5 sm:px-10 py-16 lg:py-24">
    <div class="max-w-3xl">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full obsidian-inset text-sm font-semibold text-brand-300"><?= icon('building','w-4 h-4') ?> About <?= e(setting('site_name')) ?></span>
      <h1 class="mt-4 font-display text-[30px] sm:text-4xl lg:text-5xl font-bold leading-[1.1] tracking-tightest">We turn ambition into <span class="text-gradient">employable skills.</span></h1>
      <p class="mt-6 text-base sm:text-lg text-slate-300"><?= e(setting('about_short')) ?></p>
    </div>
  </div>
</section>

<section class="py-14 sm:py-20 lg:py-24">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 grid md:grid-cols-2 gap-6">
    <div class="reveal tactile p-7 sm:p-8">
      <span class="grid place-items-center w-14 h-14 rounded-2xl tile-accent text-white"><?= icon('target','w-6 h-6') ?></span>
      <h2 class="mt-5 font-display text-2xl font-bold text-ink tracking-tightest text-emboss">Our Mission</h2>
      <p class="mt-3 text-slate-600 leading-relaxed">To make world-class, practical education accessible — equipping every learner with the real skills, projects and confidence to launch a thriving career.</p>
    </div>
    <div class="reveal tactile p-7 sm:p-8" data-delay="1">
      <span class="grid place-items-center w-14 h-14 rounded-2xl tile-rose text-white"><?= icon('rocket','w-6 h-6') ?></span>
      <h2 class="mt-5 font-display text-2xl font-bold text-ink tracking-tightest text-emboss">Our Vision</h2>
      <p class="mt-3 text-slate-600 leading-relaxed">To be the most trusted skill-development institute, recognized globally for the quality of our graduates and the strength of our industry network.</p>
    </div>
  </div>
</section>

<section class="py-14 sm:py-20 lg:py-24">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-2xl mx-auto reveal">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full chip-inset text-sm font-semibold text-brand-600"><?= icon('heart','w-4 h-4') ?> Our Values</span>
      <h2 class="mt-4 font-display text-3xl sm:text-[40px] font-bold text-ink tracking-tightest text-emboss">What we stand for</h2>
    </div>
    <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
      <?php foreach ([
        ['sparkles','Practical First','Learn by building real things, not memorizing slides.'],
        ['users','Mentorship','Small batches with mentors who genuinely care.'],
        ['trophy','Outcome Driven','Everything maps to a career outcome.'],
        ['shield-check','Integrity','Honest guidance and verified credentials.'],
      ] as $i => $v): ?>
        <div class="reveal tactile tactile-lift p-6" data-delay="<?= $i ?>">
          <span class="grid place-items-center w-14 h-14 rounded-2xl chip-inset text-brand-600"><?= icon($v[0],'w-6 h-6') ?></span>
          <h3 class="mt-4 font-semibold text-ink"><?= $v[1] ?></h3>
          <p class="mt-2 text-sm text-slate-500"><?= $v[2] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="mx-3 sm:mx-6 my-4 rounded-[28px] sm:rounded-[34px] obsidian text-white py-14 sm:py-16 relative overflow-hidden">
  <div class="absolute inset-0 bg-grid"></div>
  <div class="relative max-w-7xl mx-auto px-5 sm:px-10 grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
    <?php foreach ([['hero_stat_students','Students Trained'],['hero_stat_courses','Courses'],['hero_stat_partners','Hiring Partners'],['hero_stat_rating','Learner Rating']] as $s): ?>
      <div class="reveal obsidian-inset py-6 px-3"><p class="font-display text-3xl sm:text-4xl font-bold tracking-tightest"><?= e(setting($s[0])) ?></p><p class="mt-1 text-sm text-slate-400"><?= $s[1] ?></p></div>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
