<?php
require_once __DIR__ . '/_layout.php';
$d = db();

// Ensure table exists (so the owner doesn't have to run SQL manually)
$d->exec("CREATE TABLE IF NOT EXISTS faculty (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(140) NOT NULL,
  role VARCHAR(140) NULL,
  expertise VARCHAR(200) NULL,
  bio TEXT NULL,
  photo VARCHAR(255) NULL,
  linkedin VARCHAR(255) NULL,
  sort_order INT DEFAULT 0,
  status ENUM('active','hidden') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Session expired.'); redirect('admin/faculty.php'); }
    $do = $_POST['do'] ?? '';
    if ($do === 'delete') {
        $d->prepare("DELETE FROM faculty WHERE id=?")->execute([(int)$_POST['id']]);
        flash('success','Faculty member deleted.');
        redirect('admin/faculty.php');
    }
    if ($do === 'save') {
        $id        = (int)($_POST['id'] ?? 0);
        $name      = trim($_POST['name'] ?? '');
        $role      = trim($_POST['role'] ?? '');
        $expertise = trim($_POST['expertise'] ?? '');
        $bio       = trim($_POST['bio'] ?? '');
        $photo     = trim($_POST['photo'] ?? '');
        $photo     = upload_image('photo_file', 'faculty', $photo);
        $linkedin  = trim($_POST['linkedin'] ?? '');
        $sort      = (int)($_POST['sort_order'] ?? 0);
        $status    = $_POST['status'] ?? 'active';
        if ($name === '') { flash('error','Name is required.'); redirect('admin/faculty.php?action=' . ($id?'edit&id='.$id:'new')); }
        if ($id) {
            $d->prepare("UPDATE faculty SET name=?,role=?,expertise=?,bio=?,photo=?,linkedin=?,sort_order=?,status=? WHERE id=?")
              ->execute([$name,$role,$expertise,$bio,$photo,$linkedin,$sort,$status,$id]);
            flash('success','Faculty updated.');
        } else {
            $d->prepare("INSERT INTO faculty (name,role,expertise,bio,photo,linkedin,sort_order,status) VALUES (?,?,?,?,?,?,?,?)")
              ->execute([$name,$role,$expertise,$bio,$photo,$linkedin,$sort,$status]);
            flash('success','Faculty added.');
        }
        redirect('admin/faculty.php');
    }
}

$action = $_GET['action'] ?? 'list';

