<?php
require_once __DIR__ . '/includes/functions.php';
$page = 'home';

// Featured courses
$courses = db()->query(
  "SELECT c.*, cat.name AS category FROM courses c
   LEFT JOIN categories cat ON cat.id = c.category_id
   WHERE c.status='active' ORDER BY c.is_featured DESC, c.id ASC LIMIT 6"
)->fetchAll();

// Categories with live course counts
$categories = db()->query(
  "SELECT cat.*, (SELECT COUNT(*) FROM courses WHERE category_id = cat.id AND status='active') AS course_count
   FROM categories cat ORDER BY cat.name"
)->fetchAll();

$projects = db()->query("SELECT p.*, c.title AS course FROM projects p LEFT JOIN courses c ON c.id=p.course_id WHERE p.status='published' ORDER BY p.id DESC LIMIT 3")->fetchAll();

include __DIR__ . '/includes/header.php';

$catIcons = ['cad-design'=>'compass','software-it'=>'code','data-ai'=>'cpu','business-sap'=>'briefcase','digital-marketing'=>'megaphone'];
?>

<!-- ============ HERO ============ -->
<section class="relative mx-3 sm:mx-6 mt-4 rounded-[34px] obsidian text-white overflow-hidden">
  <div class="hero-aurora"><span></span><span></span><span></span></div>
  <div class="absolute inset-0 bg-grid"></div>
  <div class="relative max-w-7xl mx-auto px-5 sm:px-10 pt-14 pb-20 lg:pt-20 lg:pb-24">
    <div class="grid lg:grid-cols-12 gap-12 items-center">
      <div class="lg:col-span-6 animate-fadeUp">
        <span class="inline-flex items-center gap-2 pl-1.5 pr-3.5 py-1.5 rounded-full obsidian-panel text-[13px] font-medium">
          <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 pulse-ring"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span></span>
          Trusted by <?= e(setting('hero_stat_students')) ?> learners across India
        </span>
        <h1 class="mt-6 font-display text-[40px] leading-[1.04] sm:text-6xl font-bold tracking-tightest">
          Learn the skills that <span class="text-gradient">get you hired.</span>
        </h1>
        <p class="mt-6 text-lg text-slate-300 max-w-xl leading-relaxed">
          Mentor-led, project-based programs in design, engineering &amp; IT — built with hiring partners and backed by a verified, shareable certificate.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3.5">
          <a href="<?= url('courses.php') ?>" class="btn-glass btn-shine group inline-flex items-center justify-center gap-2 px-7 py-3.5">
            Explore Courses <?= icon('arrow-right','w-4 h-4 group-hover:translate-x-0.5 transition-transform') ?>
          </a>
          <a href="<?= url('contact.php') ?>" class="btn3d-dark inline-flex items-center justify-center gap-2 px-7 py-3.5">
            <?= icon('video','w-4 h-4') ?> Book Free Demo
          </a>
        </div>
        <div class="mt-9 flex flex-wrap items-center gap-x-8 gap-y-4">
          <div class="flex items-center gap-3">
            <div class="flex -space-x-2.5">
              <?php foreach (['f59e0b','6366f1','ec4899','10b981'] as $c): ?>
                <span class="w-9 h-9 rounded-full border-2 border-ink ring-1 ring-white/10 shadow-lg" style="background:linear-gradient(135deg,#<?= $c ?>,#<?= $c ?>aa)"></span>
              <?php endforeach; ?>
              <span class="grid place-items-center w-9 h-9 rounded-full border-2 border-ink obsidian-panel text-[10px] font-bold">+5k</span>
            </div>
            <div class="text-sm">
              <div class="flex items-center gap-1 text-amber-400"><?php for($i=0;$i<5;$i++) echo icon('star','w-3.5 h-3.5'); ?></div>
              <p class="text-slate-400 text-xs mt-0.5"><?= e(setting('hero_stat_rating')) ?> from 3,200+ reviews</p>
            </div>
          </div>
          <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
          <div class="flex items-center gap-2 text-sm text-slate-300"><?= icon('shield-check','w-5 h-5 text-emerald-400') ?> Verified certificates</div>
        </div>
      </div>

      <!-- Hero product card -->
      <div class="lg:col-span-6 animate-fadeUp" style="animation-delay:.12s">
        <div class="relative" data-tilt>
          <div class="absolute -inset-4 bg-gradient-to-tr from-brand-500/30 to-fuchsia-500/20 blur-2xl rounded-[40px]"></div>
          <div class="relative rounded-3xl obsidian-panel p-5 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-2">
                <span class="grid place-items-center w-9 h-9 rounded-xl tile-accent text-sm font-display font-bold text-white">N</span>
                <span class="text-sm font-semibold">Student Dashboard</span>
              </div>
              <span class="flex gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-white/20"></span><span class="w-2.5 h-2.5 rounded-full bg-white/20"></span><span class="w-2.5 h-2.5 rounded-full bg-emerald-400/70"></span></span>
            </div>
            <?php $f = $courses[0] ?? null; if ($f): $fp = $f['discount_price'] ?: $f['price']; ?>
            <div class="rounded-2xl p-5 relative overflow-hidden tile-accent">
              <div class="absolute inset-0 bg-dots opacity-30"></div>
              <div class="relative flex items-center justify-between text-xs text-white/80">
                <span class="px-2 py-1 rounded-md bg-black/15"><?= e($f['category'] ?? 'Course') ?></span>
                <span class="inline-flex items-center gap-1"><?= icon('star','w-3 h-3 text-amber-300') ?> <?= e(setting('hero_stat_rating')) ?></span>
              </div>
              <h3 class="relative mt-3 font-display text-xl font-bold"><?= e($f['title']) ?></h3>
              <div class="relative mt-4 h-2 rounded-full bg-black/25 overflow-hidden shadow-inner"><span class="block h-full w-[72%] rounded-full bg-white shadow"></span></div>
              <p class="relative mt-1.5 text-[11px] text-white/80">72% completed · keep going!</p>
            </div>
            <div class="grid grid-cols-3 gap-3 mt-4">
              <?php
                $mini = [['clock',$f['duration'],'Duration'],['signal',$f['level'],'Level'],['wallet',money($fp),'Fee']];
                foreach ($mini as $m): ?>
                <div class="rounded-xl obsidian-inset p-3">
                  <span class="text-brand-300"><?= icon($m[0],'w-4 h-4') ?></span>
                  <p class="mt-2 text-sm font-bold leading-none text-white"><?= e($m[1]) ?></p>
                  <p class="text-[10px] text-slate-400 mt-1"><?= $m[2] ?></p>
                </div>
              <?php endforeach; ?>
            </div>
            <a href="<?= url('course.php?slug=' . urlencode($f['slug'])) ?>" class="btn-glass mt-4 flex items-center justify-center gap-2 px-5 py-3 text-sm">View Program <?= icon('arrow-up-right','w-4 h-4') ?></a>
            <?php endif; ?>
          </div>
          <!-- floating pills -->
          <div class="absolute -bottom-4 -left-4 px-4 py-3 rounded-2xl tactile animate-floaty flex items-center gap-2.5" style="animation-delay:-2s">
            <span class="grid place-items-center w-9 h-9 rounded-xl tile-emerald text-white"><?= icon('award','w-5 h-5') ?></span>
            <div class="text-left"><p class="text-xs font-bold leading-none text-ink">Certificate</p><p class="text-[10px] text-slate-500 mt-1">Issued &amp; verifiable</p></div>
          </div>
          <div class="absolute -top-4 -right-3 px-3.5 py-2.5 rounded-2xl tactile animate-floaty flex items-center gap-2" style="animation-delay:-4s">
            <span class="grid place-items-center w-8 h-8 rounded-lg tile-accent text-white"><?= icon('trending-up','w-4 h-4') ?></span>
            <div class="text-left"><p class="text-[11px] font-bold leading-none text-ink">98% placed</p><p class="text-[10px] text-slate-500 mt-0.5">after graduation</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Logo cloud -->
  <div class="relative border-t border-white/10 py-7">
    <p class="text-center text-xs uppercase tracking-[0.2em] text-slate-500 mb-5">Our learners master industry-standard tools</p>
    <div class="marquee-wrap overflow-hidden">
      <div class="marquee">
        <?php $brands = ['Autodesk','Microsoft','Google','SAP','Adobe','SolidWorks','Python','AWS']; foreach (array_merge($brands,$brands) as $b): ?>
          <span class="text-lg font-display font-semibold text-white/35 whitespace-nowrap"><?= $b ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ============ STATS ============ -->
