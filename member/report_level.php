<?php $type = (isset($_GET['type']) && (int) $_GET['type'] <= 6) ? (int) $_GET['type'] : 0;
$title = ($type == 2) ? "Level Trading Income" : (($type == 1) ? "Level Income" : "subscription package- Generation Distribution");
if($type == 3){
    $title = "Dream Income";
}
elseif($type == 4){
    $title = "Team Trade Bonus";
}
elseif($type == 5){
    $title = "Royal Matrix Income";
}
elseif($type == 6){
    $title = "Crown Matrix Income";
}
include_once 'header.php';

// Get selected level from URL parameter if available
$selected_level = isset($_GET['level']) ? (int)$_GET['level'] : 0;

// Pagination setup
$records_per_page = 20;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Ensure page is at least 1
$offset = ($current_page - 1) * $records_per_page;

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM income_level as l"
        . " LEFT JOIN user as u ON u.uid=l.uid"
        . " LEFT JOIN user as f ON f.uid=l.from_uid"
        . " LEFT JOIN investments_plan as ip ON ip.recid=l.ipid"
        . " WHERE l.uid='".$uid."' AND l.type=".$type;

// If a specific level is selected, filter by that level
if ($selected_level > 0) {
    $count_query .= " AND l.level = $selected_level";
}

$count_result = my_query($count_query);
$count_row = mysqli_fetch_object($count_result);
$total_records = $count_row->total;
$total_pages = ceil($total_records / $records_per_page);

// Main query with pagination
$query = "SELECT l.*, u.login_id, u.name, f.login_id as from_login_id, f.name as from_name, ip.title FROM income_level as l"
        . " LEFT JOIN user as u ON u.uid=l.uid"
        . " LEFT JOIN user as f ON f.uid=l.from_uid"
        . " LEFT JOIN investments_plan as ip ON ip.recid=l.ipid"
        . " WHERE l.uid='".$uid."' AND l.type=".$type;

// If a specific level is selected, filter by that level
if ($selected_level > 0) {
    $query .= " AND l.level = $selected_level";
}

$query .= " ORDER BY l.datetime DESC LIMIT $records_per_page OFFSET $offset";
$result = my_query($query);
$i = $offset; // Start counter from offset for proper numbering

// Calculate total earnings
$total_earnings_query = "SELECT SUM(amount) as total FROM income_level WHERE uid='$uid' AND type=$type";
$total_earnings_result = my_query($total_earnings_query);
$total_earnings_row = mysqli_fetch_object($total_earnings_result);
$total_earnings = $total_earnings_row->total ? $total_earnings_row->total : 0;

// Get total number of transactions (use total_records for pagination)
$total_transactions = $total_records;

// Get unique referrers count
$unique_referrers_query = "SELECT COUNT(DISTINCT from_uid) as count FROM income_level WHERE uid='$uid' AND type=$type";
$unique_referrers_result = my_query($unique_referrers_query);
$unique_referrers_row = mysqli_fetch_object($unique_referrers_result);
$unique_referrers = $unique_referrers_row->count ? $unique_referrers_row->count : 0;

// Get highest level
$highest_level_query = "SELECT MAX(level) as max_level FROM income_level WHERE uid='$uid' AND type=$type";
$highest_level_result = my_query($highest_level_query);
$highest_level_row = mysqli_fetch_object($highest_level_result);
$highest_level = $highest_level_row->max_level ? $highest_level_row->max_level : 0;

// Get latest transaction date
$latest_transaction_query = "SELECT MAX(datetime) as latest FROM income_level WHERE uid='$uid' AND type=$type";
$latest_transaction_result = my_query($latest_transaction_query);
$latest_transaction_row = mysqli_fetch_object($latest_transaction_result);
$latest_transaction = $latest_transaction_row->latest ? date("d M, Y", strtotime($latest_transaction_row->latest)) : 'N/A';

