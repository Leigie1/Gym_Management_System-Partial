<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get date range (default: last 30 days)
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d', strtotime('-30 days'));
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

// 1. Total Members
$total_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM members"))['count'];

// 2. Active vs Expired Members
$active_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE status='Active'"))['count'];
$expired_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE status='Expired'"))['count'];

// 3. New Members This Month
$new_members_month = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE MONTH(date_enrolled) = MONTH(CURDATE()) AND YEAR(date_enrolled) = YEAR(CURDATE())"))['count'];

// 4. Revenue Statistics
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments"))['total'];
$monthly_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())"))['total'];
$today_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE payment_date = CURDATE()"))['total'];

// 5. Attendance Statistics
$total_checkins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance"))['count'];
$today_checkins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE check_in_date = CURDATE()"))['count'];
$monthly_checkins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE MONTH(check_in_date) = MONTH(CURDATE()) AND YEAR(check_in_date) = YEAR(CURDATE())"))['count'];

// 6. Average attendance per day (this month)
$avg_attendance = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(AVG(daily_count), 0) as avg FROM (SELECT COUNT(*) as daily_count FROM attendance WHERE MONTH(check_in_date) = MONTH(CURDATE()) AND YEAR(check_in_date) = YEAR(CURDATE()) GROUP BY check_in_date) as daily_stats"))['avg'];

// 7. Membership Plans Distribution
$plan_stats = mysqli_query($conn, "SELECT plan, COUNT(*) as count FROM members GROUP BY plan");

// 8. Revenue by Month (Last 6 months)
$revenue_by_month = mysqli_query($conn, "SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total FROM payments WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(payment_date, '%Y-%m') ORDER BY month ASC");

// 9. Attendance by Day (Last 7 days)
$attendance_by_day = mysqli_query($conn, "SELECT check_in_date, COUNT(*) as count FROM attendance WHERE check_in_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY check_in_date ORDER BY check_in_date ASC");

// 10. Top 5 Most Active Members
$top_members = mysqli_query($conn, "SELECT m.first_name, m.last_name, COUNT(a.id) as checkins FROM members m LEFT JOIN attendance a ON m.id = a.member_id GROUP BY m.id ORDER BY checkins DESC LIMIT 5");

// 11. Expiring Soon (Next 30 days)
$expiring_soon = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE date_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND status='Active'"))['count'];

// 12. Payment Methods Distribution
$payment_methods = mysqli_query($conn, "SELECT payment_method, COUNT(*) as count, SUM(amount) as total FROM payments GROUP BY payment_method");

