<?php
require_once 'config.php';
require_once 'includes/functions.php';
requireLogin();

$user = getUserById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $payment_method = $_POST['payment_method'];
    $payment_details = trim($_POST['payment_details']);
    
    // Validate input
    $errors = [];
    
    if ($amount < MIN_WITHDRAWAL) {
        $errors[] = 'Minimum withdrawal amount is ৳' . MIN_WITHDRAWAL;
    }
    
    if ($amount > $user['wallet_balance']) {
        $errors[] = 'Insufficient balance';
    }
    
    if (empty($payment_details)) {
        $errors[] = 'Payment details are required';
    }
    
    if (empty($errors)) {
        // Create withdrawal request
        $stmt = $conn->prepare("INSERT INTO withdrawals (user_id, amount, payment_method, payment_details) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $user['id'], $amount, $payment_method, $payment_details);
        
        if ($stmt->execute()) {
            // Deduct amount from wallet
            updateWallet($user['id'], -$amount);
            
            // Add transaction record
            addTransaction($user['id'], -$amount, 'withdrawal', 'Withdrawal request');
            
            setMessage('Withdrawal request submitted successfully', 'success');
            redirect('dashboard.php');
        } else {
            $errors[] = 'Failed to submit withdrawal request';
        }
    }
}

// Get user's withdrawal history
$stmt = $conn->prepare("SELECT * FROM withdrawals WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$withdrawals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Withdraw Funds</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info">
                            <h6>Available Balance: ৳<?php echo number_format($user['wallet_balance'], 2); ?></h6>
                            <p class="mb-0">Minimum Withdrawal: ৳<?php echo MIN_WITHDRAWAL; ?></p>
                        </div>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (৳)</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="<?php echo MIN_WITHDRAWAL; ?>" step="0.01" max="<?php echo $user['wallet_balance']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="bkash">bKash</option>
                                    <option value="nagad">Nagad</option>
                                    <option value="ltc" disabled>Litecoin (Coming Soon)</option>
                                    <option value="usdt" disabled>USDT (Coming Soon)</option>
                                    <option value="binance" disabled>Binance Pay (Coming Soon)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_details" class="form-label">Payment Details</label>
                                <input type="text" class="form-control" id="payment_details" name="payment_details" placeholder="Enter your bKash/Nagad number" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit Withdrawal Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Withdrawal History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($withdrawals as $withdrawal): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i', strtotime($withdrawal['created_at'])); ?></td>
                                            <td>৳<?php echo number_format($withdrawal['amount'], 2); ?></td>
                                            <td><?php echo ucfirst($withdrawal['payment_method']); ?></td>
                                            <td>
                                                <?php
                                                $status_class = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                                ?>
                                                <span class="badge bg-<?php echo $status_class[$withdrawal['status']]; ?>">
                                                    <?php echo ucfirst($withdrawal['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            const paymentDetails = document.getElementById('payment_details');
            const method = this.value;
            
            if (method === 'bkash' || method === 'nagad') {
                paymentDetails.placeholder = 'Enter your ' + method + ' number';
            } else if (method === 'ltc' || method === 'usdt') {
                paymentDetails.placeholder = 'Enter your ' + method.toUpperCase() + ' wallet address';
            } else if (method === 'binance') {
                paymentDetails.placeholder = 'Enter your Binance Pay ID';
            }
        });
    </script>
</body>
</html> 