// Get level-wise statistics (for type 4 - Team Trade Bonus)
if ($type == 4) {
    $level_stats = [];
    $max_levels = 10; // L1 to L10

    for ($level = 1; $level <= $max_levels; $level++) {
        // Get total earnings for this level
        $level_earnings_query = "SELECT SUM(amount) as total FROM income_level WHERE uid='$uid' AND type=$type AND level=$level";
        $level_earnings_result = my_query($level_earnings_query);
        $level_earnings_row = mysqli_fetch_object($level_earnings_result);
        $level_earnings = $level_earnings_row->total ? $level_earnings_row->total : 0;

        // Get user count for this level
        $level_users_query = "SELECT COUNT(DISTINCT from_uid) as count FROM income_level WHERE uid='$uid' AND type=$type AND level=$level";
        $level_users_result = my_query($level_users_query);
        $level_users_row = mysqli_fetch_object($level_users_result);
        $level_users = $level_users_row->count ? $level_users_row->count : 0;

        // Get today's earnings for this level
        $today = date('Y-m-d');
        $level_today_query = "SELECT SUM(amount) as total FROM income_level WHERE uid='$uid' AND type=$type AND level=$level AND DATE(datetime)='$today'";
        $level_today_result = my_query($level_today_query);
        $level_today_row = mysqli_fetch_object($level_today_result);
        $level_today = $level_today_row->total ? $level_today_row->total : 0;

        $level_stats[$level] = [
            'earnings' => $level_earnings,
            'users' => $level_users,
            'today' => $level_today
        ];
    }
}
?>


