# Database Reset Guide - Start Fresh

## 🔄 How to Clear All Data and Start Fresh

This guide shows you how to delete all records while keeping your database structure intact.

---

## 🎯 Quick Reset (Recommended)

### Method 1: Using the Reset Script (Easiest)

1. **Open phpMyAdmin**: `http://localhost/phpmyadmin`
2. **Select database**: Click `gym_system` in left sidebar
3. **Go to SQL tab**: Click "SQL" at the top
4. **Import reset script**:
   - Click "Import" tab
   - Choose file: `reset-database.sql`
   - Click "Go"
   
**OR** copy-paste the script:
   - Open `reset-database.sql` in text editor
   - Copy all content (Ctrl+A, Ctrl+C)
   - Paste in SQL tab (Ctrl+V)
   - Click "Go"

5. **Done!** All data cleared, structure intact ✅

---

## 📋 What Gets Deleted

### ❌ Data Removed:
- All members
- All attendance records
- All payments
- All inventory items
- All announcements
- All business metrics
- All users (except default admin)

### ✅ What Stays:
- Database structure (tables, columns)
- Stored procedures (all 9)
- Triggers (all 9)
- Default admin account (admin@powergym.com)
- Table relationships (foreign keys)

---

## 🔧 Manual Reset (Alternative)

If you prefer to do it manually:

### Step 1: Disable Foreign Key Checks
```sql
SET FOREIGN_KEY_CHECKS = 0;
```

### Step 2: Clear Each Table
```sql
TRUNCATE TABLE attendance;
TRUNCATE TABLE payments;
TRUNCATE TABLE announcements;
TRUNCATE TABLE inventory;
TRUNCATE TABLE members;
TRUNCATE TABLE business_metrics;
DELETE FROM users WHERE email != 'admin@powergym.com';
```

### Step 3: Re-enable Foreign Key Checks
```sql
SET FOREIGN_KEY_CHECKS = 1;
```

### Step 4: Reset Auto Increment
```sql
ALTER TABLE users AUTO_INCREMENT = 2;
ALTER TABLE members AUTO_INCREMENT = 1;
ALTER TABLE attendance AUTO_INCREMENT = 1;
ALTER TABLE payments AUTO_INCREMENT = 1;
ALTER TABLE inventory AUTO_INCREMENT = 1;
ALTER TABLE announcements AUTO_INCREMENT = 1;
ALTER TABLE business_metrics AUTO_INCREMENT = 1;
```

---

## 🗑️ Complete Fresh Start (Nuclear Option)

If you want to completely rebuild everything:

### Option A: Drop and Recreate Database
```sql
-- Drop entire database
DROP DATABASE IF EXISTS gym_system;

-- Recreate from scratch
CREATE DATABASE gym_system;
USE gym_system;

-- Then import database.sql
-- Then import database-triggers-procedures.sql
```

### Option B: Using phpMyAdmin
1. Select `gym_system` database
2. Click "Operations" tab
3. Scroll to "Remove database"
4. Click "Drop the database (DROP)"
5. Confirm deletion
6. Create new database: `gym_system`
7. Import `database.sql`
8. Import `database-triggers-procedures.sql`

---

## 🧹 Clean QR Codes (Optional)

The reset script doesn't delete QR code files. To remove them:

### Windows:
```bash
# Delete all QR codes
del /Q assets\qrcodes\*.png

# Or keep the folder structure
rmdir /S /Q assets\qrcodes
mkdir assets\qrcodes
```

### Mac/Linux:
```bash
# Delete all QR codes
rm -f assets/qrcodes/*.png

# Or recreate folder
rm -rf assets/qrcodes
mkdir assets/qrcodes
```

### Manual:
1. Open `assets/qrcodes/` folder
2. Delete all `.png` files
3. Keep the folder itself

---

## ✅ Verify Reset

After resetting, check if it worked:

### Check Record Counts:
```sql
SELECT 'users' as table_name, COUNT(*) as records FROM users
UNION ALL
SELECT 'members', COUNT(*) FROM members
UNION ALL
SELECT 'attendance', COUNT(*) FROM attendance
UNION ALL
SELECT 'payments', COUNT(*) FROM payments
UNION ALL
SELECT 'inventory', COUNT(*) FROM inventory
UNION ALL
SELECT 'announcements', COUNT(*) FROM announcements;
```

**Expected Results**:
- users: 1 (default admin)
- members: 0
- attendance: 0
- payments: 0
- inventory: 0
- announcements: 0

---

## 🔐 Default Login After Reset

After reset, you can login with:
- **Email**: `admin@powergym.com`
- **Password**: `admin123`

⚠️ **Remember to change this password in production!**

---

