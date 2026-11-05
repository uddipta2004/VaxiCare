<?php
// VaxiCare Data Seeder â€” run once at http://localhost/VaxiCare/seed.php
require_once 'config.php';
$conn = getDBConnection();
$log = [];

// â”€â”€ 1. INSERT USERS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$users = [
    ['Demo User', 'demo.user01@example.com', '0000000001', 'approved', '-30 days'],
    ['Demo User', 'demo.user02@example.com', '0000000002', 'approved', '-28 days'],
    ['Demo User', 'demo.user03@example.com', '0000000003', 'approved', '-25 days'],
    ['Demo User', 'demo.user04@example.com', '0000000004', 'approved', '-22 days'],
    ['Demo User', 'demo.user05@example.com', '0000000005', 'approved', '-20 days'],
    ['Demo User', 'demo.user06@example.com', '0000000006', 'approved', '-18 days'],
    ['Demo User', 'demo.user07@example.com', '0000000007', 'approved', '-15 days'],
    ['Demo User', 'demo.user08@example.com', '0000000008', 'approved', '-12 days'],
    ['Demo User', 'demo.user09@example.com', '0000000009', 'approved', '-10 days'],
    ['Demo User', 'demo.user10@example.com', '0000000010', 'approved',  '-8 days'],
    ['Demo User', 'demo.user11@example.com', '0000000011', 'pending',   '-5 days'],
    ['Demo User', 'demo.user12@example.com', '0000000012', 'pending',   '-2 days'],
];
// Seed records are for dashboard presentation only and receive non-reusable
// random passwords, so the sample accounts cannot be logged into.
$pass = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT IGNORE INTO users (name,email,phone,password,status,created_at) VALUES (?,?,?,?,?,?)");
$inserted_users = 0;
foreach ($users as $u) {
    $ts = date('Y-m-d H:i:s', strtotime($u[4]));
    $stmt->bind_param("ssssss", $u[0], $u[1], $u[2], $pass, $u[3], $ts);
    $stmt->execute();
    $inserted_users += $stmt->affected_rows;
}
$stmt->close();
$log[] = "âœ… Users inserted/skipped: $inserted_users";

// â”€â”€ 2. GET IDs â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$userIds = $conn->query("SELECT user_id FROM users WHERE email LIKE 'demo.user%@example.com' ORDER BY user_id")->fetch_all(MYSQLI_ASSOC);
$userIds = array_column($userIds, 'user_id');

// Get districts & centers
$districts = $conn->query("SELECT district_id FROM districts ORDER BY district_id LIMIT 8")->fetch_all(MYSQLI_ASSOC);
$distIds   = array_column($districts, 'district_id');
$centers   = $conn->query("SELECT center_id, district_id FROM centers ORDER BY center_id")->fetch_all(MYSQLI_ASSOC);

if (empty($distIds) || empty($centers)) {
    die("âŒ No districts/centers found. Run the main database.sql first.");
}

// â”€â”€ 3. ADD MORE VACCINE STOCK â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$vaccines = ['Covishield','Covaxin','Sputnik V'];
$stocks = [];
foreach ($distIds as $did) {
    foreach ($vaccines as $v) {
        $qty = rand(30, 800);
        $stocks[] = "($did,'$v',$qty)";
    }
}
$conn->query("INSERT IGNORE INTO vaccine_stock (district_id,vaccine_name,quantity) VALUES ".implode(',',$stocks));
// Purposely set 2 low-stock entries for alert UI demo
$conn->query("UPDATE vaccine_stock SET quantity=22 WHERE vaccine_name='Sputnik V' LIMIT 1");
$conn->query("UPDATE vaccine_stock SET quantity=35 WHERE vaccine_name='Covaxin' AND district_id=(SELECT district_id FROM districts ORDER BY district_id DESC LIMIT 1)");
$log[] = "âœ… Stock records ensured";

// â”€â”€ 4. INSERT SLOTS (past + future) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$slotDefs = [];
// Past slots (for bookings)
$pastDays = [25,22,20,18,16,14,12,10,8,6,4,2,1];
$times    = ['09:00:00','10:00:00','11:00:00','14:00:00'];
foreach ($pastDays as $i => $d) {
    $ci = $centers[$i % count($centers)];
    $v  = $vaccines[$i % 3];
    $dt = date('Y-m-d', strtotime("-$d days"));
    $t  = $times[$i % 4];
    $slotDefs[] = "({$ci['district_id']},{$ci['center_id']},'$dt','$t','$v',0)";
}
// Future slots
for ($i = 1; $i <= 10; $i++) {
    $ci = $centers[$i % count($centers)];
    $v  = $vaccines[$i % 3];
    $dt = date('Y-m-d', strtotime("+$i days"));
    $t  = $times[$i % 4];
    $slotDefs[] = "({$ci['district_id']},{$ci['center_id']},'$dt','$t','$v',".rand(20,60).")";
}
$conn->query("INSERT IGNORE INTO slots (district_id,center_id,slot_date,slot_time,vaccine_name,available_slots) VALUES ".implode(',',$slotDefs));
$log[] = "âœ… Slots inserted";

