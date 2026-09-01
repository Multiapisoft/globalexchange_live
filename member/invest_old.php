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



// if (time() - strtotime($userdata->datetime) <= $userdata->invest_hour * 3600) {
//     setMessage('You can only invest once every 24 hours. Please try again later.', 'danger');
//     // redirect('./invest.php');
//     // exit;
// }



?>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include jQuery CountUp plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-countup/1.0.0/jquery.countup.min.js"></script>
<!-- Include jQuery Waypoints (required for CountUp) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
<!-- Include Chart.js for trading charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Fresh Investment Theme - Same as Dashboard */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        /* Fresh Color Palette */
        --bg-primary: #050505;
        --bg-secondary: #121212;
        --bg-accent: #1a1a1a;
        --text-primary: #f5f5f5;
        --text-secondary: #a0a0a0;
        --text-muted: #6b6b6b;
        --brand-primary: #f0b90b;
        --brand-secondary: #c9970a;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --border: rgba(212, 175, 55, 0.22);
        --shadow: 0 8px 28px rgba(0, 0, 0, 0.45);
        --shadow-lg: 0 16px 40px rgba(0, 0, 0, 0.55);
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
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 16px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fresh-investment-header p {
        font-size: 1.4rem;
        font-weight: 600;
        opacity: 0.9;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .fresh-investment-header-icon {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.9;
    }

    /* Fresh Stats Grid */
    .fresh-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .fresh-stat-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        padding: 24px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .fresh-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-primary);
    }

    .fresh-stat-icon {
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
    }

    .fresh-stat-content {
        flex: 1;
    }

    .fresh-stat-label {
        font-size: 1.2rem;
        color: var(--text-secondary);
        font-weight: 600;
        margin-bottom: 6px;
    }

    .fresh-stat-value {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text-primary);
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
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

    /* Fresh Section Header */
    .fresh-section-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .fresh-section-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 16px;
        text-shadow: 0 2px 4px rgba(255, 255, 255, 0.2);
    }

    .fresh-section-icon {
        font-size: 1.8rem;
    }

    /* Fresh Investment Plan Cards */
    .fresh-investment-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .fresh-investment-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-primary);
    }

    .fresh-investment-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    }

    .fresh-plan-header {
        padding: 24px;
        text-align: center;
        position: relative;
    }

    .fresh-plan-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 16px;
        box-shadow: var(--shadow);
    }

    .fresh-plan-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 10px;
    }

    .fresh-plan-subtitle {
        font-size: 1.2rem;
        color: var(--text-secondary);
        margin-bottom: 18px;
        font-weight: 600;
    }

    .fresh-plan-amount {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
        padding: 14px 24px;
        border-radius: 25px;
        font-size: 1.3rem;
        font-weight: 700;
        display: inline-block;
        box-shadow: var(--shadow);
    }

    .fresh-plan-features {
        padding: 24px;
        flex: 1;
    }

    .fresh-plan-features ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .fresh-plan-features li {
        padding: 14px 0;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border);
        position: relative;
        font-size: 1.1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    .fresh-plan-features li:before {
        content: '✓';
        color: var(--success);
        margin-right: 12px;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .fresh-plan-features li:last-child {
        border-bottom: none;
    }

    .fresh-plan-button {
        padding: 24px;
        text-align: center;
    }

    .fresh-invest-btn {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        border: none;
        border-radius: 25px;
        padding: 16px 36px;
        font-size: 1.3rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: 100%;
    }

    .fresh-invest-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: linear-gradient(135deg, #5b52f0 0%, #8b5cf6 100%);
    }

    .fresh-invest-btn:active {
        transform: translateY(0);
    }

    /* Fresh Badge Styles */
    .fresh-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 700;
        z-index: 10;
    }

    .fresh-badge.hot {
        background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        color: white;
    }

    .fresh-badge.new {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
    }

    .fresh-badge.popular {
        background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
        color: white;
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
            font-size: 2.4rem;
            font-weight: 900;
        }

        .fresh-investment-header p {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .fresh-stats-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .fresh-section-title {
            font-size: 1.3rem;
            font-weight: 800;
        }

        .fresh-plan-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .fresh-plan-title {
            font-size: 1.2rem;
        }

        .fresh-invest-btn {
            padding: 12px 24px;
            font-size: 1rem;
        }
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

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .fresh-card {
        animation: fadeInUp 0.6s ease-out;
    }

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

<!-- Fresh Investment Container -->
<div class="fresh-container">
    <!-- Fresh Investment Header -->
    <div class="fresh-investment-header">
        <div class="fresh-investment-header-content">
            <div class="fresh-investment-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h1>Investment Plans</h1>
            <h3>Choose the perfect investment plan for your financial goals</h3>
        </div>
    </div>

    <!-- Fresh Stats Grid -->
    <div class="fresh-stats-grid">
        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Available Plans</div>
                <div class="fresh-stat-value"><?php echo $total_plans; ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Investment Value</div>
                <div class="fresh-stat-value">$<?php echo number_format($total_value, 0); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Active Investors</div>
                <div class="fresh-stat-value"><?php echo number_format($active_investors); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Success Rate</div>
                <div class="fresh-stat-value">98.5%</div>
            </div>
        </div>
    </div>

    <!-- Fresh Investment Plans Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-chart-pie"></i>
                Available Investment Plans
            </h2>
        </div>
        <?php /*<div class=row>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-1">
            <h2><span class="count-number"><?php echo $user->wallet * 1; ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class="small">Wallet</div>
            <i class="ti-server statistic_icon"></i>
            <div class="sparkline1 text-center"></div>
        </div>
    </div>
</div>*/ ?>
        <style>
            .plan-1 .ic,
            .plan-1 .promo .price,
            .plan-2 .ic,
            .plan-2 .promo .price,
            .plan-3 .ic,
            .plan-3 .promo .price,
            .plan-4 .ic,
            .plan-4 .promo .price {
                background: linear-gradient(135deg, #F0B90B, #FFDD33);
            }

            .button {
                border: none;
                border-radius: 4px;
                background: #F0B90B;
                color: #0B0E11;
                padding: 10px 37px;
                font-weight: 600;
            }
        </style>
        <style>
            .ic {
                position: absolute;
                top: 0px;
                left: 41%;
            }

            /* Binance-inspired Dark Theme with Gold Accents */

            body {
                background-color: #0B0E11;
                color: #eaecef;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }

            /* Plan Card Styles */
            .promos {
                margin-bottom: 30px;
            }

            .promo {
                background: #181A20;
                border-radius: 4px;
                border: 1px solid #2B3139;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease;
                overflow: hidden;
                position: relative;
                height: 100%;
                display: flex;
                flex-direction: column;
                animation: fadeIn 0.5s ease-in-out;
                animation-fill-mode: both;
                animation-delay: calc(var(--animation-order) * 0.1s);
            }

            .promo:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                border-color: #F0B90B;
            }

            .promo:before {
                content: '';
                position: absolute;
                top: -50px;
                right: -50px;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: rgba(240, 185, 11, 0.05);
                z-index: 0;
            }

            .promo:after {
                content: '';
                position: absolute;
                bottom: -50px;
                left: -50px;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: rgba(240, 185, 11, 0.05);
                z-index: 0;
            }

            .promo .deal {
                padding: 30px 20px 15px;
                position: relative;
                z-index: 1;
            }

            .promo .deal .ic {
                width: 102px;
                height: 98px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            /* Plan Color Schemes - All using Binance gold */
            .plan-1 .ic,
            .plan-1 .promo .price,
            .plan-2 .ic,
            .plan-2 .promo .price,
            .plan-3 .ic,
            .plan-3 .promo .price,
            .plan-4 .ic,
            .plan-4 .promo .price {
                background: linear-gradient(135deg, #F0B90B, #FFDD33);
            }

            .promo .deal span {
                display: block;
                text-align: center;
            }

            .promo .deal span:first-of-type {
                font-size: 1.3rem;
                font-weight: 700;
                margin-bottom: 8px;
                color: #F0B90B;
            }

            .promo .deal span:last-of-type {
                font-size: 21px;
                color: #B7BDC6;
                line-height: 1.5;
            }

            .promo .price {
                padding: 15px;
                text-align: center;
                color: #0B0E11;
                font-weight: 700;
                font-size: 1.2rem;
                margin-top: 46px;
                position: relative;
                z-index: 1;
            }

            .promo .features {
                padding: 25px 20px;
                list-style: none;
                margin: 0;
                text-align: center;
                flex: 1;
                position: relative;
                z-index: 1;
            }

            .promo .features li {
                padding: 10px 0;
                color: #eaecef;
                border-bottom: 1px solid rgba(240, 185, 11, 0.1);
                position: relative;
            }

            .promo .features li:before {
                content: '✓';
                color: #F0B90B;
                margin-right: 8px;
                font-weight: bold;
            }

            .promo .features li:last-child {
                border-bottom: none;
            }

            .button {
                display: block;
                padding: 12px 30px;
                margin: 20px auto 25px;
                border: none;
                border-radius: 4px;
                font-weight: 600;
                font-size: 1rem;
                transition: all 0.3s ease;
                cursor: pointer;
                position: relative;
                z-index: 1;
                background: #F0B90B;
                color: #0B0E11;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .button:hover {
                background: #F8D12F;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .button:active {
                transform: translateY(0);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            /* All buttons use the same Binance gold style */
            .plan-1 .button,
            .plan-2 .button,
            .plan-3 .button,
            .plan-4 .button {
                background: #F0B90B;
                color: #0B0E11;
            }

            .plan-1 .button:hover,
            .plan-2 .button:hover,
            .plan-3 .button:hover,
            .plan-4 .button:hover {
                background: #F8D12F;
            }

            /* Enhanced Responsive Adjustments */
            @media (max-width: 1200px) {
                .trading-pairs-container {
                    flex-direction: column;
                }

                .pairs-list {
                    width: 100%;
                    max-height: 300px;
                    margin-bottom: 20px;
                }

                .market-overview {
                    flex-wrap: wrap;
                }

                .market-card {
                    min-width: 45%;
                    margin-bottom: 15px;
                }
            }

            @media (max-width: 992px) {
                .trading-chart-container {
                    height: 250px;
                }

                .chart-controls {
                    flex-wrap: wrap;
                }

                .chart-control-btn {
                    margin-bottom: 5px;
                }

                .market-card {
                    min-width: 100%;
                }
            }

            @media (max-width: 768px) {
                .promo {
                    margin-bottom: 30px;
                }

                .section-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .section-actions {
                    margin-top: 10px;
                    width: 100%;
                    justify-content: space-between;
                }

                .trading-nav {
                    padding: 5px 10px;
                }

                .trading-nav-item {
                    padding: 6px 10px;
                    font-size: 14px;
                }

                .chart-title {
                    font-size: 16px;
                }
            }

            @media (max-width: 576px) {
                .trading-chart-container {
                    height: 200px;
                    padding: 15px;
                }

                .chart-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .chart-controls {
                    margin-top: 10px;
                    width: 100%;
                    justify-content: space-between;
                }

                .chart-control-btn {
                    padding: 4px 8px;
                    font-size: 12px;
                }

                .price-value {
                    font-size: 18px;
                }

                .section-header h2 {
                    font-size: 18px;
                }

                .section-action-btn {
                    padding: 5px 10px;
                    font-size: 12px;
                }

                .button {
                    padding: 10px 20px;
                    font-size: 14px;
                }
            }
        </style>






        <!-- Trading Pairs Layout -->
        <div class="trading-pairs-container">
            <!-- Trading Pairs List -->

            <!-- Investment Cards in Trading Style -->
            <div class="row" style="flex: 1;">
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
                ?>


                    <?php if ($row->action == 1 || $row->action == 2): ?>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <div class="fresh-investment-card" style="animation-delay: <?php echo $i * 0.1; ?>s;">

                                <!-- Fresh Badge for random investments -->
                                <?php if (rand(0, 5) == 1): ?>
                                    <div class="fresh-badge hot">
                                        <i class="fas fa-fire"></i> HOT
                                    </div>
                                <?php elseif (rand(0, 7) == 1): ?>
                                    <div class="fresh-badge new">
                                        <i class="fas fa-certificate"></i> NEW
                                    </div>
                                <?php elseif (rand(0, 4) == 1): ?>
                                    <div class="fresh-badge popular">
                                        <i class="fas fa-star"></i> POPULAR
                                    </div>
                                <?php endif; ?>

                                <div class="fresh-plan-header">
                                    <div class="fresh-plan-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="fresh-plan-title"><?php echo $row->title; ?></div>
                                    <div class="fresh-plan-subtitle">Professional Investment Plan</div>
                                    <div class="fresh-plan-amount">
                                        <?php echo ($row->amount_from == $row->amount_to)
                                            ? '$' . number_format($row->amount_from)
                                            : '$' . number_format($row->amount_from) . ' - $' . number_format($row->amount_to); ?>
                                    </div>
                                </div>

                                <div class="fresh-plan-features">
                                    <ul>
                                        <?php if ($row->line1) { ?><li><?php echo $row->line1; ?></li><?php } ?>
                                        <?php if ($row->line2) { ?><li><?php echo $row->line2; ?></li><?php } ?>
                                        <li>Expected ROI: <?php echo number_format($row->percentage_to/30, 2); ?>%</li>
                                        <li>24/7 Customer Support</li>
                                        <li>Secure & Regulated Platform</li>
                                    </ul>
                                </div>

                                <div class="fresh-plan-button">



                                    <?php
                                    // Check if user has any investment for this plan
                                    $userInvestment = my_query("SELECT * FROM investments WHERE uid = '$uid' AND ipid In (2,3) AND ipid = $row->recid ORDER BY datetime DESC LIMIT 1");
                                    $userdata = mysqli_fetch_object($userInvestment);

                                    $buttonType = 'invest'; // default button
                                    $remainingSeconds = 0;

                                    if ($userdata) {
                                        $investTime = strtotime($userdata->datetime);
                                        $cycleHours = (int)$userdata->invest_hour; // from DB: 1,3,5,24 etc.
                                        $elapsed = time() - $investTime;

                                        if ($userdata->is_closed == 1) {
                                            $buttonType = 'invest'; // closed → allow invest again
                                        } elseif ($elapsed < ($cycleHours * 3600)) {
                                            $buttonType = 'running';
                                            $remainingSeconds = ($cycleHours * 3600) - $elapsed;
                                        } else {
                                            $buttonType = 'closed';
                                        }
                                    }

                                    // Render button
                                    if ($buttonType == 'running'): ?>
                                        <button id="invest-btn-<?php echo $row->recid; ?>" class="fresh-plan-amount running-btn" disabled>
                                            <i class="fas fa-clock"></i> Left — <span id="countdown-<?php echo $row->recid; ?>"></span>
                                        </button>

                                        <script>
                                            let remaining<?php echo $row->recid; ?> = <?php echo $remainingSeconds; ?>;
                                            const countdownEl<?php echo $row->recid; ?> = document.getElementById("countdown-<?php echo $row->recid; ?>");
                                            const btnEl<?php echo $row->recid; ?> = document.getElementById("invest-btn-<?php echo $row->recid; ?>");

                                            function updateCountdown<?php echo $row->recid; ?>() {
                                                if (remaining<?php echo $row->recid; ?> <= 0) {
                                                    countdownEl<?php echo $row->recid; ?>.innerHTML = "00h 00m 00s";
                                                    btnEl<?php echo $row->recid; ?>.innerHTML = '<i class="fas fa-times-circle"></i> Closed';
                                                    btnEl<?php echo $row->recid; ?>.disabled = true;
                                                    btnEl<?php echo $row->recid; ?>.classList.remove('running-btn');
                                                    btnEl<?php echo $row->recid; ?>.classList.add('closed-btn');
                                                    return;
                                                }
                                                let hours = Math.floor(remaining<?php echo $row->recid; ?> / 3600);
                                                let minutes = Math.floor((remaining<?php echo $row->recid; ?> % 3600) / 60);
                                                let seconds = remaining<?php echo $row->recid; ?> % 60;
                                                countdownEl<?php echo $row->recid; ?>.innerHTML = `${hours.toString().padStart(2,'0')}h ${minutes.toString().padStart(2,'0')}m ${seconds.toString().padStart(2,'0')}s`;
                                                remaining<?php echo $row->recid; ?>--;
                                            }

                                            updateCountdown<?php echo $row->recid; ?>();
                                            setInterval(updateCountdown<?php echo $row->recid; ?>, 1000);
                                        </script>

                                    <?php elseif ($buttonType == 'closed' and $userdata->is_closed == 0): ?>
                                        <form action="self_treding_model.php" method="post">
                                            <input type="hidden" name="investment_id" value="<?php echo $userdata->recid; ?>">
                                            <button type="submit" name="submit-button" style="border-radius:25px;" class="btn btn-danger btn-lg">
                                                <i class="fas fa-times-circle"></i> Close
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="fresh-invest-btn" onclick="window.location.href='invest_now.php?i=<?php echo $row->recid; ?>'">
                                            <i class="fas fa-chart-line"></i> Invest Now
                                        </button>
                                    <?php endif; ?>





                                </div>
                            </div>
                        </div>
                    <?php endif; ?>



                <?php } ?>
            </div>
        </div>
    </div>


    <script>
        // Fresh Investment Page Interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scroll animations for investment cards
            const cards = document.querySelectorAll('.fresh-investment-card');

            // Intersection Observer for card animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });

            // Add hover effects for investment buttons
            const investButtons = document.querySelectorAll('.fresh-invest-btn');
            investButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add click animation for buttons
            investButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'translateY(0) scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(-2px) scale(1.02)';
                    }, 150);
                });
            });

            // Add stats counter animation
            const statValues = document.querySelectorAll('.fresh-stat-value');
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const finalValue = target.textContent;

                        // Only animate numbers
                        if (/^\d+/.test(finalValue)) {
                            const numericValue = parseInt(finalValue.replace(/[^\d]/g, ''));
                            let currentValue = 0;
                            const increment = numericValue / 50;

                            const timer = setInterval(() => {
                                currentValue += increment;
                                if (currentValue >= numericValue) {
                                    target.textContent = finalValue;
                                    clearInterval(timer);
                                } else {
                                    target.textContent = Math.floor(currentValue).toLocaleString();
                                }
                            }, 30);
                        }

                        statsObserver.unobserve(target);
                    }
                });
            }, {
                threshold: 0.5
            });

            statValues.forEach(stat => {
                statsObserver.observe(stat);
            });
        });
    </script>

    <?php include_once 'footer.php'; ?>