<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get dashboard stats
$total_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM members"))['count'];
$monthly_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payments WHERE MONTH(payment_date) = MONTH(CURDATE())"))['total'] ?? 0;
$attendance_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE check_in_date = CURDATE()"))['count'];
$expiry_alerts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE date_expiry <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status = 'Active'"))['count'];

// Get today's attendance
$attendance_query = "SELECT m.first_name, m.last_name, a.check_in_time, m.status 
                     FROM attendance a 
                     JOIN members m ON a.member_id = m.id 
                     WHERE a.check_in_date = CURDATE() 
                     ORDER BY a.check_in_time DESC LIMIT 6";
$attendance_result = mysqli_query($conn, $attendance_query);

// Get recent announcements
$announcement_query = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 4";
$announcement_result = mysqli_query($conn, $announcement_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — Power Fitness Gym</title>
  <link rel="stylesheet" href="assets/css/global.css"/>
  <link rel="stylesheet" href="assets/css/dashboard.css"/>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
  <script>document.addEventListener('DOMContentLoaded',()=>lucide.createIcons());</script>
</head>
<body>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar__logo">
      <span class="logo-dot"></span>
      <span class="logo-text">POWER<em>FITNESS GYM</em></span>
    </div>
    <nav class="sidebar__nav">
      <a href="dashboard.php" class="nav-item active"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></a>
      <a href="manage-member.php" class="nav-item"><i data-lucide="users"></i><span>Manage Member</span></a>
      <a href="attendance.php" class="nav-item"><i data-lucide="scan-line"></i><span>Attendance</span></a>
      <a href="inventory.php" class="nav-item"><i data-lucide="package"></i><span>Inventory</span></a>
      <a href="member-status.php" class="nav-item"><i data-lucide="shield-check"></i><span>Member Status</span></a>
      <a href="payment.php" class="nav-item"><i data-lucide="credit-card"></i><span>Payment</span></a>
      <a href="announcement.php" class="nav-item"><i data-lucide="megaphone"></i><span>Announcement</span></a>
      <a href="metrics.php" class="nav-item"><i data-lucide="bar-chart-3"></i><span>Metrics</span></a>
    </nav>
    <div class="sidebar__footer">
      <div class="sidebar__user">
        <div class="avatar"><?php echo strtoupper(substr($user_name, 0, 2)); ?></div>
        <div class="user-info"><span class="user-name"><?php echo htmlspecialchars($user_name); ?></span><span class="user-role">Admin</span></div>
      </div>
      <a href="logout.php" style="display:flex;align-items:center;gap:8px;padding:8px 16px;color:var(--text-secondary);font-size:12px;margin-top:8px;border-radius:var(--radius-sm);transition:all var(--transition);" onmouseover="this.style.background='var(--accent-bg)';this.style.color='var(--accent)';" onmouseout="this.style.background='none';this.style.color='var(--text-secondary)';"><i data-lucide="log-out" style="width:14px;height:14px;"></i>Logout</a>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <!-- TOP BAR -->
    <header class="topbar">
      <div class="topbar__title">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($user_name); ?></p>
      </div>
      <div class="topbar__actions">
        <div class="search-bar">
          <i data-lucide="search"></i>
          <input type="text" placeholder="Search..."/>
        </div>
      </div>
    </header>

    <!-- STATS -->
    <section class="stats-grid">
      <div class="stat-card stat-card--green">
        <div class="stat-card__icon"><i data-lucide="users"></i></div>
        <div class="stat-card__body">
          <span class="stat-value"><?php echo number_format($total_members); ?></span>
          <span class="stat-label">Total Members</span>
        </div>
        <span class="stat-tag">Active members</span>
      </div>
      <div class="stat-card">
        <div class="stat-card__icon"><i data-lucide="credit-card"></i></div>
        <div class="stat-card__body">
          <span class="stat-value">₱ <?php echo number_format($monthly_revenue, 2); ?></span>
          <span class="stat-label">Monthly Revenue</span>
        </div>
        <span class="stat-tag">This month</span>
      </div>
      <div class="stat-card">
        <div class="stat-card__icon"><i data-lucide="scan-line"></i></div>
        <div class="stat-card__body">
          <span class="stat-value"><?php echo $attendance_today; ?></span>
          <span class="stat-label">Attendance Today</span>
        </div>
        <span class="stat-tag">Active check-ins</span>
      </div>
      <div class="stat-card">
        <div class="stat-card__icon"><i data-lucide="alert-triangle"></i></div>
        <div class="stat-card__body">
          <span class="stat-value"><?php echo $expiry_alerts; ?></span>
          <span class="stat-label">Expiry Alerts</span>
        </div>
        <span class="stat-tag stat-tag--warn">Needs attention</span>
      </div>
    </section>

    <!-- MIDDLE ROW -->
    <section class="mid-grid">

      <!-- Quick Actions -->
      <div class="card quick-actions">
        <div class="card__header"><h2>Quick Actions</h2></div>
        <div class="actions-list">
          <a href="manage-member.php" class="action-btn action-btn--primary"><i data-lucide="user-plus"></i>Add Member</a>
          <a href="payment.php" class="action-btn"><i data-lucide="credit-card"></i>Process Payment</a>
          <a href="inventory.php" class="action-btn"><i data-lucide="package"></i>View Inventory</a>
          <a href="attendance.php" class="action-btn"><i data-lucide="qr-code"></i>Scan QR</a>
          <a href="member-status.php" class="action-btn"><i data-lucide="shield-check"></i>Member Status</a>
        </div>
      </div>

      <!-- Attendance -->
      <div class="card attendance-card">
        <div class="card__header">
          <h2>Attendance Today</h2>
          <span class="chip">Live</span>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr><th>Member</th><th>Time In</th><th>Status</th></tr>
            </thead>
            <tbody>
              <?php 
              if (mysqli_num_rows($attendance_result) > 0) {
                  while ($row = mysqli_fetch_assoc($attendance_result)) {
                      $status_class = strtolower($row['status']) == 'active' ? 'status--active' : 'status--expired';
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                      echo "<td>" . format_time($row['check_in_time']) . "</td>";
                      echo "<td><span class='status $status_class'>" . htmlspecialchars($row['status']) . "</span></td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='3' style='text-align:center;color:var(--text-muted);'>No attendance records today</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Announcements -->
      <div class="card announcements-card">
        <div class="card__header">
          <h2>Announcements</h2>
          <a href="announcement.php" class="btn btn--outline btn--sm">Manage</a>
        </div>
        <?php 
        $announcements = mysqli_fetch_all($announcement_result, MYSQLI_ASSOC);
        if (count($announcements) > 0) {
            $featured = $announcements[0];
            echo "<div class='ann-featured-mini'>";
            echo "<span class='ann-tag-mini'>" . htmlspecialchars($featured['priority']) . "</span>";
            echo "<p class='ann-title-mini'>" . htmlspecialchars($featured['title']) . "</p>";
            echo "<p class='ann-body-mini'>" . htmlspecialchars(substr($featured['message'], 0, 50)) . "...</p>";
            echo "<span class='ann-date-mini'>From " . date('M d', strtotime($featured['date_from'])) . "-" . date('d', strtotime($featured['date_to'])) . "</span>";
            echo "</div>";
            
            if (count($announcements) > 1) {
                echo "<div class='ann-prev-label'>Previous Announcements</div>";
                echo "<ul class='ann-list-mini'>";
                for ($i = 1; $i < count($announcements); $i++) {
                    echo "<li><i data-lucide='megaphone'></i>" . htmlspecialchars($announcements[$i]['title']) . "</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p style='color:var(--text-muted);text-align:center;'>No announcements</p>";
        }
        ?>
      </div>

    </section>

  </main>
</body>
</html>
