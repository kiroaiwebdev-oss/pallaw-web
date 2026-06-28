<?php
require_once __DIR__ . '/_layout.php';
$sid = $student['id'];
$locked = (int)$student['details_locked'] === 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        flash('error', 'Session expired. Please try again.');
        redirect('student/profile.php');
    }

    // Profile photo upload is allowed even when personal details are locked
    if (($_POST['action'] ?? '') === 'photo') {
        $newPhoto = upload_image('photo', 'students', $student['photo'] ?? null);
        if ($newPhoto !== ($student['photo'] ?? null)) {
            db()->prepare("UPDATE students SET photo=? WHERE id=?")->execute([$newPhoto, $sid]);
            flash('success', 'Profile photo updated.');
        }
        redirect('student/profile.php');
    }

    if ($locked) {
        flash('warning', 'Your details are locked because a certificate has been generated. Please contact admin to make changes.');
        redirect('student/profile.php');
    }

    $action = $_POST['action'] ?? 'details';

    if ($action === 'password') {
        $cur = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        if (!password_verify($cur, $student['password_hash'])) {
            flash('error', 'Current password is incorrect.');
        } elseif (strlen($new) < 6) {
            flash('error', 'New password must be at least 6 characters.');
        } else {
            $stmt = db()->prepare("UPDATE students SET password_hash=? WHERE id=?");
            $stmt->execute([password_hash($new, PASSWORD_BCRYPT), $sid]);
            flash('success', 'Password updated successfully.');
        }
    } else {
        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $dob     = $_POST['dob'] ?: null;
        $gender  = $_POST['gender'] ?: null;
        $address = trim($_POST['address'] ?? '');
        if ($name === '') {
            flash('error', 'Name cannot be empty.');
        } else {
            $stmt = db()->prepare("UPDATE students SET name=?, phone=?, dob=?, gender=?, address=? WHERE id=?");
            $stmt->execute([$name, $phone, $dob, $gender, $address, $sid]);
            flash('success', 'Personal details updated successfully.');
        }
    }
    redirect('student/profile.php');
}

student_layout_top('profile', 'Personal Details');
?>

<?php if ($locked): ?>
  <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3 text-sm flex items-center gap-2">
    🔒 Your personal details are <strong>locked</strong> because a certificate has been generated. Contact admin (<a href="tel:<?= e(setting('phone')) ?>" class="underline font-semibold"><?= e(setting('phone')) ?></a>) for any corrections.
  </div>
<?php endif; ?>

<div class="grid lg:grid-cols-3 gap-6">
  <!-- Profile card -->
  <div class="rounded-2xl bg-white border border-slate-100 p-6 text-center h-fit">
    <?php $pp = media($student['photo'] ?? ''); ?>
    <?php if ($pp): ?>
      <img src="<?= e($pp) ?>" alt="<?= e($student['name']) ?>" class="w-24 h-24 rounded-full object-cover mx-auto ring-2 ring-brand-200">
    <?php else: ?>
      <span class="grid place-items-center w-24 h-24 rounded-full bg-gradient-to-br from-brand-500 to-violet-600 text-white text-3xl font-bold mx-auto"><?= e(strtoupper(substr($student['name'],0,1))) ?></span>
    <?php endif; ?>
    <h3 class="mt-4 font-display text-xl font-bold text-slate-900"><?= e($student['name']) ?></h3>
    <p class="text-sm text-slate-400"><?= e($student['email']) ?></p>
    <span class="inline-block mt-3 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-mono"><?= e($student['student_code']) ?></span>

    <form method="post" enctype="multipart/form-data" class="mt-6 pt-5 border-t border-slate-100 text-left">
      <?= csrf_field() ?><input type="hidden" name="action" value="photo">
      <label class="block text-xs font-semibold text-slate-500 mb-2">Update profile photo</label>
      <input type="file" name="photo" accept="image/png,image/jpeg,image/webp" required
             class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
      <button class="mt-3 w-full px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-brand-600 transition">Upload Photo</button>
      <p class="mt-2 text-[11px] text-slate-400">JPG, PNG or WEBP · max 3 MB.</p>
    </form>
  </div>

  <!-- Details form -->
  <div class="lg:col-span-2 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-100 p-6">
      <h3 class="font-display text-lg font-bold text-slate-900 mb-4">Edit Details</h3>
      <form method="post" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="details">
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
            <input name="name" value="<?= e($student['name']) ?>" <?= $locked?'disabled':'' ?> class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400 disabled:bg-slate-50 disabled:text-slate-400">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email (read only)</label>
            <input value="<?= e($student['email']) ?>" disabled class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-400">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
            <input name="phone" value="<?= e($student['phone']) ?>" <?= $locked?'disabled':'' ?> class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400 disabled:bg-slate-50 disabled:text-slate-400">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth</label>
            <input type="date" name="dob" value="<?= e($student['dob']) ?>" <?= $locked?'disabled':'' ?> class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400 disabled:bg-slate-50 disabled:text-slate-400">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender</label>
            <select name="gender" <?= $locked?'disabled':'' ?> class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400 disabled:bg-slate-50 disabled:text-slate-400">
              <?php foreach (['','Male','Female','Other'] as $g): ?>
                <option value="<?= $g ?>" <?= $student['gender']===$g?'selected':'' ?>><?= $g ?: 'Select…' ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
            <input name="address" value="<?= e($student['address']) ?>" <?= $locked?'disabled':'' ?> class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400 disabled:bg-slate-50 disabled:text-slate-400">
          </div>
        </div>
        <?php if (!$locked): ?>
          <button class="px-7 py-3 rounded-xl bg-gradient-to-r from-brand-500 to-violet-600 text-white font-semibold hover:opacity-90 transition">Save Changes</button>
        <?php endif; ?>
      </form>
    </div>

    <!-- Change password -->
    <div class="rounded-2xl bg-white border border-slate-100 p-6">
      <h3 class="font-display text-lg font-bold text-slate-900 mb-4">Change Password</h3>
      <form method="post" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="password">
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
            <input type="password" name="current_password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
            <input type="password" name="new_password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
          </div>
        </div>
        <button class="px-7 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-brand-600 transition">Update Password</button>
      </form>
    </div>
  </div>
</div>

<?php student_layout_bottom(); ?>
