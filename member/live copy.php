<?php
$title = 'Trading Platform';
include 'header.php';

// Check if user is logged in
if (!isset($user)) {
    header("Location: index.php");
    exit;
}

// Handle trading status update
if (isset($_POST['update_status'])) {
    $status = (int)$_POST['status'];

    // Ensure user has an active package before updating trade status
    if ($user->package > 0) {
        my_query("UPDATE user SET trade_status = $status, trade_status_updated_at = NOW() WHERE uid = " . (int)$user->uid);
        my_query("UPDATE investments SET trade_status = $status WHERE uid = " . (int)$user->uid);

        // Return success for AJAX request
        if (is_ajax_request()) {
            // echo json_encode(['success' => true, 'message' => 'Trading status updated successfully']);
            // exit;
            setMessage('Trade activated', 'success');
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        setMessage('Kindly trade first to activate trading', 'error');
        redirect('./invest.php');
        // Block update if user has no package
        if (is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Kindly trade first to activate trading']);
            exit;
        }
    }
}


// if (isset($_POST['update_status'])) {
//     $status = intval($_POST['status']);
//     if ($user['package'] > 0) {
//         // DB update
//         // update_data('user', array('trade_status' => $status), "id='$uid'");
//         // update_data('investments', array('trade_status' => $status), "uid='$uid'");
//         my_query("UPDATE user SET trade_status = $status, trade_status_updated_at = NOW() WHERE uid = " . (int)$user->uid);
//         my_query("UPDATE investments SET trade_status = $status WHERE uid = " . (int)$user->uid);
//         if (is_ajax_request()) {
//             echo json_encode(['success' => true, 'message' => 'Trading status updated']);
//             exit;
//         }
//     } else {
//         if (is_ajax_request()) {
//             echo json_encode(['success' => false, 'message' => 'Please purchase a package first']);
//             exit;
//         } else {
//             $_SESSION['error'] = 'Please purchase a package first to activate trading';
//             header("Location: invest.php");
//             exit;
//         }
//     }
// }



// Helper function to detect AJAX request
function is_ajax_request()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Get user's trading status
$trade_active = $user->trade_status;
$trade_status_updated = $user->trade_status_updated_at;

$query = "SELECT g.*, ip.title FROM income_growth as g"
    . " LEFT JOIN investments as i ON i.recid=g.iid"
    . " LEFT JOIN investments_plan as ip ON ip.recid=i.ipid"
    . " WHERE g.uid='" . $uid . "' AND g.type=0"
    . " ORDER BY g.datetime DESC";
$result = mysqli_fetch_object(my_query($query));

$total_earnings_query = "SELECT SUM(amount) as total FROM income_growth WHERE uid='$uid' AND type=0";
$total_earnings_result = my_query($total_earnings_query);
$total_earnings_row = mysqli_fetch_object($total_earnings_result);
$total_earnings = $total_earnings_row->total ? $total_earnings_row->total : 0;

$today = date("Y-m-d");

$latest_query = "SELECT SUM(amount) as daily_amount FROM income_growth WHERE uid='$uid' AND type=0 AND DATE(datetime) = '$today'";
$latest_result = my_query($latest_query);
$latest_row = mysqli_fetch_object($latest_result);
$latest_amount = $latest_row && $latest_row->daily_amount ? $latest_row->daily_amount : 0;

$dailyroiamount = $latest_amount * 1;
$trade_active = $user->trade_status;
$start = strtotime($trade_status_updated);
$end = strtotime($today . " 23:59:59");
$animation_duration = ($end - $start) * 1000; // Animation duration in milliseconds
$current = 0;
if ($trade_active) {
    if (date("Y-m-d", $start) == $today) {
        $current = (time() - $start) / max(1, $end - $start) * $dailyroiamount;
    } else {
        $current = $dailyroiamount;
    }
}

$active_orders_query = "SELECT COUNT(*) as active_count FROM investments WHERE status = 0 AND uid = '$uid'";
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
        color: #f0b90b;
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
        padding: 15px;
        border-radius: 12px;
        min-width: 120px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }

    .exchange-card:hover {
        transform: translateY(-5px);
    }

    .exchange-card.active {
        background: rgba(240, 185, 11, 0.15);
        border: 1px solid rgba(240, 185, 11, 0.3);
    }

    .exchange-name {
        font-size: 14px;
        margin-top: 8px;
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
        grid-template-columns: 1fr 1fr 1fr;
        padding: 8px 0;
        border-bottom: 1px solid #242732;
        font-size: 14px;
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
        color: #f0b90b;
    }

    .activation-container {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #2a2e39;
    }

    .activate-button {
        background: linear-gradient(135deg, #f0b90b 0%, #d8a600 100%);
        color: #000;
        border: none;
        padding: 15px 40px;
        border-radius: 30px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(240, 185, 11, 0.4);
        transition: all 0.3s ease;
    }

    .activate-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(240, 185, 11, 0.6);
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
</style>

<div class="container">
    <div class="header">
        <div class="balance">Live</div>
        <div class="status-badge" id="status-badge">TRADING INACTIVE</div>
    </div>
    <div class="stats">
        <div class="stat-card">
            <div class="stat-title">Total Profit</div>
            <div class="stat-value">+<?= number_format($total_earnings, 2) ?> USDT</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Active Trades</div>
            <div class="stat-value"><?= $active_orders ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Trade Profit</div>
            <div class="stat-value" id="DailyProfit">+<?= number_format($current, 8) ?> USDT</div>
        </div>
    </div>
    <div class="exchanges">
        <div class="exchange-card active" data-exchange="binance">
            <div class="exchange-name">KuCoin</div>
        </div>
        <div class="exchange-card" data-exchange="bybit">
            <div class="exchange-name">Coinbase</div>
        </div>
        <div class="exchange-card" data-exchange="binance">
            <div class="exchange-name">Crypto.com</div>
        </div>
        <div class="exchange-card" data-exchange="binance">
            <div class="exchange-name">OKX</div>
        </div>
        <div class="exchange-card" data-exchange="binance">
            <div class="exchange-name">Gate.io</div>
        </div>
    </div>
    <!-- <div id="trading-sections" style="display: none;"> -->
    <div id="trading-sections">
        <div class="trading-pair">
            <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.svg" class="pair-icon" alt="BTC">
            <span>BTC/USDT</span>
        </div>
        <div class="trading-layout">
            <div class="order-book">
                <div class="section-title">Order Book</div>
                <div class="order-row header">
                    <div>Price (USDT)</div>
                    <div>Amount</div>
                    <div>Total</div>
                </div>
                <div class="order-row" style="background: #1c1f2d; font-weight: 600;">
                    <div>Buy</div>
                </div>
                <div id="order-book-bids"></div>
                <div class="order-row">
                    <div>Sell</div>
                </div>
                <div id="order-book-asks"></div>
            </div>
            <div class="trade-history">
                <div class="section-title">Trade History</div>
                <div class="trade-row header">
                    <div>Type</div>
                    <div>Amount</div>
                    <div>Time</div>
                </div>
                <div id="trade-history-list"></div>
            </div>
        </div>
    </div>
    <div id="inactive-message" class="inactive-message" style="display: none;">
        Trading is currently inactive. Please activate trading to view details.
    </div>
    <div class="activation-container" id="hide-button">
        <form action="" method="post">
            <input type="hidden" name="status" id="trade-status" value="<?= $trade_active ? '0' : '1' ?>">
            <button class="activate-button" type="submit" name="update_status">ACTIVATE TRADING</button>
            <!-- <button class="activate-button" id="activate-trading">ACTIVATE TRADING</button> -->
        </form>
    </div>
</div>


<script>
    let isActive = <?= $trade_active ?>;
    // let isActive = <?= json_encode((bool)$trade_active) ?>;

    console.log('Initial trading status:', isActive);
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


    // function toggleTradingSections(isActive) {
    //     const tradingSections = document.querySelectorAll('.trading-layout, .stats-container, .exchange-selection');
    //     const inactiveMessage = document.getElementById('inactive-message');
    //     const statusBadge = document.getElementById('status-badge');
    //     const activateButton = document.getElementById('activate-trading');
    //     const dailyProfit = document.getElementById('DailyProfit');
    //     const totalProfit = document.getElementById('TotalProfit');
    //     const profitAmount = document.querySelector('.profit-amount');

    //     if (isActive) {
    //         // ✅ Show trading UI
    //         tradingSections.forEach(section => section.style.display = 'grid');
    //         if (inactiveMessage) inactiveMessage.style.display = 'none';

    //         // ✅ Status badge green
    //         if (statusBadge) {
    //             statusBadge.textContent = 'TRADING ACTIVE';
    //             statusBadge.className = 'status-badge active';
    //         }

    //         // ✅ Button change
    //         if (activateButton) activateButton.textContent = 'DEACTIVATE TRADING';

    //         // ✅ Profit section show
    //         if (profitAmount) profitAmount.style.display = 'block';

    //         // ✅ Animation trigger (agar aap chahte ho)
    //         if (dailyProfit && totalProfit) {
    //             const start = parseFloat(totalProfit.textContent) || 0;
    //             const end = start + parseFloat(dailyProfit.textContent) || 0;
    //             animateProfit(start, end, totalProfit);
    //         }
    //     } else {
    //         // ❌ Hide trading UI
    //         tradingSections.forEach(section => section.style.display = 'none');
    //         if (inactiveMessage) inactiveMessage.style.display = 'block';

    //         // ❌ Status badge red
    //         if (statusBadge) {
    //             statusBadge.textContent = 'TRADING INACTIVE';
    //             statusBadge.className = 'status-badge inactive';
    //         }

    //         // ❌ Button change
    //         if (activateButton) activateButton.textContent = 'ACTIVATE TRADING';

    //         // ❌ Profit section hide
    //         if (profitAmount) profitAmount.style.display = 'none';
    //     }
    // }


    // function toggleTradingSections(isActive) {
    //     const tradingSections = document.querySelectorAll('.trading-layout, .stats-container, .exchange-selection');
    //     const inactiveMessage = document.getElementById('inactive-message');
    //     const statusBadge = document.getElementById('status-badge');
    //     const activateButton = document.getElementsByClassName('activation-container');
    //     const dailyProfit = document.getElementById('DailyProfit');
    //     const totalProfit = document.getElementById('TotalProfit');
    //     const profitAmount = document.querySelector('.profit-amount');
    //     document.getElementById('hide-button').style.display = isActive ? 'none' : 'block';
    //     console.log('Toggling trading sections. isActive:', isActive, 'Activate Button:', activateButton);
    
    //     // if (isActive) {
    //     //     // ✅ Show trading UI
    //     //     tradingSections.forEach(section => section.style.display = 'grid');
    //     //     if (inactiveMessage) inactiveMessage.style.display = 'none';

    //     //     // ✅ Status badge green
    //     //     if (statusBadge) {
    //     //         statusBadge.textContent = 'TRADING ACTIVE';
    //     //         statusBadge.className = 'status-badge active';
    //     //     }

    //     //     // ✅ Button change
    //     //     // if (activateButton) activateButton.textContent = 'DEACTIVATE TRADING';
            
    //     //         // 
    //     //         activateButton.style.display = 'none';
    //     //         console.log("activation container",activateButton);

    //     //     // ✅ Profit section show
    //     //     if (profitAmount) profitAmount.style.display = 'block';

    //     //     // ✅ Animation trigger
    //     //     if (dailyProfit && totalProfit) {
    //     //         const start = parseFloat(totalProfit.textContent) || 0;
    //     //         const end = start + (parseFloat(dailyProfit.textContent) || 0);
    //     //         animateProfit(start, end, totalProfit);
    //     //     }
    //     // } else {
    //     //     // ❌ Hide trading UI
    //     //     tradingSections.forEach(section => section.style.display = 'none');
    //     //     if (inactiveMessage) inactiveMessage.style.display = 'block';

    //     //     // ❌ Status badge red
    //     //     if (statusBadge) {
    //     //         statusBadge.textContent = 'TRADING INACTIVE';
    //     //         statusBadge.className = 'status-badge inactive';
    //     //     }

    //     //     // ❌ Button change
    //     //     if (activateButton) activateButton.textContent = 'ACTIVATE TRADING';

    //     //     // ❌ Profit section hide
    //     //     if (profitAmount) profitAmount.style.display = 'none';
    //     // }
      
    // }

    // 🔹 Page load ke time PHP value se initialize karo
    // document.addEventListener("DOMContentLoaded", function() {
    //     let isActive = <?= $trade_active == 1 ? 'true' : 'false' ?>;
    //     toggleTradingSections(isActive);
    // });



    function animateProfit(start, end, element) {
        let current = start;
        const increment = (end - start) / 100; // smooth step
        const interval = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(interval);
            }
            element.textContent = current.toFixed(2);
        }, 30);
    }


    // Function to generate order book rows
    function generateOrderBookRows(orders) {
        const asksContainer = document.getElementById('order-book-asks');
        const bidsContainer = document.getElementById('order-book-bids');

        asksContainer.innerHTML = '';
        bidsContainer.innerHTML = '';

        orders.asks.slice(0, 4).forEach(order => {
            const row = document.createElement('div');
            row.className = 'order-row price-down';
            row.innerHTML = `
    <div>${parseFloat(order.price).toLocaleString()}</div>
    <div>${parseFloat(order.amount).toLocaleString()}</div>
    <div>${parseFloat(order.total).toLocaleString()}</div>
    `;
            asksContainer.appendChild(row);
        });

        orders.bids.slice(0, 4).forEach(order => {
            const row = document.createElement('div');
            row.className = 'order-row price-up';
            row.innerHTML = `
    <div>${parseFloat(order.price).toLocaleString()}</div>
    <div>${parseFloat(order.amount).toLocaleString()}</div>
    <div>${parseFloat(order.total).toLocaleString()}</div>
    `;
            bidsContainer.appendChild(row);
        });
    }

    // Function to generate trade history rows
    function generateTradeHistoryRows(trades) {
        const tradeList = document.getElementById('trade-history-list');
        tradeList.innerHTML = '';

        trades.slice(0, 6).forEach(trade => {
            const row = document.createElement('div');
            row.className = `trade-row ${trade.type === 'BUY' ? 'price-up' : 'price-down'}`;
            row.innerHTML = `
    <div>${trade.type}</div>
    <div>${parseFloat(trade.amount).toLocaleString()}</div>
    <div>${trade.time}</div>
    `;
            tradeList.appendChild(row);
        });
    }

    // API Integration Functions
    async function fetchBinanceData() {
        try {
            // Fetch order book
            const orderBookResponse = await fetch('https://api.binance.com/api/v3/depth?symbol=BTCUSDT');
            const orderBookData = await orderBookResponse.json();
            // Fetch trade history
            const tradesResponse = await fetch('https://api.binance.com/api/v3/trades?symbol=BTCUSDT');
            const tradesData = await tradesResponse.json();

            const orders = {
                asks: orderBookData.asks.map(([price, amount]) => ({
                    price: parseFloat(price),
                    amount: parseFloat(amount),
                    total: parseFloat(price) * parseFloat(amount)
                })),
                bids: orderBookData.bids.map(([price, amount]) => ({
                    price: parseFloat(price),
                    amount: parseFloat(amount),
                    total: parseFloat(price) * parseFloat(amount)
                }))
            };

            const trades = tradesData.map(trade => ({
                type: trade.isBuyerMaker ? 'BUY' : 'SELL',
                price: parseFloat(trade.price),
                amount: parseFloat(trade.qty),
                time: new Date(trade.time).toLocaleTimeString()
            }));

            return {
                orders,
                trades
            };
        } catch (error) {
            console.error('Error fetching Binance data:', error);
            return null;
        }
    }

    async function fetchBybitData() {
        try {
            // Fetch order book
            const orderBookResponse = await fetch('https://api.bybit.com/v5/market/orderbook?category=spot&symbol=BTCUSDT');
            const orderBookData = await orderBookResponse.json();
            // Fetch trade history
            const tradesResponse = await fetch('https://api.bybit.com/v5/market/recent-trade?category=spot&symbol=BTCUSDT');
            const tradesData = await tradesResponse.json();

            if (orderBookData.retCode !== 0 || tradesData.retCode !== 0) {
                throw new Error('Bybit API error');
            }

            const orders = {
                asks: orderBookData.result.s.a.map(([price, amount]) => ({
                    price: parseFloat(price),
                    amount: parseFloat(amount),
                    total: parseFloat(price) * parseFloat(amount)
                })),
                bids: orderBookData.result.s.b.map(([price, amount]) => ({
                    price: parseFloat(price),
                    amount: parseFloat(amount),
                    total: parseFloat(price) * parseFloat(amount)
                }))
            };

            const trades = tradesData.result.list.map(trade => ({
                type: trade.side === 'Buy' ? 'BUY' : 'SELL',
                price: parseFloat(trade.price),
                amount: parseFloat(trade.size),
                time: new Date(parseInt(trade.time)).toLocaleTimeString()
            }));

            return {
                orders,
                trades
            };
        } catch (error) {
            console.error('Error fetching Bybit data:', error);
            return null;
        }
    }

    // Function to fetch data based on active exchange
    async function fetchExchangeData() {
        const activeExchange = document.querySelector('.exchange-card.active').dataset.exchange;
        let data;
        if (activeExchange === 'bybit') {
            data = await fetchBybitData();
        } else {
            data = await fetchBinanceData();
        }

        if (data) {
            generateOrderBookRows(data.orders);
            generateTradeHistoryRows(data.trades);
            return data;
        }
        return null;
    }

    // Function to simulate live trading updates
    function simulateLiveTrading() {
        setInterval(async () => {
            if (document.getElementById('status-badge').textContent === 'TRADING ACTIVE') {
                const data = await fetchExchangeData();
                if (!data) return;

                // Simulate order book updates
                if (Math.random() > 0.5 && data.orders.asks.length > 0) {
                    const index = Math.floor(Math.random() * data.orders.asks.length);
                    data.orders.asks[index].amount = (Math.random() * 2).toFixed(4);
                    data.orders.asks[index].total = (data.orders.asks[index].price * data.orders.asks[index].amount).toFixed(4);
                    document.querySelectorAll('.order-row')[index].classList.add('flash-sell');
                    setTimeout(() => {
                        document.querySelectorAll('.order-row')[index].classList.remove('flash-sell');
                    }, 500);
                }

                if (Math.random() > 0.5 && data.orders.bids.length > 0) {
                    const index = Math.floor(Math.random() * data.orders.bids.length);
                    data.orders.bids[index].amount = (Math.random() * 2).toFixed(4);
                    data.orders.bids[index].total = (data.orders.bids[index].price * data.orders.bids[index].amount).toFixed(4);
                    document.querySelectorAll('.order-row')[data.orders.asks.length + 1 + index].classList.add('flash-buy');
                    setTimeout(() => {
                        document.querySelectorAll('.order-row')[data.orders.asks.length + 1 + index].classList.remove('flash-buy');
                    }, 500);
                }

                generateOrderBookRows(data.orders);

                // Simulate trade updates
                const types = ['BUY', 'SELL'];
                const newTrade = {
                    type: types[Math.floor(Math.random() * types.length)],
                    price: 113390 + Math.random() * 10,
                    amount: (Math.random() * 2).toFixed(5),
                    time: new Date().toLocaleTimeString()
                };
                data.trades.unshift(newTrade);
                if (data.trades.length > 20) {
                    data.trades.pop();
                }
                generateTradeHistoryRows(data.trades);

                if (document.querySelectorAll('.trade-row').length > 1) {
                    document.querySelectorAll('.trade-row')[1].classList.add(newTrade.type === 'BUY' ? 'flash-buy' : 'flash-sell');
                    setTimeout(() => {
                        document.querySelectorAll('.trade-row')[1].classList.remove(newTrade.type === 'BUY' ? 'flash-buy' : 'flash-sell');
                    }, 500);
                }

                // Simulate profit updates
                if (Math.random() > 0.7) {
                    const profitElement = document.querySelector('#DailyProfit');
                    const currentProfit = parseFloat(profitElement.textContent.replace('+', '').replace(' USDT', ''));
                    const newProfit = (currentProfit + (Math.random() * 0.5 - 0.25)).toFixed(8);
                    profitElement.textContent = `+${newProfit} USDT`;

                    const floatingProfit = document.createElement('div');
                    floatingProfit.className = 'floating-profit';
                    floatingProfit.textContent = `${parseFloat(newProfit) - currentProfit > 0 ? '+' : ''}${(parseFloat(newProfit) - currentProfit).toFixed(4)}`;
                    floatingProfit.style.left = `${profitElement.getBoundingClientRect().left}px`;
                    floatingProfit.style.top = `${profitElement.getBoundingClientRect().top}px`;
                    document.body.appendChild(floatingProfit);

                    setTimeout(() => {
                        document.body.removeChild(floatingProfit);
                    }, 500);
                }
            }
        }, 3000);
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Start with trading inactive
        toggleTradingSections(<?= $trade_active ? 'true' : 'false' ?>);

        // Initialize with API data
        fetchExchangeData();

        // Start live trading simulation
        simulateLiveTrading();

        // Add event listener for trading activation
        document.getElementById('activate-trading').addEventListener('click', async function() {

        // const isActive = document.getElementById('status-badge').textContent === 'TRADING ACTIVE';
        <?php echo ($trade_active == 1 ? 'true' : 'false'); ?>
        const newStatus = isActive ? 0 : 1;

        // Send AJAX request to update trading status
        const response = await fetch(window.location.href, {
        method: 'POST',
        headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
        },
        body: `update_status=1&status=${newStatus}`
        });
        const result = await response.json();

        if (result.success) {
        toggleTradingSections(!isActive);
        } else {
        alert(result.message);
        }
        });

        document.getElementById('activate-trading').addEventListener('click', async function() {
            // PHP value ko JS boolean banaya
            // let isActive = <?php echo ($trade_active == 1 ? 'true' : 'false'); ?>;
            const newStatus = isActive ? 0 : 1;

            // AJAX request to backend
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `update_status=1&status=${newStatus}`
            });

            const result = await response.json();

            if (result.success) {
                // UI update after DB update
                toggleTradingSections(!isActive);
            } else {
                alert(result.message);
            }
        });

        // Add exchange selection functionality
        document.querySelectorAll('.exchange-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.exchange-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                if (document.getElementById('status-badge').textContent === 'TRADING ACTIVE') {
                    fetchExchangeData();
                }
            });
        });
    });

    <?php if ($trade_active): ?>

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
    <?php endif; ?>
</script>

<?php include 'footer.php'; ?>