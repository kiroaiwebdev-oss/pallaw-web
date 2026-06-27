<?php
require_once __DIR__ . '/includes/functions.php';
$page = 'projects';
$page_title = 'Student Projects';

$projects = db()->query(
  "SELECT p.*, c.title AS course, s.name AS student
   FROM projects p
   LEFT JOIN courses c ON c.id = p.course_id
   LEFT JOIN students s ON s.id = p.student_id
   WHERE p.status='published' ORDER BY p.id DESC"
)->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="relative mx-3 sm:mx-6 mt-4 rounded-[28px] sm:rounded-[34px] obsidian text-white overflow-hidden">
  <div class="hero-aurora"><span></span><span></span><span></span></div>
  <div class="absolute inset-0 bg-grid"></div>
  <div class="relative max-w-7xl mx-auto px-5 sm:px-10 py-14 sm:py-16 text-center">
    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full obsidian-inset text-sm font-semibold text-brand-300"><?= icon('code','w-4 h-4') ?> Project Showcase</span>
    <h1 class="mt-4 font-display text-[30px] sm:text-4xl lg:text-5xl font-bold tracking-tightest">Real work by our students</h1>
    <p class="mt-4 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto">Every program ends with a portfolio-ready project. Here's what our learners have built.</p>
  </div>
</section>

<section class="py-12 sm:py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <?php if (!$projects): ?>
      <div class="text-center py-20 tactile">
        <span class="inline-grid place-items-center w-16 h-16 rounded-2xl chip-inset text-slate-400 mx-auto"><?= icon('layers','w-7 h-7') ?></span>
        <p class="mt-5 text-lg font-semibold text-ink">No projects published yet</p>
        <p class="text-slate-500">Check back soon to see student work.</p>
      </div>
    <?php else: ?>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
        <?php foreach ($projects as $i => $p): ?>
          <article class="reveal tactile tactile-lift overflow-hidden flex flex-col p-2.5" data-delay="<?= $i % 3 ?>">
            <div class="h-44 rounded-2xl obsidian grid place-items-center text-white/25 relative overflow-hidden">
              <div class="absolute inset-0 bg-dots opacity-40"></div>
              <span class="relative"><?= icon('code','w-14 h-14') ?></span>
            </div>
            <div class="p-4 pt-5 flex flex-col flex-1">
              <?php if ($p['course']): ?><span class="text-xs font-semibold text-brand-600 uppercase tracking-wide"><?= e($p['course']) ?></span><?php endif; ?>
              <h3 class="mt-2 font-semibold text-ink"><?= e($p['title']) ?></h3>
              <p class="mt-2 text-sm text-slate-500 line-clamp-3 flex-1"><?= e($p['description']) ?></p>
              <div class="mt-4 pt-4 border-t border-slate-300/40 flex items-center justify-between text-sm">
                <span class="text-slate-400 inline-flex items-center gap-1.5"><?= icon('users','w-4 h-4') ?> <?= $p['student'] ? e($p['student']) : 'Student Project' ?></span>
                <?php if ($p['project_url'] && $p['project_url'] !== '#'): ?>
                  <a href="<?= e($p['project_url']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 font-semibold text-brand-600 hover:text-brand-700">View <?= icon('arrow-up-right','w-4 h-4') ?></a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
