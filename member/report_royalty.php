  <?php $type = (isset($_GET['type']) && (int) $_GET['type'] <= 3) ? (int) $_GET['type'] : 0;
$title = ($type == 3) ? "Pool Income" : (($type == 2) ? "Royalty" : (($type == 1) ? "Fast Track Bonus" : "Reward Income"));
include_once 'header.php';
$uid = (int) $uid; // ensure integer for queries

// Pagination setup
$records_per_page = 20;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Ensure page is at least 1
$offset = ($current_page - 1) * $records_per_page;

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM income_royalty as r WHERE r.uid=".$uid." AND r.type=".$type;
$count_result = my_query($count_query);
$count_row = mysqli_fetch_object($count_result);
$total_records = $count_row->total;
$total_pages = ceil($total_records / $records_per_page);

// Main query with pagination
$query = "SELECT r.* FROM income_royalty as r"
        . " WHERE r.uid=".$uid." AND r.type=".$type
        . " ORDER BY r.datetime DESC LIMIT $records_per_page OFFSET $offset";
$result = my_query($query);
$i = $offset; // Start counter from offset for proper numbering

$reward_arr = get_reward();
$reward_arr2 = array('-', 'Earbuds', 'Smart Phone', 'Laptop', 'Bike', 'Royal Enfield', 'Taigo', 'Thar', 'Fortuner', 'BMW x1', 'Three BHK and Mercedes Benz');

// Calculate statistics
$stats_query = "SELECT
    COUNT(*) as total_transactions,
    SUM(amount) as total_earnings,
    MAX(datetime) as latest_transaction
    FROM income_royalty
    WHERE uid=".$uid." AND type=".$type;
$stats_result = my_query($stats_query);
$stats = mysqli_fetch_object($stats_result);

$total_transactions = $stats->total_transactions ? $stats->total_transactions : 0;
$total_earnings = $stats->total_earnings ? $stats->total_earnings : 0;
$latest_transaction = $stats->latest_transaction ? date("d M, Y", strtotime($stats->latest_transaction)) : 'No transactions yet';

// Calculate today's earnings
$today = date('Y-m-d');
$today_query = "SELECT SUM(amount) as total FROM income_royalty WHERE uid=".$uid." AND type=".$type." AND DATE(datetime)='$today'";
$today_result = my_query($today_query);
$today_row = mysqli_fetch_object($today_result);
$today_earnings = $today_row->total ? $today_row->total : 0;
?>

<style>
/* Menu and Submenu Text Colors - Light */
#side-menu li a,
#side-menu li a .menu-text,
.nav-second-level li a,
.nav-second-level li a i,
.sidebar .menu-text {
    color: #eaecef !important;
}

/* Fresh Royalty Theme - Same as Downline */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    /* Dark Color Palette aligned with dashboard */
    --bg-primary: #0b0e11;
    --bg-secondary: #1e2329;
    --bg-accent: #2b3139;
    --text-primary: #eaecef;
    --text-secondary: #848e9c;
    --text-muted: #6c757d;
    --brand-primary: #4f46e5;
    --brand-secondary: #7c3aed;
    --success: #02c076;
    --warning: #f0b90b;
    --danger: #f6465d;
    --info: #3b82f6;
    --border: #2c3137;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.4);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.6);
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

/* Fresh Royalty Header */
.fresh-royalty-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    border-radius: var(--radius-lg);
    padding: 32px;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.fresh-royalty-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

.fresh-royalty-header-content {
    position: relative;
    z-index: 2;
}

