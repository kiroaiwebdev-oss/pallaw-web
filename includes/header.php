<?php
require_once __DIR__ . '/functions.php';
$page        = $page        ?? '';
$page_title  = $page_title  ?? '';
$site        = setting('site_name', 'Nexora Institute');
$title       = $page_title ? "$page_title · $site" : "$site · " . setting('tagline', 'Industry-Ready Skills');
$logo        = media(setting('logo'));
$me          = student_logged_in() ? current_student() : null;
$myPhoto     = $me ? media($me['photo'] ?? '') : '';

$navLinks = [
  'home'     => ['Home',    'index.php'],
  'courses'  => ['Courses', 'courses.php'],
  'projects' => ['Projects','projects.php'],
  'verify'   => ['Verify',  'verify.php'],
  'about'    => ['About',   'about.php'],
  'contact'  => ['Contact', 'contact.php'],
];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= e(setting('tagline')) ?>">
<meta name="theme-color" content="#e6eaf3">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e(setting('tagline')) ?>">
<meta property="og:type" content="website">
<title><?= e($title) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['Space Grotesk', 'Inter', 'sans-serif'],
      },
      colors: {
        brand: {
          50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',
          500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81',950:'#1e1b4b',
        },
        ink: '#0b1020',
        canvas: '#e6eaf3',
        surface: '#eef1f8',
      },
      boxShadow: {
        'glow': '0 0 0 1px rgba(99,102,241,.1), 0 20px 60px -20px rgba(79,70,229,.45)',
        'card': '0 1px 2px rgba(16,24,40,.04), 0 8px 24px -12px rgba(16,24,40,.12)',
        'raised': '10px 12px 26px rgba(43,54,92,.20), -8px -8px 20px rgba(255,255,255,.92)',
        'inset-soft': 'inset 7px 7px 14px rgba(43,54,92,.18), inset -6px -6px 12px rgba(255,255,255,.9)',
      },
      letterSpacing: { tightest: '-.04em' },
    },
  },
};
</script>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="font-sans bg-canvas text-slate-700 antialiased selection:bg-brand-100 grain">

<?php if (setting('splash_enabled', '1') === '1'): ?>
<!-- Splash / flash screen (shown once per session, toggle in Admin → Settings) -->
<div id="splash" data-splash>
  <div class="splash-inner">
    <?php if ($logo): ?>
      <span class="splash-logo" style="background:#fff"><img src="<?= e($logo) ?>" alt="<?= e($site) ?>" style="width:100%;height:100%;object-fit:contain;padding:14px"></span>
    <?php else: ?>
      <span class="splash-logo">N</span>
    <?php endif; ?>
    <p class="splash-name"><?= e($site) ?></p>
    <p class="splash-text"><?= e(setting('splash_text', setting('tagline'))) ?></p>
    <div class="splash-bar"><span></span></div>
  </div>
</div>
<script>document.documentElement.classList.add('splash-active');document.body.classList.add('splash-lock');</script>
<?php endif; ?>

<!-- Announcement bar -->
<div class="obsidian text-white relative z-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2.5 flex items-center justify-center gap-2 text-center text-[13px]">
    <span class="hidden sm:inline-flex items-center gap-1.5 font-medium"><?= icon('sparkles','w-3.5 h-3.5 text-brand-300') ?> Admissions open for Batch 2026 — limited seats.</span>
    <span class="text-slate-500 hidden sm:inline">·</span>
    <span class="text-slate-300">Talk to a counsellor</span>
    <a href="tel:<?= e(setting('phone')) ?>" class="font-semibold text-brand-300 hover:text-white transition inline-flex items-center gap-1"><?= icon('phone','w-3.5 h-3.5') ?><?= e(setting('phone')) ?></a>
  </div>
</div>

