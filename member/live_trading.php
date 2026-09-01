<?php
$title = 'Trading Platform';
include_once 'header.php';

$check1 = my_query("SHOW COLUMNS FROM `admin` LIKE 'bot_liquidity'");
$check2 = my_query("SHOW COLUMNS FROM `admin` LIKE 'bot_profit'");

if (mysqli_num_rows($check1) == 0 || mysqli_num_rows($check2) == 0) {
    $query = 'ALTER TABLE `admin` 
              ADD COLUMN `bot_liquidity` INT NULL AFTER `otp`, 
              ADD COLUMN `bot_profit` INT NULL AFTER `bot_liquidity`';
    my_query($query);
}

$bot = mysqli_fetch_object(my_query('SELECT bot_liquidity, bot_profit FROM admin ORDER BY recid DESC LIMIT 1'));

// Check if user is logged in
if (!isset($user)) {
    header('Location: index.php');
    exit;
}

// Handle trading status update
if (isset($_POST['update_status'])) {
    $status = (int) $_POST['status'];

    // Ensure user has an active package before updating trade status
    if ($user->package > 0) {
        my_query("UPDATE user SET trade_status = $status, trade_status_updated_at = NOW() WHERE uid = " . (int) $user->uid);
        my_query("UPDATE investments SET trade_status = $status WHERE uid = " . $user->uid . '');

        setMessage('Trade activated', 'success');
    } else {
        setMessage('Kindly trade first to activate trading', 'error');
        redirect('./invest.php');
    }
}

// Helper function to detect AJAX request
function is_ajax_request()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Get user's trading status
$trade_active = $user->trade_status;
$trade_status_updated = $user->trade_status_updated_at;

// Get user's active investment with exchange pair
$active_investment_query = "SELECT exchange_pair, exchange_coin, ipid FROM investments 
                            WHERE uid = '$uid' AND is_closed = 0 
                            ORDER BY datetime DESC LIMIT 1";
$active_investment_result = my_query($active_investment_query);
$active_investment = mysqli_fetch_object($active_investment_result);

// Coin to Trading Pair mapping
$coin_mapping = [
    'bitcoin' => ['pair' => 'BTC/USDT', 'symbol' => 'BTCUSDT', 'name' => 'Bitcoin'],
    'ethereum' => ['pair' => 'ETH/USDT', 'symbol' => 'ETHUSDT', 'name' => 'Ethereum'],
    'binancecoin' => ['pair' => 'BNB/USDT', 'symbol' => 'BNBUSDT', 'name' => 'Binance Coin'],
    'ripple' => ['pair' => 'XRP/USDT', 'symbol' => 'XRPUSDT', 'name' => 'Ripple'],
    'cardano' => ['pair' => 'ADA/USDT', 'symbol' => 'ADAUSDT', 'name' => 'Cardano'],
    'solana' => ['pair' => 'SOL/USDT', 'symbol' => 'SOLUSDT', 'name' => 'Solana']
];

// Get trading pair based on selected coin
$has_valid_investment = false;
if ($active_investment && $active_investment->exchange_coin && isset($coin_mapping[$active_investment->exchange_coin])) {
    $trading_pair = $coin_mapping[$active_investment->exchange_coin]['pair'];
    $api_symbol = $coin_mapping[$active_investment->exchange_coin]['symbol'];
    $trading_coin = $coin_mapping[$active_investment->exchange_coin]['name'];
    $has_valid_investment = true;
} else {
    // Default to BTC/USDT if no active investment
    $trading_pair = 'BTC/USDT';
    $api_symbol = 'BTCUSDT';
    $trading_coin = 'Bitcoin';

    // Show message if no valid investment found
    if (!$active_investment) {
        // No active investment at all - will show message to invest
    } elseif (!$active_investment->exchange_coin) {
        // Old investment without coin data - use default but show notice
    }
}

$query = 'SELECT g.*, ip.title FROM income_growth as g'
    . ' LEFT JOIN investments as i ON i.recid=g.iid'
    . ' LEFT JOIN investments_plan as ip ON ip.recid=i.ipid'
    . " WHERE g.uid='" . $uid . "' AND g.type=0"
    . ' ORDER BY g.datetime DESC';
$result = mysqli_fetch_object(my_query($query));

$total_earnings_query = "SELECT SUM(amount) as total FROM income_growth WHERE uid='$uid' AND type=0";
$total_earnings_result = my_query($total_earnings_query);
$total_earnings_row = mysqli_fetch_object($total_earnings_result);
$total_earnings = $total_earnings_row->total ? $total_earnings_row->total : 0;

$today = date('Y-m-d');

$latest_query = "SELECT SUM(amount) as daily_amount
                 FROM income_growth 
                 WHERE uid='$uid' 
                   AND type=0 
                   AND DATE(datetime) = '$today'";

