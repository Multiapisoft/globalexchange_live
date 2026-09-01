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
/* Report UI styles loaded from theme/css/reports-theme.css */
.content-header { display: none !important; }
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