<section class="py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-2 lg:grid-cols-4 gap-5">
    <?php
      $stats = [
        ['12500','+','Students trained','users'],
        ['40','+','Expert-led courses','book-open'],
        ['200','+','Hiring partners','building'],
        ['98','%','Placement support','trophy'],
      ];
      foreach ($stats as $i => $s): ?>
      <div class="reveal tactile tactile-lift p-7 text-center group" data-delay="<?= $i ?>">
        <span class="inline-grid place-items-center w-12 h-12 rounded-2xl tile-accent text-white mb-3 group-hover:-translate-y-0.5 transition-transform"><?= icon($s[3],'w-5 h-5') ?></span>
        <p class="font-display text-4xl font-bold text-ink tracking-tightest text-emboss"><span data-count="<?= $s[0] ?>" data-suffix="<?= $s[1] ?>">0</span></p>
        <p class="mt-1.5 text-sm text-slate-500"><?= $s[2] ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ============ CATEGORIES ============ -->
<section class="py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="max-w-2xl reveal">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full chip-inset text-sm font-semibold text-brand-600"><?= icon('layers','w-4 h-4') ?> Categories</span>
      <h2 class="mt-4 font-display text-3xl sm:text-[40px] font-bold text-ink tracking-tightest leading-tight text-emboss">Find the path that fits your ambition</h2>
      <p class="mt-4 text-slate-500 text-lg">Hands-on tracks designed with industry mentors, from CAD to AI.</p>
    </div>
    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($categories as $i => $cat): $ic = $catIcons[$cat['slug']] ?? 'graduation'; ?>
        <a href="<?= url('courses.php?cat=' . urlencode($cat['slug'])) ?>" class="reveal tactile tactile-lift group relative p-6 overflow-hidden" data-delay="<?= $i % 3 ?>">
          <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-emboss-dots opacity-60 group-hover:scale-125 transition duration-500"></div>
          <div class="relative">
            <div class="flex items-start justify-between">
              <span class="grid place-items-center w-14 h-14 rounded-2xl tile-accent text-white group-hover:-translate-y-0.5 transition-transform"><?= icon($ic,'w-6 h-6') ?></span>
              <?php if ((int)$cat['course_count'] > 0): ?><span class="text-xs font-semibold text-slate-500 chip-inset px-3 py-1.5 rounded-full"><?= (int)$cat['course_count'] ?> course<?= $cat['course_count']>1?'s':'' ?></span><?php endif; ?>
            </div>
            <h3 class="mt-5 font-display text-lg font-bold text-ink group-hover:text-brand-600 transition"><?= e($cat['name']) ?></h3>
            <p class="mt-1.5 text-sm text-slate-500">Industry-ready training, live projects &amp; placement support.</p>
            <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600">Browse courses <?= icon('arrow-right','w-4 h-4 group-hover:translate-x-1 transition-transform') ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ FEATURED COURSES ============ -->
