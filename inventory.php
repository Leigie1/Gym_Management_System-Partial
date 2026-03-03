<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

$inventory_query = "SELECT * FROM inventory ORDER BY category, item_name";
$inventory_result = mysqli_query($conn, $inventory_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory — Power Fitness Gym</title>
  <link rel="stylesheet" href="assets/css/global.css"/>
  <link rel="stylesheet" href="assets/css/inventory.css"/>
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
      <a href="inventory.php" class="nav-item active"><i data-lucide="package"></i><span>Inventory</span></a>
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
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <div class="topbar__title">
        <h1>Inventory</h1>
        <p>Manage gym products and stock levels</p>
      </div>
      <div class="topbar__actions">
      </div>
    </header>

    <div class="card">
      <div class="card__header">
        <h2>Add New Item</h2>
      </div>
      <form method="POST" action="actions/add-inventory.php" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label>Item Name</label>
          <input type="text" name="item_name" class="form-control" required/>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category" class="form-control" required>
            <option>Equipment</option>
            <option>Supplements</option>
            <option>Merchandise</option>
          </select>
        </div>
        <div class="form-group">
          <label>Quantity</label>
          <input type="number" name="quantity" class="form-control" required/>
        </div>
        <div class="form-group">
          <label>Price (₱)</label>
          <input type="number" name="price" class="form-control" step="0.01" required/>
        </div>
        <button type="submit" class="btn btn--primary" style="grid-column:1/-1;"><i data-lucide="plus"></i> Add Item</button>
      </form>
    </div>

    <div class="card" style="margin-top:20px;">
      <div class="card__header"><h2>Inventory List</h2></div>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Item Name</th><th>Category</th><th>Quantity</th><th>Price</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php 
            if (mysqli_num_rows($inventory_result) > 0) {
                while ($item = mysqli_fetch_assoc($inventory_result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['item_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['category']) . "</td>";
                    echo "<td>" . $item['quantity'] . "</td>";
                    echo "<td>₱ " . number_format($item['price'], 2) . "</td>";
                    echo "<td>";
                    echo "<form method='POST' action='actions/delete-inventory.php' style='display:inline;' onsubmit='return confirm(\"Delete this item?\")'>";
                    echo "<input type='hidden' name='item_id' value='" . $item['id'] . "'/>";
                    echo "<button type='submit' class='btn btn--danger btn--sm'>Delete</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;color:var(--text-muted);'>No items in inventory</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
