<?php
$pageTitle = 'My Certificates';
require_once 'config.php';
requireLogin();
$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT c.*, u.name, s.vaccine_name, ctr.center_name, d.district_name FROM certificates c JOIN users u ON c.user_id = u.user_id JOIN bookings b ON c.booking_id = b.booking_id JOIN slots s ON b.slot_id = s.slot_id JOIN centers ctr ON s.center_id = ctr.center_id JOIN districts d ON s.district_id = d.district_id WHERE c.user_id = ? ORDER BY c.generated_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$certificates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
include 'header.php';
?>

<main>
<div class="container py-5">
    <div class="page-header d-flex justify-content-between align-items-center" data-aos="fade-right">
        <h3><i class="fas fa-certificate me-2" style="color:#f59e0b;"></i>My Certificates</h3>
        <a href="<?php echo BASE_URL; ?>bookings.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to Bookings
        </a>
    </div>

    <?php if (count($certificates) > 0): ?>
        <div class="card" data-aos="fade-up">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Certificate #</th>
                                <th>Vaccine</th>
                                <th>Dose</th>
                                <th>Date</th>
                                <th>Center</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certificates as $cert): ?>
                                <tr>
                                    <td>
                                        <span style="color:#f59e0b;font-weight:600;font-family:monospace;">
                                            <?php echo htmlspecialchars($cert['certificate_number']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($cert['vaccine_name']); ?></td>
                                    <td><span class="badge bg-info">Dose <?php echo (int) $cert['dose_number']; ?></span></td>
                                    <td><?php echo date('d M Y', strtotime($cert['vaccination_date'])); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($cert['center_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($cert['district_name']); ?></small>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>download-certificate.php?id=<?php echo $cert['certificate_id']; ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
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
            <i class="fas fa-certificate"></i>
            <h4>No Certificates Yet</h4>
            <p>Certificates are issued after an admin marks your vaccination as completed.</p>
            <a href="<?php echo BASE_URL; ?>bookings.php" class="btn btn-primary"><i class="fas fa-calendar-check me-2"></i>View Bookings</a>
        </div>
    <?php endif; ?>
</div>
</main>

<?php include 'footer.php'; ?>