<section class="py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-end justify-between gap-4 reveal">
      <div class="max-w-xl">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full chip-inset text-sm font-semibold text-brand-600"><?= icon('sparkles','w-4 h-4') ?> Popular programs</span>
        <h2 class="mt-4 font-display text-3xl sm:text-[40px] font-bold text-ink tracking-tightest text-emboss">Featured courses</h2>
      </div>
      <a href="<?= url('courses.php') ?>" class="btn3d-light inline-flex items-center gap-1.5 text-sm px-5 py-2.5 text-slate-700">View all courses <?= icon('arrow-right','w-4 h-4') ?></a>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
      <?php foreach ($courses as $i => $c):
        $price = $c['discount_price'] ?: $c['price'];
        $hasDiscount = $c['discount_price'] && $c['discount_price'] < $c['price'];
        $off = $hasDiscount ? round(100 - ($c['discount_price'] / $c['price'] * 100)) : 0;
      ?>
        <article class="reveal tactile tactile-lift group overflow-hidden flex flex-col p-2.5" data-delay="<?= $i % 3 ?>">
          <div class="relative h-40 rounded-2xl tile-accent p-5 flex flex-col justify-between overflow-hidden">
            <div class="absolute inset-0 bg-dots opacity-30"></div>
            <div class="relative flex items-center justify-between">
              <span class="px-2.5 py-1 rounded-full bg-black/20 text-white text-xs font-medium"><?= e($c['category'] ?? 'Course') ?></span>
              <?php if ($hasDiscount): ?><span class="px-2.5 py-1 rounded-full bg-emerald-400 text-emerald-950 text-xs font-bold shadow"><?= $off ?>% OFF</span>
              <?php elseif ($c['is_featured']): ?><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-400 text-amber-950 text-xs font-bold shadow"><?= icon('star','w-3 h-3') ?> Featured</span><?php endif; ?>
            </div>
            <h3 class="relative font-display text-xl font-bold text-white leading-tight"><?= e($c['title']) ?></h3>
          </div>
          <div class="p-4 pt-5 flex flex-col flex-1">
            <p class="text-sm text-slate-500 line-clamp-2"><?= e($c['short_desc']) ?></p>
            <div class="mt-4 flex flex-wrap gap-2 text-xs">
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full chip-inset text-slate-600"><?= icon('clock','w-3.5 h-3.5') ?> <?= e($c['duration']) ?></span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full chip-inset text-slate-600"><?= icon('signal','w-3.5 h-3.5') ?> <?= e($c['level']) ?></span>
            </div>
            <div class="mt-5 pt-4 flex items-center justify-between border-t border-slate-300/40">
              <div>
                <?php if ($hasDiscount): ?><span class="text-xs text-slate-400 line-through"><?= money($c['price']) ?></span><?php endif; ?>
                <p class="font-display text-2xl font-bold text-ink tracking-tightest text-emboss"><?= money($price) ?></p>
              </div>
              <a href="<?= url('course.php?slug=' . urlencode($c['slug'])) ?>" class="btn3d btn-shine inline-flex items-center gap-1.5 px-4 py-2.5 text-sm text-white">Details <?= icon('arrow-up-right','w-4 h-4') ?></a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ WHAT'S INCLUDED ============ -->
