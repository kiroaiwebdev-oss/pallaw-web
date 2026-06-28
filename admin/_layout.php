<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$admin = current_admin();

function admin_layout_top(string $active, string $title): void
{
    global $admin;
    $groups = [
        'Overview' => [
            'dashboard' => ['Dashboard', '🏠', 'index.php'],
        ],
        'Academics' => [
            'courses'      => ['Courses',      '📚', 'courses.php'],
            'categories'   => ['Categories',   '🏷️', 'categories.php'],
            'projects'     => ['Projects',     '🛠️', 'projects.php'],
        ],
        'Showcase' => [
            'faculty'      => ['Faculty',      '👩‍🏫', 'faculty.php'],
            'works'        => ['Our Work',     '💼', 'works.php'],
        ],
        'Students' => [
            'students'     => ['Students',     '👨‍🎓', 'students.php'],
            'enrollments'  => ['Enrollments',  '📝', 'enrollments.php'],
            'payments'     => ['Fee Receipts', '🧾', 'payments.php'],
            'certificates' => ['Certificates', '📜', 'certificates.php'],
        ],
        'Site' => [
            'contacts'     => ['Enquiries',    '✉️', 'contacts.php'],
            'settings'     => ['Settings',     '⚙️', 'settings.php'],
        ],
    ];
    ?>
<!DOCTYPE html><html lang="en" class="scroll-smooth"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> · Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif'],display:['Space Grotesk','Inter','sans-serif']},colors:{brand:{50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'}}}}};</script>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head><body class="font-sans bg-slate-100 text-slate-800 antialiased">
<div class="min-h-screen lg:flex">
  <aside id="sidebar" class="fixed lg:sticky inset-y-0 lg:top-0 left-0 z-40 w-72 lg:h-screen bg-slate-950 text-slate-300 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
    <div class="p-6 flex items-center gap-3 border-b border-white/10 shrink-0">
      <?php $alogo = media(setting('logo')); if ($alogo): ?>
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-white overflow-hidden shrink-0"><img src="<?= e($alogo) ?>" alt="" class="w-full h-full object-contain p-1"></span>
      <?php else: ?>
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-violet-600 text-white font-display font-bold shrink-0">N</span>
      <?php endif; ?>
      <div class="min-w-0"><p class="font-display font-bold text-white leading-tight truncate"><?= e(setting('site_name')) ?></p><p class="text-xs text-brand-400">Admin Console</p></div>
    </div>
    <nav class="flex-1 p-4 space-y-5 overflow-y-auto sb-scroll">
      <?php foreach ($groups as $gname => $items): ?>
        <div>
          <p class="px-4 text-[11px] uppercase tracking-wider text-slate-500 font-semibold mb-1"><?= $gname ?></p>
          <?php foreach ($items as $key => $item): $is = $key === $active; ?>
            <a href="<?= url('admin/' . $item[2]) ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition <?= $is ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30' : 'hover:bg-white/5 hover:text-white' ?>">
              <span class="text-base"><?= $item[1] ?></span><?= $item[0] ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </nav>
    <div class="p-4 border-t border-white/10 shrink-0">
      <a href="<?= url('index.php') ?>" target="_blank" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm hover:bg-white/5 hover:text-white transition">🌐 View Website</a>
      <a href="<?= url('admin/logout.php') ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-rose-300 hover:bg-rose-500/10 transition">⎋ Logout</a>
    </div>
  </aside>
  <div id="overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

  <div class="flex-1 min-w-0 flex flex-col">
    <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="px-4 sm:px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button id="sbToggle" class="lg:hidden grid place-items-center w-10 h-10 rounded-lg hover:bg-slate-100"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
          <h1 class="font-display font-bold text-lg text-slate-900"><?= e($title) ?></h1>
        </div>
        <div class="flex items-center gap-3">
          <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-900"><?= e($admin['name']) ?></p><p class="text-xs text-slate-400 capitalize"><?= e($admin['role']) ?></p></div>
          <span class="grid place-items-center w-10 h-10 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 text-white font-bold"><?= e(strtoupper(substr($admin['name'],0,1))) ?></span>
        </div>
      </div>
    </header>
    <main class="flex-1 p-4 sm:p-6 lg:p-8">
      <?= render_flashes() ?>
    <?php
}

function admin_layout_bottom(): void
{
    ?>
    </main>
  </div>
</div>
<script>
  const sb=document.getElementById('sidebar'),ov=document.getElementById('overlay'),tg=document.getElementById('sbToggle');
  tg&&tg.addEventListener('click',()=>{sb.classList.remove('-translate-x-full');ov.classList.remove('hidden');});
  ov&&ov.addEventListener('click',()=>{sb.classList.add('-translate-x-full');ov.classList.add('hidden');});
</script>
</body></html>
    <?php
}

/** Small helper to build a modal-less form section card */
function field_label(string $t): string { return '<label class="block text-sm font-medium text-slate-700 mb-1.5">' . e($t) . '</label>'; }
