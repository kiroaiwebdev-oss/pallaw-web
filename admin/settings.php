<?php
require_once __DIR__ . '/_layout.php';
$d = db();

// Fields grouped for the form
$fields = [
  'Brand' => [
    'site_name' => ['Institute Name', 'text'],
    'tagline'   => ['Tagline', 'text'],
    'about_short' => ['Short About', 'textarea'],
  ],
  'Contact' => [
    'phone'    => ['Phone', 'text'],
    'whatsapp' => ['WhatsApp Number (digits with country code)', 'text'],
    'email'    => ['Email', 'text'],
    'address'  => ['Address', 'text'],
    'map_embed'=> ['Google Maps Embed URL', 'text'],
  ],
  'Homepage Stats' => [
    'hero_stat_students' => ['Students Stat', 'text'],
    'hero_stat_courses'  => ['Courses Stat', 'text'],
    'hero_stat_partners' => ['Partners Stat', 'text'],
    'hero_stat_rating'   => ['Rating Stat', 'text'],
  ],
  'Social Links' => [
    'facebook'  => ['Facebook URL', 'text'],
    'instagram' => ['Instagram URL', 'text'],
    'linkedin'  => ['LinkedIn URL', 'text'],
    'youtube'   => ['YouTube URL', 'text'],
  ],
  'Institute Profile' => [
    'director_name' => ['Director / Founder Name', 'text'],
    'director_role' => ['Director Role', 'text'],
    'established'   => ['Established Year', 'text'],
    'institute_highlights' => ['Highlights (separate with | )', 'textarea'],
  ],
  'Splash / Flash Screen' => [
    'splash_enabled' => ['Show Splash Screen on load', 'select', ['1' => 'Enabled', '0' => 'Disabled']],
    'splash_text'    => ['Splash Subtitle', 'textarea'],
  ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Session expired.'); redirect('admin/settings.php'); }

    if (($_POST['do'] ?? '') === 'password') {
        $cur = $_POST['current_password'] ?? ''; $new = $_POST['new_password'] ?? '';
        $admin = current_admin();
        if (!password_verify($cur, $admin['password_hash'])) flash('error','Current password incorrect.');
        elseif (strlen($new) < 6) flash('error','New password must be 6+ chars.');
        else { $d->prepare("UPDATE admins SET password_hash=? WHERE id=?")->execute([password_hash($new,PASSWORD_BCRYPT),$admin['id']]); flash('success','Admin password updated.'); }
        redirect('admin/settings.php');
    }

    $upd = $d->prepare("INSERT INTO settings (skey,svalue) VALUES (?,?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)");
    foreach ($fields as $group) {
        foreach ($group as $key => $meta) {
            if (array_key_exists($key, $_POST)) {
                $upd->execute([$key, trim($_POST[$key])]);
            }
        }
    }

    // Logo: upload a new one, or remove the current one
    $currentLogo = setting('logo');
    $base = realpath(__DIR__ . '/..');
    if (!empty($_POST['remove_logo'])) {
        if ($currentLogo && strncmp($currentLogo, 'assets/uploads/site/', 20) === 0) @unlink($base . '/' . $currentLogo);
        $upd->execute(['logo', '']);
    } else {
        $newLogo = upload_image('logo', 'site', $currentLogo);
        if ($newLogo !== $currentLogo) $upd->execute(['logo', $newLogo ?? '']);
    }

    flash('success','Settings saved.');
    redirect('admin/settings.php');
}

admin_layout_top('settings', 'Settings');
?>
<div class="grid lg:grid-cols-3 gap-6">
  <form method="post" enctype="multipart/form-data" class="lg:col-span-2 space-y-6">
    <?= csrf_field() ?>
    <!-- Logo -->
    <div class="rounded-2xl bg-white border border-slate-100 p-6">
      <h3 class="font-display text-lg font-bold text-slate-900 mb-4">Logo</h3>
      <div class="flex items-center gap-5 flex-wrap">
        <div class="grid place-items-center w-20 h-20 rounded-2xl bg-slate-900 overflow-hidden shrink-0">
          <?php if (setting('logo')): ?>
            <img src="<?= e(media(setting('logo'))) ?>" class="w-full h-full object-contain p-2" alt="Current logo">
          <?php else: ?>
            <span class="text-white font-display font-bold text-2xl"><?= e(strtoupper(substr(setting('site_name','N'),0,1))) ?></span>
          <?php endif; ?>
        </div>
        <div class="flex-1 min-w-[220px]">
          <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml,image/gif"
                 class="block w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
          <p class="text-xs text-slate-400 mt-2">PNG or SVG recommended · max 3&nbsp;MB · used in header, footer, splash &amp; admin.</p>
          <?php if (setting('logo')): ?>
            <label class="inline-flex items-center gap-2 mt-3 text-sm text-rose-600 font-medium"><input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300"> Remove current logo</label>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php foreach ($fields as $group => $items): ?>
      <div class="rounded-2xl bg-white border border-slate-100 p-6">
        <h3 class="font-display text-lg font-bold text-slate-900 mb-4"><?= e($group) ?></h3>
        <div class="grid sm:grid-cols-2 gap-4">
          <?php foreach ($items as $key => $meta): $label=$meta[0]; $type=$meta[1]; $opts=$meta[2]??[]; $val=setting($key); $full=$type==='textarea'?'sm:col-span-2':''; ?>
            <div class="<?= $full ?>">
              <?= field_label($label) ?>
              <?php if ($type==='textarea'): ?>
                <textarea name="<?= e($key) ?>" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"><?= e($val) ?></textarea>
              <?php elseif ($type==='select'): ?>
                <select name="<?= e($key) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
                  <?php foreach ($opts as $ov => $ol): ?><option value="<?= e($ov) ?>" <?= (string)$val===(string)$ov?'selected':'' ?>><?= e($ol) ?></option><?php endforeach; ?>
                </select>
              <?php else: ?>
                <input name="<?= e($key) ?>" value="<?= e($val) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <button class="px-7 py-3 rounded-xl bg-gradient-to-r from-brand-500 to-violet-600 text-white font-semibold hover:opacity-90 transition">Save Settings</button>
  </form>

  <!-- Admin password -->
  <div class="h-fit rounded-2xl bg-white border border-slate-100 p-6">
    <h3 class="font-display text-lg font-bold text-slate-900 mb-4">Change Admin Password</h3>
    <form method="post" class="space-y-4">
      <?= csrf_field() ?><input type="hidden" name="do" value="password">
      <div><?= field_label('Current Password') ?><input type="password" name="current_password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
      <div><?= field_label('New Password') ?><input type="password" name="new_password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
      <button class="w-full px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-brand-600 transition">Update Password</button>
    </form>
  </div>
</div>
<?php admin_layout_bottom(); ?>