.fresh-royalty-header h1 {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.fresh-royalty-header p {
    font-size: 2rem;
    font-weight: 600;
    opacity: 0.9;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-royalty-header-icon {
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
    font-size: 2rem;
    flex-shrink: 0;
}

.fresh-stat-content {
    flex: 1;
}

.fresh-stat-label {
    font-size: 2rem;
    color: var(--text-secondary);
    font-weight: 600;
    margin-bottom: 4px;
}

.fresh-stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-stat-value.earnings {
    color: var(--success);
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
    font-size: 2rem;
    font-weight: 800;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 14px;
    text-shadow: 0 2px 4px rgba(255, 255, 255, 0.2);
}

.fresh-section-icon {
    font-size: 2rem;
}

/* Fresh Search Container */
.fresh-search-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.fresh-search-input {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 25px;
    padding: 10px 18px;
    color: white;
    font-size: 1.1rem;
    font-weight: 500;
    width: 280px;
    transition: all 0.3s ease;
}

.fresh-search-input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.fresh-search-input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
}

.fresh-search-icon {
    color: rgba(255, 255, 255, 0.8);
    font-size: 2rem;
}

/* Fresh Table Container */
.fresh-table-container {
    padding: 0;
    overflow-x: auto;
}

/* Fresh Table Styling */
.fresh-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: var(--bg-secondary);
}

.fresh-table th {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    color: white;
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    padding: 20px 24px;
    text-align: left;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
    letter-spacing: 0.5px;
}

.fresh-table th:first-child {
    border-top-left-radius: 0;
}

.fresh-table th:last-child {
    border-top-right-radius: 0;
}

.fresh-table td {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    color: var(--text-primary);
    font-size: 2rem;
    font-weight: 500;
    transition: all 0.2s ease;
    background: var(--bg-secondary);
}

.fresh-table tr:last-child td {
    border-bottom: none;
}

.fresh-table tr:hover td {
    background: var(--bg-accent);
    transform: translateX(2px);
}

/* Fresh Date Display */
.fresh-date-display {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.fresh-date-primary {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1.5rem;
}

.fresh-date-secondary {
    font-size: 1.5rem;
    color: var(--text-muted);
    font-weight: 600;
}

/* Fresh Amount Value */
.fresh-amount-value {
    font-weight: 700;
    color: var(--success);
    font-size: 1.2rem;
}

/* Fresh Rank Badge */
.fresh-rank-badge {
    display: inline-flex;
    align-items: center;
    padding: 10px 16px;
    background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
    color: white;
    border-radius: 20px;
    font-size: 1.05rem;
    font-weight: 700;
    box-shadow: var(--shadow);
}

/* Fresh Reward Badge */
.fresh-reward-badge {
    display: inline-flex;
    align-items: center;
    padding: 10px 16px;
    background: linear-gradient(135deg, var(--info) 0%, #1d4ed8 100%);
    color: white;
    border-radius: 20px;
    font-size: 1.05rem;
    font-weight: 700;
    box-shadow: var(--shadow);
}

/* Fresh Month Badge */
.fresh-month-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: var(--shadow);
}

/* Fresh Empty State */
.fresh-empty-state {
    padding: 60px 20px;
    text-align: center;
    color: var(--text-muted);
}

.fresh-empty-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    color: var(--text-muted);
    opacity: 0.5;
}

.fresh-empty-text {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: var(--text-secondary);
}

.fresh-empty-subtext {
    font-size: 1.1rem;
    opacity: 0.8;
    max-width: 400px;
    margin: 0 auto;
}

/* Fresh Pagination */
.fresh-pagination-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 32px;
    padding: 20px;
    gap: 16px;
}

.fresh-pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    background: var(--bg-secondary);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
}

.fresh-pagination li {
    border-right: 1px solid var(--border);
}

.fresh-pagination li:last-child {
    border-right: none;
}

.fresh-pagination a,
.fresh-pagination span {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    color: var(--text-primary);
    text-decoration: none;
    transition: all 0.2s ease;
    min-width: 48px;
    font-weight: 600;
}

.fresh-pagination a:hover {
    background: var(--bg-accent);
    color: var(--brand-primary);
    transform: translateY(-1px);
}

.fresh-pagination .active span {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    color: white;
    font-weight: 700;
}

.fresh-pagination .disabled span {
    color: var(--text-muted);
    cursor: not-allowed;
    opacity: 0.5;
}

.fresh-pagination .dots span {
    color: var(--text-muted);
    cursor: default;
}

