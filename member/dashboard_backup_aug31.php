<?php
$title = 'Dashboard';
include_once 'header.php';
$uid = (int) $uid;  // ensure numeric for all income queries (Reward/Royalty/Level cards)

$sponsor = get_user_details($user->refer_id);
// $child_levels = get_child_levels($uid);
$reward_arr = get_reward();
$royalty_rank_names = get_royalty_rank_names();
$_address = strtolower(SITE_CURRENCY_) . '_address';

function get_child_bv_total3($uid, $p = 'L')
{
    $amt = @my_fetch_object(my_query("SELECT (teamb + topup) as amount FROM user WHERE placement_id = '" . $uid . "' AND position = '" . $p . "'"))->amount;
    $amt = ($amt > 0) ? $amt : 0;
    return $amt;
}

$total_in = get_sum('income_royalty', 'amount', "uid='" . $uid . "'") + get_sum('income_growth', 'amount', "uid='" . $uid . "'") + get_sum('income_level', 'amount', "uid='" . $uid . "'") + get_sum('income_direct', 'amount', "uid='" . $uid . "'");
$max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 0,1"))->amount;
$total_in = get_sum('income_royalty', 'amount', "uid='" . $uid . "'") + get_sum('income_growth', 'amount', "uid='" . $uid . "'") + get_sum('income_level', 'amount', "uid='" . $uid . "'") + get_sum('income_direct', 'amount', "uid='" . $uid . "'");
$max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 0,1"))->amount;
$max = ($max) ? $max : 0;
// $max2 = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '".$uid."' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 1,1"))->amount;
// $max2 = ($max2) ? $max2 : 0;
$max2 = 0;
$max3 = $user->teamb - $max - $max2;
$max3 = ($max3 > 0) ? $max3 : 0;

// Remaining earnable amount under capping (e.g. $100 invest @ 2x => $200 remaining when no earnings yet)
$trading_investment = get_trading_investment($uid);
$capping_multiplier = get_capping_multiplier($uid);
if ($capping_multiplier <= 0 && $trading_investment > 0) {
    $capping_multiplier = 2;
}
$max_earnable = $trading_investment * $capping_multiplier;
$total_earnings_cap = get_total_earnings($uid);
$remaining_amount = max(0, round($max_earnable - $total_earnings_cap, 2));

// Get active orders count
$active_orders_query = "SELECT COUNT(*) as active_count FROM investments WHERE status = 0 AND uid = '$uid'";
$active_result = my_query($active_orders_query);
$active_orders = mysqli_fetch_object($active_result)->active_count;

// Check if user has trade_active status and active orders
$trade_active = $user->trade_status;

// Check if user has active Bot Subscription (ipid = 4)
$botSubscriptionCheck = my_query("SELECT * FROM investments WHERE uid = '$uid' AND ipid = 4 AND is_closed = 0 ORDER BY datetime DESC LIMIT 1");
$hasBotSubscription = mysqli_num_rows($botSubscriptionCheck) > 0;
$botSubscriptionData = mysqli_fetch_object($botSubscriptionCheck);

$child_levels = get_child_levels_refer_($uid, $with = 'yes');

// Calculate total team members
$total_members = 0;
foreach ($child_levels as $level) {
    $total_members += count($level);
}

// Handle trade activation (Self Trade - ipid 2)
if (isset($_POST['update_trade_status'])) {
    $status = (int) $_POST['status'];
    if ($user->package > 0) {
        my_query("UPDATE user SET trade_status = $status, trade_status_updated_at = NOW() WHERE uid = " . (int) $uid);
        my_query("UPDATE investments SET trade_status = $status WHERE uid = '" . $uid . "'");
        setMessage($status ? 'Trade activated for today. ROI will be credited when cron runs.' : 'Trade deactivated.', 'success');
        redirect($status ? './live.php' : './dashboard.php');
    }
}

// Check if user has active Self Trade (ipid 2) - needs daily activation for ROI
$selfTradeCheck = my_query("SELECT recid FROM investments WHERE uid = '$uid' AND ipid = 2 AND status = 0 AND is_closed = 0 LIMIT 1");
$hasSelfTrade = my_num_rows($selfTradeCheck) > 0;

?>

