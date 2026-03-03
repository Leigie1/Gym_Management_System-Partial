# Business Metrics Table - Quick Guide

## 📊 What is the business_metrics Table?

A new table that automatically tracks key business statistics in real-time.

---

## 🗄️ Table Structure

```sql
CREATE TABLE business_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    metric_date DATE UNIQUE NOT NULL,
    daily_revenue DECIMAL(10,2) DEFAULT 0,
    monthly_revenue DECIMAL(10,2) DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0,
    daily_transactions INT DEFAULT 0,
    monthly_transactions INT DEFAULT 0,
    daily_checkins INT DEFAULT 0,
    monthly_checkins INT DEFAULT 0,
    total_members INT DEFAULT 0,
    active_members INT DEFAULT 0,
    last_updated TIMESTAMP,
    created_at TIMESTAMP
);
```

---

## 📈 What Gets Tracked?

### Revenue Metrics:
- `daily_revenue` - Total revenue for today
- `monthly_revenue` - Total revenue for current month
- `total_revenue` - All-time total revenue
- `daily_transactions` - Number of transactions today
- `monthly_transactions` - Number of transactions this month

### Attendance Metrics:
- `daily_checkins` - Total check-ins for today
- `monthly_checkins` - Total check-ins for current month

### Member Metrics:
- `total_members` - Total count of all members
- `active_members` - Count of active members only

---

## 🔄 How It Updates

### Automatically via Triggers:

1. **When payment is added** → Updates revenue metrics
2. **When member checks in** → Updates attendance metrics
3. **When member is added** → Updates member count metrics

### No Manual Work Needed!

---

## 💡 How to Use

### Example 1: Get Today's Metrics
```php
$query = "SELECT * FROM business_metrics WHERE metric_date = CURDATE()";
$result = mysqli_query($conn, $query);
$metrics = mysqli_fetch_assoc($result);

echo "Today's Revenue: ₱" . number_format($metrics['daily_revenue'], 2);
echo "Today's Check-ins: " . $metrics['daily_checkins'];
echo "Total Members: " . $metrics['total_members'];
```

### Example 2: Display on Dashboard
```php
// Get today's metrics
$metrics_query = "SELECT * FROM business_metrics WHERE metric_date = CURDATE()";
$metrics_result = mysqli_query($conn, $metrics_query);
$metrics = mysqli_fetch_assoc($metrics_result);

// If no metrics for today, show zeros
if (!$metrics) {
    $metrics = [
        'daily_revenue' => 0,
        'monthly_revenue' => 0,
        'daily_checkins' => 0,
        'monthly_checkins' => 0,
        'total_members' => 0,
        'active_members' => 0
    ];
}
?>

<div class="stats">
    <div class="stat-card">
        <h3>Today's Revenue</h3>
        <p>₱<?php echo number_format($metrics['daily_revenue'], 2); ?></p>
    </div>
    
    <div class="stat-card">
        <h3>Today's Check-ins</h3>
        <p><?php echo $metrics['daily_checkins']; ?></p>
    </div>
    
    <div class="stat-card">
        <h3>Total Members</h3>
        <p><?php echo $metrics['total_members']; ?></p>
    </div>
</div>
```

### Example 3: Get Historical Data
```php
// Get last 7 days of revenue
$query = "SELECT metric_date, daily_revenue, daily_checkins 
          FROM business_metrics 
          WHERE metric_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
          ORDER BY metric_date DESC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['metric_date'] . ": ₱" . $row['daily_revenue'] . "<br>";
}
```

---

## 🎯 Benefits

### For Dashboard:
- ✅ Real-time statistics
- ✅ No complex calculations needed
- ✅ Fast query performance
- ✅ Historical data available

### For Reports:
- ✅ Easy to generate charts
- ✅ Compare day-to-day performance
- ✅ Track trends over time
- ✅ Export data easily

### For Business:
- ✅ Instant insights
- ✅ Accurate tracking
- ✅ No manual updates
- ✅ Data consistency

---

## 🔍 Sample Queries

### Get This Month's Performance
```sql
SELECT 
    SUM(daily_revenue) as month_total,
    AVG(daily_revenue) as daily_average,
    SUM(daily_checkins) as total_checkins
FROM business_metrics
WHERE MONTH(metric_date) = MONTH(CURDATE())
AND YEAR(metric_date) = YEAR(CURDATE());
```

