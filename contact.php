<?php
require_once __DIR__ . '/includes/functions.php';
$page = 'contact';
$page_title = 'Contact';

$prefillCourse = trim($_GET['course'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Session expired. Please try again.';
    } else {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '')  $errors[] = 'Please enter your name.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
        if ($message === '') $errors[] = 'Please enter a message.';

        if (!$errors) {
            $stmt = db()->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?,?,?,?,?)");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            flash('success', 'Thank you! Your message has been received. Our team will reach out shortly.');
            redirect('contact.php');
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="relative mx-3 sm:mx-6 mt-4 rounded-[28px] sm:rounded-[34px] obsidian text-white overflow-hidden">
  <div class="hero-aurora"><span></span><span></span><span></span></div>
  <div class="absolute inset-0 bg-grid"></div>
  <div class="relative max-w-7xl mx-auto px-5 sm:px-10 py-14 sm:py-16 text-center">
    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full obsidian-inset text-sm font-semibold text-brand-300"><?= icon('headset','w-4 h-4') ?> Get in touch</span>
    <h1 class="mt-4 font-display text-[30px] sm:text-4xl lg:text-5xl font-bold tracking-tightest">Let's talk about your goals</h1>
    <p class="mt-4 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto">Have a question about a course, fees or admissions? Send us a message or call directly.</p>
  </div>
</section>

<section class="py-12 sm:py-16">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 grid lg:grid-cols-5 gap-6 sm:gap-8">
    <div class="lg:col-span-2 space-y-4">
      <?php foreach ([
        ['phone','Call us', setting('phone'), 'tel:' . setting('phone')],
        ['mail','Email', setting('email'), 'mailto:' . setting('email')],
      ] as $info): ?>
        <a href="<?= e($info[3]) ?>" class="tactile tactile-lift flex items-center gap-4 p-5">
          <span class="grid place-items-center w-12 h-12 rounded-xl tile-accent text-white shrink-0"><?= icon($info[0],'w-5 h-5') ?></span>
          <div class="min-w-0"><p class="text-xs text-slate-400"><?= $info[1] ?></p><p class="font-semibold text-ink truncate"><?= e($info[2]) ?></p></div>
        </a>
      <?php endforeach; ?>
      <div class="tactile flex items-center gap-4 p-5">
        <span class="grid place-items-center w-12 h-12 rounded-xl tile-accent text-white shrink-0"><?= icon('map-pin','w-5 h-5') ?></span>
        <div><p class="text-xs text-slate-400">Visit us</p><p class="font-semibold text-ink text-sm"><?= e(setting('address')) ?></p></div>
      </div>
      <div class="tactile-inset overflow-hidden h-56 p-1.5">
        <iframe src="<?= e(setting('map_embed')) ?>" class="w-full h-full rounded-2xl" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map"></iframe>
      </div>
    </div>

    <div class="lg:col-span-3">
      <div class="tactile-xl p-5 sm:p-8">
        <?= render_flashes() ?>
        <?php if ($errors): ?>
          <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm space-y-1">
            <?php foreach ($errors as $err): ?><p class="flex items-center gap-2"><?= icon('x-circle','w-4 h-4') ?> <?= e($err) ?></p><?php endforeach; ?>
          </div>
        <?php endif; ?>
        <form method="post" class="space-y-4">
          <?= csrf_field() ?>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name *</label>
              <input name="name" value="<?= e($_POST['name'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl tactile-inset text-ink focus:outline-none focus:ring-2 focus:ring-brand-400 transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
              <input name="phone" value="<?= e($_POST['phone'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl tactile-inset text-ink focus:outline-none focus:ring-2 focus:ring-brand-400 transition">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email *</label>
            <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl tactile-inset text-ink focus:outline-none focus:ring-2 focus:ring-brand-400 transition">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Subject</label>
            <input name="subject" value="<?= e($_POST['subject'] ?? ($prefillCourse ? 'Enquiry: ' . $prefillCourse : '')) ?>" class="w-full px-4 py-3 rounded-xl tactile-inset text-ink focus:outline-none focus:ring-2 focus:ring-brand-400 transition">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Message *</label>
            <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-xl tactile-inset text-ink focus:outline-none focus:ring-2 focus:ring-brand-400 transition"><?= e($_POST['message'] ?? '') ?></textarea>
          </div>
          <button class="btn3d btn-shine inline-flex w-full sm:w-auto justify-center items-center gap-2 px-8 py-3.5 text-white font-semibold">Send Message <?= icon('arrow-right','w-4 h-4') ?></button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