<section class="py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-2xl mx-auto reveal">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full chip-inset text-sm font-semibold text-brand-600"><?= icon('puzzle','w-4 h-4') ?> What's included</span>
      <h2 class="mt-4 font-display text-3xl sm:text-[40px] font-bold text-ink tracking-tightest text-emboss">Everything you need to succeed</h2>
      <p class="mt-4 text-slate-500 text-lg">Every program comes loaded with the support and tools to take you from beginner to job-ready.</p>
    </div>
    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
        $included = [
          ['monitor','Live + recorded classes','Attend interactive live sessions and revisit anytime with lifetime recordings.'],
          ['puzzle','Real-world projects','Build a portfolio of industry-grade projects you can show recruiters.'],
          ['headset','1:1 doubt support','Get unstuck fast with dedicated mentor support throughout your journey.'],
          ['wallet-cards','Easy EMI options','Flexible, no-cost installment plans so fees never hold you back.'],
          ['badge-check','Verified certificate','A shareable, uniquely-coded certificate employers can verify instantly.'],
          ['briefcase','Placement assistance','Resume building, mock interviews and intros to 200+ hiring partners.'],
        ];
        foreach ($included as $i => $f): ?>
        <div class="reveal tactile tactile-lift group p-6" data-delay="<?= $i % 3 ?>">
          <span class="grid place-items-center w-14 h-14 rounded-2xl chip-inset text-brand-600 group-hover:text-brand-700 transition"><?= icon($f[0],'w-6 h-6') ?></span>
          <h3 class="mt-5 font-semibold text-ink"><?= $f[1] ?></h3>
          <p class="mt-2 text-sm text-slate-500 leading-relaxed"><?= $f[2] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ WHY US (bento) ============ -->