$latest_result = my_query($latest_query);
$latest_row = mysqli_fetch_object($latest_result);
$latest_amount = $latest_row && $latest_row->daily_amount ? $latest_row->daily_amount : 0;

$dailyroiamount = $latest_amount * 1;
$trade_active = $user->trade_status;
$start = strtotime($trade_status_updated);
$end = strtotime($today . ' 23:59:59');
$animation_duration = ($end - $start) * 10000000;  // Animation duration in milliseconds

$current = 0;
if ($trade_active) {
    if (date('Y-m-d', $start) == $today) {
        $current = (time() - $start) / max(1, $end - $start) * $dailyroiamount;
    } else {
        $current = $dailyroiamount;
    }
}

$active_orders_query = "SELECT COUNT(*) as active_count
                       FROM investments
                       WHERE status = 0
                       AND uid = '$uid'";
$active_result = my_query($active_orders_query);
$active_orders = mysqli_fetch_object($active_result)->active_count;
?>

<style>
    /* Base styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #0c0e14;
        color: #eaecef;
        padding: 16px;
        line-height: 1.6;
    }

    .container {
        width: 100%;
        padding: 0 16px;
        padding-left: 50px;
        margin: 0 auto;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #2a2e39;
    }

    .balance {
        font-size: 24px;
        font-weight: 600;
        color: #d4af37;
    }

    .content-header {
        position: relative;
        padding: 30px;
        margin-left: 15px !important;
        margin: 0 15px;
    }

    .status-badge {
        background: linear-gradient(135deg, #00d4aa 0%, #00b894 100%);
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 0 15px rgba(0, 212, 170, 0.4);
        animation: pulse 2s infinite;
    }

    .status-badge.inactive {
        background: linear-gradient(135deg, #f6465d 0%, #ee5a52 100%);
        box-shadow: 0 0 15px rgba(246, 70, 93, 0.4);
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 15px rgba(0, 212, 170, 0.4);
        }

        50% {
            box-shadow: 0 0 20px rgba(0, 212, 170, 0.7);
        }

        100% {
            box-shadow: 0 0 15px rgba(0, 212, 170, 0.4);
        }
    }

    .exchanges {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding: 10px 0;
        margin-bottom: 20px;
        scrollbar-width: none;
    }

    .exchanges::-webkit-scrollbar {
        display: none;
    }

    .exchange-card {
        background: #161824;
        padding: 8px 12px;
        border-radius: 8px;
        min-width: 80px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }

    .exchange-card:hover {
        transform: translateY(-5px);
    }

    .exchange-card.active {
        background: rgba(212, 175, 55, 0.15);
        border: 1px solid rgba(212, 175, 55, 0.3);
    }

    .exchange-name {
        font-size: 12px;
        margin-top: 4px;
    }

    .trading-pair {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pair-icon {
        width: 24px;
        height: 24px;
    }

    .trading-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .content-header {
            display: none;
        }

        .trading-layout {
            grid-template-columns: 1fr;
        }

        .container {
            padding-left: 0px !important;
        }
    }

    .order-book,
    .trade-history {
        background: #161824;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #2a2e39;
    }

    .order-row,
    .trade-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
        padding: 8px 0;
        border-bottom: 1px solid #242732;
        font-size: 14px;
    }

    .trade-row {
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
    }

    .order-row.header,
    .trade-row.header {
        font-weight: 600;
        color: #848e9c;
        border-bottom: 2px solid #2a2e39;
    }

    .price-up {
        color: #00d4aa;
    }

    .price-down {
        color: #f6465d;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .inactive-message {
        text-align: center;
        margin: 50px 0;
        font-size: 24px;
        color: #848e9c;
    }

    @media (max-width: 600px) {
        .stats {
            grid-template-columns: 1fr;
        }

        .inactive-message {
            margin: 0 !important;
            font-size: 20px !important;
        }
    }

    .stat-card {
        background: #161824;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .stat-title {
        font-size: 14px;
        color: #848e9c;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #d4af37;
    }

    .activation-container {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #2a2e39;
    }

    .activate-button {
        background: linear-gradient(135deg, #d4af37 0%, #d8a600 100%);
        color: #000;
        border: none;
        padding: 15px 40px;
        border-radius: 30px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        transition: all 0.3s ease;
    }

    .activate-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.6);
    }

    @keyframes flashGreen {
        0% {
            background-color: rgba(0, 212, 170, 0.1);
        }

        100% {
            background-color: transparent;
        }
    }

    @keyframes flashRed {
        0% {
            background-color: rgba(246, 70, 93, 0.1);
        }

        100% {
            background-color: transparent;
        }
    }

    .flash-buy {
        animation: flashGreen 0.5s ease;
    }

    .flash-sell {
        animation: flashRed 0.5s ease;
    }

    @keyframes floatUp {
        0% {
            transform: translateY(0);
            opacity: 1;
        }

        100% {
            transform: translateY(-30px);
            opacity: 0;
        }
    }

    .floating-profit {
        position: absolute;
        color: #00d4aa;
        font-weight: 600;
        pointer-events: none;
        z-index: 10;
        animation: floatUp 0.5s ease-out forwards;
    }

    .blink {
        background: linear-gradient(135deg, #f6465d 0%, #ee5a52 100%);
        color: #fff;
        font-weight: 600;
        font-size: 12px;
        border-radius: 5px;
        padding: 5px 10px;
        animation: blinkIndicator 1s infinite alternate;
        box-shadow: 0 0 10px rgba(246, 70, 93, 0.5);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    @keyframes blinkIndicator {
        from {
            opacity: 0.8;
            box-shadow: 0 0 10px rgba(246, 70, 93, 0.5);
        }

        to {
            opacity: 1;
            box-shadow: 0 0 15px rgba(246, 70, 93, 0.8);
        }
    }

    /* Header Content Styles */
    .header-content {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .trading-pair-display {
        color: #d4af37;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: baseline;
        gap: 5px;
    }

    .trading-pair-display .coin-name {
        font-size: 12px;
        color: #848e9c;
        font-weight: 400;
    }

    .investment-badge {
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .investment-badge.active {
        background: linear-gradient(135deg, #02c076 0%, #00b894 100%);
    }

    .investment-badge.demo {
        background: linear-gradient(135deg, #d4af37 0%, #cea000 100%);
    }

    /* Responsive styles for header section */
    @media (max-width: 768px) {
        .header-content {
            gap: 12px;
            flex-wrap: wrap;
        }

        .trading-pair-display {
            font-size: 16px;
        }

        .trading-pair-display .coin-name {
            font-size: 11px;
        }

        .investment-badge {
            padding: 5px 10px;
            font-size: 10px;
        }

        .blink {
            font-size: 11px;
            padding: 4px 8px;
        }
    }

    @media (max-width: 600px) {
        .header-content {
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .trading-pair-display {
            font-size: 14px;
            flex-wrap: wrap;
        }

        .trading-pair-display .coin-name {
            font-size: 10px;
        }

        .investment-badge {
            padding: 4px 8px;
            font-size: 9px;
            border-radius: 12px;
        }

        .blink {
            font-size: 10px;
            padding: 3px 6px;
        }

        .blink i {
            font-size: 6px !important;
        }
    }

    @media (min-width: 769px) {
        .header-content {
            gap: 20px;
        }

        .trading-pair-display {
            font-size: 18px;
        }

        .trading-pair-display .coin-name {
            font-size: 12px;
        }

        .investment-badge {
            padding: 6px 12px;
            font-size: 11px;
        }

        .blink {
            font-size: 12px;
            padding: 5px 10px;
        }
    }
</style>

<div class="container">
    <!-- Header Section -->
    <div class="header">
        <div class="header-content">
            <?php if ($trade_active) { ?>
                <div class="blink">
                    <i class="fas fa-circle" style="font-size: 8px;"></i> Live
                </div>
            <?php } ?>
            <div class="trading-pair-display">
                <?php echo $trading_pair; ?> 
                <span class="coin-name">(<?php echo $trading_coin; ?>)</span>
            </div>
            <?php if ($has_valid_investment): ?>
                <div class="investment-badge active">
                    <i class="fas fa-check-circle"></i> Active Investment
                </div>
            <?php else: ?>
                <div class="investment-badge demo">
                    <i class="fas fa-info-circle"></i> Demo Mode
                </div>
            <?php endif; ?>
        </div>
        <div class="status-badge inactive" id="status-badge">TRADING INACTIVE</div>
    </div>
    <!-- Stats Section -->
    <!-- <div class="stats">
        <div class="stat-card">
            <div class="stat-title">Trades Active Time</div>
            <div class="stat-value" id="trade-active-time">00:00:00</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Your Profit</div>
            <div class="stat-value"> + <?= number_format(get_sum('income_growth', 'amount', $uid), 2) ?? 0 ?> USDT</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Today Profit</div>
            <div class="stat-value" id="DailyProfit">+<?= number_format($current, 8) ?> USDT</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Bot Liquidity</div>
            <div class="stat-value">+<?= $bot->bot_liquidity ?? 0 ?> USDT</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Bot Profit</div>
            <div class="stat-value">+<?= $bot->bot_profit ?? 0 ?> USDT</div>
        </div>
    </div> -->
    <!-- Exchange Cards -->
    <div class="exchanges">
        <div class="exchange-card active">
            <div class="exchange-name">Binance</div>
        </div>
        <div class="exchange-card">
            <div class="exchange-name">Bybit</div>
        </div>
        <div class="exchange-card">
            <div class="exchange-name">KuCoin</div>
        </div>
        <div class="exchange-card">
            <div class="exchange-name">Coinbase</div>
        </div>
        <div class="exchange-card">
            <div class="exchange-name">OKX</div>
        </div>
    </div>

    <!-- Trading Sections (Hidden by Default) -->
    <div id="trading-sections" style="display: none;">
        <!-- Trading Pair -->
        <div class="trading-pair">
            <span><?php echo $trading_pair; ?></span>
            <span style="font-size: 14px; color: #848e9c;">(<?php echo $trading_coin; ?>)</span>
        </div>

        <!-- Trading Layout -->
        <div class="trading-layout">
            <!-- Order Book -->
            <div class="order-book">
                <div class="section-title">Order Book</div>
                <div class="order-row header">
                    <div>Coin</div>
                    <div>Pair</div>
                    <div>Price (USDT)</div>
                    <div>Amount</div>
                    <div>Total</div>
                </div>
                <div class="order-row" style="background: #1c1f2d; font-weight: 600;" id="Sell-price-row">
                    <div>-</div>
                    <div>-</div>
                    <div class="price-up">-</div>
                    <div>-</div>
                    <div>-</div>
                </div>
                <div id="order-book-asks"></div>
                <div class="order-row" style="background: #1c1f2d; font-weight: 600;" id="current-price-row">
                    <div>-</div>
                    <div>-</div>
                    <div class="price-up">-</div>
                    <div>-</div>
                    <div>-</div>
                </div>
                <div id="order-book-bids"></div>
            </div>

            <!-- Trade History -->
            <div class="trade-history">
                <div class="section-title">Trade History</div>
                <div class="trade-row header">
                    <div>Coin</div>
                    <div>Pair</div>
                    <div>Type</div>
                    <div>Price (USDT)</div>
                    <div>Amount</div>
                    <div>Time</div>
                </div>
                <div id="trade-history-list"></div>
            </div>
        </div>
    </div>

    <!-- Inactive Trading Message -->
    <div id="inactive-message" class="inactive-message">
        <?php if (!$has_valid_investment): ?>
            <div style="margin-bottom: 20px; color: #f59e0b;">
                <i class="fas fa-exclamation-triangle" style="font-size: 40px;"></i>
            </div>
            <div style="font-size: 20px; margin-bottom: 10px;">No Active Investment Found</div>
            <div style="font-size: 14px; color: #848e9c;">
                Please make an investment with Self-Trading or Bot Trading to start live trading.
            </div>
            <a href="trade.php" style="display: inline-block; margin-top: 20px; background: linear-gradient(135deg, #d4af37 0%, #d8a600 100%); color: #000; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-plus-circle"></i> Make Investment
            </a>
        <?php else: ?>
            Trading is currently inactive. Please activate trading to view details.
            <div class="activation-container" id="InactiveForm">
                <button class="activate-button" id="activate-trading">ACTIVATE TRADING</button>
            </div>
            <div class="activation-container" id="activForm" style="display: none;">
                <form action="" method="post">
                    <input type="hidden" name="status" id="trade-status" value="1">
                    <button class="activate-button" type="submit" name="update_status">ACTIVATE TRADING</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Trading Activation -->
</div>

<script>
    let exchanges = ["Binance", "Bybit"];
    let index = 0;
    let activeExchange = exchanges[index];

    setInterval(() => {
        index = (index + 1) % exchanges.length;
        activeExchange = exchanges[index];
    }, 500);

    // Global variables for caching trade data
    let lastBinanceTrades = [];
    let lastBybitTrades = [];
    let lastBinancePrice = 0;
    let lastBybitPrice = 0;
    let startWithBinance = true;

    // Function to toggle trading sections visibility
    function toggleTradingSections(isActive) {
        const tradingSections = document.getElementById('trading-sections');
        const inactiveMessage = document.getElementById('inactive-message');
        const statusBadge = document.getElementById('status-badge');
        const activateButton = document.getElementById('activate-trading');

        if (isActive) {
            tradingSections.style.display = 'block';
            inactiveMessage.style.display = 'none';
            statusBadge.textContent = 'TRADING ACTIVE';
            statusBadge.classList.remove('inactive');
            activateButton.textContent = 'DEACTIVATE TRADING';
        } else {
            tradingSections.style.display = 'none';
            inactiveMessage.style.display = 'block';
            statusBadge.textContent = 'TRADING INACTIVE';
            statusBadge.classList.add('inactive');
            activateButton.textContent = 'ACTIVATE TRADING';
        }
    }

    // Function to generate order book rows
    // function generateOrderBookRows(binanceAsks, binanceBids, bybitAsks, bybitBids, binancePrice, bybitPrice) {
    //     const asksContainer = document.getElementById('order-book-asks');
    //     const bidsContainer = document.getElementById('order-book-bids');
    //     const currentPriceRow = document.getElementById('current-price-row');
    //     const sellPriceRow = document.getElementById('Sell-price-row');

    //     asksContainer.innerHTML = '';
    //     bidsContainer.innerHTML = '';

    //     // Take top 10 asks and bids from each exchange and merge
    //     let allAsks = [
    //         ...binanceAsks.slice(0, 10).map(order => ({
    //             exchange: 'Binance',
    //             price: parseFloat(order[0]),
    //             amount: parseFloat(order[1])
    //         })),
    //         ...bybitAsks.slice(0, 10).map(order => ({
    //             exchange: 'Bybit',
    //             price: parseFloat(order[0]),
    //             amount: parseFloat(order[1])
    //         }))
    //     ].sort((a, b) => b.price - a.price); // Sort in descending order
    //     debugger
    //     let allBids = [
    //         ...binanceBids.slice(0, 10).map(order => ({
    //             exchange: 'Binance',
    //             price: parseFloat(order[0]),
    //             amount: parseFloat(order[1])
    //         })),
    //         ...bybitBids.slice(0, 10).map(order => ({
    //             exchange: 'Bybit',
    //             price: parseFloat(order[0]),
    //             amount: parseFloat(order[1])
    //         }))
    //     ].sort((a, b) => b.price - a.price); // Sort in descending order
    //     debugger

    //     // Display top 5 asks
    //     allAsks.slice(0, 10).forEach(order => {
    //         const row = document.createElement('div');
    //         row.className = 'order-row price-down';
    //         row.innerHTML = `
    //             <div>${order.exchange}</div>
    //             <div>BTC/USDT</div>
    //             <div>${order.price.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
    //             <div>${order.amount.toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
    //             <div>${(order.price * order.amount).toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
    //         `;
    //         asksContainer.appendChild(row);
    //     });

    //     // Display top 5 bids
    //     allBids.slice(0, 10).forEach(order => {
    //         const row = document.createElement('div');
    //         row.className = 'order-row price-up';
    //         row.innerHTML = `
    //             <div>${order.exchange}</div>
    //             <div>BTC/USDT</div>
    //             <div>${order.price.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
    //             <div>${order.amount.toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
    //             <div>${(order.price * order.amount).toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
    //         `;
    //         bidsContainer.appendChild(row);
    //     });

    //     // Update current and sell price rows
    //     currentPriceRow.innerHTML = `
    //         <div>Buy</div>
    //         <div></div>
    //         <div></div>
    //         <div>-</div>
    //         <div>-</div>
    //     `;
    //     sellPriceRow.innerHTML = `
    //         <div>Sell</div>
    //         <div></div>
    //         <div></div>
    //         <div>-</div>
    //         <div>-</div>
    //     `;
    // }



    function generateOrderBookRows(binanceAsks, binanceBids, bybitAsks, bybitBids, binancePrice, bybitPrice) {
    const asksContainer = document.getElementById('order-book-asks');
    const bidsContainer = document.getElementById('order-book-bids');
    const currentPriceRow = document.getElementById('current-price-row');
    const sellPriceRow = document.getElementById('Sell-price-row');

    asksContainer.innerHTML = '';
    bidsContainer.innerHTML = '';

    // Sort each exchange's asks and bids in descending order
    let sortedBinanceAsks = binanceAsks.slice(0, 10).map(order => ({
        exchange: 'Binance',
        price: parseFloat(order[0]),
        amount: parseFloat(order[1])
    })).sort((a, b) => b.price - a.price);

    let sortedBybitAsks = bybitAsks.slice(0, 10).map(order => ({
        exchange: 'Bybit',
        price: parseFloat(order[0]),
        amount: parseFloat(order[1])
    })).sort((a, b) => b.price - a.price);

    let sortedBinanceBids = binanceBids.slice(0, 10).map(order => ({
        exchange: 'Binance',
        price: parseFloat(order[0]),
        amount: parseFloat(order[1])
    })).sort((a, b) => b.price - a.price);

    let sortedBybitBids = bybitBids.slice(0, 10).map(order => ({
        exchange: 'Bybit',
        price: parseFloat(order[0]),
        amount: parseFloat(order[1])
    })).sort((a, b) => b.price - a.price);

    // Alternate asks (up to 5 rows)
    let allAsks = [];
    const maxAskRows = 5;
    for (let i = 0; i < maxAskRows && (sortedBinanceAsks[i] || sortedBybitAsks[i]); i++) {
        if (i % 2 === 0) {
            if (sortedBinanceAsks[i]) allAsks.push(sortedBinanceAsks[i]);
            if (sortedBybitAsks[i]) allAsks.push(sortedBybitAsks[i]);
        } else {
            if (sortedBybitAsks[i]) allAsks.push(sortedBybitAsks[i]);
            if (sortedBinanceAsks[i]) allAsks.push(sortedBinanceAsks[i]);
        }
    }

    // Alternate bids (up to 5 rows)
    let allBids = [];
    const maxBidRows = 5;
    for (let i = 0; i < maxBidRows && (sortedBinanceBids[i] || sortedBybitBids[i]); i++) {
        if (i % 2 === 0) {
            if (sortedBinanceBids[i]) allBids.push(sortedBinanceBids[i]);
            if (sortedBybitBids[i]) allBids.push(sortedBybitBids[i]);
        } else {
            if (sortedBybitBids[i]) allBids.push(sortedBybitBids[i]);
            if (sortedBinanceBids[i]) allBids.push(sortedBinanceBids[i]);
        }
    }

    // Display top 5 asks
    allAsks.slice(0, maxAskRows).forEach(order => {
        const row = document.createElement('div');
        row.className = 'order-row price-down';
        row.innerHTML = `
            <div><?php echo $trading_coin; ?></div>
            <div><?php echo $trading_pair; ?></div>
            <div>${order.price.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
            <div>${order.amount.toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
            <div>${(order.price * order.amount).toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
        `;
        asksContainer.appendChild(row);
    });

    // Display top 5 bids
    allBids.slice(0, maxBidRows).forEach(order => {
        const row = document.createElement('div');
        row.className = 'order-row price-up';
        row.innerHTML = `
            <div><?php echo $trading_coin; ?></div>
            <div><?php echo $trading_pair; ?></div>
            <div>${order.price.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
            <div>${order.amount.toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
            <div>${(order.price * order.amount).toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
        `;
        bidsContainer.appendChild(row);
    });

    // Update current and sell price rows
    currentPriceRow.innerHTML = `
        <div>Buy</div>
        <div></div>
        <div></div>
        <div>-</div>
        <div>-</div>
    `;
    sellPriceRow.innerHTML = `
        <div>Sell</div>
        <div></div>
        <div></div>
        <div>-</div>
        <div>-</div>
    `;
}

    // Function to generate trade history rows
    function generateTradeHistoryRows(binanceTrades, bybitTrades, binancePrice, bybitPrice, startWithBinance) {
        const tradeList = document.getElementById('trade-history-list');
        tradeList.innerHTML = '';

        // Determine trade sides based on price comparison
        const bybitTradeSide = bybitPrice > binancePrice ? 'sell' : 'buy';
        const binanceTradeSide = bybitPrice > binancePrice ? 'buy' : 'sell';

        // Combine trades alternately, up to 12 rows
        const maxRows = 12;
        const combinedTrades = [];
        const minTrades = Math.min(binanceTrades.length, bybitTrades.length, Math.ceil(maxRows / 2));

        for (let i = 0; i < minTrades && combinedTrades.length < maxRows; i++) {
            if (startWithBinance) {
                if (binanceTrades[i]) {
                    combinedTrades.push({
                        exchange: 'Binance',
                        side: binanceTradeSide,
                        price: binanceTrades[i].price || binancePrice,
                        size: binanceTrades[i].size,
                        time: binanceTrades[i].time
                    });
                }
                if (bybitTrades[i]) {
                    combinedTrades.push({
                        exchange: 'Bybit',
                        side: bybitTradeSide,
                        price: bybitTrades[i].price || bybitPrice,
                        size: bybitTrades[i].size,
                        time: bybitTrades[i].time
                    });
                }
            } else {
                if (bybitTrades[i]) {
                    combinedTrades.push({
                        exchange: 'Bybit',
                        side: bybitTradeSide,
                        price: bybitTrades[i].price || bybitPrice,
                        size: bybitTrades[i].size,
                        time: bybitTrades[i].time
                    });
                }
                if (binanceTrades[i]) {
                    combinedTrades.push({
                        exchange: 'Binance',
                        side: binanceTradeSide,
                        price: binanceTrades[i].price || binancePrice,
                        size: binanceTrades[i].size,
                        time: binanceTrades[i].time
                    });
                }
            }
        }

        // Render combined trades
        combinedTrades.slice(0, maxRows).forEach(trade => {
            const row = document.createElement('div');
            row.className = `trade-row ${trade.side === 'buy' ? 'price-up' : 'price-down'}`;
            // Ensure valid time formatting
            let tradeTime = new Date(parseInt(trade.time));
            let formattedTime = isNaN(tradeTime.getTime()) ? 'Invalid Time' : tradeTime.toLocaleTimeString();
            row.innerHTML = `
                <div><?php echo $trading_coin; ?></div>
                <div><?php echo $trading_pair; ?></div>
                <div>${trade.side.toUpperCase()}</div>
                <div>${parseFloat(trade.price).toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
                <div>${parseFloat(trade.size).toLocaleString(undefined, { minimumFractionDigits: 5 })}</div>
                <div>${formattedTime}</div>
            `;
            tradeList.appendChild(row);
        });
    }

    // API Integration Functions
    async function fetchBinanceData() {
        const apiSymbol = '<?php echo $api_symbol; ?>';
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);

            const [priceResponse, orderBookResponse, tradesResponse] = await Promise.all([
                fetch(`https://api.binance.com/api/v3/ticker/price?symbol=${apiSymbol}`, {
                    signal: controller.signal
                }),
                fetch(`https://api.binance.com/api/v3/depth?symbol=${apiSymbol}&limit=10`, {
                    signal: controller.signal
                }),
                fetch(`https://api.binance.com/api/v3/trades?symbol=${apiSymbol}&limit=10`, {
                    signal: controller.signal
                })
            ]);

            clearTimeout(timeoutId);

            if (!priceResponse.ok || !orderBookResponse.ok || !tradesResponse.ok) {
                console.warn(`Binance API error for ${apiSymbol}:`, {
                    priceStatus: priceResponse.status,
                    orderBookStatus: orderBookResponse.status,
                    tradesStatus: tradesResponse.status
                });
                throw new Error(`HTTP error: ${priceResponse.status}, ${orderBookResponse.status}, ${tradesResponse.status}`);
            }

            const priceData = await priceResponse.json();
            const orderBookData = await orderBookResponse.json();
            const tradesData = await tradesResponse.json();

            console.log('Binance data fetched successfully for', apiSymbol);
            
            return {
                currentPrice: priceData.price || '0',
                asks: orderBookData.asks || [],
                bids: orderBookData.bids || [],
                trades: tradesData.map(trade => ({
                    side: trade.isBuyerMaker ? 'buy' : 'sell',
                    price: trade.price,
                    size: trade.qty,
                    time: trade.time
                })) || []
            };
        } catch (error) {
            console.error('Error fetching Binance data for', apiSymbol, ':', error);
            return {
                currentPrice: '0',
                asks: [],
                bids: [],
                trades: []
            };
        }
    }

    async function fetchBybitData() {
        const apiSymbol = '<?php echo $api_symbol; ?>';
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);

            const [priceResponse, orderBookResponse, tradesResponse] = await Promise.all([
                fetch(`https://api.bybit.com/v5/market/tickers?category=linear&symbol=${apiSymbol}`, {
                    signal: controller.signal
                }),
                fetch(`https://api.bybit.com/v5/market/orderbook?category=linear&symbol=${apiSymbol}&limit=10`, {
                    signal: controller.signal
                }),
                fetch(`https://api.bybit.com/v5/market/recent-trade?category=linear&symbol=${apiSymbol}&limit=10`, {
                    signal: controller.signal
                })
            ]);

            clearTimeout(timeoutId);

            if (!priceResponse.ok || !orderBookResponse.ok || !tradesResponse.ok) {
                console.warn(`Bybit API error for ${apiSymbol}:`, {
                    priceStatus: priceResponse.status,
                    orderBookStatus: orderBookResponse.status,
                    tradesStatus: tradesResponse.status
                });
                throw new Error(`HTTP error: ${priceResponse.status}, ${orderBookResponse.status}, ${tradesResponse.status}`);
            }

            const priceData = await priceResponse.json();
            const orderBookData = await orderBookResponse.json();
            const tradesData = await tradesResponse.json();

            if (!priceData.result || !orderBookData.result || !tradesData.result) {
                console.warn('Invalid Bybit API response structure for', apiSymbol);
                throw new Error('Invalid Bybit API response structure');
            }

            console.log('Bybit data fetched successfully for', apiSymbol);
            
            return {
                currentPrice: priceData.result.list[0]?.lastPrice || '0',
                asks: orderBookData.result.a || [],
                bids: orderBookData.result.b || [],
                trades: tradesData.result.list?.map(trade => ({
                    side: trade.side.toLowerCase(),
                    price: trade.price,
                    size: trade.size,
                    time: parseInt(trade.time) // Ensure time is parsed as integer
                })) || []
            };
        } catch (error) {
            console.error('Error fetching Bybit data for', apiSymbol, ':', error);
            return {
                currentPrice: '0',
                asks: [],
                bids: [],
                trades: []
            };
        }
    }

    // Function to simulate live trading updates
    async function simulateLiveTrading() {
        async function updateData() {
            if (document.getElementById('status-badge').textContent !== 'TRADING ACTIVE') return;

            const binanceData = await fetchBinanceData();
            const bybitData = await fetchBybitData();

            // Update cached data
            lastBinanceTrades = binanceData.trades;
            lastBybitTrades = bybitData.trades;
            lastBinancePrice = parseFloat(binanceData.currentPrice) || 0;
            lastBybitPrice = parseFloat(bybitData.currentPrice) || 0;

            // Always display merged order book data
            generateOrderBookRows(binanceData.asks, binanceData.bids, bybitData.asks, bybitData.bids, lastBinancePrice, lastBybitPrice);
            // Render trade history
            generateTradeHistoryRows(lastBinanceTrades, lastBybitTrades, lastBinancePrice, lastBybitPrice, startWithBinance);
        }

        setInterval(updateData, 3000);

        // Separate interval for alternating trade history positions every 2s
        setInterval(() => {
            if (document.getElementById('status-badge').textContent === 'TRADING ACTIVE' && lastBinanceTrades.length > 0 && lastBybitTrades.length > 0) {
                startWithBinance = !startWithBinance;
                generateTradeHistoryRows(lastBinanceTrades, lastBybitTrades, lastBinancePrice, lastBybitPrice, startWithBinance);
            }
        }, 2000);
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Log current trading configuration for debugging
        console.log('Trading Configuration:', {
            pair: '<?php echo $trading_pair; ?>',
            symbol: '<?php echo $api_symbol; ?>',
            coin: '<?php echo $trading_coin; ?>'
        });
        
        toggleTradingSections(false);
        simulateLiveTrading();

        document.getElementById('activate-trading').addEventListener('click', function() {
            const isActive = <?= $trade_active ?>;
            if (isActive) {
                window.location.href = '?update_status=1&status=0';
            } else {
                document.getElementById('activForm').style.display = 'block';
                document.getElementById('InactiveForm').style.display = 'none';
            }
        });

        const isActive = <?= $trade_active ?>;
        if (isActive == 0) {
            document.getElementById('activForm').style.display = 'block';
            document.getElementById('status-badge').textContent = 'TRADING INACTIVE';
            document.getElementById('InactiveForm').style.display = 'none';
        } else {
            document.getElementById('InactiveForm').style.display = 'none';
        }
        toggleTradingSections(isActive);

        document.querySelectorAll('.exchange-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.exchange-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                activeExchange = this.querySelector('.exchange-name').textContent;
            });
        });
    });
