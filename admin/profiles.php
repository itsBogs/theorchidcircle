<?php
require_once __DIR__ . '/../db.php';
require_admin();

// DELETE GALLERY IMAGE
if (isset($_GET['del_img'])) {
    $imgId = (int)$_GET['del_img'];
    $row = $pdo->prepare('SELECT image FROM profile_images WHERE id=?');
    $row->execute([$imgId]);
    $img = $row->fetchColumn();
    if ($img) @unlink(__DIR__ . '/../uploads/' . $img);
    $pdo->prepare('DELETE FROM profile_images WHERE id=?')->execute([$imgId]);
    header('Location: profiles.php?edit=' . (int)$_GET['pid']); exit;
}

// CHECK UPLOAD SIZE LIMIT
$upload_error = '';
if (isset($_SESSION['upload_error'])) {
    $upload_error = $_SESSION['upload_error'];
    unset($_SESSION['upload_error']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $upload_error = "The images you tried to upload are too large. Please select smaller images or fewer images at a time.";
}

// ADD GALLERY IMAGES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_gallery') {
    $pid = (int)$_POST['profile_id'];
    $successCount = 0;
    $errorMsgs = [];
    
    if (isset($_FILES['gallery_images']['name']) && is_array($_FILES['gallery_images']['name'])) {
        foreach ($_FILES['gallery_images']['name'] as $k => $fname) {
            if (empty($fname)) continue;
            
            $error = $_FILES['gallery_images']['error'][$k];
            if ($error !== UPLOAD_ERR_OK) {
                $errorMsgs[] = "File '$fname' failed with error code $error.";
                continue;
            }
            
            $imgName = time() . '_' . $k . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($fname));
            $target = __DIR__ . '/../uploads/' . $imgName;
            
            if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$k], $target)) {
                $pdo->prepare('INSERT INTO profile_images (profile_id, image, sort_order) VALUES (?,?,?)')->execute([$pid, $imgName, $k]);
                $successCount++;
            } else {
                $errorMsgs[] = "Failed to move file '$fname' to uploads directory.";
            }
        }
    }
    
    log_action($pdo, "Gallery upload for profile $pid. Success: $successCount. Errors: " . implode(' | ', $errorMsgs));
    
    // Store errors in session to display them after redirect
    if (!empty($errorMsgs)) {
        $_SESSION['upload_error'] = implode('<br>', $errorMsgs);
    }
    header('Location: profiles.php?edit=' . $pid); 
    exit;
}

// ADD PROFILE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']==='add') {
    $name = $_POST['name']; $desc = $_POST['description'];
    $rate = $_POST['rate'] ?: 0; $status = $_POST['status'] ?: 'Available';
    $category = $_POST['category'] ?: 'regular';
    $age = $_POST['age'] ?: null; $height = $_POST['height'] ?: null;
    $waist = $_POST['waist'] ?: null; $cup = $_POST['cup_size'] ?: null;
    $imageName = '';
    
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/' . $imageName);
    }
    
    $pdo->prepare('INSERT INTO profiles (name, description, image, rate_per_hour, status, category, age, height, waist, cup_size) VALUES (?,?,?,?,?,?,?,?,?,?)')
        ->execute([$name,$desc,$imageName,$rate,$status,$category,$age,$height,$waist,$cup]);
    $newId = $pdo->lastInsertId();
    log_action($pdo, 'Added profile ('.$category.'): '.$name);
    
    // Handle gallery images during add
    if (isset($_FILES['gallery_images']['name']) && is_array($_FILES['gallery_images']['name'])) {
        foreach ($_FILES['gallery_images']['name'] as $k => $fname) {
            if (empty($fname)) continue;
            if ($_FILES['gallery_images']['error'][$k] === UPLOAD_ERR_OK) {
                $gImg = time() . '_' . $k . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($fname));
                if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$k], __DIR__ . '/../uploads/' . $gImg)) {
                    $pdo->prepare('INSERT INTO profile_images (profile_id, image, sort_order) VALUES (?,?,?)')->execute([$newId, $gImg, $k]);
                }
            }
        }
    }
    header('Location: profiles.php'); exit;
}

