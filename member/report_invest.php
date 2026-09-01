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

// Cap remaining (e.g. $100 invest @ 2x => $200 when no earnings yet)
$trading_investment = get_trading_investment($uid);
$capping_multiplier = get_capping_multiplier($uid);
if ($capping_multiplier <= 0 && $trading_investment > 0) {
    $capping_multiplier = 2;
}
$max_earnable = $trading_investment * $capping_multiplier;
$total_earnings_cap = get_total_earnings($uid);
$remaining_amount = max(0, round($max_earnable - $total_earnings_cap, 2));

// Reset counter
$i = 0;


?>




<style>
/* Report UI styles loaded from theme/css/reports-theme.css */
.content-header { display: none !important; }
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
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Remaining Amount</div>
                <div class="fresh-stat-value fresh-remaining-value">$<?php echo number_format($remaining_amount, 2); ?></div>
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