// â”€â”€ 5. GET PAST SLOT IDs â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$pastSlots = $conn->query("SELECT slot_id,vaccine_name,slot_date FROM slots WHERE slot_date < CURDATE() ORDER BY slot_date")->fetch_all(MYSQLI_ASSOC);

// â”€â”€ 6. INSERT BOOKINGS (30+) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$stmt = $conn->prepare("INSERT IGNORE INTO bookings (user_id,slot_id,status,booking_date) VALUES (?,?,?,?)");
$booking_ids_completed = [];
$bi = 0;
foreach ($pastSlots as $si => $slot) {
    // 2-3 users per past slot
    foreach (array_slice($userIds, $si % count($userIds), 3) as $uid) {
        $bdate = date('Y-m-d H:i:s', strtotime($slot['slot_date'].' -2 days'));
        $status = ($bi % 7 == 0) ? 'cancelled' : 'completed';
        $stmt->bind_param("iiss", $uid, $slot['slot_id'], $status, $bdate);
        $stmt->execute();
        if ($stmt->affected_rows > 0 && $status === 'completed') {
            $bid = $conn->insert_id;
            $booking_ids_completed[] = ['id'=>$bid,'uid'=>$uid,'vaccine'=>$slot['vaccine_name'],'date'=>$slot['slot_date']];
        }
        $bi++;
    }
}
// A few upcoming bookings
$futureSlots = $conn->query("SELECT slot_id,vaccine_name,slot_date FROM slots WHERE slot_date >= CURDATE() LIMIT 8")->fetch_all(MYSQLI_ASSOC);
foreach ($futureSlots as $si => $slot) {
    $uid   = $userIds[($si + 6) % count($userIds)];
    $bdate = date('Y-m-d H:i:s', strtotime('-1 day'));
    $status = 'booked';
    $stmt->bind_param("iiss", $uid, $slot['slot_id'], $status, $bdate);
    $stmt->execute();
}
$stmt->close();
$totalBookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0];
$log[] = "âœ… Bookings: $totalBookings total";

// â”€â”€ 7. INSERT CERTIFICATES â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$stmt = $conn->prepare("INSERT IGNORE INTO certificates (user_id,booking_id,vaccine_name,dose_number,vaccination_date,certificate_number) VALUES (?,?,?,1,?,?)");
$certs = 0;
foreach ($booking_ids_completed as $bk) {
    $certNum = 'VXCI-'.date('Y',strtotime($bk['date'])).'-'.str_pad($bk['id'],5,'0',STR_PAD_LEFT);
    $stmt->bind_param("iisss", $bk['uid'], $bk['id'], $bk['vaccine'], $bk['date'], $certNum);
    $stmt->execute();
    if ($stmt->affected_rows > 0) $certs++;
}
$stmt->close();
$log[] = "âœ… Certificates inserted: $certs";

// â”€â”€ 8. SUMMARY â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$summary = [
    'users'     => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'bookings'  => $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0],
    'certs'     => $conn->query("SELECT COUNT(*) FROM certificates")->fetch_row()[0],
    'stock'     => $conn->query("SELECT COUNT(*) FROM vaccine_stock")->fetch_row()[0],
    'slots'     => $conn->query("SELECT COUNT(*) FROM slots")->fetch_row()[0],
];
$conn->close();
?>
<!DOCTYPE html>
<html><head><title>VaxiCare Seeder</title>
<style>body{font-family:Inter,sans-serif;background:#0c0e1a;color:#e2e8f0;padding:40px;max-width:700px;margin:auto}
h2{color:#0ea5e9}pre{background:#111827;padding:20px;border-radius:12px;border:1px solid rgba(14,165,233,.2)}
.big{font-size:1.6rem;font-weight:800;color:#22c55e}.lbl{color:#64748b;font-size:.85rem}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:20px 0}
.box{background:#111827;border:1px solid rgba(14,165,233,.2);border-radius:12px;padding:16px;text-align:center}
a{color:#0ea5e9;text-decoration:none;padding:10px 24px;background:rgba(14,165,233,.15);border:1px solid rgba(14,165,233,.3);border-radius:8px;display:inline-block;margin-top:20px}
</style></head><body>
<h2>âœ… VaxiCare Seed Complete</h2>
<div class="grid">
    <?php foreach($summary as $k=>$v): ?>
    <div class="box"><div class="big"><?php echo $v; ?></div><div class="lbl"><?php echo ucfirst($k); ?></div></div>
    <?php endforeach; ?>
</div>
<pre><?php echo implode("\n",$log); ?></pre>
<p>Seeded user records are presentation data and cannot be used to log in.</p>
<a href="admin/dashboard.php">â†’ Go to Admin Dashboard</a>
<a href="<?php echo BASE_URL; ?>" style="margin-left:10px">â†’ Homepage</a>
<p style="color:#ef4444;margin-top:20px;font-size:.85rem">âš ï¸ Delete seed.php after use for security.</p>
</body></html>
