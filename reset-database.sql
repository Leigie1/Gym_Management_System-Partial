-- ============================================================
-- RESET DATABASE - Clear All Data (Start Fresh)
-- ============================================================
-- This script deletes all records but keeps the database structure
-- Use this to start with a clean slate
-- ============================================================

USE gym_system;

-- ============================================================
-- DISABLE FOREIGN KEY CHECKS (Important!)
-- ============================================================
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- DELETE ALL RECORDS FROM TABLES
-- ============================================================

-- Clear attendance records
TRUNCATE TABLE attendance;

-- Clear payment records
TRUNCATE TABLE payments;

-- Clear announcements
TRUNCATE TABLE announcements;

-- Clear inventory
TRUNCATE TABLE inventory;

-- Clear members (this will also clear related attendance and payments due to CASCADE)
TRUNCATE TABLE members;

-- Clear business metrics (if exists)
TRUNCATE TABLE business_metrics;

-- Clear users (except default admin)
DELETE FROM users WHERE email != 'admin@powergym.com';

-- ============================================================
-- RE-ENABLE FOREIGN KEY CHECKS
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- RESET AUTO INCREMENT COUNTERS
-- ============================================================

ALTER TABLE users AUTO_INCREMENT = 2;
ALTER TABLE members AUTO_INCREMENT = 1;
ALTER TABLE attendance AUTO_INCREMENT = 1;
ALTER TABLE payments AUTO_INCREMENT = 1;
ALTER TABLE inventory AUTO_INCREMENT = 1;
ALTER TABLE announcements AUTO_INCREMENT = 1;
ALTER TABLE business_metrics AUTO_INCREMENT = 1;

-- ============================================================
-- VERIFICATION
-- ============================================================

-- Check record counts (should all be 0 or 1 for users)
SELECT 'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'members', COUNT(*) FROM members
UNION ALL
SELECT 'attendance', COUNT(*) FROM attendance
UNION ALL
SELECT 'payments', COUNT(*) FROM payments
UNION ALL
SELECT 'inventory', COUNT(*) FROM inventory
UNION ALL
SELECT 'announcements', COUNT(*) FROM announcements
UNION ALL
SELECT 'business_metrics', COUNT(*) FROM business_metrics;

-- ============================================================
-- SUCCESS MESSAGE
-- ============================================================

SELECT 'Database reset successfully! All data cleared except default admin account.' as status;
SELECT 'You can now start fresh with clean data.' as message;
SELECT 'Default admin: admin@powergym.com / admin123' as login_info;

-- ============================================================
-- NOTES
-- ============================================================
-- What was deleted:
-- ✓ All members
-- ✓ All attendance records
-- ✓ All payments
-- ✓ All inventory items
-- ✓ All announcements
-- ✓ All business metrics
-- ✓ All users except default admin
--
-- What was kept:
-- ✓ Database structure (tables, columns)
-- ✓ Stored procedures
-- ✓ Triggers
-- ✓ Default admin account
-- ✓ QR code files (in assets/qrcodes/ - delete manually if needed)
-- ============================================================
