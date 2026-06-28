<?php
require_once __DIR__ . '/_layout.php';
$d = db();

// Ensure table exists
$d->exec("CREATE TABLE IF NOT EXISTS works (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  type VARCHAR(100) NULL,
  description TEXT NULL,
  link VARCHAR(255) NULL,
  image VARCHAR(255) NULL,
  sort_order INT DEFAULT 0,
  status ENUM('published','draft') DEFAULT 'published',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Session expired.'); redirect('admin/works.php'); }
    $do = $_POST['do'] ?? '';
    if ($do === 'delete') {
        $d->prepare("DELETE FROM works WHERE id=?")->execute([(int)$_POST['id']]);
        flash('success','Work item deleted.');
        redirect('admin/works.php');
    }
    if ($do === 'save') {
        $id    = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $type  = trim($_POST['type'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $link  = trim($_POST['link'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $sort  = (int)($_POST['sort_order'] ?? 0);
        $status= $_POST['status'] ?? 'published';
        if ($title === '') { flash('error','Title is required.'); redirect('admin/works.php?action=' . ($id?'edit&id='.$id:'new')); }
        if ($id) {
            $d->prepare("UPDATE works SET title=?,type=?,description=?,link=?,image=?,sort_order=?,status=? WHERE id=?")
              ->execute([$title,$type,$desc,$link,$image,$sort,$status,$id]);
            flash('success','Work updated.');
        } else {
            $d->prepare("INSERT INTO works (title,type,description,link,image,sort_order,status) VALUES (?,?,?,?,?,?,?)")
              ->execute([$title,$type,$desc,$link,$image,$sort,$status]);
            flash('success','Work added.');
        }
        redirect('admin/works.php');
    }
}

$action = $_GET['action'] ?? 'list';

if ($action === 'new' || $action === 'edit') {
    $w = ['id'=>0,'title'=>'','type'=>'','description'=>'','link'=>'','image'=>'','sort_order'=>0,'status'=>'published'];
    if ($action==='edit') { $stmt=$d->prepare("SELECT * FROM works WHERE id=?"); $stmt->execute([(int)$_GET['id']]); $w=$stmt->fetch()?:$w; }
    admin_layout_top('works', $action==='edit'?'Edit Work':'Add Work');
    ?>
    <a href="<?= url('admin/works.php') ?>" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-900 mb-4">← Back to work</a>
    <form method="post" class="rounded-2xl bg-white border border-slate-100 p-6 sm:p-8 max-w-2xl space-y-5">
      <?= csrf_field() ?><input type="hidden" name="do" value="save"><input type="hidden" name="id" value="<?= (int)$w['id'] ?>">
      <div class="grid sm:grid-cols-2 gap-4">
        <div><?= field_label('Project Title *') ?><input name="title" value="<?= e($w['title']) ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
        <div><?= field_label('Type / Category') ?><input name="type" value="<?= e($w['type']) ?>" placeholder="e.g. Web Application" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
      </div>
      <div><?= field_label('Description') ?><textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"><?= e($w['description']) ?></textarea></div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><?= field_label('Live / Project Link (optional)') ?><input name="link" value="<?= e($w['link']) ?>" placeholder="https://…" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
        <div><?= field_label('Image URL (optional)') ?><input name="image" value="<?= e($w['image']) ?>" placeholder="https://…/cover.jpg" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
      </div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><?= field_label('Sort Order (lower = first)') ?><input type="number" name="sort_order" value="<?= (int)$w['sort_order'] ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
        <div><?= field_label('Status') ?><select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200"><?php foreach(['published','draft'] as $st): ?><option <?= $w['status']===$st?'selected':'' ?>><?= $st ?></option><?php endforeach; ?></select></div>
      </div>
      <div class="flex gap-3"><button class="px-7 py-3 rounded-xl bg-gradient-to-r from-brand-500 to-violet-600 text-white font-semibold hover:opacity-90 transition">Save</button><a href="<?= url('admin/works.php') ?>" class="px-7 py-3 rounded-xl border border-slate-200 font-semibold text-slate-700">Cancel</a></div>
    </form>
    <?php admin_layout_bottom(); exit;
}

$rows = $d->query("SELECT * FROM works ORDER BY sort_order ASC, id DESC")->fetchAll();
admin_layout_top('works', 'Our Work');
?>
<div class="flex items-center justify-between mb-6"><p class="text-slate-500 text-sm"><?= count($rows) ?> work item<?= count($rows)!==1?'s':'' ?></p><a href="<?= url('admin/works.php?action=new') ?>" class="px-5 py-2.5 rounded-full bg-slate-900 text-white text-sm font-semibold hover:bg-brand-600 transition">+ Add Work</a></div>
<div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 text-left"><tr><th class="px-5 py-3 font-semibold">Title</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold">Link</th><th class="px-5 py-3 font-semibold">Order</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-5 py-4 font-medium text-slate-900"><?= e($r['title']) ?></td>
            <td class="px-5 py-4 text-slate-500"><?= e($r['type'] ?: '—') ?></td>
            <td class="px-5 py-4 text-slate-500"><?php if($r['link'] && $r['link']!=='#'): ?><a href="<?= e($r['link']) ?>" target="_blank" rel="noopener" class="text-brand-600">Visit ↗</a><?php else: ?>—<?php endif; ?></td>
            <td class="px-5 py-4 text-slate-500"><?= (int)$r['sort_order'] ?></td>
            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $r['status']==='published'?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500' ?>"><?= e($r['status']) ?></span></td>
            <td class="px-5 py-4 text-right whitespace-nowrap"><a href="<?= url('admin/works.php?action=edit&id=' . $r['id']) ?>" class="text-brand-600 font-semibold">Edit</a><form method="post" class="inline" onsubmit="return confirm('Delete this work item?')"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="ml-3 text-rose-600 font-semibold">Delete</button></form></td>
          </tr>
        <?php endforeach; ?>
        <?php if(!$rows): ?><tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No work added yet. Showcase your projects here.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php admin_layout_bottom(); ?>
