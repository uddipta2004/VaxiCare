<?php
http_response_code(404);
$pageTitle = '404 Not Found';
require_once 'config.php';
include 'header.php';
?>
<main class="d-flex align-items-center justify-content-center" style="min-height: 80vh; padding: 40px 0;">
    <div class="container text-center" data-aos="fade-up">
        <div class="glass-visual-card mx-auto" style="max-width: 600px; padding: 60px 40px; border-radius: 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(14,165,233,0.15); box-shadow: 0 30px 80px rgba(0,0,0,0.5);">
            <div style="font-size: 6rem; font-weight: 900; line-height: 1; background: linear-gradient(135deg, #0ea5e9, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 20px;">
                404
            </div>
            <div style="font-size: 3rem; color: rgba(14,165,233,0.2); margin-bottom: -50px; transform: translateY(-70px); pointer-events: none;">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h2 class="text-white fw-bold mb-3">Signal Lost</h2>
            <p style="color: #94a3b8; font-size: 1.1rem; margin-bottom: 40px;">
                The page you are looking for has been moved, deleted, or does not exist. Let's get you back to safety.
            </p>
            <a href="<?php echo BASE_URL; ?>" class="btn btn-primary px-5 py-3" style="border-radius: 50px; font-weight: 700; letter-spacing: 1px; box-shadow: 0 10px 25px rgba(14,165,233,0.4);">
                <i class="fas fa-home me-2"></i>Return to Base
            </a>
        </div>
    </div>
</main>
<?php include 'footer.php'; ?>
