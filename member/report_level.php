<?php $type = (isset($_GET['type']) && (int) $_GET['type'] <= 6) ? (int) $_GET['type'] : 0;
$title = ($type == 2) ? "Level Trubot Income" : (($type == 1) ? "Level Income" : "subscription package- Generation Distribution");
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
/* Menu and Submenu Text Colors - Light */
#side-menu li a,
#side-menu li a .menu-text,
.nav-second-level li a,
.nav-second-level li a i,
.sidebar .menu-text {
    color: #eaecef !important;
}

/* Fresh Level Theme - Same as Downline */
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

/* Fresh Level Cards Grid */
.fresh-level-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.fresh-level-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 24px;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid var(--border);
    cursor: pointer;
    animation: fadeIn 0.5s ease-out;
    box-shadow: var(--shadow);
    transform: translateZ(0);
    backface-visibility: hidden;
}

.fresh-level-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-lg);
    border-color: var(--brand-primary);
}

.fresh-level-card.active {
    border-color: var(--brand-primary);
    box-shadow: 0 0 20px rgba(79, 70, 229, 0.3);
    background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-accent) 100%);
}

    .fresh-level-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--brand-primary), transparent);
        opacity: 0.5;
        transition: opacity 0.3s ease;
    }

    .fresh-level-card:hover::before,
    .fresh-level-card.active::before {
        opacity: 1;
    }

    .fresh-level-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 0;
        transition: all 0.5s ease;
    }

    .fresh-level-card:hover::after {
        transform: scale(1.2) translateX(10px) translateY(10px);
        opacity: 0.8;
    }

    .fresh-level-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        border-radius: 50%;
        margin-bottom: 15px;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .fresh-level-card:hover .fresh-level-number {
        transform: scale(1.1);
        box-shadow: var(--shadow-lg);
    }

    .fresh-level-number::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.3), transparent);
        opacity: 0;
        z-index: -1;
        transition: opacity 0.3s ease;
    }

    .fresh-level-card:hover .fresh-level-number::before {
        opacity: 1;
        animation: rotate 3s linear infinite;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .fresh-level-number span {
        font-size: 2rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Fresh Level Header */
    .fresh-level-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        border-radius: var(--radius-lg);
        padding: 32px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .fresh-level-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    .fresh-level-header-content {
        position: relative;
        z-index: 2;
    }

    .fresh-level-header h1 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 12px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fresh-level-header p {
        font-size: 2rem;
        font-weight: 600;
        opacity: 0.9;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .fresh-level-header-icon {
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

    .fresh-level-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        font-weight: 700;
        font-size: 2rem;
        box-shadow: var(--shadow);
    }

    /* Fresh Level Card Components */
    .fresh-level-stats {
        position: relative;
        z-index: 1;
    }

    .fresh-level-stat {
        margin-bottom: 12px;
        position: relative;
        transition: all 0.3s ease;
    }

    .fresh-level-card:hover .fresh-level-stat {
        transform: translateX(5px);
    }

    .fresh-level-stat-label {
        font-size: 2rem;
        color: var(--text-muted);
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color 0.3s ease;
    }

    .fresh-level-card:hover .fresh-level-stat-label {
        color: var(--text-secondary);
    }

    .fresh-level-stat-label i {
        color: var(--brand-primary);
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .fresh-level-card:hover .fresh-level-stat-label i {
        transform: scale(1.2);
    }

    .fresh-level-stat-value {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .fresh-level-stat-value.earnings {
        color: var(--success);
    }

    .fresh-level-stat-value.today {
        color: var(--warning);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .fresh-level-stat-value.today i {
        font-size: 12px;
        animation: bounceUp 2s infinite;
    }

    @keyframes bounceUp {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .fresh-level-users {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 15px;
        transition: all 0.3s ease;
    }

    .fresh-level-card:hover .fresh-level-users {
        transform: translateX(5px);
    }

    .fresh-level-users-count {
        background: var(--bg-accent);
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12px;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .fresh-level-card:hover .fresh-level-users-count {
        background: var(--border);
        box-shadow: var(--shadow-lg);
    }

    .fresh-level-users-count i {
        color: var(--brand-primary);
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .fresh-level-card:hover .fresh-level-users-count i {
        transform: scale(1.2);
    }

    .fresh-level-card-footer {
        margin-top: 20px;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .fresh-view-level-btn {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        border: none;
        border-radius: 20px;
        padding: 8px 15px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .fresh-view-level-btn:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .fresh-level-card.active .fresh-view-level-btn {
        box-shadow: 0 0 10px rgba(79, 70, 229, 0.3);
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
        font-size: 1.6rem;
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
        font-size: 1.15rem;
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
        font-size: 1.1rem;
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
        font-size: 1.15rem;
    }

    .fresh-user-id {
        font-size: 1rem;
        color: var(--text-muted);
        font-weight: 600;
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

    /* Fresh Amount Value */
    .fresh-amount-value {
        font-weight: 700;
        color: var(--success);
        font-size: 1.2rem;
    }

    /* Fresh Plan Badge */
    .fresh-plan-badge {
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

        .fresh-level-header {
            padding: 24px;
        }

        .fresh-level-header h1 {
            font-size: 2rem;
            font-weight: 900;
        }

        .fresh-level-header p {
            font-size: 1rem;
            font-weight: 600;
        }

        .fresh-stats-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .fresh-level-cards-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
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

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .fresh-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .fresh-card:nth-child(1) { animation-delay: 0.1s; }
    .fresh-card:nth-child(2) { animation-delay: 0.2s; }
    .fresh-card:nth-child(3) { animation-delay: 0.3s; }

    /* Animation delay for level cards to create a cascade effect */
    .fresh-level-cards-grid .fresh-level-card:nth-child(1) { animation-delay: 0.05s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(2) { animation-delay: 0.1s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(3) { animation-delay: 0.15s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(4) { animation-delay: 0.2s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(5) { animation-delay: 0.25s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(6) { animation-delay: 0.3s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(7) { animation-delay: 0.35s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(8) { animation-delay: 0.4s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(9) { animation-delay: 0.45s; }
    .fresh-level-cards-grid .fresh-level-card:nth-child(10) { animation-delay: 0.5s; }

    .level-stats {
        position: relative;
        z-index: 1;
    }

    .level-stat {
        margin-bottom: 12px;
        position: relative;
        transition: all 0.3s ease;
    }

    .level-card:hover .level-stat {
        transform: translateX(5px);
    }

    .level-stat-label {
        font-size: 12px;
        color: #848e9c;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color 0.3s ease;
    }

    .level-card:hover .level-stat-label {
        color: #a0a9b8;
    }

    .level-stat-label i {
        color: #f0b90b;
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .level-card:hover .level-stat-label i {
        transform: scale(1.2);
    }

    .level-stat-value {
        font-size: 18px;
        font-weight: 600;
        color: #fff;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .level-stat-value.earnings {
        color: #0ecb81;
        text-shadow: 0 0 10px rgba(14, 203, 129, 0.3);
    }

    .level-card:hover .level-stat-value.earnings {
        text-shadow: 0 0 15px rgba(14, 203, 129, 0.5);
    }

    .level-stat-value.today {
        color: #f0b90b;
        display: flex;
        align-items: center;
        gap: 5px;
        text-shadow: 0 0 10px rgba(240, 185, 11, 0.3);
    }

    .level-card:hover .level-stat-value.today {
        text-shadow: 0 0 15px rgba(240, 185, 11, 0.5);
    }

    .level-stat-value.today i {
        font-size: 12px;
        animation: bounceUp 2s infinite;
    }

    @keyframes bounceUp {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .level-users {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 15px;
        transition: all 0.3s ease;
    }

    .level-card:hover .level-users {
        transform: translateX(5px);
    }

    .level-users-count {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12px;
        color: #eaecef;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .level-card:hover .level-users-count {
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .level-users-count i {
        color: #f0b90b;
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .level-card:hover .level-users-count i {
        transform: scale(1.2);
    }

    .level-card-footer {
        margin-top: 20px;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .view-level-btn {
        background: rgba(240, 185, 11, 0.1);
        color: #f0b90b;
        border: 1px solid rgba(240, 185, 11, 0.2);
        border-radius: 20px;
        padding: 8px 15px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .view-level-btn::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: rotate(30deg);
        transition: all 0.5s ease;
        opacity: 0;
    }

    .view-level-btn:hover::before {
        opacity: 1;
        animation: shimmerEffect 1.5s infinite;
    }

    @keyframes shimmerEffect {
        0% { transform: translateX(-100%) rotate(30deg); }
        100% { transform: translateX(100%) rotate(30deg); }
    }

    .view-level-btn:hover {
        background: rgba(240, 185, 11, 0.2);
        border-color: rgba(240, 185, 11, 0.3);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .level-card.active .view-level-btn {
        background: rgba(240, 185, 11, 0.2);
        border-color: rgba(240, 185, 11, 0.3);
        box-shadow: 0 0 10px rgba(240, 185, 11, 0.2);
    }

    /* Responsive adjustments for level cards */
    @media (max-width: 1200px) {
        .level-cards-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .level-cards-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
        }

        .level-card {
            padding: 15px;
        }

        .level-number {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }

        .level-number span {
            font-size: 16px;
        }

        .level-stat-value {
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .level-cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .level-card {
            padding: 12px;
        }

        .level-number {
            width: 36px;
            height: 36px;
        }

        .level-number span {
            font-size: 14px;
        }

        .level-stat-value {
            font-size: 14px;
        }

        .view-level-btn {
            padding: 6px 12px;
            font-size: 11px;
        }
    }

    /* Level Details Modal */
    .level-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        overflow-y: auto;
        backdrop-filter: blur(5px);
        animation: fadeIn 0.3s ease-out;
    }

    .level-modal.show {
        display: block;
    }

    .modal-content {
        background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
        border-radius: 16px;
        margin: 50px auto;
        width: 90%;
        max-width: 800px;
        position: relative;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.05);
        overflow: hidden;
        animation: slideUp 0.4s ease-out;
    }

    .modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(0, 0, 0, 0.2);
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #f0b90b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-title .level-badge {
        width: 36px;
        height: 36px;
        font-size: 14px;
        background: rgba(240, 185, 11, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f0b90b;
        font-weight: 700;
        box-shadow: 0 0 15px rgba(240, 185, 11, 0.2);
        position: relative;
        overflow: hidden;
    }

    .modal-title .level-badge::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: rotate(30deg);
        animation: shimmerEffect 2s infinite;
    }

    .modal-close {
        background: rgba(255, 255, 255, 0.05);
        border: none;
        color: #848e9c;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 25px;
    }

    .modal-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .modal-stat-card {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        padding: 15px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .modal-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #f0b90b, transparent);
    }

    .modal-stat-label {
        font-size: 12px;
        color: #848e9c;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .modal-stat-label i {
        color: #f0b90b;
    }

    .modal-stat-value {
        font-size: 20px;
        font-weight: 600;
        color: #fff;
    }

    .modal-stat-value.earnings {
        color: #0ecb81;
    }

    .modal-users-list {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .modal-users-header {
        padding: 15px 20px;
        background: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-users-title {
        font-size: 16px;
        font-weight: 500;
        color: #eaecef;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-users-title i {
        color: #f0b90b;
    }

    .modal-users-count {
        background: rgba(240, 185, 11, 0.1);
        color: #f0b90b;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .modal-users-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modal-users-table th {
        background: rgba(0, 0, 0, 0.2);
        color: #848e9c;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .modal-users-table td {
        padding: 12px 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: #eaecef;
        font-size: 14px;
    }

    .modal-users-table tr:last-child td {
        border-bottom: none;
    }

    .modal-users-table tr:hover td {
        background: rgba(255, 255, 255, 0.03);
    }

    .modal-footer {
        padding: 20px 25px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        background: rgba(0, 0, 0, 0.2);
    }

    .modal-btn {
        padding: 8px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .modal-btn-primary {
        background: linear-gradient(135deg, #f0b90b, #f8d33a);
        color: #000;
        border: none;
    }

    .modal-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(240, 185, 11, 0.3);
    }

    .modal-btn-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: #eaecef;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modal-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            margin: 30px auto;
        }

        .modal-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 15px;
        }
    }

    @media (max-width: 480px) {
        .modal-content {
            margin: 15px auto;
        }

        .modal-stats {
            grid-template-columns: 1fr;
        }

        .modal-users-table {
            display: block;
            overflow-x: auto;
        }
    }

    /* Income Header */
    .income-header {
        background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        animation: slideInFade 0.8s ease-out;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .income-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #f0b90b, transparent);
    }

    .income-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: radial-gradient(ellipse at bottom, rgba(240, 185, 11, 0.1), transparent 70%);
        pointer-events: none;
    }

    .income-title {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .income-title h2 {
        font-size: 24px;
        color: #fff;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .income-title h2 i {
        margin-right: 12px;
        color: #f0b90b;
    }

    .income-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        padding: 15px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
        z-index: -1;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .stat-label {
        font-size: 14px;
        color: #848e9c;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .stat-label i {
        margin-right: 8px;
        color: #f0b90b;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #fff;
        display: flex;
        align-items: baseline;
    }

    .stat-value.earnings {
        color: #0ecb81;
    }

    .stat-value .currency {
        font-size: 14px;
        margin-right: 4px;
        opacity: 0.7;
    }

    .stat-value .unit {
        font-size: 14px;
        margin-left: 4px;
        opacity: 0.7;
    }

    /* Income Card */
    .income-card {
        background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
        border-radius: 12px;
        margin-bottom: 25px;
        overflow: hidden;
        animation: slideUp 0.6s ease-out;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.05);
        position: relative;
    }

    .income-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, #f0b90b, transparent);
    }

    .card-header {
        background: rgba(0, 0, 0, 0.2);
        padding: 15px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header h3 {
        margin: 0;
        font-size: 16px;
        color: #f0b90b;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .card-header h3 i {
        margin-right: 10px;
    }

    .card-header-actions {
        display: flex;
        gap: 10px;
    }

    .card-filter {
        background: rgba(255, 255, 255, 0.05);
        border: none;
        color: #eaecef;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .card-filter:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .card-body {
        padding: 0;
    }

    /* Table Styling */
    .income-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .income-table th {
        background: rgba(0, 0, 0, 0.2);
        color: #848e9c;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .income-table td {
        padding: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: #eaecef;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .income-table tr:last-child td {
        border-bottom: none;
    }

    .income-table tr:hover td {
        background: rgba(255, 255, 255, 0.03);
    }

    /* User Info */
    .user-info {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(45deg, #f0b90b, #f8d33a);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #0b0e11;
        font-weight: bold;
        margin-right: 10px;
        position: relative;
        overflow: hidden;
    }

    .user-avatar::after {
        content: '';
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
        transform: rotate(45deg);
    }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 500;
        color: #fff;
    }

    .user-id {
        font-size: 12px;
        color: #848e9c;
    }

    /* Plan Badge */
    .plan-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        background-color: rgba(240, 185, 11, 0.1);
        color: #f0b90b;
        border: 1px solid rgba(240, 185, 11, 0.2);
    }

    /* Amount Value */
    .amount-value {
        font-weight: 600;
        color: #0ecb81;
    }

    /* Level Badge */
    .level-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(240, 185, 11, 0.15);
        color: #f0b90b;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 0 15px rgba(240, 185, 11, 0.2);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .level-badge::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: rotate(30deg);
        animation: shimmerEffect 2s infinite;
    }

    .stat-card:hover .level-badge {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(240, 185, 11, 0.3);
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

    /* Level Summary in Card Header */
    .back-link {
        font-size: 12px;
        color: #848e9c;
        margin-left: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 255, 255, 0.05);
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #f0b90b;
    }

    .level-summary {
        display: flex;
        gap: 15px;
    }

    .level-summary-item {
        display: flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 255, 255, 0.05);
        padding: 5px 10px;
        border-radius: 4px;
    }

    .summary-label {
        font-size: 12px;
        color: #848e9c;
    }

    .summary-value {
        font-size: 14px;
        font-weight: 600;
        color: #eaecef;
    }

    .summary-value.earnings {
        color: #0ecb81;
    }

    /* Empty State */
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #848e9c;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 20px;
        color: rgba(240, 185, 11, 0.3);
    }

    .empty-text {
        font-size: 16px;
        margin-bottom: 15px;
    }

    .empty-subtext {
        font-size: 14px;
        opacity: 0.7;
        max-width: 400px;
        margin: 0 auto;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .income-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .income-header {
            padding: 20px;
        }

        .income-table {
            display: block;
            overflow-x: auto;
        }

        .income-stats {
            grid-template-columns: 1fr;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .card-header-actions {
            width: 100%;
            justify-content: space-between;
        }
    }

    /* Animations */
    @keyframes slideInFade {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(240, 185, 11, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(240, 185, 11, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(240, 185, 11, 0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Animation delay for level cards to create a cascade effect */
    .level-cards-grid .level-card:nth-child(1) { animation-delay: 0.05s; }
    .level-cards-grid .level-card:nth-child(2) { animation-delay: 0.1s; }
    .level-cards-grid .level-card:nth-child(3) { animation-delay: 0.15s; }
    .level-cards-grid .level-card:nth-child(4) { animation-delay: 0.2s; }
    .level-cards-grid .level-card:nth-child(5) { animation-delay: 0.25s; }
    .level-cards-grid .level-card:nth-child(6) { animation-delay: 0.3s; }
    .level-cards-grid .level-card:nth-child(7) { animation-delay: 0.35s; }
    .level-cards-grid .level-card:nth-child(8) { animation-delay: 0.4s; }
    .level-cards-grid .level-card:nth-child(9) { animation-delay: 0.45s; }
    .level-cards-grid .level-card:nth-child(10) { animation-delay: 0.5s; }
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