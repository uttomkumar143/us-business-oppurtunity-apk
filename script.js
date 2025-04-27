// Global variables
let currentUser = null;
let isAdmin = false;
let spinWheelItems = [
    { text: "৳০", description: "Bad Luck 😢", probability: 20, value: 0 },
    { text: "৳৫", description: "Small Reward 🎉", probability: 30, value: 5 },
    { text: "৳১০", description: "Full Return ♻️", probability: 20, value: 10 },
    { text: "৳২০", description: "Double Profit 💰", probability: 10, value: 20 },
    { text: "Bonus Farm", description: "১ দিন Passive Farming", probability: 10, value: 5 },
    { text: "2x Login", description: "পরদিন Double Login Bonus", probability: 5, value: 5 },
    { text: "Free Spin", description: "১টা Extra Spin", probability: 5, value: 10 }
];

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Check if user is logged in
    checkLoginStatus();

    // Initialize spin wheel
    initSpinWheel();

    // Form submissions
    document.getElementById('loginForm')?.addEventListener('submit', handleLogin);
    document.getElementById('registerForm')?.addEventListener('submit', handleRegister);
    document.getElementById('forgotPasswordForm')?.addEventListener('submit', handleForgotPassword);
});

// Check login status from localStorage
function checkLoginStatus() {
    const userData = localStorage.getItem('mlmUser');
    if (userData) {
        currentUser = JSON.parse(userData);
        isAdmin = currentUser.email === 'uttombarmon45@gmail.com' && currentUser.password === 'ukb@1234';
        
        if (window.location.pathname.includes('dashboard.html') || window.location.hash === '#dashboard') {
            showUserDashboard();
        } else {
            // Redirect to dashboard if logged in
            window.location.hash = '#dashboard';
        }
    }
}

// Show login modal
function showLoginModal() {
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
}

// Show register modal
function showRegisterModal() {
    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    registerModal.show();
    
    // Check for referral code in URL
    const urlParams = new URLSearchParams(window.location.search);
    const refCode = urlParams.get('ref');
    if (refCode) {
        document.getElementById('regReferralCode').value = refCode;
    }
}

// Show forgot password modal
function showForgotPasswordModal() {
    const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
    loginModal.hide();
    
    const forgotModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
    forgotModal.show();
}

// Handle login form submission
function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const rememberMe = document.getElementById('rememberMe').checked;
    
    // Simple validation
    if (!email || !password) {
        alert('Please enter both email and password');
        return;
    }
    
    // Check if admin
    if (email === 'uttombarmon45@gmail.com' && password === 'ukb@1234') {
        currentUser = {
            email: email,
            password: password,
            name: 'Admin User',
            level: 'Platinum',
            balance: 10000,
            referrals: 50,
            isActive: true
        };
        isAdmin = true;
    } else {
        // Check user in localStorage (simplified)
        const users = JSON.parse(localStorage.getItem('mlmUsers')) || [];
        const user = users.find(u => u.email === email && u.password === password);
        
        if (!user) {
            alert('Invalid email or password');
            return;
        }
        
        if (!user.isActive) {
            alert('Your account is not active yet. Please wait for admin approval.');
            return;
        }
        
        currentUser = user;
        isAdmin = false;
    }
    
    // Save to localStorage
    if (rememberMe) {
        localStorage.setItem('mlmUser', JSON.stringify(currentUser));
    } else {
        sessionStorage.setItem('mlmUser', JSON.stringify(currentUser));
    }
    
    // Close modal and show dashboard
    const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
    loginModal.hide();
    
    showUserDashboard();
}

