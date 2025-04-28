<?php
require_once 'config.php';
require_once 'includes/functions.php';
requireAdmin();

$user = getUserById($_SESSION['user_id']);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'approve_user':
                $user_id = $_POST['user_id'];
                $stmt = $conn->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                setMessage('User approved successfully', 'success');
                break;
                
            case 'block_user':
                $user_id = $_POST['user_id'];
                $stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                setMessage('User blocked successfully', 'success');
                break;
                
            case 'update_level':
                $user_id = $_POST['user_id'];
                $level = $_POST['level'];
                $stmt = $conn->prepare("UPDATE users SET level = ? WHERE id = ?");
                $stmt->bind_param("si", $level, $user_id);
                $stmt->execute();
                setMessage('User level updated successfully', 'success');
                break;
                
            case 'update_settings':
                foreach ($_POST['settings'] as $key => $value) {
                    updateSetting($key, $value);
                }
                setMessage('Settings updated successfully', 'success');
                break;
                
            case 'approve_withdrawal':
                $withdrawal_id = $_POST['withdrawal_id'];
                $stmt = $conn->prepare("UPDATE withdrawals SET status = 'approved' WHERE id = ?");
                $stmt->bind_param("i", $withdrawal_id);
                $stmt->execute();
                setMessage('Withdrawal approved successfully', 'success');
                break;
                
            case 'reject_withdrawal':
                $withdrawal_id = $_POST['withdrawal_id'];
                $stmt = $conn->prepare("UPDATE withdrawals SET status = 'rejected' WHERE id = ?");
                $stmt->bind_param("i", $withdrawal_id);
                $stmt->execute();
                setMessage('Withdrawal rejected successfully', 'success');
                break;
        }
    }
}

// Get pending users
$stmt = $conn->prepare("SELECT * FROM users WHERE is_active = 0 ORDER BY created_at DESC");
$stmt->execute();
$pending_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get all users
$stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
$stmt->execute();
$all_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get pending withdrawals
$stmt = $conn->prepare("SELECT w.*, u.username FROM withdrawals w JOIN users u ON w.user_id = u.id WHERE w.status = 'pending' ORDER BY w.created_at DESC");
$stmt->execute();
$pending_withdrawals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get all settings
$stmt = $conn->prepare("SELECT * FROM settings");
$stmt->execute();
$settings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Admin Panel</h2>
        
        <!-- Pending Users -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Pending Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Referral Code</th>
                                <th>Referred By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_users as $pending_user): ?>
                                <tr>
                                    <td><?php echo $pending_user['id']; ?></td>
                                    <td><?php echo $pending_user['username']; ?></td>
                                    <td><?php echo $pending_user['email']; ?></td>
                                    <td><?php echo $pending_user['referral_code']; ?></td>
                                    <td><?php echo $pending_user['referred_by']; ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($pending_user['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="approve_user">
                                            <input type="hidden" name="user_id" value="<?php echo $pending_user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="block_user">
                                            <input type="hidden" name="user_id" value="<?php echo $pending_user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Block</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- All Users -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">All Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>VIP</th>
                                <th>Wallet</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update_level">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <select name="level" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="Silver" <?php echo $user['level'] === 'Silver' ? 'selected' : ''; ?>>Silver</option>
                                                <option value="Golden" <?php echo $user['level'] === 'Golden' ? 'selected' : ''; ?>>Golden</option>
                                                <option value="Platinum" <?php echo $user['level'] === 'Platinum' ? 'selected' : ''; ?>>Platinum</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo $user['is_vip'] ? 'Yes' : 'No'; ?></td>
                                    <td>৳<?php echo number_format($user['wallet_balance'], 2); ?></td>
                                    <td><?php echo $user['is_active'] ? 'Active' : 'Blocked'; ?></td>
                                    <td>
                                        <?php if ($user['is_active']): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="block_user">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Block</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="approve_user">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Pending Withdrawals -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Pending Withdrawals</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Details</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_withdrawals as $withdrawal): ?>
                                <tr>
                                    <td><?php echo $withdrawal['id']; ?></td>
                                    <td><?php echo $withdrawal['username']; ?></td>
                                    <td>৳<?php echo number_format($withdrawal['amount'], 2); ?></td>
                                    <td><?php echo ucfirst($withdrawal['payment_method']); ?></td>
                                    <td><?php echo $withdrawal['payment_details']; ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($withdrawal['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="approve_withdrawal">
                                            <input type="hidden" name="withdrawal_id" value="<?php echo $withdrawal['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="reject_withdrawal">
                                            <input type="hidden" name="withdrawal_id" value="<?php echo $withdrawal['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Settings -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_settings">
                    <div class="row">
                        <?php foreach ($settings as $setting): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?></label>
                                <input type="text" class="form-control" name="settings[<?php echo $setting['setting_key']; ?>]" value="<?php echo $setting['setting_value']; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Settings</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 