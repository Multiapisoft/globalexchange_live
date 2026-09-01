<?php
$title = "Withdrawal History";
include_once 'header.php';
$query = "SELECT w.* FROM withdrawal_block as w"
        . " WHERE w.uid='".$uid."'"
        . " ORDER BY w.datetime DESC";
$result = my_query($query);
$i=0;

// Calculate withdrawal statistics
$total_withdrawals = mysqli_num_rows($result);
$total_amount = 0;
$pending_count = 0;
$success_count = 0;
$rejected_count = 0;

// Reset result pointer and calculate stats
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_object($result)) {
    $total_amount += $row->amount;
    if ($row->status == 0) $pending_count++;
    elseif ($row->status == 1) $success_count++;
    elseif ($row->status == 2) $rejected_count++;
}
// Reset result pointer for display
mysqli_data_seek($result, 0);
?>


<style>
/* Report UI styles loaded from theme/css/reports-theme.css */
.content-header { display: none !important; }
</style>
<!-- Fresh Withdrawal History Container -->
<div class="fresh-container">
    <!-- Fresh Withdrawal History Header -->
    <div class="fresh-withdrawal-history-header">
        <div class="fresh-withdrawal-history-header-content">
            <div class="fresh-withdrawal-history-header-icon">
                <i class="fas fa-history"></i>
            </div>
            <h1>Withdrawal History</h1>
            <p>Track your withdrawal transactions and status</p>
        </div>
    </div>

    <!-- Fresh Stats Grid -->
    <div class="fresh-stats-grid">
        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Withdrawals</div>
                <div class="fresh-stat-value success">$<?php echo number_format($total_amount, 2); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Successful</div>
                <div class="fresh-stat-value success"><?php echo $success_count; ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Pending</div>
                <div class="fresh-stat-value warning"><?php echo $pending_count; ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Rejected</div>
                <div class="fresh-stat-value danger"><?php echo $rejected_count; ?></div>
            </div>
        </div>
    </div>

    <!-- Fresh Withdrawal History Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-list"></i>
                Withdrawal Transactions
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="withdrawalSearchInput" class="fresh-search-input" placeholder="Search by amount, status, address...">
            </div>
        </div>
        <div class="fresh-table-container">
            <?php if($total_withdrawals > 0): ?>
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Fee</th>
                        <th>Net Amount</th>
                        <th>Value</th>
                        <th>Address</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                    <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                        <td><span class="fresh-amount-value">$<?php echo number_format($row->amount*1, 2);?></span></td>
                        <td><span class="fresh-fee-value">$<?php echo number_format($row->fee*1, 2);?></span></td>
                        <td><span class="fresh-net-amount-value">$<?php echo number_format($row->net_amount*1, 2);?></span></td>
                        <td><strong><?php echo number_format($row->amount_coin*1, 4);?> <?php echo $row->type;?></strong></td>
                        <td><span class="fresh-address-text" title="<?php echo $row->withdrawal_address;?>" onclick="copyToClipboard('<?php echo $row->withdrawal_address;?>')"><?php echo $row->withdrawal_address;?></span></td>
                        <td><span class="fresh-type-badge"><?php echo $row->type;?></span></td>
                        <td>
                            <?php if($row->status==0):?>
                                <span class="fresh-status-badge pending"><i class="fas fa-clock"></i> Pending</span>
                            <?php elseif($row->status==1):?>
                                <span class="fresh-status-badge success"><i class="fas fa-check-circle"></i> Success</span>
                            <?php elseif($row->status==2):?>
                                <span class="fresh-status-badge rejected"><i class="fas fa-times-circle"></i> Rejected</span>
                            <?php endif;?>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <?php else: ?>
            <!-- Fresh Empty state -->
            <div class="fresh-empty-state">
                <div class="fresh-empty-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="fresh-empty-text">No withdrawal history found</div>
                <div class="fresh-empty-subtext">You haven't made any withdrawal requests yet. Your withdrawal history will appear here once you make a withdrawal.</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
// Fresh Withdrawal History Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('withdrawalSearchInput');
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
                        No withdrawal transactions found matching "${searchTerm}"
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

    // Add hover effects to status badges
    const statusBadges = document.querySelectorAll('.fresh-status-badge');
    statusBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = 'var(--shadow-lg)';
        });
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'var(--shadow)';
        });
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
                Address copied to clipboard!
            </div>
        `;
        document.body.appendChild(toast);

        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeInUp 0.3s ease-out reverse';
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy to clipboard');
    });
}
</script>

<?php include_once 'footer.php'; ?>