-- ============================================================
-- POWER FITNESS GYM - TRIGGERS AND STORED PROCEDURES
-- ============================================================
-- This file contains 9 triggers and 9 stored procedures
-- for enhanced database functionality and automation
-- ============================================================

USE gym_system;

-- ============================================================
-- CREATE BUSINESS METRICS TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS business_metrics (
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
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- STORED PROCEDURES (9)
-- ============================================================

-- 1. Add New Member with Auto-Generated ID
DELIMITER //
CREATE PROCEDURE sp_add_member(
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_address VARCHAR(200),
    IN p_phone VARCHAR(20),
    IN p_gender VARCHAR(10),
    IN p_date_of_birth DATE,
    IN p_plan VARCHAR(50),
    IN p_duration VARCHAR(20),
    IN p_amount DECIMAL(10,2),
    IN p_date_enrolled DATE,
    OUT p_member_id_code VARCHAR(20)
)
BEGIN
    DECLARE v_last_number INT;
    DECLARE v_new_number INT;
    DECLARE v_expiry_date DATE;
    
    -- Get last member number
    SELECT COALESCE(MAX(CAST(SUBSTRING(member_id_code, 5) AS UNSIGNED)), 0) 
    INTO v_last_number 
    FROM members;
    
    -- Generate new member ID
    SET v_new_number = v_last_number + 1;
    SET p_member_id_code = CONCAT('MEM-', LPAD(v_new_number, 5, '0'));
    
    -- Calculate expiry date
    SET v_expiry_date = CASE p_duration
        WHEN '1 Month' THEN DATE_ADD(p_date_enrolled, INTERVAL 1 MONTH)
        WHEN '3 Months' THEN DATE_ADD(p_date_enrolled, INTERVAL 3 MONTH)
        WHEN '6 Months' THEN DATE_ADD(p_date_enrolled, INTERVAL 6 MONTH)
        WHEN '1 Year' THEN DATE_ADD(p_date_enrolled, INTERVAL 1 YEAR)
        ELSE DATE_ADD(p_date_enrolled, INTERVAL 1 MONTH)
    END;
    
    -- Insert member
    INSERT INTO members (
        member_id_code, first_name, last_name, address, phone, gender,
        date_of_birth, plan, duration, amount, date_enrolled, date_expiry, status
    ) VALUES (
        p_member_id_code, p_first_name, p_last_name, p_address, p_phone, p_gender,
        p_date_of_birth, p_plan, p_duration, p_amount, p_date_enrolled, v_expiry_date, 'Active'
    );
END //
DELIMITER ;

-- 2. Record Member Check-in
DELIMITER //
CREATE PROCEDURE sp_checkin_member(
    IN p_member_id INT,
    OUT p_status VARCHAR(50),
    OUT p_message VARCHAR(200)
)
BEGIN
    DECLARE v_member_status VARCHAR(20);
    DECLARE v_today_checkin INT;
    
    -- Check if member exists
    SELECT status INTO v_member_status FROM members WHERE id = p_member_id;
    
    IF v_member_status IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Member not found';
    ELSE
        -- Check if already checked in today
        SELECT COUNT(*) INTO v_today_checkin 
        FROM attendance 
        WHERE member_id = p_member_id AND check_in_date = CURDATE();
        
        IF v_today_checkin > 0 THEN
            SET p_status = 'WARNING';
            SET p_message = 'Member already checked in today';
        ELSE
            -- Record check-in
            INSERT INTO attendance (member_id, check_in_date, check_in_time)
            VALUES (p_member_id, CURDATE(), CURTIME());
            
            SET p_status = 'SUCCESS';
            SET p_message = 'Check-in recorded successfully';
        END IF;
    END IF;
END //
DELIMITER ;

-- 3. Get Member Statistics
DELIMITER //
CREATE PROCEDURE sp_get_member_stats(
    IN p_member_id INT,
    OUT p_total_checkins INT,
    OUT p_last_checkin DATE,
    OUT p_total_payments DECIMAL(10,2),
    OUT p_days_until_expiry INT
)
BEGIN
    -- Get total check-ins
    SELECT COUNT(*) INTO p_total_checkins
    FROM attendance WHERE member_id = p_member_id;
    
    -- Get last check-in date
    SELECT MAX(check_in_date) INTO p_last_checkin
    FROM attendance WHERE member_id = p_member_id;
    
    -- Get total payments
    SELECT COALESCE(SUM(amount), 0) INTO p_total_payments
    FROM payments WHERE member_id = p_member_id;
    
    -- Get days until expiry
    SELECT DATEDIFF(date_expiry, CURDATE()) INTO p_days_until_expiry
    FROM members WHERE id = p_member_id;
END //
DELIMITER ;

-- 4. Update Member Status Based on Expiry
DELIMITER //
CREATE PROCEDURE sp_update_member_statuses()
BEGIN
    -- Update expired members
    UPDATE members 
    SET status = 'Expired' 
    WHERE date_expiry < CURDATE() AND status = 'Active';
    
    -- Return count of updated members
    SELECT ROW_COUNT() as updated_count;
END //
DELIMITER ;

-- 5. Get Revenue Report by Date Range
DELIMITER //
CREATE PROCEDURE sp_revenue_report(
    IN p_date_from DATE,
    IN p_date_to DATE
)
BEGIN
    SELECT 
        DATE(payment_date) as date,
        payment_method,
        COUNT(*) as transaction_count,
        SUM(amount) as total_amount
    FROM payments
    WHERE payment_date BETWEEN p_date_from AND p_date_to
    GROUP BY DATE(payment_date), payment_method
    ORDER BY date DESC, payment_method;
END //
DELIMITER ;

-- 6. Get Attendance Report by Date Range
DELIMITER //
CREATE PROCEDURE sp_attendance_report(
    IN p_date_from DATE,
    IN p_date_to DATE
)
BEGIN
    SELECT 
        a.check_in_date,
        COUNT(DISTINCT a.member_id) as unique_members,
        COUNT(*) as total_checkins,
        GROUP_CONCAT(CONCAT(m.first_name, ' ', m.last_name) SEPARATOR ', ') as members
    FROM attendance a
    JOIN members m ON a.member_id = m.id
    WHERE a.check_in_date BETWEEN p_date_from AND p_date_to
    GROUP BY a.check_in_date
    ORDER BY a.check_in_date DESC;
END //
DELIMITER ;

-- 7. Get Expiring Memberships
DELIMITER //
CREATE PROCEDURE sp_get_expiring_memberships(
    IN p_days_ahead INT
)
BEGIN
    SELECT 
        member_id_code,
        CONCAT(first_name, ' ', last_name) as member_name,
        phone,
        date_expiry,
        DATEDIFF(date_expiry, CURDATE()) as days_remaining,
        plan,
        amount
    FROM members
    WHERE status = 'Active'
    AND date_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL p_days_ahead DAY)
    ORDER BY date_expiry ASC;
