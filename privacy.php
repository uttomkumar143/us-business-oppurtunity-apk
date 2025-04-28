<?php
require_once 'config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Privacy Policy</h2>
            </div>
            <div class="card-body">
                <h4>1. Information We Collect</h4>
                <p>We collect the following types of information:</p>
                <ul>
                    <li>Personal information (name, email, phone number)</li>
                    <li>Account credentials</li>
                    <li>Payment information</li>
                    <li>Usage data and analytics</li>
                </ul>

                <h4>2. How We Use Your Information</h4>
                <p>We use your information to:</p>
                <ul>
                    <li>Provide and maintain our services</li>
                    <li>Process transactions and payments</li>
                    <li>Send important notifications</li>
                    <li>Improve our services</li>
                    <li>Prevent fraud and abuse</li>
                </ul>

                <h4>3. Data Security</h4>
                <p>We implement appropriate security measures to protect your personal information:</p>
                <ul>
                    <li>Encryption of sensitive data</li>
                    <li>Secure servers and databases</li>
                    <li>Regular security audits</li>
                    <li>Access controls and authentication</li>
                </ul>

                <h4>4. Data Sharing</h4>
                <p>We do not sell or rent your personal information to third parties. We may share your information with:</p>
                <ul>
                    <li>Payment processors</li>
                    <li>Service providers who assist in our operations</li>
                    <li>Law enforcement when required by law</li>
                </ul>

                <h4>5. Cookies and Tracking</h4>
                <p>We use cookies and similar tracking technologies to:</p>
                <ul>
                    <li>Remember your preferences</li>
                    <li>Analyze site usage</li>
                    <li>Improve user experience</li>
                </ul>

                <h4>6. Your Rights</h4>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal information</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Opt-out of marketing communications</li>
                </ul>

                <h4>7. Children's Privacy</h4>
                <p>Our services are not intended for individuals under 18 years of age. We do not knowingly collect personal information from children.</p>

                <h4>8. Changes to Privacy Policy</h4>
                <p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>

                <h4>9. Contact Us</h4>
                <p>If you have any questions about this privacy policy, please contact us at <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 