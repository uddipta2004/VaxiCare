<?php
$pageTitle = 'Verify Certificate';
require_once 'config.php';

$cert_data = null;
$error = null;
$search_id = '';

if (isset($_GET['id'])) {
    $search_id = trim($_GET['id']);
    
    if (!empty($search_id)) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT c.*, u.name as beneficiary_name, s.vaccine_name, s.slot_date, ctr.center_name, d.district_name 
                                FROM certificates c
                                JOIN users u ON c.user_id = u.user_id
                                JOIN bookings b ON c.booking_id = b.booking_id
                                JOIN slots s ON b.slot_id = s.slot_id
                                JOIN centers ctr ON s.center_id = ctr.center_id
                                JOIN districts d ON s.district_id = d.district_id
                                WHERE c.certificate_number = ?");
        $stmt->bind_param("s", $search_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $cert_data = $result->fetch_assoc();
        } else {
            $error = "Invalid Certificate ID. This certificate could not be found or is not authentic.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate - VaxiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#0c0e1a 0%,#0f172a 50%,#0c1e35 100%); min-height:100vh; color:#e2e8f0; display:flex; align-items:center; justify-content:center; }
        .verify-container { width:100%; max-width:600px; padding:20px; }
        .verify-box { background:rgba(255,255,255,.03); border:1px solid rgba(14,165,233,.2); border-radius:24px; padding:40px; box-shadow:0 20px 40px rgba(0,0,0,.4), 0 0 30px rgba(14,165,233,0.05) inset; backdrop-filter:blur(10px); }
        .brand { text-align:center; margin-bottom:30px; }
        .brand h2 { font-weight:800; background:linear-gradient(135deg,#0ea5e9,#6366f1); -webkit-background-clip:text; -webkit-text-fill-color:transparent; display:inline-block; margin-top:15px; }
        
        .search-box { position:relative; margin-bottom:20px; }
        .search-box i { position:absolute; left:18px; top:50%; transform:translateY(-50%); color:#64748b; font-size:1.1rem; }
        .search-input { width:100%; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:50px; padding:15px 20px 15px 50px; color:#fff; font-size:1rem; outline:none; transition:all .3s; }
        .search-input:focus { border-color:#0ea5e9; background:rgba(255,255,255,.08); box-shadow:0 0 15px rgba(14,165,233,.2); }
        .btn-verify { width:100%; background:linear-gradient(135deg,#0ea5e9,#6366f1); border:none; border-radius:50px; padding:15px; color:#fff; font-weight:700; font-size:1rem; cursor:pointer; transition:all .3s; box-shadow:0 4px 15px rgba(14,165,233,.3); }
        .btn-verify:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(14,165,233,.5); }

        .result-box { margin-top:30px; border-radius:16px; padding:25px; animation:fadeIn 0.5s ease; }
        .result-success { background:rgba(34,197,94,.05); border:1px solid rgba(34,197,94,.2); }
        .result-error { background:rgba(239,68,68,.05); border:1px solid rgba(239,68,68,.2); }
        
        .status-icon { font-size:3rem; margin-bottom:15px; text-align:center; }
        .status-success { color:#4ade80; text-shadow:0 0 20px rgba(74,222,128,.4); }
        .status-error { color:#f87171; text-shadow:0 0 20px rgba(248,113,113,.4); }
        
        .result-title { text-align:center; font-weight:800; font-size:1.3rem; margin-bottom:5px; color:#fff; }
        .result-subtitle { text-align:center; font-size:.85rem; color:#94a3b8; margin-bottom:25px; }

        .detail-row { display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px dashed rgba(255,255,255,.1); }
        .detail-row:last-child { border-bottom:none; }
        .detail-label { color:#94a3b8; font-size:.85rem; font-weight:500; }
        .detail-value { color:#f8fafc; font-size:.9rem; font-weight:700; text-align:right; }

        @keyframes fadeIn { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }
        
        .back-link { display:block; text-align:center; margin-top:30px; color:#64748b; text-decoration:none; font-size:.9rem; transition:color .3s; }
        .back-link:hover { color:#fff; }
    </style>
</head>
<body>

    <div class="verify-container">
        <div class="verify-box">
            <div class="brand">
                <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="VaxiCare" style="height:60px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,.3);">
                <h2>Certificate Verification</h2>
                <p style="color:#94a3b8; font-size:.9rem; margin-top:5px;">Verify the authenticity of a VaxiCare certificate</p>
            </div>

            <form method="GET" action="verify.php">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="id" class="search-input" placeholder="Enter Certificate ID" value="<?php echo htmlspecialchars($search_id); ?>" required>
                </div>
                <button type="submit" class="btn-verify">Verify Authenticity</button>
            </form>

            <?php if ($cert_data): ?>
                <div class="result-box result-success">
                    <div class="status-icon status-success"><i class="fas fa-check-circle"></i></div>
                    <div class="result-title">Authentic Certificate</div>
                    <div class="result-subtitle">This certificate is verified by VaxiCare</div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Beneficiary Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($cert_data['beneficiary_name']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Beneficiary ID</div>
                        <div class="detail-value"><?php echo 'BEN-' . str_pad($cert_data['user_id'], 6, '0', STR_PAD_LEFT); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Vaccine</div>
                        <div class="detail-value"><?php echo htmlspecialchars($cert_data['vaccine_name']); ?> (Dose <?php echo $cert_data['dose_number']; ?>)</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Date of Dose</div>
                        <div class="detail-value"><?php echo date('d M Y', strtotime($cert_data['vaccination_date'])); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Issued By</div>
                        <div class="detail-value"><?php echo htmlspecialchars($cert_data['center_name']); ?></div>
                    </div>
                </div>
            <?php elseif ($error): ?>
                <div class="result-box result-error">
                    <div class="status-icon status-error"><i class="fas fa-times-circle"></i></div>
                    <div class="result-title">Verification Failed</div>
                    <div class="result-subtitle" style="color:#f87171;"><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>

</body>
</html>