<section class="mx-3 sm:mx-6 my-4 rounded-[34px] obsidian text-white py-16 lg:py-24 relative overflow-hidden">
  <div class="absolute inset-0 bg-grid"></div>
  <div class="relative max-w-7xl mx-auto px-5 sm:px-10">
    <div class="max-w-2xl reveal">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full obsidian-inset text-sm font-semibold text-brand-300"><?= icon('zap','w-4 h-4') ?> Why Nexora</span>
      <h2 class="mt-4 font-display text-3xl sm:text-[40px] font-bold tracking-tightest">Built for outcomes, not just lectures</h2>
      <p class="mt-4 text-slate-300 text-lg">Everything maps to a real career result — from your first lesson to your first offer.</p>
    </div>
    <div class="mt-12 grid md:grid-cols-3 gap-5">
      <div class="reveal md:row-span-2 obsidian-panel p-7 flex flex-col">
        <span class="grid place-items-center w-14 h-14 rounded-2xl tile-accent text-white"><?= icon('target','w-6 h-6') ?></span>
        <h3 class="mt-5 font-display text-xl font-bold">Industry-aligned curriculum</h3>
        <p class="mt-2 text-sm text-slate-400">Designed with hiring partners and refreshed every quarter so you learn what employers actually use.</p>
        <div class="mt-auto pt-6 grid grid-cols-2 gap-3 text-center">
          <div class="obsidian-inset p-4"><p class="font-display text-2xl font-bold"><?= e(setting('hero_stat_courses')) ?></p><p class="text-[11px] text-slate-400 mt-1">Live courses</p></div>
          <div class="obsidian-inset p-4"><p class="font-display text-2xl font-bold"><?= e(setting('hero_stat_partners')) ?></p><p class="text-[11px] text-slate-400 mt-1">Hiring partners</p></div>
        </div>
      </div>
      <?php
        $why = [
          ['rocket','Real, portfolio-ready projects','Graduate with work you can show recruiters — not just theory.'],
          ['shield-check','Verified digital certificates','Each certificate has a unique code anyone can verify online.'],
          ['headset','Dedicated placement support','Resume reviews, mock interviews and warm intros to partners.'],
          ['users','Mentor-led small batches','Personal attention and doubt-clearing from working professionals.'],
        ];
        foreach ($why as $i => $w): ?>
        <div class="reveal obsidian-panel p-6 hover:-translate-y-1 transition-transform" data-delay="<?= $i % 3 ?>">
          <span class="grid place-items-center w-12 h-12 rounded-xl obsidian-inset text-brand-300"><?= icon($w[0],'w-5 h-5') ?></span>
          <h3 class="mt-4 font-semibold"><?= $w[1] ?></h3>
          <p class="mt-1.5 text-sm text-slate-400"><?= $w[2] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ HOW IT WORKS ============ -->
<section class="py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-2xl mx-auto reveal">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full chip-inset text-sm font-semibold text-brand-600"><?= icon('infinity','w-4 h-4') ?> Simple process</span>
      <h2 class="mt-4 font-display text-3xl sm:text-[40px] font-bold text-ink tracking-tightest text-emboss">From enrollment to employment</h2>
    </div>
    <div class="mt-14 grid md:grid-cols-4 gap-8 relative">
      <div class="hidden md:block absolute top-9 left-[12%] right-[12%] h-1.5 rounded-full tactile-inset"></div>
      <?php
        $steps = [
          ['book-open','Choose a course','Pick from 40+ mentor-led programs that match your goals.'],
          ['video','Learn by building','Attend live sessions and ship real, portfolio-grade projects.'],
          ['award','Earn your certificate','Get a verified certificate the moment you complete the program.'],
          ['briefcase','Get placed','Use our placement support and partner network to land a role.'],
        ];
        foreach ($steps as $i => $s): ?>
        <div class="reveal relative text-center" data-delay="<?= $i ?>">
          <span class="relative z-10 inline-grid place-items-center w-18 h-18 rounded-full tactile text-brand-600 mx-auto" style="width:4.5rem;height:4.5rem"><?= icon($s[0],'w-7 h-7') ?></span>
          <span class="absolute top-0 left-1/2 -translate-x-1/2 -mt-1 ml-8 w-7 h-7 rounded-full tile-accent text-white text-xs font-bold grid place-items-center z-20"><?= $i+1 ?></span>
          <h3 class="mt-5 font-display font-bold text-ink"><?= $s[1] ?></h3>
          <p class="mt-2 text-sm text-slate-500"><?= $s[2] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ OUTCOMES BAND ============ -->
