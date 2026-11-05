<?php
$pageTitle = 'Dashboard';
require_once 'config.php';
requireLogin();
$conn = getDBConnection();
ensureCoreTables($conn);
$user_id = $_SESSION['user_id'];
$user = fetchOnePrepared($conn, "SELECT * FROM users WHERE user_id = ?", "i", $user_id);
if (!$user) { session_destroy(); session_start(); $_SESSION['error'] = 'Account not found. Please login again.'; redirect('login.php'); }
$_SESSION['user_name'] = $user['name'];
$upcomingBookings = fetchAllPrepared($conn, "SELECT b.*, s.slot_date, s.slot_time, s.vaccine_name, c.center_name, d.district_name FROM bookings b JOIN slots s ON b.slot_id = s.slot_id JOIN centers c ON s.center_id = c.center_id JOIN districts d ON s.district_id = d.district_id WHERE b.user_id = ? AND s.slot_date >= CURDATE() AND b.status = 'booked' ORDER BY s.slot_date, s.slot_time", "i", $user_id);
$pastVaccinations = fetchAllPrepared($conn, "SELECT b.*, s.slot_date, s.vaccine_name, c.center_name, d.district_name, cert.certificate_id, cert.certificate_number FROM bookings b JOIN slots s ON b.slot_id = s.slot_id JOIN centers c ON s.center_id = c.center_id JOIN districts d ON s.district_id = d.district_id LEFT JOIN certificates cert ON b.booking_id = cert.booking_id WHERE b.user_id = ? AND (b.status = 'completed' OR s.slot_date < CURDATE()) ORDER BY s.slot_date DESC", "i", $user_id);
$certificateCount = getScalarValue($conn, "SELECT COUNT(*) FROM certificates WHERE user_id = " . intval($user_id));
$totalBookings = getScalarValue($conn, "SELECT COUNT(*) FROM bookings WHERE user_id = " . intval($user_id));
$conn->close();
$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
include 'header.php';
?>

