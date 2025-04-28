<?php
require_once 'config.php';
require_once 'includes/functions.php';
requireLogin();

$user = getUserById($_SESSION['user_id']);

// Get transactions with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total transactions count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM transactions WHERE user_id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$total_transactions = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_transactions / $limit);

// Get transactions
$stmt = $conn->prepare("
    SELECT * FROM transactions 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $user['id'], $limit, $offset);
$stmt->execute();
$transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get transaction summary
$stmt = $conn->prepare("
    SELECT 
        SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as total_income,
        SUM(CASE WHEN amount < 0 THEN amount ELSE 0 END) as total_expense
    FROM transactions 
    WHERE user_id = ?
");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <!-- Transaction Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Transaction Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Income:</span>
                            <span class="text-success">৳<?php echo number_format($summary['total_income'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Expense:</span>
                            <span class="text-danger">৳<?php echo number_format(abs($summary['total_expense']), 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Net Balance:</span>
                            <span class="text-primary">৳<?php echo number_format($summary['total_income'] + $summary['total_expense'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Transaction History -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Transaction History</h5>
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
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i', strtotime($transaction['created_at'])); ?></td>
                                            <td>
                                                <?php
                                                $type_icons = [
                                                    'registration' => 'fa-user-plus',
                                                    'referral' => 'fa-users',
                                                    'level' => 'fa-level-up-alt',
                                                    'login' => 'fa-sign-in-alt',
                                                    'withdrawal' => 'fa-money-bill-wave',
                                                    'spin' => 'fa-sync-alt',
                                                    'farming' => 'fa-seedling',
                                                    'vip' => 'fa-crown'
                                                ];
                                                $icon = $type_icons[$transaction['type']] ?? 'fa-exchange-alt';
                                                ?>
                                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                                <?php echo ucfirst($transaction['type']); ?>
                                            </td>
                                            <td class="<?php echo $transaction['amount'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                                ৳<?php echo number_format(abs($transaction['amount']), 2); ?>
                                            </td>
                                            <td><?php echo $transaction['description']; ?></td>
                                            <td>
                                                <?php
                                                $status_class = [
                                                    'pending' => 'warning',
                                                    'completed' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                                ?>
                                                <span class="badge bg-<?php echo $status_class[$transaction['status']]; ?>">
                                                    <?php echo ucfirst($transaction['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Transaction pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 