.fresh-pagination-info {
    color: var(--text-secondary);
    font-size: 1rem;
    font-weight: 500;
    text-align: center;
    background: var(--bg-secondary);
    padding: 12px 20px;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
}

/* Fresh Mobile Responsive */
@media (max-width: 768px) {
    .fresh-container {
        padding: 16px;
    }

    .fresh-royalty-header {
        padding: 24px;
    }

    .fresh-royalty-header h1 {
        font-size: 2rem;
        font-weight: 900;
    }

    .fresh-royalty-header p {
        font-size: 1rem;
        font-weight: 600;
    }

    .fresh-stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .fresh-table-container {
        padding: 16px;
        overflow-x: auto;
    }

    .fresh-table th,
    .fresh-table td {
        padding: 12px 16px;
        font-size: 0.9rem;
    }

    .fresh-section-title {
        font-size: 1.3rem;
        font-weight: 800;
    }

    .fresh-search-input {
        width: 200px;
        font-size: 1rem;
    }

    .fresh-pagination-container {
        padding: 16px;
    }

    .fresh-pagination a,
    .fresh-pagination span {
        padding: 10px 12px;
        min-width: 40px;
        font-size: 0.9rem;
    }

    .fresh-pagination-info {
        font-size: 0.9rem;
        padding: 10px 16px;
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
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
    }
}

.fresh-card {
    animation: fadeInUp 0.6s ease-out;
}

.fresh-card:nth-child(1) { animation-delay: 0.1s; }
.fresh-card:nth-child(2) { animation-delay: 0.2s; }
.fresh-card:nth-child(3) { animation-delay: 0.3s; }
@media (max-width: 768px) {
    h3 {
        font-size: 16px !important;
    }
    h4 {
        font-size: 12px !important;
    }
    .fresh-stat-card {
        padding: 5px 16px !important;
    }
    .fresh-stat-icon {
        width: 40px !important;
        height: 40px !important;
    }
    .fresh-stat-content {
        flex: 1 !important;
    }
    .fresh-package-badge {
        padding: 5px 16px !important;
        font-size: 16px !important;
    }
    .fresh-user-name {
        font-size: 16px !important;
    }
    .fresh-user-id {
        font-size: 14px !important;
    }
    .fresh-user-details {
        gap: 1px !important;
    }
    .fresh-search-input {
        width: 112px !important;
        font-size: 12px !important;
    }
}
</style>

