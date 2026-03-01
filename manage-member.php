<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get all members
$members_query = "SELECT * FROM members ORDER BY created_at DESC";
$members_result = mysqli_query($conn, $members_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Member — Power Fitness Gym</title>
  <link rel="stylesheet" href="assets/css/global.css"/>
  <link rel="stylesheet" href="assets/css/manage-member.css"/>
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
      <a href="dashboard.php" class="nav-item"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></a>
      <a href="manage-member.php" class="nav-item active"><i data-lucide="users"></i><span>Manage Member</span></a>
      <a href="attendance.php" class="nav-item"><i data-lucide="scan-line"></i><span>Attendance</span></a>
      <a href="inventory.php" class="nav-item"><i data-lucide="package"></i><span>Inventory</span></a>
      <a href="member-status.php" class="nav-item"><i data-lucide="shield-check"></i><span>Member Status</span></a>
      <a href="payment.php" class="nav-item"><i data-lucide="credit-card"></i><span>Payment</span></a>
      <a href="announcement.php" class="nav-item"><i data-lucide="megaphone"></i><span>Announcement</span></a>    </nav>
    <div class="sidebar__footer">
      <div class="sidebar__user">
        <div class="avatar">JD</div>
        <div class="user-info"><span class="user-name">John Doe</span><span class="user-role">Admin</span></div>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <!-- TOP BAR -->
    <header class="topbar">
      <div class="topbar__title">
        <h1>Manage Member</h1>
        <p>Register and manage gym memberships</p>
      </div>
      <div class="topbar__actions">
        <div class="search-bar">
          <i data-lucide="search"></i>
          <input type="text" placeholder="Search members..."/>
        </div>
        <button class="icon-btn"><i data-lucide="bell"></i><span class="badge">3</span></button>
      </div>
    </header>

    <!-- CONTENT GRID -->
    <div class="mm-grid">

      <!-- REGISTRATION FORM -->
      <div class="card reg-form-card">
        <div class="card__header">
          <h2>Membership Registration</h2>
          <span class="chip">New</span>
        </div>

        <form method="POST" action="actions/add-member.php">
        <div class="form-row">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" placeholder="e.g. James" required/>
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" placeholder="e.g. Carter" required/>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" placeholder="Street, City" required/>
          </div>
          <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
              <option value="">Select</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" placeholder="09XX XXX XXXX" required/>
          </div>
          <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" required/>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Plan</label>
            <select name="plan" class="form-control" required>
              <option value="Membership">Membership</option>
              <option value="Supplements">Supplements</option>
              <option value="Merchandise">Merchandise</option>
            </select>
          </div>
          <div class="form-group">
            <label>Duration</label>
            <select name="duration" class="form-control" required>
              <option value="1 Month">1 Month</option>
              <option value="3 Months">3 Months</option>
              <option value="6 Months">6 Months</option>
              <option value="1 Year">1 Year</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Amount</label>
            <input type="number" name="amount" class="form-control" placeholder="₱ 0.00" step="0.01" required/>
          </div>
          <div class="form-group">
            <label>Date Enrolled</label>
            <input type="date" name="date_enrolled" class="form-control" value="<?php echo date('Y-m-d'); ?>" required/>
          </div>
        </div>

        <button type="submit" class="btn btn--primary btn-add">
          <i data-lucide="user-plus"></i>
          Add Member
        </button>
        </form>
      </div>

      <!-- MEMBER LIST -->
      <div class="card member-list-card">
        <div class="card__header">
          <h2>Member's List</h2>
        </div>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Date Enrolled</th>
                <th>Amount</th>
                <th>Inventory</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              if (mysqli_num_rows($members_result) > 0) {
                  while ($member = mysqli_fetch_assoc($members_result)) {
                      $status_class = 'status--' . strtolower($member['status']);
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) . "</td>";
                      echo "<td>" . htmlspecialchars($member['address']) . "</td>";
                      echo "<td>" . date('Y-m-d', strtotime($member['date_enrolled'])) . "</td>";
                      echo "<td>₱" . number_format($member['amount'], 2) . "</td>";
                      echo "<td>" . htmlspecialchars($member['plan']) . "</td>";
                      echo "<td><span class='status $status_class'>" . htmlspecialchars($member['status']) . "</span></td>";
                      echo "<td>";
                      echo "<form method='POST' action='actions/delete-member.php' style='display:inline;' onsubmit='return confirm(\"Delete this member?\")'>";
                      echo "<input type='hidden' name='member_id' value='" . $member['id'] . "'/>";
                      echo "<button type='submit' class='btn btn--danger btn--sm'>Delete</button>";
                      echo "</form>";
                      echo "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='7' style='text-align:center;color:var(--text-muted);'>No members found</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- Success toast (hidden by default, shown via JS) -->
        <div class="toast toast--success" id="successToast">
          <i data-lucide="check-circle"></i>
          <span id="toastMsg">Member registered successfully!</span>
        </div>
      </div>

    </div><!-- /mm-grid -->
  </main>

  <?php
  // Show success/error messages
  if (isset($_GET['success'])) {
      echo "<script>
          document.addEventListener('DOMContentLoaded', function() {
              const toast = document.getElementById('successToast');
              document.getElementById('toastMsg').textContent = '" . htmlspecialchars($_GET['success']) . "';
              toast.classList.add('show');
              setTimeout(() => toast.classList.remove('show'), 3000);
              lucide.createIcons();
          });
      </script>";
  }
  ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() { if (typeof lucide !== 'undefined') { lucide.createIcons(); } });
  </script>
</body>
</html>
