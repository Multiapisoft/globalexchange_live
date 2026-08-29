<?php
$title = 'Investment Plans';
$_is_dashboard = 1;
include_once 'header.php';


$check = my_query("SHOW COLUMNS FROM `investments_plan` LIKE 'action'");

if (mysqli_num_rows($check) == 0) {
    $query = "ALTER TABLE `investments_plan` ADD `action` TINYINT NOT NULL DEFAULT '1' COMMENT '1. active\r\n,0. Deactive' AFTER `status`;";
    my_query($query);
}





$query = "SELECT * FROM investments_plan WHERE status = 0  ORDER BY recid ASC";
$result = my_query($query);
$i = 0;
$j = 0;

// Calculate total investment plans
$total_plans = mysqli_num_rows($result);

// Calculate total investment value (example calculation)
$total_value = 0;
$temp_result = my_query($query);
while ($temp_row = mysqli_fetch_object($temp_result)) {
    $total_value += $temp_row->amount_from;
}

// Calculate active investors (example)
$active_investors = 1250 + rand(0, 100);

?>

<div class="container">
    <!-- Package Selection Section -->
    <div class="card bg-white border-0 shadow-sm mb-5">
        <div class="card-header bg-transparent border-0 py-4">
            <h2 class="h4 mb-0 text-dark fw-bold">Choose Your Trading Plan</h2>
            <p class="text-muted mb-0">Select a package that matches your investment goals</p>
        </div>
        <div class="card-body px-0 pb-4">
            <div class="row g-4">
                <!-- Package 1 -->
                <?php while ($row = mysqli_fetch_object($result)) {
                    $i++;
                    $j++;
                    if ($j == 5) {
                        $j = 1;
                    }

                    // Generate random ROI between 5% and 25%
                    $roi = rand(5, 25);
                    // Randomly decide if it's positive or negative trend
                    $trend = rand(0, 10) > 2 ? 'price-up' : 'price-down';

                    if ($row->action == 1 || $row->action == 2):
                        ?>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="package-card h-100 position-relative overflow-hidden shadow-lg"
                                onclick="selectPackage(this, 'Basic')" data-package="basic">
                                <?php if (rand(0, 5) == 1): ?>
                                    <div class="package-ribbon bg-danger">Hot</div>
                                <?php elseif (rand(0, 7) == 1): ?>
                                    <div class="package-ribbon bg-success">New</div>
                                <?php elseif (rand(0, 4) == 1): ?>
                                    <div class="package-ribbon bg-warning">Popular</div>
                                <?php endif; ?>
                                <div class="card h-100 border-0 shadow-sm hover-lift">
                                    <div class="card-body p-4 text-center">
                                        <div class="icon-shape icon-shape-primary mx-auto mb-4 rounded-xl">
                                            <i class="fas fa-chart-line fa-2x text-white"></i>
                                        </div>
                                        <h3 class="h5 mb-3"><?php echo $row->title; ?></h3>
                                        <!-- <div class="price mb-4">
                                            <span class="h1 text-dark fw-bold">5%</span>
                                            <span class="text-muted">/day</span>
                                        </div> -->
                                        <ul class="list-unstyled mb-4">
                                            <li class="py-2">
                                                <!-- <i class="fas fa-check text-success me-2"></i> -->
                                                <span class="decoration-4 p-2 border rounded-xl bg-indigo-600 text-white"><?php echo ($row->amount_from == $row->amount_to)
                                                    ? '$' . number_format($row->amount_from)
                                                    : '$' . number_format($row->amount_from) . ' - $' . number_format($row->amount_to); ?></span>
                                            </li>
                                            <li class="py-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <span>24/7 Support</span>
                                            </li>
                                            <li class="py-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <span>Secure & Regulated Platform</span>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    endif;
                } ?>
              
            </div>
            <input type="hidden" id="selectedPackage" name="package" value="">
        </div>
    </div>

    <!-- Time Selection Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h4 class="card-title mb-0 text-dark fw-bold">Select Trading Duration</h4>
        </div>
        <div class="card-body p-4">
            <div class="duration-buttons d-flex flex-wrap gap-3">
                <input type="radio" class="btn-check" name="duration" id="duration1" value="1" autocomplete="off"
                    checked>
                <label
                    class="btn btn-outline-primary d-flex align-items-center justify-content-center flex-grow-1 rounded-xl shadow-lg"
                    for="duration1">
                    <i class="fas fa-clock me-2"></i>1 Hour
                </label>

                <input type="radio" class="btn-check" name="duration" id="duration3" value="3" autocomplete="off">
                <label
                    class="btn btn-outline-primary d-flex align-items-center justify-content-center flex-grow-1 rounded-xl shadow-lg"
                    for="duration3">
                    <i class="fas fa-clock me-2"></i>3 Hours
                </label>

                <input type="radio" class="btn-check" name="duration" id="duration5" value="5" autocomplete="off">
                <label
                    class="btn btn-outline-primary d-flex align-items-center justify-content-center flex-grow-1 rounded-xl shadow-lg"
                    for="duration5">
                    <i class="fas fa-clock me-2"></i>5 Hours
                </label>
            </div>
        </div>
    </div>

    <!-- Amount Selection Section -->
    <div class="card bg-dark text-white border-0 mb-4">
        <div class="card-header bg-dark">
            <h4 class="card-title mb-0">Enter Investment Amount</h4>
        </div>
        <div class="card-body bg-dark">
            <div class="input-group mb-3 w-100">
                <input type="number" class="form-control bg-dark border-secondary w-full" id="investmentAmount"
                    placeholder="Enter amount" min="100" step="1">
            </div>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="button" class="btn btn-outline-primary" onclick="setPercentage(25)">25%</button>
                <button type="button" class="btn btn-outline-primary" onclick="setPercentage(50)">50%</button>
                <button type="button" class="btn btn-outline-primary" onclick="setPercentage(75)">75%</button>
                <button type="button" class="btn btn-outline-primary" onclick="setPercentage(100)">100%</button>
            </div>

            <div class="d-flex justify-content-between align-items-center text-black">
                <small class="text-muted">Available Balance:</small>
                <span class="inline-flex items-center rounded-md bg-purple-400/10 px-2 py-1 font-medium text-indigo-600 inset-ring inset-ring-indigo-400/30">$<span id="walletBalance" >0.00</span></span>
            </div>
        </div>
    </div>

    <!-- Exchange Pair Selection -->
    <div class="card bg-dark border-0 mb-4">
        <div class="card-header bg-dark">
            <h4 class="card-title mb-0">Select Exchange Pair</h4>
        </div>
        <div class="card-body bg-dark">
            <select class="form-select border-secondary" id="exchangePair">
                <option value="">Select Exchange Pair</option>
                <option value="BTC/USDT">BTC ↔ USDT</option>
                <option value="ETH/USDT">ETH ↔ USDT</option>
                <option value="BNB/USDT">BNB ↔ USDT</option>
                <option value="SOL/USDT">SOL ↔ USDT</option>
            </select>
        </div>
    </div>

    <!-- Trade Coin Selection -->
    <div class="card bg-dark  border-0 mb-4">
        <div class="card-header bg-dark">
            <h4 class="card-title mb-0">Select Trade Coin</h4>
        </div>
        <div class="card-body bg-dark">
            <select class="form-select bg-dark  border-secondary" id="tradeCoin">

                <option value="BTC">Bitcoin (BTC)</option>
                <option value="ETH">Ethereum (ETH)</option>
                <option value="BNB">Binance Coin (BNB)</option>
                <option value="SOL">Solana (SOL)</option>
            </select>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="text-center mb-5">
        <button class="btn btn-primary btn-lg px-5" onclick="submitTrade()">Start Trading</button>
    </div>