// Handle register form submission
function handleRegister(e) {
    e.preventDefault();
    
    const name = document.getElementById('regName').value;
    const email = document.getElementById('regEmail').value;
    const phone = document.getElementById('regPhone').value;
    const password = document.getElementById('regPassword').value;
    const confirmPassword = document.getElementById('regConfirmPassword').value;
    const referralCode = document.getElementById('regReferralCode').value;
    const agreeTerms = document.getElementById('agreeTerms').checked;
    
    // Validation
    if (!name || !email || !phone || !password || !confirmPassword) {
        alert('Please fill all required fields');
        return;
    }
    
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return;
    }
    
    if (!agreeTerms) {
        alert('You must agree to the terms and conditions');
        return;
    }
    
    // Check if user already exists
    const users = JSON.parse(localStorage.getItem('mlmUsers')) || [];
    if (users.some(u => u.email === email)) {
        alert('This email is already registered');
        return;
    }
    
    // Create new user (not active yet)
    const newUser = {
        id: Date.now(),
        name,
        email,
        phone,
        password,
        referralCode,
        balance: 0,
        referrals: 0,
        level: 'Silver',
        isActive: false,
        joinDate: new Date().toISOString(),
        securityQuestion: '',
        securityAnswer: ''
    };
    
    // Save user to localStorage
    users.push(newUser);
    localStorage.setItem('mlmUsers', JSON.stringify(users));
    
    // Close register modal and show payment modal
    const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
    registerModal.hide();
    
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    paymentModal.show();
}

// Handle forgot password form submission
function handleForgotPassword(e) {
    e.preventDefault();
    
    const email = document.getElementById('resetEmail').value;
    const question = document.getElementById('securityQuestion').value;
    const answer = document.getElementById('securityAnswer').value;
    
    // In a real app, you would verify this with your backend
    alert('If the information matches our records, password reset instructions will be sent to your email.');
    
    const forgotModal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
    forgotModal.hide();
}

// Submit payment details
function submitPayment() {
    const mobileNumber = document.getElementById('mobilePaymentNumber').value;
    const transactionId = document.getElementById('transactionId').value;
    
    if (!mobileNumber || !transactionId) {
        alert('Please fill all payment details');
        return;
    }
    
    // In a real app, you would send this to your backend for verification
    alert('Payment details submitted. Your account will be activated after admin approval.');
    
    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
    paymentModal.hide();
}

// Show user dashboard
function showUserDashboard() {
    // Hide all sections of the main page
    document.querySelectorAll('section, footer, nav').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show dashboard
    const dashboard = document.getElementById('userDashboard');
    dashboard.style.display = 'block';
    
    // Update dashboard with user data
    if (currentUser) {
        document.getElementById('dashboardUsername').textContent = currentUser.name;
        document.getElementById('dashboardLevel').textContent = currentUser.level + ' Member';
        document.getElementById('walletBalance').textContent = currentUser.balance.toLocaleString();
        
        // Set avatar based on level
        const avatar = document.getElementById('dashboardAvatar');
        if (currentUser.level === 'Golden') {
            avatar.style.border = '3px solid gold';
        } else if (currentUser.level === 'Platinum') {
            avatar.style.border = '3px solid #e5e4e2';
        }
        
        // Show appropriate section
        if (isAdmin) {
            showAdminDashboard();
        } else {
            showDashboardSection('overview');
        }
    }
}