<!-- Navbar (floating tactile bar) -->
<header id="navbar" class="sticky top-0 z-50 transition-all duration-300">
  <div class="max-w-7xl mx-auto px-3 sm:px-6 pt-3">
    <nav id="navbar-bar" class="tactile rounded-full px-3 sm:px-4 transition-all duration-300">
      <div class="flex items-center justify-between h-14 lg:h-16">
        <a href="<?= url('index.php') ?>" class="flex items-center gap-2 sm:gap-2.5 group shrink-0 min-w-0 pl-1">
          <?php if ($logo): ?>
            <span class="grid place-items-center w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-white chip-raised overflow-hidden shrink-0 group-active:translate-y-0.5 transition-transform"><img src="<?= e($logo) ?>" alt="<?= e($site) ?>" class="w-full h-full object-contain p-1"></span>
          <?php else: ?>
            <span class="grid place-items-center w-9 h-9 sm:w-10 sm:h-10 rounded-2xl tile-accent text-white font-display font-bold text-base sm:text-lg group-active:translate-y-0.5 transition-transform shrink-0">N</span>
          <?php endif; ?>
          <span class="font-display font-bold text-base sm:text-xl tracking-tightest text-ink text-emboss truncate"><?= e($site) ?></span>
        </a>

        <div class="hidden lg:flex items-center gap-1 text-[15px] font-medium">
          <?php foreach ($navLinks as $key => $l): $active = $key === $page; ?>
            <a href="<?= url($l[1]) ?>" class="relative px-4 py-2 rounded-full transition <?= $active ? 'text-ink chip-inset' : 'text-slate-500 hover:text-ink' ?>">
              <?= e($l[0]) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <div class="hidden lg:flex items-center gap-2.5">
          <?php if ($me): ?>
            <a href="<?= url('student/dashboard.php') ?>" class="btn3d-light inline-flex items-center gap-2 text-[15px] pl-1.5 pr-4 py-1.5">
              <?php if ($myPhoto): ?>
                <img src="<?= e($myPhoto) ?>" alt="" class="w-8 h-8 rounded-full object-cover">
              <?php else: ?>
                <span class="grid place-items-center w-8 h-8 rounded-full tile-accent text-white text-xs font-bold"><?= e(strtoupper(substr($me['name'],0,1))) ?></span>
              <?php endif; ?>
              Dashboard
            </a>
          <?php else: ?>
            <a href="<?= url('student/login.php') ?>" class="btn3d-light inline-flex items-center text-[15px] px-5 py-2.5">Student Login</a>
          <?php endif; ?>
          <a href="<?= url('courses.php') ?>" class="btn3d btn-shine group inline-flex items-center gap-1.5 text-[15px] text-white px-5 py-2.5">
            Enroll Now <?= icon('arrow-right','w-4 h-4 group-hover:translate-x-0.5 transition-transform') ?>
          </a>
        </div>

        <button id="menuBtn" class="lg:hidden grid place-items-center w-11 h-11 rounded-full chip-raised text-ink active:translate-y-0.5 transition" aria-label="Open menu">
          <?= icon('menu','w-6 h-6') ?>
        </button>
      </div>
    </nav>
  </div>

  <!-- Mobile menu -->
  <div id="mobileMenu" class="lg:hidden fixed inset-0 z-50 hidden">
    <div id="mmOverlay" class="absolute inset-0 bg-ink/50 backdrop-blur-sm"></div>
    <div id="mmPanel" class="absolute top-0 right-0 h-full w-[84%] max-w-sm bg-canvas grain shadow-2xl translate-x-full transition-transform duration-300 flex flex-col">
      <div class="flex items-center justify-between px-5 h-16">
        <span class="font-display font-bold text-lg text-ink text-emboss"><?= e($site) ?></span>
        <button id="menuClose" class="grid place-items-center w-11 h-11 rounded-full chip-raised text-ink active:translate-y-0.5" aria-label="Close menu"><?= icon('x','w-6 h-6') ?></button>
      </div>
      <div class="flex-1 overflow-y-auto px-4 py-4 space-y-2.5 text-[15px] font-medium">
        <?php foreach ($navLinks as $key => $l): ?>
          <a href="<?= url($l[1]) ?>" class="flex items-center justify-between px-5 py-3.5 rounded-2xl <?= $key===$page ? 'tactile-inset text-brand-700 font-semibold' : 'tactile text-slate-600' ?>">
            <?= e($l[0]) ?> <?= icon('chevron-right','w-4 h-4 text-slate-400') ?>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="p-4 space-y-3">
        <?php if ($me): ?>
          <a href="<?= url('student/dashboard.php') ?>" class="btn3d-light flex items-center justify-center gap-2 px-4 py-3.5 font-semibold text-slate-700">
            <?php if ($myPhoto): ?><img src="<?= e($myPhoto) ?>" alt="" class="w-7 h-7 rounded-full object-cover">
            <?php else: ?><span class="grid place-items-center w-7 h-7 rounded-full tile-accent text-white text-xs font-bold"><?= e(strtoupper(substr($me['name'],0,1))) ?></span><?php endif; ?>
            My Dashboard
          </a>
        <?php else: ?>
          <a href="<?= url('student/login.php') ?>" class="btn3d-light block text-center px-4 py-3.5 font-semibold text-slate-700">Student Login</a>
        <?php endif; ?>
        <a href="<?= url('courses.php') ?>" class="btn3d block text-center px-4 py-3.5 text-white font-semibold">Enroll Now</a>
        <a href="tel:<?= e(setting('phone')) ?>" class="flex items-center justify-center gap-2 px-4 py-3 text-sm text-slate-500"><?= icon('phone','w-4 h-4') ?><?= e(setting('phone')) ?></a>
      </div>
    </div>
  </div>
</header>

<main>
