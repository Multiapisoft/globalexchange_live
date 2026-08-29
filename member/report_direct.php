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
    /* <!-- Fresh Direct Theme - Same as Downline --> */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    /* Fresh Color Palette */
    --bg-primary: #f8fafc;
    --bg-secondary: #ffffff;
    --bg-accent: #e2e8f0;
    --text-primary: #1a202c;
    --text-secondary: #4a5568;
    --text-muted: #718096;
    --brand-primary: #4f46e5;
    --brand-secondary: #7c3aed;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --border: #e5e7eb;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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

/* Fresh Direct Header */
.fresh-direct-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    border-radius: var(--radius-lg);
    padding: 32px;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.fresh-direct-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

.fresh-direct-header-content {
    position: relative;
    z-index: 2;
}

.fresh-direct-header h1 {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.fresh-direct-header p {
    font-size: 2rem;
    font-weight: 600;
    opacity: 0.9;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-direct-header-icon {
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

/* Fresh User Info */
.fresh-user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.fresh-user-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: var(--shadow);
    text-transform: uppercase;
}

.fresh-user-details {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.fresh-user-name {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1.5rem;
}

.fresh-user-id {
    font-size: 1.5rem;
    color: var(--text-muted);
    font-weight: 600;
}

/* Fresh Package Badge */
.fresh-package-badge {
    display: inline-flex;
    align-items: center;
    padding: 10px 16px;
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    color: white;
    border-radius: 20px;
    font-size: 1.05rem;
    font-weight: 700;
    box-shadow: var(--shadow);
}

/* Fresh Amount Value */
.fresh-amount-value {
    font-weight: 700;
    color: var(--warning);
    font-size: 1.2rem;
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
    font-size: 1.15rem;
}

.fresh-date-secondary {
    font-size: 1rem;
    color: var(--text-muted);
    font-weight: 600;
}

/* Fresh Pagination */
.fresh-pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 32px;
    padding: 20px;
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

.fresh-pagination a {
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
}

.fresh-pagination .active a {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    color: white;
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

/* Fresh Mobile Responsive */
@media (max-width: 768px) {
    .fresh-container {
        padding: 16px;
    }

    .fresh-direct-header {
        padding: 24px;
    }

    .fresh-direct-header h1 {
        font-size: 2rem;
        font-weight: 900;
    }

    .fresh-direct-header p {
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