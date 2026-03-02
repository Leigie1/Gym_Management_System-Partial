<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get today's attendance
$attendance_query = "SELECT m.first_name, m.last_name, m.address, a.check_in_date, a.check_in_time, m.status 
                     FROM attendance a 
                     JOIN members m ON a.member_id = m.id 
                     WHERE a.check_in_date = CURDATE() 
                     ORDER BY a.check_in_time DESC";
$attendance_result = mysqli_query($conn, $attendance_query);
$attendance_count = mysqli_num_rows($attendance_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Attendance — Power Fitness Gym</title>
  <link rel="stylesheet" href="assets/css/global.css"/>
  <link rel="stylesheet" href="assets/css/attendance.css"/>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
  <script src="https://unpkg.com/html5-qrcode"></script>
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
      <a href="attendance.php" class="nav-item active"><i data-lucide="scan-line"></i><span>Attendance</span></a>
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

  <main class="main">

    <header class="topbar">
      <div class="topbar__title">
        <h1>Attendance</h1>
        <p>Track member check-ins in real time</p>
      </div>
      <div class="topbar__actions">
        <div class="search-bar">
          <i data-lucide="search"></i>
          <input type="text" placeholder="Search members..."/>
        </div>
      </div>
    </header>

    <div class="att-grid">

      <!-- SCAN PANEL -->
      <div class="card scan-card">
        <div class="card__header">
          <h2>Check In</h2>
          <span class="chip">Live</span>
        </div>

        <div class="scan-methods">
          <button class="scan-btn active" id="btnQR">
            <i data-lucide="qr-code"></i>
            Scan QR
          </button>
          <button class="scan-btn" id="btnID">
            <i data-lucide="keyboard"></i>
            Enter ID
          </button>
        </div>

        <!-- QR Scanner Placeholder -->
        <div class="qr-area" id="qrArea">
          <div class="qr-frame">
            <svg viewBox="0 0 120 120" class="qr-svg">
              <rect x="10" y="10" width="30" height="30" rx="4" fill="none" stroke="#c8f53a" stroke-width="3"/>
              <rect x="80" y="10" width="30" height="30" rx="4" fill="none" stroke="#c8f53a" stroke-width="3"/>
              <rect x="10" y="80" width="30" height="30" rx="4" fill="none" stroke="#c8f53a" stroke-width="3"/>
              <rect x="17" y="17" width="16" height="16" rx="2" fill="#c8f53a"/>
              <rect x="87" y="17" width="16" height="16" rx="2" fill="#c8f53a"/>
              <rect x="17" y="87" width="16" height="16" rx="2" fill="#c8f53a"/>
              <rect x="50" y="10" width="5" height="5" fill="#c8f53a"/>
              <rect x="58" y="10" width="5" height="5" fill="#c8f53a"/>
              <rect x="50" y="18" width="5" height="5" fill="#c8f53a"/>
              <rect x="58" y="26" width="5" height="5" fill="#c8f53a"/>
              <rect x="50" y="34" width="5" height="5" fill="#c8f53a"/>
              <rect x="58" y="42" width="5" height="5" fill="#c8f53a"/>
              <rect x="50" y="50" width="5" height="5" fill="#c8f53a"/>
              <rect x="58" y="50" width="5" height="5" fill="#c8f53a"/>
              <rect x="66" y="50" width="5" height="5" fill="#c8f53a"/>
              <rect x="74" y="50" width="5" height="5" fill="#c8f53a"/>
              <rect x="50" y="58" width="5" height="5" fill="#c8f53a"/>
              <rect x="66" y="58" width="5" height="5" fill="#c8f53a"/>
              <rect x="74" y="58" width="5" height="5" fill="#c8f53a"/>
              <rect x="50" y="66" width="5" height="5" fill="#c8f53a"/>
              <rect x="58" y="66" width="5" height="5" fill="#c8f53a"/>
              <rect x="66" y="74" width="5" height="5" fill="#c8f53a"/>
              <rect x="74" y="74" width="5" height="5" fill="#c8f53a"/>
              <rect x="50" y="82" width="5" height="5" fill="#c8f53a"/>
              <rect x="50" y="90" width="5" height="5" fill="#c8f53a"/>
              <rect x="58" y="90" width="5" height="5" fill="#c8f53a"/>
              <rect x="66" y="90" width="5" height="5" fill="#c8f53a"/>
              <rect x="74" y="90" width="5" height="5" fill="#c8f53a"/>
              <rect x="82" y="50" width="5" height="5" fill="#c8f53a"/>
              <rect x="90" y="50" width="5" height="5" fill="#c8f53a"/>
              <rect x="98" y="50" width="5" height="5" fill="#c8f53a"/>
              <rect x="82" y="58" width="5" height="5" fill="#c8f53a"/>
              <rect x="90" y="66" width="5" height="5" fill="#c8f53a"/>
              <rect x="98" y="66" width="5" height="5" fill="#c8f53a"/>
              <rect x="82" y="74" width="5" height="5" fill="#c8f53a"/>
              <rect x="90" y="82" width="5" height="5" fill="#c8f53a"/>
              <rect x="98" y="82" width="5" height="5" fill="#c8f53a"/>
            </svg>
            <div class="scan-line"></div>
          </div>
          <p class="scan-hint">Position the QR code within the frame</p>
        </div>

        <!-- ID Entry (hidden by default) -->
        <div class="id-area hidden" id="idArea">
          <div class="form-group">
            <label>Member ID</label>
            <input type="text" id="manualMemberId" class="form-control id-input" placeholder="Enter Member ID..."/>
          </div>
          <button class="btn btn--primary" style="width:100%;justify-content:center;" onclick="manualCheckin()">
            <i data-lucide="log-in"></i> Check In
          </button>
        </div>

        <!-- Success toast -->
        <div class="checkin-toast hidden" id="checkinToast">
          <i data-lucide="check-circle-2"></i>
          <span>Check-in Successful!</span>
        </div>
      </div>

      <!-- ATTENDANCE TABLE -->
      <div class="card att-table-card">
        <div class="card__header">
          <h2>Today's Attendance</h2>
          <div class="att-stats">
            <span class="att-count" id="attCount"><?php echo $attendance_count; ?></span>
            <span style="color:var(--text-muted);font-size:12px;">checked in</span>
          </div>
        </div>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Date &amp; Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="attTableBody">
              <?php 
              if ($attendance_count > 0) {
                  while ($row = mysqli_fetch_assoc($attendance_result)) {
                      $status_class = strtolower($row['status']) == 'active' ? 'status--active' : 'status--expired';
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                      echo "<td>" . date('Y-m-d', strtotime($row['check_in_date'])) . " &nbsp; " . format_time($row['check_in_time']) . "</td>";
                      echo "<td><span class='status $status_class'>" . htmlspecialchars($row['status']) . "</span></td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='4' style='text-align:center;color:var(--text-muted);'>No check-ins today</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div><!-- /att-grid -->
  </main>

  <script>
    // Initialize Lucide icons when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });

    const btnQR   = document.getElementById('btnQR');
    const btnID   = document.getElementById('btnID');
    const qrArea  = document.getElementById('qrArea');
    const idArea  = document.getElementById('idArea');
    let html5QrCode = null;
    let isScanning = false;

    btnQR.addEventListener('click', () => {
      btnQR.classList.add('active'); btnID.classList.remove('active');
      qrArea.classList.remove('hidden'); idArea.classList.add('hidden');
      startQRScanner();
    });
    
    btnID.addEventListener('click', () => {
      btnID.classList.add('active'); btnQR.classList.remove('active');
      idArea.classList.remove('hidden'); qrArea.classList.add('hidden');
      stopQRScanner();
    });

    function startQRScanner() {
      if (isScanning) return;
      
      html5QrCode = new Html5Qrcode("qrArea");
      
      html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 180, height: 180 } },
        (decodedText) => {
          checkInMember(decodedText);
          isScanning = false;
        },
        (errorMessage) => {
          // Scanning (ignore errors)
        }
      ).then(() => {
        isScanning = true;
      }).catch(err => {
        console.error("Camera error:", err);
        alert("Unable to access camera. Please check permissions or use Manual ID entry.");
      });
    }

    function stopQRScanner() {
      if (html5QrCode && isScanning) {
        html5QrCode.stop().then(() => {
          isScanning = false;
        }).catch(err => console.error(err));
      }
    }

    function manualCheckin() {
      const memberId = document.getElementById('manualMemberId').value.trim();
      if (!memberId) {
        alert('Please enter a Member ID');
        return;
      }
      checkInMember(memberId);
    }

    function checkInMember(memberId) {
      fetch('api/check-in.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'member_id=' + encodeURIComponent(memberId)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast('Check-in Successful!');
          addAttendanceRow(data.member);
          document.getElementById('manualMemberId').value = '';
          
          const countEl = document.getElementById('attCount');
          countEl.textContent = parseInt(countEl.textContent) + 1;
        } else {
          alert(data.message || 'Check-in failed');
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error processing check-in');
      });
    }

    function addAttendanceRow(member) {
      const tbody = document.getElementById('attTableBody');
      const now = new Date();
      const time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
      const date = now.toISOString().split('T')[0];
      
      const statusClass = member.status.toLowerCase() === 'active' ? 'status--active' : 'status--expired';
      
      const row = `<tr>
        <td>${member.name}</td>
        <td>${member.address}</td>
        <td>${date} &nbsp; ${time}</td>
        <td><span class="status ${statusClass}">${member.status}</span></td>
      </tr>`;
      
      tbody.insertAdjacentHTML('afterbegin', row);
    }

    function showToast(message) {
      const toast = document.getElementById('checkinToast');
      toast.classList.remove('hidden');
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.classList.add('hidden'), 400);
      }, 2800);
    }
  </script>
</body>
</html>