<style>
    /* Complete Fresh Modern Dashboard Design */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        /* Dark Theme Color Palette */
        --bg-primary: #0b0e11;
        --bg-secondary: #1e2329;
        --bg-accent: #2b3139;
        --text-primary: #eaecef;
        --text-secondary: #848e9c;
        --text-muted: #6c757d;
        --brand-primary: #f0b90b;
        --brand-secondary: #c9970a;
        --success: #02c076;
        --warning: #f0b90b;
        --danger: #f6465d;
        --info: #3b82f6;
        --border: #2c3137;
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
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
        display: block;
    }

    /* Ensure desktop container is visible on desktop */
    @media (min-width: 769px) {
        .fresh-container {
            display: block !important;
        }
       
    }

    /* Fresh Card */
    .fresh-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 24px;
        margin: 0 0 24px 0 !important;
        
        transition: all 0.3s ease;
    }

    .fresh-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    /* Fresh Welcome Section */
    .fresh-welcome {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        border-radius: var(--radius-lg);
        padding: 32px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
    }

    .fresh-welcome::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .fresh-welcome-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 32px;
    }

    .fresh-welcome h1 {
        font-size: 2.0rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .fresh-welcome p {
        font-size: 2.0rem;
        opacity: 0.9;
        margin-bottom: 16px;
    }

    .fresh-user-id {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 2.0rem;
        font-weight: 600;
    }

    .fresh-stats {
        display: flex;
        gap: 20px;
    }

    .fresh-stat {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: var(--radius);
        padding: 20px;
        text-align: center;
        min-width: 120px;
        transition: all 0.3s ease;
    }

    .fresh-stat:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-4px);
    }

    .fresh-stat-value {
        font-size: 2.0rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .fresh-stat-label {
        font-size: 2.0rem;
        opacity: 0.8;
    }

    /* Fresh Trading Status */
    .fresh-trading {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 32px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .fresh-trading.active {
        border-color: var(--success);
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(5, 150, 105, 0.05) 100%);
    }

    .fresh-trading.inactive {
        border-color: var(--warning);
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(217, 119, 6, 0.05) 100%);
    }

    .fresh-trading-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .fresh-trading-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .fresh-trading-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.0rem;
        color: white;
        position: relative;
    }

    .fresh-trading-icon.active {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        animation: pulse-green 2s infinite;
    }

    .fresh-trading-icon.inactive {
        background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
        animation: pulse-orange 2s infinite;
    }

    @keyframes pulse-green {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    @keyframes pulse-orange {
        0% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
        }
    }

    .fresh-trading-details h3 {
        font-size: 2.0rem;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .fresh-trading-details p {
        color: var(--text-secondary);
        font-size: 2.0rem;
        margin-bottom: 8px;
    }

    .fresh-status {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fresh-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .fresh-status-dot.active {
        background: var(--success);
        animation: blink-green 2s infinite;
    }

    .fresh-status-dot.inactive {
        background: var(--warning);
        animation: blink-orange 2s infinite;
    }

    @keyframes blink-green {

        0%,
        50% {
            opacity: 1;
        }

        51%,
        100% {
            opacity: 0.3;
        }
    }

    @keyframes blink-orange {

        0%,
        50% {
            opacity: 1;
        }

        51%,
        100% {
            opacity: 0.3;
        }
    }

    .fresh-status-text {
        font-size: 2.0rem;
        font-weight: 500;
    }

    .fresh-status-text.active {
        color: var(--success);
    }

    .fresh-status-text.inactive {
        color: var(--warning);
    }

    .fresh-trading-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }

    .fresh-trading-stat {
        text-align: center;
    }

    .fresh-trading-stat-value {
        font-size: 2.0rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .fresh-trading-stat-label {
        font-size: 2.0rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .fresh-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 2.0rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .fresh-btn-primary {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
    }

    .fresh-btn-success {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
    }

    .fresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .fresh-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .fresh-btn:hover::before {
        left: 100%;
    }

    /* Fresh Quick Actions */
    .fresh-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .fresh-action-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        padding: 24px;
        text-align: center;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        text-decoration: none;
        color: var(--text-primary);
        position: relative;
        overflow: hidden;
    }

    .fresh-action-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-primary);
        color: var(--text-primary);
        text-decoration: none;
    }

    .fresh-action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .fresh-action-card:hover::before {
        transform: scaleX(1);
    }

    .fresh-action-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.0rem;
        margin: 0 auto 16px;
        transition: all 0.3s ease;
    }

    .fresh-action-card:hover .fresh-action-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .fresh-action-title {
        font-size: 2.0rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .fresh-action-desc {
        font-size: 2.0rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    /* Fresh Grid Layout */
    .fresh-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 32px;
        margin-bottom: 32px;
    }

    /* Fresh Referral Section */
    .fresh-referral {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .fresh-section-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .fresh-section-title {
        font-size: 2.0rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.1);
    }

    .fresh-section-icon {
        font-size: 2.0rem;
    }

    .fresh-referral-content {
        padding: 24px;
    }

    .fresh-referral h3 {
        font-size: 2.0rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .fresh-referral p {
        color: var(--text-secondary);
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .fresh-referral-stats {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
    }

    .fresh-stat-card {
        background: var(--bg-accent);
        border-radius: var(--radius);
        padding: 16px;
        text-align: center;
        flex: 1;
        transition: all 0.3s ease;
    }

    .fresh-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .fresh-stat-value {
        font-size: 2.0rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 4px;
    }

    .fresh-stat-label {
        font-size: 2.0rem;
        color: #ffff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .fresh-link-section h4 {
        font-size: 2.0rem;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .fresh-link-container {
        display: flex;
        margin-bottom: 16px;
        border-radius: var(--radius);
        overflow: hidden;
        border: 1px solid var(--border);
    }

    .fresh-link-input {
        flex: 1;
        padding: 12px 16px;
        border: none;
        background: var(--bg-primary);
        font-size: 2.0rem;
        color: var(--text-primary);
        font-family: 'Monaco', 'Menlo', monospace;
    }

    .fresh-copy-btn {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .fresh-copy-btn:hover {
        background: linear-gradient(135deg, var(--info) 0%, var(--brand-primary) 100%);
    }

    .fresh-link-actions {
        display: flex;
        gap: 8px;
    }

    .fresh-link-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        background: var(--bg-primary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .fresh-link-btn:hover {
        background: var(--brand-primary);
        color: white;
        border-color: var(--brand-primary);
        transform: translateY(-2px);
        text-decoration: none;
    }

    /* Dashboard Grid Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    /* Referral Section */
    .referral-section {
        background: var(--card-bg);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .referral-section:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .section-header {
        background: var(--gradient-primary);
        color: white;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .section-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: float 8s ease-in-out infinite;
    }

    .section-title {
        font-size: 2.0rem;
        font-weight: 600;
        margin: 0;
        position: relative;
        z-index: 2;
    }

    .section-icon {
        font-size: 2.0rem;
        position: relative;
        z-index: 2;
    }

    .referral-content {
        padding: 2rem;
        display: flex;
        gap: 2rem;
        align-items: flex-start;
    }

    .referral-info {
        flex: 1;
    }

    .referral-info h3 {
        font-size: 2.0rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .referral-info p {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .referral-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .ref-stat-card {
        background: var(--secondary-bg);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        flex: 1;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .ref-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--accent-primary);
    }

    .ref-stat-value {
        font-size: 2.0rem;
        font-weight: 700;
        color: var(--accent-primary);
        margin-bottom: 0.25rem;
    }

    .ref-stat-label {
        font-size: 2.0rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .referral-link-section {
        flex: 1;
        background: var(--secondary-bg);
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
    }

    .referral-link-section h4 {
        font-size: 2.0rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .referral-link-container {
        display: flex;
        margin-bottom: 1rem;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .referral-link {
        flex: 1;
        padding: 0.75rem 1rem;
        background: var(--card-bg);
        font-size: 0.9rem;
        color: var(--text-primary);
        border: none;
        outline: none;
        font-family: 'Monaco', 'Menlo', monospace;
    }

    .copy-button {
        background: var(--gradient-primary);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .copy-button:hover {
        background: var(--gradient-info);
    }

    .copy-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .copy-button:hover::before {
        left: 100%;
    }

    .referral-actions {
        display: flex;
        gap: 0.5rem;
    }

    .ref-action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .ref-action-btn:hover {
        background: var(--accent-primary);
        color: white;
        border-color: var(--accent-primary);
        transform: translateY(-2px);
        text-decoration: none;
    }

    /* Fresh Portfolio Section */
    .fresh-portfolio {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .fresh-portfolio-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0;
    }

    .fresh-portfolio-card {
        padding: 24px;
        border-right: 1px solid var(--border);
        transition: all 0.3s ease;
        position: relative;
    }

    .fresh-portfolio-card:last-child {
        border-right: none;
    }

    .fresh-portfolio-card:hover {
        background: var(--bg-accent);
        transform: translateY(-2px);
    }

    .fresh-portfolio-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        font-size: 2.0rem;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .fresh-portfolio-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .fresh-portfolio-change {
        font-size: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .fresh-portfolio-change.positive {
        color: var(--success);
    }

    .fresh-portfolio-change.negative {
        color: var(--danger);
    }

    /* Fresh Income Section */
    .fresh-income-section {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        margin-bottom: 32px;
        overflow: hidden;
    }

    .fresh-income-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        padding: 24px;
    }

    .fresh-income-card {
        background: linear-gradient(135deg, var(--bg-accent) 0%, var(--bg-secondary) 100%);
        border-radius: var(--radius-lg);
        padding: 24px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }

    .fresh-income-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-primary);
    }

    .fresh-income-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .fresh-income-card:hover::before {
        transform: scaleX(1);
    }

    .fresh-income-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.0rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .fresh-income-card:hover .fresh-income-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .fresh-income-content {
        flex: 1;
    }

    .fresh-income-label {
        font-size: 2.0rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .fresh-income-value {
        font-size: 2.0rem;
        font-weight: 800;
        color: var(--brand-primary);
        margin-bottom: 4px;
        text-shadow: 0 2px 4px rgba(79, 70, 229, 0.1);
    }

    .fresh-income-desc {
        font-size: 2.0rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Fresh Team Section */
    .fresh-team-section {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        margin-bottom: 32px;
        overflow: hidden;
    }

    .fresh-team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        padding: 24px;
    }

    .fresh-team-card {
        background: linear-gradient(135deg, var(--bg-accent) 0%, var(--bg-secondary) 100%);
        border-radius: var(--radius-lg);
        padding: 24px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }

    .fresh-team-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--success);
    }

    .fresh-team-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .fresh-team-card:hover::before {
        transform: scaleX(1);
    }

    .fresh-team-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.0rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .fresh-team-card:hover .fresh-team-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .fresh-team-content {
        flex: 1;
    }

    .fresh-team-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .fresh-team-value {
        font-size: 2.0rem;
        font-weight: 800;
        color: var(--success);
        margin-bottom: 4px;
        text-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
    }

    .fresh-team-desc {
        font-size: 2.0rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Fresh Mobile Design */
    .fresh-mobile {
        display: none;
        background: var(--bg-primary);
        min-height: 100vh;
        padding-bottom: 80px;
    }

    /* Ensure mobile container is hidden on desktop */
    @media (min-width: 769px) {
        .fresh-mobile {
            display: none !important;
        }
    }

    .fresh-mobile-section {
        padding: 16px 20px 8px;
        font-size: 2.0rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .fresh-mobile-cards {
        padding: 0 20px;
    }

    .fresh-mobile-card {
        background: var(--bg-secondary);
        border-radius: var(--radius);
        padding: 16px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
    }

    .fresh-mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-primary);
    }

    .fresh-mobile-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .fresh-mobile-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.0rem;
    }

    .fresh-mobile-details .fresh-mobile-title {
        font-size: 2.0rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }



    .fresh-mobile-details .fresh-mobile-desc {
        font-size: 2.0rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .fresh-mobile-balance {
        text-align: right;
    }

    .fresh-mobile-amount {
        font-size: 2.0rem;
        font-weight: 800;
        color: var(--brand-primary);
        margin-bottom: 2px;
        text-shadow: 0 1px 2px rgba(79, 70, 229, 0.1);
    }

    .fresh-mobile-unit {
        font-size: 2.0rem;
        color: var(--text-secondary);
        font-weight: 600;
    }

    /* Fresh Mobile Tools */
    .fresh-mobile-tools {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        padding: 0 20px 20px;
    }

    .fresh-mobile-tool {
        background: var(--bg-secondary);
        border-radius: var(--radius);
        padding: 20px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: var(--text-primary);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .fresh-mobile-tool:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-primary);
        color: var(--text-primary);
        text-decoration: none;
    }

    .fresh-mobile-tool::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .fresh-mobile-tool:hover::before {
        transform: scaleX(1);
    }

    .fresh-mobile-tool-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.0rem;
        margin-bottom: 12px;
        transition: all 0.3s ease;
    }

    .fresh-mobile-tool:hover .fresh-mobile-tool-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .fresh-mobile-tool-name {
        font-size: 2.0rem;
        font-weight: 600;
        text-align: center;
    }

    /* Fresh Bottom Navigation */
    .fresh-bottom-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--bg-secondary);
        border-top: 1px solid var(--border);
        padding: 12px 0;
        z-index: 1000;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    }

    .fresh-bottom-nav-container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-width: 500px;
        margin: 0 auto;
        padding: 0 16px;
    }


    .fresh-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: var(--text-muted);
        transition: all 0.3s ease;
        padding: 8px;
        border-radius: var(--radius);
        min-width: 60px;
    }

    .fresh-nav-item.active {
        color: var(--brand-primary);
        background: rgba(79, 70, 229, 0.1);
    }

    .fresh-nav-item:hover {
        color: var(--brand-primary);
        text-decoration: none;
        transform: translateY(-2px);
    }

    .fresh-nav-icon {
        font-size: 2.0rem;
        margin-bottom: 4px;
        transition: all 0.3s ease;
    }

    .fresh-nav-item.active .fresh-nav-icon {
        transform: scale(1.1);
    }

    .fresh-nav-label {
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Fresh Responsive Design */
    @media (max-width: 1200px) {
        .fresh-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .fresh-welcome-content {
            flex-direction: column;
            text-align: center;
            gap: 24px;
        }

        .fresh-stats {
            justify-content: center;
        }
    }

    @media (max-width: 1200px) {
        .fresh-income-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .fresh-team-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .fresh-referral-content {
            flex-direction: column;
            gap: 20px;
        }

        .fresh-referral-stats {
            flex-direction: column;
            gap: 12px;
        }

        .fresh-actions {
            grid-template-columns: repeat(2, 1fr);
        }

        .fresh-portfolio-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .fresh-income-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .fresh-team-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .fresh-income-card,
        .fresh-team-card {
            padding: 20px;
        }

        .fresh-income-value,
        .fresh-team-value {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 768px) {

        /* Switch to mobile view */
        .fresh-container {
            display: none !important;
        }

        .fresh-mobile {
            display: block !important;
        }

        .fresh-bottom-nav {
            display: block !important;
        }

        body,
        #page-wrapper {
            margin-top: 0;
            padding-top: 0;
        }

        .fresh-welcome {
            margin: 16px;
            padding: 24px;
        }

        .fresh-welcome h1 {
            font-size: 1.75rem;
        }

        .fresh-stat {
            min-width: 100px;
            padding: 16px;
        }

        .fresh-trading {
            margin: 16px;
            padding: 20px;
        }

        .fresh-trading-content {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }

        .fresh-trading-info {
            flex-direction: column;
            gap: 12px;
        }

        .fresh-actions {
            grid-template-columns: 1fr;
            gap: 16px;
            margin: 0 16px 24px;
        }
    }

    @media (max-width: 480px) {
        .fresh-welcome {
            margin: 12px;
            padding: 20px;
        }

        .fresh-welcome h1 {
            font-size: 1.5rem;
        }

        .fresh-stats {
            flex-direction: column;
            gap: 12px;
        }

        .fresh-stat {
            min-width: auto;
            width: 100%;
        }

        .fresh-mobile-tools {
            padding: 0 12px 16px;
            gap: 10px;
        }

        .fresh-mobile-cards {
            padding: 0 12px;
        }

        .fresh-mobile-section {
            padding: 16px 12px 8px;
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

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes scaleInBounce {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Apply Fresh Animations */
    .fresh-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .fresh-welcome {
        animation: scaleInBounce 0.8s ease-out;
    }

    .fresh-action-card {
        animation: slideInLeft 0.6s ease-out;
    }

    .fresh-action-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .fresh-action-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .fresh-action-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .fresh-action-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .fresh-portfolio-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .fresh-portfolio-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .fresh-portfolio-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .fresh-portfolio-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .fresh-portfolio-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .fresh-mobile-card {
        animation: fadeInUp 0.5s ease-out;
    }

    .fresh-mobile-tool {
        animation: scaleInBounce 0.5s ease-out;
    }

    /* Fresh Utility Classes */
    .fresh-text-gradient {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .fresh-shadow-hover {
        transition: box-shadow 0.3s ease;
    }

    .fresh-shadow-hover:hover {
        box-shadow: var(--shadow-lg);
    }

    .fresh-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Animated Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
        border-radius: 12px;
        padding: clamp(20px, 4vw, 30px);
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        overflow: hidden;
        animation: slideInFade 0.8s ease-out;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        gap: 20px;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        /*background: linear-gradient(90deg, #fcd535, transparent);*/
        /*animation: shimmer 2s infinite;*/
    }

    .welcome-text {
        flex: 1;
    }

    .welcome-text h2 {
        font-size: clamp(20px, 4vw, 28px);
        margin-bottom: 10px;
        color: #fff;
        animation: slideUp 0.6s ease-out;
    }

    .welcome-text p {
        font-size: clamp(14px, 2vw, 16px);
        opacity: 0.8;
        animation: slideUp 0.6s ease-out 0.2s backwards;
    }

    .welcome-stats {
        display: flex;
        gap: clamp(15px, 3vw, 30px);
        animation: slideLeft 0.6s ease-out 0.3s backwards;
    }

    .stat {
        background: rgba(255, 255, 255, 0.05);
        padding: 15px;
        border-radius: 8px;
        min-width: 150px;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .stat:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.08);
    }

    .stat-label {
        font-size: 14px;
        color: #848e9c;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: clamp(16px, 3vw, 20px);
        font-weight: 600;
    }

    /* Enhanced Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: clamp(10px, 2vw, 20px);
        margin-bottom: 25px;
        animation: fadeIn 0.8s ease-out 0.4s backwards;
    }

    .quick-actions a {
        text-decoration: none;
        color: inherit;
    }

    .action-card {
        background: linear-gradient(145deg, #1e2126, #262b33);
        border-radius: 12px;
        padding: clamp(15px, 3vw, 25px);
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #2c3137;
        position: relative;
        overflow: hidden;
    }

    .action-card::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .action-card:hover {
        transform: translateY(-5px);
        border-color: #fcd535;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .action-card:hover::after {
        opacity: 1;
    }

    .action-icon {
        font-size: clamp(24px, 4vw, 32px);
        margin-bottom: 15px;
        color: #fcd535;
        animation: bounce 2s infinite;
    }

    .action-card h3 {
        font-size: clamp(16px, 2.5vw, 18px);
        margin: 0;
    }

    /* Main Grid Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: auto auto;
        gap: 20px;
        margin: 20px 0;
        transition: all 0.3s ease;
    }

    /* Sidebar Styles */
    .left-sidebar,
    .right-sidebar {
        transition: all 0.3s ease;
    }

    /* Enhanced Card Styles */
    .dashboard-card {
        background: #1e2126;
        border-radius: 12px;
        border: 1px solid #2c3137;
        margin-bottom: 20px;
        overflow: hidden;
        animation: fadeIn 0.5s ease;
        height: fit-content;
        transition: all 0.3s ease;
    }

    .card-header {
        background: #262b33;
        padding: 20px;
        border-bottom: 1px solid #2c3137;
        font-weight: 500;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header .icon {
        color: #fcd535;
    }

    /* Market Pairs */
    .market-pair {
        display: flex;
        justify-content: space-between;
        padding: 15px 20px;
        border-bottom: 1px solid #2c3137;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .market-pair:hover {
        background: #262b33;
        padding-left: 25px;
    }

    .market-pair-name {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .crypto-icon {
        width: 24px;
        height: 24px;
    }

    /* Price Cards */
    .price-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .price-card {
        background: #262b33;
        padding: 20px;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .price-card:hover {
        transform: translateY(-5px);
    }

    .price-label {
        color: #848e9c;
        font-size: 13px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .price-value {
        font-size: 20px;
        font-weight: 600;
    }

    /* Wallet Section */
    .wallet-card {
        padding: 20px;
        border-bottom: 1px solid #2c3137;
        transition: all 0.3s ease;
    }

    .wallet-card:hover {
        background: #262b33;
    }

    .wallet-icon {
        margin-right: 10px;
        color: #fcd535;
    }

    /* Animations */
    @keyframes slideInFade {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideLeft {
        from {
            transform: translateX(20px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    .price-up {
        color: #0ecb81;
        animation: pulse 2s infinite;
    }

    .price-down {
        color: #f6465d;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }

        100% {
            opacity: 1;
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Mobile App Specific Styles */
    .mobile-app-container {
        display: none;
        background: linear-gradient(180deg, #0b0e11 0%, #131722 100%);
    }

    /* Bottom Navigation */
    .bottom-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #1c2127;
        justify-content: space-around;
        padding: 10px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        z-index: 1000;
    }

    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #848e9c;
    }

    .nav-item.active {
        color: #f0b90b;
    }

    .nav-icon {
        font-size: 20px;
        margin-bottom: 4px;
    }

    .nav-label {
        font-size: 10px;
    }

    /* Status Bar */
    .status-bar {
        display: none;
        background: #0b0e11;
        padding: 10px 15px;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .status-bar-left {
        display: flex;
        align-items: center;
    }

    .status-bar-title {
        font-size: 18px;
        font-weight: 600;
        color: #f0b90b;
    }

    .status-bar-right {
        display: flex;
        gap: 15px;
    }

    .status-icon {
        font-size: 18px;
        color: #848e9c;
    }

    /* Enhanced Responsive Design for Dashboard Grid */
    @media screen and (max-width: 1200px) {
        .dashboard-grid {
            grid-template-columns: auto auto;
            gap: 15px;
        }
    }

    @media screen and (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: auto auto;
            gap: 15px;
        }

        .right-sidebar {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(auto, auto));
            gap: 15px;
        }

        .right-sidebar .dashboard-card {
            margin-bottom: 0;
        }
    }

    @media screen and (max-width: 768px) {

        /* Switch to mobile app view */


        .fresh-stat-label {
    font-size: 1rem;
    color: #ffff;
    text-transform: uppercase;
    }
    .fresh-welcome p {
        font-size: 1.25rem;
    }
    .fresh-btn {
    display: inline-flex !important;
    align-items: center;
    gap: 8px;
    padding: 12px 24px !important;
    border-radius: 50px !important;
    /* font-weight: 600; */
    /* font-size: 2.0rem; */
    text-decoration: none;
    border: none !important;
    cursor: pointer !important;
    transition: all 0.3s 
ease !important;
    position: relative !important;
    overflow: hidden !important;
}
        .dashboard-wrapper {
            display: none;
        }

        .mobile-app-container {
            display: block;
            padding-bottom: 70px;
        }

        .status-bar {
            display: flex;
        }

        .bottom-nav {
            display: flex;
        }

        /* Mobile app specific styles */
        .balance-card {
            background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
            border-radius: 16px;
            padding: 20px;
            margin: 15px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(240, 185, 11, 0.1);
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #f0b90b, transparent);
        }

        .balance-card::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(240, 185, 11, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .balance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .balance-title {
            font-size: 14px;
            color: #848e9c;
        }

        .balance-actions {
            display: flex;
            gap: 10px;
        }

        .balance-action {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f0b90b;
            font-size: 14px;
            cursor: pointer;
        }

        .balance-amount {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .balance-value {
            font-size: 14px;
            color: #848e9c;
        }

        .balance-change {
            display: inline-flex;
            align-items: center;
            background: rgba(14, 203, 129, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #0ecb81;
            margin-left: 10px;
        }

        .balance-change i {
            margin-right: 4px;
        }

        .balance-change.negative {
            background: rgba(246, 70, 93, 0.1);
            color: #f6465d;
        }

        /* Quick Actions */
        .mobile-quick-actions {
            display: flex;
            justify-content: space-around;
            padding: 5px 15px 20px;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .mobile-quick-actions::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 15px;
            right: 15px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(240, 185, 11, 0.2), transparent);
            z-index: -1;
        }

        .mobile-quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #eaecef;
            width: 70px;
            position: relative;
            z-index: 1;
        }

        .action-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(240, 185, 11, 0.2);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .action-icon-wrapper::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: radial-gradient(circle at center, rgba(240, 185, 11, 0.15) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-quick-action:hover .action-icon-wrapper::before {
            opacity: 1;
        }

        .mobile-quick-action:hover .action-icon-wrapper {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }

        .action-icon {
            font-size: 20px;
            color: #f0b90b;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .mobile-quick-action:hover .action-icon {
            transform: scale(1.2);
        }

        .action-label {
            font-size: 12px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .mobile-quick-action:hover .action-label {
            color: #f0b90b;
        }

        /* Section Title */
        .mobile-section-title {
            padding: 0 15px;
            margin: 20px 0 10px;
            font-size: 16px;
            font-weight: 600;
            color: #eaecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .mobile-section-title .view-all {
            font-size: 12px;
            color: #f0b90b;
            text-decoration: none;
        }

        /* Feature Card (Trading Status) - Enhanced Trading Animation */
        .feature-card {
            background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
            border-radius: 12px;
            padding: 15px;
            margin: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        .feature-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        /* Active Trading Status */
        .bot-subscription-card.active-trading {
            border: 1px solid #1aac54;
            position: relative;
            animation: activeCardPulse 3s infinite alternate;
        }

        @keyframes activeCardPulse {
            0% {
                box-shadow: 0 4px 20px rgba(26, 172, 84, 0.1);
                border-color: rgba(26, 172, 84, 0.7);
            }

            100% {
                box-shadow: 0 4px 30px rgba(26, 172, 84, 0.3);
                border-color: rgba(26, 172, 84, 1);
            }
        }

        /* Inactive Trading Status */
        .bot-subscription-card.inactive-trading {
            border: 1px solid #f0b90b;
            position: relative;
            animation: inactiveCardPulse 3s infinite alternate;
        }

        @keyframes inactiveCardPulse {
            0% {
                box-shadow: 0 4px 20px rgba(240, 185, 11, 0.1);
                border-color: rgba(240, 185, 11, 0.7);
            }

            100% {
                box-shadow: 0 4px 30px rgba(240, 185, 11, 0.3);
                border-color: rgba(240, 185, 11, 1);
            }
        }

        .bot-subscription-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 12px;
            border: 1px solid rgba(26, 172, 84, 0.5);
            pointer-events: none;
            animation: borderGlow 3s infinite alternate;
        }

        .active-trading::before {
            border-color: rgba(26, 172, 84, 0.5);
        }

        .inactive-trading::before {
            border-color: rgba(240, 185, 11, 0.5);
        }

        @keyframes borderGlow {
            0% {
                opacity: 0.5;
                transform: scale(1);
            }

            100% {
                opacity: 1;
                transform: scale(1.02);
            }
        }

        .active-trading::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(26, 172, 84, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -75px;
            right: -75px;
            opacity: 0.7;
            animation: rotateGlow 15s linear infinite;
        }

        .inactive-trading::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(240, 185, 11, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -75px;
            right: -75px;
            opacity: 0.7;
            animation: rotateGlow 15s linear infinite;
        }

        @keyframes rotateGlow {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .feature-card-left {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            position: relative;
            overflow: hidden;
        }

        .active-trading .feature-icon {
            background: rgba(26, 172, 84, 0.1);
            color: #1aac54;
            animation: activeIconPulse 2s infinite alternate;
        }

        .inactive-trading .feature-icon {
            background: rgba(240, 185, 11, 0.1);
            color: #f0b90b;
            animation: inactiveIconPulse 2s infinite alternate;
        }

        @keyframes activeIconPulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(26, 172, 84, 0.7);
            }

            100% {
                transform: scale(1.1);
                box-shadow: 0 0 0 10px rgba(26, 172, 84, 0);
            }
        }

        @keyframes inactiveIconPulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(240, 185, 11, 0.7);
            }

            100% {
                transform: scale(1.1);
                box-shadow: 0 0 0 10px rgba(240, 185, 11, 0);
            }
        }

        .pulse-rings {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            z-index: -1;
        }

        .active-trading .pulse-rings::before,
        .active-trading .pulse-rings::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            background: rgba(26, 172, 84, 0.3);
            opacity: 0;
            animation: ringPulse 2s infinite;
        }

        .inactive-trading .pulse-rings::before,
        .inactive-trading .pulse-rings::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            background: rgba(240, 185, 11, 0.3);
            opacity: 0;
            animation: ringPulse 2s infinite;
        }

        .pulse-rings::after {
            animation-delay: 0.5s;
        }

        @keyframes ringPulse {
            0% {
                transform: scale(1);
                opacity: 0.5;
            }

            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .active-trading .feature-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(26, 172, 84, 0.3) 0%, transparent 70%);
            opacity: 0;
            animation: iconGlow 3s infinite;
        }

        .inactive-trading .feature-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(240, 185, 11, 0.3) 0%, transparent 70%);
            opacity: 0;
            animation: iconGlow 3s infinite;
        }

        @keyframes iconGlow {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }

            50% {
                opacity: 1;
                transform: scale(1);
            }

            100% {
                opacity: 0;
                transform: scale(1.5);
            }
        }

        .feature-info {
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.5s ease-out;
        }

        .feature-title {
            font-weight: 600;
            font-size: 16px;
            color: #eaecef;
            position: relative;
            display: inline-block;
        }

        .active-trading .feature-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #1aac54, transparent);
            animation: lineExpand 3s infinite alternate;
        }

        .inactive-trading .feature-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #f0b90b, transparent);
            animation: lineExpand 3s infinite alternate;
        }

        @keyframes lineExpand {
            0% {
                width: 0;
                opacity: 0.5;
            }

            100% {
                width: 100%;
                opacity: 1;
            }
        }

        .feature-subtitle {
            font-size: 12px;
            color: #848e9c;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .market-pair-name {
            font-weight: 500;
            color: #eaecef;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            position: relative;
        }

        .status-dot.active {
            background: #1aac54;
            box-shadow: 0 0 10px rgba(26, 172, 84, 0.5);
            animation: activePulse 2s infinite;
        }

        .status-dot.inactive {
            background: #f0b90b;
            box-shadow: 0 0 10px rgba(240, 185, 11, 0.5);
            animation: inactivePulse 2s infinite;
        }

        @keyframes activePulse {
            0% {
                box-shadow: 0 0 0 0 rgba(26, 172, 84, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(26, 172, 84, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(26, 172, 84, 0);
            }
        }

        @keyframes inactivePulse {
            0% {
                box-shadow: 0 0 0 0 rgba(240, 185, 11, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(240, 185, 11, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(240, 185, 11, 0);
            }
        }

        .status-dot.active::after {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            background: rgba(26, 172, 84, 0.3);
            animation: dotPulse 2s infinite;
            z-index: -1;
        }

        .status-dot.inactive::after {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            background: rgba(240, 185, 11, 0.3);
            animation: dotPulse 2s infinite;
            z-index: -1;
        }

        @keyframes dotPulse {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .status-text {
            font-size: 12px;
            font-weight: 500;
        }

        .active-trading .status-text {
            color: #1aac54;
            animation: textPulse 2s infinite alternate;
        }

        .inactive-trading .status-text {
            color: #f0b90b;
            animation: textPulse 2s infinite alternate;
        }

        @keyframes textPulse {
            0% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        .trading-stats {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #eaecef;
        }

        .active-trading .stat-value {
            color: #1aac54;
            text-shadow: 0 0 10px rgba(26, 172, 84, 0.3);
        }

        .inactive-trading .stat-value {
            color: #f0b90b;
            text-shadow: 0 0 10px rgba(240, 185, 11, 0.3);
        }

        .stat-label {
            font-size: 10px;
            color: #848e9c;
        }

        .subscribed-badge {
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            z-index: 2;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }

        .active-badge {
            background: linear-gradient(135deg, #1aac54 0%, #0d8a3e 100%);
            color: #fff;
            box-shadow: 0 2px 10px rgba(26, 172, 84, 0.3);
            animation: activeBadgePulse 3s infinite alternate;
        }

        .inactive-badge {
            background: linear-gradient(135deg, #f0b90b 0%, #e6a800 100%);
            color: #fff;
            box-shadow: 0 2px 10px rgba(240, 185, 11, 0.3);
            animation: inactiveBadgePulse 3s infinite alternate;
        }

        @keyframes activeBadgePulse {
            0% {
                transform: scale(1);
                box-shadow: 0 2px 10px rgba(26, 172, 84, 0.3);
            }

            100% {
                transform: scale(1.05);
                box-shadow: 0 5px 15px rgba(26, 172, 84, 0.5);
            }
        }

        @keyframes inactiveBadgePulse {
            0% {
                transform: scale(1);
                box-shadow: 0 2px 10px rgba(240, 185, 11, 0.3);
            }

            100% {
                transform: scale(1.05);
                box-shadow: 0 5px 15px rgba(240, 185, 11, 0.5);
            }
        }

        .subscribed-badge:hover {
            transform: translateY(-2px);
        }

        .active-badge::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(30deg);
            animation: shimmerEffect 3s infinite;
        }

        .inactive-badge::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(30deg);
            animation: shimmerEffect 3s infinite;
        }

        @keyframes shimmerEffect {
            0% {
                transform: translateX(-100%) rotate(30deg);
            }

            100% {
                transform: translateX(100%) rotate(30deg);
            }
        }

        /* Section Headers - Enhanced Animation */
        .section-header {
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            color: #f0b90b;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            animation: fadeInLeft 0.6s ease-out;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 15px;
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, #f0b90b, transparent);
            animation: expandLine 1.5s ease-out forwards;
        }

        @keyframes expandLine {
            from {
                width: 0;
                opacity: 0;
            }

            to {
                width: 50px;
                opacity: 1;
            }
        }

        .section-icon {
            color: #f0b90b;
            animation: rotatePulse 3s infinite alternate;
            display: inline-block;
        }

        @keyframes rotatePulse {
            0% {
                transform: scale(1) rotate(0deg);
                text-shadow: 0 0 5px rgba(240, 185, 11, 0.5);
            }

            100% {
                transform: scale(1.2) rotate(10deg);
                text-shadow: 0 0 15px rgba(240, 185, 11, 0.8);
            }
        }

        /* Tools Grid - Enhanced Animation */
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            padding: 0 15px 15px;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tool-card {
            background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
            border-radius: 12px;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(240, 185, 11, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        .tool-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .tool-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .tool-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .tool-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .tool-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(240, 185, 11, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .tool-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.7s ease;
        }

        .tool-card:hover::after {
            left: 100%;
        }

        .tool-card:hover,
        .tool-card:active {
            transform: translateY(-5px) scale(1.03);
            border-color: rgba(240, 185, 11, 0.5);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3), 0 0 15px rgba(240, 185, 11, 0.2);
        }

        .tool-card:hover::before {
            opacity: 1;
        }

        .tool-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(240, 185, 11, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            color: #f0b90b;
            font-size: 20px;
            position: relative;
            z-index: 2;
            transition: all 0.4s ease;
            box-shadow: 0 0 0 rgba(240, 185, 11, 0.4);
        }

        .tool-card:hover .tool-icon {
            transform: scale(1.15);
            background: rgba(240, 185, 11, 0.2);
            box-shadow: 0 0 20px rgba(240, 185, 11, 0.4);
        }

        .tool-icon::after {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border-radius: 50%;
            background: rgba(240, 185, 11, 0.15);
            z-index: -1;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.4s ease;
        }

        .tool-card:hover .tool-icon::after {
            opacity: 1;
            transform: scale(1.2);
            animation: pulseIcon 2s infinite;
        }

        @keyframes pulseIcon {
            0% {
                transform: scale(0.8);
                opacity: 0.8;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.4;
            }

            100% {
                transform: scale(0.8);
                opacity: 0.8;
            }
        }

        .tool-name {
            font-size: 14px;
            font-weight: 500;
            color: #eaecef;
            text-align: center;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .tool-card:hover .tool-name {
            color: #f0b90b;
            transform: scale(1.05);
            text-shadow: 0 0 10px rgba(240, 185, 11, 0.3);
        }

        /* Mobile Wallet Cards */
        .mobile-wallet-cards {
            padding: 0 15px;
        }

        .mobile-wallet-card {
            background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .mobile-wallet-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-color: rgba(240, 185, 11, 0.2);
        }

        .mobile-wallet-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #f0b90b;
            opacity: 0.7;
        }

        .mobile-wallet-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 40px;
            height: 40px;
            background: radial-gradient(circle, rgba(240, 185, 11, 0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .wallet-info {
            display: flex;
            align-items: center;
        }

        .wallet-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(240, 185, 11, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 18px;
            color: #f0b90b;
        }

        .wallet-name {
            display: flex;
            flex-direction: column;
        }

        .wallet-type {
            font-size: 16px;
            font-weight: 600;
        }

        .wallet-description {
            font-size: 12px;
            color: #848e9c;
        }

        .wallet-balance {
            text-align: right;
        }

        .wallet-amount {
            font-size: 16px;
            font-weight: 600;
        }

        .wallet-value {
            font-size: 12px;
            color: #848e9c;
        }
    }

    @media screen and (max-width: 768px) {
        .dashboard-grid {
            gap: 10px;
            margin: 10px 0;
        }
        .fresh-card {
            margin: 0 0 24px 2px !important;
        }
        .dashboard-card {
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .card-header {
            padding: 15px;
        }

        .market-pairs {
            grid-template-columns: 1fr;
        }

        .market-pair {
            padding: 12px 15px;
        }

        .price-overview {
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 10px;
        }

        .price-card {
            padding: 12px;
        }

        .wallet-card {
            padding: 15px;
        }

        /* Font Size Adjustments */
        .card-header span {
            font-size: 16px;
        }

        .price-label {
            font-size: 12px;
        }

        .price-value {
            font-size: 18px;
        }

        .market-pair-name span {
            font-size: 14px;
        }

        /* Market Chart Card - Enhanced Animation */
        .market-chart-card {
            background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
            border-radius: 16px;
            padding: 15px;
            margin: 0 15px 15px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(240, 185, 11, 0.1);
            animation: fadeInUp 0.8s ease-out;
            transform: translateZ(0);
            backface-visibility: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .market-chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3), 0 0 15px rgba(240, 185, 11, 0.1);
            border-color: rgba(240, 185, 11, 0.3);
        }

        .market-chart-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #f0b90b, transparent);
            z-index: 2;
        }

        .market-chart-card::after {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(240, 185, 11, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0.7;
            z-index: 1;
            animation: rotateGradient 15s linear infinite;
        }

        @keyframes rotateGradient {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
            animation: fadeInDown 0.6s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chart-title {
            display: flex;
            flex-direction: column;
        }

        .chart-symbol {
            font-size: 16px;
            font-weight: 600;
            color: #eaecef;
            position: relative;
            display: inline-block;
        }

        .chart-symbol::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #f0b90b, transparent);
            animation: expandWidth 1.5s ease-out forwards;
            animation-delay: 0.5s;
        }

        @keyframes expandWidth {
            from {
                width: 0;
                opacity: 0;
            }

            to {
                width: 100%;
                opacity: 1;
            }
        }

        .chart-price {
            font-size: 20px;
            font-weight: 700;
            color: #eaecef;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: pulseFade 2s infinite alternate;
        }

        @keyframes pulseFade {
            0% {
                text-shadow: 0 0 0 rgba(240, 185, 11, 0);
            }

            100% {
                text-shadow: 0 0 10px rgba(240, 185, 11, 0.3);
            }
        }

        .price-change-positive {
            font-size: 14px;
            color: #0ecb81;
            background: rgba(14, 203, 129, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
            animation: pulseGreen 2s infinite alternate;
        }

        @keyframes pulseGreen {
            0% {
                box-shadow: 0 0 0 rgba(14, 203, 129, 0);
            }

            100% {
                box-shadow: 0 0 10px rgba(14, 203, 129, 0.5);
            }
        }

        .price-change-positive::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(14, 203, 129, 0.2), transparent);
            animation: shimmerGreen 2s infinite;
        }

        @keyframes shimmerGreen {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(200%);
            }
        }

        .price-change-negative {
            font-size: 14px;
            color: #f6465d;
            background: rgba(246, 70, 93, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
            animation: pulseRed 2s infinite alternate;
        }

        @keyframes pulseRed {
            0% {
                box-shadow: 0 0 0 rgba(246, 70, 93, 0);
            }

            100% {
                box-shadow: 0 0 10px rgba(246, 70, 93, 0.5);
            }
        }

        .price-change-negative::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(246, 70, 93, 0.2), transparent);
            animation: shimmerRed 2s infinite;
        }

        @keyframes shimmerRed {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(200%);
            }
        }

        .chart-timeframes {
            display: flex;
            gap: 8px;
            animation: fadeIn 0.8s ease-out;
            animation-delay: 0.3s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .timeframe {
            font-size: 12px;
            color: #848e9c;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .timeframe:hover {
            color: #f0b90b;
            background: rgba(240, 185, 11, 0.05);
        }

        .timeframe.active {
            background: rgba(240, 185, 11, 0.1);
            color: #f0b90b;
            box-shadow: 0 0 10px rgba(240, 185, 11, 0.2);
        }

        .timeframe.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: #f0b90b;
            animation: timeframeActivate 0.3s ease-out;
        }

        @keyframes timeframeActivate {
            from {
                transform: scaleX(0);
            }

            to {
                transform: scaleX(1);
            }
        }

        .chart-container {
            position: relative;
            height: 150px;
            z-index: 2;
            animation: fadeIn 1s ease-out;
            animation-delay: 0.5s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        .chart-source {
            text-align: right;
            font-size: 10px;
            color: #848e9c;
            padding: 5px 10px;
            font-style: italic;
            position: relative;
            z-index: 2;
            animation: fadeIn 1s ease-out;
            animation-delay: 0.7s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(240, 185, 11, 0.3);
            border-radius: 50%;
            border-top-color: #f0b90b;
            animation: spin 1s linear infinite;
            margin-left: 8px;
            vertical-align: middle;
            box-shadow: 0 0 10px rgba(240, 185, 11, 0.2);
        }

        /* Mobile app specific adjustments */
        .balance-amount {
            font-size: 24px;
        }

        .mobile-quick-action {
            width: 60px;
        }

        .action-icon-wrapper {
            width: 45px;
            height: 45px;
        }

        .action-icon {
            font-size: 18px;
        }

        .action-label {
            font-size: 11px;
        }
    }

    /* Loading Skeleton Animation */
    @keyframes shimmer {
        0% {
            background-position: -468px 0;
        }

        100% {
            background-position: 468px 0;
        }
    }

    /* Smooth Transitions */
    .dashboard-card,
    .market-pair,
    .price-card,
    .wallet-card {
        transition: all 0.3s ease;
    }

    /* Enhanced Hover Effects */
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .market-pair:hover,
    .wallet-card:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .price-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Add these new animation keyframes */
    @keyframes floatUp {
        0% {
            transform: translateY(20px);
            opacity: 0;
        }

        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0.9);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes glowPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(252, 213, 53, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(252, 213, 53, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(252, 213, 53, 0);
        }
    }

    /* Enhanced component animations */
    .welcome-banner {
        animation: floatUp 0.8s ease-out, glowPulse 2s infinite;
    }

    .stat {
        animation: scaleIn 0.5s ease-out backwards;
    }

    .stat:nth-child(2) {
        animation-delay: 0.2s;
    }

    .action-card {
        animation: floatUp 0.5s ease-out backwards;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .action-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .action-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .action-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .action-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .action-card:hover {
        transform: translateY(-8px) scale(1.02);
    }

    .dashboard-card {
        animation: scaleIn 0.5s ease-out backwards;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .price-card {
        animation: floatUp 0.5s ease-out backwards;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .price-card:hover {
        transform: translateY(-5px) scale(1.02);
        background: linear-gradient(145deg, #262b33, #2a2f38);
    }

    .market-pair {
        animation: slideLeft 0.5s ease-out backwards;
        transition: all 0.3s ease;
    }

    .market-pair:hover {
        transform: translateX(5px);
        background: linear-gradient(90deg, #262b33, #1e2126);
    }

    /* Enhanced loading states */
    .loading {
        position: relative;
        overflow: hidden;
    }

    .loading::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shimmer 1.5s infinite;
    }

    /* Smooth number transitions */
    .stat-value,
    .price-value {
        transition: all 0.3s ease;
    }

    /* Enhanced hover effects for icons */
    .action-icon {
        transition: all 0.3s ease;
    }

    .action-card:hover .action-icon {
        transform: scale(1.2);
        color: #fcd535;
    }

    /* Refined price indicators */
    .price-up,
    .price-down {
        position: relative;
        padding-left: 15px;
    }

    .price-up::before,
    .price-down::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
    }

    .price-up::before {
        border-bottom: 6px solid #0ecb81;
        transform: translateY(-50%);
    }

    .price-down::before {
        border-top: 6px solid #f6465d;
        transform: translateY(-50%);
    }

    /* Welcome Banner Base Styles */
    .welcome-banner {
        background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
        border-radius: 12px;
        padding: clamp(20px, 4vw, 30px);
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        overflow: hidden;
        animation: slideInFade 0.8s ease-out;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        gap: 20px;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        /*background: linear-gradient(90deg, #fcd535, transparent);*/
        /*animation: shimmer 2s infinite;*/
    }

    .welcome-text {
        flex: 1;
    }

    .welcome-text h2 {
        font-size: clamp(20px, 4vw, 28px);
        margin-bottom: 10px;
        color: #fff;
        animation: slideUp 0.6s ease-out;
    }

    .welcome-text p {
        font-size: clamp(14px, 2vw, 16px);
        opacity: 0.8;
        animation: slideUp 0.6s ease-out 0.2s backwards;
    }

    .welcome-stats {
        display: flex;
        gap: clamp(15px, 3vw, 30px);
        animation: slideLeft 0.6s ease-out 0.3s backwards;
    }

    .stat {
        background: rgba(255, 255, 255, 0.05);
        padding: 15px;
        border-radius: 8px;
        min-width: 150px;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .stat:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.08);
    }

    .stat-label {
        font-size: 14px;
        color: #848e9c;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: clamp(16px, 3vw, 20px);
        font-weight: 600;
    }

    /* Enhanced Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: clamp(10px, 2vw, 20px);
        margin-bottom: 25px;
        animation: fadeIn 0.8s ease-out 0.4s backwards;
    }

    .quick-actions a {
        text-decoration: none;
        color: inherit;
    }

    .action-card {
        background: linear-gradient(145deg, #1e2126, #262b33);
        border-radius: 12px;
        padding: clamp(15px, 3vw, 25px);
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #2c3137;
        position: relative;
        overflow: hidden;
    }

    .action-card::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .action-card:hover {
        transform: translateY(-5px);
        border-color: #fcd535;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .action-card:hover::after {
        opacity: 1;
    }

    .action-icon {
        font-size: clamp(24px, 4vw, 32px);
        margin-bottom: 15px;
        color: #fcd535;
        animation: bounce 2s infinite;
    }

    .action-card h3 {
        font-size: clamp(16px, 2.5vw, 18px);
        margin: 0;
    }

    /* Main Grid Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: auto auto;
        gap: 20px;
        margin: 20px 0;
        transition: all 0.3s ease;
    }

    /* Sidebar Styles */
    .left-sidebar,
    .right-sidebar {
        transition: all 0.3s ease;
    }

    /* Enhanced Card Styles */
    .dashboard-card {
        background: #1e2126;
        border-radius: 12px;
        border: 1px solid #2c3137;
        margin-bottom: 20px;
        overflow: hidden;
        animation: fadeIn 0.5s ease;
        height: fit-content;
        transition: all 0.3s ease;
    }

    .card-header {
        background: #262b33;
        padding: 20px;
        border-bottom: 1px solid #2c3137;
        font-weight: 500;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header .icon {
        color: #fcd535;
    }

    /* Market Pairs */
    .market-pair {
        display: flex;
        justify-content: space-between;
        padding: 15px 20px;
        border-bottom: 1px solid #2c3137;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .market-pair:hover {
        background: #262b33;
        padding-left: 25px;
    }

    .market-pair-name {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .crypto-icon {
        width: 24px;
        height: 24px;
    }

    /* Price Cards */
    .price-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .price-card {
        background: #262b33;
        padding: 20px;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .price-card:hover {
        transform: translateY(-5px);
    }

    .price-label {
        color: #848e9c;
        font-size: 13px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .price-value {
        font-size: 20px;
        font-weight: 600;
    }

    /* Wallet Section */
    .wallet-card {
        padding: 20px;
        border-bottom: 1px solid #2c3137;
        transition: all 0.3s ease;
    }

    .wallet-card:hover {
        background: #262b33;
    }

    .wallet-icon {
        margin-right: 10px;
        color: #fcd535;
    }

    /* Animations */
    @keyframes slideInFade {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideLeft {
        from {
            transform: translateX(20px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    .price-up {
        color: #0ecb81;
        animation: pulse 2s infinite;
    }

    .price-down {
        color: #f6465d;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }

        100% {
            opacity: 1;
        }
    }

    /* User ID Display */
    .user-id-display {
        margin-top: 10px;
        display: inline-flex;
        align-items: center;
        background: rgba(240, 185, 11, 0.1);
        border-radius: 4px;
        padding: 5px 10px;
        border: 1px solid rgba(240, 185, 11, 0.3);
    }

    .id-label {
        font-size: 12px;
        color: #848e9c;
        margin-right: 5px;
    }

    .id-value {
        font-size: 14px;
        font-weight: 600;
        color: #f0b90b;
    }

    /* Referral Section Styles */
    .referral-section {
        margin-bottom: 25px;
        animation: fadeIn 0.5s ease;
    }

    .referral-content {
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .referral-info {
        flex: 1;
        min-width: 300px;
    }

    .referral-header h3 {
        font-size: 18px;
        color: #eaecef;
        margin: 0 0 10px 0;
    }

    .referral-header p {
        font-size: 14px;
        color: #848e9c;
        margin: 0 0 20px 0;
        line-height: 1.5;
    }

    .referral-stats {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .ref-stat-item {
        background: #262b33;
        border-radius: 8px;
        padding: 15px;
        flex: 1;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #2c3137;
    }

    .ref-stat-item:hover {
        transform: translateY(-5px);
        border-color: #f0b90b;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .ref-stat-value {
        font-size: 18px;
        font-weight: 600;
        color: #f0b90b;
        margin-bottom: 5px;
    }

    .ref-stat-label {
        font-size: 12px;
        color: #848e9c;
    }

    .referral-link-container {
        flex: 1;
        min-width: 300px;
        background: #262b33;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #2c3137;
    }

    .referral-link-container h4 {
        font-size: 16px;
        color: #eaecef;
        margin: 0 0 15px 0;
    }

    .referral-link-box {
        display: flex;
        margin-bottom: 15px;
        position: relative;
    }

    .referral-link {
        flex: 1;
        background: #1e2126;
        border: 1px solid #2c3137;
        border-radius: 4px 0 0 4px;
        padding: 10px 15px;
        font-size: 14px;
        color: #eaecef;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .copy-btn {
        background: #f0b90b;
        color: #0b0e11;
        border: none;
        border-radius: 0 4px 4px 0;
        padding: 0 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .copy-btn:hover {
        background: #e6a800;
    }

    .referral-actions {
        display: flex;
        gap: 10px;
    }

    .ref-action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #1e2126;
        border: 1px solid #2c3137;
        border-radius: 4px;
        padding: 10px;
        color: #eaecef;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .ref-action-btn:hover {
        background: #262b33;
        border-color: #f0b90b;
        color: #f0b90b;
    }

    /* Enhanced Responsive Design for Dashboard Grid */
    @media screen and (max-width: 1200px) {
        .dashboard-grid {
            grid-template-columns: auto auto;
            gap: 15px;
        }
    }

    @media screen and (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: auto auto;
            gap: 15px;
        }

        .right-sidebar {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(auto, auto));
            gap: 15px;
        }

        .right-sidebar .dashboard-card {
            margin-bottom: 0;
        }
    }

    @media screen and (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .left-sidebar {
            grid-column: 1 / -1;
        }

        .main-content {
            grid-column: 1 / -1;
        }

        /* Market Pairs Responsive */
        .market-pairs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .market-pair {
            border-right: 1px solid #2c3137;
        }

        /* Price Overview Responsive */
        .price-overview {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 15px;
        }

        .price-card {
            padding: 15px;
        }
    }

    @media screen and (max-width: 480px) {
        .dashboard-grid {
            gap: 10px;
            margin: 10px 0;
        }

        .dashboard-card {
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .card-header {
            padding: 15px;
        }

        .market-pairs {
            grid-template-columns: 1fr;
        }

        .market-pair {
            padding: 12px 15px;
        }

        .price-overview {
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 10px;
        }

        .price-card {
            padding: 12px;
        }

        .wallet-card {
            padding: 15px;
        }

        /* Font Size Adjustments */
        .card-header span {
            font-size: 16px;
        }

        .price-label {
            font-size: 12px;
        }

        .price-value {
            font-size: 18px;
        }

        .market-pair-name span {
            font-size: 14px;
        }
    }

    /* Loading Skeleton Animation */
    @keyframes shimmer {
        0% {
            background-position: -468px 0;
        }

        100% {
            background-position: 468px 0;
        }
    }

    /* Smooth Transitions */
    .dashboard-card,
    .market-pair,
    .price-card,
    .wallet-card {
        transition: all 0.3s ease;
    }

    /* Enhanced Hover Effects */
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .market-pair:hover,
    .wallet-card:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .price-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Add these new animation keyframes */
    @keyframes floatUp {
        0% {
            transform: translateY(20px);
            opacity: 0;
        }

        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0.9);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes glowPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(252, 213, 53, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(252, 213, 53, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(252, 213, 53, 0);
        }
    }

    /* Enhanced component animations */
    .welcome-banner {
        animation: floatUp 0.8s ease-out, glowPulse 2s infinite;
    }

    .stat {
        animation: scaleIn 0.5s ease-out backwards;
    }

    .stat:nth-child(2) {
        animation-delay: 0.2s;
    }

    .action-card {
        animation: floatUp 0.5s ease-out backwards;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .action-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .action-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .action-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .action-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .action-card:hover {
        transform: translateY(-8px) scale(1.02);
    }

    .dashboard-card {
        animation: scaleIn 0.5s ease-out backwards;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .price-card {
        animation: floatUp 0.5s ease-out backwards;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .price-card:hover {
        transform: translateY(-5px) scale(1.02);
        background: linear-gradient(145deg, #262b33, #2a2f38);
    }

    .market-pair {
        animation: slideLeft 0.5s ease-out backwards;
        transition: all 0.3s ease;
    }

    .market-pair:hover {
        transform: translateX(5px);
        background: linear-gradient(90deg, #262b33, #1e2126);
    }

    /* Enhanced loading states */
    .loading {
        position: relative;
        overflow: hidden;
    }

    .loading::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shimmer 1.5s infinite;
    }

    /* Smooth number transitions */
    .stat-value,
    .price-value {
        transition: all 0.3s ease;
    }

    /* Enhanced hover effects for icons */
    .action-icon {
        transition: all 0.3s ease;
    }

    .action-card:hover .action-icon {
        transform: scale(1.2);
        color: #fcd535;
    }

    /* Refined price indicators */
    .price-up,
    .price-down {
        position: relative;
        padding-left: 15px;
    }

    .price-up::before,
    .price-down::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
    }

    .price-up::before {
        border-bottom: 6px solid #0ecb81;
        transform: translateY(-50%);
    }

    .price-down::before {
        border-top: 6px solid #f6465d;
        transform: translateY(-50%);
    }

    /* Responsive Breakpoints */
    @media screen and (max-width: 992px) {
        .welcome-banner {
            padding: 20px;
        }

        .stat {
            min-width: 130px;
        }
    }

    @media screen and (max-width: 768px) {
        .welcome-banner {
            flex-direction: column;
            text-align: center;
            padding: 25px;
            gap: 25px;
        }

        .user-id-display {
            margin: 10px auto 0;
            display: inline-flex;
        }

        .welcome-text {
            width: 100%;
        }

        .welcome-stats {
            width: 100%;
            justify-content: center;
        }

        .stat {
            flex: 1;
            min-width: 120px;
            max-width: 160px;
        }
    }

    @media screen and (max-width: 480px) {
        .welcome-banner {
            padding: 20px 15px;
            margin-bottom: 15px;
            gap: 20px;
        }

        .referral-content {
            padding: 15px;
            gap: 15px;
        }

        .referral-stats {
            flex-direction: column;
            gap: 10px;
        }

        .referral-link-container {
            padding: 15px;
        }

        .welcome-text h2 {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .welcome-text p {
            font-size: 14px;
        }

        .welcome-stats {
            flex-direction: column;
            gap: 10px;
        }

        .stat {
            max-width: 100%;
            padding: 12px;
        }

        .stat-label {
            font-size: 12px;
        }

        .stat-value {
            font-size: 18px;
        }
    }

    /* High DPI Screens */
    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        .welcome-banner {
            border: 0.5px solid rgba(255, 255, 255, 0.1);
        }
    }

    /* Bot Subscription Animation Styles */
    .bot-subscription-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
        animation: botBannerPulse 3s ease-in-out infinite;
    }

    @keyframes botBannerPulse {
        0%, 100% {
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        50% {
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.5);
        }
    }

    .bot-subscription-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: botShine 3s linear infinite;
    }

    @keyframes botShine {
        0% {
            transform: translateX(-100%) translateY(-100%);
        }
        100% {
            transform: translateX(100%) translateY(100%);
        }
    }

    .bot-subscription-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .bot-subscription-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .bot-icon-container {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: botIconRotate 4s linear infinite;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    @keyframes botIconRotate {
        0%, 100% {
            transform: rotate(0deg) scale(1);
        }
        25% {
            transform: rotate(5deg) scale(1.05);
        }
        50% {
            transform: rotate(0deg) scale(1.1);
        }
        75% {
            transform: rotate(-5deg) scale(1.05);
        }
    }

    .bot-icon-container i {
        font-size: 30px;
        color: white;
        animation: botIconPulse 2s ease-in-out infinite;
    }

    @keyframes botIconPulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(0.95);
        }
    }

    .bot-subscription-text h3 {
        color: white;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 5px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bot-status-badge {
        background: rgba(16, 185, 129, 0.9);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        animation: badgePulse 2s ease-in-out infinite;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    @keyframes badgePulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .bot-subscription-text p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        margin: 0;
    }

    .bot-subscription-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    .bot-stats {
        display: flex;
        gap: 20px;
        background: rgba(255, 255, 255, 0.15);
        padding: 12px 20px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    .bot-stat-item {
        text-align: center;
    }

    .bot-stat-value {
        color: white;
        font-size: 20px;
        font-weight: 700;
        display: block;
        animation: numberCount 1s ease-out;
    }

    @keyframes numberCount {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .bot-stat-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bot-active-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        color: white;
        font-size: 13px;
        font-weight: 600;
    }

    .bot-active-dot {
        width: 10px;
        height: 10px;
        background: #10b981;
        border-radius: 50%;
        animation: dotPulse 1.5s ease-in-out infinite;
        box-shadow: 0 0 10px #10b981;
    }

    @keyframes dotPulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.3);
            opacity: 0.7;
        }
    }

    /* Mobile Responsive */
    @media screen and (max-width: 768px) {
        .bot-subscription-banner {
            padding: 15px;
        }

        .bot-subscription-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .bot-subscription-right {
            width: 100%;
            align-items: flex-start;
        }

        .bot-stats {
            width: 100%;
            justify-content: space-around;
        }

        .bot-icon-container {
            width: 50px;
            height: 50px;
        }

        .bot-icon-container i {
            font-size: 24px;
        }

        .bot-subscription-text h3 {
            font-size: 18px;
        }
    }
</style>


<!-- Fresh Mobile App Container -->
<div class="fresh-mobile">
    <!-- Fresh Welcome Banner -->
    <div class="fresh-welcome">
        <div class="fresh-welcome-content">
            <div class="fresh-welcome-text">
                <h1>Welcome back, <?php echo $user->name; ?>!</h1>
                <p>Your trading journey continues with us</p>
                <div class="fresh-user-id">
                    <i class="fas fa-user"></i>
                    ID: <?php echo $user->login_id; ?>
                </div>
                <!-- <div class="fresh-user-rank" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
                    <span class="fresh-rank-badge" style="background: linear-gradient(135deg, #f5d76e 0%, #f0b90b 45%, #c9970a 100%); color:#fff; padding:6px 12px; border-radius:8px; font-size:0.9rem; font-weight:600;">
                        <i class="fas fa-trophy"></i> Reward: <?php echo isset($reward_arr[(int) $user->reward]) ? htmlspecialchars($reward_arr[(int) $user->reward]) : 'No Rank'; ?>
                    </span>
                    <span class="fresh-rank-badge" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); color:#fff; padding:6px 12px; border-radius:8px; font-size:0.9rem; font-weight:600;">
                        <i class="fas fa-crown"></i> Royalty: <?php echo isset($royalty_rank_names[(int) $user->royalty]) ? htmlspecialchars($royalty_rank_names[(int) $user->royalty]) : '-'; ?>
                    </span>
                </div> -->
            </div>
            <div class="fresh-stats">
                <div class="fresh-stat">
                    <div class="fresh-stat-value"><?php echo $active_orders; ?></div>
                    <div class="fresh-stat-label">Active Orders</div>
                </div>
                <div class="fresh-stat">
                    <div class="fresh-stat-value"><?php echo round($user->balance, 2); ?></div>
                    <div class="fresh-stat-label">Balance USDT</div>
                </div>
                <div class="fresh-stat <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>" id="manageBox">
                    <a href="live_trading.php" class="fresh-btn <?php echo ($trade_active && $active_orders > 0) ? 'fresh-btn-success' : 'fresh-btn-primary'; ?>">
                        <!-- <i class="fas fa-<?php echo ($trade_active && $active_orders > 0) ? 'cog' : 'play'; ?>"></i>
                        <?php echo ($trade_active && $active_orders > 0) ? 'Manage' : 'Start Trading'; ?> -->
                        <?php if ($trade_active && $active_orders > 0) { ?>
                        <span class="bot-only-icon">
                            <i class="fas fa-robot fs-1"></i>
                        </span>
                    <?php } else { ?>
                        <i class="fas fa-play"></i>
                        Start Trading
                    <?php } ?>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <?php if ($hasBotSubscription): ?>
    <!-- Bot Subscription Animation Banner (Mobile) -->
    <div class="bot-subscription-banner">
        <div class="bot-subscription-content">
            <div class="bot-subscription-left">
                <div class="bot-icon-container">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="bot-subscription-text">
                    <h3>
                        Bot Subscription Active
                        <span class="bot-status-badge">
                            <i class="fas fa-check-circle"></i> PREMIUM
                        </span>
                    </h3>
                    <p>Your trading bot is working 24/7 for you</p>
                </div>
            </div>
            <div class="bot-subscription-right">
                <div class="bot-stats">
                    <div class="bot-stat-item">
                        <span class="bot-stat-value">$<?php echo number_format($botSubscriptionData->amount ?? 0, 2); ?></span>
                        <span class="bot-stat-label">Invested</span>
                    </div>
                    <div class="bot-stat-item">
                        <span class="bot-stat-value"><?php echo $active_orders; ?></span>
                        <span class="bot-stat-label">Active</span>
                    </div>
                </div>
                <div class="bot-active-indicator">
                    <span class="bot-active-dot"></span>
                    <span>Bot is Running</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($hasSelfTrade): ?>
    <!-- Self Trade - Daily Activation for ROI -->
    <div class="fresh-card" style="margin: 0 0 24px 0;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="mb-1" style="color: var(--text-primary);"><i class="fas fa-chart-line text-success me-2"></i>Self Trade - Daily Activation</h4>
                <p class="mb-0" style="color: var(--text-secondary); font-size: 0.9rem;">Activate trade daily to receive ROI. After cron runs, you need to activate again next day.</p>
            </div>
            <div>
                <?php if ($trade_active): ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active for Today</span>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="update_trade_status" value="1">
                        <input type="hidden" name="status" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Deactivate</button>
                    </form>
                </div>
                <?php else: ?>
                <form method="post">
                    <input type="hidden" name="update_trade_status" value="1">
                    <input type="hidden" name="status" value="1">
                    <button type="submit" class="btn btn-success"><i class="fas fa-play me-1"></i> Activate Trade for Today</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Fresh Trading Status -->
    <!--<div class="fresh-trading <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>">-->
    <!--    <div class="fresh-trading-content">-->
    <!--        <div class="fresh-trading-info">-->
    <!--            <div class="fresh-trading-icon <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>">-->
    <!--                <i class="fas fa-robot"></i>-->
    <!--            </div>-->
    <!--            <div class="fresh-trading-details">-->
    <!--                <h3>AI Trading Bot</h3>-->
    <!--                <p>Automated cryptocurrency trading</p>-->
    <!--                <div class="fresh-status">-->
    <!--                    <span class="fresh-status-dot <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>"></span>-->
    <!--                    <span class="fresh-status-text <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>">-->
    <!--                        <?php echo ($trade_active && $active_orders > 0) ? 'Active Trading' : 'Ready to Start'; ?>-->
    <!--                    </span>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="fresh-trading-actions">-->
    <!--            <div class="fresh-trading-stat">-->
    <!--                <div class="fresh-trading-stat-value"><?php echo $active_orders; ?></div>-->
    <!--                <div class="fresh-trading-stat-label">Active Orders</div>-->
    <!--            </div>-->
    <!--            <a href="live_trading.php" class="fresh-btn <?php echo ($trade_active && $active_orders > 0) ? 'fresh-btn-success' : 'fresh-btn-primary'; ?>">-->
    <!--                <i class="fas fa-<?php echo ($trade_active && $active_orders > 0) ? 'cog' : 'play'; ?>"></i>-->
    <!--                <?php echo ($trade_active && $active_orders > 0) ? 'Manage' : 'Start Trading'; ?>-->
    <!--            </a>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Referral Section -->
    <div class="referral-section">
        <div class="section-header">
            <h2 class="section-title">Trading Network Growth</h2>
            <i class="fas fa-network-wired section-icon"></i>
        </div>
        <div class="referral-content">
            <div class="referral-info">
                <h3>Grow Your Trading Network</h3>
                <p>Expand your trading influence and earn rewards through our multi-level referral program</p>
                <div class="referral-stats">
                    <div class="ref-stat-card">
                        <div class="ref-stat-value" style="color : #eaecef !important"><?php echo round(get_sum('income_direct', 'amount', "uid='$uid' AND type=0") * 1, 2); ?></div>
                        <div class="ref-stat-label" style="color : #848e9c !important">Referral Earnings</div>
                    </div>
                    <div class="ref-stat-card">
                        <div class="ref-stat-value" style="color : #eaecef !important"><?php echo round(get_sum('income_level', 'amount', "uid='$uid' AND type=2") * 1, 2); ?></div>
                        <div class="ref-stat-label" style="color : #848e9c !important">Level ROI Income</div>
                    </div>
                </div>
            </div>
            <div class="referral-link-section">
                <h4>Your Referral Link</h4>
                <div class="referral-link-container">
                    <input type="text" id="referral-link1" class="referral-link" value="<?php echo SITE_URL; ?>/soft/member/register.php?ref=<?php echo $uid; ?>" readonly>
                    <button class="copy-button" onclick="CopyToClipboard('referral-link1')" id="referral-link_copy1">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
                <div class="referral-actions">
                    <!--<a href="https://skillclash.live/home" target="_blank" class="ref-action-btn">
                        <i class="fas fa-share icon"></i> Go To Game
                    </a>-->
                    <!--<a href="referral.php" class="ref-action-btn">-->
                    <!--    <i class="fas fa-users"></i> View Network-->
                    <!--</a>-->
                </div>
            </div>
        </div>
    </div>
    <!-- Fresh Quick Actions -->
    <div class="fresh-mobile-section">
        <i class="fas fa-bolt"></i> Quick Actions
    </div>
    <div class="fresh-mobile-tools">
        <a href="trade.php" class="fresh-mobile-tool">
            <div class="fresh-mobile-tool-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="fresh-mobile-tool-name">Trade Now</div>
        </a>
        <a href="report_invest.php" class="fresh-mobile-tool">
            <div class="fresh-mobile-tool-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="fresh-mobile-tool-name">Trade History</div>
        </a>
        <a href="deposit_block.php" class="fresh-mobile-tool">
            <div class="fresh-mobile-tool-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="fresh-mobile-tool-name">Deposit</div>
        </a>
        <a href="withdrawal_block.php" class="fresh-mobile-tool">
            <div class="fresh-mobile-tool-icon">
                <i class="fas fa-minus-circle"></i>
            </div>
            <div class="fresh-mobile-tool-name">Withdraw</div>
        </a>
        <!-- <a href="report_growth.php" class="fresh-mobile-tool">
            <div class="fresh-mobile-tool-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="fresh-mobile-tool-name">MPR 10%</div>
        </a> -->
        <!-- <a href="email_inbox.php" class="fresh-mobile-tool">
            <div class="fresh-mobile-tool-icon">
                <i class="fas fa-headset"></i>
            </div>
            <div class="fresh-mobile-tool-name">Support</div>
        </a> -->
    </div>
    <!--<a href="settings.php" class="tool-card">-->
    <!--    <div class="tool-icon">-->
    <!--        <i class="fas fa-cog"></i>-->
    <!--    </div>-->
    <!--    <div class="tool-name">Settings</div>-->
    <!--</a>-->

    <!-- Fresh Wallets Section -->
    <div class="fresh-portfolio">
            <div class="fresh-section-header">
                <h2 class="fresh-section-title">Portfolio Overview</h2>
                <i class="fas fa-chart-pie fresh-section-icon"></i>
            </div>
            <div class="fresh-portfolio-grid">
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-wallet"></i>
                        Earning Wallet
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round($user->wallet * 1, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Available
                    </div>
                </div>
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-chart-line"></i>
                        Package Wallet
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round($user->wallet_topup * 1, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Active
                    </div>
                </div>
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-chart-bar"></i>
                        Total Trades
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round(get_sum('investments', 'amount', "uid='$uid' AND ipid != 4") * 1, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Volume
                    </div>
                </div>
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-coins"></i>
                        Total Income
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round($total_in, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Earned
                    </div>
                </div>
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-hourglass-half"></i>
                        Remaining Amount
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round($remaining_amount, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Cap left
                    </div>
                </div>
            </div>
        </div>

    <!-- Fresh Team Incomes Section -->
    <!-- <div class="fresh-mobile-section">
        <i class="fas fa-users"></i> Team Incomes
    </div>
    <div class="fresh-mobile-cards">
        <div class="fresh-mobile-card">
            <div class="fresh-mobile-info">
                <div class="fresh-mobile-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="fresh-mobile-details">
                    <div class="fresh-mobile-title">Team Members</div>
                    <div class="fresh-mobile-desc">Total team size</div>
                </div>
            </div>
            <div class="fresh-mobile-balance">
                <div class="fresh-mobile-amount"><?php echo $user->teamc; ?></div>
                <div class="fresh-mobile-unit">Members</div>
            </div>
        </div>

        <div class="fresh-mobile-card">
            <div class="fresh-mobile-info">
                <div class="fresh-mobile-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="fresh-mobile-details">
                    <div class="fresh-mobile-title">Team Business</div>
                    <div class="fresh-mobile-desc">Total team volume</div>
                </div>
            </div>
            <div class="fresh-mobile-balance">
                <div class="fresh-mobile-amount"><?php echo round($user->teamb, 2); ?></div>
                <div class="fresh-mobile-unit">USDT</div>
            </div>
        </div>

        <?php  /*<div class="fresh-mobile-card">
             <div class="fresh-mobile-info">
                 <div class="fresh-mobile-icon">
                     <i class="fas fa-handshake"></i>
                 </div>
                 <div class="fresh-mobile-details">
                      <div class="fresh-mobile-title">Referral Income</div>
                     <div class="fresh-mobile-desc">Direct referral earnings</div>
                 </div>
             </div>
             <div class="fresh-mobile-balance">
                 <div class="fresh-mobile-amount"><?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?></div>
                 <div class="fresh-mobile-unit">USDT</div>
             </div>
         </div>*/
        ?>

        <div class="fresh-mobile-card">
            <div class="fresh-mobile-info">
                <div class="fresh-mobile-icon">
                    <i class="fas fa-chart-area"></i>
                </div>
                <div class="fresh-mobile-details">
                    <div class="fresh-mobile-title">Trading Income</div>
                    <div class="fresh-mobile-desc">Profit from trading</div>
                </div>
            </div>
            <div class="fresh-mobile-balance">
                <div class="fresh-mobile-amount"><?php echo round(get_sum('income_growth', 'amount', "uid='" . $uid . "'") * 1, 2); ?></div>
                <div class="fresh-mobile-unit">USDT</div>
            </div>
        </div>

        <div class="fresh-mobile-card">
            <div class="fresh-mobile-info">
                <div class="fresh-mobile-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div class="fresh-mobile-details">
                    <div class="fresh-mobile-title">Direct Business</div>
                    <div class="fresh-mobile-desc">From your network</div>
                </div>
            </div>
            <div class="fresh-mobile-balance">
                <div class="fresh-mobile-amount"><?php echo round(get_sum('investments', 'amount', "uid IN (SELECT uid FROM user WHERE status = 0 AND topup>0 AND refer_id='" . $uid . "')") * 1, 2); ?></div>
                <div class="fresh-mobile-unit">USDT</div>
            </div>
        </div>

        <div class="fresh-mobile-card">
            <div class="fresh-mobile-info">
                <div class="fresh-mobile-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="fresh-mobile-details">
                    <div class="fresh-mobile-title">Total Income</div>
                    <div class="fresh-mobile-desc">From Network & Trading</div>
                </div>
            </div>
            <div class="fresh-mobile-balance">
                <div class="fresh-mobile-amount"><?php echo round($total_in, 2); ?></div>
                <div class="fresh-mobile-unit">USDT</div>
            </div>
        </div>
    </div> -->

    <!-- Fresh Bottom Navigation -->
    <!-- <div class="fresh-bottom-nav">
        <div class="fresh-bottom-nav-container">
            <a href="dashboard.php" class="fresh-nav-item active">
                <i class="fas fa-home fresh-nav-icon"></i>
                <span class="fresh-nav-label">Home</span>
            </a>
            <a href="trade.php" class="fresh-nav-item">
                <i class="fas fa-chart-line fresh-nav-icon"></i>
                <span class="fresh-nav-label">Trades</span>
            </a>
            <a href="deposit_block.php" class="fresh-nav-item">
                <i class="fas fa-wallet fresh-nav-icon"></i>
                <span class="fresh-nav-label">Wallet</span>
            </a>
            <a href="sharing_social.php" class="fresh-nav-item">
                <i class="fas fa-gift fresh-nav-icon"></i>
                <span class="fresh-nav-label">Earn</span>
            </a>
            <a href="profile.php" class="fresh-nav-item">
                <i class="fas fa-user fresh-nav-icon"></i>
                <span class="fresh-nav-label">Profile</span>
            </a>
        </div>
    </div> -->


     <!-- Fresh Income Cards Section -->
     <div class="fresh-income-section">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">Income Overview</h2>
            <i class="fas fa-coins fresh-section-icon"></i>
        </div>
        <div class="fresh-income-grid">
            <?php  /*<div class="fresh-income-card">
                 <div class="fresh-income-icon">
                     <i class="fas fa-handshake"></i>
                 </div>
                 <div class="fresh-income-content">
                     <div class="fresh-income-label">Referral Income</div>
                     <div class="fresh-income-value">$<?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?></div>
                     <div class="fresh-income-desc">Direct referral earnings</div>
                 </div>
             </div>*/
            ?>

            <div class="fresh-income-card">
                <div class="fresh-income-icon">
                    <i class="fas fa-chart-area"></i>
                </div>
                <div class="fresh-income-content">
                    <div class="fresh-income-label">Trading Income</div>
                    <div class="fresh-income-value">$<?php echo round(get_sum('income_growth', 'amount', "uid='" . $uid . "'") * 1, 2); ?></div>
                    <div class="fresh-income-desc">Profit from trading</div>
                </div>
            </div>

            <?php  /*<div class="fresh-income-card">
                 <div class="fresh-income-icon">
                     <i class="fas fa-layer-group"></i>
                 </div>
                 <div class="fresh-income-content">
                     <div class="fresh-income-label">Level Income</div>
                     <div class="fresh-income-value">$<?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=1") * 1, 2); ?></div>
                     <div class="fresh-income-desc">Multi-level bonuses</div>
                 </div>
             </div>*/
            ?>

            <div class="fresh-income-card">
                <div class="fresh-income-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="fresh-income-content">
                    <div class="fresh-income-label">Direct Income</div>
                    <div class="fresh-income-value">$<?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?></div>
                    <div class="fresh-income-desc">Multi-level bonuses</div>
                </div>
            </div>

            <a href="report_level.php?type=2" class="fresh-income-card" style="text-decoration:none; color:inherit; display:block;">
                <div class="fresh-income-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="fresh-income-content">
                    <div class="fresh-income-label">Level Generation Income</div>
                    <div class="fresh-income-value">$<?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=2") * 1, 2); ?></div>
                    <div class="fresh-income-desc">Multi-level bonuses</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Fresh Team Overview Section -->
    <div class="fresh-team-section">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">Team Overview</h2>
            <i class="fas fa-users fresh-section-icon"></i>
        </div>
        <div class="fresh-team-grid">
            <div class="fresh-team-card">
                <div class="fresh-team-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="fresh-team-content">
                    <div class="fresh-team-label">Team Members</div>
                    <div class="fresh-team-value"><?php echo $total_members; ?></div>
                    <div class="fresh-team-desc">Total team size</div>
                </div>
            </div>

            <div class="fresh-team-card">
                <div class="fresh-team-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="fresh-team-content">
                    <div class="fresh-team-label">Team Business</div>
                    <div class="fresh-team-value">$<?php echo round($user->teamb, 2); ?></div>
                    <div class="fresh-team-desc">Total team volume</div>
                </div>
            </div>

            <div class="fresh-team-card">
                <div class="fresh-team-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div class="fresh-team-content">
                    <div class="fresh-team-label">Direct Business</div>
                    <div class="fresh-team-value">$<?php echo round(get_sum('investments', 'amount', "uid IN (SELECT uid FROM user WHERE status = 0 AND topup>0 AND refer_id='" . $uid . "')") * 1, 2); ?></div>
                    <div class="fresh-team-desc">From your network</div>
                </div>
            </div>

            <div class="fresh-team-card">
                <div class="fresh-team-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="fresh-team-content">
                    <div class="fresh-team-label">Total Earnings</div>
                    <div class="fresh-team-value">$<?php echo round($total_in, 2); ?></div>
                    <div class="fresh-team-desc">All income combined</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fresh Desktop Dashboard -->
<div class="fresh-container">
    <!-- Fresh Welcome Banner -->
    <div class="fresh-welcome">
        <div class="fresh-welcome-content">
            <div class="fresh-welcome-text">
                <h1>Welcome back, <?php echo $user->name; ?>!</h1>
                <h4>Your portfolio overview and market summary</h4>
                <div class="fresh-user-id">
                    <i class="fas fa-user"></i>
                    ID: <?php echo $user->login_id; ?>
                </div>
                <!-- <div class="fresh-user-rank" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
                    <span class="fresh-rank-badge" style="background: linear-gradient(135deg, #f5d76e 0%, #f0b90b 45%, #c9970a 100%); color:#fff; padding:6px 12px; border-radius:8px; font-size:0.9rem; font-weight:600;">
                        <i class="fas fa-trophy"></i> Reward: <?php echo isset($reward_arr[(int) $user->reward]) ? htmlspecialchars($reward_arr[(int) $user->reward]) : 'No Rank'; ?>
                    </span>
                    <span class="fresh-rank-badge" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); color:#fff; padding:6px 12px; border-radius:8px; font-size:0.9rem; font-weight:600;">
                        <i class="fas fa-crown"></i> Royalty: <?php echo isset($royalty_rank_names[(int) $user->royalty]) ? htmlspecialchars($royalty_rank_names[(int) $user->royalty]) : '-'; ?>
                    </span>
                </div> -->
            </div>
            <div class="fresh-stats">
                <div class="fresh-stat">
                    <div class="fresh-stat-value">$<?php echo round(get_sum('income_growth', 'amount', "uid='" . $uid . "'") * 1, 2); ?></div>
                    <div class="fresh-stat-label">Trading Income</div>
                </div>
                <div class="fresh-stat">
                    <div class="fresh-stat-value">$<?php echo round($user->balance, 2); ?></div>
                    <div class="fresh-stat-label">Total Balance</div>
                </div>
                <div class="fresh-stat">
                    <div class="fresh-stat-value"><?php echo $total_members; ?></div>
                    <div class="fresh-stat-label">Team Members</div>
                </div>
                <!--<div class="fresh-stat <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>">-->
                <a href="live_trading.php" class="fresh-btn <?php echo ($trade_active && $active_orders > 0) ? 'fresh-btn-success' : 'fresh-btn-primary'; ?>">
                    <!-- <i class="fas fa-<?php echo ($trade_active && $active_orders > 0) ? 'cog' : 'play'; ?>"></i>
                    <?php echo ($trade_active && $active_orders > 0) ? 'Manage' : 'Start Trading'; ?> -->
                    <?php if ($trade_active && $active_orders > 0) { ?>
                        <span class="bot-only-icon">
                            <i class="fas fa-robot fs-1"></i>
                        </span>
                    <?php } else { ?>
                        <i class="fas fa-play"></i>
                        Start Trading
                    <?php } ?>
                </a>
                <!--</div>-->
            </div>
        </div>
    </div>

    <?php if ($hasBotSubscription): ?>
    <!-- Bot Subscription Animation Banner (Desktop) -->
    <div class="bot-subscription-banner">
        <div class="bot-subscription-content">
            <div class="bot-subscription-left">
                <div class="bot-icon-container">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="bot-subscription-text">
                    <h3>
                        Bot Subscription Active
                        <span class="bot-status-badge">
                            <i class="fas fa-check-circle"></i> PREMIUM
                        </span>
                    </h3>
                    <p>Your trading bot is working 24/7 for you</p>
                </div>
            </div>
            <div class="bot-subscription-right">
                <div class="bot-stats">
                    <div class="bot-stat-item">
                        <span class="bot-stat-value">$<?php echo number_format($botSubscriptionData->amount ?? 0, 2); ?></span>
                        <span class="bot-stat-label">Invested</span>
                    </div>
                    <div class="bot-stat-item">
                        <span class="bot-stat-value"><?php echo $active_orders; ?></span>
                        <span class="bot-stat-label">Active</span>
                    </div>
                </div>
                <div class="bot-active-indicator">
                    <span class="bot-active-dot"></span>
                    <span>Bot is Running</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Fresh Trading Status -->
    <!--<div class="fresh-trading <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>">-->
    <!--    <div class="fresh-trading-content">-->
    <!--        <div class="fresh-trading-info">-->
    <!--            <div class="fresh-trading-icon <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>">-->
    <!--                <i class="fas fa-robot"></i>-->
    <!--            </div>-->
    <!--            <div class="fresh-trading-details">-->
    <!--                <h4>AI Trading Bot</h4>-->
    <!--                <h5>Automated cryptocurrency trading system</h5>-->
    <!--                <div class="fresh-status">-->
    <!--                    <span class="fresh-status-dot <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>"></span>-->
    <!--                    <span class="fresh-status-text <?php echo ($trade_active && $active_orders > 0) ? 'active' : 'inactive'; ?>">-->
    <!--                        <?php echo ($trade_active && $active_orders > 0) ? 'Active Trading' : 'Ready to Start'; ?>-->
    <!--                    </span>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="fresh-trading-actions">-->
    <!--            <div class="fresh-trading-stat">-->
    <!--                <div class="fresh-trading-stat-value"><?php echo $active_orders; ?></div>-->
    <!--                <div class="fresh-trading-stat-label">Active Orders</div>-->
    <!--            </div>-->
    <!--            <a href="live_trading.php" class="fresh-btn <?php echo ($trade_active && $active_orders > 0) ? 'fresh-btn-success' : 'fresh-btn-primary'; ?>">-->
    <!--                <i class="fas fa-<?php echo ($trade_active && $active_orders > 0) ? 'cog' : 'play'; ?>"></i>-->
    <!--                <?php echo ($trade_active && $active_orders > 0) ? 'Manage Trading' : 'Start Trading'; ?>-->
    <!--            </a>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Fresh Quick Actions -->
    <div class="fresh-actions">
        <a href="trade.php" class="fresh-action-card">
            <div class="fresh-action-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="fresh-action-title">
                <h3>Trade Now </h3>
            </div>
            <div class="fresh-action-desc">
                <h4>Start trading with AI assistance</h4>
            </div>
        </a>
        <a href="deposit_block.php" class="fresh-action-card">
            <div class="fresh-action-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="fresh-action-title">
                <h3>Deposit</h3>
            </div>
            <div class="fresh-action-desc">
                <h4>Add funds to your wallet</h4>
            </div>
        </a>
        <a href="withdrawal_block.php" class="fresh-action-card">
            <div class="fresh-action-icon">
                <i class="fas fa-minus-circle"></i>
            </div>
            <div class="fresh-action-title">
                <h3>Withdraw</h3>
            </div>
            <div class="fresh-action-desc">
                <h4>Withdraw your earnings</h4>
            </div>
        </a>
        <!-- <a href="referral.php" class="fresh-action-card">
            <div class="fresh-action-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="fresh-action-title"><h3>Referrals</h3></div>
            <div class="fresh-action-desc"><h4>Grow your network</h4></div>
        </a> -->
    </div>
    <!-- Fresh Grid Layout -->
    <div class="fresh-grid">
        <!-- Fresh Referral Section -->
        <div class="fresh-referral">
            <div class="fresh-section-header">
                <h2 class="fresh-section-title">Trading Network Growth</h2>
                <i class="fas fa-network-wired fresh-section-icon"></i>
            </div>
            <div class="fresh-referral-content">
                <div class="fresh-referral-info">
                    <h3>Grow Your Trading Network</h3>
                    <p>Expand your trading influence and earn rewards through our multi-level referral program</p>
                    <div class="fresh-referral-stats">
                        <div class="fresh-stat-card">
                            <div class="fresh-stat-value" style="color : #eaecef !important"><?php echo round(get_sum('income_direct', 'amount', "uid='$uid' AND type=0") * 1, 2); ?></div>
                            <div class="fresh-stat-label" style="color : #848e9c !important">Referral Income</div>
                        </div>
                        <?php  /*<div class="fresh-stat-card">
                             <div class="fresh-stat-value" style="color : #eaecef !important"><?php echo round(get_sum('income_level', 'amount', "uid='$uid' AND type=1") * 1, 2); ?></div>
                             <div class="fresh-stat-label" style="color : #848e9c !important">Level Income</div>
                         </div>*/
                        ?>
                        <div class="fresh-stat-card">
                            <div class="fresh-stat-value" style="color : #eaecef !important"><?php echo round(get_sum('income_level', 'amount', "uid='$uid' AND type=2") * 1, 2); ?></div>
                            <div class="fresh-stat-label" style="color : #848e9c !important">Level Generation Income</div>
                        </div>
                    </div>
                </div>
                <div class="fresh-link-section">
                    <h4>Your Referral Link</h4>
                    <div class="fresh-link-container">
                        <input type="text" id="referral-link" class="fresh-link-input" value="<?php echo SITE_URL; ?>/soft/member/register.php?ref=<?php echo $uid; ?>" readonly>
                        <button class="fresh-copy-btn" onclick="CopyToClipboard('referral-link')" id="referral-link_copy">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                    <div class="fresh-link-actions">
                        <!--<a href="https://skillclash.live/home" target="_blank" class="ref-action-btn">
                            <i class="fas fa-share icon"></i> Go To Game
                        </a>-->
                        <!--<a href="referral.php" class="fresh-link-btn">-->
                        <!--    <i class="fas fa-users"></i> View Network-->
                        <!--</a>-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Fresh Portfolio Overview -->
        <div class="fresh-portfolio">
            <div class="fresh-section-header">
                <h2 class="fresh-section-title">Portfolio Overview</h2>
                <i class="fas fa-chart-pie fresh-section-icon"></i>
            </div>
            <div class="fresh-portfolio-grid">
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-wallet"></i>
                        Earning Wallet
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round($user->wallet * 1, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Available
                    </div>
                </div>
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-chart-line"></i>
                        Package Wallet
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round($user->wallet_topup * 1, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Active
                    </div>
                </div>
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-chart-bar"></i>
                        Total Trades
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round(get_sum('investments', 'amount', "uid='$uid' AND ipid != 4") * 1, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Volume
                    </div>
                </div>
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-coins"></i>
                        Total Income
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round($total_in, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Earned
                    </div>
                </div>
                <div class="fresh-portfolio-card">
                    <div class="fresh-portfolio-label">
                        <i class="fas fa-hourglass-half"></i>
                        Remaining Amount
                    </div>
                    <div class="fresh-portfolio-value"><?php echo round($remaining_amount, 2); ?> USDT</div>
                    <div class="fresh-portfolio-change positive">
                        <i class="fas fa-arrow-up"></i> Cap left
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fresh Income Cards Section -->
    <div class="fresh-income-section">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">Income Overview</h2>
            <i class="fas fa-coins fresh-section-icon"></i>
        </div>
        <div class="fresh-income-grid">
            <?php  /*<div class="fresh-income-card">
                 <div class="fresh-income-icon">
                     <i class="fas fa-handshake"></i>
                 </div>
                 <div class="fresh-income-content">
                     <div class="fresh-income-label">Referral Income</div>
                     <div class="fresh-income-value">$<?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?></div>
                     <div class="fresh-income-desc">Direct referral earnings</div>
                 </div>
             </div>*/
            ?>

            <div class="fresh-income-card">
                <div class="fresh-income-icon">
                    <i class="fas fa-chart-area"></i>
                </div>
                <div class="fresh-income-content">
                    <div class="fresh-income-label">Trading Income</div>
                    <div class="fresh-income-value">$<?php echo round(get_sum('income_growth', 'amount', "uid='" . $uid . "'") * 1, 2); ?></div>
                    <div class="fresh-income-desc">Profit from trading</div>
                </div>
            </div>

            <?php  /*<div class="fresh-income-card">
                 <div class="fresh-income-icon">
                     <i class="fas fa-layer-group"></i>
                 </div>
                 <div class="fresh-income-content">
                     <div class="fresh-income-label">Level Income</div>
                     <div class="fresh-income-value">$<?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=1") * 1, 2); ?></div>
                     <div class="fresh-income-desc">Multi-level bonuses</div>
                 </div>
             </div>*/
            ?>

            <div class="fresh-income-card">
                <div class="fresh-income-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="fresh-income-content">
                    <div class="fresh-income-label">Direct Income</div>
                    <div class="fresh-income-value">$<?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?></div>
                    <div class="fresh-income-desc">Multi-level bonuses</div>
                </div>
            </div>

            <div class="fresh-income-card">
                <div class="fresh-income-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="fresh-income-content">
                    <div class="fresh-income-label">Level Generation Income</div>
                    <div class="fresh-income-value">$<?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=2") * 1, 2); ?></div>
                    <div class="fresh-income-desc">Multi-level bonuses</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fresh Team Overview Section -->
    <div class="fresh-team-section">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">Team Overview</h2>
            <i class="fas fa-users fresh-section-icon"></i>
        </div>
        <div class="fresh-team-grid">
            <div class="fresh-team-card">
                <div class="fresh-team-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="fresh-team-content">
                    <div class="fresh-team-label">Team Members</div>
                    <div class="fresh-team-value"><?php echo $total_members; ?></div>
                    <div class="fresh-team-desc">Total team size</div>
                </div>
            </div>

            <div class="fresh-team-card">
                <div class="fresh-team-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="fresh-team-content">
                    <div class="fresh-team-label">Team Business</div>
                    <div class="fresh-team-value">$<?php echo round($user->teamb, 2); ?></div>
                    <div class="fresh-team-desc">Total team volume</div>
                </div>
            </div>

            <div class="fresh-team-card">
                <div class="fresh-team-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div class="fresh-team-content">
                    <div class="fresh-team-label">Direct Business</div>
                    <div class="fresh-team-value">$<?php echo round(get_sum('investments', 'amount', "uid IN (SELECT uid FROM user WHERE status = 0 AND topup>0 AND refer_id='" . $uid . "')") * 1, 2); ?></div>
                    <div class="fresh-team-desc">From your network</div>
                </div>
            </div>

            <div class="fresh-team-card">
                <div class="fresh-team-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="fresh-team-content">
                    <div class="fresh-team-label">Total Earnings</div>
                    <div class="fresh-team-value">$<?php echo round($total_in, 2); ?></div>
                    <div class="fresh-team-desc">All income combined</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fresh News Section -->
<div class="fresh-card">
    <div class="fresh-section-header">
        <h2 class="fresh-section-title">Latest News & Updates</h2>
        <i class="fas fa-newspaper fresh-section-icon"></i>
    </div>
    <div class="fresh-news-container" style="height: 300px; overflow: hidden; position: relative;">
        <div class="fresh-news-scroll" id="freshNewsScroll" style="animation: scrollNews 30s linear infinite;">
            <?php
            $news_res = my_query('SELECT title, description, datetime FROM cms WHERE mid=1 ORDER BY datetime DESC');
            while ($news = my_fetch_object($news_res)) {
                $date = new DateTime($news->datetime);
                ?>
                <div class="fresh-news-item" style="background: var(--bg-accent); border-radius: var(--radius); padding: 16px; margin-bottom: 16px; border: 1px solid var(--border);">
                    <div class="fresh-news-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <h4 style="color: var(--text-primary); font-size: 1rem; font-weight: 600; margin: 0; line-height: 1.4; flex: 1;"><?php echo htmlspecialchars($news->title); ?></h4>
                        <span style="color: var(--text-muted); font-size: 0.8rem; white-space: nowrap; margin-left: 16px;"><?php echo $date->format('M d, Y'); ?></span>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin: 0;"><?php echo htmlspecialchars($news->description); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<link href="./css/dashboard.css" rel="stylesheet" type="text/css" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const newsScroll = document.getElementById('newsScroll');

        // Clone news items for smooth infinite scroll
        newsScroll.innerHTML += newsScroll.innerHTML;

        // Handle animation reset
        const resetAnimation = () => {
            newsScroll.style.animation = 'none';
            newsScroll.offsetHeight; // Trigger reflow
            newsScroll.style.animation = null;
        };

        // Reset animation when it completes
        newsScroll.addEventListener('animationend', resetAnimation);

        // Touch device handling
        const newsContainer = document.querySelector('.news-container');
        let touchStartY = 0;
        let scrolling = false;

        newsContainer.addEventListener('touchstart', (e) => {
            touchStartY = e.touches[0].clientY;
            newsScroll.style.animationPlayState = 'paused';
        }, {
            passive: true
        });

        newsContainer.addEventListener('touchend', () => {
            if (!scrolling) {
                newsScroll.style.animationPlayState = 'running';
            }
        }, {
            passive: true
        });

        newsContainer.addEventListener('touchmove', (e) => {
            const touchY = e.touches[0].clientY;
            const diff = touchStartY - touchY;

            if (Math.abs(diff) > 5) {
                scrolling = true;
            }
        }, {
            passive: true
        });

        // Handle visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                newsScroll.style.animationPlayState = 'paused';
            } else {
                newsScroll.style.animationPlayState = 'running';
            }
        });

        // Performance optimization
        window.addEventListener('resize', () => {
            if (window.requestAnimationFrame) {
                window.requestAnimationFrame(resetAnimation);
            } else {
                setTimeout(resetAnimation, 66);
            }
        });
    });
</script>


<?php include_once 'footer.php'; ?>
<!-- Web3 and other scripts -->
<?php if (SITE_CURRENCY_ == 'BNB') { ?>
    <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
    <script type="text/javascript" src="../contract/bnb/index.js"></script>
    <script type="text/javascript" src="../contract/bnb/login.js"></script>
<?php } else { ?>
    <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
    <script src="../contract/eth/index.js"></script>
    <script src="../contract/eth/login.js"></script>
<?php } ?>
<!-- Copy to clipboard functionality -->
<script>
    function CopyToClipboard(containerid) {
        if (window.getSelection) {
            if (window.getSelection().empty) {
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {
            document.selection.empty();
        }

        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select().createTextRange();
            document.execCommand("Copy");
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().addRange(range);
            document.execCommand("Copy");
            $('#' + containerid + '_copy').text('Copied');
            $('#' + containerid + '_copy').addClass('bg-green');
            $('#' + containerid + '_copy').removeClass('bg-yellow');
        }
    }

    $("#coin_address_copy, #bitcoin_address_copy, #ltc_address_copy, #doge_address_copy, #left_link_copy, #right_link_copy").mouseleave(function() {
        $(this).text('Copy');
        $(this).addClass('bg-yellow');
        $(this).removeClass('bg-green');
    });
</script>

<!-- News Modal -->
<?php $hot_news = my_fetch_object(my_query('SELECT * FROM hot_news WHERE recid=1'));
if ($hot_news->image && $hot_news->status == 0) { ?>
    <div id="myModal-22" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="    background-color: #292929;">
                <div class="modal-header" style="    background-color: #292929;">
                    <h5 class="modal-title" style="color:#"><?php echo SITE_NAME; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo '<img src="../uploads/' . $hot_news->image . '" width="100%" />'; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" style="    background-color: #bd8e00;" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // $(window).load(function(){
        // $('#myModal-22').modal('show');
        // });
    </script>
<?php } ?>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Mobile App Functionality -->
<script>

document.addEventListener('DOMContentLoaded', function() {
    // Add active class to current navigation item
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (currentPath.includes(href)) {
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        }
    });

    // Add touch feedback
    const touchElements = document.querySelectorAll('.mobile-quick-action, .mobile-wallet-card, .nav-item');
    touchElements.forEach(el => {
        el.addEventListener('touchstart', function() {
            this.style.opacity = '0.7';
        });

        el.addEventListener('touchend', function() {
            this.style.opacity = '1';
        });

        el.addEventListener('touchcancel', function() {
            this.style.opacity = '1';
        });
    });
});
</script>