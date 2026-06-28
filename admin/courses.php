<?php
require_once __DIR__ . '/_layout.php';
$d = db();

/* ---------- Handle POST (create / update / delete) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error', 'Session expired.'); redirect('admin/courses.php'); }
    $do = $_POST['do'] ?? '';

    if ($do === 'delete') {
        $stmt = $d->prepare("DELETE FROM courses WHERE id=?");
        $stmt->execute([(int)$_POST['id']]);
        flash('success', 'Course deleted.');
        redirect('admin/courses.php');
    }

    if ($do === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $category_id = $_POST['category_id'] ?: null;

        // Inline "create new category" straight from the course form
        $new_category = trim($_POST['new_category'] ?? '');
        if ($new_category !== '') {
            $catSlug = slugify($new_category);
            $look = $d->prepare("SELECT id FROM categories WHERE slug=? OR name=? LIMIT 1");
            $look->execute([$catSlug, $new_category]);
            $existingCat = $look->fetchColumn();
            if ($existingCat) {
                $category_id = (int)$existingCat;
            } else {
                try {
                    $d->prepare("INSERT INTO categories (name, slug, icon) VALUES (?,?, 'graduation-cap')")->execute([$new_category, $catSlug]);
                    $category_id = (int)$d->lastInsertId();
                } catch (Throwable $e) {
                    $look->execute([$catSlug, $new_category]);
                    $category_id = ((int)$look->fetchColumn()) ?: $category_id;
                }
            }
        }
        $short_desc  = trim($_POST['short_desc'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration    = trim($_POST['duration'] ?? '');
        $level       = $_POST['level'] ?? 'Beginner';
        $price       = (float)($_POST['price'] ?? 0);
        $discount    = $_POST['discount_price'] !== '' ? (float)$_POST['discount_price'] : null;
        $software    = trim($_POST['software'] ?? '');
        $syllabus    = trim($_POST['syllabus'] ?? '');
        $featured    = isset($_POST['is_featured']) ? 1 : 0;
        $status      = $_POST['status'] ?? 'active';

        if ($title === '') { flash('error','Title is required.'); redirect('admin/courses.php?action=' . ($id?'edit&id='.$id:'new')); }
        $slug = slugify($title) . ($id ? '' : '-' . substr(uniqid(), -4));

        if ($id) {
            $stmt = $d->prepare("UPDATE courses SET title=?,category_id=?,short_desc=?,description=?,duration=?,level=?,price=?,discount_price=?,software=?,syllabus=?,is_featured=?,status=? WHERE id=?");
            $stmt->execute([$title,$category_id,$short_desc,$description,$duration,$level,$price,$discount,$software,$syllabus,$featured,$status,$id]);
            flash('success', 'Course updated.');
        } else {
            $stmt = $d->prepare("INSERT INTO courses (title,slug,category_id,short_desc,description,duration,level,price,discount_price,software,syllabus,is_featured,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title,$slug,$category_id,$short_desc,$description,$duration,$level,$price,$discount,$software,$syllabus,$featured,$status]);
            flash('success', 'Course created.');
        }
        redirect('admin/courses.php');
    }
}

$action = $_GET['action'] ?? 'list';
$categories = $d->query("SELECT * FROM categories ORDER BY name")->fetchAll();

/* ---------- Add / Edit form ---------- */
if ($action === 'new' || $action === 'edit') {
    $course = ['id'=>0,'title'=>'','category_id'=>'','short_desc'=>'','description'=>'','duration'=>'3 Months','level'=>'Beginner','price'=>'','discount_price'=>'','software'=>'','syllabus'=>'','is_featured'=>0,'status'=>'active'];
    if ($action === 'edit') {
        $stmt = $d->prepare("SELECT * FROM courses WHERE id=?"); $stmt->execute([(int)$_GET['id']]);
        $course = $stmt->fetch() ?: $course;
    }
    admin_layout_top('courses', $action==='edit' ? 'Edit Course' : 'Add Course');
    ?>
    <a href="<?= url('admin/courses.php') ?>" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-900 mb-4">← Back to courses</a>
    <form method="post" class="rounded-2xl bg-white border border-slate-100 p-6 sm:p-8 max-w-3xl space-y-5">
      <?= csrf_field() ?>
      <input type="hidden" name="do" value="save">
      <input type="hidden" name="id" value="<?= (int)$course['id'] ?>">
      <div>
        <?= field_label('Course Title *') ?>
        <input name="title" value="<?= e($course['title']) ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
      </div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <?= field_label('Category') ?>
          <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
            <option value="">— None —</option>
            <?php foreach ($categories as $cat): ?><option value="<?= $cat['id'] ?>" <?= (string)$course['category_id']===(string)$cat['id']?'selected':'' ?>><?= e($cat['name']) ?></option><?php endforeach; ?>
          </select>
          <div class="mt-2">
            <input name="new_category" value="" placeholder="➕ Or type a new category to create…" class="w-full px-3 py-2 text-sm rounded-lg border border-dashed border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400">
            <p class="mt-1 text-[11px] text-slate-400">No category in the list? Just type a new one here — it will be created &amp; selected automatically on save.</p>
          </div>
        </div>
        <div>
          <?= field_label('Level') ?>
          <select name="level" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
            <?php foreach (['Beginner','Intermediate','Advanced'] as $l): ?><option <?= $course['level']===$l?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div>
        <?= field_label('Short Description') ?>
        <input name="short_desc" value="<?= e($course['short_desc']) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
      </div>
      <div>
        <?= field_label('Full Description') ?>
        <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"><?= e($course['description']) ?></textarea>
      </div>
      <div class="grid sm:grid-cols-3 gap-4">
        <div><?= field_label('Duration') ?><input name="duration" value="<?= e($course['duration']) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
        <div><?= field_label('Price (₹) *') ?><input type="number" step="0.01" name="price" value="<?= e((string)$course['price']) ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
        <div><?= field_label('Discount Price (₹)') ?><input type="number" step="0.01" name="discount_price" value="<?= e((string)$course['discount_price']) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"></div>
      </div>
      <div>
        <?= field_label('Software / Tools Covered') ?>
        <input name="software" value="<?= e($course['software']) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400">
      </div>
      <div>
        <?= field_label('Syllabus modules (separate with | )') ?>
        <textarea name="syllabus" rows="3" placeholder="Module 1|Module 2|Module 3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-400"><?= e($course['syllabus']) ?></textarea>
      </div>
      <div class="flex flex-wrap items-center gap-6">
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700"><input type="checkbox" name="is_featured" <?= $course['is_featured']?'checked':'' ?> class="w-4 h-4 rounded text-brand-600"> Featured</label>
        <div class="flex items-center gap-2">
          <?= field_label('Status') ?>
          <select name="status" class="px-3 py-2 rounded-lg border border-slate-200"><?php foreach(['active','draft'] as $s): ?><option <?= $course['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select>
        </div>
      </div>
      <div class="pt-2 flex gap-3">
        <button class="px-7 py-3 rounded-xl bg-gradient-to-r from-brand-500 to-violet-600 text-white font-semibold hover:opacity-90 transition">Save Course</button>
        <a href="<?= url('admin/courses.php') ?>" class="px-7 py-3 rounded-xl border border-slate-200 font-semibold text-slate-700 hover:border-slate-300 transition">Cancel</a>
      </div>
    </form>
    <?php
    admin_layout_bottom();
    exit;
}

/* ---------- List ---------- */
$courses = $d->query("SELECT c.*, cat.name AS category FROM courses c LEFT JOIN categories cat ON cat.id=c.category_id ORDER BY c.id DESC")->fetchAll();
admin_layout_top('courses', 'Courses');
?>
<div class="flex items-center justify-between mb-6">
  <p class="text-slate-500 text-sm"><?= count($courses) ?> total courses</p>
  <a href="<?= url('admin/courses.php?action=new') ?>" class="px-5 py-2.5 rounded-full bg-slate-900 text-white text-sm font-semibold hover:bg-brand-600 transition">+ Add Course</a>
</div>

<div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 text-left">
        <tr><th class="px-5 py-3 font-semibold">Course</th><th class="px-5 py-3 font-semibold">Category</th><th class="px-5 py-3 font-semibold">Price</th><th class="px-5 py-3 font-semibold">Level</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th></tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($courses as $c): $price=$c['discount_price']?:$c['price']; ?>
          <tr class="hover:bg-slate-50">
            <td class="px-5 py-4">
              <div class="flex items-center gap-2">
                <p class="font-medium text-slate-900"><?= e($c['title']) ?></p>
                <?php if($c['is_featured']): ?><span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold">★</span><?php endif; ?>
              </div>
            </td>
            <td class="px-5 py-4 text-slate-500"><?= e($c['category'] ?? '—') ?></td>
            <td class="px-5 py-4 font-semibold text-slate-900"><?= money($price) ?><?php if($c['discount_price']): ?> <span class="text-xs text-slate-400 line-through font-normal"><?= money($c['price']) ?></span><?php endif; ?></td>
            <td class="px-5 py-4 text-slate-500"><?= e($c['level']) ?></td>
            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $c['status']==='active'?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500' ?>"><?= e($c['status']) ?></span></td>
            <td class="px-5 py-4 text-right whitespace-nowrap">
              <a href="<?= url('admin/courses.php?action=edit&id=' . $c['id']) ?>" class="text-brand-600 font-semibold hover:text-brand-700">Edit</a>
              <form method="post" class="inline" onsubmit="return confirm('Delete this course? This cannot be undone.')">
                <?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button class="ml-3 text-rose-600 font-semibold hover:text-rose-700">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(!$courses): ?><tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No courses. Add your first course.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php admin_layout_bottom(); ?>
