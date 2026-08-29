<?php
$title = "Deposit History";
include_once 'header.php';
$status_arr = array('Pending', 'Success', 'Failed');
$query = "SELECT f.* FROM deposit_block as f"
        . " WHERE f.uid='".$uid."'"
        . " ORDER BY f.datetime DESC";
$result = my_query($query);
$i=0;

// Calculate total deposit amount
$total_deposit_query = "SELECT SUM(amount) as total FROM deposit_block WHERE uid='$uid'";
$total_deposit_result = my_query($total_deposit_query);
$total_deposit_row = mysqli_fetch_object($total_deposit_result);
$total_deposit = $total_deposit_row->total ? $total_deposit_row->total : 0;

// Get total number of transactions
$total_transactions = mysqli_num_rows($result);

// Get latest transaction date
$latest_transaction_query = "SELECT MAX(datetime) as latest FROM deposit_block WHERE uid='$uid'";
$latest_transaction_result = my_query($latest_transaction_query);
$latest_transaction_row = mysqli_fetch_object($latest_transaction_result);
$latest_transaction = $latest_transaction_row->latest ? date("d M, Y", strtotime($latest_transaction_row->latest)) : 'N/A';

// Calculate today's deposits
$today = date('Y-m-d');
$today_deposit_query = "SELECT SUM(amount) as total FROM deposit_block WHERE uid='$uid' AND DATE(datetime)='$today'";
$today_deposit_result = my_query($today_deposit_query);
$today_deposit_row = mysqli_fetch_object($today_deposit_result);
$today_deposit = $today_deposit_row->total ? $today_deposit_row->total : 0;
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

/* Fresh Deposit Theme - Same as Downline */
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

/* Fresh Deposit Header */
.fresh-deposit-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    border-radius: var(--radius-lg);
    padding: 32px;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.fresh-deposit-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

.fresh-deposit-header-content {
    position: relative;
    z-index: 2;
}