// EDIT PROFILE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']==='edit') {
    $id = (int)$_POST['id'];
    $name = $_POST['name']; $desc = $_POST['description'];
    $rate = $_POST['rate'] ?: 0; $status = $_POST['status'] ?: 'Available';
    $category = $_POST['category'] ?: 'regular';
    $age = $_POST['age'] ?: null; $height = $_POST['height'] ?: null;
    $waist = $_POST['waist'] ?: null; $cup = $_POST['cup_size'] ?: null;
    
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/' . $imageName);
        $pdo->prepare('UPDATE profiles SET name=?,description=?,image=?,rate_per_hour=?,status=?,category=?,age=?,height=?,waist=?,cup_size=? WHERE id=?')
            ->execute([$name,$desc,$imageName,$rate,$status,$category,$age,$height,$waist,$cup,$id]);
    } else {
        $pdo->prepare('UPDATE profiles SET name=?,description=?,rate_per_hour=?,status=?,category=?,age=?,height=?,waist=?,cup_size=? WHERE id=?')
            ->execute([$name,$desc,$rate,$status,$category,$age,$height,$waist,$cup,$id]);
    }
    
    // Handle gallery images during edit
    $errorMsgs = [];
    if (isset($_FILES['gallery_images']['name']) && is_array($_FILES['gallery_images']['name'])) {
        foreach ($_FILES['gallery_images']['name'] as $k => $fname) {
            if (empty($fname)) continue;
            $err = $_FILES['gallery_images']['error'][$k];
            if ($err !== UPLOAD_ERR_OK) {
                $errorMsgs[] = "File '$fname' failed (code $err).";
                continue;
            }
            $gImg = time() . '_' . $k . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($fname));
            if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$k], __DIR__ . '/../uploads/' . $gImg)) {
                $pdo->prepare('INSERT INTO profile_images (profile_id, image, sort_order) VALUES (?,?,?)')->execute([$id, $gImg, $k]);
            }
        }
    }
    
    log_action($pdo, 'Edited profile: '.$id);
    if (!empty($errorMsgs)) {
        $_SESSION['upload_error'] = implode('<br>', $errorMsgs);
    }
    
    header('Location: profiles.php?edit='.$id); exit;
}

// DELETE PROFILE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM profiles WHERE id=?')->execute([$id]);
    log_action($pdo, 'Deleted profile: '.$id);
    header('Location: profiles.php'); exit;
}