<main>
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="dashboard-sidebar-card" data-aos="fade-right">
                <div class="text-center p-4 pb-3">
                    <?php if ($user['photo'] && file_exists($user['photo'])): ?>
                        <img src="<?php echo BASE_URL . $user['photo']; ?>" alt="Profile" class="dashboard-avatar mb-3">
                    <?php else: ?>
                        <div class="dashboard-avatar-placeholder mb-3">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <h5 class="text-white fw-700 mb-1"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p style="color:#64748b;font-size:.82rem;margin-bottom:10px;"><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="badge bg-<?php echo $user['status']=='approved'?'success':($user['status']=='pending'?'warning':'danger'); ?>">
                        <i class="fas fa-<?php echo $user['status']=='approved'?'check-circle':($user['status']=='pending'?'clock':'times-circle'); ?> me-1"></i>
                        <?php echo ucfirst($user['status']); ?>
                    </span>
                </div>
                <nav>
                    <a href="<?php echo BASE_URL; ?>dashboard.php" class="dashboard-nav-link active">
                        <i class="fas fa-th-large"></i>Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>profile.php" class="dashboard-nav-link">
                        <i class="fas fa-user-edit"></i>Edit Profile
                    </a>
                    <a href="<?php echo BASE_URL; ?>bookings.php" class="dashboard-nav-link">
                        <i class="fas fa-calendar-check"></i>My Bookings
                    </a>
                    <a href="<?php echo BASE_URL; ?>certificates.php" class="dashboard-nav-link">
                        <i class="fas fa-certificate"></i>Certificates
                    </a>
                    <a href="<?php echo BASE_URL; ?>slots.php" class="dashboard-nav-link">
                        <i class="fas fa-calendar-plus"></i>Book New Slot
                    </a>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="dashboard-nav-link" style="color:rgba(239,68,68,.8);">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9" data-aos="fade-left">
            <!-- Greeting Banner -->
            <div class="greeting-banner" data-aos="fade-down">
                <h4 class="fw-bold text-white mb-1"><?php echo $greeting; ?>, <?php echo htmlspecialchars($user['name']); ?> 👋</h4>
                <p style="color:rgba(255,255,255,.55);margin:0;font-size:.9rem;">
                    <?php if ($user['status'] == 'approved'): ?>
                        Your account is active. <a href="<?php echo BASE_URL; ?>slots.php" class="auth-link">Book your next vaccination slot &rarr;</a>
                    <?php else: ?>
                        Your account is <strong style="color:#f59e0b;"><?php echo ucfirst($user['status']); ?></strong>. You'll be notified once approved.
                    <?php endif; ?>
                </p>
            </div>

            <!-- Stat Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="dashboard-stats dashboard-card" data-aos="fade-up" data-aos-delay="0">
                        <div class="dashboard-stats-icon blue"><i class="fas fa-calendar-check"></i></div>
                        <div class="dashboard-stats-info">
                            <h3><?php echo $totalBookings; ?></h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-stats dashboard-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="dashboard-stats-icon green"><i class="fas fa-syringe"></i></div>
                        <div class="dashboard-stats-info">
                            <h3><?php echo count($pastVaccinations); ?></h3>
                            <p>Vaccinations Done</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-stats dashboard-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="dashboard-stats-icon orange"><i class="fas fa-certificate"></i></div>
                        <div class="dashboard-stats-info">
                            <h3><?php echo $certificateCount; ?></h3>
                            <p>Certificates</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments - Timeline -->
            <div class="card mb-4" data-aos="fade-up" data-aos-delay="50">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0"><i class="fas fa-calendar me-2 text-primary"></i>Upcoming Appointments</h5>
                    <a href="<?php echo BASE_URL; ?>slots.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Book Slot</a>
                </div>
                <div class="card-body">
                    <?php if (count($upcomingBookings) > 0): ?>
                        <div class="vax-timeline">
                            <?php foreach ($upcomingBookings as $booking): ?>
                            <div class="vax-timeline-item">
                                <div class="vax-timeline-dot"></div>
                                <div class="vax-timeline-card">
                                    <div class="vax-timeline-date"><?php echo date('D, d M Y', strtotime($booking['slot_date'])); ?> &middot; <?php echo date('h:i A', strtotime($booking['slot_time'])); ?></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-white fw-700 mb-0"><?php echo htmlspecialchars($booking['vaccine_name']); ?></h6>
                                            <small class="text-muted"><i class="fas fa-hospital me-1"></i><?php echo htmlspecialchars($booking['center_name']); ?>, <?php echo htmlspecialchars($booking['district_name']); ?></small>
                                        </div>
                                        <span class="badge bg-primary">Booked</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h4>No Upcoming Appointments</h4>
                            <p>You have no scheduled vaccination appointments.</p>
                            <a href="<?php echo BASE_URL; ?>slots.php" class="btn btn-primary btn-sm"><i class="fas fa-calendar-plus me-1"></i>Book a Slot</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vaccination History -->
            <div class="card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header">
                    <h5 class="m-0"><i class="fas fa-history me-2 text-success"></i>Vaccination History</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($pastVaccinations) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead><tr><th>Date</th><th>Vaccine</th><th>Center</th><th>Certificate</th></tr></thead>
                                <tbody>
                                    <?php foreach ($pastVaccinations as $v): ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($v['slot_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($v['vaccine_name']); ?></td>
                                        <td><?php echo htmlspecialchars($v['center_name']); ?><br><small class="text-muted"><?php echo htmlspecialchars($v['district_name']); ?></small></td>
                                        <td>
                                            <?php if ($v['certificate_id']): ?>
                                                <a href="<?php echo BASE_URL; ?>download-certificate.php?id=<?php echo $v['certificate_id']; ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-download me-1"></i>Download
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-syringe"></i>
                            <h4>No Vaccination History</h4>
                            <p>Your completed vaccinations will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<?php include 'footer.php'; ?>
