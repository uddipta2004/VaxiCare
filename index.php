<?php
$pageTitle = 'Home';
require_once 'config.php';
$conn = getDBConnection();
$totalUsers = getScalarValue($conn, "SELECT COUNT(*) FROM users WHERE status = 'approved'");
$totalBookings = getScalarValue($conn, "SELECT COUNT(*) FROM bookings WHERE status = 'completed'");
$totalCertificates = getScalarValue($conn, "SELECT COUNT(*) FROM certificates");
$availableSlots = getScalarValue($conn, "SELECT COALESCE(SUM(available_slots), 0) FROM slots WHERE slot_date >= CURDATE()");
$displayUsers = $totalUsers > 0 ? number_format($totalUsers) . '+' : '2,500+';
$displayBookings = $totalBookings > 0 ? number_format($totalBookings) . '+' : '1,800+';
$displayCertificates = $totalCertificates > 0 ? number_format($totalCertificates) . '+' : '1,200+';
$displaySlots = $availableSlots > 0 ? number_format($availableSlots) . '+' : '500+';
if ($totalUsers == 0 && $totalBookings == 0 && $totalCertificates == 0 && $availableSlots == 0) {
    $_SESSION['warning'] = $_SESSION['warning'] ?? 'Fresh setup detected. Import database.sql into vaccine_db from phpMyAdmin.';
}
$conn->close();
include 'header.php';
?>