</script>

<script>
    let isTradingActive = <?php echo $user->trade_status == 1 ? 'true' : 'false'; ?>;
    let startTimeStr = "<?php echo date('H:i:s', strtotime($user->trade_status_updated_at)); ?>"; // only time "HH:MM:SS"

    // Convert HH:MM:SS to total seconds
    function parseTimeToSeconds(timeStr) {
        let parts = timeStr.split(':');
        return (+parts[0]) * 3600 + (+parts[1]) * 60 + (+parts[2]);
    }

    // Convert total seconds to HH:MM:SS
    function formatSecondsToTime(seconds) {
        let h = String(Math.floor(seconds / 3600)).padStart(2, '0');
        let m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
        let s = String(seconds % 60).padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    function startTradeTimer() {
        const display = document.getElementById("trade-active-time");
        if (!isTradingActive) {
            display.textContent = "00:00:00";
            return;
        }

        let startSeconds = parseTimeToSeconds(startTimeStr);

        setInterval(() => {
            let now = new Date();
            let nowSeconds = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

            let diff = nowSeconds - startSeconds;

            // Reset counter if it reaches 23:59:59
            if (diff >= 24 * 3600 || diff < 0) {
                diff = 0;
                startSeconds = 0; // Start count from next day
            }

            display.textContent = formatSecondsToTime(diff);
        }, 1000);
    }

    startTradeTimer();
</script>
<?php if ($trade_active): ?>
    <script>
        function animate(id, start, end, dur = 2000) {
            let el = document.getElementById(id),
                s = +start,
                e = +end,
                d = dur,
                st = Date.now();
            let t = setInterval(() => {
                let p = Math.min((Date.now() - st) / d, 1);
                el.innerHTML = "+" + (s + (e - s) * p).toFixed(8) + " USDT";
                if (p >= 1) clearInterval(t);
            }, 30);
        }
        animate("DailyProfit", <?= round($current, 8) ?>, <?= $dailyroiamount ?>, <?= $animation_duration ?>);
    </script>
<?php endif; ?>
<?php include_once 'footer.php'; ?>