END //
DELIMITER ;

-- 8. Renew Member Membership
DELIMITER //
CREATE PROCEDURE sp_renew_membership(
    IN p_member_id INT,
    IN p_duration VARCHAR(20),
    IN p_amount DECIMAL(10,2),
    OUT p_new_expiry DATE,
    OUT p_status VARCHAR(50)
)
BEGIN
    DECLARE v_current_expiry DATE;
    DECLARE v_new_expiry DATE;
    
    -- Get current expiry date
    SELECT date_expiry INTO v_current_expiry FROM members WHERE id = p_member_id;
    
    IF v_current_expiry IS NULL THEN
        SET p_status = 'ERROR: Member not found';
        SET p_new_expiry = NULL;
    ELSE
        -- Calculate new expiry from current expiry or today (whichever is later)
        SET v_current_expiry = IF(v_current_expiry > CURDATE(), v_current_expiry, CURDATE());
        
        SET v_new_expiry = CASE p_duration
            WHEN '1 Month' THEN DATE_ADD(v_current_expiry, INTERVAL 1 MONTH)
            WHEN '3 Months' THEN DATE_ADD(v_current_expiry, INTERVAL 3 MONTH)
            WHEN '6 Months' THEN DATE_ADD(v_current_expiry, INTERVAL 6 MONTH)
            WHEN '1 Year' THEN DATE_ADD(v_current_expiry, INTERVAL 1 YEAR)
            ELSE DATE_ADD(v_current_expiry, INTERVAL 1 MONTH)
        END;
        
        -- Update member
        UPDATE members 
        SET date_expiry = v_new_expiry,
            status = 'Active',
            duration = p_duration,
            amount = p_amount
        WHERE id = p_member_id;
        
        -- Record payment
        INSERT INTO payments (member_id, category, quantity, amount, payment_method, payment_date, status)
        VALUES (p_member_id, 'Membership Renewal', 1, p_amount, 'Cash', CURDATE(), 'Paid');
        
        SET p_new_expiry = v_new_expiry;
        SET p_status = 'SUCCESS: Membership renewed';
    END IF;
