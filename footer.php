    <footer style="background: rgba(12,14,26,0.85); backdrop-filter: blur(16px); border-top: 1px solid rgba(14,165,233,0.15); padding: 60px 0 20px; margin-top: 60px;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="VaxiCare" style="width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 0 10px rgba(14,165,233,0.5));">
                        </div>
                        <span style="font-size:1.15rem;font-weight:800;background:linear-gradient(135deg,#0ea5e9,#6366f1);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">VaxiCare</span>
                    </div>
                    <p class="text-white-50" style="font-size:.88rem;line-height:1.7;">A digital vaccine registration system for seamless slot booking, certificate generation, and vaccine stock management across districts.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="text-white-50 fab"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white-50 fab"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white-50 fab"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white-50 fab"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>"><i class="fas fa-chevron-right me-1" style="font-size:.65rem;color:#0ea5e9;"></i>Home</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>about.php"><i class="fas fa-chevron-right me-1" style="font-size:.65rem;color:#0ea5e9;"></i>About</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>slots.php"><i class="fas fa-chevron-right me-1" style="font-size:.65rem;color:#0ea5e9;"></i>View Slots</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>contact.php"><i class="fas fa-chevron-right me-1" style="font-size:.65rem;color:#0ea5e9;"></i>Contact</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>User</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>register.php"><i class="fas fa-chevron-right me-1" style="font-size:.65rem;color:#0ea5e9;"></i>Register</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>login.php"><i class="fas fa-chevron-right me-1" style="font-size:.65rem;color:#0ea5e9;"></i>Login</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>dashboard.php"><i class="fas fa-chevron-right me-1" style="font-size:.65rem;color:#0ea5e9;"></i>Dashboard</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>certificates.php"><i class="fas fa-chevron-right me-1" style="font-size:.65rem;color:#0ea5e9;"></i>Certificates</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6>Contact Info</h6>
                    <p class="text-white-50 mb-2"><i class="fas fa-map-marker-alt me-2" style="color:#0ea5e9;"></i>Guwahati, Assam, India</p>
                    <p class="text-white-50 mb-2"><i class="fas fa-phone me-2" style="color:#0ea5e9;"></i>+91 1234567890</p>
                    <p class="text-white-50 mb-2"><i class="fas fa-envelope me-2" style="color:#0ea5e9;"></i>support@VaxiCare.com</p>
                    <p class="text-white-50 mb-0"><i class="fas fa-clock me-2" style="color:#0ea5e9;"></i>Mon – Sat: 9:00 AM – 6:00 PM</p>
                </div>
            </div>
            <hr style="border-color:rgba(14,165,233,0.2); margin: 40px 0 20px;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <p class="mb-0 text-white-50" style="font-size:.82rem;">&copy; <?php echo date('Y'); ?> VaxiCare – Online Vaccine Registration System. All rights reserved.</p>
                <p class="mb-0 text-white-50" style="font-size:.82rem;">BCA Final Year Project &nbsp;|&nbsp; Built with <i class="fas fa-heart" style="color:#ef4444;"></i></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    <!-- ── Global No Internet Detection ── -->
    <script src="<?php echo BASE_URL; ?>assets/js/no-internet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({ duration: 700, once: true, offset: 60 });
        // Initialize TomSelect only on plain dropdowns (not date/time/hidden/search inputs)
        document.querySelectorAll('select.form-select:not([data-ts-init]):not([data-no-ts])').forEach(function(el) {
            if (el.closest('.ts-wrapper')) return; // already initialized
            if (el.type === 'date' || el.type === 'time' || el.type === 'hidden') return;
            el.setAttribute('data-ts-init','1');
            new TomSelect(el, {
                create: false,
                sortField: { field: 'text', direction: 'asc' },
                render: {
                    option: function(data, escape) {
                        return '<div class="ts-option" style="color:#e2e8f0;padding:9px 14px;">' + escape(data.text) + '</div>';
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
