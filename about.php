<?php
$pageTitle = 'About Us';
include 'header.php';
?>

<main>
    <div class="auth-page">
        <!-- Background Icons -->
        <i class="fas fa-heartbeat auth-bg-icon auth-bg-icon-1"></i>
        <i class="fas fa-shield-virus auth-bg-icon auth-bg-icon-2"></i>
        <i class="fas fa-dna auth-bg-icon auth-bg-icon-3"></i>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <!-- Header -->
                    <div class="text-center mb-5" data-aos="fade-up">
                        <span class="text-primary fw-bold text-uppercase" style="letter-spacing:2px;font-size:.8rem">About VaxiCare</span>
                        <h2 class="mt-2" style="font-weight:800">Online Vaccine Registration System</h2>
                        <p class="text-white-50 mx-auto" style="max-width:600px">A comprehensive web-based solution for managing vaccine registration, slot booking, and certificate generation efficiently.</p>
                    </div>

                    <div class="row g-4 mb-5">
                        <!-- Mission -->
                        <div class="col-md-6" data-aos="fade-right">
                            <div class="card h-100">
                                <div class="card-body p-4">
                                    <h5><i class="fas fa-bullseye text-primary me-2"></i>Our Mission</h5>
                                    <p class="text-white-50 mb-3">To provide a seamless digital platform for vaccine registration:</p>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Easy online registration</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Convenient slot booking</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Digital certificate generation</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Real-time stock management</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Features -->
                        <div class="col-md-6" data-aos="fade-left">
                            <div class="card h-100">
                                <div class="card-body p-4">
                                    <h5><i class="fas fa-star text-primary me-2"></i>Key Features</h5>
                                    <p class="text-white-50 mb-3">The system offers a wide range of features:</p>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>OTP & Captcha verification</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>District-wise centers</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Admin verification system</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Daily report generation</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- How It Works -->
                    <div class="card mb-5" data-aos="fade-up">
                        <div class="card-body p-4">
                            <h5 class="mb-4 text-center">How It Works</h5>
                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <div class="auth-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.2rem"><strong>1</strong></div>
                                    <h6>Register</h6>
                                    <small class="text-muted">Create account with OTP verification</small>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="auth-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.2rem"><strong>2</strong></div>
                                    <h6>Book Slot</h6>
                                    <small class="text-muted">Select center and preferred time</small>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="auth-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.2rem"><strong>3</strong></div>
                                    <h6>Get Vaccinated</h6>
                                    <small class="text-muted">Visit center on scheduled date</small>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="auth-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.2rem"><strong>4</strong></div>
                                    <h6>Certificate</h6>
                                    <small class="text-muted">Download digital certificate</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tech Stack -->
                    <div class="text-center" data-aos="fade-up">
                        <h5 class="mb-3">Technology Stack</h5>
                        <div class="d-flex justify-content-center flex-wrap gap-3">
                            <span class="badge bg-primary fs-6">HTML5</span>
                            <span class="badge bg-success fs-6">CSS3</span>
                            <span class="badge bg-warning fs-6">JavaScript</span>
                            <span class="badge bg-info fs-6">Bootstrap</span>
                            <span class="badge bg-secondary fs-6">PHP</span>
                            <span class="badge bg-primary fs-6">MySQL</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