<main class="home-main">
    <div class="heartbeat-bg"></div>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container hero-content-wrapper">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 hero-content" data-aos="fade-right">
                    <span style="display:inline-flex;align-items:center;gap:8px;background:rgba(14,165,233,.12);border:1px solid rgba(14,165,233,.4);border-radius:50px;padding:8px 20px;font-size:.8rem;font-weight:700;color:#0ea5e9;letter-spacing:1px;text-transform:uppercase;margin-bottom:24px;box-shadow: 0 0 20px rgba(14,165,233,0.35);">
                        🏥 AUTHORIZED HEALTHCARE PARTNER
                    </span>
                    <h1 class="hero-title">India's Trusted<br><span id="typewriterText" style="background:linear-gradient(135deg,#0ea5e9,#6366f1);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">VaxiCare</span><br>Platform</h1>
                    <p class="hero-subtitle my-4">Register online for vaccination, book your preferred slot at a nearby center, and download your digital certificate — all in one place.</p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo BASE_URL; ?>dashboard.php" class="btn btn-glass-primary">
                                <i class="fas fa-th-large me-2"></i>My Dashboard
                            </a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-glass-primary">
                                <i class="fas fa-user-plus me-2"></i>Register Now
                            </a>
                            <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-glass-secondary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-wrap gap-4 mt-5 pt-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <span style="color:rgba(255,255,255,.7);font-size:.85rem;">Free Registration</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <span style="color:rgba(255,255,255,.7);font-size:.85rem;">Digital Certificate</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <span style="color:rgba(255,255,255,.7);font-size:.85rem;">24/7 Access</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center d-flex justify-content-center" data-aos="fade-left" data-aos-delay="150">
                    <div class="hero-visual-core">
                        <div class="glow-orb orb-primary"></div>
                        <div class="glow-orb orb-secondary"></div>
                        <div class="glass-visual-card main-card">
                            <div class="card-icon" style="background:transparent; padding:0; width:60px; height:60px; margin-bottom:15px; border:none;">
                                <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="Logo" style="width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 0 15px rgba(14,165,233,0.6));">
                            </div>
                            <div class="card-info">
                                <h4 class="text-white fw-bold mb-1">VaxiCare System</h4>
                                <p style="color:rgba(255,255,255,.55);font-size:.82rem;margin-bottom:10px;">Online Vaccine Registration</p>
                                <div class="status-badge">
                                    <span class="pulse-dot"></span> Live &amp; Secure
                                </div>
                            </div>
                            <div class="floating-badge badge-1"><i class="fas fa-shield-alt"></i></div>
                            <div class="floating-badge badge-2"><i class="fas fa-heartbeat"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        
        <style>
            .hero-particles i { position: absolute; color: rgba(14,165,233,0.15); font-size: 2rem; z-index: 1; animation: floatParticle 12s infinite ease-in-out alternate; pointer-events: none; filter: blur(1px); }
            .hero-particles .p-1 { top: 15%; left: 10%; animation-delay: 0s; font-size: 3rem; }
            .hero-particles .p-2 { top: 25%; right: 15%; animation-delay: 2s; font-size: 2.2rem; }
            .hero-particles .p-3 { bottom: 30%; left: 20%; animation-delay: 4s; font-size: 2.8rem; color: rgba(34,197,94,0.12); }
            .hero-particles .p-4 { bottom: 20%; right: 25%; animation-delay: 1s; font-size: 2.5rem; color: rgba(99,102,241,0.15); }
            @keyframes floatParticle {
                0% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0.5; }
                50% { transform: translateY(-30px) translateX(15px) rotate(20deg); opacity: 1; }
                100% { transform: translateY(10px) translateX(-10px) rotate(-10deg); opacity: 0.6; }
            }
        </style>
        <div class="hero-particles">
            <i class="fas fa-dna p-1"></i>
            <i class="fas fa-shield-virus p-2"></i>
            <i class="fas fa-syringe p-3"></i>
            <i class="fas fa-heartbeat p-4"></i>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="pb-5 position-relative" style="z-index:3;margin-top:-10px;">
        <div class="container">
            <div class="stats-container d-flex justify-content-between align-items-center p-4" data-aos="fade-up">
                <div class="stat-item d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(14,165,233,.15);color:#0ea5e9;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white"><?php echo $displayUsers; ?></h3>
                        <span style="color:rgba(255,255,255,.6);font-size:.85rem;">Registered Users</span>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(34,197,94,.15);color:#22c55e;">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white"><?php echo $displayBookings; ?></h3>
                        <span style="color:rgba(255,255,255,.6);font-size:.85rem;">Vaccinations Done</span>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(99,102,241,.15);color:#6366f1;">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white"><?php echo $displayCertificates; ?></h3>
                        <span style="color:rgba(255,255,255,.6);font-size:.85rem;">Certificates Issued</span>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(245,158,11,.15);color:#f59e0b;">
                        <i class="far fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white"><?php echo $displaySlots; ?></h3>
                        <span style="color:rgba(255,255,255,.6);font-size:.85rem;">Available Slots</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="py-5 position-relative" style="z-index:2;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span style="color:#0ea5e9;font-weight:700;letter-spacing:2px;font-size:.8rem;text-transform:uppercase;">WHY CHOOSE VAXICARE?</span>
                <h2 class="mt-2 fw-bold text-white" style="font-size:2.2rem;">We Care For Your Health</h2>
                <p style="color:rgba(255,255,255,.55);font-size:.95rem;max-width:500px;margin:10px auto 0;">A comprehensive digital platform for vaccine registration, slot booking, and certificate management.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                    <div class="feature-card-modern p-4">
                        <div class="icon-box mb-4" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Secure &amp; Safe</h5>
                        <p style="color:rgba(255,255,255,.55);font-size:.88rem;margin:0;line-height:1.6;">Your data is protected with top-level encryption and government-grade security protocols.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card-modern p-4">
                        <div class="icon-box mb-4" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Easy Booking</h5>
                        <p style="color:rgba(255,255,255,.55);font-size:.88rem;margin:0;line-height:1.6;">Book your vaccination slot at your preferred center with just a few clicks, anytime.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card-modern p-4">
                        <div class="icon-box mb-4" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                            <i class="fas fa-file-certificate"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Digital Certificate</h5>
                        <p style="color:rgba(255,255,255,.55);font-size:.88rem;margin:0;line-height:1.6;">Download your official vaccination certificate instantly after completing your vaccination.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card-modern p-4">
                        <div class="icon-box mb-4" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">Instant Updates</h5>
                        <p style="color:rgba(255,255,255,.55);font-size:.88rem;margin:0;line-height:1.6;">Get real-time updates about slot availability, vaccination drives, and your booking status.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="py-5 position-relative" style="z-index:2;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span style="color:#0ea5e9;font-weight:700;letter-spacing:2px;font-size:.8rem;text-transform:uppercase;">SIMPLE PROCESS</span>
                <h2 class="mt-2 fw-bold text-white" style="font-size:2.2rem;">How It Works</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-3 text-center" data-aos="fade-up" data-aos-delay="0">
                    <div style="width:70px;height:70px;background:linear-gradient(135deg,#0ea5e9,#6366f1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#fff;box-shadow:0 0 25px rgba(14,165,233,.4);">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div style="width:36px;height:36px;background:rgba(14,165,233,.15);border:2px solid rgba(14,165,233,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-weight:800;color:#0ea5e9;font-size:.9rem;">1</div>
                    <h6 class="fw-bold text-white">Register</h6>
                    <p style="color:rgba(255,255,255,.5);font-size:.85rem;">Create your account with OTP verification</p>
                </div>
                <div class="col-md-3 text-center" data-aos="fade-up" data-aos-delay="100">
                    <div style="width:70px;height:70px;background:linear-gradient(135deg,#22c55e,#0ea5e9);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#fff;box-shadow:0 0 25px rgba(34,197,94,.4);">
                        <i class="fas fa-search"></i>
                    </div>
                    <div style="width:36px;height:36px;background:rgba(34,197,94,.15);border:2px solid rgba(34,197,94,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-weight:800;color:#22c55e;font-size:.9rem;">2</div>
                    <h6 class="fw-bold text-white">Find Slot</h6>
                    <p style="color:rgba(255,255,255,.5);font-size:.85rem;">Browse slots by district, date, and vaccine</p>
                </div>
                <div class="col-md-3 text-center" data-aos="fade-up" data-aos-delay="200">
                    <div style="width:70px;height:70px;background:linear-gradient(135deg,#6366f1,#f59e0b);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#fff;box-shadow:0 0 25px rgba(99,102,241,.4);">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <div style="width:36px;height:36px;background:rgba(99,102,241,.15);border:2px solid rgba(99,102,241,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-weight:800;color:#6366f1;font-size:.9rem;">3</div>
                    <h6 class="fw-bold text-white">Get Vaccinated</h6>
                    <p style="color:rgba(255,255,255,.5);font-size:.85rem;">Visit the center on your appointment date</p>
                </div>
                <div class="col-md-3 text-center" data-aos="fade-up" data-aos-delay="300">
                    <div style="width:70px;height:70px;background:linear-gradient(135deg,#f59e0b,#22c55e);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#fff;box-shadow:0 0 25px rgba(245,158,11,.4);">
                        <i class="fas fa-download"></i>
                    </div>
                    <div style="width:36px;height:36px;background:rgba(245,158,11,.15);border:2px solid rgba(245,158,11,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-weight:800;color:#f59e0b;font-size:.9rem;">4</div>
                    <h6 class="fw-bold text-white">Download Certificate</h6>
                    <p style="color:rgba(255,255,255,.5);font-size:.85rem;">Get your digital certificate instantly</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <?php if (!isLoggedIn()): ?>
    <section class="py-5" style="z-index:2;position:relative;">
        <div class="container">
            <div class="text-center p-5" style="background:linear-gradient(135deg,rgba(14,165,233,.12),rgba(99,102,241,.12));border:1px solid rgba(14,165,233,.25);border-radius:24px;" data-aos="zoom-in">
                <i class="fas fa-syringe" style="font-size:3rem;color:#0ea5e9;margin-bottom:20px;display:block;"></i>
                <h2 class="fw-bold text-white mb-3">Ready to Get Vaccinated?</h2>
                <p style="color:rgba(255,255,255,.6);max-width:400px;margin:0 auto 28px;">Join thousands of citizens who have already registered on VaxiCare and secured their health.</p>
                <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-glass-primary me-3">
                    <i class="fas fa-user-plus me-2"></i>Register Free
                </a>
                <a href="<?php echo BASE_URL; ?>slots.php" class="btn btn-glass-secondary">
                    <i class="fas fa-calendar-alt me-2"></i>View Slots
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php include 'footer.php'; ?>
