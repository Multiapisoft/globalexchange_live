<?php
$title = 'Matching Income';
include_once 'header.php';
$query = 'SELECT b.amount, b.datetime, b.pair_left, b.pair_right, b.matching, b.left_carry, b.right_carry, b.current_left, b.current_right, b.type, b.self_bv FROM income_binary as b'
    . " WHERE b.uid='" . $uid . "' AND b.type=0"
    . ' ORDER BY b.datetime DESC';
$result = my_query($query);
$i = 0;
?>

<style>
.content-header { display: none !important; }
</style>

<div class="fresh-container ge-report-binary">
  <div class="fresh-growth-header">
    <div class="fresh-growth-header-content">
      <div class="fresh-growth-header-icon"><i class="fas fa-balance-scale"></i></div>
      <h1><?php echo htmlspecialchars($title); ?></h1>
      <p>Matching income history</p>
    </div>
  </div>

  <div class="fresh-card">
    <div class="fresh-section-header">
      <h2 class="fresh-section-title"><i class="fas fa-history"></i> Payment History</h2>
    </div>
    <div class="fresh-table-container">
      <div class="table-responsive" style="margin:0;">
        <table id="dataTableExample1" class="table table-bordered table-striped table-hover fresh-table" style="min-width:720px;margin:0;">
          <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Left</th>
              <th>Right</th>
              <th>Matching</th>
              <th>Left Carry</th>
              <th>Right Carry</th>
              <th><?php echo SITE_CURRENCY; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $has = false;
            while ($row = mysqli_fetch_object($result)) {
                $has = true;
                $i++;
            ?>
            <tr>
              <td><?php echo $i; ?></td>
              <td><?php echo date('d M, Y h:i A', strtotime($row->datetime)); ?></td>
              <td><?php echo $row->pair_left * 1; ?></td>
              <td><?php echo $row->pair_right * 1; ?></td>
              <td><?php echo $row->matching * 1; ?></td>
              <td><?php echo $row->left_carry * 1; ?></td>
              <td><?php echo $row->right_carry * 1; ?></td>
              <td><span class="fresh-amount-value"><?php echo $row->amount * 1; ?></span></td>
            </tr>
            <?php } ?>
            <?php if (!$has): ?>
            <tr>
              <td colspan="8">
                <div class="fresh-empty-state">
                  <div class="fresh-empty-icon"><i class="fas fa-inbox"></i></div>
                  <div class="fresh-empty-text">No matching income found</div>
                </div>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once 'footer.php'; ?>