</div>




<style>


    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --secondary: #7c3aed;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --border-color: #e2e8f0;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius: 0.5rem;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-primary);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        -webkit-font-smoothing: antialiased;
    }



    .card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        background: var(--bg-white);
        box-shadow: var(--shadow);
        transition: var(--transition);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-bottom: none;
    }

    .card-header h4 {
        font-weight: 700;
        margin: 0;
        font-size: 1.25rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Package Cards */
    .package-card {
        cursor: pointer;
        transition: var(--transition);
        border-radius: var(--radius);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .package-card .card {
        position: relative;
        z-index: 2;
        background: white;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .package-card:hover .card {
        transform: translateY(-8px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }

    .package-card.selected .card {
        border: 2px solid var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
    }

    .package-ribbon {
        position: absolute;
        top: 15px;
        right: -25px;
        width: 120px;
        padding: 4px 0;
        background: var(--primary);
        color: white;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 600;
        transform: rotate(45deg) translateY(-5px) scale(1.02);
        transform-origin: center;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        pointer-events: none;
    }

    .package-ribbon.bg-warning {
        background: var(--warning);
    }

    .package-ribbon.bg-success {
        background: var(--success);
    }

    .package-ribbon.bg-danger {
        background: var(--danger);
    }

    .icon-shape {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-shape-primary {
        background: var(--gradient-primary);
    }

    .icon-shape-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    }

    .icon-shape-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .icon-shape-dark {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }

    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .package-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .btn-outline-primary {
        color: var(--primary);
        border-color: var(--primary);
        transition: var(--transition);
    }

    .btn-outline-primary:hover,
    .btn-check:checked+.btn-outline-primary {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
        transform: translateY(-2px);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border: none;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: var(--transition);
        padding: 0.75rem 2rem;
        font-size: 1.1rem;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        background: linear-gradient(135deg, var(--primary-hover) 0%, #6d28d9 100%);
    }

    .form-control,
    .form-select {
        border: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
        border-radius: var(--radius);
        transition: var(--transition);
        color: #1a202c;
        background-color: #ffffff;
        width: 100%;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }

    .input-group-text {
        background-color: var(--bg-light);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
    }

    .badge {
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.25rem;
        }

        .btn,
        .form-control,
        .form-select {
            font-size: 0.9rem;
            padding: 0.65rem 1rem;
        }
    }
</style>

<script>
    // Package Selection with animation
    function selectPackage(element, packageName) {
        // Remove selected class from all packages
        document.querySelectorAll('.package-card').forEach(card => {
            card.classList.remove('selected');
            const cardInner = card.querySelector('.card');
            if (cardInner) {
                cardInner.classList.remove('border-primary');
            }
        });

        // Add selected class to clicked package
        element.classList.add('selected');
        const selectedCard = element.querySelector('.card');
        if (selectedCard) {
            selectedCard.classList.add('border-primary');
        }

        // Update hidden input
        document.getElementById('selectedPackage').value = packageName;

        // Scroll to next section smoothly
        document.querySelector('.duration-section').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // Set percentage of wallet with animation
    function setPercentage(percent) {
        const walletBalance = 100000;
        const amount = (walletBalance * percent / 100).toFixed(2);
        const input = document.getElementById('investmentAmount');
        input.value = amount;
        input.classList.add('fade-in');
        setTimeout(() => input.classList.remove('fade-in'), 500);
    }

    // Submit trade with enhanced UI feedback
    function submitTrade() {
        const packageName = document.getElementById('selectedPackage').value;
        const duration = document.querySelector('input[name="duration"]:checked').value;
        const amount = document.getElementById('investmentAmount').value;
        const exchangePair = document.getElementById('exchangePair').value;
        const tradeCoin = document.getElementById('tradeCoin').value;

        if (!packageName) {
            Swal.fire({
                icon: 'warning',
                title: 'Package Required',
                text: 'Please select a package to continue',
                confirmButtonColor: 'var(--primary)',
                background: 'var(--bg-white)',
                color: 'var(--text-primary)',
                confirmButtonText: 'Got it!',
                customClass: {
                    popup: 'fade-in'
                }
            });
            return;
        }

        if (!amount || amount < 100) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Amount',
                text: 'Please enter a valid amount (minimum ₹100)',
                confirmButtonColor: 'var(--primary)',
                background: 'var(--bg-white)',
                color: 'var(--text-primary)',
                confirmButtonText: 'Okay',
                customClass: {
                    popup: 'fade-in'
                }
            });
            return;
        }

        // Simulate API call with loading state
        const submitBtn = document.querySelector('.btn-primary');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';

        // Simulate API delay
        setTimeout(() => {
            // Here you would typically send data to your server
            console.log('Trade submitted:', {
                package: packageName,
                duration: duration + ' hours',
                amount: amount,
                exchangePair: exchangePair,
                tradeCoin: tradeCoin
            });

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Trade Started!',
                text: 'Your trading session has been started successfully!',
                confirmButtonColor: 'var(--primary)',
                background: 'var(--bg-white)',
                color: 'var(--text-primary)',
                confirmButtonText: 'Great!',
                customClass: {
                    popup: 'fade-in'
                }
            });

            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }, 1500);
    }

    // Initialize wallet balance with count-up animation
    document.addEventListener('DOMContentLoaded', function () {
        const walletBalance = 100000;
        const balanceElement = document.getElementById('walletBalance');

        // Animate the balance
        const options = {
            useEasing: true,
            useGrouping: true,
            separator: ',',
            decimal: '.',
            prefix: '₹'
        };

        if (typeof CountUp === 'function') {
            const balanceAnimation = new CountUp('walletBalance', walletBalance, options);
            balanceAnimation.start();
        } else {
            balanceElement.textContent = walletBalance.toLocaleString('en-IN');
        }
    });
</script>

<?php
include_once 'footer.php';
?>