<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

$announcements_query = "SELECT * FROM announcements ORDER BY created_at DESC";
$announcements_result = mysqli_query($conn, $announcements_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Announcements — Power Fitness Gym</title>
  <link rel="stylesheet" href="assets/css/global.css"/>
  <link rel="stylesheet" href="assets/css/announcement.css"/>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
  <script>document.addEventListener('DOMContentLoaded',()=>lucide.createIcons());</script>
</head>
<body>
  <aside class="sidebar">
    <div class="sidebar__logo">
      <span class="logo-dot"></span>
      <span class="logo-text">POWER<em>FITNESS GYM</em></span>
    </div>
    <nav class="sidebar__nav">
      <a href="dashboard.php" class="nav-item"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></a>
      <a href="manage-member.php" class="nav-item"><i data-lucide="users"></i><span>Manage Member</span></a>
      <a href="attendance.php" class="nav-item"><i data-lucide="scan-line"></i><span>Attendance</span></a>
      <a href="inventory.php" class="nav-item"><i data-lucide="package"></i><span>Inventory</span></a>
      <a href="member-status.php" class="nav-item"><i data-lucide="shield-check"></i><span>Member Status</span></a>
      <a href="payment.php" class="nav-item"><i data-lucide="credit-card"></i><span>Payment</span></a>
      <a href="announcement.php" class="nav-item active"><i data-lucide="megaphone"></i><span>Announcement</span></a>    </nav>
    <div class="sidebar__footer">
      <div class="sidebar__user">
        <div class="avatar"><?php echo strtoupper(substr($user_name, 0, 2)); ?></div>
        <div class="user-info"><span class="user-name"><?php echo htmlspecialchars($user_name); ?></span><span class="user-role">Admin</span></div>
      </div>
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <div class="topbar__title">
        <h1>Announcements</h1>
        <p>Create and manage gym announcements</p>
      </div>
      <div class="topbar__actions">
        <button class="icon-btn"><i data-lucide="bell"></i><span class="badge">3</span></button>
      </div>
    </header>

    <div class="card">
      <div class="card__header"><h2>Create Announcement</h2></div>
      <form method="POST" action="actions/add-announcement.php">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" class="form-control" required/>
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea name="message" class="form-control" rows="4" required></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
          <div class="form-group">
            <label>Date From</label>
            <input type="date" name="date_from" class="form-control" required/>
          </div>
          <div class="form-group">
            <label>Date To</label>
            <input type="date" name="date_to" class="form-control" required/>
          </div>
          <div class="form-group">
            <label>Priority</label>
            <select name="priority" class="form-control" required>
              <option>Normal</option>
              <option>Important</option>
              <option>Urgent</option>
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn--primary"><i data-lucide="megaphone"></i> Post Announcement</button>
      </form>
    </div>

    <div class="card" style="margin-top:20px;">
      <div class="card__header"><h2>All Announcements</h2></div>
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php 
        if (mysqli_num_rows($announcements_result) > 0) {
            while ($ann = mysqli_fetch_assoc($announcements_result)) {
                echo "<div style='background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;'>";
                echo "<div style='display:flex;justify-content:space-between;align-items:start;margin-bottom:8px;'>";
                echo "<div>";
                echo "<span style='font-size:10px;background:var(--accent);color:#0d0d0d;padding:2px 8px;border-radius:20px;font-weight:700;'>" . htmlspecialchars($ann['priority']) . "</span>";
                echo "<h3 style='margin:6px 0;font-size:16px;'>" . htmlspecialchars($ann['title']) . "</h3>";
                echo "</div>";
                echo "<form method='POST' action='actions/delete-announcement.php' style='display:inline;' onsubmit='return confirm(\"Delete this announcement?\")'>";
                echo "<input type='hidden' name='announcement_id' value='" . $ann['id'] . "'/>";
                echo "<button type='submit' class='btn btn--danger btn--sm'>Delete</button>";
                echo "</form>";
                echo "</div>";
                echo "<p style='color:var(--text-secondary);font-size:13px;margin-bottom:8px;'>" . htmlspecialchars($ann['message']) . "</p>";
                echo "<p style='color:var(--accent);font-size:11px;'>From " . date('M d', strtotime($ann['date_from'])) . " to " . date('M d, Y', strtotime($ann['date_to'])) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p style='text-align:center;color:var(--text-muted);'>No announcements yet</p>";
        }
        ?>
      </div>
    </div>
  </main>
</body>
</html>