// Show dashboard section
function showDashboardSection(section) {
    // Hide all sections
    document.querySelectorAll('.dashboard-section').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show selected section
    document.getElementById('dashboard' + capitalizeFirstLetter(section)).style.display = 'block';
    
    // Update active nav link
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    const activeLink = document.querySelector(`.sidebar .nav-link[onclick*="${section}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
    
    // Load section-specific content
    switch(section) {
        case 'overview':
            loadDashboardOverview();
            break;
        case 'wallet':
            loadWalletSection();
            break;
        case 'referrals':
            loadReferralsSection();
            break;
        case 'withdraw':
            loadWithdrawSection();
            break;
        case 'spin':
            loadSpinSection();
            break;
        case 'farming':
            loadFarmingSection();
            break;
        case 'vip':
            loadVipSection();
            break;
        case 'profile':
            loadProfileSection();
            break;
    }
}

// Load dashboard overview
function loadDashboardOverview() {
    // In a real app, you would fetch this data from your backend
    document.getElementById('loginBonusBadge').classList.add('pulse-animation');
    
    // Simulate fetching data
    setTimeout(() => {
        document.getElementById('loginBonusBadge').classList.remove('pulse-animation');
        
        // Update balance with login bonus
        if (currentUser) {
            currentUser.balance += 5;
            localStorage.setItem('mlmUser', JSON.stringify(currentUser));
            document.getElementById('walletBalance').textContent = currentUser.balance.toLocaleString();
            
            // Show notification
            alert('You received ৳5 daily login bonus!');
        }
    }, 2000);
}

// Load wallet section
function loadWalletSection() {
    // In a real app, you would fetch transaction history from your backend
    const transactions = [
        { date: '2023-06-15', description: 'Referral Income (John)', amount: 280, type: 'credit' },
        { date: '2023-06-14', description: 'Daily Login Bonus', amount: 5, type: 'credit' },
        { date: '2023-06-13', description: 'Level Income (1st)', amount: 96, type: 'credit' },
        { date: '2023-06-10', description: 'Withdrawal', amount: 500, type: 'debit' }
    ];
    
    const tbody = document.createElement('tbody');
    transactions.forEach(tx => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${tx.date}</td>
            <td>${tx.description}</td>
            <td class="${tx.type === 'credit' ? 'text-success' : 'text-danger'}">
                ${tx.type === 'credit' ? '+' : '-'}৳${tx.amount}
            </td>
        `;
        tbody.appendChild(row);
    });
    
    const table = document.createElement('table');
    table.className = 'table table-striped';
    table.innerHTML = `
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
    `;
    table.appendChild(tbody);
    
    const section = document.getElementById('dashboardWallet');
    section.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>My Wallet</h3>
            <div>
                <span class="badge bg-primary">Balance: ৳${currentUser.balance.toLocaleString()}</span>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Transaction History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive"></div>
            </div>
        </div>
    `;
    
    section.querySelector('.table-responsive').appendChild(table);
}

// Load referrals section
function loadReferralsSection() {
    // In a real app, you would fetch referrals from your backend
    const referrals = [
        { id: 1, name: 'John Smith', email: 'john@example.com', joinDate: '2023-06-10', level: 'Silver', earnings: 280 },
        { id: 2, name: 'Sarah Johnson', email: 'sarah@example.com', joinDate: '2023-06-05', level: 'Silver', earnings: 280 },
        { id: 3, name: 'Michael Brown', email: 'michael@example.com', joinDate: '2023-05-28', level: 'Golden', earnings: 560 }
    ];
    
    const section = document.getElementById('dashboardReferrals');
    section.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>My Referrals</h3>
            <div>
                <span class="badge bg-primary">Total Referrals: ${referrals.length}</span>
                <span class="badge bg-success ms-2">Earnings: ৳${referrals.reduce((sum, r) => sum + r.earnings, 0)}</span>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Referral Network</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6>Your Referral Link:</h6>
                    <div class="input-group">
                        <input type="text" class="form-control" id="referralLink" value="${window.location.origin}?ref=${currentUser.id}" readonly>
                        <button class="btn btn-primary" onclick="copyReferralLink()">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Join Date</th>
                                <th>Level</th>
                                <th>Earnings</th>
                            </tr>
                        </thead>
                        <tbody id="referralsTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    const tbody = section.querySelector('#referralsTableBody');
    referrals.forEach(ref => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${ref.name}</td>
            <td>${ref.email}</td>
            <td>${ref.joinDate}</td>
            <td><span class="badge ${getLevelBadgeClass(ref.level)}">${ref.level}</span></td>
            <td class="text-success">+৳${ref.earnings}</td>
        `;
        tbody.appendChild(row);
    });
}