.fresh-deposit-header h1 {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.fresh-deposit-header p {
    font-size: 2rem;
    font-weight: 600;
    opacity: 0.9;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-deposit-header-icon {
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

.fresh-stat-value.success {
    color: var(--success);
}

.fresh-stat-value.warning {
    color: var(--warning);
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
    font-size: 1rem;
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

/* Fresh Status Badge */
.fresh-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: var(--shadow);
}

.fresh-status-badge.success {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    color: white;
}

.fresh-status-badge.pending {
    background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
    color: white;
}

.fresh-status-badge.failed {
    background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
    color: white;
}

/* Fresh Transaction ID */
.fresh-txid {
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 1.5rem;
    color: var(--text-muted);
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
    transition: all 0.2s ease;
    padding: 6px 12px;
    background: var(--bg-accent);
    border-radius: 8px;
    border: 1px solid var(--border);
}

.fresh-txid:hover {
    color: var(--brand-primary);
    cursor: pointer;
    background: var(--bg-secondary);
    border-color: var(--brand-primary);
    transform: scale(1.02);
}

/* Fresh Address */
.fresh-address {
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 1rem;
    color: var(--text-muted);
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
    transition: all 0.2s ease;
    padding: 6px 12px;
    background: var(--bg-accent);
    border-radius: 8px;
    border: 1px solid var(--border);
}

.fresh-address:hover {
    color: var(--brand-primary);
    cursor: pointer;
    background: var(--bg-secondary);
    border-color: var(--brand-primary);
    transform: scale(1.02);
}

/* Fresh Amount Values */
.fresh-amount-value {
    font-weight: 700;
    color: var(--success);
    font-size: 1.2rem;
}

.fresh-fee-value {
    font-weight: 700;
    color: var(--danger);
    font-size: 1.2rem;
}

.fresh-net-amount-value {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1.2rem;
}

/* Fresh Empty State */
.fresh-empty-state {
    padding: 60px 20px;
    text-align: center;
    color: var(--text-muted);
}

.fresh-empty-icon {
    font-size: 4rem;
    margin-bottom: 24px;
    color: var(--brand-primary);
    opacity: 0.3;
}

.fresh-empty-text {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 12px;
    color: var(--text-secondary);
}

.fresh-empty-subtext {
    font-size: 1.1rem;
    opacity: 0.8;
    max-width: 500px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Fresh Mobile Responsive */
@media (max-width: 768px) {
    .fresh-container {
        padding: 16px;
    }

    .fresh-deposit-header {
        padding: 24px;
    }

    .fresh-deposit-header h1 {
        font-size: 2rem;
        font-weight: 900;
    }

    .fresh-deposit-header p {
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

    .fresh-section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .fresh-search-container {
        width: 100%;
        justify-content: center;
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

<!-- Fresh Deposit Container -->
<div class="fresh-container">
    <!-- Fresh Deposit Header -->
    <div class="fresh-deposit-header">
        <div class="fresh-deposit-header-content">
            <div class="fresh-deposit-header-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <h1><?php echo $title; ?></h1>
            <p>Track your deposit transactions and history</p>
        </div>
    </div>

    <!-- Fresh Stats Grid -->
    <div class="fresh-stats-grid">
        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Deposits</div>
                <div class="fresh-stat-value success">$<?php echo number_format($total_deposit, 2); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Transactions</div>
                <div class="fresh-stat-value"><?php echo $total_transactions; ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Today's Deposits</div>
                <div class="fresh-stat-value warning">
                    $<?php echo number_format($today_deposit, 2); ?>
                    <?php if ($today_deposit > 0): ?>
                    <i class="fas fa-arrow-up" style="color: var(--success); font-size: 1rem; margin-left: 8px;"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Last Deposit</div>
                <div class="fresh-stat-value"><?php echo $latest_transaction; ?></div>
            </div>
        </div>
    </div>

    <!-- Fresh Deposit History Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-history"></i>
                Deposit Transactions
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="depositSearchInput" class="fresh-search-input" placeholder="Search by amount, coin, status...">
            </div>
        </div>
        <div class="fresh-table-container">
            <?php if ($total_transactions > 0): ?>
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date & Time</th>
                        <th>Amount</th>
                        <th>Fee</th>
                        <th>Net Amount</th>
                        <th>Coin</th>
                        <th>Transaction ID</th>
                        <th>Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                    <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                        <td class="fresh-amount-value">$<?php echo number_format($row->amount*1, 4);?></td>
                        <td class="fresh-fee-value">$<?php echo number_format($row->fee*1, 4);?></td>
                        <td class="fresh-net-amount-value">$<?php echo number_format($row->net_amount*1, 4);?></td>
                        <td><strong><?php echo $row->amount_coin*1;?> <?php echo $row->type;?></strong></td>
                        <td>
                            <span class="fresh-txid" title="<?php echo $row->txid;?>" onclick="copyToClipboard('<?php echo $row->txid;?>')">
                                <?php echo $row->txid;?>
                            </span>
                        </td>
                        <td>
                            <span class="fresh-address" title="<?php echo $user->pay_address;?>" onclick="copyToClipboard('<?php echo $user->pay_address;?>')">
                                <?php echo $user->pay_address;?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $status = strtolower($row->status);
                            $status_class = 'pending';
                            $status_text = 'Pending';
                            if ($status == 1) {
                                $status_class = 'success';
                                $status_text = 'Success';
                            } else if ($status == 0) {
                                $status_class = 'failed';
                                $status_text = 'Failed';
                            }
                            ?>
                            <span class="fresh-status-badge <?php echo $status_class; ?>">
                                <?php echo $status_text;?>
                            </span>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="fresh-empty-state">
                <div class="fresh-empty-icon"><i class="fas fa-coins"></i></div>
                <div class="fresh-empty-text">No deposit transactions found</div>
                <div class="fresh-empty-subtext">Your deposit history will appear here once you make a deposit. All transactions will be tracked and displayed in this table.</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Fresh Deposit Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('depositSearchInput');
    const table = document.querySelector('.fresh-table tbody');

    if (searchInput && table) {
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
                noResultsRow.innerHTML = `
                    <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 1.1rem;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i><br>
                        No deposit transactions found matching "${searchTerm}"
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

    // Add hover animations to stat cards
    const statCards = document.querySelectorAll('.fresh-stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-4px) scale(1)';
        });
    });

    // Add pulse animation to fresh elements on load
    const freshElements = document.querySelectorAll('.fresh-stat-value, .fresh-status-badge');
    freshElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.animation = 'pulse 2s ease-in-out';
        }, index * 200);
    });
});

// Enhanced copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Create a modern toast notification
        const toast = document.createElement('div');
        toast.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                box-shadow: var(--shadow-lg);
                z-index: 10000;
                font-weight: 600;
                font-size: 1rem;
                animation: slideInLeft 0.3s ease-out;
            ">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                Copied to clipboard!
            </div>
        `;
        document.body.appendChild(toast);

        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeInUp 0.3s ease-out reverse';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy to clipboard');
    });
}
</script>

<?php include_once 'footer.php'; ?>
