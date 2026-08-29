<?php
$title = 'Investment Plans';
$_is_dashboard = 1;
include_once 'header.php';


$check = my_query("SHOW COLUMNS FROM `investments_plan` LIKE 'action'");

if (mysqli_num_rows($check) == 0) {
    $query = "ALTER TABLE `investments_plan` ADD `action` TINYINT NOT NULL DEFAULT '1' COMMENT '1. active\r\n,0. Deactive' AFTER `status`;";
    my_query($query);
}

// Check and add exchange_pair column
$check_pair = my_query("SHOW COLUMNS FROM `investments` LIKE 'exchange_pair'");
if (mysqli_num_rows($check_pair) == 0) {
    $query_pair = "ALTER TABLE `investments` ADD `exchange_pair` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Trading pair like BTC/USDT' AFTER `invest_hour`;";
    my_query($query_pair);
}

// Check and add exchange_coin column
$check_coin = my_query("SHOW COLUMNS FROM `investments` LIKE 'exchange_coin'");
if (mysqli_num_rows($check_coin) == 0) {
    $query_coin = "ALTER TABLE `investments` ADD `exchange_coin` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Selected cryptocurrency' AFTER `exchange_pair`;";
    my_query($query_coin);
}

// Bot Activation Account (ipid = 1) required before Silver/Gold
$botActivationCheck = my_query("SELECT recid FROM investments WHERE uid = '" . (int) $_SESSION['userid'] . "' AND ipid = 1 LIMIT 1");
$hasBotActivation = mysqli_num_rows($botActivationCheck) > 0;

// Legacy check (ipid = 4 Bot Subscription) — kept for reference
$botSubscriptionCheck = my_query("SELECT * FROM investments WHERE uid = '" . (int) $_SESSION['userid'] . "' AND ipid = 4 AND is_closed = 0 ORDER BY datetime DESC LIMIT 1");
$hasBotSubscription = mysqli_num_rows($botSubscriptionCheck) > 0;

$query = "SELECT * FROM investments_plan WHERE status = 0 AND action = 1 ORDER BY recid ASC";
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

// Fetch all plans for dynamic display
$all_plans = [];
$plans_result = my_query($query);
while ($plan = mysqli_fetch_object($plans_result)) {
    // Control visibility by subscription state
    // if (
    //     (!$hasBotSubscription && $plan->recid == 3) || // hide bot trading until subscribed
    //     ($hasBotSubscription && in_array($plan->recid, [2, 4])) // hide self-trading & subscription once subscribed
    // ) {
    //     continue;
    // }
    $all_plans[] = $plan;
}

// Define color mapping for all plans - this ensures card and form colors match
$available_colors = ['green', 'blue', 'purple', 'orange', 'red', 'pink', 'indigo', 'teal', 'yellow', 'cyan'];
$plan_color_map = [];

// Create color mapping based on plan recid
foreach ($all_plans as $index => $plan) {
    // If plan recid is 1-4, use predefined colors, otherwise cycle through available colors
    if ($plan->recid >= 1 && $plan->recid <= 4) {
        $predefined_colors = [1 => 'green', 2 => 'blue', 3 => 'purple', 4 => 'orange'];
        $plan_color_map[$plan->recid] = $predefined_colors[$plan->recid];
    } else {
        // For plans beyond 4, cycle through available colors
        $plan_color_map[$plan->recid] = $available_colors[($plan->recid - 1) % count($available_colors)];
    }
}

