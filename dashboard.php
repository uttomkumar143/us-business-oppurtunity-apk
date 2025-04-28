<?php
require_once 'config.php';
require_once 'includes/functions.php';
requireLogin();

$user = getUserById($_SESSION['user_id']);

// Process daily login bonus
$last_login = strtotime($user['last_login']);
$now = time();
if (date('Y-m-d', $last_login) !== date('Y-m-d', $now)) {
    addTransaction($user['id'], DAILY_LOGIN_BONUS, 'login', 'Daily login bonus');
    updateWallet($user['id'], DAILY_LOGIN_BONUS);
    
    // Update last login
    $stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
}

// Get user's transactions
$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get user's referrals
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE referred_by = ?");
$stmt->bind_param("s", $user['referral_code']);
$stmt->execute();
$referral_count = $stmt->get_result()->fetch_assoc()['count'];

// Get farming status
$stmt = $conn->prepare("SELECT * FROM farming_slots WHERE user_id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$farming_slots = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get settings
$spin_enabled = getSetting('spin_enabled');
$farming_enabled = getSetting('farming_enabled');
$vip_enabled = getSetting('vip_enabled');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <!-- Wallet Overview -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Wallet Overview</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-primary">৳<?php echo number_format($user['wallet_balance'], 2); ?></h3>
                        <p class="text-muted">Available Balance</p>
                        <a href="withdraw.php" class="btn btn-primary">Withdraw</a>
                    </div>
                </div>
            </div>
            
            <!-- Referral Stats -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Referral Stats</h5>
                    </div>
                    <div class="card-body">
                        <h3><?php echo $referral_count; ?></h3>
                        <p class="text-muted">Total Referrals</p>
                        <a href="referral.php" class="btn btn-success">View Referrals</a>
                    </div>
                </div>
            </div>
            
            <!-- Level Status -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Level Status</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-warning"><?php echo $user['level']; ?></h3>
                        <p class="text-muted">Current Level</p>
                        <?php if ($user['is_vip']): ?>
                            <span class="badge bg-success">VIP Member</span>
                        <?php else: ?>
                            <a href="vip.php" class="btn btn-warning">Upgrade to VIP</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <!-- Recent Transactions -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i', strtotime($transaction['created_at'])); ?></td>
                                            <td><?php echo ucfirst($transaction['type']); ?></td>
                                            <td class="<?php echo $transaction['amount'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                                ৳<?php echo number_format($transaction['amount'], 2); ?>
                                            </td>
                                            <td><?php echo $transaction['description']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="transactions.php" class="btn btn-outline-primary">View All Transactions</a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($spin_enabled): ?>
                            <a href="spin.php" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-sync-alt"></i> Spin & Win
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($farming_enabled): ?>
                            <a href="farming.php" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-seedling"></i> Farming
                            </a>
                        <?php endif; ?>
                        
                        <a href="referral.php" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-users"></i> Referral Link
                        </a>
                        
                        <?php if ($vip_enabled && !$user['is_vip']): ?>
                            <a href="vip.php" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-crown"></i> Upgrade to VIP
                            </a>
                        <?php endif; ?>
                        
                        <a href="profile.php" class="btn btn-secondary w-100">
                            <i class="fas fa-user"></i> Profile Settings
                        </a>
                    </div>
                </div>
                
                <!-- Farming Status -->
                <?php if ($farming_enabled): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Farming Status</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($farming_slots as $slot): ?>
                                <div class="mb-3">
                                    <h6>Slot #<?php echo $slot['id']; ?></h6>
                                    <p class="mb-1">Status: <?php echo $slot['is_active'] ? 'Active' : 'Inactive'; ?></p>
                                    <?php if ($slot['is_active']): ?>
                                        <p class="mb-1">Last Claim: <?php echo $slot['last_claim'] ? date('Y-m-d H:i', strtotime($slot['last_claim'])) : 'Never'; ?></p>
                                        <a href="farming.php?claim=<?php echo $slot['id']; ?>" class="btn btn-sm btn-success">Claim Reward</a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 