$profiles = $pdo->query('SELECT * FROM profiles ORDER BY id DESC')->fetchAll();
$editing = null; $galleryImgs = [];
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $s = $pdo->prepare('SELECT * FROM profiles WHERE id=?'); $s->execute([$eid]); $editing = $s->fetch();
    $gi = $pdo->prepare('SELECT * FROM profile_images WHERE profile_id=? ORDER BY sort_order ASC'); $gi->execute([$eid]);
    $galleryImgs = $gi->fetchAll();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Profiles</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .gallery-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 1rem; }
    .gallery-grid img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0; }
    .gallery-grid .del-img { font-size: 0.75rem; color: #dc2626; cursor: pointer; display: block; text-align: center; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-row div label { display: block; margin-bottom: 0.4rem; font-weight: 500; font-size: 0.9rem; }
    .form-row div input, .form-row div select { width: 100%; padding: 0.75rem; border: 1px solid rgba(212,175,55,0.2); border-radius: var(--radius-md); font-family: var(--font-family); }
  </style>
</head>
<body style="background: var(--bg-color); margin: 0; padding: 0;">
  <div class="admin-layout">
    <aside class="sidebar">
      <h2 style="display:flex; align-items:center; gap:8px; text-transform:none; letter-spacing:0.5px; font-size:1.1rem;"><img src="../uploads/logo-removebg-preview.png" style="height:28px; object-fit:contain;"> The Orchid Circle</h2>
      <nav style="margin-top: 1rem;">
        <a href="dashboard.php">Dashboard</a>
        <a href="profiles.php" style="border-left: 4px solid var(--accent-gold); background: rgba(255,255,255,0.05); color: var(--accent-gold);">Profiles</a>
        <a href="bookings.php">Bookings</a>
        <a href="messages.php">Messages</a>
        <a href="users.php">Admin Users</a>
        <a href="logout.php" style="margin-top: 2rem; color: #ef4444;">Logout</a>
      </nav>
    </aside>

    <main class="admin-content">
      <header style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin: 0; color: var(--gold);">Manage Profiles</h1>
        <?php if($editing): ?><a href="profiles.php" style="padding: 0.5rem 1rem; background: var(--accent-gold); color: var(--gold); border-radius: 8px; text-decoration: none; font-weight: 600;">+ New Profile</a><?php endif; ?>
      </header>

      <?php if (!empty($upload_error)): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #991b1b;">
          <strong>Error:</strong> <?php echo htmlspecialchars($upload_error); ?>
        </div>
      <?php endif; ?>

      <!-- FORM -->
      <div class="card" style="max-width: 860px; padding: 2rem; margin-bottom: 2rem;">
        <?php if ($editing): ?>
          <h3 style="margin-top: 0; color: var(--gold);">Editing: <?php echo htmlspecialchars($editing['name']); ?></h3>
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">

            <div class="form-row">
              <div><label>Name</label><input name="name" value="<?php echo htmlspecialchars($editing['name']); ?>" required></div>
              <div><label>Rate per hour (&#8369;)</label><input name="rate" type="number" step="0.01" value="<?php echo htmlspecialchars($editing['rate_per_hour']); ?>"></div>
            </div>
            <div class="form-row" style="margin-top:1rem;">
              <div><label>Age</label><input name="age" type="number" value="<?php echo htmlspecialchars($editing['age'] ?? ''); ?>"></div>
              <div><label>Height</label><input name="height" placeholder="e.g. 5'5&quot;" value="<?php echo htmlspecialchars($editing['height'] ?? ''); ?>"></div>
            </div>
            <div class="form-row" style="margin-top:1rem;">
              <div><label>Waist</label><input name="waist" placeholder='e.g. 24"' value="<?php echo htmlspecialchars($editing['waist'] ?? ''); ?>"></div>
              <div><label>Cup Size</label><input name="cup_size" placeholder="e.g. 34C" value="<?php echo htmlspecialchars($editing['cup_size'] ?? ''); ?>"></div>
            </div>
            <label style="display:block;margin:1rem 0 0.4rem;font-weight:500;">Description</label>
            <textarea name="description" rows="3" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;font-family:var(--font-family);"><?php echo htmlspecialchars($editing['description']); ?></textarea>
            <div class="form-row" style="margin-top:1rem;">
              <div>
                <label>Category</label>
                <select name="category" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;font-weight:600;">
                  <option value="regular"<?php if(($editing['category'] ?? 'regular')==='regular') echo ' selected'; ?>>Regular</option>
                  <option value="vip"<?php if(($editing['category'] ?? '')==='vip') echo ' selected'; ?>>VIP</option>
                </select>
              </div>
              <div>
                <label>Status</label>
                <select name="status" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;">
                  <option<?php if($editing['status']==='Available') echo ' selected'; ?>>Available</option>
                  <option<?php if($editing['status']==='Unavailable') echo ' selected'; ?>>Unavailable</option>
                </select>
              </div>
            </div>
            
            <div style="background: var(--bg-color); padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem; border: 1px dashed #cbd5e1;">
              <h4 style="margin: 0 0 1rem; color: var(--gold);">Upload Photos</h4>
              <label style="display:block;margin-bottom:0.4rem;font-weight:500;">1. Cover Image (leave empty to keep)</label>
              <input type="file" name="image" style="margin-bottom:1rem; width: 100%;">
              
              <?php if(count($galleryImgs) < 9): ?>
                <label style="display:block;margin-bottom:0.4rem;font-weight:500;">2. Add Gallery Photos (You can select multiple files at once)</label>
                <input type="file" name="gallery_images[]" multiple accept="image/*" style="width: 100%;">
              <?php else: ?>
                <p style="color: #ef4444; font-size: 0.85rem;">Gallery limit reached (max 9). Delete some to upload more.</p>
              <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-gold" style="width:100%;padding:1.2rem;font-size:1.1rem;margin-top:1.5rem;">Save All Changes</button>
          </form>

          <!-- Gallery Management (View/Delete only) -->
          <div style="margin-top:2rem;border-top:1px solid #e2e8f0;padding-top:1.5rem;">
            <h4 style="margin:0 0 1rem;color:var(--gold);">Current Gallery Images (<?php echo count($galleryImgs); ?>/9)</h4>
            <div class="gallery-grid">
              <?php foreach($galleryImgs as $gi): ?>
                <div style="text-align:center;">
                  <img src="../uploads/<?php echo htmlspecialchars($gi['image']); ?>" title="<?php echo htmlspecialchars($gi['image']); ?>">
                  <a class="del-img" href="profiles.php?del_img=<?php echo $gi['id']; ?>&edit=<?php echo $editing['id']; ?>&pid=<?php echo $editing['id']; ?>" onclick="return confirm('Remove this image?')">&times; Remove</a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

        <?php else: ?>
          <h3 style="margin-top:0;color:var(--gold);">Add New Profile</h3>
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="form-row">
              <div><label>Name</label><input name="name" required placeholder="e.g. Jane Doe"></div>
              <div><label>Rate per hour (&#8369;)</label><input name="rate" type="number" step="0.01" placeholder="1500"></div>
            </div>
            <div class="form-row" style="margin-top:1rem;">
              <div><label>Age</label><input name="age" type="number" placeholder="22"></div>
              <div><label>Height</label><input name="height" placeholder="5'5&quot;"></div>
            </div>
            <div class="form-row" style="margin-top:1rem;">
              <div><label>Waist</label><input name="waist" placeholder='24"'></div>
              <div><label>Cup Size</label><input name="cup_size" placeholder="34C"></div>
            </div>
            <label style="display:block;margin:1rem 0 0.4rem;font-weight:500;">Description</label>
            <textarea name="description" rows="3" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;font-family:var(--font-family);" placeholder="Short bio..."></textarea>
            <div class="form-row" style="margin-top:1rem;">
              <div>
                <label>Category</label>
                <select name="category" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;font-weight:600;">
                  <option value="regular">Regular</option>
                  <option value="vip">VIP</option>
                </select>
              </div>
              <div>
                <label>Status</label>
                <select name="status" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;">
                  <option>Available</option>
                  <option>Unavailable</option>
                </select>
              </div>
            </div>
            
            <div style="background: var(--bg-color); padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem; border: 1px dashed #cbd5e1;">
              <h4 style="margin: 0 0 1rem; color: var(--gold);">Upload Photos</h4>
              <label style="display:block;margin-bottom:0.4rem;font-weight:500;">1. Cover Image</label>
              <input type="file" name="image" style="margin-bottom:1rem; width: 100%;">
              
              <label style="display:block;margin-bottom:0.4rem;font-weight:500;">2. Add Gallery Photos (You can select multiple files at once)</label>
              <input type="file" name="gallery_images[]" multiple accept="image/*" style="width: 100%;">
            </div>
            
            <button type="submit" class="btn-gold" style="width:100%;padding:1.2rem;font-size:1.1rem;margin-top:1.5rem;">Add Profile & Upload Photos</button>
          </form>
        <?php endif; ?>
      </div>

      <!-- PROFILES TABLE -->
      <div class="table-wrapper">
        <h3 style="margin:0 0 1rem;color:var(--gold);">All Profiles</h3>
        <table>
          <thead>
            <tr><th>Photo</th><th>Name</th><th>Age</th><th>Stats</th><th>Rate</th><th>Category</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($profiles as $p): ?>
              <tr>
                <td style="width:60px;">
                  <?php if(!empty($p['image'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($p['image']); ?>" style="width:48px;height:60px;object-fit:cover;border-radius:8px;">
                  <?php else: ?>
                    <div style="width:48px;height:60px;border-radius:8px;background:var(--gold);color:var(--accent-gold);display:flex;align-items:center;justify-content:center;font-weight:bold;"><?php echo strtoupper(substr($p['name'],0,1)); ?></div>
                  <?php endif; ?>
                </td>
                <td style="font-weight:600;color:var(--gold);"><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo $p['age'] ? $p['age'].' yrs' : '&mdash;'; ?></td>
                <td style="font-size:0.85rem;color:var(--text-light);">
                  <?php echo !empty($p['height']) ? htmlspecialchars($p['height']) : '&mdash;'; ?> &middot; <?php echo !empty($p['waist']) ? htmlspecialchars($p['waist']) : '&mdash;'; ?> &middot; <?php echo !empty($p['cup_size']) ? htmlspecialchars($p['cup_size']) : '&mdash;'; ?>
                </td>
                <td>&#8369;<?php echo number_format($p['rate_per_hour'],0); ?>/hr</td>
                <td>
                  <?php if(($p['category'] ?? 'regular') === 'vip'): ?>
                    <span style="background:linear-gradient(135deg,#D4AF37,#f0d060);color:#000;padding:3px 10px;border-radius:999px;font-size:0.75rem;font-weight:700;">VIP</span>
                  <?php else: ?>
                    <span style="background:#e2e8f0;color:#475569;padding:3px 10px;border-radius:999px;font-size:0.75rem;font-weight:600;">Regular</span>
                  <?php endif; ?>
                </td>
                <td><span class="badge <?php echo $p['status']; ?>"><?php echo $p['status']; ?></span></td>
                <td>
                  <a href="profiles.php?edit=<?php echo $p['id']; ?>" style="color:var(--gold);margin-right:10px;text-decoration:none;font-weight:600;">Edit</a>
                  <a href="profiles.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete this profile?');" style="color:#ef4444;text-decoration:none;font-weight:600;">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if(empty($profiles)): ?><tr><td colspan="8" style="text-align:center;">No profiles found.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>

