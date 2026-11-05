-- VaxiCare Realistic Seed Data (30+ records)
-- Run this in phpMyAdmin â†’ vaccine_db
USE vaccine_db;

-- â”€â”€â”€ USERS (12 realistic users) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
INSERT IGNORE INTO users (name, email, phone, password, status, created_at) VALUES
('Demo User', 'demo.user01@example.com', '0000000001', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 30 DAY)),
('Demo User', 'demo.user02@example.com', '0000000002', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 28 DAY)),
('Demo User', 'demo.user03@example.com', '0000000003', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 25 DAY)),
('Demo User', 'demo.user04@example.com', '0000000004', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 22 DAY)),
('Demo User', 'demo.user05@example.com', '0000000005', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 20 DAY)),
('Demo User', 'demo.user06@example.com', '0000000006', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 18 DAY)),
('Demo User', 'demo.user07@example.com', '0000000007', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 15 DAY)),
('Demo User', 'demo.user08@example.com', '0000000008', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 12 DAY)),
('Demo User', 'demo.user09@example.com', '0000000009', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL 10 DAY)),
('Demo User', 'demo.user10@example.com', '0000000010', 'DEMO_ACCOUNT_DISABLED', 'approved', DATE_SUB(NOW(), INTERVAL  8 DAY)),
('Demo User', 'demo.user11@example.com', '0000000011', 'DEMO_ACCOUNT_DISABLED', 'pending',  DATE_SUB(NOW(), INTERVAL  5 DAY)),
('Demo User', 'demo.user12@example.com', '0000000012', 'DEMO_ACCOUNT_DISABLED', 'pending',  DATE_SUB(NOW(), INTERVAL  2 DAY));

-- â”€â”€â”€ VACCINE STOCK (district-wise, realistic quantities) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
INSERT IGNORE INTO vaccine_stock (district_id, vaccine_name, quantity) VALUES
(3, 'Covishield', 620), (3, 'Covaxin', 280), (3, 'Sputnik V', 150),
(6, 'Covishield', 310), (6, 'Covaxin', 180),
(7, 'Covishield', 400), (7, 'Sputnik V',  90),
(8, 'Covaxin',     45), (8, 'Covishield', 220);  -- 45 = low stock alert

-- â”€â”€â”€ SLOTS (past + upcoming for bookings) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
INSERT IGNORE INTO slots (district_id, center_id, slot_date, slot_time, vaccine_name, available_slots) VALUES
(1, 1, DATE_SUB(CURDATE(), INTERVAL 25 DAY), '09:00:00', 'Covishield', 0),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 20 DAY), '10:00:00', 'Covaxin',    0),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 18 DAY), '09:00:00', 'Covishield', 0),
(2, 3, DATE_SUB(CURDATE(), INTERVAL 15 DAY), '10:00:00', 'Covishield', 0),
(4, 4, DATE_SUB(CURDATE(), INTERVAL 14 DAY), '11:00:00', 'Covaxin',    0),
(5, 5, DATE_SUB(CURDATE(), INTERVAL 12 DAY), '09:30:00', 'Covishield', 0),
(6, 6, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '10:00:00', 'Sputnik V',  0),
(7, 7, DATE_SUB(CURDATE(), INTERVAL  8 DAY), '09:00:00', 'Covishield', 0),
(8, 8, DATE_SUB(CURDATE(), INTERVAL  7 DAY), '10:00:00', 'Covaxin',    0),
(1, 1, DATE_SUB(CURDATE(), INTERVAL  5 DAY), '09:00:00', 'Covaxin',    0),
(1, 2, DATE_SUB(CURDATE(), INTERVAL  3 DAY), '11:00:00', 'Covishield', 0),
(2, 3, DATE_SUB(CURDATE(), INTERVAL  2 DAY), '10:00:00', 'Sputnik V',  0),
(3, 1, DATE_SUB(CURDATE(), INTERVAL  1 DAY), '09:00:00', 'Covishield', 0),
-- upcoming
(1, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY),  '09:00:00', 'Covishield', 45),
(1, 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY),  '10:00:00', 'Covaxin',    30),
(2, 3, DATE_ADD(CURDATE(), INTERVAL 4 DAY),  '09:00:00', 'Covishield', 40),
(3, 1, DATE_ADD(CURDATE(), INTERVAL 5 DAY),  '11:00:00', 'Sputnik V',  20),
(5, 5, DATE_ADD(CURDATE(), INTERVAL 6 DAY),  '09:30:00', 'Covaxin',    35);

-- â”€â”€â”€ BOOKINGS (30 bookings across users & slots) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
-- Uses slot IDs 6â€“18 (past slots added above, adjust if slots already exist)
-- Safer: use subquery to get the last inserted slot IDs
-- We'll insert against the newly inserted past slots using date matching

INSERT IGNORE INTO bookings (user_id, slot_id, status, booking_date)
SELECT u.user_id, s.slot_id, 'completed',
       DATE_ADD(s.slot_date, INTERVAL -2 DAY)
FROM users u, slots s
WHERE u.email IN ('demo@example.com','demo@example.com','demo@example.com','demo@example.com','demo@example.com')
  AND s.slot_date < CURDATE()
  AND s.available_slots = 0
LIMIT 20;

INSERT IGNORE INTO bookings (user_id, slot_id, status, booking_date)
SELECT u.user_id, s.slot_id, 'booked',
       DATE_SUB(NOW(), INTERVAL 1 DAY)
FROM users u, slots s
WHERE u.email IN ('demo@example.com','demo@example.com','demo@example.com')
  AND s.slot_date >= CURDATE()
LIMIT 9;

INSERT IGNORE INTO bookings (user_id, slot_id, status, booking_date)
SELECT u.user_id, s.slot_id, 'cancelled',
       DATE_ADD(s.slot_date, INTERVAL -3 DAY)
FROM users u, slots s
WHERE u.email IN ('demo@example.com','demo@example.com')
  AND s.slot_date < CURDATE()
  AND s.available_slots = 0
LIMIT 4;

-- â”€â”€â”€ CERTIFICATES (for all completed bookings) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
INSERT IGNORE INTO certificates (user_id, booking_id, vaccine_name, dose_number, vaccination_date, certificate_number)
SELECT b.user_id, b.booking_id, s.vaccine_name, 1, s.slot_date,
       CONCAT('VXCI-', YEAR(s.slot_date), '-', LPAD(b.booking_id, 5, '0'))
FROM bookings b
JOIN slots s ON b.slot_id = s.slot_id
WHERE b.status = 'completed'
  AND NOT EXISTS (SELECT 1 FROM certificates c WHERE c.booking_id = b.booking_id);

-- â”€â”€â”€ DAILY REPORTS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
INSERT IGNORE INTO daily_reports (report_date, district_id, total_bookings, total_vaccinations, total_certificates)
SELECT s.slot_date, s.district_id,
       COUNT(b.booking_id),
       SUM(CASE WHEN b.status='completed' THEN 1 ELSE 0 END),
       COUNT(c.certificate_id)
FROM bookings b
JOIN slots s ON b.slot_id = s.slot_id
LEFT JOIN certificates c ON b.booking_id = c.booking_id
GROUP BY s.slot_date, s.district_id;

-- Done! Check totals:
SELECT 'users' AS tbl, COUNT(*) AS cnt FROM users
UNION ALL SELECT 'bookings', COUNT(*) FROM bookings
UNION ALL SELECT 'certificates', COUNT(*) FROM certificates
UNION ALL SELECT 'vaccine_stock', COUNT(*) FROM vaccine_stock
UNION ALL SELECT 'slots', COUNT(*) FROM slots;
