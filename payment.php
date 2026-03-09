<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get all members for dropdown
$members_query = "SELECT id, first_name, last_name, member_id_code FROM members ORDER BY first_name ASC";
$members_result = mysqli_query($conn, $members_query);

// Get transaction history
$payments_query = "SELECT p.*, m.first_name, m.last_name 
                   FROM payments p 
                   JOIN members m ON p.member_id = m.id 
                   ORDER BY p.payment_date DESC, p.created_at DESC";
$payments_result = mysqli_query($conn, $payments_query);

// Calculate total revenue
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payments"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment — Power Fitness Gym</title>
  <link rel="stylesheet" href="assets/css/global.css"/>
  <link rel="stylesheet" href="assets/css/payment.css"/>
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
      <a href="payment.php" class="nav-item active"><i data-lucide="credit-card"></i><span>Payment</span></a>
      <a href="announcement.php" class="nav-item"><i data-lucide="megaphone"></i><span>Announcement</span></a>
      <a href="metrics.php" class="nav-item"><i data-lucide="bar-chart-3"></i><span>Metrics</span></a>
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
        <h1>Payment</h1>
        <p>Process and track member payments</p>
      </div>
      <div class="topbar__actions">
        <div class="search-bar">
          <i data-lucide="search"></i>
          <input type="text" placeholder="Search member..."/>
        </div>
      </div>
    </header>

    <div class="pay-grid">

      <!-- TRANSACTION FORM -->
      <div class="card pay-form-card">
        <div class="card__header">
          <h2>New Transaction</h2>
        </div>

        <form method="POST" action="actions/add-payment.php">
        <!-- Member select -->
        <div class="form-group">
          <label>Select Member</label>
          <select name="member_id" class="form-control" required>
            <option value="">-- Select Member --</option>
            <?php 
            while ($member = mysqli_fetch_assoc($members_result)) {
                echo "<option value='" . $member['id'] . "'>" . 
                     htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) . 
                     " (" . $member['member_id_code'] . ")</option>";
            }
            ?>
          </select>
        </div>

        <!-- Transaction -->
        <div class="form-group">
          <label>Category</label>
          <select name="category" class="form-control" required>
            <option>Membership</option>
            <option>Supplements</option>
            <option>Merchandise</option>
          </select>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" value="1" min="1" required/>
          </div>
          <div class="form-group">
            <label>Amount (₱)</label>
            <input type="number" name="amount" class="form-control" id="payAmount" placeholder="0.00" step="0.01" required/>
          </div>
        </div>

        <!-- Payment method -->
        <div class="pay-method-label">Select Payment Method</div>
        <div class="pay-methods">
          <button type="button" class="pay-method-btn" onclick="selectMethod(this,'gcash')">
            <span class="pay-icon pay-icon--gcash">G</span>
            GCash
          </button>
          <button type="button" class="pay-method-btn active" onclick="selectMethod(this,'cash')">
            <i data-lucide="banknote"></i>
            Cash
          </button>
        </div>
        <input type="hidden" name="payment_method" id="paymentMethod" value="Cash"/>
          
        </div>

        <!-- GCash QR (shown when GCash selected) -->
        <div class="gcash-qr" id="gcashQR" style="display:none;">
          <p class="qr-label">Scan QR Code</p>
          <div class="qr-frame-sm">
            <svg viewBox="0 0 80 80" width="120" height="120">
              <rect x="5"  y="5"  width="22" height="22" rx="3" fill="none" stroke="#c8f53a" stroke-width="2"/>
              <rect x="53" y="5"  width="22" height="22" rx="3" fill="none" stroke="#c8f53a" stroke-width="2"/>
              <rect x="5"  y="53" width="22" height="22" rx="3" fill="none" stroke="#c8f53a" stroke-width="2"/>
              <rect x="10" y="10" width="12" height="12" rx="1" fill="#c8f53a"/>
              <rect x="58" y="10" width="12" height="12" rx="1" fill="#c8f53a"/>
              <rect x="10" y="58" width="12" height="12" rx="1" fill="#c8f53a"/>
              <rect x="34" y="5"  width="4" height="4" fill="#c8f53a"/>
              <rect x="40" y="11" width="4" height="4" fill="#c8f53a"/>
              <rect x="34" y="17" width="4" height="4" fill="#c8f53a"/>
              <rect x="34" y="34" width="4" height="4" fill="#c8f53a"/>
              <rect x="40" y="40" width="4" height="4" fill="#c8f53a"/>
              <rect x="46" y="34" width="4" height="4" fill="#c8f53a"/>
              <rect x="52" y="40" width="4" height="4" fill="#c8f53a"/>
              <rect x="58" y="34" width="4" height="4" fill="#c8f53a"/>
              <rect x="64" y="40" width="4" height="4" fill="#c8f53a"/>
              <rect x="34" y="52" width="4" height="4" fill="#c8f53a"/>
              <rect x="46" y="58" width="4" height="4" fill="#c8f53a"/>
              <rect x="58" y="52" width="4" height="4" fill="#c8f53a"/>
              <rect x="64" y="64" width="4" height="4" fill="#c8f53a"/>
            </svg>
          </div>
          <p class="qr-acct">John Power Cashless</p>
          <p class="qr-num">09121258969</p>
        </div>

        <button type="submit" class="btn btn--primary pay-submit-btn">
          <i data-lucide="check-circle"></i>
          Make Payment
        </button>
        </form>
      </div>

      <!-- TRANSACTION HISTORY -->
      <div class="card pay-history-card">
        <div class="card__header">
          <h2>Transaction History</h2>
          <div class="pay-totals">
            <span class="pay-total-label">Total</span>
            <span class="pay-total-val">₱ <?php echo number_format($total_revenue, 2); ?></span>
          </div>
        </div>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Member</th>
                <th>Date</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              if (mysqli_num_rows($payments_result) > 0) {
                  while ($payment = mysqli_fetch_assoc($payments_result)) {
                      $status_class = strtolower($payment['status']) == 'paid' ? 'status--active' : 'status--expired';
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) . "</td>";
                      echo "<td>" . date('Y-m-d', strtotime($payment['payment_date'])) . "</td>";
                      echo "<td>" . htmlspecialchars($payment['category']) . "</td>";
                      echo "<td>₱ " . number_format($payment['amount'], 2) . "</td>";
                      echo "<td>" . htmlspecialchars($payment['payment_method']) . "</td>";
                      echo "<td><span class='status $status_class'>" . htmlspecialchars($payment['status']) . "</span></td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='6' style='text-align:center;color:var(--text-muted);'>No transactions yet</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div><!-- /pay-grid -->

    <!-- PAYMENT SUCCESS MODAL (removed, using redirect instead) -->

  </main>

  <script>
    let activeMethod = 'cash';

    function selectMethod(btn, method) {
      document.querySelectorAll('.pay-method-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeMethod = method;
      document.getElementById('paymentMethod').value = method === 'gcash' ? 'GCash' : 'Cash';
      document.getElementById('gcashQR').style.display = method === 'gcash' ? 'flex' : 'none';
      lucide.createIcons();
    }
  </script>

  <?php
  // Show success/error messages
  if (isset($_GET['success'])) {
      $success_msg = htmlspecialchars($_GET['success']);
      echo "<script>
          alert('Success: " . addslashes($success_msg) . "');
      </script>";
  }
  if (isset($_GET['error'])) {
      $error_msg = htmlspecialchars($_GET['error']);
      echo "<script>
          alert('Error: " . addslashes($error_msg) . "');
      </script>";
  }
  ?>
</body>
</html>
