<?php
$title = 'Trading Platform';
include_once 'header.php';
// Check if user is logged in
if (!isset($user)) {
    header("Location: index.php");
    exit;
}


// User must have an active non-bot trading plan
$hasLiveTradingPlan = my_num_rows(my_query(
    "SELECT recid FROM investments WHERE uid = '" . (int) $uid . "' AND status = 0 AND is_closed = 0 AND ipid != 4 LIMIT 1"
)) > 0;

// Daily profit animation uses plan eligibility (bot plan excluded above)
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
// echo $latest_amount;

// $trade_status_updated = "2025-09-15 14:20:00";
$start = strtotime($trade_status_updated);
$end   = strtotime($today . " 23:59:59");
// echo $start . " - " . $end;

$animation_duration = ($end - $start) * 1000; // Animation duration in milliseconds

// $animation_duration = 100*1000; // Animation duration in milliseconds
$current = 0;
if ($hasLiveTradingPlan && $dailyroiamount > 0) {
    if ($trade_status_updated && date("Y-m-d", $start) == $today) {
        $current = (time() - $start) / max(1, $end - $start) * $dailyroiamount;
    } else {
        $current = $dailyroiamount;
    }
}


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
        background: rgba(212, 175, 55, 0.15);
        border: 1px solid rgba(212, 175, 55, 0.3);
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

    .live-chart-panel {
        background: #161824;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        min-height: 420px;
    }

    .live-chart-panel .tradingview-widget-container,
    .live-chart-panel .tradingview-widget-container__widget {
        height: 400px !important;
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
        <?php if ($hasLiveTradingPlan) { ?>
            <div class="blink" style="border-radius:5px; padding: 5px 10px;"> Live</div>
        <?php } ?>
        <div class="status-badge" id="status-badge"><?php echo $hasLiveTradingPlan ? 'LIVE MARKET' : 'NO ACTIVE PLAN'; ?></div>
    </div>
    <!-- Stats Section -->
    <div class="stats">
        <div class="stat-card">
            <div class="stat-title">Total Profit</div>
            <div class="stat-value"> + <?= number_format($total_earnings, 2) ?> USDT</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Trade Profit</div>
            <div class="stat-value" id="DailyProfit">+<?= number_format($current, 8) ?> USDT</div>
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

    <!-- Trading Chart (always visible when user has a non-bot plan) -->
    <div id="trading-sections" style="<?php echo $hasLiveTradingPlan ? 'display:block;' : 'display:none;'; ?>">
        <div class="live-chart-panel">
            <div class="section-title">Live Trading Chart</div>
            <div class="tradingview-widget-container">
                <div class="tradingview-widget-container__widget"></div>
                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                {
                    "autosize": true,
                    "symbol": "BINANCE:BTCUSDT",
                    "interval": "15",
                    "timezone": "Etc/UTC",
                    "theme": "dark",
                    "style": "1",
                    "locale": "en",
                    "backgroundColor": "rgba(22, 24, 36, 1)",
                    "gridColor": "rgba(212, 175, 55, 0.08)",
                    "allow_symbol_change": true,
                    "calendar": false,
                    "support_host": "https://www.tradingview.com"
                }
                </script>
            </div>
        </div>
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
                    <div>Price (USDT)</div>
                    <div>Amount</div>
                    <div>Total</div>
                </div>
                <div id="order-book-asks"></div>
                <div class="order-row" id="current-price" style="background: #1c1f2d; font-weight: 600;">
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
                    <div>Type</div>
                    <div>Amount</div>
                    <div>Time</div>
                </div>
                <div id="trade-history-list"></div>
            </div>
        </div>


    </div>

    <!-- No trading plan message (bot plan excluded) -->
    <div id="inactive-message" class="inactive-message" style="<?php echo $hasLiveTradingPlan ? 'display:none;' : 'display:block;'; ?>">
        You need an active trading plan to view the live chart. Bot subscription plans are not eligible.
        <a href="trade.php" style="color:#d4af37;font-weight:700;margin-left:6px;">View Plans</a>
    </div>

</div>

<script>
    const hasLiveTradingPlan = <?= $hasLiveTradingPlan ? 'true' : 'false' ?>;

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
                fetch('https://api.binance.com/api/v3/depth?symbol=BTCUSDT&limit=5'),
                fetch('https://api.binance.com/api/v3/trades?symbol=BTCUSDT&limit=10')
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
                fetch('https://api.bybit.com/public/v5/market/recent-trade?symbol=BTCUSDT&limit=10')
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

    let activeExchange = 'Binance';
    let refreshMarketData = null;

    async function simulateLiveTrading() {
        if (!hasLiveTradingPlan) {
            return;
        }

        async function updateData() {
            let data;
            if (activeExchange === 'Binance') {
                data = await fetchBinanceData();
            } else if (activeExchange === 'Bybit') {
                data = await fetchBybitData();
            }

            if (data) {
                generateOrderBookRows(data.asks, data.bids, data.currentPrice);
                generateTradeHistoryRows(data.trades);
            }
        }

        refreshMarketData = updateData;
        updateData();
        setInterval(updateData, 3000);
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        if (hasLiveTradingPlan) {
            simulateLiveTrading();
        }

        document.querySelectorAll('.exchange-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.exchange-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                activeExchange = this.querySelector('.exchange-name').textContent.trim();
                if (refreshMarketData && (activeExchange === 'Binance' || activeExchange === 'Bybit')) {
                    refreshMarketData();
                }
            });
        });
    });
</script>

<?php if ($hasLiveTradingPlan && $dailyroiamount > 0): ?>
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