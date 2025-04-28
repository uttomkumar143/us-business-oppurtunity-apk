<?php
require_once 'config.php';
require_once 'includes/functions.php';
requireLogin();

$user = getUserById($_SESSION['user_id']);

// Get referral statistics
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_referrals,
        SUM(CASE WHEN level = 'Silver' THEN 1 ELSE 0 END) as silver_referrals,
        SUM(CASE WHEN level = 'Golden' THEN 1 ELSE 0 END) as golden_referrals,
        SUM(CASE WHEN level = 'Platinum' THEN 1 ELSE 0 END) as platinum_referrals
    FROM users 
    WHERE referred_by = ?
");
$stmt->bind_param("s", $user['referral_code']);
$stmt->execute();
$referral_stats = $stmt->get_result()->fetch_assoc();

// Get referral income
$stmt = $conn->prepare("
    SELECT 
        SUM(CASE WHEN type = 'referral' THEN amount ELSE 0 END) as referral_income,
        SUM(CASE WHEN type = 'level' THEN amount ELSE 0 END) as level_income
    FROM transactions 
    WHERE user_id = ?
");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$income_stats = $stmt->get_result()->fetch_assoc();

// Get direct referrals
$stmt = $conn->prepare("
    SELECT id, username, email, level, created_at 
    FROM users 
    WHERE referred_by = ? 
    ORDER BY created_at DESC
");
$stmt->bind_param("s", $user['referral_code']);
$stmt->execute();
$direct_referrals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <!-- Referral Link -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Your Referral Link</h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="referralLink" value="<?php echo SITE_URL . '/register.php?ref=' . $user['referral_code']; ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyReferralLink()">Copy</button>
                        </div>
                        <p class="text-muted">Share this link to earn referral income!</p>
                        
                        <div class="mt-4">
                            <h6>Referral Income Structure:</h6>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Direct Referral
                                    <span class="badge bg-primary rounded-pill">35%</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Level 1 (Silver)
                                    <span class="badge bg-primary rounded-pill">12%</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Level 2 (Golden)
                                    <span class="badge bg-primary rounded-pill">8%</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Level 3 (Platinum)
                                    <span class="badge bg-primary rounded-pill">5%</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Referral Statistics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Referral Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-primary text-white mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Referrals</h6>
                                        <h3><?php echo $referral_stats['total_referrals']; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Income</h6>
                                        <h3>৳<?php echo number_format($income_stats['referral_income'] + $income_stats['level_income'], 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Silver</h6>
                                        <h4><?php echo $referral_stats['silver_referrals']; ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Golden</h6>
                                        <h4><?php echo $referral_stats['golden_referrals']; ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Platinum</h6>
                                        <h4><?php echo $referral_stats['platinum_referrals']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Direct Referrals -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Direct Referrals</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>Joined Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($direct_referrals as $referral): ?>
                                <tr>
                                    <td><?php echo $referral['username']; ?></td>
                                    <td><?php echo $referral['email']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $referral['level'] === 'Silver' ? 'light' : 
                                                ($referral['level'] === 'Golden' ? 'warning' : 'info'); 
                                        ?>">
                                            <?php echo $referral['level']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($referral['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyReferralLink() {
            const referralLink = document.getElementById('referralLink');
            referralLink.select();
            document.execCommand('copy');
            alert('Referral link copied to clipboard!');
        }
    </script>
</body>
</html> 