// Load withdraw section
function loadWithdrawSection() {
    const section = document.getElementById('dashboardWithdraw');
    section.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Withdraw Funds</h3>
            <div>
                <span class="badge bg-primary">Balance: ৳${currentUser.balance.toLocaleString()}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Withdraw Request</h5>
                    </div>
                    <div class="card-body">
                        <form id="withdrawForm">
                            <div class="mb-3">
                                <label for="withdrawAmount" class="form-label">Amount (৳)</label>
                                <input type="number" class="form-control" id="withdrawAmount" min="200" required>
                                <small class="text-muted">Minimum withdrawal: ৳200</small>
                            </div>
                            <div class="mb-3">
                                <label for="withdrawMethod" class="form-label">Payment Method</label>
                                <select class="form-select" id="withdrawMethod" required>
                                    <option value="" selected disabled>Select method</option>
                                    <option value="bkash">bKash</option>
                                    <option value="nagad">Nagad</option>
                                    <option value="usdt" disabled>USDT (Coming Soon)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="withdrawNumber" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="withdrawNumber" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">Request Withdrawal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Withdrawal History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="withdrawHistoryBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Withdraw form submission
    document.getElementById('withdrawForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat(document.getElementById('withdrawAmount').value);
        const method = document.getElementById('withdrawMethod').value;
        const number = document.getElementById('withdrawNumber').value;
        
        if (amount < 200) {
            alert('Minimum withdrawal amount is ৳200');
            return;
        }
        
        if (amount > currentUser.balance) {
            alert('Insufficient balance');
            return;
        }
        
        if (!method || !number) {
            alert('Please fill all fields');
            return;
        }
        
        // In a real app, you would send this to your backend
        alert(`Withdrawal request of ৳${amount} via ${method} has been submitted for approval.`);
        
        // Update balance (temporary)
        currentUser.balance -= amount;
        localStorage.setItem('mlmUser', JSON.stringify(currentUser));
        document.getElementById('walletBalance').textContent = currentUser.balance.toLocaleString();
        
        // Add to withdrawal history
        const history = [
            { date: new Date().toISOString().split('T')[0], amount, method, status: 'Pending' },
            { date: '2023-06-01', amount: 500, method: 'bkash', status: 'Completed' }
        ];
        
        const tbody = document.getElementById('withdrawHistoryBody');
        tbody.innerHTML = '';
        
        history.forEach(wd => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${wd.date}</td>
                <td>৳${wd.amount}</td>
                <td>${wd.method}</td>
                <td><span class="badge ${wd.status === 'Completed' ? 'bg-success' : 'bg-warning'}">${wd.status}</span></td>
            `;
            tbody.appendChild(row);
        });
        
        // Reset form
        this.reset();
    });
}

// Initialize spin wheel
function initSpinWheel() {
    const wheel = document.querySelector('.spin-wheel');
    if (!wheel) return;
    
    // Create wheel segments
    const segmentAngle = 360 / spinWheelItems.length;
    let currentAngle = 0;
    
    spinWheelItems.forEach((item, index) => {
        const segment = document.createElement('div');
        segment.className = 'spin-wheel-item';
        segment.textContent = item.text;
        segment.style.transform = `rotate(${currentAngle}deg)`;
        segment.style.backgroundColor = getSegmentColor(index);
        segment.setAttribute('data-index', index);
        wheel.appendChild(segment);
        currentAngle += segmentAngle;
    });
}

// Load spin section
function loadSpinSection() {
    const section = document.getElementById('dashboardSpin');
    section.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Spin & Win</h3>
            <div>
                <span class="badge bg-primary">Spins Available: 1</span>
                <span class="badge bg-success ms-2">Cost: ৳10 per spin</span>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="spin-wheel">
                    <button class="spin-btn" id="spinButton" onclick="spinWheel()">SPIN</button>
                </div>
                <div class="mt-4" id="spinResult"></div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Prize Structure</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Prize</th>
                                <th>Description</th>
                                <th>Probability</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody id="prizeTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    // Populate prize table
    const tbody = document.getElementById('prizeTableBody');
    spinWheelItems.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.text}</td>
            <td>${item.description}</td>
            <td>${item.probability}%</td>
            <td>৳${item.value}</td>
        `;
        tbody.appendChild(row);
    });
    
    // Initialize wheel if not already
    initSpinWheel();
}