## 🎯 Common Use Cases

### Use Case 1: Testing
**Scenario**: You want to test the system with fresh data  
**Solution**: Run `reset-database.sql`

### Use Case 2: Demo
**Scenario**: You want to show the system to someone  
**Solution**: Run `reset-database.sql`, then add sample data

### Use Case 3: Production Deployment
**Scenario**: Moving from development to production  
**Solution**: 
1. Export structure only (no data)
2. Import on production server
3. Create new admin account
4. Don't import sample data

### Use Case 4: Mistake Recovery
**Scenario**: You accidentally added wrong data  
**Solution**: Run `reset-database.sql` and start over

---

## ⚠️ Important Warnings

### Before Resetting:

1. **Backup First** (if you have important data):
   ```sql
   -- Export database
   mysqldump -u root -p gym_system > backup_before_reset.sql
   ```

2. **Close All Applications**: Make sure no one is using the system

3. **QR Codes**: Reset doesn't delete QR files (delete manually if needed)

4. **No Undo**: Once you run TRUNCATE, data is permanently deleted

---

## 🔄 Reset vs Drop Database

### TRUNCATE (Reset Script):
- ✅ Keeps structure
- ✅ Keeps procedures/triggers
- ✅ Keeps default admin
- ✅ Fast
- ❌ Can't undo

### DROP DATABASE:
- ✅ Complete fresh start
- ❌ Loses everything
- ❌ Must reimport structure
- ❌ Must recreate procedures/triggers
- ❌ Slower

**Recommendation**: Use reset script (TRUNCATE) for most cases

---

## 📊 Step-by-Step: Complete Fresh Start

### Full Reset Procedure:

1. **Backup** (optional but recommended):
   ```bash
   mysqldump -u root -p gym_system > backup.sql
   ```

2. **Reset database**:
   - Import `reset-database.sql` in phpMyAdmin

3. **Clear QR codes**:
   - Delete all files in `assets/qrcodes/`

4. **Verify**:
   - Login with default admin
   - Check all pages are empty
   - Try adding a test member

5. **Start fresh**:
   - Add your real data
   - Generate new QR codes
   - Configure settings

---

## 🧪 Test After Reset

### Quick Test Checklist:

- [ ] Can login with default admin
- [ ] Dashboard shows zero stats
- [ ] No members in member list
- [ ] No attendance records
- [ ] No payments
- [ ] No inventory items
- [ ] No announcements
- [ ] Can add new member successfully
- [ ] QR code generates for new member
- [ ] Can check in new member
- [ ] Can record payment
- [ ] Metrics update automatically

---

## 💡 Pro Tips

### Tip 1: Keep Sample Data
Create a separate SQL file with sample data you can import after reset:
```sql
-- sample-data.sql
INSERT INTO members (...) VALUES (...);
INSERT INTO inventory (...) VALUES (...);
-- etc.
```

### Tip 2: Automate Reset for Testing
Create a batch file (Windows) or shell script (Mac/Linux):
```bash
# reset.bat (Windows)
mysql -u root -p gym_system < reset-database.sql
pause
```

### Tip 3: Reset Specific Tables Only
If you only want to clear members:
```sql
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE members;
TRUNCATE TABLE attendance;
TRUNCATE TABLE payments;
SET FOREIGN_KEY_CHECKS = 1;
```

---

## 🆘 Troubleshooting

### Issue: "Cannot truncate table referenced in foreign key"
**Solution**: Make sure you disabled foreign key checks:
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- your TRUNCATE commands
SET FOREIGN_KEY_CHECKS = 1;
```

### Issue: "Table doesn't exist"
**Solution**: Some tables might not exist yet. The script handles this gracefully.

### Issue: "Access denied"
**Solution**: Make sure you're logged in as root or have DELETE privileges.

### Issue: "Lost admin account"
**Solution**: The script keeps the default admin. If you deleted it:
```sql
INSERT INTO users (email, password, full_name, role) 
VALUES ('admin@powergym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'admin');
```

---

## 📝 Summary

**Quick Reset**: Import `reset-database.sql` in phpMyAdmin  
**What's Deleted**: All data (members, attendance, payments, etc.)  
**What's Kept**: Structure, procedures, triggers, default admin  
**Time**: Less than 1 minute  
**Reversible**: No (backup first if needed)  

---

## ✅ Ready to Reset?

1. Backup (if needed)
2. Import `reset-database.sql`
3. Clear QR codes (optional)
4. Login with default admin
5. Start fresh!

**That's it!** 🚀

---

**File**: `reset-database.sql`  
**Safe to run**: Yes (keeps structure)  
**Reversible**: No (backup first)  
**Time**: < 1 minute
