<?php
$type = (isset($_GET['type']) && (int) $_GET['type'] <= 6) ? (int) $_GET['type'] : 0;
$title = ($type == 2) ? 'Campaign Team Bonus' : (($type == 1) ? 'Referral Team Bonus' : 'Direct Income');
include_once 'header.php';
$query = 'SELECT d.*, u.login_id, u.name, f.login_id as from_login_id, f.name as from_name, ip.title FROM income_direct as d'
    . ' LEFT JOIN user as u ON u.uid=d.uid'
    . ' LEFT JOIN user as f ON f.uid=d.from_uid'
    . ' LEFT JOIN investments_plan as ip ON ip.recid=d.ipid'
    . " WHERE d.uid='" . $uid . "' AND d.type=" . $type
    . ' ORDER BY d.datetime DESC';
$result = my_query($query);
$i = 0;

// Calculate total earnings
$total_earnings_query = "SELECT SUM(amount) as total FROM income_direct WHERE uid='$uid' AND type=$type";
$total_earnings_result = my_query($total_earnings_query);
$total_earnings_row = mysqli_fetch_object($total_earnings_result);
$total_earnings = $total_earnings_row->total ? $total_earnings_row->total : 0;

// Get total number of transactions
$total_transactions = mysqli_num_rows($result);

// Get unique referrers count
$unique_referrers_query = "SELECT COUNT(DISTINCT from_uid) as count FROM income_direct WHERE uid='$uid' AND type=$type";
$unique_referrers_result = my_query($unique_referrers_query);
$unique_referrers_row = mysqli_fetch_object($unique_referrers_result);
$unique_referrers = $unique_referrers_row->count ? $unique_referrers_row->count : 0;

// Get latest transaction date
$latest_transaction_query = "SELECT MAX(datetime) as latest FROM income_direct WHERE uid='$uid' AND type=$type";
$latest_transaction_result = my_query($latest_transaction_query);
$latest_transaction_row = mysqli_fetch_object($latest_transaction_result);
$latest_transaction = $latest_transaction_row->latest ? date('d M, Y', strtotime($latest_transaction_row->latest)) : 'N/A';
?>


<style>
/* Report UI styles loaded from theme/css/reports-theme.css */
.content-header { display: none !important; }
</style>
<!-- Fresh Direct Container -->
<div class="fresh-container">
    <!-- Fresh Direct Header -->
    <div class="fresh-direct-header">
        <div class="fresh-direct-header-content">
            <div class="fresh-direct-header-icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <h1><?php echo $title; ?></h1>
            <p>Track your referral earnings and bonuses</p>
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

        <!--<div class="fresh-stat-card">-->
        <!--    <div class="fresh-stat-icon">-->
        <!--        <i class="fas fa-users"></i>-->
        <!--    </div>-->
        <!--    <div class="fresh-stat-content">-->
        <!--        <div class="fresh-stat-label">Unique Referrers</div>-->
        <!--        <div class="fresh-stat-value"><?php echo $unique_referrers; ?></div>-->
        <!--    </div>-->
        <!--</div>-->

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

    <!-- Fresh Direct History Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-history"></i>
                Transaction History
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="directSearchInput" class="fresh-search-input" placeholder="Search by name, amount, package...">
            </div>
        </div>
        <div class="fresh-table-container">
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>From User</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Package</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if ($total_transactions > 0) {
                        while ($row = mysqli_fetch_object($result)) {
                            $i++;
                            ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td>
                                <div class="fresh-user-info">
                                    <div class="fresh-user-avatar"><?php echo substr($row->from_name, 0, 1); ?></div>
                                    <div class="fresh-user-details">
                                        <div class="fresh-user-name"><?php echo $row->from_name; ?></div>
                                        <div class="fresh-user-id"><?php echo $row->from_login_id; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fresh-date-display">
                                    <span class="fresh-date-primary"><?php echo date('d M, Y', strtotime($row->datetime)); ?></span>
                                    <span class="fresh-date-secondary"><?php echo date('h:i A', strtotime($row->datetime)); ?></span>
                                </div>
                            </td>
                            <td><span class="fresh-amount-value">$<?php echo number_format($row->amount * 1, 2); ?></span></td>
                            <td><span class="fresh-package-badge"><?php echo $row->title; ?></span></td>
                        </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="5">
                                <div class="fresh-empty-state">
                                    <div class="fresh-empty-icon"><i class="fas fa-hand-holding-usd"></i></div>
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
        <?php if ($total_transactions > 0): ?>
        <div class="fresh-pagination-container">
            <ul class="fresh-pagination">
                <li><a href="#"><i class="fas fa-angle-double-left"></i></a></li>
                <li><a href="#"><i class="fas fa-angle-left"></i></a></li>
                <li class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i></a></li>
                <li><a href="#"><i class="fas fa-angle-double-right"></i></a></li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>
// Fresh Direct Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('directSearchInput');
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
            noResultsRow.innerHTML = `
                <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 1.1rem;">
                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i><br>
                    No direct transactions found matching "${searchTerm}"
                </td>
            `;
            table.appendChild(noResultsRow);
        }
    });

    // Add search icon animation
    searchInput.addEventListener('focus', function() {
        const icon = document.querySelector('.fresh-search-icon');
        icon.style.transform = 'scale(1.1)';
        icon.style.color = 'rgba(255, 255, 255, 1)';
    });

    searchInput.addEventListener('blur', function() {
        const icon = document.querySelector('.fresh-search-icon');
        icon.style.transform = 'scale(1)';
        icon.style.color = 'rgba(255, 255, 255, 0.8)';
    });

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
            icon.style.transform = 'scale(1.1) rotate(5deg)';
            icon.style.animation = 'pulse 2s infinite';
        });

        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.fresh-stat-icon');
            icon.style.transform = 'scale(1) rotate(0deg)';
            icon.style.animation = 'none';
        });
    });

    // Add hover animation to user avatars
    const userAvatars = document.querySelectorAll('.fresh-user-avatar');
    userAvatars.forEach(avatar => {
        avatar.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
            this.style.boxShadow = '0 8px 20px rgba(79, 70, 229, 0.3)';
        });

        avatar.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.boxShadow = 'var(--shadow)';
        });
    });

    // Add click animation to package badges
    const packageBadges = document.querySelectorAll('.fresh-package-badge');
    packageBadges.forEach(badge => {
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
    const headerIcon = document.querySelector('.fresh-direct-header-icon');
    if (headerIcon) {
        setInterval(() => {
            headerIcon.style.transform = 'translateY(-5px)';
            setTimeout(() => {
                headerIcon.style.transform = 'translateY(0px)';
            }, 1000);
        }, 2000);
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

    .fresh-direct-header-icon {
        transition: transform 1s ease-in-out;
    }
`;
document.head.appendChild(style);
</script>

<?php include_once 'footer.php'; ?>