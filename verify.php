<?php
require_once __DIR__ . '/includes/functions.php';
$page = 'verify';
$page_title = 'Verify Certificate';

$code = trim($_GET['code'] ?? '');
$cert = null;
$searched = false;

if ($code !== '') {
    $searched = true;
    $stmt = db()->prepare(
      "SELECT cert.*, s.name AS student_name, s.student_code, c.title AS course_title, c.duration
       FROM certificates cert
       JOIN students s ON s.id = cert.student_id
       JOIN courses  c ON c.id = cert.course_id
       WHERE cert.certificate_no = ? LIMIT 1"
    );
    $stmt->execute([$code]);
    $cert = $stmt->fetch();
}

include __DIR__ . '/includes/header.php';
?>

<section class="relative mx-3 sm:mx-6 mt-4 rounded-[28px] sm:rounded-[34px] obsidian text-white overflow-hidden">
  <div class="hero-aurora"><span></span><span></span><span></span></div>
  <div class="absolute inset-0 bg-grid"></div>
  <div class="relative max-w-3xl mx-auto px-5 sm:px-10 py-14 sm:py-16 text-center">
    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full obsidian-inset text-sm font-semibold text-brand-300"><?= icon('shield-check','w-4 h-4') ?> Trust &amp; Authenticity</span>
    <h1 class="mt-4 font-display text-[30px] sm:text-4xl lg:text-5xl font-bold tracking-tightest">Verify a Certificate</h1>
    <p class="mt-4 text-base text-slate-300">Enter the certificate number printed on the document to confirm its authenticity.</p>
    <form method="get" class="mt-8 flex flex-col sm:flex-row gap-3 max-w-xl mx-auto">
      <div class="relative flex-1">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 z-10"><?= icon('award','w-5 h-5') ?></span>
        <input name="code" value="<?= e($code) ?>" placeholder="e.g. NEX-CERT-2026-0001" required
               class="w-full pl-12 pr-4 py-3.5 rounded-full obsidian-inset text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-400">
      </div>
      <button class="btn-glass btn-shine px-7 py-3.5">Verify</button>
    </form>
  </div>
</section>

<section class="py-12 sm:py-16 min-h-[40vh]">
  <div class="max-w-3xl mx-auto px-4 sm:px-6">
    <?php if ($searched && $cert): ?>
      <div class="tactile-xl overflow-hidden">
        <div class="tile-emerald text-white px-5 sm:px-6 py-5 flex items-center gap-3">
          <span class="grid place-items-center w-12 h-12 rounded-full bg-white/20 shadow-inner shrink-0"><?= icon('check-circle','w-6 h-6') ?></span>
          <div><p class="font-bold text-lg">Certificate Verified</p><p class="text-sm text-emerald-50">Authentic certificate issued by <?= e(setting('site_name')) ?>.</p></div>
        </div>
        <div class="relative p-6 sm:p-8 bg-emboss-dots">
          <div class="grid sm:grid-cols-2 gap-5">
            <?php foreach ([
              ['Certificate No.', $cert['certificate_no']],
              ['Issue Date', fmt_date($cert['issue_date'])],
              ['Student Name', $cert['student_name']],
              ['Enrollment ID', $cert['student_code']],
              ['Course', $cert['course_title']],
              ['Grade', $cert['grade']],
            ] as $row): ?>
              <div class="tactile-inset p-4"><p class="text-xs text-slate-400 uppercase tracking-wide"><?= $row[0] ?></p><p class="font-semibold text-ink mt-0.5"><?= e($row[1]) ?></p></div>
            <?php endforeach; ?>
          </div>
          <div class="mt-6 flex items-center gap-3 text-sm text-slate-500">
            <span class="grid place-items-center w-12 h-12 rounded-full tile-emerald text-white shrink-0"><?= icon('shield-check','w-6 h-6') ?></span>
            <p>This document carries a unique verification code and is recognized as genuine.</p>
          </div>
        </div>
      </div>
    <?php elseif ($searched && !$cert): ?>
      <div class="tactile p-8 text-center">
        <span class="grid place-items-center w-14 h-14 rounded-full tile-rose text-white mx-auto"><?= icon('x-circle','w-7 h-7') ?></span>
        <h2 class="mt-4 font-display text-2xl font-bold text-ink text-emboss">No matching certificate</h2>
        <p class="mt-2 text-slate-500">We couldn't find a certificate with the number <span class="font-semibold text-slate-700">"<?= e($code) ?>"</span>. Please double-check and try again.</p>
      </div>
    <?php else: ?>
      <div class="tactile p-10 text-center">
        <span class="inline-grid place-items-center w-16 h-16 rounded-2xl chip-inset text-brand-600 mx-auto"><?= icon('lock','w-7 h-7') ?></span>
        <p class="mt-5 text-slate-500">Enter a certificate number above to verify its authenticity.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