END //
DELIMITER ;

-- 9. Get Top Active Members
DELIMITER //
CREATE PROCEDURE sp_get_top_members(
    IN p_limit INT,
    IN p_date_from DATE,
    IN p_date_to DATE
)
BEGIN
    SELECT 
        m.member_id_code,
        CONCAT(m.first_name, ' ', m.last_name) as member_name,
        m.phone,
        m.status,
        COUNT(a.id) as checkin_count,
        MAX(a.check_in_date) as last_checkin
    FROM members m
    LEFT JOIN attendance a ON m.id = a.member_id 
        AND a.check_in_date BETWEEN p_date_from AND p_date_to
    GROUP BY m.id
    ORDER BY checkin_count DESC
    LIMIT p_limit;
END //
DELIMITER ;

-- ============================================================
-- TRIGGERS (9)
-- ============================================================

-- 1. Before Insert Member - Validate Data
DELIMITER //
CREATE TRIGGER trg_before_insert_member
BEFORE INSERT ON members
FOR EACH ROW
BEGIN
    -- Validate phone number (must be numeric and 10-11 digits)
    IF NEW.phone IS NOT NULL AND (LENGTH(NEW.phone) < 10 OR LENGTH(NEW.phone) > 11) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Phone number must be 10-11 digits';
    END IF;
    
    -- Validate amount (must be positive)
    IF NEW.amount <= 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Amount must be greater than zero';
    END IF;
    
    -- Validate date of birth (must be at least 10 years old)
    IF TIMESTAMPDIFF(YEAR, NEW.date_of_birth, CURDATE()) < 10 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Member must be at least 10 years old';
    END IF;
    
    -- Set created_at timestamp
    SET NEW.created_at = NOW();
END //
DELIMITER ;

-- 2. After Insert Member - Log Activity and Update Metrics
DELIMITER //
CREATE TRIGGER trg_after_insert_member
AFTER INSERT ON members
FOR EACH ROW
BEGIN
    DECLARE v_total_members INT;
    DECLARE v_active_members INT;
    
    -- Count total members
    SELECT COUNT(*) INTO v_total_members FROM members;
    
    -- Count active members
    SELECT COUNT(*) INTO v_active_members FROM members WHERE status = 'Active';
    
    -- Update business metrics
    INSERT INTO business_metrics (
        metric_date,
        total_members,
        active_members,
        last_updated
    ) VALUES (
        CURDATE(),
        v_total_members,
        v_active_members,
        NOW()
    )
    ON DUPLICATE KEY UPDATE
        total_members = v_total_members,
        active_members = v_active_members,
        last_updated = NOW();
    
    -- Set session variables for tracking
    SET @last_member_id = NEW.id;
    SET @last_action = 'MEMBER_CREATED';