<style>
/* Report UI styles loaded from theme/css/reports-theme.css */
.content-header { display: none !important; }
</style>
<!-- Fresh Level Container -->
<div class="fresh-container">
    <!-- Fresh Level Header -->
    <div class="fresh-level-header">
        <div class="fresh-level-header-content">
            <div class="fresh-level-header-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <h1><?php echo $title; ?></h1>
            <p>Track your level-based income and bonuses</p>
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
                    $<?php
                    // Calculate today's earnings
                    $today = date('Y-m-d');
                    $today_earnings_query = "SELECT SUM(amount) as total FROM income_level WHERE uid='$uid' AND type=$type AND DATE(datetime)='$today'";
                    $today_earnings_result = my_query($today_earnings_query);
                    $today_earnings_row = mysqli_fetch_object($today_earnings_result);
                    $today_earnings = $today_earnings_row->total ? $today_earnings_row->total : 0;
                    echo number_format($today_earnings, 2);
                    ?>
                    <?php if ($today_earnings > 0): ?>
                    <i class="fas fa-arrow-up" style="color: var(--success); font-size: 14px; margin-left: 5px;"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Highest Level</div>
                <div class="fresh-stat-value"><div class="fresh-level-badge"><?php echo $highest_level; ?></div></div>
            </div>
        </div>
    </div>

    <?php if ($type == 4): // Show level cards only for Team Trade Bonus ?>
    <!-- Fresh Level Cards Grid -->
    <div class="fresh-level-cards-grid">
        <?php for ($level = 1; $level <= 10; $level++): ?>
        <div class="fresh-level-card <?php echo ($selected_level == $level) ? 'active' : ''; ?>" onclick="openLevelModal(<?php echo $level; ?>)">
            <div class="fresh-level-number">
                <span>L<?php echo $level; ?></span>
            </div>
            <div class="fresh-level-stats">
                <div class="fresh-level-stat">
                    <div class="fresh-level-stat-label"><i class="fas fa-coins"></i> Earnings</div>
                    <div class="fresh-level-stat-value earnings">$<?php echo number_format($level_stats[$level]['earnings'], 2); ?></div>
                </div>
                <div class="fresh-level-stat">
                    <div class="fresh-level-stat-label"><i class="fas fa-chart-line"></i> Today</div>
                    <div class="fresh-level-stat-value today">
                        $<?php echo number_format($level_stats[$level]['today'], 2); ?>
                        <?php if ($level_stats[$level]['today'] > 0): ?>
                        <i class="fas fa-arrow-up"></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="fresh-level-users">
                <div class="fresh-level-users-count">
                    <i class="fas fa-user"></i> <?php echo $level_stats[$level]['users']; ?> users
                </div>
            </div>
            <div class="fresh-level-card-footer">
                <button class="fresh-view-level-btn" onclick="event.stopPropagation(); openLevelModal(<?php echo $level; ?>)">
                    View Details
                </button>
            </div>
        </div>
        <?php endfor; ?>
    </div>

    <!-- Level Details Modals -->
    <?php for ($level = 1; $level <= 10; $level++): ?>
    <div id="levelModal<?php echo $level; ?>" class="level-modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <div class="level-badge">L<?php echo $level; ?></div>
                    Level <?php echo $level; ?> Team Trade Bonus Details
                </div>
                <button class="modal-close" onclick="closeLevelModal(<?php echo $level; ?>)"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="modal-stats">
                    <div class="modal-stat-card">
                        <div class="modal-stat-label"><i class="fas fa-coins"></i> Total Earnings</div>
                        <div class="modal-stat-value earnings">$<?php echo number_format($level_stats[$level]['earnings'], 2); ?></div>
                    </div>
                    <div class="modal-stat-card">
                        <div class="modal-stat-label"><i class="fas fa-users"></i> Total Users</div>
                        <div class="modal-stat-value"><?php echo $level_stats[$level]['users']; ?></div>
                    </div>
                    <div class="modal-stat-card">
                        <div class="modal-stat-label"><i class="fas fa-calendar-day"></i> Today's Earnings</div>
                        <div class="modal-stat-value">
                            $<?php echo number_format($level_stats[$level]['today'], 2); ?>
                            <?php if ($level_stats[$level]['today'] > 0): ?>
                            <i class="fas fa-arrow-up" style="color: #0ecb81; font-size: 14px; margin-left: 5px;"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="modal-users-list">
                    <div class="modal-users-header">
                        <div class="modal-users-title">
                            <i class="fas fa-user-friends"></i> Level <?php echo $level; ?> Users
                        </div>
                        <div class="modal-users-count">
                            <?php echo $level_stats[$level]['users']; ?> users
                        </div>
                    </div>

                    <?php
                    // Get users for this level
                    $level_users_query = "SELECT l.*, u.login_id, u.name, f.login_id as from_login_id, f.name as from_name
                                         FROM income_level as l
                                         LEFT JOIN user as u ON u.uid=l.uid
                                         LEFT JOIN user as f ON f.uid=l.from_uid
                                         WHERE l.uid='$uid' AND l.type=$type AND l.level=$level
                                         GROUP BY l.from_uid
                                         ORDER BY l.datetime DESC";
                    $level_users_result = my_query($level_users_query);
                    $level_users_count = mysqli_num_rows($level_users_result);
                    ?>

                    <?php if ($level_users_count > 0): ?>
                    <table class="modal-users-table">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Earnings</th>
                                <th>Last Transaction</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = mysqli_fetch_object($level_users_result)):
                                // Get total earnings from this user
                                $user_earnings_query = "SELECT SUM(amount) as total FROM income_level
                                                      WHERE uid='$uid' AND type=$type AND level=$level AND from_uid='$user->from_uid'";
                                $user_earnings_result = my_query($user_earnings_query);
                                $user_earnings_row = mysqli_fetch_object($user_earnings_result);
                                $user_earnings = $user_earnings_row->total ? $user_earnings_row->total : 0;

                                // Get last transaction date
                                $last_tx_query = "SELECT MAX(datetime) as latest FROM income_level
                                                 WHERE uid='$uid' AND type=$type AND level=$level AND from_uid='$user->from_uid'";
                                $last_tx_result = my_query($last_tx_query);
                                $last_tx_row = mysqli_fetch_object($last_tx_result);
                                $last_tx_date = $last_tx_row->latest ? date("d M, Y", strtotime($last_tx_row->latest)) : 'N/A';
                            ?>
                            <tr>
                                <td><?php echo $user->from_login_id; ?></td>
                                <td><?php echo $user->from_name; ?></td>
                                <td>$<?php echo number_format($user_earnings, 2); ?></td>
                                <td><?php echo $last_tx_date; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-users-slash"></i></div>
                        <div class="empty-state-message">No users found for Level <?php echo $level; ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-secondary" onclick="closeLevelModal(<?php echo $level; ?>)">Close</button>
                <a href="report_level.php?type=4&level=<?php echo $level; ?>" class="modal-btn modal-btn-primary">View All Transactions</a>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <!-- Modal JavaScript -->
    <script>
    function openLevelModal(level) {
        document.getElementById('levelModal' + level).classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeLevelModal(level) {
        document.getElementById('levelModal' + level).classList.remove('show');
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.level-modal');
        modals.forEach(function(modal) {
            if (event.target === modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });
    </script>
    <?php endif; ?>

    <!-- Fresh Income History Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-history"></i>
                <?php if ($type == 4 && $selected_level > 0): ?>
                    Level <?php echo $selected_level; ?> Income History
                <?php else: ?>
                    Income History
                <?php endif; ?>
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="levelSearchInput" class="fresh-search-input" placeholder="Search by name, amount, level...">
            </div>
        </div>
        <div class="fresh-table-container">
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>From User</th>
                        <th>Date</th>
                        <?php if($type == 0){?>
                        <th>Package</th>
                        <th>Plan</th>
                        <?php }?>
                        <th>Amount</th>
                        <th><?php echo ($type == 3 || $type == 3) ? 'Pool' : 'Level';?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($total_transactions > 0) {
                        while ($row = mysqli_fetch_object($result)){$i++;?>
                        <tr>
                            <td><?php echo $i;?></td>
                            <td>
                                <div class="fresh-user-info">
                                    <div class="fresh-user-avatar"><?php echo substr($row->from_name, 0, 1); ?></div>
                                    <div class="fresh-user-details">
                                        <div class="fresh-user-name"><?php echo $row->from_name;?></div>
                                        <div class="fresh-user-id"><?php echo $row->from_login_id;?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fresh-date-display">
                                    <span class="fresh-date-primary"><?php echo date("d M, Y", strtotime($row->datetime));?></span>
                                    <span class="fresh-date-secondary"><?php echo date("h:i A", strtotime($row->datetime));?></span>
                                </div>
                            </td>
                            <?php if($type == 0){?>
                            <td><span class="fresh-amount-value">$<?php echo number_format($row->iamount*1, 2);?></span></td>
                            <td><span class="fresh-plan-badge"><?php echo $row->title;?></span></td>
                            <?php }?>
                            <td><span class="fresh-amount-value">$<?php echo number_format(($row->wamt > 0 || 0) ? $row->wamt*1 : $row->amount*1, 2);?></span></td>
                            <td><div class="fresh-level-badge"><?php echo ($type == 3) ? $row->pool : (($type == 3) ? $row->pool-6 : $row->level);?></div></td>
                        </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="<?php echo ($type == 0) ? '7' : '5'; ?>">
                                <div class="fresh-empty-state">
                                    <div class="fresh-empty-icon"><i class="fas fa-layer-group"></i></div>
                                    <div class="fresh-empty-text">No income transactions found</div>
                                    <div class="fresh-empty-subtext">You don't have any <?php echo strtolower($title); ?> transactions yet</div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

                <!-- Empty state (will only show if there are no transactions) -->
                <?php if ($total_transactions == 0): ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="empty-text">No income transactions found</div>
                    <div class="empty-subtext">You don't have any <?php echo strtolower($title); ?> transactions yet</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Fresh Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="fresh-pagination-container">
                <ul class="fresh-pagination">
                    <?php
                    // Build base URL with current parameters
                    $base_url = "report_level.php?type=$type";
                    if ($selected_level > 0) {
                        $base_url .= "&level=$selected_level";
                    }

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
</div>

<script>
// Fresh Level Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('levelSearchInput');
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
                noResultsRow.innerHTML = `
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 1.1rem;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i><br>
                        No level transactions found matching "${searchTerm}"
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

    // Add hover animations to level cards
    const levelCards = document.querySelectorAll('.fresh-level-card');
    levelCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const levelNumber = this.querySelector('.fresh-level-number');
            if (levelNumber) {
                levelNumber.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });

        card.addEventListener('mouseleave', function() {
            const levelNumber = this.querySelector('.fresh-level-number');
            if (levelNumber) {
                levelNumber.style.transform = 'scale(1) rotate(0deg)';
            }
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
    const headerIcon = document.querySelector('.fresh-level-header-icon');
    if (headerIcon) {
        setInterval(() => {
            headerIcon.style.transform = 'translateY(-5px)';
            setTimeout(() => {
                headerIcon.style.transform = 'translateY(0px)';
            }, 1000);
        }, 2000);
    }

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

// Add pulse animation keyframes
const style = document.createElement('style');
style.textContent = `
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

    .fresh-table tbody tr {
        opacity: 0;
        transform: translateY(20px);
    }

    .fresh-table tbody tr.animate-in {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .fresh-level-header-icon {
        transition: transform 1s ease-in-out;
    }
`;
document.head.appendChild(style);
</script>

<?php include_once 'footer.php'; ?>