<?php
$title = "P2P Transfer History";
include_once 'header.php';
$query = "SELECT t.uid, t.amount, t.datetime, u.login_id, u.name, f.login_id as from_login_id, f.name as from_name, t.type, t.remark, t.from_uid, t.tamt FROM fund_transfer as t"
        . " LEFT JOIN user as u ON u.uid=t.uid"
        . " LEFT JOIN user as f ON f.uid=t.from_uid"
        . " WHERE t.uid='".$uid."' OR t.from_uid='".$uid."'"
        . " ORDER BY t.datetime DESC";
$result = my_query($query);
$i=0;
$fund_type = get_fund_type(1);

// Calculate total transfers
$total_transfers_query = "SELECT COUNT(*) as total FROM fund_transfer WHERE uid='$uid' OR from_uid='$uid'";
$total_transfers_result = my_query($total_transfers_query);
$total_transfers_row = mysqli_fetch_object($total_transfers_result);
$total_transfers = $total_transfers_row->total;

// Calculate total amount transferred
$total_amount_query = "SELECT SUM(amount) as total FROM fund_transfer WHERE uid='$uid' OR from_uid='$uid'";
$total_amount_result = my_query($total_amount_query);
$total_amount_row = mysqli_fetch_object($total_amount_result);
$total_amount = $total_amount_row->total ? $total_amount_row->total : 0;

// Get latest transfer date
$latest_transfer_query = "SELECT MAX(datetime) as latest FROM fund_transfer WHERE uid='$uid' OR from_uid='$uid'";
$latest_transfer_result = my_query($latest_transfer_query);
$latest_transfer_row = mysqli_fetch_object($latest_transfer_result);
$latest_transfer = $latest_transfer_row->latest ? date("d M, Y", strtotime($latest_transfer_row->latest)) : 'N/A';

// Calculate sent and received amounts
$sent_amount_query = "SELECT SUM(amount) as total FROM fund_transfer WHERE from_uid='$uid'";
$sent_amount_result = my_query($sent_amount_query);
$sent_amount_row = mysqli_fetch_object($sent_amount_result);
$sent_amount = $sent_amount_row->total ? $sent_amount_row->total : 0;

$received_amount_query = "SELECT SUM(amount) as total FROM fund_transfer WHERE uid='$uid'";
$received_amount_result = my_query($received_amount_query);
$received_amount_row = mysqli_fetch_object($received_amount_result);
$received_amount = $received_amount_row->total ? $received_amount_row->total : 0;
?>



<style>
/* Report UI styles loaded from theme/css/reports-theme.css */
.content-header { display: none !important; }
</style>
<!-- Fresh P2P Transfer Container -->
<div class="fresh-container">
    <!-- Fresh P2P Header -->
    <div class="fresh-p2p-header">
        <div class="fresh-p2p-header-content">
            <div class="fresh-p2p-header-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <h1><?php echo $title; ?></h1>
            <p>Track your peer-to-peer fund transfers</p>
        </div>
    </div>

    <!-- Fresh Stats Grid -->
    <div class="fresh-stats-grid">
        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Amount</div>
                <div class="fresh-stat-value success"><?php echo SITE_CURRENCY; ?><?php echo number_format($total_amount, 2); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon success">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Sent Amount</div>
                <div class="fresh-stat-value success"><?php echo SITE_CURRENCY; ?><?php echo number_format($sent_amount, 2); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon info">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Received Amount</div>
                <div class="fresh-stat-value info"><?php echo SITE_CURRENCY; ?><?php echo number_format($received_amount, 2); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon warning">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Transfers</div>
                <div class="fresh-stat-value warning"><?php echo $total_transfers; ?></div>
            </div>
        </div>
    </div>

    <!-- Fresh P2P History Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-history"></i>
                Transfer History
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="transferSearchInput" class="fresh-search-input" placeholder="Search by user, amount, type...">
            </div>
        </div>
        <div class="fresh-table-container">
            <?php if ($total_transfers > 0): ?>
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                    <tr>
                        <td data-label="#"><?php echo $i;?></td>
                        <td data-label="From"><span class="fresh-user-badge"><?php echo $row->from_login_id ? $row->from_login_id ." (".$row->from_name.")" : "ADMIN";?></span></td>
                        <td data-label="To"><span class="fresh-user-badge"><?php echo $row->login_id." (".$row->name.")";?></span></td>
                        <td data-label="Date"><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                        <td data-label="Amount"><span class="fresh-amount-value"><?php echo SITE_CURRENCY; ?><?php echo number_format($row->amount, 2);?></span></td>
                        <td data-label="Type"><span class="fresh-type-badge"><?php echo $fund_type[$row->type];?></span></td>
                        <td data-label="Remark"><?php echo $row->remark ? $row->remark : '-';?></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <?php else: ?>
            <!-- Fresh Empty state -->
            <div class="fresh-empty-state">
                <div class="fresh-empty-icon"><i class="fas fa-exchange-alt"></i></div>
                <div class="fresh-empty-text">No transfers found</div>
                <div class="fresh-empty-subtext">You haven't made any P2P transfers yet. Your transfer history will appear here once you send or receive funds.</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Fresh P2P Transfer Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('transferSearchInput');
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
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 1.1rem;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i><br>
                        No transfer transactions found matching "${searchTerm}"
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
    const freshElements = document.querySelectorAll('.fresh-stat-value, .fresh-user-badge, .fresh-type-badge');
    freshElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.animation = 'pulse 2s ease-in-out';
        }, index * 100);
    });

    // Add hover effects to user badges
    const userBadges = document.querySelectorAll('.fresh-user-badge');
    userBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = 'var(--shadow-lg)';
        });
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'var(--shadow)';
        });
    });

    // Add hover effects to type badges
    const typeBadges = document.querySelectorAll('.fresh-type-badge');
    typeBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = 'var(--shadow-lg)';
        });
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'var(--shadow)';
        });
    });

    // Add click-to-copy functionality for user badges (optional)
    userBadges.forEach(badge => {
        badge.style.cursor = 'pointer';
        badge.title = 'Click to copy user info';
        badge.addEventListener('click', function() {
            const text = this.textContent;
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
                        User info copied to clipboard!
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
            });
        });
    });
});
</script>

<?php include_once 'footer.php'; ?>