?>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Ensure trade page content stays below sidebar */
    .trade-page.container-fluid {
        position: relative;
        z-index: 1 !important;
    }

    @media (max-width: 768px) {
        .trade-page.container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100vw;
            overflow-x: hidden;
        }

        body, #page-wrapper {
            overflow-x: hidden;
            width: 100%;
        }
    }

    /* Dark theme overrides for trade page */
    .trade-page.bg-gray-100 {
        background-color: #0b0e11 !important;
    }

    .trade-page .package-card {
        background-color: #1e2329 !important;
        border-color: #2c3137 !important;
        color: #eaecef !important;
    }

    .trade-page .package-card .text-gray-800,
    .trade-page .package-card .text-gray-600,
    .trade-page .text-gray-800,
    .trade-page .text-gray-600 {
        color: #eaecef !important;
    }

    .trade-page .package-card .bg-white {
        background-color: #1e2329 !important;
    }

    .trade-page .package-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.6);
    }

    /* Custom styles for investment plans page */
    .investment-plans-page {
        color: #eaecef;
        background-color: #0b0e11;
    }

    .investment-plans-page h1,
    .investment-plans-page h2,
    .investment-plans-page h3 {
        color: #ffffff;
    }

    .investment-plans-page a {
        color: #4f83cc;
    }

    .investment-plans-page a:hover {
        color: #3b5998;
    }

    /* Specific styles for the dynamic tab section */
    .dynamic-tab-section {
        color: #eaecef;
        background-color: #1e2329;
        border-radius: 12px;
        padding: 24px;
        margin-top: 32px;
    }

    .dynamic-tab-section h3 {
        color: #ffffff;
        font-size: 1.875rem; /* 30px */
        margin-bottom: 16px;
    }

    .dynamic-tab-section label {
        color: #b0bec5;
        font-weight: 500;
    }

    .dynamic-tab-section input,
    .dynamic-tab-section select {
        color: #ffffff;
        background-color: #2c3137;
        border: 1px solid #37474f;
        border-radius: 8px;
        padding: 12px;
        font-size: 1rem; /* 16px */
    }

    .dynamic-tab-section input::placeholder {
        color: #78909c;
    }

    .dynamic-tab-section button {
        background-color: #4caf50;
        color: #ffffff;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 1rem; /* 16px */
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .dynamic-tab-section button:hover {
        background-color: #45a049;
    }

    /* Responsive styles for mobile devices */
    @media (max-width: 768px) {
        .dynamic-tab-section {
            padding: 16px;
        }

        .dynamic-tab-section h3 {
            font-size: 1.5rem; /* 24px */
        }

        .dynamic-tab-section input,
        .dynamic-tab-section select {
            font-size: 0.875rem; /* 14px */
        }

        .dynamic-tab-section button {
            font-size: 0.875rem; /* 14px */
        }
    }
    .trade-page .package-card.plan-locked {
        opacity: 0.55;
        filter: grayscale(0.35);
        cursor: not-allowed !important;
        pointer-events: none;
    }

    .trade-page .package-card .plan-lock-overlay {
        position: absolute;
        inset: 0;
        z-index: 20;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(11, 14, 17, 0.72);
        color: #fff;
        text-align: center;
        padding: 16px;
        pointer-events: none;
    }

    .trade-page .package-card .plan-lock-overlay i {
        font-size: 2rem;
        margin-bottom: 8px;
        color: #f0b90b;
    }

    .trade-page .package-card .plan-lock-overlay strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 4px;
    }

    .trade-page .package-card .plan-lock-overlay span {
        font-size: 0.8rem;
        color: #b7bdc6;
    }
</style>