END //
DELIMITER ;

-- 3. Before Update Member - Auto-Update Status
DELIMITER //
CREATE TRIGGER trg_before_update_member
BEFORE UPDATE ON members
FOR EACH ROW
BEGIN
    -- Auto-update status based on expiry date
    IF NEW.date_expiry < CURDATE() THEN
        SET NEW.status = 'Expired';
    ELSEIF NEW.date_expiry >= CURDATE() AND OLD.status = 'Expired' THEN
        SET NEW.status = 'Active';
    END IF;
    
    -- Validate amount if changed
    IF NEW.amount <= 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Amount must be greater than zero';
    END IF;
END //
DELIMITER ;

-- 4. After Insert Payment - Update Business Metrics
DELIMITER //
CREATE TRIGGER trg_after_insert_payment
AFTER INSERT ON payments
FOR EACH ROW
BEGIN
    DECLARE v_today_revenue DECIMAL(10,2);
    DECLARE v_month_revenue DECIMAL(10,2);
    DECLARE v_total_revenue DECIMAL(10,2);
    DECLARE v_today_transactions INT;
    DECLARE v_month_transactions INT;
    
    -- Calculate today's revenue
    SELECT COALESCE(SUM(amount), 0) INTO v_today_revenue
    FROM payments WHERE payment_date = CURDATE();
    
    -- Calculate this month's revenue
    SELECT COALESCE(SUM(amount), 0) INTO v_month_revenue
    FROM payments 
    WHERE MONTH(payment_date) = MONTH(CURDATE()) 
    AND YEAR(payment_date) = YEAR(CURDATE());
    
    -- Calculate total revenue
    SELECT COALESCE(SUM(amount), 0) INTO v_total_revenue
    FROM payments;
    
    -- Count today's transactions
    SELECT COUNT(*) INTO v_today_transactions
    FROM payments WHERE payment_date = CURDATE();
    
    -- Count this month's transactions
    SELECT COUNT(*) INTO v_month_transactions
    FROM payments 
    WHERE MONTH(payment_date) = MONTH(CURDATE()) 
    AND YEAR(payment_date) = YEAR(CURDATE());
    
    -- Update or insert into business_metrics table
    INSERT INTO business_metrics (
        metric_date,
        daily_revenue,
        monthly_revenue,
        total_revenue,
        daily_transactions,
        monthly_transactions,
        last_updated
    ) VALUES (
        CURDATE(),
        v_today_revenue,
        v_month_revenue,
        v_total_revenue,
        v_today_transactions,
        v_month_transactions,
        NOW()
    )
    ON DUPLICATE KEY UPDATE
        daily_revenue = v_today_revenue,
        monthly_revenue = v_month_revenue,
        total_revenue = v_total_revenue,
        daily_transactions = v_today_transactions,
        monthly_transactions = v_month_transactions,
        last_updated = NOW();
END //
DELIMITER ;

-- 5. Before Insert Attendance - Validate Check-in
DELIMITER //
CREATE TRIGGER trg_before_insert_attendance
BEFORE INSERT ON attendance
FOR EACH ROW
BEGIN
    DECLARE v_member_exists INT;
    DECLARE v_duplicate_checkin INT;
    
    -- Check if member exists
    SELECT COUNT(*) INTO v_member_exists FROM members WHERE id = NEW.member_id;
    IF v_member_exists = 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Member does not exist';
    END IF;
    
    -- Check for duplicate check-in today
    SELECT COUNT(*) INTO v_duplicate_checkin
    FROM attendance
    WHERE member_id = NEW.member_id AND check_in_date = NEW.check_in_date;
    
    IF v_duplicate_checkin > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Member already checked in today';
    END IF;
    
    -- Set created_at timestamp
    SET NEW.created_at = NOW();
END //
DELIMITER ;