if ($action === 'new' || $action === 'edit') {
    $f = ['id'=>0,'name'=>'','role'=>'','expertise'=>'','bio'=>'','photo'=>'','linkedin'=>'','sort_order'=>0,'status'=>'active'];
    if ($action==='edit') { $stmt=$d->prepare("SELECT * FROM faculty WHERE id=?"); $stmt->execute([(int)$_GET['id']]); $f=$stmt->fetch()?:$f; }
    admin_layout_top('faculty', $action==='edit'?'Edit Faculty':'Add Faculty');
    ?>
    <a href="<?= url('admin/faculty.php') ?>" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-900 mb-4">← Back to faculty</a>
    <form method="post" enctype="multipart/form-data" class="rounded-2xl bg-white border border-slate-100 p-6 sm:p-8 max-w-2xl space-y-5">
      <?= csrf_field() ?><input type="hidden" name="do" value="save"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
      <div class="grid sm:grid-cols-2 gap-4">
        <div><?= field_label('Full Name *') ?><input name="name" value="<?= e($f['name']) ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
        <div><?= field_label('Role / Title') ?><input name="role" value="<?= e($f['role']) ?>" placeholder="e.g. Senior CAD Faculty" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
      </div>
      <div><?= field_label('Expertise (comma separated)') ?><input name="expertise" value="<?= e($f['expertise']) ?>" placeholder="AutoCAD, SolidWorks" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
      <div><?= field_label('Short Bio') ?><textarea name="bio" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"><?= e($f['bio']) ?></textarea></div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <?= field_label('Photo') ?>
          <div class="flex items-center gap-3">
            <?php if (!empty($f['photo'])): ?><img src="<?= e(media($f['photo'])) ?>" alt="" class="w-14 h-14 rounded-full object-cover border border-slate-200 shrink-0"><?php endif; ?>
            <input type="file" name="photo_file" accept="image/png,image/jpeg,image/webp" class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
          </div>
          <input name="photo" value="<?= e($f['photo']) ?>" placeholder="…or paste an image URL" class="mt-2 w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
        </div>
        <div><?= field_label('LinkedIn URL (optional)') ?><input name="linkedin" value="<?= e($f['linkedin']) ?>" placeholder="https://linkedin.com/in/…" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
      </div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><?= field_label('Sort Order (lower = first)') ?><input type="number" name="sort_order" value="<?= (int)$f['sort_order'] ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
        <div><?= field_label('Status') ?><select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200"><?php foreach(['active','hidden'] as $st): ?><option <?= $f['status']===$st?'selected':'' ?>><?= $st ?></option><?php endforeach; ?></select></div>
      </div>
      <div class="flex gap-3"><button class="px-7 py-3 rounded-xl bg-gradient-to-r from-brand-500 to-violet-600 text-white font-semibold hover:opacity-90 transition">Save</button><a href="<?= url('admin/faculty.php') ?>" class="px-7 py-3 rounded-xl border border-slate-200 font-semibold text-slate-700">Cancel</a></div>
    </form>
    <?php admin_layout_bottom(); exit;
}

$rows = $d->query("SELECT * FROM faculty ORDER BY sort_order ASC, id ASC")->fetchAll();
admin_layout_top('faculty', 'Faculty');
?>
<div class="flex items-center justify-between mb-6"><p class="text-slate-500 text-sm"><?= count($rows) ?> faculty member<?= count($rows)!==1?'s':'' ?></p><a href="<?= url('admin/faculty.php?action=new') ?>" class="px-5 py-2.5 rounded-full bg-slate-900 text-white text-sm font-semibold hover:bg-brand-600 transition">+ Add Faculty</a></div>
<div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 text-left"><tr><th class="px-5 py-3 font-semibold">Name</th><th class="px-5 py-3 font-semibold">Role</th><th class="px-5 py-3 font-semibold">Expertise</th><th class="px-5 py-3 font-semibold">Order</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-5 py-4">
              <div class="flex items-center gap-3">
                <?php if ($r['photo']): ?><img src="<?= e(media($r['photo'])) ?>" alt="" class="w-9 h-9 rounded-full object-cover">
                <?php else: ?><span class="grid place-items-center w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-violet-600 text-white text-sm font-bold"><?= e(strtoupper(substr($r['name'],0,1))) ?></span><?php endif; ?>
                <span class="font-medium text-slate-900"><?= e($r['name']) ?></span>
              </div>
            </td>
            <td class="px-5 py-4 text-slate-500"><?= e($r['role'] ?: '—') ?></td>
            <td class="px-5 py-4 text-slate-500"><?= e($r['expertise'] ?: '—') ?></td>
            <td class="px-5 py-4 text-slate-500"><?= (int)$r['sort_order'] ?></td>
            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $r['status']==='active'?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500' ?>"><?= e($r['status']) ?></span></td>
            <td class="px-5 py-4 text-right whitespace-nowrap"><a href="<?= url('admin/faculty.php?action=edit&id=' . $r['id']) ?>" class="text-brand-600 font-semibold">Edit</a><form method="post" class="inline" onsubmit="return confirm('Delete this faculty member?')"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="ml-3 text-rose-600 font-semibold">Delete</button></form></td>
          </tr>
        <?php endforeach; ?>
        <?php if(!$rows): ?><tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No faculty yet. Add your first mentor.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php admin_layout_bottom(); ?>