// Spin the wheel
function spinWheel() {
    const spinButton = document.getElementById('spinButton');
    const wheel = document.querySelector('.spin-wheel');
    const resultDiv = document.getElementById('spinResult');
    
    if (!spinButton || !wheel) return;
    
    // Disable button during spin
    spinButton.disabled = true;
    resultDiv.innerHTML = '<div class="spinner"></div>';
    
    // Random selection with probability
    const random = Math.random() * 100;
    let cumulativeProbability = 0;
    let selectedIndex = 0;
    
    for (let i = 0; i < spinWheelItems.length; i++) {
        cumulativeProbability += spinWheelItems[i].probability;
        if (random <= cumulativeProbability) {
            selectedIndex = i;
            break;
        }
    }
    
    // Calculate rotation (5 full rotations + segment angle)
    const segmentAngle = 360 / spinWheelItems.length;
    const targetAngle = 1800 + (segmentAngle * selectedIndex);
    
    // Apply rotation
    wheel.style.transition = 'transform 5s cubic-bezier(0.17, 0.67, 0.21, 0.99)';
    wheel.style.transform = `rotate(${-targetAngle}deg)`;
    
    // Show result after spin
    setTimeout(() => {
        const prize = spinWheelItems[selectedIndex];
        resultDiv.innerHTML = `
            <div class="alert ${prize.value > 0 ? 'alert-success' : 'alert-warning'}">
                <h4>${prize.text}</h4>
                <p>${prize.description}</p>
                ${prize.value > 0 ? `<p class="fw-bold">You won ৳${prize.value}!</p>` : ''}
            </div>
        `;
        
        // Update user balance if prize has value
        if (prize.value > 0 && currentUser) {
            currentUser.balance += prize.value;
            localStorage.setItem('mlmUser', JSON.stringify(currentUser));
            document.getElementById('walletBalance').textContent = currentUser.balance.toLocaleString();
        }
        
        // Re-enable button
        spinButton.disabled = false;
    }, 5500);
}

// Load farming section
function loadFarmingSection() {
    const section = document.getElementById('dashboardFarming');
    section.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Farming System</h3>
            <div>
                <span class="badge bg-primary">Active Slots: 1/3</span>
                <span class="badge bg-success ms-2">Earnings: ৳5/slot/day</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="farming-slot active">
                    <h5>Slot #1</h5>
                    <div class="farming-timer">৳5 Ready</div>
                    <button class="btn btn-success btn-sm mt-2">Claim</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="farming-slot">
                    <h5>Slot #2</h5>
                    <p class="text-muted">Inactive</p>
                    <button class="btn btn-primary btn-sm mt-2">Activate (৳100)</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="farming-slot">
                    <h5>Slot #3</h5>
                    <p class="text-muted">Inactive</p>
                    <button class="btn btn-primary btn-sm mt-2">Activate (৳200)</button>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Farming History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Slot</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2023-06-15</td>
                                <td>Slot #1</td>
                                <td class="text-success">+৳5</td>
                            </tr>
                            <tr>
                                <td>2023-06-14</td>
                                <td>Slot #1</td>
                                <td class="text-success">+৳5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    // Add click handlers for farming slots
    section.querySelectorAll('.farming-slot .btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const slot = this.closest('.farming-slot');
            
            if (this.textContent.includes('Claim')) {
                // Claim farming reward
                if (currentUser) {
                    currentUser.balance += 5;
                    localStorage.setItem('mlmUser', JSON.stringify(currentUser));
                    document.getElementById('walletBalance').textContent = currentUser.balance.toLocaleString();
                    alert('You claimed ৳5 from farming!');
                    
                    // Reset timer
                    const timer = slot.querySelector('.farming-timer');
                    timer.textContent = '৳5 Ready';
                }
            } else if (this.textContent.includes('Activate')) {
                // Activate farming slot
                const cost = parseInt(this.textContent.match(/৳(\d+)/)[1]);
                
                if (currentUser.balance >= cost) {
                    currentUser.balance -= cost;
                    localStorage.setItem