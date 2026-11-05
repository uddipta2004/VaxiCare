<?php
$pageTitle = 'Confirm Booking';
require_once 'config.php';
requireLogin();
if (!isset($_GET['id'])) { redirect('slots.php'); }
$slot_id = intval($_GET['id']);
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT status FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user_status = $stmt->get_result()->fetch_assoc()['status'];
$stmt->close();
if ($user_status != 'approved') { $_SESSION['warning'] = "Your account is pending admin approval."; redirect('slots.php'); }
$stmt = $conn->prepare("SELECT s.*, d.district_name, c.center_name, c.address, c.contact FROM slots s JOIN districts d ON s.district_id = d.district_id JOIN centers c ON s.center_id = c.center_id WHERE s.slot_id = ? AND s.available_slots > 0 AND s.slot_date >= CURDATE()");
$stmt->bind_param("i", $slot_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) { $_SESSION['error'] = "Slot not available or already full."; redirect('slots.php'); }
$slot = $result->fetch_assoc();
$stmt->close();
$stmt = $conn->prepare("SELECT booking_id FROM bookings WHERE user_id = ? AND slot_id = ? AND status != 'cancelled'");
$stmt->bind_param("ii", $_SESSION['user_id'], $slot_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) { $_SESSION['warning'] = "You have already booked this slot."; redirect('bookings.php'); }
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    requireCSRF();
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, slot_id, status) VALUES (?, ?, 'booked')");
        $stmt->bind_param("ii", $_SESSION['user_id'], $slot_id);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("UPDATE slots SET available_slots = available_slots - 1 WHERE slot_id = ? AND available_slots > 0");
        $stmt->bind_param("i", $slot_id);
        $stmt->execute();
        if ($stmt->affected_rows == 0) { throw new Exception("Slot is no longer available."); }
        $stmt->close();
        $conn->commit();
        $_SESSION['success'] = "Slot booked successfully! Please visit the center on the scheduled date.";
        redirect('bookings.php');
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        redirect('slots.php');
    }
}
$conn->close();
include 'header.php';
?>

<main>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6" data-aos="fade-up">
            <!-- Alert Info -->
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Please review the vaccination slot details carefully before confirming your booking.
            </div>

            <div class="card">
                <div class="card-header text-center">
                    <h4 class="m-0"><i class="fas fa-calendar-check me-2 text-primary"></i>Confirm Your Booking</h4>
                </div>
                <div class="card-body">
                    <!-- Center Info -->
                    <div class="p-3 mb-3" style="background:rgba(14,165,233,.08);border-radius:12px;border:1px solid rgba(14,165,233,.15);">
                        <h6 style="color:#94a3b8;font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Vaccination Center</h6>
                        <h5 class="text-white fw-bold mb-2"><?php echo htmlspecialchars($slot['center_name']); ?></h5>
                        <p class="mb-1" style="font-size:.88rem;"><i class="fas fa-map-marker-alt me-2 text-primary"></i><?php echo htmlspecialchars($slot['address']); ?></p>
                        <p class="mb-1" style="font-size:.88rem;"><i class="fas fa-phone me-2 text-primary"></i><?php echo htmlspecialchars($slot['contact']); ?></p>
                        <p class="mb-0" style="font-size:.88rem;"><i class="fas fa-city me-2 text-primary"></i><?php echo htmlspecialchars($slot['district_name']); ?></p>
                    </div>

                    <!-- Slot Details -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3" style="background:rgba(34,197,94,.08);border-radius:12px;border:1px solid rgba(34,197,94,.15);text-align:center;">
                                <i class="fas fa-calendar" style="color:#22c55e;font-size:1.3rem;margin-bottom:8px;display:block;"></i>
                                <small class="text-muted d-block">Date</small>
                                <strong class="text-white"><?php echo date('d M Y', strtotime($slot['slot_date'])); ?></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3" style="background:rgba(99,102,241,.08);border-radius:12px;border:1px solid rgba(99,102,241,.15);text-align:center;">
                                <i class="fas fa-clock" style="color:#6366f1;font-size:1.3rem;margin-bottom:8px;display:block;"></i>
                                <small class="text-muted d-block">Time</small>
                                <strong class="text-white"><?php echo date('h:i A', strtotime($slot['slot_time'])); ?></strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3" style="background:rgba(245,158,11,.08);border-radius:12px;border:1px solid rgba(245,158,11,.15);text-align:center;">
                                <i class="fas fa-syringe" style="color:#f59e0b;font-size:1.3rem;margin-bottom:8px;display:block;"></i>
                                <small class="text-muted d-block">Vaccine</small>
                                <strong class="text-white"><?php echo htmlspecialchars($slot['vaccine_name']); ?></strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <form method="POST">
                        <?php echo csrfField(); ?>
                        <div class="d-flex gap-3">
                            <a href="<?php echo BASE_URL; ?>slots.php" class="btn btn-secondary w-50">
                                <i class="fas fa-arrow-left me-2"></i>Go Back
                            </a>
                            <button type="submit" class="btn btn-primary w-50">
                                <i class="fas fa-check-circle me-2"></i>Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<?php include 'footer.php'; ?>
