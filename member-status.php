<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/qr-generator.php';

// Get all members
$members_query = "SELECT * FROM members ORDER BY first_name ASC";
$members_result = mysqli_query($conn, $members_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Member Status — Power Fitness Gym</title>
  <link rel="stylesheet" href="assets/css/global.css"/>
  <link rel="stylesheet" href="assets/css/member-status.css"/>
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
      <a href="member-status.php" class="nav-item active"><i data-lucide="shield-check"></i><span>Member Status</span></a>
      <a href="payment.php" class="nav-item"><i data-lucide="credit-card"></i><span>Payment</span></a>
      <a href="announcement.php" class="nav-item"><i data-lucide="megaphone"></i><span>Announcement</span></a>    </nav>
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
        <h1>Member Status</h1>
        <p>View and manage individual member details</p>
      </div>
      <div class="topbar__actions">
        <div class="search-bar">
          <i data-lucide="search"></i>
          <input type="text" placeholder="Search by name..." id="msSearch" oninput="filterMembers()"/>
        </div>
      </div>
    </header>

    <div class="ms-grid">

      <!-- MEMBER LIST -->
      <div class="card ms-list-card">
        <div class="card__header">
          <h2>All Members</h2>
          <div class="ms-filter-wrap">
            <select class="form-control" style="width:auto;padding:6px 12px;font-size:12px;" onchange="filterByStatus(this.value)">
              <option value="">All</option>
              <option value="Active">Active</option>
              <option value="Expired">Expired</option>
              <option value="Pending">Pending</option>
            </select>
          </div>
        </div>

        <div class="table-wrap">
          <table class="data-table" id="memberTable">
            <thead>
              <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Date Joined</th>
                <th>Expiry</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              if (mysqli_num_rows($members_result) > 0) {
                  while ($member = mysqli_fetch_assoc($members_result)) {
                      // Update status based on expiry
                      $current_status = check_member_status($member['date_expiry']);
                      if ($current_status != $member['status']) {
                          mysqli_query($conn, "UPDATE members SET status='$current_status' WHERE id=" . $member['id']);
                          $member['status'] = $current_status;
                      }
                      
                      $status_class = 'status--' . strtolower($member['status']);
                      $qr_path = qr_exists($member['member_id_code']) ? get_qr_path($member['member_id_code']) : '';
                      echo "<tr onclick='showProfile(this)' class='clickable-row' 
                            data-id='" . $member['id'] . "'
                            data-name='" . htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) . "'
                            data-gender='" . htmlspecialchars($member['gender']) . "'
                            data-dob='" . date('m/d/Y', strtotime($member['date_of_birth'])) . "'
                            data-duration='" . htmlspecialchars($member['duration']) . "'
                            data-email='member@email.com'
                            data-phone='" . htmlspecialchars($member['phone']) . "'
                            data-status='" . htmlspecialchars($member['status']) . "'
                            data-memberid='" . htmlspecialchars($member['member_id_code']) . "'
                            data-qrpath='" . htmlspecialchars($qr_path) . "'
                            data-initials='" . strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) . "'>";
                      echo "<td>" . htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) . "</td>";
                      echo "<td>" . htmlspecialchars($member['address']) . "</td>";
                      echo "<td>" . date('Y-m-d', strtotime($member['date_enrolled'])) . "</td>";
                      echo "<td>" . date('Y-m-d', strtotime($member['date_expiry'])) . "</td>";
                      echo "<td><span class='status $status_class'>" . htmlspecialchars($member['status']) . "</span></td>";
                      echo "<td><button class='btn btn--outline btn--sm'>View</button></td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='6' style='text-align:center;color:var(--text-muted);'>No members found</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="ms-remove-toast hidden" id="removeToast">
          <i data-lucide="user-x"></i> Member removed successfully!
        </div>
      </div>

      <!-- PROFILE PANEL -->
      <div class="ms-profile-panel" id="profilePanel">

        <!-- Personal Info -->
        <div class="card profile-card">
          <div class="card__header">
            <h2>Personal Info</h2>
            <div style="display:flex;gap:8px;">
              <button class="btn btn--outline btn--sm"><i data-lucide="pencil"></i> Update</button>
              <button class="btn btn--outline btn--sm"><i data-lucide="printer"></i> Print ID</button>
            </div>
          </div>
          <div class="profile-body">
            <div class="profile-avatar-lg">JN</div>
            <div class="profile-details">
              <div class="profile-row">
                <div class="profile-field"><span class="pf-label">Name</span><span class="pf-val" id="pName">Justin Nabunturan</span></div>
                <div class="profile-field"><span class="pf-label">Gender</span><span class="pf-val">Male</span></div>
              </div>
              <div class="profile-row">
                <div class="profile-field"><span class="pf-label">Date of Birth</span><span class="pf-val">04/10/2025</span></div>
                <div class="profile-field"><span class="pf-label">Duration</span><span class="pf-val">1 Year</span></div>
              </div>
              <div class="profile-field">
                <span class="pf-label">Status</span>
                <span class="status status--active">Active</span>
              </div>
              <div class="profile-row">
                <div class="profile-field"><span class="pf-label">Email</span><span class="pf-val">JustineNabunturan@gmail.com</span></div>
                <div class="profile-field"><span class="pf-label">Phone Number</span><span class="pf-val">0909060319</span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Membership Card -->
        <div class="card membership-card-display">
          <div class="card__header">
            <h2>Membership Card</h2>
          </div>
          <div class="mem-card">
            <div class="mem-card__left">
              <div class="mem-logo">
                <span class="logo-dot" style="width:22px;height:22px;"></span>
                <span style="font-family:var(--font-display);font-size:11px;line-height:1.1;">POWER<br><em style="font-style:normal;color:var(--accent);font-size:9px;">FITNESS GYM</em></span>
              </div>
              <div class="mem-avatar">JN</div>
              <div>
                <p class="mem-field-label">Name</p>
                <p class="mem-field-val">Justine Nabunturan</p>
              </div>
              <div>
                <p class="mem-field-label">Status</p>
                <span class="status status--active">Active</span>
              </div>
              <div>
                <p class="mem-field-label">Member ID</p>
                <p class="mem-field-val">00915</p>
              </div>
            </div>
            <div class="mem-card__right">
              <p class="mem-field-label" style="text-align:center;margin-bottom:6px;">QR CODE</p>
              <div class="qr-placeholder" id="qrDisplay">
                <svg viewBox="0 0 80 80" width="100" height="100">
                  <rect x="5"  y="5"  width="22" height="22" rx="3" fill="none" stroke="#c8f53a" stroke-width="2"/>
                  <rect x="53" y="5"  width="22" height="22" rx="3" fill="none" stroke="#c8f53a" stroke-width="2"/>
                  <rect x="5"  y="53" width="22" height="22" rx="3" fill="none" stroke="#c8f53a" stroke-width="2"/>
                  <rect x="10" y="10" width="12" height="12" rx="1" fill="#c8f53a"/>
                  <rect x="58" y="10" width="12" height="12" rx="1" fill="#c8f53a"/>
                  <rect x="10" y="58" width="12" height="12" rx="1" fill="#c8f53a"/>
                  <rect x="34" y="5"  width="4" height="4" fill="#c8f53a"/>
                  <rect x="40" y="5"  width="4" height="4" fill="#c8f53a"/>
                  <rect x="34" y="11" width="4" height="4" fill="#c8f53a"/>
                  <rect x="40" y="17" width="4" height="4" fill="#c8f53a"/>
                  <rect x="34" y="23" width="4" height="4" fill="#c8f53a"/>
                  <rect x="34" y="34" width="4" height="4" fill="#c8f53a"/>
                  <rect x="40" y="34" width="4" height="4" fill="#c8f53a"/>
                  <rect x="46" y="34" width="4" height="4" fill="#c8f53a"/>
                  <rect x="52" y="34" width="4" height="4" fill="#c8f53a"/>
                  <rect x="58" y="34" width="4" height="4" fill="#c8f53a"/>
                  <rect x="64" y="34" width="4" height="4" fill="#c8f53a"/>
                  <rect x="34" y="40" width="4" height="4" fill="#c8f53a"/>
                  <rect x="46" y="40" width="4" height="4" fill="#c8f53a"/>
                  <rect x="58" y="40" width="4" height="4" fill="#c8f53a"/>
                  <rect x="34" y="46" width="4" height="4" fill="#c8f53a"/>
                  <rect x="46" y="46" width="4" height="4" fill="#c8f53a"/>
                  <rect x="58" y="52" width="4" height="4" fill="#c8f53a"/>
                  <rect x="64" y="52" width="4" height="4" fill="#c8f53a"/>
                  <rect x="34" y="58" width="4" height="4" fill="#c8f53a"/>
                  <rect x="40" y="58" width="4" height="4" fill="#c8f53a"/>
                  <rect x="46" y="58" width="4" height="4" fill="#c8f53a"/>
                  <rect x="58" y="58" width="4" height="4" fill="#c8f53a"/>
                  <rect x="34" y="64" width="4" height="4" fill="#c8f53a"/>
                  <rect x="52" y="64" width="4" height="4" fill="#c8f53a"/>
                  <rect x="64" y="64" width="4" height="4" fill="#c8f53a"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /profile panel -->

    </div><!-- /ms-grid -->
  </main>

  <script>
    function showProfile(row) {
      document.querySelectorAll('.clickable-row').forEach(r => r.classList.remove('selected'));
      row.classList.add('selected');
      
      // Get data from row
      const name = row.dataset.name;
      const gender = row.dataset.gender;
      const dob = row.dataset.dob;
      const duration = row.dataset.duration;
      const email = row.dataset.email;
      const phone = row.dataset.phone;
      const status = row.dataset.status;
      const memberId = row.dataset.memberid;
      const initials = row.dataset.initials;
      const qrPath = row.dataset.qrpath;
      
      // Update profile panel
      document.getElementById('pName').textContent = name;
      document.querySelector('.profile-avatar-lg').textContent = initials;
      document.querySelectorAll('.mem-avatar').forEach(el => el.textContent = initials);
      
      // Update all fields
      const fields = document.querySelectorAll('.pf-val');
      fields[0].textContent = name;
      fields[1].textContent = gender;
      fields[2].textContent = dob;
      fields[3].textContent = duration;
      fields[4].textContent = email;
      fields[5].textContent = phone;
      
      // Update status
      const statusEl = document.querySelector('.profile-details .status');
      statusEl.className = 'status status--' + status.toLowerCase();
      statusEl.textContent = status;
      
      // Update membership card
      document.querySelectorAll('.mem-field-val')[0].textContent = name;
      document.querySelectorAll('.mem-field-val')[1].textContent = memberId;
      
      const cardStatus = document.querySelector('.mem-card .status');
      cardStatus.className = 'status status--' + status.toLowerCase();
      cardStatus.textContent = status;
      
      // Update QR code display
      const qrDisplay = document.getElementById('qrDisplay');
      if (qrPath) {
        qrDisplay.innerHTML = '<img src="' + qrPath + '" alt="QR Code" style="width:120px;height:120px;border-radius:8px;">';
      } else {
        qrDisplay.innerHTML = '<p style="color:var(--text-muted);font-size:12px;text-align:center;">QR not generated</p>';
      }
      
      document.getElementById('profilePanel').classList.add('visible');
      lucide.createIcons();
    }
    
    function filterMembers() {
      const q = document.getElementById('msSearch').value.toLowerCase();
      document.querySelectorAll('#memberTable tbody tr').forEach(row => {
        row.style.display = row.cells[0].textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    }
    
    function filterByStatus(val) {
      document.querySelectorAll('#memberTable tbody tr').forEach(row => {
        const s = row.querySelector('.status')?.textContent || '';
        row.style.display = (!val || s === val) ? '' : 'none';
      });
    }
  </script>
</body>
</html>
