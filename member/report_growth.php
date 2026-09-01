<?php
$type = (isset($_GET['type']) && (int) $_GET['type'] <= 6) ? (int) $_GET['type'] : 0;
$title = ($type == 2) ? "Super Jackpot Income" : (($type == 1) ? "Stacking Referral Income" : "Trading Income");
include_once 'header.php';
$query = "SELECT g.*, ip.title FROM income_growth as g"
        . " LEFT JOIN investments as i ON i.recid=g.iid"
        . " LEFT JOIN investments_plan as ip ON ip.recid=i.ipid"
        . " WHERE g.uid='".$uid."' AND g.type=".$type
        . " ORDER BY g.datetime DESC";
$result = my_query($query);
$i=0;

// Calculate total ROI earnings
$total_earnings_query = "SELECT SUM(amount) as total FROM income_growth WHERE uid='$uid' AND type=$type";
$total_earnings_result = my_query($total_earnings_query);
$total_earnings_row = mysqli_fetch_object($total_earnings_result);
$total_earnings = $total_earnings_row->total ? $total_earnings_row->total : 0;

// Calculate average percentage
$avg_percentage_query = "SELECT AVG(percentage) as avg_percentage FROM income_growth WHERE uid='$uid' AND type=$type";
$avg_percentage_result = my_query($avg_percentage_query);
$avg_percentage_row = mysqli_fetch_object($avg_percentage_result);
$avg_percentage = $avg_percentage_row->avg_percentage ? round($avg_percentage_row->avg_percentage, 2) : 0;

// Get total number of payments
$total_payments = mysqli_num_rows($result);

// Get latest payment date
$latest_payment_query = "SELECT MAX(datetime) as latest FROM income_growth WHERE uid='$uid' AND type=$type";
$latest_payment_result = my_query($latest_payment_query);
$latest_payment_row = mysqli_fetch_object($latest_payment_result);
$latest_payment = $latest_payment_row->latest ? date("d M, Y", strtotime($latest_payment_row->latest)) : 'N/A';
?>


<style>
/* Report UI styles loaded from theme/css/reports-theme.css */
.content-header { display: none !important; }
</style>
<!-- Fresh Growth Container -->
<div class="fresh-container">
    <!-- Fresh Growth Header -->
    <div class="fresh-growth-header">
        <div class="fresh-growth-header-content">
            <div class="fresh-growth-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h1><?php echo $title; ?></h1>
            <p>Track your growth earnings and Arbitradge performance</p>
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

        <!-- <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Average Referral Income</div>
                <div class="fresh-stat-value"><?php echo $avg_percentage; ?>%</div>
            </div>
        </div> -->

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Total Payments</div>
                <div class="fresh-stat-value"><?php echo $total_payments; ?></div>
            </div>
        </div>

        <div class="fresh-stat-card">
            <div class="fresh-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="fresh-stat-content">
                <div class="fresh-stat-label">Latest Payment</div>
                <div class="fresh-stat-value" style="font-size: 1.4rem;"><?php echo $latest_payment; ?></div>
            </div>
        </div>
    </div>

    <!-- Fresh Growth History Card -->
    <div class="fresh-card">
        <div class="fresh-section-header">
            <h2 class="fresh-section-title">
                <i class="fas fa-history"></i>
                Payment History
            </h2>
            <div class="fresh-search-container">
                <i class="fas fa-search fresh-search-icon"></i>
                <input type="text" id="growthSearchInput" class="fresh-search-input" placeholder="Search by amount, date, percentage...">
            </div>
        </div>
        <div class="fresh-table-container">
            <table class="fresh-table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Investment</th>
                        <th>Days</th>
                        <th>Date</th>
                        <th>Trading Income</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($total_payments > 0) {
                        while ($row = mysqli_fetch_object($result)){$i++;?>
                        <tr>
                            <td><?php echo $i;?></td>
                            <td><span class="fresh-plan-badge">$<?php echo number_format($row->iamount, 2);?></span></td>
                            <td><div class="fresh-days-badge"><?php echo $row->days;?></div></td>
                            <td>
                                <div class="fresh-date-display">
                                    <span class="fresh-date-primary"><?php echo date("d M, Y", strtotime($row->datetime));?></span>
                                    <span class="fresh-date-secondary"><?php echo date("h:i A", strtotime($row->datetime));?></span>
                                </div>
                            </td>
                            <td><span class="fresh-amount-value">$<?php echo number_format($row->amount*1, 2);?></span></td>
                            <td><span class="fresh-percentage-badge"><?php echo $row->percentage;?>%</span></td>
                        </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6">
                                <div class="fresh-empty-state">
                                    <div class="fresh-empty-icon"><i class="fas fa-coins"></i></div>
                                    <div class="fresh-empty-text">No payments found</div>
                                    <div class="fresh-empty-subtext">You don't have any <?php echo strtolower($title); ?> payments yet</div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Fresh Pagination -->
        <?php if ($total_payments > 0): ?>
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
document.addEventListener('DOMContentLoaded', function () {
  var searchInput = document.getElementById('growthSearchInput');
  var table = document.querySelector('.fresh-table tbody');
  if (!searchInput || !table) return;
  var rows = table.querySelectorAll('tr');

  searchInput.addEventListener('input', function () {
    var searchTerm = this.value.toLowerCase().trim();
    rows.forEach(function (row) {
      if (row.classList.contains('no-results-row')) return;
      var cells = row.querySelectorAll('td');
      var found = false;
      cells.forEach(function (cell) {
        if (cell.textContent.toLowerCase().indexOf(searchTerm) !== -1) found = true;
      });
      row.style.display = (found || searchTerm === '') ? '' : 'none';
    });

    var existingMessage = table.querySelector('.no-results-row');
    if (existingMessage) existingMessage.remove();

    var visibleRows = Array.prototype.filter.call(rows, function (row) {
      return !row.classList.contains('no-results-row') && row.style.display !== 'none';
    });

    if (visibleRows.length === 0 && searchTerm !== '') {
      var noResultsRow = document.createElement('tr');
      noResultsRow.className = 'no-results-row';
      noResultsRow.innerHTML =
        '<td colspan="6"><div class="fresh-empty-state"><div class="fresh-empty-icon"><i class="fas fa-search"></i></div><div class="fresh-empty-text">No growth payments found matching "' +
        searchTerm.replace(/"/g, '') +
        '"</div></div></td>';
      table.appendChild(noResultsRow);
    }
  });
});
</script>

<?php include_once 'footer.php'; ?>