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
/* Menu and Submenu Text Colors - Light */
#side-menu li a,
#side-menu li a .menu-text,
.nav-second-level li a,
.nav-second-level li a i,
.sidebar .menu-text {
    color: #eaecef !important;
}

/* Fresh P2P Transfer Theme - Same as Other Pages */
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

/* Fresh P2P Header */
.fresh-p2p-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    border-radius: var(--radius-lg);
    padding: 32px;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.fresh-p2p-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

.fresh-p2p-header-content {
    position: relative;
    z-index: 2;
}

.fresh-p2p-header h1 {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.fresh-p2p-header p {
    font-size: 2rem;
    font-weight: 600;
    opacity: 0.9;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-p2p-header-icon {
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

.fresh-stat-icon.success {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
}

.fresh-stat-icon.warning {
    background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
}

.fresh-stat-icon.info {
    background: linear-gradient(135deg, var(--info) 0%, #1d4ed8 100%);
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

.fresh-stat-value.info {
    color: var(--info);
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
    font-size: 1.5rem;
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

/* Fresh Amount Value */
.fresh-amount-value {
    font-weight: 700;
    color: var(--success);
    font-size: 1.2rem;
}

/* Fresh User Badge */
.fresh-user-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 1rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--info) 0%, #1d4ed8 100%);
    color: white;
    box-shadow: var(--shadow);
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Fresh Type Badge */
.fresh-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
    color: white;
    border-radius: 16px;
    font-size: 0.9rem;
    font-weight: 700;
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

    .fresh-p2p-header {
        padding: 24px;
    }

    .fresh-p2p-header h1 {
        font-size: 2rem;
        font-weight: 900;
    }

    .fresh-p2p-header p {
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

    .fresh-user-badge {
        font-size: 0.9rem;
        padding: 6px 12px;
        max-width: 150px;
    }

    /* Mobile table card layout */
    .fresh-table {
        display: block;
        width: 100%;
    }

    .fresh-table thead {
        display: none;
    }

    .fresh-table tbody {
        display: block;
        width: 100%;
    }

    .fresh-table tr {
        display: block;
        width: 100%;
        margin-bottom: 16px;
        background: var(--bg-secondary);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    .fresh-table td {
        display: flex;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        align-items: center;
        text-align: left;
        font-size: 1rem;
    }

    .fresh-table td:before {
        content: attr(data-label);
        width: 40%;
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        padding-right: 12px;
    }

    .fresh-table td:last-child {
        border-bottom: none;
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
