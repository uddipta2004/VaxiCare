<?php
$pageTitle = 'My Bookings';
require_once 'config.php';
requireLogin();
$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT b.*, s.slot_date, s.slot_time, s.vaccine_name, c.center_name, d.district_name, cert.certificate_id, cert.certificate_number FROM bookings b JOIN slots s ON b.slot_id = s.slot_id JOIN centers c ON s.center_id = c.center_id JOIN districts d ON s.district_id = d.district_id LEFT JOIN certificates cert ON b.booking_id = cert.booking_id WHERE b.user_id = ? ORDER BY s.slot_date DESC, s.slot_time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
include 'header.php';
?>

<main>
<div class="container py-5">
    <div class="page-header d-flex justify-content-between align-items-center" data-aos="fade-right">
        <h3><i class="fas fa-calendar-check me-2" style="color:#0ea5e9;"></i>My Bookings</h3>
        <a href="<?php echo BASE_URL; ?>slots.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Book New Slot
        </a>
    </div>

    <?php if (count($bookings) > 0): ?>
        <div class="card" data-aos="fade-up">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Date &amp; Time</th>
                                <th>Center</th>
                                <th>Vaccine</th>
                                <th>Status</th>
                                <th>Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <?php
                                $statusClass = '';
                                switch($booking['status']) {
                                    case 'booked': $statusClass = 'bg-primary'; break;
                                    case 'completed': $statusClass = 'bg-success'; break;
                                    case 'cancelled': $statusClass = 'bg-danger'; break;
                                }
                                ?>
                                <tr>
                                    <td><span style="color:#0ea5e9;font-weight:600;">#<?php echo $booking['booking_id']; ?></span></td>
                                    <td>
                                        <?php echo date('d M Y', strtotime($booking['slot_date'])); ?><br>
                                        <small class="text-muted"><?php echo date('h:i A', strtotime($booking['slot_time'])); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($booking['center_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['district_name']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['vaccine_name']); ?></td>
                                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                    <td>
                                        <?php if ($booking['certificate_id']): ?>
                                            <a href="<?php echo BASE_URL; ?>download-certificate.php?id=<?php echo $booking['certificate_id']; ?>" class="btn btn-success btn-sm">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        <?php elseif ($booking['status'] == 'completed'): ?>
                                            <span class="badge bg-warning">Processing</span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state" data-aos="fade-up">
            <i class="fas fa-calendar-times"></i>
            <h4>No Bookings Yet</h4>
            <p>You haven't booked any vaccination slots yet.</p>
            <a href="<?php echo BASE_URL; ?>slots.php" class="btn btn-primary"><i class="fas fa-calendar-plus me-2"></i>Book a Slot</a>
        </div>
    <?php endif; ?>
</div>
</main>

<?php include 'footer.php'; ?>