<section class="py-4">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="reveal relative overflow-hidden rounded-[28px] tile-accent px-6 py-12 sm:px-12 text-white">
      <div class="absolute inset-0 bg-dots opacity-25"></div>
      <div class="relative grid lg:grid-cols-4 gap-8 items-center">
        <div class="lg:col-span-1">
          <h2 class="font-display text-2xl font-bold tracking-tightest">Outcomes that speak for themselves</h2>
          <p class="mt-2 text-white/80 text-sm">Real results from real graduates.</p>
        </div>
        <div class="lg:col-span-3 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
          <?php foreach ([['trending-up','3.2x','Avg. salary growth'],['clock-check','60 days','Time to placement'],['users','12,500','Careers launched'],['badge-check','98%','Completion rate']] as $o): ?>
            <div class="rounded-2xl bg-black/10 p-4 shadow-inner">
              <span class="inline-grid place-items-center w-12 h-12 rounded-xl bg-white/15 mx-auto shadow-inner"><?= icon($o[0],'w-5 h-5') ?></span>
              <p class="mt-3 font-display text-3xl font-bold tracking-tightest"><?= e($o[1]) ?></p>
              <p class="text-xs text-white/80 mt-1"><?= e($o[2]) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ PROJECTS PREVIEW ============ -->
<?php if ($projects): ?>
<section class="py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-end justify-between gap-4 reveal">
      <div class="max-w-xl">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full chip-inset text-sm font-semibold text-brand-600"><?= icon('code','w-4 h-4') ?> Student work</span>
        <h2 class="mt-4 font-display text-3xl sm:text-[40px] font-bold text-ink tracking-tightest text-emboss">Projects that speak louder than grades</h2>
      </div>
      <a href="<?= url('projects.php') ?>" class="btn3d-light inline-flex items-center gap-1.5 text-sm px-5 py-2.5 text-slate-700">All projects <?= icon('arrow-right','w-4 h-4') ?></a>
    </div>
    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
      <?php foreach ($projects as $i => $p): ?>
        <div class="reveal tactile tactile-lift overflow-hidden p-2.5" data-delay="<?= $i % 3 ?>">
          <div class="h-40 rounded-2xl obsidian grid place-items-center text-white/25 relative overflow-hidden">
            <div class="absolute inset-0 bg-dots opacity-40"></div>
            <span class="relative"><?= icon('code','w-12 h-12') ?></span>
          </div>
          <div class="p-4 pt-5">
            <?php if ($p['course']): ?><span class="text-xs font-semibold text-brand-600 uppercase tracking-wide"><?= e($p['course']) ?></span><?php endif; ?>
            <h3 class="mt-1.5 font-semibold text-ink"><?= e($p['title']) ?></h3>
            <p class="mt-2 text-sm text-slate-500 line-clamp-2"><?= e($p['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ TESTIMONIALS ============ -->
<section class="py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-2xl mx-auto reveal">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full chip-inset text-sm font-semibold text-brand-600"><?= icon('heart','w-4 h-4') ?> Loved by learners</span>
      <h2 class="mt-4 font-display text-3xl sm:text-[40px] font-bold text-ink tracking-tightest text-emboss">Real stories, real outcomes</h2>
    </div>
    <div class="mt-12 grid lg:grid-cols-3 gap-6">
      <!-- Featured testimonial -->
      <figure class="reveal lg:row-span-2 rounded-[26px] obsidian text-white p-8 flex flex-col relative overflow-hidden">
        <div class="hero-aurora opacity-60"><span></span><span></span></div>
        <span class="relative text-brand-300"><?= icon('quote','w-10 h-10') ?></span>
        <blockquote class="relative mt-4 text-xl font-display leading-snug">"I joined with zero coding background. Six months later I had three live projects and a developer job. The mentorship genuinely changed my life."</blockquote>
        <div class="relative mt-auto pt-8 flex items-center gap-3">
          <span class="grid place-items-center w-12 h-12 rounded-full tile-accent text-white font-bold">RM</span>
          <div><p class="font-semibold">Rahul Mehta</p><p class="text-sm text-slate-400">Full Stack Web Development · Placed at a product startup</p></div>
        </div>
      </figure>

      <?php
        $tst = [
          ['Priya Sharma','AutoCAD Professional','PS','f59e0b','The projects made me job-ready. I was placed within a month of finishing the course.'],
          ['Aisha Khan','SAP FICO','AK','ec4899','Clear, practical and well-structured. The verified certificate genuinely opened doors.'],
          ['Vikram Rao','Data Science with Python','VR','10b981','From spreadsheets to ML models — the mentors made complex topics feel simple.'],
          ['Neha Gupta','Digital Marketing','NG','6366f1','Ran real ad campaigns during the course. Landed a marketing role right after.'],
        ];
        foreach ($tst as $i => $t): ?>
        <figure class="reveal tactile p-6" data-delay="<?= $i % 2 ?>">
          <div class="flex items-center gap-1 text-amber-400 mb-3"><?php for($j=0;$j<5;$j++) echo icon('star','w-4 h-4'); ?></div>
          <blockquote class="text-slate-700 leading-relaxed text-[15px]">"<?= $t[4] ?>"</blockquote>
          <figcaption class="mt-5 flex items-center gap-3 pt-5 border-t border-slate-300/40">
            <span class="grid place-items-center w-10 h-10 rounded-full text-white font-bold text-sm shadow" style="background:linear-gradient(135deg,#<?= $t[3] ?>,#<?= $t[3] ?>cc)"><?= $t[2] ?></span>
            <div><p class="font-semibold text-ink text-sm"><?= $t[0] ?></p><p class="text-xs text-slate-400"><?= $t[1] ?></p></div>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section class="py-16 lg:py-20">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 grid lg:grid-cols-3 gap-10">
    <div class="reveal">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full chip-inset text-sm font-semibold text-brand-600"><?= icon('message','w-4 h-4') ?> FAQ</span>
      <h2 class="mt-4 font-display text-3xl font-bold text-ink tracking-tightest text-emboss">Questions, answered</h2>
      <p class="mt-3 text-slate-500">Can't find what you're looking for?</p>
      <a href="<?= url('contact.php') ?>" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700">Talk to our team <?= icon('arrow-right','w-4 h-4') ?></a>
    </div>
    <div class="lg:col-span-2 space-y-4">
      <?php
        $faqs = [
          ['Do I get a certificate after completing a course?','Yes. Every course ends with a verified digital certificate carrying a unique code that anyone can validate on our Verify page.'],
          ['Are the courses beginner friendly?','Absolutely. Most programs start from the fundamentals and progress to advanced, real-world projects.'],
          ['Can I pay the fee in installments?','Yes. Flexible, no-cost installment options are available — our team generates an official fee receipt for every payment.'],
          ['Is placement support included?','We provide resume building, mock interviews and access to 200+ hiring partners.'],
          ['Will I get recordings if I miss a live class?','Yes, every live session is recorded and available in your student dashboard for lifetime access.'],
        ];
        foreach ($faqs as $i => $q): ?>
        <div class="reveal tactile overflow-hidden" data-delay="<?= $i % 3 ?>">
          <button data-faq class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left font-semibold text-ink transition">
            <span><?= $q[0] ?></span>
            <span data-faq-icon class="grid place-items-center w-8 h-8 rounded-full chip-inset text-brand-600 transition-transform duration-300 shrink-0"><?= icon('plus','w-4 h-4') ?></span>
          </button>
          <div class="overflow-hidden transition-all duration-300" style="max-height:0">
            <p class="px-5 pb-5 text-slate-500 leading-relaxed"><?= $q[1] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