-- 6. After Insert Attendance - Track Last Activity
DELIMITER //
CREATE TRIGGER trg_after_insert_attendance
AFTER INSERT ON attendance
FOR EACH ROW
BEGIN
    DECLARE v_today_checkins INT;
    DECLARE v_month_checkins INT;
    
    -- Count today's check-ins
    SELECT COUNT(*) INTO v_today_checkins
    FROM attendance WHERE check_in_date = CURDATE();
    
    -- Count this month's check-ins
    SELECT COUNT(*) INTO v_month_checkins
    FROM attendance 
    WHERE MONTH(check_in_date) = MONTH(CURDATE()) 
    AND YEAR(check_in_date) = YEAR(CURDATE());
    
    -- Update business metrics
    INSERT INTO business_metrics (
        metric_date,
        daily_checkins,
        monthly_checkins,
        last_updated
    ) VALUES (
        CURDATE(),
        v_today_checkins,
        v_month_checkins,
        NOW()
    )
    ON DUPLICATE KEY UPDATE
        daily_checkins = v_today_checkins,
        monthly_checkins = v_month_checkins,
        last_updated = NOW();
    
    -- Set session variables for tracking
    SET @last_checkin_member = NEW.member_id;
    SET @last_checkin_date = NEW.check_in_date;
END //
DELIMITER ;

-- 7. Before Insert Payment - Validate Payment
DELIMITER //
CREATE TRIGGER trg_before_insert_payment
BEFORE INSERT ON payments
FOR EACH ROW
BEGIN
    DECLARE v_member_exists INT;
    
    -- Check if member exists
    SELECT COUNT(*) INTO v_member_exists FROM members WHERE id = NEW.member_id;
    IF v_member_exists = 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Member does not exist';
    END IF;
    
    -- Validate amount
    IF NEW.amount <= 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Payment amount must be greater than zero';
    END IF;
    
    -- Validate quantity
    IF NEW.quantity <= 0 THEN
        SET NEW.quantity = 1;
    END IF;
    
    -- Set default status if not provided
    IF NEW.status IS NULL OR NEW.status = '' THEN
        SET NEW.status = 'Paid';
    END IF;
    
    -- Set created_at timestamp
    SET NEW.created_at = NOW();
END //
DELIMITER ;

-- 8. Before Update Inventory - Validate Stock
DELIMITER //
CREATE TRIGGER trg_before_update_inventory
BEFORE UPDATE ON inventory
FOR EACH ROW
BEGIN
    -- Prevent negative quantity
    IF NEW.quantity < 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Inventory quantity cannot be negative';
    END IF;
    
    -- Validate price
    IF NEW.price < 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Price cannot be negative';
    END IF;
    
    -- Alert if stock is low (less than 5)
    IF NEW.quantity < 5 AND OLD.quantity >= 5 THEN
        SET @low_stock_alert = CONCAT('Low stock alert: ', NEW.item_name);
    END IF;
END //
DELIMITER ;

-- 9. Before Insert Announcement - Validate Dates
DELIMITER //
CREATE TRIGGER trg_before_insert_announcement
BEFORE INSERT ON announcements
FOR EACH ROW
BEGIN
    -- Validate date range
    IF NEW.date_to < NEW.date_from THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'End date must be after start date';
    END IF;
    
    -- Validate priority
    IF NEW.priority NOT IN ('Normal', 'Important', 'Urgent') THEN
        SET NEW.priority = 'Normal';
    END IF;
    
    -- Set created_at timestamp
    SET NEW.created_at = NOW();
END //
DELIMITER ;

-- ============================================================
-- END OF TRIGGERS AND STORED PROCEDURES
-- ============================================================

-- To view all stored procedures:
-- SHOW PROCEDURE STATUS WHERE Db = 'gym_system';

-- To view all triggers:
-- SHOW TRIGGERS FROM gym_system;

-- To drop a procedure: DROP PROCEDURE IF EXISTS procedure_name;
-- To drop a trigger: DROP TRIGGER IF EXISTS trigger_name;
