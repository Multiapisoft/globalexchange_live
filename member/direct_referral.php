<?php
$title = 'Referral';
include_once 'header.php';

// Same as shewin_online_soft — force numeric uid from session
$uid = (int) (isset($_SESSION['userid']) ? $_SESSION['userid'] : $uid);

$query = "SELECT u.uid, u.login_id, u.name, u.mobile, u.datetime, s.login_id as sponsor, p.login_id as placement, u.position, u.package, u.topup, u.teamb, u.trade_status as status, u.topup_datetime as active_date, u.`rank` FROM user as u"
    . " LEFT JOIN user as s ON s.uid=u.refer_id"
    . " LEFT JOIN user as p ON p.uid=u.placement_id"
    . " WHERE u.refer_id = '" . $uid . "'"
    . " ORDER BY u.datetime DESC";
$result = my_query($query);

// Load rows immediately (same display data as shewin)
$result_data = [];
if ($result) {
    while ($row = my_fetch_object($result)) {
        $result_data[] = $row;
    }
}

$total_referrals = count($result_data);

$total_earnings_query = "SELECT SUM(topup) as total FROM user WHERE refer_id = '" . $uid . "'";
$total_earnings_result = my_query($total_earnings_query);
$total_earnings_row = $total_earnings_result ? mysqli_fetch_object($total_earnings_result) : null;
$total_earnings = ($total_earnings_row && $total_earnings_row->total) ? $total_earnings_row->total : 0;
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

    /* Fresh Direct Referral Theme - Same as Dashboard */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        /* Dark Color Palette aligned with member dashboard */
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

    /* Fresh Referral Header */
    .fresh-referral-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        border-radius: var(--radius-lg);
        padding: 32px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .fresh-referral-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .fresh-referral-header-content {
        position: relative;
        z-index: 2;
    }

    .fresh-referral-header h1 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 12px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fresh-referral-header p {
        font-size: 2rem;
        font-weight: 600;
        opacity: 0.9;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .fresh-referral-header-icon {
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
        font-size: 1.6rem;
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

    .fresh-table td {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        color: #eaecef !important;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.2s ease;
        background: var(--bg-secondary);
    }

    .fresh-table td h4,
    .fresh-table .fresh-user-name,
    .fresh-table .fresh-user-id {
        color: #eaecef !important;
    }

    .fresh-table th {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white !important;
        font-size: 0.95rem;
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
        font-size: 2rem;
    }

    .fresh-user-id {
        font-size: 2rem;
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
        font-size: 2rem;
        font-weight: 700;
        box-shadow: var(--shadow);
    }

    /* Fresh Mobile Responsive */
    @media (max-width: 768px) {
        .fresh-container {
            padding: 16px;
        }

        .fresh-referral-header {
            padding: 24px;
        }

        .fresh-referral-header h1 {
            font-size: 2rem;
            font-weight: 900;
        }

        .fresh-referral-header p {
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

    .fresh-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .fresh-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .fresh-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .fresh-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    @media (max-width: 768px) {
        h3 {
            font-size: 16px !important;
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

<!-- Fresh Direct Referral Container -->
<div class="fresh-container">
    <!-- Fresh Referral Header -->
    <div class="fresh-referral-header">
        <div class="fresh-referral-header-content">
            <div class="fresh-referral-header-icon">
                <i class="fas fa-users"></i>
            </div>
            <h1>Direct Referrals</h1>
            <h3>Manage your referral network</h3>
        </div>
    </div>

    <!-- Fresh Stats Grid -->
    <div class="fresh-stats-grid">
        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">
                    <h3>Total Referrals</h3>
                </div>
                <div class="fresh-stat-value"><?php echo $total_referrals; ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">
                    <h3>Total Business</h3>
                </div>
                <div class="fresh-stat-value">$<?php echo number_format($total_earnings, 2); ?></div>
            </div>
        </div>

        <!-- <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label"><h3>Active Members</h3></div>
                <div class="fresh-stat-value"><?php echo $total_referrals; ?></div>
            </div>
        </div> -->
    </div>

    <!-- Fresh Referral List Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-list"></i>
                Referral List
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="referralSearchInput" class="fresh-search-input"
                    placeholder="Search by name, ID, mobile...">
            </div>
        </div>
        <div class="fresh-table-container">
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Joined</th>
                        <th>Activation</th>
                        <th>Package</th>
                        <th>Business</th>
                        <?php /* <th>Leg</th> */ ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($result_data)) {
                        echo '<tr><td colspan="7" style="text-align:center;padding:40px;color:#848e9c;">No direct referrals found.</td></tr>';
                    }

                    $i = 0;
                    foreach ($result_data as $row) {
                        $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td>
                                <div class="fresh-user-info">
                                    <div class="fresh-user-avatar"><?php echo substr($row->name, 0, 1); ?></div>
                                    <div class="fresh-user-details">
                                        <div class="fresh-user-name"><?php echo $row->name; ?></div>
                                        <div class="fresh-user-id"><?php echo $row->login_id; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fresh-user-details">
                                    <div class="fresh-user-name">
                                        <h4><?php echo $row->mobile; ?></h4>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <h4><?php echo date("d M, Y", strtotime($row->datetime)); ?></h4>
                            </td>
                            <td>
                                <h4>
                                    <?php
                                    if (!empty($row->active_date) && $row->active_date !== '0000-00-00 00:00:00' && $row->active_date !== '0000-00-00') {
                                        echo date('d M, Y', strtotime($row->active_date));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </h4>
                            </td>
                            <td>
                                <span class="fresh-package-badge">$<?php echo $row->topup * 1; ?></span>
                            </td>
                            <td>
                                <span class="fresh-package-badge">
                                    $<?php echo number_format($row->teamb + $row->topup, 2); ?>
                                </span>
                            </td>
                            <?php /* 
                            <td>
                                <?php
                                $current_total_business = $row->teamb + $row->topup;
                                $leg_status = 'Other Leg (20%)';
                                $leg_color = '#f59e0b';
                                ?>
                                <div class="fresh-user-details">
                                    <div class="fresh-user-name">
                                        <h4 style="color: <?php echo $leg_color; ?>; font-weight: 700;">
                                            <?php echo $leg_status; ?>
                                        </h4>
                                    </div>
                                </div>
                            </td>
                            */ ?>
                        </tr>
                    <?php } ?>
                </tbody>


            </table>
        </div>
       
    </div>
</div>

<script>
    // Fresh Referral Search Functionality
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('referralSearchInput');
        const table = document.querySelector('.fresh-table tbody');
        const rows = table.querySelectorAll('tr');

        searchInput.addEventListener('input', function () {
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
                    No referrals found matching "${searchTerm}"
                </td>
            `;
                table.appendChild(noResultsRow);
            }
        });

        // Add search icon animation
        searchInput.addEventListener('focus', function () {
            const icon = document.querySelector('.fresh-search-icon');
            icon.style.transform = 'scale(1.1)';
            icon.style.color = 'rgba(255, 255, 255, 1)';
        });

        searchInput.addEventListener('blur', function () {
            const icon = document.querySelector('.fresh-search-icon');
            icon.style.transform = 'scale(1)';
            icon.style.color = 'rgba(255, 255, 255, 0.8)';
        });
    });
</script>

<?php include_once 'footer.php'; ?>