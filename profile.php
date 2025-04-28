<?php
require_once 'config.php';
require_once 'includes/functions.php';
requireLogin();

$user = getUserById($_SESSION['user_id']);
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $errors = [];
        
        // Validate username
        if (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters long';
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }
        
        // Check if email is already taken
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'Email is already taken';
        }
        
        // Password change validation
        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                $errors[] = 'New password must be at least 6 characters long';
            }
            
            if ($new_password !== $confirm_password) {
                $errors[] = 'New passwords do not match';
            }
            
            if (!password_verify($current_password, $user['password'])) {
                $errors[] = 'Current password is incorrect';
            }
        }
        
        if (empty($errors)) {
            // Update profile
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $username, $email, $hashed_password, $user['id']);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $username, $email, $user['id']);
            }
            
            if ($stmt->execute()) {
                $message = 'Profile updated successfully';
                $message_type = 'success';
                $user = getUserById($_SESSION['user_id']); // Refresh user data
            } else {
                $message = 'Failed to update profile';
                $message_type = 'danger';
            }
        } else {
            $message = implode('<br>', $errors);
            $message_type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <!-- User Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Account Information</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h4><?php echo $user['username']; ?></h4>
                        <p class="text-muted"><?php echo $user['email']; ?></p>
                        <div class="mb-2">
                            <span class="badge bg-<?php 
                                echo $user['level'] === 'Silver' ? 'light' : 
                                    ($user['level'] === 'Golden' ? 'warning' : 'info'); 
                            ?>">
                                <?php echo $user['level']; ?> Level
                            </span>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-<?php echo $user['is_vip'] ? 'success' : 'secondary'; ?>">
                                <?php echo $user['is_vip'] ? 'VIP Member' : 'Standard Member'; ?>
                            </span>
                        </div>
                        <p class="mb-0">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
                
                <!-- Account Stats -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Account Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Wallet Balance:</span>
                            <span class="text-primary">৳<?php echo number_format($user['wallet_balance'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Referral Code:</span>
                            <span class="text-muted"><?php echo $user['referral_code']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Last Login:</span>
                            <span class="text-muted"><?php echo $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Profile Update Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Update Profile</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                            </div>
                            
                            <hr>
                            
                            <h6>Change Password</h6>
                            <p class="text-muted small">Leave blank to keep current password</p>
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">Two-Factor Authentication</h6>
                                <p class="text-muted small mb-0">Add an extra layer of security to your account</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="2fa" disabled>
                                <label class="form-check-label" for="2fa">Coming Soon</label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Login Notifications</h6>
                                <p class="text-muted small mb-0">Get notified when someone logs into your account</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notifications" disabled>
                                <label class="form-check-label" for="notifications">Coming Soon</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 