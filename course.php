<?php
require_once __DIR__ . '/includes/functions.php';
$page = 'courses';

$slug = trim($_GET['slug'] ?? '');
$stmt = db()->prepare(
  "SELECT c.*, cat.name AS category FROM courses c
   LEFT JOIN categories cat ON cat.id = c.category_id
   WHERE c.slug = ? AND c.status='active' LIMIT 1"
);
$stmt->execute([$slug]);
$c = $stmt->fetch();

if (!$c) {
    http_response_code(404);
    $page_title = 'Course not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="max-w-3xl mx-auto px-4 py-24 sm:py-28 text-center"><span class="inline-grid place-items-center w-16 h-16 rounded-2xl chip-inset text-slate-400 mx-auto">' . icon('search','w-7 h-7') . '</span><h1 class="mt-5 font-display text-3xl font-bold text-ink text-emboss">Course not found</h1><p class="mt-3 text-slate-500">The course you are looking for doesn\'t exist or was removed.</p><a href="' . url('courses.php') . '" class="btn3d inline-flex items-center gap-2 mt-6 px-6 py-3 text-white font-semibold">' . icon('arrow-right','w-4 h-4') . ' Back to courses</a></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $c['title'];
$price = $c['discount_price'] ?: $c['price'];
$hasDiscount = $c['discount_price'] && $c['discount_price'] < $c['price'];
$off = $hasDiscount ? round(100 - ($c['discount_price'] / $c['price'] * 100)) : 0;
$syllabus = array_filter(array_map('trim', explode('|', (string)$c['syllabus'])));

$related = db()->prepare("SELECT * FROM courses WHERE category_id = ? AND id <> ? AND status='active' LIMIT 3");
$related->execute([$c['category_id'], $c['id']]);
$related = $related->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="relative mx-3 sm:mx-6 mt-4 rounded-[28px] sm:rounded-[34px] obsidian text-white overflow-hidden">
  <div class="hero-aurora"><span></span><span></span><span></span></div>
  <div class="absolute inset-0 bg-grid"></div>
  <div class="relative max-w-7xl mx-auto px-5 sm:px-10 py-12 lg:py-20">
    <nav class="flex items-center gap-2 text-sm text-slate-400 mb-6 flex-wrap">
      <a href="<?= url('index.php') ?>" class="hover:text-white transition">Home</a> <?= icon('chevron-right','w-4 h-4 text-slate-600') ?>
      <a href="<?= url('courses.php') ?>" class="hover:text-white transition">Courses</a> <?= icon('chevron-right','w-4 h-4 text-slate-600') ?>
      <span class="text-white truncate max-w-[160px] sm:max-w-none"><?= e($c['title']) ?></span>
    </nav>
    <div class="max-w-3xl">
      <span class="inline-block px-3 py-1 rounded-full obsidian-inset text-xs font-medium"><?= e($c['category'] ?? 'Course') ?></span>
      <h1 class="mt-4 font-display text-[28px] sm:text-4xl lg:text-5xl font-bold leading-tight tracking-tightest"><?= e($c['title']) ?></h1>
      <p class="mt-4 text-base sm:text-lg text-slate-300 max-w-2xl"><?= e($c['short_desc']) ?></p>
      <div class="mt-6 flex flex-wrap gap-2.5 text-sm">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full obsidian-inset"><?= icon('clock','w-4 h-4') ?> <?= e($c['duration']) ?></span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full obsidian-inset"><?= icon('signal','w-4 h-4') ?> <?= e($c['level']) ?></span>
        <?php if ($c['software']): ?><span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full obsidian-inset"><?= icon('cpu','w-4 h-4') ?> <?= e($c['software']) ?></span><?php endif; ?>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300"><?= icon('award','w-4 h-4') ?> Verified Certificate</span>
      </div>
    </div>
  </div>
</section>

<section class="py-12 lg:py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
      <div class="reveal tactile p-6 sm:p-8">
        <h2 class="font-display text-2xl font-bold text-ink tracking-tightest text-emboss">About this course</h2>
        <p class="mt-4 text-slate-600 leading-relaxed"><?= nl2br(e($c['description'])) ?></p>
      </div>

      <?php if ($syllabus): ?>
      <div class="reveal tactile p-6 sm:p-8">
        <h2 class="font-display text-2xl font-bold text-ink tracking-tightest text-emboss">What you'll learn</h2>
        <div class="mt-5 grid sm:grid-cols-2 gap-3">
          <?php foreach ($syllabus as $i => $mod): ?>
            <div class="flex items-start gap-3 p-3.5 rounded-xl tactile-inset">
              <span class="grid place-items-center w-7 h-7 rounded-lg tile-accent text-white text-xs font-bold shrink-0"><?= $i + 1 ?></span>
              <span class="text-sm text-slate-700 font-medium"><?= e($mod) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="reveal tactile p-6 sm:p-8">
        <h2 class="font-display text-2xl font-bold text-ink tracking-tightest text-emboss">Why this program</h2>
        <ul class="mt-5 grid sm:grid-cols-2 gap-3">
          <?php foreach ([['target','Mentor-led live training with doubt support'],['rocket','Hands-on, portfolio-ready capstone project'],['shield-check','Industry-recognized verified certificate'],['briefcase','Placement assistance & interview prep']] as $b): ?>
            <li class="flex items-start gap-3 text-slate-600"><span class="grid place-items-center w-8 h-8 rounded-lg chip-inset text-emerald-600 shrink-0"><?= icon($b[0],'w-4 h-4') ?></span><span class="text-sm pt-1.5"><?= $b[1] ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- Sticky enroll ticket -->
    <aside class="lg:sticky lg:top-24 h-fit">
      <div class="relative tactile-xl p-6 overflow-hidden">
        <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-canvas shadow-inner"></div>
        <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-canvas shadow-inner"></div>
        <div class="flex items-end gap-3 flex-wrap">
          <p class="font-display text-4xl font-bold text-ink tracking-tightest text-emboss"><?= money($price) ?></p>
          <?php if ($hasDiscount): ?>
            <span class="text-slate-400 line-through pb-1"><?= money($c['price']) ?></span>
            <span class="ml-auto px-2.5 py-1 rounded-full tile-emerald text-white text-xs font-bold"><?= $off ?>% OFF</span>
          <?php endif; ?>
        </div>
        <p class="mt-1 text-sm text-slate-400">One-time fee · EMI available</p>

        <a href="<?= url('contact.php?course=' . urlencode($c['title'])) ?>" class="btn3d btn-shine mt-5 flex items-center justify-center gap-2 px-5 py-3.5 text-white font-semibold">Enroll Now <?= icon('arrow-right','w-4 h-4') ?></a>
        <a href="tel:<?= e(setting('phone')) ?>" class="btn3d-light mt-3 flex items-center justify-center gap-2 px-5 py-3.5 font-semibold text-slate-700"><?= icon('phone','w-4 h-4') ?> Talk to Counsellor</a>

        <div class="mt-6 border-t border-dashed border-slate-300/70 pt-5">
          <dl class="space-y-3.5 text-sm">
            <?php foreach ([['Duration',$c['duration'],'clock'],['Level',$c['level'],'signal'],['Category',$c['category'] ?? '-','layers'],['Certificate','Included','award']] as $row): ?>
              <div class="flex items-center justify-between">
                <dt class="inline-flex items-center gap-2 text-slate-500"><?= icon($row[2],'w-4 h-4 text-slate-400') ?> <?= $row[0] ?></dt>
                <dd class="font-semibold <?= $row[0]==='Certificate' ? 'text-emerald-600' : 'text-ink' ?>"><?= e($row[1]) ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </div>
      </div>
      <div class="mt-4 rounded-2xl obsidian text-white p-5 flex items-center gap-3">
        <span class="grid place-items-center w-11 h-11 rounded-xl obsidian-inset text-brand-300 shrink-0"><?= icon('headset','w-5 h-5') ?></span>
        <div><p class="text-sm font-semibold">Need guidance?</p><a href="tel:<?= e(setting('phone')) ?>" class="text-xs text-slate-300 hover:text-white"><?= e(setting('phone')) ?></a></div>
      </div>
    </aside>
  </div>
</section>

<?php if ($related): ?>
<section class="py-12 lg:py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <h2 class="font-display text-2xl font-bold text-ink tracking-tightest text-emboss mb-8">Related courses</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
      <?php foreach ($related as $r): $rp = $r['discount_price'] ?: $r['price']; ?>
        <a href="<?= url('course.php?slug=' . urlencode($r['slug'])) ?>" class="tactile tactile-lift block p-5">
          <h3 class="font-semibold text-ink"><?= e($r['title']) ?></h3>
          <p class="mt-2 text-sm text-slate-500 line-clamp-2"><?= e($r['short_desc']) ?></p>
          <div class="mt-4 flex items-center justify-between">
            <p class="font-display text-xl font-bold text-ink tracking-tightest text-emboss"><?= money($rp) ?></p>
            <span class="grid place-items-center w-9 h-9 rounded-full chip-inset text-brand-600"><?= icon('arrow-up-right','w-5 h-5') ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
