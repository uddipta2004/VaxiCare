<?php
$pageTitle = 'Download Certificate';
require_once 'config.php';
requireLogin();
if (!isset($_GET['id'])) { redirect('certificates.php'); }
$certificate_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT c.*, u.name, u.email, u.age, u.gender, s.slot_date, s.vaccine_name, ctr.center_name, d.district_name
                       FROM certificates c
                       JOIN users u ON c.user_id = u.user_id
                       JOIN bookings b ON c.booking_id = b.booking_id
                       JOIN slots s ON b.slot_id = s.slot_id
                       JOIN centers ctr ON s.center_id = ctr.center_id
                       JOIN districts d ON s.district_id = d.district_id
                       WHERE c.certificate_id = ? AND c.user_id = ?");
$stmt->bind_param("ii", $certificate_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) { $_SESSION['error'] = "Certificate not found."; redirect('certificates.php'); }
$cert = $result->fetch_assoc();
$stmt->close();
$conn->close();
$manufacturer = (stripos($cert['vaccine_name'], 'Covishield') !== false) ? 'Serum Institute of India' : ((stripos($cert['vaccine_name'], 'Covaxin') !== false) ? 'Bharat Biotech' : 'Authorized Manufacturer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Certificate – <?php echo htmlspecialchars($cert['certificate_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#0c0e1a 0%,#0f172a 50%,#0c1e35 100%); min-height:100vh; color:#e2e8f0; }

        /* ── ACTION BAR ── */
        .action-bar { position:fixed; top:0; left:0; right:0; z-index:100; background:rgba(12,14,26,.92); backdrop-filter:blur(20px); border-bottom:1px solid rgba(14,165,233,.15); padding:14px 24px; display:flex; justify-content:space-between; align-items:center; }
        .action-bar .brand { display:flex; align-items:center; gap:10px; font-weight:800; font-size:1.1rem; background:linear-gradient(135deg,#0ea5e9,#6366f1); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .action-bar .brand i { font-size:1.3rem; background:linear-gradient(135deg,#0ea5e9,#6366f1); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .btn-act { border-radius:50px; padding:8px 22px; font-weight:600; font-size:.88rem; border:none; transition:all .3s; cursor:pointer; display:inline-flex; align-items:center; gap:7px; }
        .btn-back { background:rgba(255,255,255,.08); color:#e2e8f0; border:1px solid rgba(255,255,255,.15); }
        .btn-back:hover { background:rgba(255,255,255,.15); color:#fff; }
        .btn-print { background:rgba(99,102,241,.2); color:#a5b4fc; border:1px solid rgba(99,102,241,.3); }
        .btn-print:hover { background:rgba(99,102,241,.35); color:#fff; }
        .btn-pdf { background:linear-gradient(135deg,#0ea5e9,#6366f1); color:#fff; box-shadow:0 4px 15px rgba(14,165,233,.35); }
        .btn-pdf:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(14,165,233,.5); color:#fff; }

        /* ── CERT WRAPPER ── */
        .cert-wrap { padding:100px 20px 60px; display:flex; justify-content:center; }
        .cert-box { width:100%; max-width:800px; background:rgba(255,255,255,.04); border:1px solid rgba(14,165,233,.2); border-radius:24px; overflow:hidden; box-shadow:0 30px 80px rgba(0,0,0,.6), 0 0 40px rgba(14,165,233,0.1) inset; position:relative; }
        .watermark { position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); font-size:25rem; color:rgba(255,255,255,0.08); pointer-events:none; z-index:0; text-shadow: 0 0 20px rgba(14,165,233,0.15); }

        /* ── HEADER STRIP ── */
        .cert-hdr { background:linear-gradient(135deg,#0ea5e9,#6366f1); padding:28px 40px; display:flex; align-items:center; justify-content:space-between; position:relative; z-index:1; }
        .cert-hdr-left { display:flex; align-items:center; gap:16px; }
        .cert-hdr-icon { width:56px; height:56px; background:rgba(255,255,255,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:#fff; }
        .cert-hdr h1 { color:#fff; font-weight:900; font-size:1.4rem; margin:0; letter-spacing:.3px; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .cert-hdr p { color:rgba(255,255,255,.9); font-size:.85rem; margin:2px 0 0; font-weight:500; }
        .cert-hdr-badge { background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.4); border-radius:50px; padding:6px 16px; font-size:.75rem; font-weight:700; color:#fff; letter-spacing:.5px; text-transform:uppercase; box-shadow:0 0 10px rgba(255,255,255,0.2); }

        /* ── CERT NUMBER BAND ── */
        .cert-num-band { background:rgba(14,165,233,.08); border-bottom:1px solid rgba(14,165,233,.15); padding:14px 40px; display:flex; align-items:center; justify-content:space-between; position:relative; z-index:1; }
        .cert-num-left { display:flex; align-items:center; gap:12px; }
        .cert-num-left span { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:#94a3b8; }
        .cert-num-left strong { font-size:1.1rem; color:#38bdf8; font-family:monospace; letter-spacing:1.5px; text-shadow:0 0 15px rgba(56,189,248,0.4); }
        .cert-num-right span { font-size:.7rem; font-weight:600; color:#64748b; background:rgba(255,255,255,.05); padding:4px 10px; border-radius:20px; border:1px solid rgba(255,255,255,.1); }

        /* ── CERT BODY ── */
        .cert-body { padding:36px 40px; position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:30px; }
        .cert-col { flex:1; min-width:300px; }
        .cert-section-title { font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:2px; color:#64748b; margin-bottom:18px; display:flex; align-items:center; gap:10px; }
        .cert-section-title::after { content:''; flex:1; height:1px; background:linear-gradient(90deg, rgba(255,255,255,.1), transparent); }
        .cert-row { display:flex; align-items:flex-start; padding:12px 0; border-bottom:1px dashed rgba(255,255,255,.08); gap:16px; }
        .cert-row:last-child { border-bottom:none; }
        .cert-row-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; box-shadow:0 4px 10px rgba(0,0,0,0.2); }
        .cert-row-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#cbd5e1; margin-bottom:4px; }
        .cert-row-value { font-size:.95rem; font-weight:600; color:#f8fafc; text-shadow:0 0 1px rgba(255,255,255,0.1); }

        /* ── QR + FOOTER ── */
        .cert-footer { border-top:1px solid rgba(14,165,233,.2); padding:30px 40px; display:flex; align-items:center; justify-content:space-between; gap:24px; background:rgba(12,14,26,.6); position:relative; z-index:1; border-radius:0 0 24px 24px; }
        .cert-qr-wrap { display:flex; flex-direction:column; align-items:center; gap:8px; }
        .qr-scan-text { font-size:.65rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#cbd5e1; margin-bottom:-2px; }
        .cert-qr-box { background:#fff; padding:8px; border-radius:8px; display:inline-block; box-shadow:0 0 15px rgba(255,255,255,0.1); }
        .cert-qr-url { font-size:.65rem; color:#cbd5e1; font-family:monospace; text-align:center; white-space:nowrap; }
        
        .cert-verified { display:inline-flex; align-items:center; gap:12px; padding:14px 28px; border-radius:50px; background:linear-gradient(135deg,rgba(34,197,94,.15),rgba(14,165,233,.1)); border:1px solid rgba(34,197,94,.4); box-shadow:0 0 20px rgba(34,197,94,0.1); }
        .cert-verified i { color:#4ade80; font-size:1.4rem; text-shadow:0 0 10px rgba(74,222,128,0.5); }
        .cert-verified-text h6 { margin:0; font-weight:800; font-size:.95rem; }
        .cert-verified-text p { margin:0; font-size:.75rem; color:#94a3b8; font-weight:500; }

        .signature-box { display:flex; align-items:flex-end; justify-content:flex-end; gap:15px; min-width:140px; }
        .seal-icon { color:#ef4444; font-size:3.5rem; opacity:0.85; transform:rotate(-15deg); text-shadow:0 0 15px rgba(239,68,68,0.3); }
        .signature-content { text-align:right; }
        .signature-content .sig-font { font-family:'Brush Script MT', 'Dancing Script', cursive; font-size:1.8rem; color:#38bdf8; transform:rotate(-4deg); margin-bottom:-4px; text-shadow:0 0 8px rgba(56,189,248,0.3); }
        .signature-content .sig-line { width:100%; height:1px; background:linear-gradient(90deg, transparent, rgba(56,189,248,.6)); margin-bottom:6px; }
        .signature-content .sig-label { font-size:.65rem; color:#64748b; text-transform:uppercase; letter-spacing:1px; font-weight:700; }

        /* ── HOLOGRAPHIC ── */
        .holographic { background:linear-gradient(135deg,#4ade80,#38bdf8,#818cf8,#4ade80); background-size:300% 300%; animation:hologram 4s ease infinite; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        @keyframes hologram { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }

        /* ── PRINT ── */
        @media print {
            @page { margin: 0; size: A4 portrait; }
            .action-bar, .no-print { display:none!important; }
            body { background:#fff!important; color:#0f172a!important; }
            .cert-wrap { padding:20px!important; align-items:flex-start; }
            .cert-box { border:2px solid #0ea5e9!important; box-shadow:none!important; border-radius:0!important; margin:0 auto; height:auto; page-break-inside: avoid; }
            .cert-hdr { -webkit-print-color-adjust:exact; print-color-adjust:exact; padding:20px 30px; }
            .cert-num-band { -webkit-print-color-adjust:exact; print-color-adjust:exact; background:rgba(14,165,233,.05)!important; }
            .cert-row-value { color:#0f172a!important; text-shadow:none!important; }
            .cert-row-label { color:#475569!important; }
            .cert-section-title { color:#334155!important; }
            .cert-section-title::after { background:#cbd5e1!important; }
            .cert-row { border-bottom:1px dashed #cbd5e1!important; }
            .cert-footer { border-top:1px solid #cbd5e1!important; background:#fff!important; border-radius:0!important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .watermark { display:none!important; }
            .cert-verified { background:rgba(34,197,94,.05)!important; border-color:#22c55e!important; box-shadow:none!important; }
            .holographic { background:none!important; -webkit-text-fill-color:#16a34a!important; color:#16a34a!important; }
            .signature-box .sig-font { color:#0ea5e9!important; text-shadow:none!important; }
        }
    </style>
</head>
<body>
    <!-- Action Bar -->
    <div class="action-bar no-print">
        <div class="brand"><img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="VaxiCare" style="height:24px; border-radius:4px; margin-right:8px;"> VaxiCare</div>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>certificates.php" class="btn-act btn-back"><i class="fas fa-arrow-left"></i> Back</a>
            <button onclick="window.print()" class="btn-act btn-print"><i class="fas fa-print"></i> Print</button>
            <button onclick="downloadPDF()" class="btn-act btn-pdf"><i class="fas fa-download"></i> Download PDF</button>
        </div>
    </div>

    <!-- Certificate -->
    <div class="cert-wrap">
        <div class="cert-box" id="certElement">
            <i class="fas fa-shield-virus watermark"></i>
            
            <!-- Header Strip -->
            <div class="cert-hdr">
                <div class="cert-hdr-left">
                    <div class="cert-hdr-icon" style="background:transparent;"><img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="VaxiCare Logo" style="width:100%;height:100%;object-fit:contain;border-radius:12px;"></div>
                    <div>
                        <h1>Vaccination Certificate</h1>
                        <p>VaxiCare Vaccination Authority</p>
                    </div>
                </div>
                <div class="cert-hdr-badge">✓ Official</div>
            </div>

            <!-- Certificate Number Band -->
            <div class="cert-num-band">
                <div class="cert-num-left">
                    <i class="fas fa-hashtag" style="color:#38bdf8;font-size:1rem;"></i>
                    <span>Certificate ID</span>
                    <strong><?php echo htmlspecialchars($cert['certificate_number']); ?></strong>
                </div>
                <div class="cert-num-right">
                    <span>Status: Completed & Verified</span>
                </div>
            </div>

            <!-- Body -->
            <div class="cert-body">
                <div class="cert-col">
                    <!-- Beneficiary Section -->
                    <div class="cert-section-title"><i class="fas fa-user-circle"></i> Beneficiary Details</div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(14,165,233,.12);color:#38bdf8;"><i class="fas fa-id-card"></i></div>
                        <div>
                            <div class="cert-row-label">Beneficiary ID</div>
                            <div class="cert-row-value"><?php echo 'BEN-' . str_pad($cert['user_id'], 6, '0', STR_PAD_LEFT); ?></div>
                        </div>
                    </div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(99,102,241,.12);color:#818cf8;"><i class="fas fa-user"></i></div>
                        <div>
                            <div class="cert-row-label">Full Name</div>
                            <div class="cert-row-value" style="font-size:1.1rem;font-weight:800;"><?php echo htmlspecialchars($cert['name']); ?></div>
                        </div>
                    </div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(236,72,153,.12);color:#f472b6;"><i class="fas fa-envelope"></i></div>
                        <div>
                            <div class="cert-row-label">Email Address</div>
                            <div class="cert-row-value"><?php echo htmlspecialchars($cert['email']); ?></div>
                        </div>
                    </div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(245,158,11,.12);color:#fbbf24;"><i class="fas fa-birthday-cake"></i></div>
                        <div>
                            <div class="cert-row-label">Age / Gender</div>
                            <div class="cert-row-value"><?php echo (!empty($cert['age']) && !empty($cert['gender'])) ? htmlspecialchars($cert['age']) . ' Yrs / ' . htmlspecialchars($cert['gender']) : 'Not Specified'; ?></div>
                        </div>
                    </div>
                </div>

                <div class="cert-col">
                    <!-- Vaccination Section -->
                    <div class="cert-section-title"><i class="fas fa-syringe"></i> Vaccination Details</div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(34,197,94,.12);color:#4ade80;"><i class="fas fa-vial"></i></div>
                        <div>
                            <div class="cert-row-label">Vaccine Name & Batch</div>
                            <div class="cert-row-value"><?php echo htmlspecialchars($cert['vaccine_name']); ?> <span style="font-size:0.8rem;color:#94a3b8;font-weight:normal;">(Batch: <?php echo 'BCH-'.date('ym', strtotime($cert['vaccination_date'])).str_pad($cert['booking_id'],4,'0',STR_PAD_LEFT); ?>)</span></div>
                            <div style="font-size:.82rem;color:#cbd5e1;"><?php echo htmlspecialchars($manufacturer); ?></div>
                        </div>
                    </div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(245,158,11,.12);color:#fbbf24;"><i class="fas fa-list-ol"></i></div>
                        <div>
                            <div class="cert-row-label">Dose Number</div>
                            <div class="cert-row-value">Dose <?php echo $cert['dose_number']; ?></div>
                        </div>
                    </div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(14,165,233,.12);color:#38bdf8;"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <div class="cert-row-label">Date of Vaccination</div>
                            <div class="cert-row-value"><?php echo date('d F Y', strtotime($cert['vaccination_date'])); ?></div>
                        </div>
                    </div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(99,102,241,.12);color:#818cf8;"><i class="fas fa-hospital"></i></div>
                        <div>
                            <div class="cert-row-label">Vaccination Center</div>
                            <div class="cert-row-value"><?php echo htmlspecialchars($cert['center_name']); ?></div>
                            <div style="font-size:.82rem;color:#94a3b8;"><?php echo htmlspecialchars($cert['district_name']); ?></div>
                        </div>
                    </div>
                    <div class="cert-row">
                        <div class="cert-row-icon" style="background:rgba(168,85,247,.12);color:#c084fc;"><i class="fas fa-shield-check"></i></div>
                        <div>
                            <div class="cert-row-label">Certificate Validity</div>
                            <div class="cert-row-value">Lifetime Valid</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="cert-footer">
                <div class="cert-qr-wrap">
                    <div class="qr-scan-text">Scan to Verify</div>
                    <div class="cert-qr-box" id="qrcode"></div>
                    <div class="cert-qr-url"><?php echo BASE_URL; ?>verify.php?id=<?php echo htmlspecialchars($cert['certificate_number']); ?></div>
                </div>
                <div class="text-center flex-grow-1">
                    <div class="cert-verified mx-auto">
                        <i class="fas fa-check-circle"></i>
                        <div class="cert-verified-text">
                            <h6 class="holographic">Verified Digital Certificate</h6>
                            <p>Issued by VaxiCare &mdash; Authorized Partner</p>
                        </div>
                    </div>
                </div>
                <div class="signature-box">
                    <i class="fas fa-stamp seal-icon"></i>
                    <div class="signature-content">
                        <div class="sig-font">VaxiCare Admin</div>
                        <div class="sig-line"></div>
                        <div class="sig-label">Authorized Signature</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "<?php echo BASE_URL; ?>verify.php?id=<?php echo htmlspecialchars($cert['certificate_number']); ?>",
            width: 90,
            height: 90,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        function downloadPDF() {
            const bar = document.querySelector('.action-bar');
            bar.style.display = 'none';
            // Slight delay to ensure QR is fully rendered before PDF creation
            setTimeout(() => {
                html2pdf().set({
                    margin: 0,
                    filename: 'VaxiCare_Certificate_<?php echo htmlspecialchars($cert['certificate_number']); ?>.pdf',
                    image: { type:'jpeg', quality:1.0 },
                    html2canvas: { scale:3, useCORS:true, backgroundColor:'#ffffff', letterRendering: true },
                    jsPDF: { unit:'mm', format:'a4', orientation:'portrait' }
                }).from(document.getElementById('certElement')).save().then(() => {
                    bar.style.display = 'flex';
                });
            }, 100);
        }
    </script>
</body>
</html>
