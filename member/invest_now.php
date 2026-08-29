<?php
$title = 'Investment Order';
$_is_dashboard = 1;
include_once 'header.php';
$childs = get_single_dimensional(get_child_levels($uid));
$recid = isset($_GET['i']) ? (int) $_GET['i'] : 0;
$row = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . $recid . "'"));
if (!$row) {
    redirect('invest.php');
}
$min = $row->amount_from * 1;
$max = $row->amount_to * 1;
if (!$user->name || !$user->email || !$user->mobile || !$user->bitcoin || !$user->wallet_address) {
    //redirect('profile.php');die;
}

// Generate random ROI between 5% and 25% for display purposes
$roi = $row->percentage;
// Randomly decide if it's positive or negative trend
$trend = rand(0, 10) > 2 ? 'up' : 'down';


$userInvestments = my_query("SELECT * FROM investments WHERE uid = '" . $uid . "' AND ipid = 2 or ipid = 3 ORDER BY datetime DESC LIMIT 1");

$userdata = mysqli_fetch_object($userInvestments);
?>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<style>
    /* Fresh Investment Now Theme - Same as Dashboard */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        /* Fresh Color Palette */
        --bg-primary: #f8fafc;
        --bg-secondary: #ffffff;
        --bg-accent: #e2e8f0;
        --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --text-muted: #718096;
        --brand-primary: #4f46e5;
        --brand-secondary: #7c3aed;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --border: #e5e7eb;
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius: 12px;
        --radius-lg: 16px;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--bg-primary);
        color: var(--text-primary);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
    }

    #page-wrapper {
        background: var(--bg-primary);
        margin-top: 0;
    }

    .content-header {
        display: none;
    }

    /* Fresh Container */
    .fresh-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Fresh Investment Header */
    .fresh-investment-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        border-radius: var(--radius-lg);
        padding: 32px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .fresh-investment-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    .fresh-investment-header-content {
        position: relative;
        z-index: 2;
    }

    .fresh-investment-header h1 {
        font-size: 2.6rem;
        font-weight: 900;
        margin-bottom: 16px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fresh-investment-header p {
        font-size: 1.3rem;
        font-weight: 600;
        opacity: 0.9;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .fresh-investment-header-icon {
        font-size: 2.5rem;
        margin-bottom: 16px;
        opacity: 0.9;
    }

    /* Fresh Card */
    .fresh-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        margin-bottom: 32px;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out;
    }

    .fresh-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    /* Fresh Form Styles */
    .fresh-form-group {
        margin-bottom: 28px;
    }

    .fresh-label {
        display: block;
        margin-bottom: 10px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 1.2rem;
    }

    .fresh-input {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .fresh-input:focus {
        outline: none;
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .fresh-input::placeholder {
        color: var(--text-muted);
    }

    /* Fresh Button */
    .fresh-btn {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        border: none;
        border-radius: var(--radius);
        padding: 18px 32px;
        font-size: 1.3rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .fresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: linear-gradient(135deg, #5b52f0 0%, #8b5cf6 100%);
    }

    .fresh-btn:active {
        transform: translateY(0);
    }

    /* Fresh Investment Plan Card */
    .fresh-plan-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .fresh-plan-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-primary);
    }

    .fresh-plan-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border);
    }

    .fresh-plan-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        box-shadow: var(--shadow);
    }

    .fresh-plan-info h3 {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
    }

    .fresh-plan-info p {
        color: var(--text-secondary);
        margin: 0;
        font-size: 1.1rem;
    }

    /* Fresh Stats Grid */
    .fresh-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .fresh-stat-item {
        background: var(--bg-accent);
        border-radius: var(--radius);
        padding: 16px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .fresh-stat-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .fresh-stat-label {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin-bottom: 6px;
        font-weight: 600;
    }

    .fresh-stat-value {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    /* Fresh Range Slider */
    .fresh-range-container {
        margin: 20px 0;
    }

    .fresh-range-slider {
        width: 100%;
        height: 6px;
        border-radius: 3px;
        background: var(--bg-accent);
        outline: none;
        -webkit-appearance: none;
        transition: all 0.3s ease;
    }

    .fresh-range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .fresh-range-slider::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        box-shadow: var(--shadow-lg);
    }

    .fresh-range-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
    }

    .fresh-range-label {
        font-size: 1.1rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* Fresh Input Group */
    .fresh-input-group {
        display: flex;
        border-radius: var(--radius);
        overflow: hidden;
        border: 2px solid var(--border);
        transition: all 0.3s ease;
    }

    .fresh-input-group:focus-within {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .fresh-input-group input {
        flex: 1;
        border: none;
        padding: 16px 20px;
        background: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 1.2rem;
    }

    .fresh-input-group input:focus {
        outline: none;
    }

    .fresh-input-addon {
        background: var(--bg-accent);
        color: var(--text-secondary);
        padding: 16px 20px;
        font-weight: 700;
        font-size: 1.2rem;
        border-left: 1px solid var(--border);
    }

    /* Fresh Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    /* Fresh Alert/Notification */
    .fresh-alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: fadeInUp 0.6s ease-out;
    }

    .fresh-alert.success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: var(--success);
    }

    .fresh-alert.info {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: var(--info);
    }

    .fresh-alert.warning {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: var(--warning);
    }

    /* Fresh Mobile Responsive */
    @media (max-width: 768px) {
        .fresh-container {
            padding: 16px;
        }

        .fresh-investment-header {
            padding: 24px;
        }

        .fresh-investment-header h1 {
            font-size: 2.2rem;
            font-weight: 900;
        }

        .fresh-investment-header p {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .fresh-stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .fresh-plan-header {
            flex-direction: column;
            text-align: center;
            gap: 12px;
        }

        .fresh-plan-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }

        .fresh-btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Animation Classes */
    .fadeInUp {
        animation: fadeInUp 0.6s ease-out;
    }

    .fadeInLeft {
        animation: fadeInLeft 0.6s ease-out;
    }

    .fadeInRight {
        animation: fadeInRight 0.6s ease-out;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    /* Fresh Card Animation Delays */
    .fresh-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .fresh-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .fresh-card:nth-child(3) {
        animation-delay: 0.3s;
    }
</style>
</style>


<!-- Fresh Investment Container -->
<div class="fresh-container">
    <!-- Fresh Investment Header -->
    <div class="fresh-investment-header fadeInUp">
        <div class="fresh-investment-header-content">
            <div class="fresh-investment-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h1>Complete Your Investment</h1>
            <p>Secure your financial future with our premium investment plan</p>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="fresh-card fadeInUp" style="animation-delay: 0.1s;">
        <div style="padding: 20px; text-align: center;">
            <div style="display: inline-flex; align-items: center; gap: 20px; background: linear-gradient(135deg, var(--success) 0%, #059669 100%); color: white; padding: 20px 28px; border-radius: var(--radius-lg); box-shadow: var(--shadow);">
                <i class="fas fa-wallet" style="font-size: 2rem;"></i>
                <div>
                    <div style="font-size: 1.2rem; opacity: 0.9; font-weight: 600;">Available Balance</div>
                    <div style="font-size: 2rem; font-weight: 900;">
                        $<?php echo number_format($user->wallet_topup * 1, 2); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Investment Details -->
        <div class="col-md-4">
            <div class="fresh-card fadeInLeft">
                <div class="fresh-plan-header">
                    <div class="fresh-plan-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="fresh-plan-info">
                        <h3><?php echo $row->title; ?></h3>
                        <p><?php echo $row->description; ?></p>
                    </div>
                </div>

                <div class="fresh-stats-grid">
                    <div class="fresh-stat-item">
                        <div class="fresh-stat-label">Min Investment</div>
                        <div class="fresh-stat-value">$<?php echo number_format($min); ?></div>
                    </div>
                    <div class="fresh-stat-item">
                        <div class="fresh-stat-label">Max Investment</div>
                        <div class="fresh-stat-value">$<?php echo number_format($max); ?></div>
                    </div>
                    <div class="fresh-stat-item">
                        <div class="fresh-stat-label">Expected ROI</div>
                        <div class="fresh-stat-value" style="color: var(--success);">+<?php echo $roi; ?>%</div>
                    </div>
                    <div class="fresh-stat-item">
                        <div class="fresh-stat-label">Duration</div>
                        <div class="fresh-stat-value">Daily</div>
                    </div>
                </div>

                <div class="fresh-alert info pulse">
                    <i class="fas fa-users"></i>
                    <div>
                        <strong><?php echo rand(100, 999); ?>+ investors</strong> have joined this plan
                    </div>
                </div>

                <div class="fresh-alert success">
                    <i class="fas fa-bolt"></i>
                    <div>
                        <?php echo rand(5, 20); ?> new investments in the last hour
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Order Form -->
        <div class="col-md-8">
            <div class="fresh-card fadeInRight">
                <div style="padding: 32px;">
                    <div style="text-align: center; margin-bottom: 32px;">
                        <h2 style="font-size: 2.2rem; font-weight: 800; color: var(--text-primary); margin-bottom: 12px;">
                            Investment Order Form
                        </h2>
                        <p style="color: var(--text-secondary); font-size: 1.3rem; font-weight: 600;">
                            Complete your investment in <?php echo $row->title; ?>
                        </p>
                    </div>

                    <form class="form-horizontal" action="invest_now_model.php" method="post">
                        <?php if ($min != $max) { ?>
                            <div class="fresh-form-group">
                                <label class="fresh-label">Investment Amount</label>
                                <div class="fresh-input-group">
                                    <input class="fresh-input" type="text" id="invest_amount" name="amount" maxlength="20" required="required" placeholder="Enter amount" style="border: none;">
                                    <span class="fresh-input-addon">USD</span>
                                </div>
                                <div class="fresh-range-container">
                                    <input type="range" min="<?php echo $min; ?>" max="<?php echo $max; ?>" value="<?php echo $min; ?>" id="investment_range" class="fresh-range-slider">
                                </div>
                                <div class="fresh-range-labels">
                                    <div class="fresh-range-label">$<?php echo number_format($min); ?></div>
                                    <div class="fresh-range-label">$<?php echo number_format($max); ?></div>
                                </div>
                                <div id="calculator_msg" style="margin-top: 12px; color: var(--text-muted); font-size: 1.1rem;"></div>
                            </div>


                            <?php if ($recid == 2) { ?>
                                <div class="fresh-form-row">
                                    <div class="fresh-form-col">
                                        <label class="fresh-form-label" for="time">Trading cycles (hr) : </label>&nbsp;&nbsp;
                                        <input type="radio" name="time" value="1" checked required /> 1hr &nbsp;&nbsp;

                                        <input type="radio" name="time" value="3" required /> 3hr &nbsp;&nbsp;

                                        <input type="radio" name="time" value="5" required /> 5 hr &nbsp;&nbsp;
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($recid == 3) { ?>
                                <div class="fresh-form-row">
                                    <div class="fresh-form-col">
                                        <label class="fresh-form-label" for="time">Trading cycles (hr) : </label>&nbsp;&nbsp;
                                        <input type="radio" name="time" value="24" checked required /> 24hr &nbsp;&nbsp;
                                    </div>
                                </div>
                            <?php } ?>

                        <?php } else { ?>
                            <div class="fresh-form-group">
                                <label class="fresh-label">Fixed Investment Amount</label>
                                <div class="fresh-input-group">
                                    <input class="fresh-input" type="text" value="$<?php echo number_format($min); ?>" readonly style="border: none;">
                                    <span class="fresh-input-addon">USD</span>
                                </div>
                            </div>
                            <?php if ($recid == 2) { ?>
                                <div class="fresh-form-row">
                                    <div class="fresh-form-col">
                                        <label class="fresh-form-label" for="time">Trading cycles (hr) : </label>&nbsp;&nbsp;
                                        <input type="radio" name="time" value="1" checked required /> 1hr &nbsp;&nbsp;

                                        <input type="radio" name="time" value="3" required /> 3hr &nbsp;&nbsp;

                                        <input type="radio" name="time" value="5" required /> 5 hr &nbsp;&nbsp;
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($recid == 3) { ?>
                                <div class="fresh-form-row">
                                    <div class="fresh-form-col">
                                        <label class="fresh-form-label" for="time">Trading cycles (hr) : </label>&nbsp;&nbsp;
                                        <input type="radio" name="time" value="24" checked required /> 24hr &nbsp;&nbsp;
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <div class="fresh-card" style="background: var(--bg-accent); margin: 24px 0; border: none;">
                            <div style="padding: 24px;">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                                    <i class="fas fa-calculator" style="color: var(--brand-primary); font-size: 1.2rem;"></i>
                                    <h3 style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); margin: 0;">
                                        Investment Summary
                                    </h3>
                                </div>

                                <div style="display: grid; gap: 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: var(--text-secondary); font-weight: 600; font-size: 1.1rem;">Investment Amount:</span>
                                        <span style="font-weight: 800; color: var(--text-primary); font-size: 1.2rem;" id="summary_amount">$0.00</span>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: var(--text-secondary); font-weight: 600; font-size: 1.1rem;">Expected Daily Return:</span>
                                        <span style="font-weight: 800; color: var(--success); font-size: 1.2rem;" id="summary_daily_return">$0.00</span>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: var(--text-secondary); font-weight: 600; font-size: 1.1rem;">Expected Monthly Return:</span>
                                        <span style="font-weight: 800; color: var(--success); font-size: 1.2rem;" id="summary_monthly_return">$0.00</span>
                                    </div>

                                    <div style="border-top: 2px solid var(--border); padding-top: 12px; margin-top: 8px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="color: var(--text-primary); font-weight: 800; font-size: 1.3rem;">Total Expected (30 days):</span>
                                            <span style="font-weight: 900; color: var(--brand-primary); font-size: 1.4rem;" id="summary_total">$0.00</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="fresh-alert info" style="margin-top: 16px; margin-bottom: 0;">
                                    <i class="fas fa-info-circle"></i>
                                    <div style="font-size: 1.1rem; font-weight: 600;">
                                        Returns calculated based on daily ROI of <?php echo $roi; ?>%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="fresh-form-group">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                                <input type="checkbox" id="terms_check" checked style="width: 20px; height: 20px; accent-color: var(--brand-primary);">
                                <label for="terms_check" style="color: var(--text-secondary); font-size: 1.1rem; font-weight: 600; cursor: pointer;">
                                    I agree to the <a href="#" style="color: var(--brand-primary); text-decoration: none; font-weight: 700;">Terms and Conditions</a>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="recid" value="<?php echo $recid; ?>" />
                        <input type="hidden" id="invest_type" name="type" value="<?php echo $row->type; ?>" />
                        <input type="hidden" name="plan_id" value="<?php echo $row->recid; ?>">
                        <?php if ($min == $max) { ?>
                            <input type="hidden" name="amount" value="<?php echo $row->amount_from; ?>" />
                        <?php } ?>
                        <input type="hidden" name="min" value="<?php echo $min; ?>" />
                        <input type="hidden" name="max" value="<?php echo $max; ?>" />






                        <!-- <div style="text-align: center; margin-top: 32px;">
                            <button type="submit" class="fresh-btn" style="width: 100%; font-size: 1.2rem; padding: 18px 32px;">
                                <i class="fas fa-rocket"></i>
                                Invest Now
                            </button>
                        </div> -->

                        <?php
                        // if():
                        // Fetch user investment for this plan
                        $userInvestment = my_query("SELECT * FROM investments WHERE uid = '$uid' AND ipid IN (2,3) AND ipid = $recid ORDER BY datetime DESC LIMIT 1");
                        $userdata = mysqli_fetch_object($userInvestment);

                        // Default button type
                        $buttonType = 'invest';
                        $remainingSeconds = 0;

                        if ($userdata) {
                            $investTime = strtotime($userdata->datetime);
                            $cycleHours = (int)$userdata->invest_hour; // hours per investment
                            $elapsed = time() - $investTime;

                            if ($userdata->is_closed == 1) {
                                $buttonType = 'invest'; // closed → allow investing again
                            } elseif ($elapsed < ($cycleHours * 3600)) {
                                $buttonType = 'running';
                                $remainingSeconds = ($cycleHours * 3600) - $elapsed;
                            } else {
                                $buttonType = 'closed';
                            }
                        }

                        // Render buttons
                        ?>

                        <?php if ($buttonType == 'running'): ?>
                            <div class="fresh-alert success" style="margin-top: 20px; text-align: center;">
                                <i class="fas fa-clock"></i>
                                <div style="font-size: 1.1rem; font-weight: 600;" id="invest-btn-<?php echo $recid; ?>">
                                    Left — <span id="countdown-<?php echo $recid; ?>"></span>
                                </div>
                            </div>

                            <script>
                                let remaining<?php echo $recid; ?> = <?php echo $remainingSeconds; ?>;
                                const countdownEl<?php echo $recid; ?> = document.getElementById("countdown-<?php echo $recid; ?>");
                                const btnEl<?php echo $recid; ?> = document.getElementById("invest-btn-<?php echo $recid; ?>");

                                function updateCountdown<?php echo $recid; ?>() {
                                    if (remaining<?php echo $recid; ?> <= 0) {
                                        countdownEl<?php echo $recid; ?>.innerHTML = "00h 00m 00s";
                                        btnEl<?php echo $recid; ?>.innerHTML = '<i class="fas fa-times-circle"></i> Closed';
                                        btnEl<?php echo $recid; ?>.classList.remove('running-btn');
                                        btnEl<?php echo $recid; ?>.classList.add('closed-btn');
                                        return;
                                    }
                                    let hours = Math.floor(remaining<?php echo $recid; ?> / 3600);
                                    let minutes = Math.floor((remaining<?php echo $recid; ?> % 3600) / 60);
                                    let seconds = remaining<?php echo $recid; ?> % 60;
                                    countdownEl<?php echo $recid; ?>.innerHTML =
                                        `${hours.toString().padStart(2,'0')}h ${minutes.toString().padStart(2,'0')}m ${seconds.toString().padStart(2,'0')}s`;
                                    remaining<?php echo $recid; ?>--;
                                }

                                updateCountdown<?php echo $recid; ?>();
                                setInterval(updateCountdown<?php echo $recid; ?>, 1000);
                            </script>

                        <?php elseif ($buttonType == 'closed' && $userdata->is_closed == 0): ?>
                            <form id="close-form-<?php echo $recid; ?>" action="self_treding_model.php" method="post">
                                <input type="hidden" name="investment_id" value="<?php echo $userdata->recid; ?>">
                            </form>
                            <div style="text-align: center; margin-top: 20px;">
                                <button class="btn btn-danger btn-lg" style="border-radius:25px;"
                                    onclick="document.getElementById('close-form-<?php echo $recid; ?>').submit();">
                                    <i class="fas fa-times-circle"></i> Close
                                </button>
                            </div>

                        <?php else: ?>
                            <div style="text-align: center; margin-top: 32px;">
                                <button class="fresh-btn" style="width: 100%; font-size: 1.2rem; padding: 18px 32px;"
                                    onclick="window.location.href='invest_now.php?i=<?php echo $recid; ?>'">
                                    <i class="fas fa-rocket"></i> Invest Now
                                </button>
                            </div>
                        <?php endif; ?>




                       







                        <div class="fresh-alert success" style="margin-top: 20px; text-align: center;">
                            <i class="fas fa-shield-alt"></i>
                            <div style="font-size: 1.1rem; font-weight: 600;">
                                Your investment is secured with 256-bit SSL encryption
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script>
    // Fresh Investment Now Page Interactions
    $(document).ready(function() {
        // Investment amount range slider
        $('#investment_range').on('input', function() {
            const value = $(this).val();
            $('#invest_amount').val(value);
            updateCalculator();
        });

        // Investment amount input
        $('#invest_amount').on('input', function() {
            const value = $(this).val();
            const min = parseInt($('#investment_range').attr('min'));
            const max = parseInt($('#investment_range').attr('max'));

            if (value >= min && value <= max) {
                $('#investment_range').val(value);
            }
            updateCalculator();
        });

        // Calculator function with fresh styling
        function updateCalculator() {
            const amount = parseFloat($('#invest_amount').val()) || 0;
            const roi = <?php echo $roi; ?>;

            const dailyReturn = (amount * roi) / 100;
            const monthlyReturn = dailyReturn * 30;
            const totalReturn = amount + monthlyReturn;

            $('#summary_amount').text('$' + amount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#summary_daily_return').text('$' + dailyReturn.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#summary_monthly_return').text('$' + monthlyReturn.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#summary_total').text('$' + totalReturn.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Update calculator message with fresh styling
            if (amount > 0) {
                $('#calculator_msg').html(`
                <div style="display: flex; align-items: center; gap: 8px; color: var(--success); font-weight: 600;">
                    <i class="fas fa-calculator"></i>
                    Daily profit: $${dailyReturn.toFixed(2)}
                </div>
            `);
            } else {
                $('#calculator_msg').text('');
            }
        }

        // Initialize calculator
        updateCalculator();

        // Form validation (keeping original logic)
        window.abc_ = function() {
            const amount = parseFloat($('#invest_amount').val());
            const min = <?php echo $min; ?>;
            const max = <?php echo $max; ?>;
            const balance = <?php echo $user->wallet_topup * 1; ?>;

            // if (!amount || amount < min || amount > max) {
            //   alert(`Investment amount must be between $${min.toLocaleString()} and $${max.toLocaleString()}`);
            // return false;
            //}

            if (amount > balance) {
                alert('Insufficient balance. Please top up your wallet.');
                return false;
            }

            if (!$('#terms_check').is(':checked')) {
                alert('Please accept the terms and conditions');
                return false;
            }

            return true;
        };

        // Fresh button loading animation
        // $('form').on('submit', function() {
        //     const submitBtn = $(this).find('.fresh-btn');
        //     submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing Investment...');
        //     submitBtn.prop('disabled', true);
        //     submitBtn.css('opacity', '0.7');
        // });
        $('form').on('submit', function(e) {
            if (!abc_()) {
                e.preventDefault(); // Stop submission
                return false;
            }
            const submitBtn = $(this).find('.fresh-btn');
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing Investment...');
            submitBtn.prop('disabled', true);
            submitBtn.css('opacity', '0.7');
        });
        // Fresh scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe fresh cards
        document.querySelectorAll('.fresh-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });

        // Fresh hover effects for buttons
        $('.fresh-btn').hover(
            function() {
                $(this).css('transform', 'translateY(-2px) scale(1.02)');
            },
            function() {
                $(this).css('transform', 'translateY(0) scale(1)');
            }
        );

        // Fresh card hover effects
        $('.fresh-card').hover(
            function() {
                $(this).css('transform', 'translateY(-4px)');
            },
            function() {
                $(this).css('transform', 'translateY(0)');
            }
        );

        // Animate stats on load
        setTimeout(() => {
            $('.fresh-stat-value').each(function(index) {
                $(this).css({
                    'animation': `fadeInUp 0.6s ease-out ${index * 0.1}s both`
                });
            });
        }, 500);
    });
</script>