// 13. Inventory Value
$inventory_value = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(quantity * price), 0) as total FROM inventory"))['total'];
$inventory_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory"))['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Business Metrics — Power Fitness Gym</title>
  <link rel="stylesheet" href="assets/css/global.css"/>
  <link rel="stylesheet" href="assets/css/dashboard.css"/>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .metric-card {
      background: var(--card-bg);
      border-radius: 12px;
      padding: 20px;
      border: 1px solid var(--border);
    }
    .metric-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 15px;
    }
    .metric-icon {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(200, 245, 58, 0.1);
    }
    .metric-icon i {
      color: var(--accent);
      width: 20px;
      height: 20px;
    }
    .metric-title {
      font-size: 13px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .metric-value {
      font-size: 32px;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 8px;
    }
    .metric-subtitle {
      font-size: 13px;
      color: var(--text-muted);
    }
    .metric-trend {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      padding: 4px 8px;
      border-radius: 4px;
      margin-top: 8px;
    }
    .metric-trend.up {
      background: rgba(34, 197, 94, 0.1);
      color: #22c55e;
    }
    .metric-trend.down {
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
    }
    .chart-container {
      background: var(--card-bg);
      border-radius: 12px;
      padding: 24px;
      border: 1px solid var(--border);
      margin-bottom: 20px;
    }
    .chart-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--text-primary);
    }
    .chart-wrapper {
      position: relative;
      height: 300px;
    }
    .table-container {
      background: var(--card-bg);
      border-radius: 12px;
      padding: 24px;
      border: 1px solid var(--border);
    }
    .filter-bar {
      background: var(--card-bg);
      border-radius: 12px;
      padding: 20px;
      border: 1px solid var(--border);
      margin-bottom: 20px;
      display: flex;
      gap: 15px;
      align-items: center;
      flex-wrap: wrap;
    }
    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }
    .filter-group label {
      font-size: 12px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .filter-group input {
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid var(--border);
      background: var(--bg);
      color: var(--text-primary);
      font-size: 14px;
    }
  </style>
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
      <a href="announcement.php" class="nav-item"><i data-lucide="megaphone"></i><span>Announcement</span></a>
      <a href="metrics.php" class="nav-item active"><i data-lucide="bar-chart-3"></i><span>Metrics</span></a>
    </nav>
    <div class="sidebar__footer">
      <div class="sidebar__user">
        <div class="avatar">JD</div>
        <div class="user-info"><span class="user-name">John Doe</span><span class="user-role">Admin</span></div>
      </div>
    </div>
  </aside>

  <main class="main">

    <header class="topbar">
      <div class="topbar__title">
        <h1>Business Metrics</h1>
        <p>Analytics and performance insights</p>
      </div>
      <div class="topbar__actions">
        <button class="btn btn--outline" onclick="window.print()">
          <i data-lucide="printer"></i> Print Report
        </button>
      </div>
    </header>

    <!-- Key Metrics -->
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon">
            <i data-lucide="users"></i>
          </div>
          <div class="metric-title">Total Members</div>
        </div>
        <div class="metric-value"><?php echo number_format($total_members); ?></div>
        <div class="metric-subtitle"><?php echo $active_members; ?> active, <?php echo $expired_members; ?> expired</div>
        <div class="metric-trend up">
          <i data-lucide="trending-up" style="width:14px;height:14px;"></i>
          <?php echo $new_members_month; ?> new this month
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon">
            <i data-lucide="dollar-sign"></i>
          </div>
          <div class="metric-title">Total Revenue</div>
        </div>
        <div class="metric-value">₱<?php echo number_format($total_revenue, 2); ?></div>
        <div class="metric-subtitle">₱<?php echo number_format($monthly_revenue, 2); ?> this month</div>
        <div class="metric-trend up">
          <i data-lucide="trending-up" style="width:14px;height:14px;"></i>
          ₱<?php echo number_format($today_revenue, 2); ?> today
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon">
            <i data-lucide="activity"></i>
          </div>
          <div class="metric-title">Total Check-ins</div>
        </div>
        <div class="metric-value"><?php echo number_format($total_checkins); ?></div>
        <div class="metric-subtitle"><?php echo number_format($monthly_checkins); ?> this month</div>
        <div class="metric-trend up">
          <i data-lucide="trending-up" style="width:14px;height:14px;"></i>
          <?php echo $today_checkins; ?> today
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon">
            <i data-lucide="trending-up"></i>
          </div>
          <div class="metric-title">Avg Daily Attendance</div>
        </div>
        <div class="metric-value"><?php echo number_format($avg_attendance, 1); ?></div>
        <div class="metric-subtitle">Members per day (this month)</div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon">
            <i data-lucide="alert-circle"></i>
          </div>
          <div class="metric-title">Expiring Soon</div>
        </div>
        <div class="metric-value"><?php echo $expiring_soon; ?></div>
        <div class="metric-subtitle">Memberships expiring in 30 days</div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon">
            <i data-lucide="package"></i>
          </div>
          <div class="metric-title">Inventory Value</div>
        </div>
        <div class="metric-value">₱<?php echo number_format($inventory_value, 2); ?></div>
        <div class="metric-subtitle"><?php echo $inventory_items; ?> items in stock</div>
      </div>
    </div>

    <!-- Charts Row 1 -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:20px;margin-bottom:20px;">
      
      <!-- Revenue Chart -->
      <div class="chart-container">
        <div class="chart-title">Revenue Trend (Last 6 Months)</div>
        <div class="chart-wrapper">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>

      <!-- Attendance Chart -->
      <div class="chart-container">
        <div class="chart-title">Attendance Trend (Last 7 Days)</div>
        <div class="chart-wrapper">
          <canvas id="attendanceChart"></canvas>
        </div>
      </div>

    </div>

    <!-- Charts Row 2 -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:20px;margin-bottom:20px;">
      
      <!-- Member Status Chart -->
      <div class="chart-container">
        <div class="chart-title">Member Status Distribution</div>
        <div class="chart-wrapper">
          <canvas id="memberStatusChart"></canvas>
        </div>
      </div>

      <!-- Payment Methods Chart -->
      <div class="chart-container">
        <div class="chart-title">Payment Methods</div>
        <div class="chart-wrapper">
          <canvas id="paymentMethodsChart"></canvas>
        </div>
      </div>

    </div>

    <!-- Top Members Table -->
    <div class="table-container">
      <div class="chart-title">Top 5 Most Active Members</div>
      <table class="data-table">
        <thead>
          <tr>
            <th>Rank</th>
            <th>Member Name</th>
            <th>Total Check-ins</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $rank = 1;
          while ($member = mysqli_fetch_assoc($top_members)) {
            echo "<tr>";
            echo "<td><strong>#" . $rank . "</strong></td>";
            echo "<td>" . htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) . "</td>";
            echo "<td><span class='status status--active'>" . $member['checkins'] . " check-ins</span></td>";
            echo "</tr>";
            $rank++;
          }
          ?>
        </tbody>
      </table>
    </div>

  </main>

  <script>
    // Revenue Chart
    const revenueData = {
      labels: [
        <?php 
        mysqli_data_seek($revenue_by_month, 0);
        $labels = [];
        while ($row = mysqli_fetch_assoc($revenue_by_month)) {
          $labels[] = "'" . date('M Y', strtotime($row['month'] . '-01')) . "'";
        }
        echo implode(',', $labels);
        ?>
      ],
      datasets: [{
        label: 'Revenue (₱)',
        data: [
          <?php 
          mysqli_data_seek($revenue_by_month, 0);
          $values = [];
          while ($row = mysqli_fetch_assoc($revenue_by_month)) {
            $values[] = $row['total'];
          }
          echo implode(',', $values);
          ?>
        ],
        borderColor: '#c8f53a',
        backgroundColor: 'rgba(200, 245, 58, 0.1)',
        tension: 0.4,
        fill: true
      }]
    };

    new Chart(document.getElementById('revenueChart'), {
      type: 'line',
      data: revenueData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { 
            beginAtZero: true,
            ticks: { color: '#888' },
            grid: { color: 'rgba(255,255,255,0.05)' }
          },
          x: { 
            ticks: { color: '#888' },
            grid: { color: 'rgba(255,255,255,0.05)' }
          }
        }
      }
    });

    // Attendance Chart
    const attendanceData = {
      labels: [
        <?php 
        mysqli_data_seek($attendance_by_day, 0);
        $labels = [];
        while ($row = mysqli_fetch_assoc($attendance_by_day)) {
          $labels[] = "'" . date('M d', strtotime($row['check_in_date'])) . "'";
        }
        echo implode(',', $labels);
        ?>
      ],
      datasets: [{
        label: 'Check-ins',
        data: [
          <?php 
          mysqli_data_seek($attendance_by_day, 0);
          $values = [];
          while ($row = mysqli_fetch_assoc($attendance_by_day)) {
            $values[] = $row['count'];
          }
          echo implode(',', $values);
          ?>
        ],
        backgroundColor: '#c8f53a',
        borderRadius: 6
      }]
    };

    new Chart(document.getElementById('attendanceChart'), {
      type: 'bar',
      data: attendanceData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { 
            beginAtZero: true,
            ticks: { color: '#888', stepSize: 1 },
            grid: { color: 'rgba(255,255,255,0.05)' }
          },
          x: { 
            ticks: { color: '#888' },
            grid: { display: false }
          }
        }
      }
    });

    // Member Status Chart
    new Chart(document.getElementById('memberStatusChart'), {
      type: 'doughnut',
      data: {
        labels: ['Active', 'Expired'],
        datasets: [{
          data: [<?php echo $active_members; ?>, <?php echo $expired_members; ?>],
          backgroundColor: ['#c8f53a', '#ef4444'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: '#888', padding: 15 }
          }
        }
      }
    });

    // Payment Methods Chart
    new Chart(document.getElementById('paymentMethodsChart'), {
      type: 'pie',
      data: {
        labels: [
          <?php 
          mysqli_data_seek($payment_methods, 0);
          $labels = [];
          while ($row = mysqli_fetch_assoc($payment_methods)) {
            $labels[] = "'" . $row['payment_method'] . "'";
          }
          echo implode(',', $labels);
          ?>
        ],
        datasets: [{
          data: [
            <?php 
            mysqli_data_seek($payment_methods, 0);
            $values = [];
            while ($row = mysqli_fetch_assoc($payment_methods)) {
              $values[] = $row['count'];
            }
            echo implode(',', $values);
            ?>
          ],
          backgroundColor: ['#c8f53a', '#3b82f6', '#8b5cf6'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: '#888', padding: 15 }
          }
        }
      }
    });
  </script>
</body>
</html>
