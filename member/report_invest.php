<?php
$type = (isset($_GET['type']) && (int) $_GET['type'] <= 6) ? (int) $_GET['type'] : 0;
$title = ($type == 2) ? "Super Jackpot Income" : (($type == 1) ? "Stacking Investments" : "All Trades");
//$title = "Investments";
include_once 'header.php';

// Get investments data
$query = "SELECT i.*, ip.title FROM investments as i"
    . " LEFT JOIN investments_plan as ip ON ip.recid=i.ipid"
    . " WHERE i.uid='" . $uid . "'";

// $query .= ($type == 1) ? " AND i.ipid >= 6" : " AND i.ipid <= 5";
$query .= " ORDER BY i.datetime DESC";
$result = my_query($query);

// Calculate summary statistics
$total_invested = 0;
$total_remaining = 0;
$total_tokens = 0;
$total_investments = 0;

$temp_result = my_query($query);
while ($row = mysqli_fetch_object($temp_result)) {
    $total_invested += $row->amount;
    $total_remaining += $row->amount2;
    $total_tokens += $row->bonus;
    $total_investments++;
}

// Reset counter
$i = 0;


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

    /* Fresh Investment Theme - Same as Downline */
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

    /* Fresh Investment Header */
    .fresh-investment-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        border-radius: var(--radius-lg);
        padding: 32px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .fresh-investment-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    .fresh-investment-header-content {
        position: relative;
        z-index: 2;
    }

    .fresh-investment-header h1 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 12px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fresh-investment-header p {
        font-size: 2rem;
        font-weight: 600;
        opacity: 0.9;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .fresh-investment-header-icon {
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
        font-size: 1.5rem;
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

    /* Fresh Amount Values */
    .fresh-amount-value {
        font-weight: 700;
        color: var(--success);
        font-size: 2rem;
    }

    .fresh-remaining-value {
        font-weight: 700;
        color: var(--warning);
        font-size: 2rem;
    }

    .fresh-token-value {
        font-weight: 700;
        color: var(--info);
        font-size: 2rem;
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
        font-size: 2rem;
    }

    .fresh-date-secondary {
        font-size: 2rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* Fresh Action Button */
    .fresh-btn-release {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border-radius: 20px;
        font-size: 1.05rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: var(--shadow);
    }

    .fresh-btn-release:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
        text-decoration: none;
    }

    .fresh-btn-release i {
        margin-right: 8px;
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

        .fresh-investment-header {
            padding: 24px;
        }

        .fresh-investment-header h1 {
            font-size: 2rem;
            font-weight: 900;
        }

        .fresh-investment-header p {
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
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


<!-- Fresh Investment Container -->
<div class="fresh-container">
    <!-- Fresh Investment Header -->
    <div class="fresh-investment-header">
        <div class="fresh-investment-header-content">
            <div class="fresh-investment-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h1><?php echo $title; ?></h1>
            <h3>Track your investment portfolio</h3>
        </div>
    </div>

    <!-- Fresh Stats Grid -->
    <div class="fresh-stats-grid">
        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Invested</div>
                <div class="fresh-stat-value earnings">$<?php echo number_format($total_invested, 2); ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Investments</div>
                <div class="fresh-stat-value"><?php echo $total_investments; ?></div>
            </div>
        </div>
    </div>

    <!-- Fresh Investment List Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-list"></i>
                Investment History
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="investmentSearchInput" class="fresh-search-input" placeholder="Search by date, amount...">
            </div>
        </div>
        <div class="fresh-table-container">
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th style="display:none;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_object($result)) {
                            $i++;

                            // echo "<pre>";
                            // print_r($row);
                            // echo "</pre>";
                            // exit;
                    ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><span class="fresh-amount-value">
                                        <h4><?php echo $row->title ?></h4>
                                    </span></td>
                                <td><span class="fresh-amount-value">
                                        <h4>$<?php echo number_format($row->amount * 1, 2); ?></h4>
                                    </span></td>
                                <td>
                                    <div class="fresh-date-display">
                                        <span class="fresh-date-primary">
                                            <h5><?php echo date("d M, Y", strtotime($row->datetime)); ?></h5>
                                        </span>
                                        <span class="fresh-date-secondary">
                                            <h5><?php echo date("h:i A", strtotime($row->datetime)); ?></h5>
                                        </span>
                                    </div>
                                </td>

                                <td style="display:none;">




                                    <!-- <?php
                                            if (($row->ipid == 2 || $row->ipid == 3)) :
                                                //
                                                // Check if user has any investment for this plan
                                                $userInvestment = my_query("SELECT * FROM investments WHERE uid = '$uid' AND ipid IN (2,3) AND ipid = $row->recid  ORDER BY datetime DESC LIMIT 1");
                                                $userdata = mysqli_fetch_object($userInvestment);

                                                // $buttonType = 'completed'; // default: completed (no invest button)
                                                $remainingSeconds = 0;

                                                if ($userdata) {
                                                    $investTime = strtotime($userdata->datetime);
                                                    $cycleHours = (int)$userdata->invest_hour; // from DB: 1,3,5,24 etc.
                                                    $elapsed = time() - $investTime;
                                                    if ($userdata->is_closed == 1) {
                                                        $buttonType = 'completed'; // completed
                                                    } elseif ($elapsed < ($cycleHours * 3600)) {
                                                        $buttonType = 'running'; // active
                                                        $remainingSeconds = ($cycleHours * 3600) - $elapsed;
                                                    } else {
                                                        $buttonType = 'closed'; // ready to close
                                                    }
                                                }

                                                // Render button logic
                                                if ($buttonType == 'running'): ?>
                                            <button id="invest-btn-<?php echo $row->recid; ?>" class="btn btn-success btn-lg" disabled style="border-radius:25px;">
                                                <i class="fas fa-clock"></i> Left — <span id="countdown-<?php echo $row->recid; ?>"></span>
                                            </button>

                                            <script>
                                                let remaining<?php echo $row->recid; ?> = <?php echo $remainingSeconds; ?>;
                                                const countdownEl<?php echo $row->recid; ?> = document.getElementById("countdown-<?php echo $row->recid; ?>");
                                                const btnEl<?php echo $row->recid; ?> = document.getElementById("invest-btn-<?php echo $row->recid; ?>");

                                                function updateCountdown<?php echo $row->recid; ?>() {
                                                    if (remaining<?php echo $row->recid; ?> <= 0) {
                                                        countdownEl<?php echo $row->recid; ?>.innerHTML = "00h 00m 00s";
                                                        btnEl<?php echo $row->recid; ?>.innerHTML = '<i class="fas fa-times-circle"></i> Closed';
                                                        btnEl<?php echo $row->recid; ?>.disabled = true;
                                                        btnEl<?php echo $row->recid; ?>.classList.remove('btn-success');
                                                        btnEl<?php echo $row->recid; ?>.classList.add('btn-danger');
                                                        return;
                                                    }
                                                    let hours = Math.floor(remaining<?php echo $row->recid; ?> / 3600);
                                                    let minutes = Math.floor((remaining<?php echo $row->recid; ?> % 3600) / 60);
                                                    let seconds = remaining<?php echo $row->recid; ?> % 60;
                                                    countdownEl<?php echo $row->recid; ?>.innerHTML = `${hours.toString().padStart(2,'0')}h ${minutes.toString().padStart(2,'0')}m ${seconds.toString().padStart(2,'0')}s`;
                                                    remaining<?php echo $row->recid; ?>--;
                                                }

                                                updateCountdown<?php echo $row->recid; ?>();
                                                setInterval(updateCountdown<?php echo $row->recid; ?>, 1000);
                                            </script>

                                        <?php elseif ($buttonType == 'closed' && $userdata->is_closed == 0): ?>
                                            <form action="self_treding_model.php" method="post">
                                                <input type="hidden" name="investment_id" value="<?php echo $userdata->recid; ?>">
                                                <button type="submit" name="submit-button" style="border-radius:25px;" class="btn btn-danger btn-lg">
                                                    <i class="fas fa-times-circle"></i> Close
                                                </button>
                                            </form>

                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-lg" disabled style="border-radius:25px;">
                                                <i class="fas fa-check-circle"></i> Completed
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        --
                                    <?php endif; ?> -->




                                    <?php
                                    /* Action/Closed column - commented out
                                    if (($row->ipid == 2 ) && $row->is_closed == 0 && $row->amount2 > 0) :
                                        //
                                        // Check if user has any investment for this plan
                                        // CHANGE: Fixed query to use $row->ipid instead of $row->recid for correct matching of investment type (ipid 2/3)
                                        $userInvestment = my_query("SELECT * FROM investments WHERE uid = '$uid' AND ipid IN (2,3) AND ipid = $row->ipid  ORDER BY datetime DESC LIMIT 1");
                                        $userdata = mysqli_fetch_object($userInvestment);

                                        // $buttonType = 'completed'; // default: completed (no invest button)
                                        $remainingSeconds = 0;

                                        if ($userdata) {
                                            $investTime = strtotime($userdata->datetime);
                                            $cycleHours = (int)$userdata->invest_hour; // from DB: 1,3,5,24 etc.
                                            $elapsed = time() - $investTime;
                                            // if ($userdata->is_closed == 1) {
                                            //     $buttonType = 'completed'; // completed
                                            // } 

                                            if ($elapsed < ($cycleHours * 3600)) {
                                                $buttonType = 'running'; // active
                                                $remainingSeconds = ($cycleHours * 3600) - $elapsed;
                                            } else {
                                                $buttonType = 'closed'; // ready to close
                                            }
                                        }

                                        // Render button logic
                                        // CHANGE: Added isset($buttonType) checks to handle cases where no userdata exists (avoids undefined variable errors)
                                        if (isset($buttonType) && $buttonType == 'running'): ?>
                                            <!-- Hidden form for closing investment -->
                                            <form id="close-form-<?php echo $row->recid; ?>" action="self_treding_model.php" method="post" style="display: none;">
                                                <input type="hidden" name="investment_id" value="<?php echo $userdata->recid; ?>">
                                                <input type="hidden" name="submit-button" value="1">
                                            </form>

                                            <button id="invest-btn-<?php echo $row->recid; ?>" class="btn btn-success btn-lg" disabled style="border-radius:25px;">
                                                <i class="fas fa-clock"></i> Left — <span id="countdown-<?php echo $row->recid; ?>"></span>
                                            </button>

                                            <script>
                                                let remaining<?php echo $row->recid; ?> = <?php echo $remainingSeconds; ?>;
                                                const countdownEl<?php echo $row->recid; ?> = document.getElementById("countdown-<?php echo $row->recid; ?>");
                                                const btnEl<?php echo $row->recid; ?> = document.getElementById("invest-btn-<?php echo $row->recid; ?>");

                                                function updateCountdown<?php echo $row->recid; ?>() {
                                                    if (remaining<?php echo $row->recid; ?> <= 0) {
                                                        countdownEl<?php echo $row->recid; ?>.innerHTML = "00h 00m 00s";
                                                        btnEl<?php echo $row->recid; ?>.innerHTML = '<i class="fas fa-times-circle"></i> Close';
                                                        btnEl<?php echo $row->recid; ?>.disabled = false;
                                                        btnEl<?php echo $row->recid; ?>.classList.remove('btn-success');
                                                        btnEl<?php echo $row->recid; ?>.classList.add('btn-danger');
                                                        btnEl<?php echo $row->recid; ?>.onclick = function() {
                                                            closeInvestment<?php echo $row->recid; ?>();
                                                        };
                                                        return;
                                                    }
                                                    let hours = Math.floor(remaining<?php echo $row->recid; ?> / 3600);
                                                    let minutes = Math.floor((remaining<?php echo $row->recid; ?> % 3600) / 60);
                                                    let seconds = remaining<?php echo $row->recid; ?> % 60;
                                                    countdownEl<?php echo $row->recid; ?>.innerHTML = `${hours.toString().padStart(2,'0')}h ${minutes.toString().padStart(2,'0')}m ${seconds.toString().padStart(2,'0')}s`;
                                                    remaining<?php echo $row->recid; ?>--;
                                                }

                                                function closeInvestment<?php echo $row->recid; ?>() {
                                                    document.getElementById('close-form-<?php echo $row->recid; ?>').submit();
                                                }

                                                updateCountdown<?php echo $row->recid; ?>();
                                                setInterval(updateCountdown<?php echo $row->recid; ?>, 1000);
                                            </script>

                                        <?php elseif (isset($buttonType) && $buttonType == 'closed' && $userdata->is_closed == 0): ?>
                                            <form action="self_treding_model.php" method="post">
                                                <input type="hidden" name="investment_id" value="<?php echo $userdata->recid; ?>">
                                                <button type="submit" name="submit-button" style="border-radius:25px;" class="btn btn-danger btn-lg">
                                                    <i class="fas fa-times-circle"></i> Close
                                                </button>
                                            </form>

                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-lg" disabled style="border-radius:25px;">
                                                <i class="fas fa-check-circle"></i> Completed
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        --
                                    <?php endif; ?>
                                    */
                                    ?>
                                        --
                                </td>

                                <!--<td>-->
                                <!--    <?php if ($row->status == 0 && $row->amount != 0 && $row->amount2 != 0) { ?>-->
                                <!--        <a href="release.php?recid=<?php echo $row->recid; ?>" onclick="return confirm('Are you sure?');" class="fresh-btn-release">-->
                                <!--            <i class="fas fa-unlock"></i> Release-->
                                <!--        </a>-->
                                <!--    <?php } else { ?>-->
                                <!--        <span style="color: var(--text-muted); font-weight: 600;">-</span>-->
                                <!--    <?php } ?>-->
                                <!--</td>-->
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="4">
                                <div class="fresh-empty-state">
                                    <div class="fresh-empty-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                    <div class="fresh-empty-text">No investments found</div>
                                    <div class="fresh-empty-subtext">You don't have any investments in this category yet</div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    // Fresh Investment Search Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('investmentSearchInput');
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
                <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 1.1rem;">
                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i><br>
                    No investments found matching "${searchTerm}"
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

        // Add click animation to release buttons
        const releaseButtons = document.querySelectorAll('.fresh-btn-release');
        releaseButtons.forEach(button => {
            button.addEventListener('click', function(e) {
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
`;
    document.head.appendChild(style);
</script>

<?php include_once 'footer.php'; ?>