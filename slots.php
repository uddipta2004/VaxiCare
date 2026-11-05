<?php
$pageTitle = 'View Slots';
include 'header.php';
$conn = getDBConnection();
if (isLoggedIn()) {
    $stmt = $conn->prepare("SELECT status FROM users WHERE user_id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Fetch all districts for pill filters
$dResult = $conn->query("SELECT * FROM districts ORDER BY district_name");
$all_districts = [];
while ($d = $dResult->fetch_assoc()) $all_districts[] = $d;

$selected_district = isset($_GET['district']) ? intval($_GET['district']) : 0;
$selected_date = isset($_GET['date']) ? trim($_GET['date']) : '';
if ($selected_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) { $selected_date = ''; }

$query = "SELECT s.*, d.district_name, c.center_name, c.address, c.contact
          FROM slots s
          JOIN districts d ON s.district_id = d.district_id
          JOIN centers c ON s.center_id = c.center_id
          WHERE s.slot_date >= CURDATE() AND s.available_slots >= 0";
if ($selected_district) { $query .= " AND s.district_id = " . intval($selected_district); }
if ($selected_date) { $query .= " AND s.slot_date = '" . $conn->real_escape_string($selected_date) . "'"; }
$query .= " ORDER BY s.slot_date, s.slot_time";
$slots = $conn->query($query);
$conn->close();

// Vaccine color map
$vaccineColors = [
    'Covishield' => ['#0ea5e9', 'rgba(14,165,233,.13)'],
    'Covaxin'    => ['#6366f1', 'rgba(99,102,241,.13)'],
    'Corbevax'   => ['#22c55e', 'rgba(34,197,94,.13)'],
];
?>

<main>
<div class="container py-5">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-right">
        <div>
            <span style="color:#0ea5e9;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;">Vaccination Slots</span>
            <h3 class="fw-bold mb-0 mt-1">Available Slots</h3>
        </div>
        <?php if (!isLoggedIn()): ?>
            <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus me-1"></i>Register to Book
            </a>
        <?php endif; ?>
    </div>

    <!-- District Pill Filters -->
    <div data-aos="fade-up" class="mb-3">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="slots.php<?php echo $selected_date ? '?date='.$selected_date : ''; ?>"
               class="filter-pill <?php echo !$selected_district ? 'active' : ''; ?>">
                <i class="fas fa-globe-asia me-1"></i>All Districts
            </a>
            <?php foreach ($all_districts as $d): ?>
            <a href="?district=<?php echo $d['district_id']; ?><?php echo $selected_date ? '&date='.$selected_date : ''; ?>"
               class="filter-pill <?php echo $selected_district == $d['district_id'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($d['district_name']); ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Date Filter -->
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <?php if ($selected_district): ?>
                <input type="hidden" name="district" value="<?php echo $selected_district; ?>">
            <?php endif; ?>
            <input type="date" name="date" class="form-control"
                   style="max-width:200px;background:rgba(255,255,255,.07);border:1.5px solid rgba(14,165,233,.25);border-radius:10px;color:#fff;"
                   value="<?php echo $selected_date; ?>" min="<?php echo date('Y-m-d'); ?>">
            <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            <?php if ($selected_date || $selected_district): ?>
                <a href="slots.php" class="btn btn-secondary btn-sm px-3">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Slots Grid -->
    <?php if ($slots && $slots->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($slot = $slots->fetch_assoc()):
                $isFull = $slot['available_slots'] <= 0;
                // Availability bar: cap at 100 slots for visual, red/yellow/green
                $pct = min(100, max(0, $slot['available_slots']));
                $urgencyColor = $slot['available_slots'] > 30 ? '#22c55e'
                              : ($slot['available_slots'] > 10  ? '#f59e0b' : '#ef4444');
                [$vColor, $vBg] = $vaccineColors[$slot['vaccine_name']] ?? ['#22c55e', 'rgba(34,197,94,.13)'];
            ?>
            <div class="col-lg-6" data-aos="fade-up">
                <div class="slot-card <?php echo $isFull ? '' : 'available'; ?>"
                     style="border-left:4px solid <?php echo $vColor; ?>;">

                    <!-- Top Row: Name + Badges -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1 fw-bold"><?php echo htmlspecialchars($slot['center_name']); ?></h5>
                            <p class="text-muted mb-0 small">
                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($slot['district_name']); ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <!-- Vaccine badge -->
                            <span class="badge mb-1 d-block"
                                  style="background:<?php echo $vBg; ?>;color:<?php echo $vColor; ?>;border:1px solid <?php echo $vColor; ?>40;font-size:.72rem;">
                                <i class="fas fa-syringe me-1"></i><?php echo htmlspecialchars($slot['vaccine_name']); ?>
                            </span>
                            <!-- Slot count badge -->
                            <span class="badge <?php echo $isFull ? 'bg-danger' : 'bg-success'; ?>">
                                <i class="fas <?php echo $isFull ? 'fa-times-circle' : 'fa-check-circle'; ?> me-1"></i>
                                <?php echo $slot['available_slots']; ?> Slots
                            </span>
                        </div>
                    </div>

                    <!-- Availability Progress Bar -->
                    <div class="mb-3">
                        <div style="display:flex;justify-content:space-between;font-size:.7rem;color:#64748b;margin-bottom:5px;">
                            <span>Slot Availability</span>
                            <span><?php echo $slot['available_slots']; ?> remaining</span>
                        </div>
                        <div style="height:5px;background:rgba(255,255,255,.07);border-radius:3px;overflow:hidden;">
                            <div style="width:<?php echo $pct; ?>%;height:100%;background:<?php echo $urgencyColor; ?>;border-radius:3px;transition:width 1.2s ease;"></div>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div style="background:rgba(14,165,233,.08);border-radius:10px;padding:10px;">
                                <small class="text-muted d-block"><i class="fas fa-calendar me-1"></i>Date</small>
                                <strong class="text-white"><?php echo date('d M Y', strtotime($slot['slot_date'])); ?></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="background:rgba(99,102,241,.08);border-radius:10px;padding:10px;">
                                <small class="text-muted d-block"><i class="fas fa-clock me-1"></i>Time</small>
                                <strong class="text-white"><?php echo date('h:i A', strtotime($slot['slot_time'])); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Address + Contact -->
                    <p class="text-muted small mb-3" style="font-size:.8rem;">
                        <i class="fas fa-location-arrow me-1"></i><?php echo htmlspecialchars($slot['address']); ?>
                        &nbsp;|&nbsp;
                        <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($slot['contact']); ?>
                    </p>

                    <!-- Book Button -->
                    <?php if (isLoggedIn()): ?>
                        <?php if (!$isFull && isset($user['status']) && $user['status'] === 'approved'): ?>
                            <a href="book-slot.php?id=<?php echo $slot['slot_id']; ?>" class="btn btn-primary w-100">
                                <i class="fas fa-calendar-check me-2"></i>Book This Slot
                            </a>
                        <?php elseif (!isset($user['status']) || $user['status'] !== 'approved'): ?>
                            <button class="btn btn-warning w-100" disabled>
                                <i class="fas fa-clock me-2"></i>Pending Account Approval
                            </button>
                        <?php else: ?>
                            <button class="btn btn-danger w-100" disabled>
                                <i class="fas fa-times-circle me-2"></i>Slot Full
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state" data-aos="fade-up">
            <i class="fas fa-calendar-times"></i>
            <h4>No Slots Available</h4>
            <p>No vaccination slots found for the selected criteria. Try different filters.</p>
            <?php if ($selected_district || $selected_date): ?>
                <a href="slots.php" class="btn btn-primary btn-sm mt-2">
                    <i class="fas fa-undo me-1"></i>View All Slots
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>
</main>

<?php include 'footer.php'; ?>
