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
    flash('success','Settings saved.');
    redirect('admin/settings.php');
}

admin_layout_top('settings', 'Settings');
?>
<div class="grid lg:grid-cols-3 gap-6">
  <form method="post" class="lg:col-span-2 space-y-6">
    <?= csrf_field() ?>
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