<div class="container-fluid bg-gray-900 min-h-screen py-8 flex flex-col items-center trade-page investment-plans-page">

    <!-- Page Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Investment Plans</h1>
        <p class="text-lg text-gray-600">Choose the perfect plan for your trading strategy</p>
        <?php if (!$hasBotActivation): ?>
        <p class="mt-3 text-sm text-yellow-400">
            <i class="fas fa-lock mr-1"></i>
            Silver &amp; Gold packages unlock after you buy <strong>Bot Activation Account</strong>.
        </p>
        <?php else: ?>
        <p class="mt-3 text-sm text-green-400">
            <i class="fas fa-check-circle mr-1"></i>
            Bot Activation completed. Trading packages are unlocked.
        </p>
        <?php endif; ?>
    </div>

    <!-- Cards Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-full w-full px-4 mb-8">

        <?php 
        // exit;
        foreach ($all_plans as $plan) {
            // Get color from unified color map - ensures card and form colors match
            $color = $plan_color_map[$plan->recid] ?? 'indigo';
            
            // Check if user has investment for this plan (for Self-Trading and Bot-Trading)
            $userInvestment = my_query("SELECT * FROM investments WHERE uid = '" . (int) $_SESSION['userid'] . "' AND ipid = '" . (int) $plan->recid . "' ORDER BY datetime DESC LIMIT 1");
            $userdata = mysqli_fetch_object($userInvestment);
            
            $buttonType = 'invest'; // default
            $remainingSeconds = 0;
            $isDisabled = false;
            $lockReason = '';
            // Bot Activation: one-time only — disable after purchase
            // Silver/Gold: locked until Bot Activation is purchased
            $isPlanLocked = false;
            if ((int) $plan->recid === 1 && $hasBotActivation) {
                $isPlanLocked = true;
                $lockReason = 'activated';
            } elseif (!$hasBotActivation && in_array((int) $plan->recid, [2, 3], true)) {
                $isPlanLocked = true;
                $lockReason = 'need_bot';
            }
            if ($isPlanLocked) {
                $isDisabled = true;
            }
            
            // COMMENTED: Timer functionality
            // if ($userdata && in_array($plan->recid, [2])) { // Only for Self-Trading (ipid=2) and Bot-Trading (ipid=3)
            //     $investTime = strtotime($userdata->datetime);
            //     $cycleHours = (int) $userdata->invest_hour;
            //     $elapsed = time() - $investTime;
            //     
            //     if ($userdata->is_closed == 1) {
            //         // Investment is closed - card is enabled (can invest again)
            //         $buttonType = 'invest';
            //         $isDisabled = false;
            //     } elseif ($elapsed < ($cycleHours * 3600)) {
            //         // Timer still running - card is disabled
            //         $buttonType = 'running';
            //         $remainingSeconds = ($cycleHours * 3600) - $elapsed;
            //         $isDisabled = true;
            //     } else {
            //         // COMMENTED: Closed functionality
            //         // Time completed but not closed yet - show close button but keep card disabled for new investment
            //         // $buttonType = 'closed';
            //         // $isDisabled = true;
            //         $buttonType = 'running';
            //         $isDisabled = true;
            //     }
            // }
        ?>
        
        <!-- Dynamic Package Card -->
        <div onclick="<?php echo ($isDisabled && $buttonType != 'closed') ? '' : ($buttonType == 'closed' ? '' : "selectPackage(this, 'tab{$plan->recid}', 'plan-{$plan->recid}')"); ?>"
            class="package-card <?php echo $isPlanLocked ? 'plan-locked' : ''; ?> <?php echo ($buttonType != 'closed' && !$isDisabled) ? 'cursor-pointer' : 'cursor-default'; ?> bg-white shadow-lg hover:shadow-2xl transition-all duration-300 rounded-2xl overflow-hidden border-2 <?php echo $buttonType == 'closed' ? 'border-red-300 bg-red-50' : 'border-gray-200'; ?> <?php echo !$isDisabled ? 'hover:border-'.$color.'-500 transform hover:-translate-y-1' : ''; ?> <?php echo ($isDisabled && $buttonType != 'closed') ? 'opacity-60' : ''; ?> relative"
            <?php echo ($isDisabled && $buttonType != 'closed') ? 'style="pointer-events: none;"' : ''; ?>>
            
            <?php if ($isPlanLocked): ?>
            <div class="plan-lock-overlay">
                <?php if ($lockReason === 'activated'): ?>
                <i class="fas fa-check-circle" style="color:#0ecb81;"></i>
                <strong>Already Activated</strong>
                <span>Bot Activation purchased — one time only</span>
                <?php else: ?>
                <i class="fas fa-lock"></i>
                <strong>Locked</strong>
                <span>Buy Bot Activation Account first</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Random Badge Display -->
            <?php if (!$isPlanLocked && rand(0, 5) == 1): ?>
                <div class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold px-3 py-1 rounded-full z-10 shadow-lg">
                    <i class="fas fa-fire"></i> HOT
                </div>
            <?php elseif (!$isPlanLocked && rand(0, 7) == 1): ?>
                <div class="absolute top-3 right-3 bg-gradient-to-r from-green-500 to-green-600 text-white text-xs font-bold px-3 py-1 rounded-full z-10 shadow-lg">
                    <i class="fas fa-certificate"></i> NEW
                </div>
            <?php elseif (!$isPlanLocked && rand(0, 4) == 1): ?>
                <div class="absolute top-3 right-3 bg-gradient-to-r from-yellow-400 to-yellow-500 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full z-10 shadow-lg">
                    <i class="fas fa-star"></i> POPULAR
                </div>
            <?php endif; ?>
            
            <div class="bg-gradient-to-r from-<?php echo $color; ?>-500 to-<?php echo $color; ?>-600 p-4 text-center">
                <h2 class="text-xl font-bold text-white mb-1"><?php echo $plan->title; ?></h2>
                <p class="text-<?php echo $color; ?>-100 text-sm"><?php echo substr($plan->line1, 0, 30); ?></p>
            </div>
            
            <div class="p-5">
                <?php 
                // COMMENTED: Timer functionality - entire timer section commented out
                // if ($buttonType == 'running'): ?>
                    <!-- COMMENTED: Timer Display -->
                    <!-- <div class="text-center mb-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="text-lg font-bold text-yellow-700 mb-1">
                            <i class="fas fa-clock"></i> Time Left
                        </div>
                        <div class="text-2xl font-bold text-yellow-800" id="card-countdown-<?php echo $plan->recid; ?>"></div>
                        <div class="text-xs text-yellow-600 mt-1">Trading in Progress...</div>
                    </div>
                    
                    <script>
                        let cardRemaining<?php echo $plan->recid; ?> = <?php echo $remainingSeconds; ?>;
                        const cardCountdownEl<?php echo $plan->recid; ?> = document.getElementById("card-countdown-<?php echo $plan->recid; ?>");
                        const cardDiv<?php echo $plan->recid; ?> = cardCountdownEl<?php echo $plan->recid; ?>.closest('.package-card');
                        
                        function updateCardCountdown<?php echo $plan->recid; ?>() {
                            // COMMENTED: Closed functionality
                            // if (cardRemaining<?php echo $plan->recid; ?> <= 0) {
                            //     // Timer completed - show closed button
                            //     cardCountdownEl<?php echo $plan->recid; ?>.closest('.bg-yellow-50').outerHTML = `
                            //         <div class="text-center mb-3">
                            //             <form action="close_investment.php" method="POST" class="inline-block">
                            //                 <input type="hidden" name="investment_id" value="<?php echo $userdata->recid ?? ''; ?>">
                            //                 <button type="submit" name="submit-button" 
                            //                     class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 shadow-lg">
                            //                     <i class="fas fa-times-circle mr-2"></i> Close Investment
                            //                 </button>
                            //             </form>
                            //             <p class="text-xs text-red-600 mt-2">Time completed - Click to close</p>
                            //         </div>
                            //     `;
                            //     return;
                            // }
                            if (cardRemaining<?php echo $plan->recid; ?> <= 0) {
                                return;
                            }
                            let hours = Math.floor(cardRemaining<?php echo $plan->recid; ?> / 3600);
                            let minutes = Math.floor((cardRemaining<?php echo $plan->recid; ?> % 3600) / 60);
                            let seconds = cardRemaining<?php echo $plan->recid; ?> % 60;
                            cardCountdownEl<?php echo $plan->recid; ?>.innerHTML = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                            cardRemaining<?php echo $plan->recid; ?>--;
                        }
                        
                        updateCardCountdown<?php echo $plan->recid; ?>();
                        setInterval(updateCardCountdown<?php echo $plan->recid; ?>, 1000);
                    </script> -->
                <?php 
                // COMMENTED: Closed functionality
                // elseif ($buttonType == 'closed' && $userdata->is_closed == 0): ?>
                    <!-- COMMENTED: Closed Button - Time Expired -->
                    <!-- <div class="text-center mb-3">
                        <form action="close_investment.php" method="POST" class="inline-block w-full">
                            <input type="hidden" name="investment_id" value="<?php echo $userdata->recid; ?>">
                            <button type="submit" name="submit-button" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-times-circle mr-2"></i> Close Investment
                            </button>
                        </form>
                        <p class="text-xs text-red-600 mt-2">Time completed - Click to close</p>
                    </div> -->
                <?php // else: ?>
                <!-- COMMENTED: Always show investment amount (timer functionality disabled) -->
                <div class="text-center mb-3">
                    <div class="text-3xl font-bold text-gray-800">
                        <?php 
                        if ($plan->amount_from == $plan->amount_to) {
                            echo '$' . number_format($plan->amount_from);
                        } else {
                            echo '$' . number_format($plan->amount_from) . ' - $' . number_format($plan->amount_to);
                        }
                        ?>
                    </div>
                    <div class="text-gray-500 text-sm">Investment Amount</div>
                </div>
                <?php // endif; ?>
                
                <div class="space-y-2 mb-3">
                    <?php if ($plan->line1): ?>
                    <div class="flex items-center text-<?php echo $color; ?>-600 text-base">
                        <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><?php echo substr($plan->line1, 0, 25); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($plan->line2): ?>
                    <div class="flex items-center text-<?php echo $color; ?>-600 text-base">
                        <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><?php echo substr($plan->line2, 0, 25); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Common Features for All Plans -->
                    <div class="flex items-center text-<?php echo $color; ?>-600 text-base">
                        <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>24/7 Customer Support</span>
                    </div>
                    
                    <div class="flex items-center text-<?php echo $color; ?>-600 text-base">
                        <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Secure & Regulated Platform</span>
                    </div>
                </div>
            </div>
        </div>

        <?php } ?>

        </div>

    <!-- Tab Content Section - Dynamic Forms -->
    
    <?php foreach ($all_plans as $plan) { 
        // Get color from unified color map - ensures card and form colors match
        $form_color = $plan_color_map[$plan->recid] ?? 'indigo';
        // Hide forms that should not be investable
        // - Bot Activation form after already purchased
        // - Silver/Gold forms until Bot Activation is purchased
        if ((int) $plan->recid === 1 && $hasBotActivation) {
            continue;
        }
        if (!$hasBotActivation && in_array((int) $plan->recid, [2, 3], true)) {
            continue;
        }
    ?>
    
    <!-- Dynamic Tab Form for Plan ID: <?php echo $plan->recid; ?> -->
    <div class="max-w-4xl w-full mt-10 bg-white shadow-xl rounded-2xl p-8 hidden dynamic-tab-section" id="tab<?php echo $plan->recid; ?>">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-3xl font-bold text-<?php echo $form_color; ?>-600 mb-2"><?php echo $plan->title; ?></h3>
                    <!-- <p class="text-gray-600"><?php echo $plan->line1 . ' - ' . $plan->line2; ?></p> -->
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 mb-1">Available Balance</p>
                    <p class="text-2xl font-bold text-<?php echo $form_color; ?>-600">
                        $<?php echo number_format($_SESSION['userdata']->wallet_topup, 2); ?>
                    </p>
                </div>
            </div>
        </div>

        <form action="invest_now_model.php" method="POST" class="space-y-6">
            <input type="hidden" name="recid" value="<?php echo $plan->recid; ?>">
            <input type="hidden" name="type" value="0">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Invest Amount -->
                <div>
                    <label class="block text-lg font-semibold text-gray-700 mb-2">Investment Amount</label>
                    <div class="relative">
                        <span class="absolute left-1 top-4 text-white-500 font-bold">$</span>
                        <?php 
                        // Check if min amount equals max amount (fixed amount plan)
                        $is_fixed_amount = ($plan->amount_from == $plan->amount_to);
                        $fixed_amount_value = $is_fixed_amount ? number_format($plan->amount_from, 2, '.', '') : '';
                        ?>
                        <input type="number" name="amount" id="amount_input_<?php echo $plan->recid; ?>"
                            class="w-full pl-8 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:border-<?php echo $form_color; ?>-500 focus:outline-none <?php echo ($is_fixed_amount || $plan->recid == 4) ? 'bg-gray-100 cursor-not-allowed' : ''; ?>" 
                            placeholder="Enter amount" required 
                            min="<?php echo $plan->amount_from; ?>" 
                            max="<?php echo $plan->amount_to; ?>"
                            <?php if ($is_fixed_amount || $plan->recid == 4): ?>
                                value="<?php echo $fixed_amount_value ? $fixed_amount_value : number_format($plan->amount_from, 2, '.', ''); ?>" 
                                readonly
                            <?php endif; ?>>
                    </div>
                    <p class="text-sm text-gray-800 mt-3">
                        <?php if ($is_fixed_amount || $plan->recid == 4): ?>
                            Fixed amount: $<?php echo number_format($plan->amount_from, 2); ?>
                        <?php else: ?>
                            Min: $<?php echo number_format($plan->amount_from); ?> - Max: $<?php echo number_format($plan->amount_to); ?>
                        <?php endif; ?>
                    </p>
                    
                    <!-- Quick Amount Selection Buttons -->
                    <?php if (in_array($plan->recid, [2, 3])): // Trading packages only ?>
                    <div class="mt-3">
                        <p class="text-xs text-gray-600 mb-2"><i class="fas fa-bolt"></i> Quick Select:</p>
                        <div class="grid grid-cols-4 gap-2">
                            <button type="button" 
                                onclick="setPercentageAmount<?php echo $plan->recid; ?>(25)"
                                class="bg-gradient-to-r from-blue-400 to-blue-500 hover:from-blue-500 hover:to-blue-600 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 text-sm">
                                25%
                            </button>
                            <button type="button" 
                                onclick="setPercentageAmount<?php echo $plan->recid; ?>(50)"
                                class="bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-600 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 text-sm">
                                50%
                            </button>
                            <button type="button" 
                                onclick="setPercentageAmount<?php echo $plan->recid; ?>(75)"
                                class="bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 text-sm">
                                75%
                            </button>
                            <button type="button" 
                                onclick="setPercentageAmount<?php echo $plan->recid; ?>(100)"
                                class="bg-gradient-to-r from-purple-400 to-purple-500 hover:from-purple-500 hover:to-purple-600 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 text-sm">
                                100%
                            </button>
                        </div>
                    </div>
                    
                    <script>
                    function setPercentageAmount<?php echo $plan->recid; ?>(percentage) {
                        const walletBalance = <?php echo $_SESSION['userdata']->wallet_topup; ?>;
                        const minAmount = <?php echo $plan->amount_from; ?>;
                        const maxAmount = <?php echo $plan->amount_to; ?>;
                        
                        // Calculate percentage amount
                        let calculatedAmount = (walletBalance * percentage) / 100;
                        
                        // Round to 2 decimal places
                        calculatedAmount = Math.round(calculatedAmount * 100) / 100;
                        
                        // Validate against min/max limits
                        if (calculatedAmount < minAmount) {
                            alert('Calculated amount ($' + calculatedAmount.toFixed(2) + ') is less than minimum investment ($' + minAmount + '). Please add more funds to your wallet.');
                            calculatedAmount = minAmount;
                        } else if (calculatedAmount > maxAmount) {
                            calculatedAmount = maxAmount;
                            alert('Amount adjusted to maximum limit: $' + maxAmount);
                        }
                        
                        // Set the input value
                        document.getElementById('amount_input_<?php echo $plan->recid; ?>').value = calculatedAmount.toFixed(2);
                        
                        // Add visual feedback
                        const inputField = document.getElementById('amount_input_<?php echo $plan->recid; ?>');
                        inputField.classList.add('border-<?php echo $form_color; ?>-500', 'bg-<?php echo $form_color; ?>-50');
                        setTimeout(() => {
                            inputField.classList.remove('bg-<?php echo $form_color; ?>-50');
                        }, 500);
                    }
                    </script>
                    <?php endif; ?>
    </div>

                <?php if ($plan->recid == 2): // Self-Trading ?>
                <!-- Duration Time for Self-Trading -->
                <!-- <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Duration Time</label>
                    <select name="time" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-<?php echo $form_color; ?>-500 focus:outline-none" required>
                        <option value="">Select Duration</option>
                        <option value="1">1 Hour</option>
                        <option value="3">3 Hours</option>
                        <option value="5">5 Hours</option>
                    </select>
                </div> -->

                <?php elseif ($plan->recid == 3): // Bot Trading ?>
                <!-- Fixed Duration for Bot Trading -->
                <!-- <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Duration</label>
                    <div class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50">
                        <span class="text-gray-700 font-semibold">24 Hours (Fixed)</span>
                    </div>
                    <input type="hidden" name="time" value="24">
                </div> -->

                <?php endif; ?>

                <?php if (in_array($plan->recid, [2, 3])): // Trading packages only (not Bot Activation) ?>
                <!-- Exchange Pair (Exchange Platforms) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Exchange Pair (Select Exchanges)</label>
                    <select name="exchange_pair" id="exchange_pair_<?php echo $plan->recid; ?>"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-<?php echo $form_color; ?>-500 focus:outline-none" 
                        onchange="fetchPrices<?php echo $plan->recid; ?>()" required>
                        <option value="">Select Exchange Pair</option>
                        <option value="binance-bybit">Binance vs Bybit</option>
                        <option value="binance-kucoin">Binance vs KuCoin</option>
                        <option value="binance-okx">Binance vs OKX</option>
                        <option value="bybit-kucoin">Bybit vs KuCoin</option>
                        <option value="bybit-okx">Bybit vs OKX</option>
                    </select>
                </div>

                <!-- Exchange Coin -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Cryptocurrency</label>
                    <select name="exchange_coin" id="exchange_coin_<?php echo $plan->recid; ?>"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-<?php echo $form_color; ?>-500 focus:outline-none" 
                        onchange="fetchPrices<?php echo $plan->recid; ?>()" required>
                        <option value="">Select Coin</option>
                        <option value="bitcoin">Bitcoin (BTC)</option>
                        <option value="ethereum">Ethereum (ETH)</option>
                        <option value="binancecoin">Binance Coin (BNB)</option>
                        <option value="ripple">Ripple (XRP)</option>
                        <option value="cardano">Cardano (ADA)</option>
                        <option value="solana">Solana (SOL)</option>
                    </select>
                </div>

                <!-- Price Comparison Display -->
                <div class="col-span-2" id="price_display_<?php echo $plan->recid; ?>" style="display: none;">
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-2 border-<?php echo $form_color; ?>-300 rounded-lg p-6">
                        <h4 class="text-lg font-bold text-<?php echo $form_color; ?>-800 mb-4 text-center">
                            <i class="fas fa-chart-line mr-2"></i>Live Price Comparison
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Exchange 1 Price -->
                            <div class="bg-white rounded-lg p-4 shadow-md">
                                <div class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-exchange-alt mr-1"></i><span id="exchange1_name_<?php echo $plan->recid; ?>">Exchange 1</span>
                                </div>
                                <div class="text-2xl font-bold text-green-600" id="exchange1_price_<?php echo $plan->recid; ?>">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </div>
                            </div>

                            <!-- Exchange 2 Price -->
                            <div class="bg-white rounded-lg p-4 shadow-md">
                                <div class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-exchange-alt mr-1"></i><span id="exchange2_name_<?php echo $plan->recid; ?>">Exchange 2</span>
                                </div>
                                <div class="text-2xl font-bold text-blue-600" id="exchange2_price_<?php echo $plan->recid; ?>">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </div>
                            </div>
                        </div>

                        <!-- Price Difference -->
                        <div class="bg-gradient-to-r from-yellow-100 to-orange-100 rounded-lg p-4 text-center">
                            <div class="text-sm text-gray-700 mb-1">Price Difference</div>
                            <div class="text-xl font-bold text-orange-700" id="price_difference_<?php echo $plan->recid; ?>">
                                Calculating...
                            </div>
                            <div class="text-xs text-gray-600 mt-2" id="arbitrage_opportunity_<?php echo $plan->recid; ?>"></div>
                        </div>

                        <div class="text-xs text-gray-500 text-center mt-3">
                            <i class="fas fa-sync-alt mr-1"></i> Prices update every 30 seconds
                        </div>
                    </div>
                </div>

                <script>
                // API configuration for Plan <?php echo $plan->recid; ?>

                let priceInterval<?php echo $plan->recid; ?> = null;
                
                function fetchPrices<?php echo $plan->recid; ?>() {
                    const exchangePair = document.getElementById('exchange_pair_<?php echo $plan->recid; ?>').value;
                    const coin = document.getElementById('exchange_coin_<?php echo $plan->recid; ?>').value;
                    const priceDisplay = document.getElementById('price_display_<?php echo $plan->recid; ?>');
                    
                    // Clear previous interval
                    if (priceInterval<?php echo $plan->recid; ?>) {
                        clearInterval(priceInterval<?php echo $plan->recid; ?>);
                    }
                    
                    if (!exchangePair || !coin) {
                        priceDisplay.style.display = 'none';
                        return;
                    }
                    
                    priceDisplay.style.display = 'block';
                    
                    // Parse exchange pair
                    const exchanges = exchangePair.split('-');
                    const exchange1 = exchanges[0].charAt(0).toUpperCase() + exchanges[0].slice(1);
                    const exchange2 = exchanges[1].charAt(0).toUpperCase() + exchanges[1].slice(1);
                    
                    document.getElementById('exchange1_name_<?php echo $plan->recid; ?>').textContent = exchange1;
                    document.getElementById('exchange2_name_<?php echo $plan->recid; ?>').textContent = exchange2;
                    
                    // Fetch prices immediately
                    updatePrices<?php echo $plan->recid; ?>(coin, exchange1, exchange2);
                    
                    // Update prices every 30 seconds
                    priceInterval<?php echo $plan->recid; ?> = setInterval(() => {
                        updatePrices<?php echo $plan->recid; ?>(coin, exchange1, exchange2);
                    }, 30000);
                }
                
                async function updatePrices<?php echo $plan->recid; ?>(coin, exchange1, exchange2) {
                    try {
                        // Fetch price from CoinGecko API
                        const response = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${coin}&vs_currencies=usd&include_24hr_change=true`);
                        const data = await response.json();
                        
                        if (data[coin] && data[coin].usd) {
                            const basePrice = data[coin].usd;
                            
                            // Simulate slight price difference between exchanges (0.1% - 0.5%)
                            const variation1 = (Math.random() * 0.4 + 0.1) / 100; // 0.1% to 0.5%
                            const variation2 = (Math.random() * 0.4 + 0.1) / 100;
                            
                            const price1 = basePrice * (1 + variation1);
                            const price2 = basePrice * (1 - variation2);
                            
                            // Display prices
                            document.getElementById('exchange1_price_<?php echo $plan->recid; ?>').innerHTML = 
                                `$${price1.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                            document.getElementById('exchange2_price_<?php echo $plan->recid; ?>').innerHTML = 
                                `$${price2.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                            
                            // Calculate difference
                            const difference = Math.abs(price1 - price2);
                            const percentageDiff = ((difference / Math.min(price1, price2)) * 100).toFixed(2);
                            
                            const higherExchange = price1 > price2 ? exchange1 : exchange2;
                            const lowerExchange = price1 > price2 ? exchange2 : exchange1;
                            
                            document.getElementById('price_difference_<?php echo $plan->recid; ?>').innerHTML = 
                                `$${difference.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} <span class="text-sm">(${percentageDiff}%)</span>`;
                            
                            document.getElementById('arbitrage_opportunity_<?php echo $plan->recid; ?>').innerHTML = 
                                `<i class="fas fa-info-circle mr-1"></i> Buy on ${lowerExchange}, Sell on ${higherExchange}`;
                            
                        }
                    } catch (error) {
                        console.error('Error fetching prices:', error);
                        document.getElementById('exchange1_price_<?php echo $plan->recid; ?>').innerHTML = 
                            '<span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> Error</span>';
                        document.getElementById('exchange2_price_<?php echo $plan->recid; ?>').innerHTML = 
                            '<span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> Error</span>';
                    }
                }
                </script>
                <?php endif; ?>
            </div>

            <?php if (in_array($plan->recid, [1, 4])): // Fixed Return and Bot Subscription Benefits ?>
            <!-- Plan Benefits -->
            <div class="bg-gray-800 border-2 border-gray-700 rounded-lg p-6">
                <h4 class="text-lg font-bold text-white mb-4">Plan Benefits:</h4>
                <ul class="space-y-3">
                    <li class="flex items-center text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>24/7 Customer Support</span>
                    </li>
                    <li class="flex items-center text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Secure & Regulated Platform</span>
                    </li>
                    <?php if ($plan->recid == 4): ?>
                    <li class="flex items-center text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Lifetime Bot Usage & Networking Rewards</span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-<?php echo $form_color; ?>-500 to-<?php echo $form_color; ?>-600 text-white font-bold py-4 px-6 rounded-lg hover:from-<?php echo $form_color; ?>-600 hover:to-<?php echo $form_color; ?>-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-check-circle mr-2"></i> Invest Now
                </button>
            </div>
        </form>
    </div>
    
    <?php } ?>
</div>

<!-- JS for Tab Toggle -->
<script>
    function selectPackage(element, tabId, packageName) {
        // Remove active state from all cards
        document.querySelectorAll('.package-card').forEach(card => {
            card.classList.remove('border-indigo-600', 'ring-4', 'ring-indigo-200', 'scale-105');
            card.classList.add('border-gray-200');
        });

        // Add active state to clicked card with enhanced effects
        element.classList.remove('border-gray-200');
        element.classList.add('border-indigo-600', 'ring-4', 'ring-indigo-200', 'scale-105');

        // Hide all tabs
        document.querySelectorAll('[id^="tab"]').forEach(tab => tab.classList.add('hidden'));

        // Show selected tab
        document.getElementById(tabId).classList.remove('hidden');

        // Scroll smoothly to content
        document.getElementById(tabId).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

        // Update hidden input if exists
        const selectedPackageInput = document.getElementById('selectedPackage');
        if (selectedPackageInput) {
            selectedPackageInput.value = packageName;
        }
    }
</script>

<?php
include_once 'footer.php';
?>