### Compare This Month vs Last Month
```sql
SELECT 
    MONTH(metric_date) as month,
    SUM(daily_revenue) as total_revenue,
    SUM(daily_checkins) as total_checkins
FROM business_metrics
WHERE metric_date >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
GROUP BY MONTH(metric_date);
```

### Get Best Performing Days
```sql
SELECT 
    metric_date,
    daily_revenue,
    daily_checkins
FROM business_metrics
ORDER BY daily_revenue DESC
LIMIT 10;
```

---

## 🧪 Testing

### Test 1: Add Payment and Check Metrics
```sql
-- Add a payment
INSERT INTO payments (member_id, category, amount, payment_method, payment_date)
VALUES (1, 'Membership', 700.00, 'Cash', CURDATE());

-- Check metrics (should update automatically)
SELECT daily_revenue, daily_transactions 
FROM business_metrics 
WHERE metric_date = CURDATE();
```

### Test 2: Check-in Member and Check Metrics
```sql
-- Check in a member
INSERT INTO attendance (member_id, check_in_date, check_in_time)
VALUES (1, CURDATE(), CURTIME());

-- Check metrics (should update automatically)
SELECT daily_checkins 
FROM business_metrics 
WHERE metric_date = CURDATE();
```

### Test 3: Add Member and Check Metrics
```sql
-- Add a member (use your existing form or procedure)
-- Then check metrics
SELECT total_members, active_members 
FROM business_metrics 
WHERE metric_date = CURDATE();
```

---

## ⚠️ Important Notes

### Data Consistency:
- Metrics are calculated from actual data
- Always accurate and up-to-date
- Updates happen automatically via triggers

### Performance:
- One row per day (efficient storage)
- Fast queries (indexed by date)
- No complex joins needed

### Maintenance:
- No manual updates required
- Triggers handle everything
- Data persists for historical analysis

---

## 🚀 Integration with Existing Pages

### Update Dashboard (dashboard.php)
Replace complex queries with simple metric queries:

```php
// OLD WAY (complex):
$revenue = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT SUM(amount) as total FROM payments WHERE payment_date = CURDATE()"))['total'];

// NEW WAY (simple):
$metrics = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT daily_revenue FROM business_metrics WHERE metric_date = CURDATE()"));
$revenue = $metrics['daily_revenue'];
```

### Update Metrics Page (metrics.php)
Use business_metrics for faster queries and charts.

---

## 📊 Example Dashboard Integration

```php
<?php
// Get today's metrics
$metrics_query = "SELECT * FROM business_metrics WHERE metric_date = CURDATE()";
$metrics_result = mysqli_query($conn, $metrics_query);
$today = mysqli_fetch_assoc($metrics_result);

// Handle case where no metrics exist yet
if (!$today) {
    $today = [
        'daily_revenue' => 0,
        'monthly_revenue' => 0,
        'total_revenue' => 0,
        'daily_checkins' => 0,
        'monthly_checkins' => 0,
        'total_members' => 0,
        'active_members' => 0
    ];
}
?>

<!-- Display Metrics -->
<div class="metrics-grid">
    <div class="metric-card">
        <h3>Today's Revenue</h3>
        <p class="big-number">₱<?php echo number_format($today['daily_revenue'], 2); ?></p>
        <small>Monthly: ₱<?php echo number_format($today['monthly_revenue'], 2); ?></small>
    </div>
    
    <div class="metric-card">
        <h3>Today's Check-ins</h3>
        <p class="big-number"><?php echo $today['daily_checkins']; ?></p>
        <small>Monthly: <?php echo $today['monthly_checkins']; ?></small>
    </div>
    
    <div class="metric-card">
        <h3>Total Members</h3>
        <p class="big-number"><?php echo $today['total_members']; ?></p>
        <small>Active: <?php echo $today['active_members']; ?></small>
    </div>
</div>
```

---

## ✅ Summary

**What**: Automatic business metrics tracking table  
**How**: Updates via triggers on payments, attendance, and members  
**Why**: Real-time insights without manual calculations  
**Benefit**: Faster queries, accurate data, historical tracking  

**Installation**: Included in `database-triggers-procedures.sql`  
**Usage**: Query like any other table  
**Maintenance**: Zero - fully automatic!  

---

**Ready to use after installing the triggers and procedures!** 🚀
