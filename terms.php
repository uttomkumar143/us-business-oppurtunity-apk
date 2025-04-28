<?php
require_once 'config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Terms & Conditions</h2>
            </div>
            <div class="card-body">
                <h4>1. Acceptance of Terms</h4>
                <p>By accessing and using <?php echo SITE_NAME; ?>, you accept and agree to be bound by the terms and provision of this agreement.</p>

                <h4>2. User Registration</h4>
                <p>To use our services, you must register an account. You agree to provide accurate and complete information during registration and to update this information to keep it current.</p>

                <h4>3. Account Security</h4>
                <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You agree to notify us immediately of any unauthorized use of your account.</p>

                <h4>4. Referral Program</h4>
                <p>Our referral program is subject to the following conditions:</p>
                <ul>
                    <li>Referral bonuses are only paid for active referrals who complete the registration process</li>
                    <li>Referral income is calculated based on the current commission structure</li>
                    <li>We reserve the right to modify the referral program at any time</li>
                </ul>

                <h4>5. Withdrawal Policy</h4>
                <p>Withdrawals are subject to the following conditions:</p>
                <ul>
                    <li>Minimum withdrawal amount: ৳<?php echo MIN_WITHDRAWAL; ?></li>
                    <li>Withdrawal requests are processed within 24-48 hours</li>
                    <li>We reserve the right to verify withdrawal requests</li>
                </ul>

                <h4>6. Prohibited Activities</h4>
                <p>You agree not to:</p>
                <ul>
                    <li>Use automated scripts or bots to access the service</li>
                    <li>Create multiple accounts</li>
                    <li>Engage in fraudulent activities</li>
                    <li>Violate any applicable laws or regulations</li>
                </ul>

                <h4>7. Intellectual Property</h4>
                <p>All content on <?php echo SITE_NAME; ?> is protected by copyright and other intellectual property laws. You may not reproduce, distribute, or create derivative works without our permission.</p>

                <h4>8. Limitation of Liability</h4>
                <p><?php echo SITE_NAME; ?> shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the service.</p>

                <h4>9. Changes to Terms</h4>
                <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting to the website.</p>

                <h4>10. Contact Information</h4>
                <p>For any questions regarding these terms, please contact us at <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 