<!-- Fresh Royalty Container -->
<div class="fresh-container">
    <!-- Fresh Royalty Header -->
    <div class="fresh-royalty-header">
        <div class="fresh-royalty-header-content">
            <div class="fresh-royalty-header-icon">
                <i class="fas fa-crown"></i>
            </div>
            <h1><?php echo $title; ?></h1>
            <p>Track your royalty rewards and bonuses</p>
        </div>
    </div>

    <!-- Fresh Stats Grid -->
    <div class="fresh-stats-grid">
        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Earnings</div>
                <div class="fresh-stat-value earnings">$<?php echo number_format($total_earnings, 2); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Transactions</div>
                <div class="fresh-stat-value"><?php echo $total_transactions; ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Today's Earnings</div>
                <div class="fresh-stat-value earnings">
                    $<?php echo number_format($today_earnings, 2); ?>
                    <?php if ($today_earnings > 0): ?>
                    <i class="fas fa-arrow-up" style="color: var(--success); font-size: 14px; margin-left: 5px;"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Latest Transaction</div>
                <div class="fresh-stat-value" style="font-size: 1.4rem;"><?php echo $latest_transaction; ?></div>
            </div>
        </div>
    </div>

    <!-- Fresh Royalty History Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-history"></i>
                Transaction History
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="royaltySearchInput" class="fresh-search-input" placeholder="Search by date, amount, rank...">
            </div>
        </div>
        <div class="fresh-table-container">
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <?php if($type == 0 || $type == 1){?><th>Rank</th><?php }?>
                        <?php if($type == 1){?><th>Month</th><?php }?>
                        <?php if($type == 1){?><th>Reward Name</th><?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($total_records > 0) {
                        while ($row = mysqli_fetch_object($result)){$i++;?>
                        <tr>
                            <td><?php echo $i;?></td>
                            <td>
                                <div class="fresh-date-display">
                                    <span class="fresh-date-primary"><?php echo date("d M, Y", strtotime($row->datetime));?></span>
                                    <span class="fresh-date-secondary"><?php echo date("h:i A", strtotime($row->datetime));?></span>
                                </div>
                            </td>
                            <td><span class="fresh-amount-value">$<?php echo number_format($row->amount*1, 2);?></span></td>
                            <?php if($type == 0 || $type == 1){?><td><span class="fresh-rank-badge"><?php echo $reward_arr[$row->level];?></span></td><?php }?>
                            <?php if($type == 1){?><td><h5><?php echo $row->days;?></h5></td><?php }?>
                            <?php if($type == 1){?><td><span class="fresh-reward-badge"><?php echo $reward_arr2[$row->level];?></span></td><?php }?>
                        </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="<?php echo ($type == 0) ? '5' : (($type == 1) ? '5' : '3'); ?>">
                                <div class="fresh-empty-state">
                                    <div class="fresh-empty-icon"><i class="fas fa-crown"></i></div>
                                    <div class="fresh-empty-text">No transactions found</div>
                                    <div class="fresh-empty-subtext">You don't have any <?php echo strtolower($title); ?> transactions yet</div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Fresh Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="fresh-pagination-container">
            <ul class="fresh-pagination">
                <?php
                // Build base URL with current parameters
                $base_url = "report_royalty.php?type=$type";

                // First page
                if ($current_page > 1): ?>
                    <li><a href="<?php echo $base_url; ?>&page=1" title="First Page"><i class="fas fa-angle-double-left"></i></a></li>
                <?php else: ?>
                    <li class="disabled"><span><i class="fas fa-angle-double-left"></i></span></li>
                <?php endif;

                // Previous page
                if ($current_page > 1): ?>
                    <li><a href="<?php echo $base_url; ?>&page=<?php echo ($current_page - 1); ?>" title="Previous Page"><i class="fas fa-angle-left"></i></a></li>
                <?php else: ?>
                    <li class="disabled"><span><i class="fas fa-angle-left"></i></span></li>
                <?php endif;

                // Page numbers
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);

                // Show first page if we're not starting from it
                if ($start_page > 1): ?>
                    <li><a href="<?php echo $base_url; ?>&page=1">1</a></li>
                    <?php if ($start_page > 2): ?>
                        <li class="dots"><span>...</span></li>
                    <?php endif;
                endif;

                // Show page numbers
                for ($page = $start_page; $page <= $end_page; $page++): ?>
                    <li <?php echo ($page == $current_page) ? 'class="active"' : ''; ?>>
                        <?php if ($page == $current_page): ?>
                            <span><?php echo $page; ?></span>
                        <?php else: ?>
                            <a href="<?php echo $base_url; ?>&page=<?php echo $page; ?>"><?php echo $page; ?></a>
                        <?php endif; ?>
                    </li>
                <?php endfor;

                // Show last page if we're not ending with it
                if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <li class="dots"><span>...</span></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base_url; ?>&page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a></li>
                <?php endif;

                // Next page
                if ($current_page < $total_pages): ?>
                    <li><a href="<?php echo $base_url; ?>&page=<?php echo ($current_page + 1); ?>" title="Next Page"><i class="fas fa-angle-right"></i></a></li>
                <?php else: ?>
                    <li class="disabled"><span><i class="fas fa-angle-right"></i></span></li>
                <?php endif;

                // Last page
                if ($current_page < $total_pages): ?>
                    <li><a href="<?php echo $base_url; ?>&page=<?php echo $total_pages; ?>" title="Last Page"><i class="fas fa-angle-double-right"></i></a></li>
                <?php else: ?>
                    <li class="disabled"><span><i class="fas fa-angle-double-right"></i></span></li>
                <?php endif; ?>
            </ul>

            <!-- Pagination Info -->
            <div class="fresh-pagination-info">
                Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $records_per_page, $total_records); ?> of <?php echo $total_records; ?> entries
                (Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>)
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Fresh Royalty Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('royaltySearchInput');
    if (searchInput) {
        const table = document.querySelector('.fresh-table tbody');
        const rows = table.querySelectorAll('tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let found = false;

                // Search in all cells
                cells.forEach(cell => {
                    const text = cell.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        found = true;
                    }
                });

                // Show/hide row based on search result
                if (found || searchTerm === '') {
                    row.style.display = '';
                    row.style.animation = 'fadeInUp 0.3s ease-out';
                } else {
                    row.style.display = 'none';
                }
            });

            // Show "No results found" message if no rows are visible
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');

            // Remove existing no-results message
            const existingMessage = table.querySelector('.no-results-row');
            if (existingMessage) {
                existingMessage.remove();
            }

            if (visibleRows.length === 0 && searchTerm !== '') {
                const noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                const colspan = <?php echo ($type == 0) ? '5' : (($type == 1) ? '5' : '3'); ?>;
                noResultsRow.innerHTML = `
                    <td colspan="${colspan}" style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 1.1rem;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i><br>
                        No royalty transactions found matching "${searchTerm}"
                    </td>
                `;
                table.appendChild(noResultsRow);
            }
        });

        // Add search icon animation
        searchInput.addEventListener('focus', function() {
            const icon = document.querySelector('.fresh-search-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.color = 'rgba(255, 255, 255, 1)';
            }
        });

        searchInput.addEventListener('blur', function() {
            const icon = document.querySelector('.fresh-search-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
                icon.style.color = 'rgba(255, 255, 255, 0.8)';
            }
        });
    }

    // Add smooth hover animations to table rows
    const tableRows = document.querySelectorAll('.fresh-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Add hover animations to stat cards
    const statCards = document.querySelectorAll('.fresh-stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.fresh-stat-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
                icon.style.animation = 'pulse 2s infinite';
            }
        });

        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.fresh-stat-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
                icon.style.animation = 'none';
            }
        });
    });

    // Add click animation to badges
    const badges = document.querySelectorAll('.fresh-rank-badge, .fresh-reward-badge, .fresh-month-badge');
    badges.forEach(badge => {
        badge.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.pointerEvents = 'none';

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add pagination hover effects
    const paginationLinks = document.querySelectorAll('.fresh-pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = 'var(--shadow-lg)';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });

        // Add click animation
        link.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(79, 70, 229, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.pointerEvents = 'none';

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);

            // Add loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.style.pointerEvents = 'none';

            // Restore after a short delay (the page will navigate anyway)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }, 1000);
        });
    });

    // Add staggered animation to table rows
    const allTableRows = document.querySelectorAll('.fresh-table tbody tr');
    allTableRows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
        row.style.animation = 'fadeInUp 0.6s ease-out forwards';
        row.style.opacity = '0';

        setTimeout(() => {
            row.style.opacity = '1';
        }, index * 100);
    });

    // Add floating animation to header icon
    const headerIcon = document.querySelector('.fresh-royalty-header-icon');
    if (headerIcon) {
        setInterval(() => {
            headerIcon.style.transform = 'translateY(-5px)';
            setTimeout(() => {
                headerIcon.style.transform = 'translateY(0px)';
            }, 1000);
        }, 2000);
    }

    // Add smooth scroll to top when pagination is clicked
    const paginationContainer = document.querySelector('.fresh-pagination-container');
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                // Smooth scroll to top of table
                const tableContainer = document.querySelector('.fresh-table-container');
                if (tableContainer) {
                    setTimeout(() => {
                        tableContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 100);
                }
            }
        });
    }
});

// Add ripple animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .fresh-table tbody tr {
        opacity: 0;
        transform: translateY(20px);
    }

    .fresh-table tbody tr.animate-in {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .fresh-royalty-header-icon {
        transition: transform 1s ease-in-out;
    }
`;
document.head.appendChild(style);
</script>

<?php include_once 'footer.php'; ?>