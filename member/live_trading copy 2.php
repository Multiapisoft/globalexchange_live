<?php
$title = 'Trading Platform';
include_once 'header.php';
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

        setMessage('Trade activated', 'success');
        // Return success for AJAX request
        // if (is_ajax_request()) {
        //     echo json_encode(['success' => true, 'message' => 'Trading status updated successfully']);
        //     exit;
        // }
    } else {
        setMessage('Kindly trade first to activate trading', 'error');
        redirect('./invest.php');
        // Block update if user has no package
        // if (is_ajax_request()) {
        //     echo json_encode(['success' => false, 'message' => 'Kindly trade first to activate trading']);

        //     exit;
        // }
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

// Calculate average percentage
// $avg_percentage_query = "SELECT AVG(percentage) as avg_percentage FROM income_growth WHERE uid='$uid' AND type=0";
// $avg_percentage_result = my_query($avg_percentage_query);
// $avg_percentage_row = mysqli_fetch_object($avg_percentage_result);
// $avg_percentage = $avg_percentage_row->avg_percentage ? round($avg_percentage_row->avg_percentage, 2) : 0;

$today = date("Y-m-d");

$latest_query = "SELECT SUM(amount) as daily_amount
                 FROM income_growth 
                 WHERE uid='$uid' 
                   AND type=0 
                   AND DATE(datetime) = '$today'";

$latest_result = my_query($latest_query);
$latest_row = mysqli_fetch_object($latest_result);
$latest_amount = $latest_row && $latest_row->daily_amount ? $latest_row->daily_amount : 0;

// echo "Today's total amount: " . $latest_amount;

//Profit time counter

$dailyroiamount = $latest_amount * 1;
// $dailyroiamount = 1;
$trade_active = $user->trade_status; // 👈 active / inactive
// echo $latest_amount;

// $trade_status_updated = "2025-09-15 14:20:00";
$start = strtotime($trade_status_updated);
$end   = strtotime($today . " 23:59:59");
// echo $start . " - " . $end;

$animation_duration = ($end - $start) * 1000; // Animation duration in milliseconds

// $animation_duration = 100*1000; // Animation duration in milliseconds
$current = 0;
if ($trade_active) {
    if (date("Y-m-d", $start) == $today) {
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

    .blink {
        background: rgba(246, 70, 93, 0.7);
        animation: blinkIndicator 1s infinite alternate;
    }

    @keyframes blinkIndicator {
        from {
            opacity: 0.7;
        }

        to {
            opacity: 1;
        }
    }
</style>

<div class="container">
    <!-- Header Section -->
    <div class="header">
        <?php if ($trade_active) { ?>
            <div class="blink" style="border-radius:5px; padding: 5px 10px;"> Live</div>
        <?php } ?>
        <div class="status-badge" id="status-badge">TRADING INACTIVE</div>
    </div>
    <!-- Stats Section -->
    <div class="stats">
        <div class="stat-card">
            <div class="stat-title">Active Trades</div>
            <div class="stat-value"><?php echo $active_orders ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Profit</div>
            <div class="stat-value"> + <?= number_format($total_earnings, 2) ?> USDT</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Today Profit</div>
            <div class="stat-value" id="DailyProfit">+<?= number_format($current, 8) ?> USDT</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Bot Liquidity</div>
            <div class="stat-value" ></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Bot Profit</div>
            <div class="stat-value" ></div>
        </div>
    </div>
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
            <!-- <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.svg" class="pair-icon" alt="BTC"> -->
            <span>BTC/USDT</span>
        </div>

        <!-- Trading Layout -->
        <div class="trading-layout">
            <!-- Order Book -->
            <div class="order-book">
                <div class="section-title">Order Book</div>
                <div class="order-row header">
                    <div>Exchange</div>
                    <div>Pair</div>
                    <div>Price (USDT)</div>
                    <div>Amount</div>
                    <div>Total</div>
                </div>
                <div id="order-book-asks"></div>
                <div class="order-row" style="background: #1c1f2d; font-weight: 600;">
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
                    <div>Exchange</div>
                    <div>Pair</div>
                    <div>Type</div>
                    <div>Amount</div>
                    <div>Time</div>
                </div>
                <div id="trade-history-list"></div>
            </div>
        </div>


    </div>

    <!-- Inactive Trading Message -->
    <div id="inactive-message" class="inactive-message">
        Trading is currently inactive. Please activate trading to view details.
    </div>

    <!-- Trading Activation -->
    <div class="activation-container" id="InactiveForm">
        <!-- <form action="" method="post">
            <input type="hidden" name="status" id="trade-status" value="<?= $trade_active ? '0' : '1' ?>">
            <button class="activate-button" type="submit" name="update_status">ACTIVATE TRADING</button>
        </form> -->
        <button class="activate-button" id="activate-trading">ACTIVATE TRADING</button>
    </div>
    <div class="activation-container" id="activForm" style="display: none;">
        <form action="" method="post">
            <input type="hidden" name="status" id="trade-status" value="1">
            <button class="activate-button" type="submit" name="update_status">ACTIVATE TRADING</button>
        </form>
    </div>

</div>

<script>
    // Function to toggle trading sections visibility
    function toggleTradingSections(isActive) {

        console.log("toggleTradingSections", isActive);
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
    function generateOrderBookRows(asks, bids, currentPrice) {
        const asksContainer = document.getElementById('order-book-asks');
        const bidsContainer = document.getElementById('order-book-bids');
        const currentPriceRow = document.getElementById('current-price');

        asksContainer.innerHTML = '';
        bidsContainer.innerHTML = '';

        asks.slice(0, 5).forEach(order => {
            const row = document.createElement('div');
            row.className = 'order-row price-down';
            row.innerHTML = `
                    <div>${parseFloat(order[0]).toLocaleString()}</div>
                    <div>${parseFloat(order[1]).toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
                    <div>${(parseFloat(order[0]) * parseFloat(order[1])).toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
                `;
            asksContainer.appendChild(row);
        });

        bids.slice(0, 5).forEach(order => {
            const row = document.createElement('div');
            row.className = 'order-row price-up';
            row.innerHTML = `
                    <div>${parseFloat(order[0]).toLocaleString()}</div>
                    <div>${parseFloat(order[1]).toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
                    <div>${(parseFloat(order[0]) * parseFloat(order[1])).toLocaleString(undefined, { minimumFractionDigits: 4 })}</div>
                `;
            bidsContainer.appendChild(row);
        });

        currentPriceRow.innerHTML = `
                <div class="price-up">${parseFloat(currentPrice).toLocaleString()}</div>
                <div>-</div>
                <div>-</div>
            `;
    }

    // Function to generate trade history rows
    function generateTradeHistoryRows(trades) {
        const tradeList = document.getElementById('trade-history-list');
        tradeList.innerHTML = '';

        trades.slice(0, 10).forEach(trade => {
            const row = document.createElement('div');
            row.className = `trade-row ${trade.side === 'buy' ? 'price-up' : 'price-down'}`;
            row.innerHTML = `
                    <div>${trade.side.toUpperCase()}</div>
                    <div>${parseFloat(trade.size).toLocaleString(undefined, { minimumFractionDigits: 5 })}</div>
                    <div>${new Date(trade.time).toLocaleTimeString()}</div>
                `;
            tradeList.appendChild(row);
            
        });
    }

    // API Integration Functions
    async function fetchBinanceData() {
        try {
            const [priceResponse, orderBookResponse, tradesResponse] = await Promise.all([
                fetch('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT'),
                fetch('https://api.binance.com/api/v3/depth?symbol=BTCUSDT'),
                fetch('https://api.binance.com/api/v3/trades?symbol=BTCUSDT')
            ]);

            const priceData = await priceResponse.json();
            const orderBookData = await orderBookResponse.json();
            const tradesData = await tradesResponse.json();

            return {
                currentPrice: priceData.price,
                asks: orderBookData.asks,
                bids: orderBookData.bids,
                trades: tradesData.map(trade => ({
                    side: trade.isBuyerMaker ? 'buy' : 'sell',
                    size: trade.qty,
                    time: trade.time
                }))
            };
        } catch (error) {
            console.error('Error fetching Binance data:', error);
            return null;
        }
    }

    async function fetchBybitData() {
        try {
            const [priceResponse, orderBookResponse, tradesResponse] = await Promise.all([
                fetch('https://api.bybit.com/v2/public/tickers?symbol=BTCUSDT'),
                fetch('https://api.bybit.com/v2/public/orderBook/L2?symbol=BTCUSDT'),
                fetch('https://api.bybit.com/public/v5/market/recent-trade?symbol=BTCUSDT')
            ]);

            const priceData = await priceResponse.json();
            const orderBookData = await orderBookResponse.json();
            const tradesData = await tradesResponse.json();

            return {
                currentPrice: priceData.result[0].last_price,
                asks: orderBookData.result.filter(item => item.side === 'Sell').map(item => [item.price, item.size]),
                bids: orderBookData.result.filter(item => item.side === 'Buy').map(item => [item.price, item.size]),
                trades: tradesData.result.list.map(trade => ({
                    side: trade.side.toLowerCase(),
                    size: trade.size,
                    time: new Date(trade.time).getTime()
                }))
            };
        } catch (error) {
            console.error('Error fetching Bybit data:', error);
            return null;
        }
    }

    // Function to simulate live trading updates
    async function simulateLiveTrading() {
        console.log("simulateLiveTrading");
        let activeExchange = 'Binance';
        async function updateData() {
            if (document.getElementById('status-badge').textContent !== 'TRADING ACTIVE') return;

            let data;
            if (activeExchange === 'Binance') {
                data = await fetchBinanceData();
            } else if (activeExchange === 'Bybit') {
                data = await fetchBybitData();
            }

            generateTradeHistoryRows(data.trades);
            generateOrderBookRows(data.asks, data.bids, data.currentPrice);
            if (data) {

                // Simulate profit updates
                if (Math.random() > 0.7) {
                    const profitElement = document.querySelector('.stat-value');
                    // const currentProfit = parseFloat(profitElement.textContent) || 10;
                    // const newProfit = (currentProfit + (Math.random() * 0.5 - 0.25)).toFixed(8);
                    // profitElement.textContent = `${newProfit} USDT`;

                    // const floatingProfit = document.createElement('div');
                    // floatingProfit.className = 'floating-profit';
                    // floatingProfit.textContent = `${parseFloat(newProfit) - currentProfit > 0 ? '+' : ''}${(parseFloat(newProfit) - currentProfit).toFixed(4)}`;
                    // floatingProfit.style.left = `${profitElement.getBoundingClientRect().left}px`;
                    // floatingProfit.style.top = `${profitElement.getBoundingClientRect().top}px`;
                    // document.body.appendChild(floatingProfit);

                    setTimeout(() => {
                        document.body.removeChild(floatingProfit);
                    }, 1000);
                }
            }
        }

        setInterval(updateData, 3000);
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        toggleTradingSections(false);
        simulateLiveTrading();

        document.getElementById('activate-trading').addEventListener('click', function() {


            console.log("jhfksdhjhsd $trade_active", <?= $trade_active ?>);
        });

        const isActive = <?= $trade_active ?>;
        // const isActive = 0;
        // const isActive = document.getElementById('status-badge').textContent === 'TRADING ACTIVE';
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
        const test = animate("DailyProfit", <?= round($current, 8) ?>, <?= $dailyroiamount ?>, <?= $animation_duration ?>);
        // console.log(" testing = ".test);
    </script>
<?php endif; ?>
<?php include_once 'footer.php'; ?>
<!-- </body>

</html> -->