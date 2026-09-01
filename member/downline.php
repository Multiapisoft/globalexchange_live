<?php
$title = "Team";
include_once 'header.php';
$i = 0;
$j = 0;
$child_levels = get_child_levels_refer_($uid, $with = 'yes');

// Calculate total team members
$total_members = 0;
foreach ($child_levels as $level) {
    $total_members += count($level);
}

// Calculate levels
$total_levels = count($child_levels);

// Calculate total earnings (example calculation)
$total_earnings = 0;
foreach ($child_levels as $level) {
    $uids = implode(",", $level);
    if (!empty($uids)) {
        $earnings_query = "SELECT SUM(topup) as total FROM user WHERE uid IN ($uids)";
        $earnings_result = my_query($earnings_query);
        $earnings_row = mysqli_fetch_object($earnings_result);
        $total_earnings += ($earnings_row->total ? $earnings_row->total : 0);
    }
}

$referral_link = SITE_URL . '/soft/member/register.php?ref=' . (int) $uid;
?>

<style>
/* Downline / Team — match network.html + direct_referral theme */
.content-header { display: none !important; }

.ge-net {
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 0.25rem 0 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  box-sizing: border-box;
  font-family: "Montserrat", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
  color: #fff;
  font-size: 16px;
  line-height: 1.55;
  -webkit-font-smoothing: antialiased;
}
.ge-net *,
.ge-net *::before,
.ge-net *::after { box-sizing: border-box; }

.ge-net-head h1 {
  margin: 0;
  font-size: clamp(1.15rem, 2.5vw, 1.35rem);
  font-weight: 700;
  color: #fff;
}
.ge-net-head p {
  margin: 0.25rem 0 0;
  font-size: 0.8rem;
  color: #9ca3af;
}

.ge-panel {
  border-radius: 14px;
  border: 1px solid rgba(212, 175, 55, 0.22);
  background: #141414;
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.35);
  overflow: hidden;
}
.ge-panel-pad {
  padding: 1.25rem 1.35rem;
}
@media (min-width: 640px) {
  .ge-panel-pad { padding: 1.5rem 1.75rem; }
}

.ge-ref-top {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
@media (min-width: 1024px) {
  .ge-ref-top {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }
}

.ge-label-gold {
  margin: 0 0 0.5rem;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #d4af37;
}
.ge-ref-top .sub {
  margin: 0 0 0.75rem;
  font-size: 0.9rem;
  color: #9ca3af;
}

.ge-copy-row {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
@media (min-width: 640px) {
  .ge-copy-row {
    flex-direction: row;
    align-items: stretch;
  }
}
.ge-copy-row input {
  flex: 1;
  min-width: 0;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.28);
  background: #0a0a0a;
  color: #e5e5e5;
  padding: 0.75rem 0.9rem;
  font-size: 0.85rem;
  font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
}
.ge-btn-gold {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  flex-shrink: 0;
  padding: 0.75rem 1.25rem;
  border: none;
  border-radius: 12px;
  font-family: inherit;
  font-size: 0.92rem;
  font-weight: 700;
  color: #1a1408;
  cursor: pointer;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  box-shadow: 0 6px 18px rgba(212, 175, 55, 0.28);
}
.ge-btn-gold:hover { filter: brightness(1.06); }

.ge-mini-stats {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.75rem;
  min-width: 0;
}
@media (min-width: 1024px) {
  .ge-mini-stats { min-width: 280px; }
}
.ge-mini-stat {
  border-radius: 12px;
  border: 1px solid rgba(212, 175, 55, 0.2);
  padding: 0.75rem 0.5rem;
  text-align: center;
  background: rgba(255, 255, 255, 0.02);
}
.ge-mini-stat .n {
  margin: 0;
  font-size: clamp(1.15rem, 3vw, 1.5rem);
  font-weight: 800;
  letter-spacing: -0.02em;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.ge-mini-stat .n.silver {
  background: none;
  -webkit-background-clip: unset;
  background-clip: unset;
  color: #c0c0c0;
}
.ge-mini-stat .l {
  margin: 0.35rem 0 0;
  font-size: 0.65rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #9ca3af;
  font-weight: 600;
}

.ge-section-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #fff;
}
.ge-section-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1.1rem 1.25rem;
  border-bottom: 1px solid rgba(212, 175, 55, 0.12);
}

.ge-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.28rem 0.7rem;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.03em;
}
.ge-badge-gold {
  background: rgba(212, 175, 55, 0.15);
  color: #f5c842;
  border: 1px solid rgba(212, 175, 55, 0.4);
}
.ge-badge-level {
  background: rgba(99, 102, 241, 0.15);
  color: #a5b4fc;
  border: 1px solid rgba(99, 102, 241, 0.35);
}
.ge-badge-success {
  background: rgba(34, 197, 94, 0.15);
  color: #22c55e;
  border: 1px solid rgba(34, 197, 94, 0.35);
}
.ge-badge-muted {
  background: rgba(255, 255, 255, 0.06);
  color: #9ca3af;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.ge-search {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.28);
  background: #0a0a0a;
  padding: 0.45rem 0.75rem;
  min-width: 180px;
  max-width: 280px;
  flex: 1;
}
.ge-search i { color: #9ca3af; font-size: 0.85rem; }
.ge-search input {
  flex: 1;
  min-width: 0;
  border: none;
  background: transparent;
  color: #fff;
  font-size: 0.88rem;
  font-family: inherit;
  outline: none;
}
.ge-search input::placeholder { color: #6b7280; }

.ge-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.ge-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 640px;
}
.ge-table th {
  text-align: left;
  padding: 0.9rem 1rem;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #d4af37;
  background: #111;
  border-bottom: 1px solid rgba(212, 175, 55, 0.18);
  white-space: nowrap;
}
.ge-table td {
  padding: 0.95rem 1rem;
  font-size: 0.92rem;
  color: #e5e5e5;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  vertical-align: middle;
  background: #141414;
}
.ge-table tbody tr:hover td {
  background: rgba(212, 175, 55, 0.05);
}
.ge-table tbody tr:last-child td { border-bottom: none; }

.ge-user {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-width: 0;
}
.ge-avatar {
  width: 40px;
  height: 40px;
  border-radius: 999px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  font-weight: 700;
  color: #1a1408;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  text-transform: uppercase;
}
.ge-user-name {
  margin: 0;
  font-weight: 700;
  font-size: 0.95rem;
  color: #fff;
}
.ge-user-id {
  margin: 0.15rem 0 0;
  font-size: 0.8rem;
  color: #9ca3af;
  font-weight: 500;
}

.ge-amt {
  display: inline-flex;
  align-items: center;
  padding: 0.28rem 0.65rem;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 700;
  color: #1a1408;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
}

.ge-empty {
  text-align: center;
  padding: 2.5rem 1rem;
  color: #9ca3af;
  font-size: 0.95rem;
}

.ge-hint {
  margin: 0;
  font-size: 0.78rem;
  color: #9ca3af;
}

.animate-in {
  animation: geFadeUp 0.4s ease both;
}
@keyframes geFadeUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 640px) {
  .ge-section-bar { padding: 1rem; }
  .ge-search { max-width: none; width: 100%; }
}
</style>

<div class="ge-net">
  <div class="ge-net-head">
    <h1>My Team</h1>
    <p>Manage your downline network</p>
  </div>

  <section class="ge-panel ge-panel-pad animate-in">
    <div class="ge-ref-top">
      <div style="flex:1;min-width:0;">
        <p class="ge-label-gold">Referral link</p>
        <p class="sub">Share to grow your team across levels</p>
        <div class="ge-copy-row">
          <input type="text" id="refLink" readonly value="<?php echo htmlspecialchars($referral_link); ?>" />
          <button type="button" class="ge-btn-gold" id="copyRefBtn" data-copy="<?php echo htmlspecialchars($referral_link); ?>">
            <i class="fas fa-copy"></i> Copy Link
          </button>
        </div>
      </div>
      <div class="ge-mini-stats">
        <div class="ge-mini-stat">
          <p class="n"><?php echo (int) $total_members; ?></p>
          <p class="l">Members</p>
        </div>
        <div class="ge-mini-stat">
          <p class="n silver"><?php echo (int) $total_levels; ?></p>
          <p class="l">Levels</p>
        </div>
        <div class="ge-mini-stat">
          <p class="n">$<?php echo number_format((float) $total_earnings, 0); ?></p>
          <p class="l">Value</p>
        </div>
      </div>
    </div>
  </section>

  <section class="ge-panel animate-in">
    <div class="ge-section-bar">
      <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
        <h3 class="ge-section-title">Team Members</h3>
        <span class="ge-badge ge-badge-gold"><?php echo (int) $total_members; ?> Members</span>
      </div>
      <div class="ge-search">
        <i class="fas fa-search"></i>
        <input type="text" id="teamSearchInput" placeholder="Search name, ID, level...">
      </div>
    </div>

    <div class="ge-table-wrap">
      <table class="ge-table" id="teamTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Member</th>
            <th>Joined</th>
            <!-- <th>Activation</th> -->
            <!-- <th>Placement</th> -->
            <th>Package</th>
            <th>Level</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $has_rows = false;
          foreach ($child_levels as $key => $child_level) {
              $uids = implode(" , ", $child_level);
              if (!$uids) {
                  $uids = 0;
              }
              $query = "SELECT u.uid, u.login_id, u.name, u.mobile, u.datetime, s.uid as sponsoruid, s.login_id as sponsor, p.uid as placementuid, p.login_id as placement, u.position, u.package, u.topup FROM user as u"
                  . " LEFT JOIN user as s ON s.uid=u.refer_id"
                  . " LEFT JOIN user as p ON p.uid=u.placement_id"
                  . " WHERE u.uid IN ($uids)";

              $result = my_query($query);
              while ($row = my_fetch_object($result)) {
                  $has_rows = true;
                  $i++;
                  $is_active = ((float) $row->topup) > 0;
                  $initial = strtoupper(substr(trim((string) $row->name), 0, 1));
                  if ($initial === '') {
                      $initial = 'U';
                  }
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td>
                      <div class="ge-user">
                        <div class="ge-avatar"><?php echo htmlspecialchars($initial); ?></div>
                        <div>
                          <p class="ge-user-name"><?php echo htmlspecialchars($row->name); ?></p>
                          <p class="ge-user-id"><?php echo htmlspecialchars($row->login_id); ?></p>
                        </div>
                      </div>
                    </td>
                    <td><?php echo date("d M, Y", strtotime($row->datetime)); ?></td>
                    <!-- <td><?php echo date("d M, Y", strtotime($row->topup_datetime)); ?></td> -->
                    <!-- <td>
                      <?php
                      if (!empty($row->topup_datetime) && $row->topup_datetime !== '0000-00-00 00:00:00' && $row->topup_datetime !== '0000-00-00') {
                          echo date('d M, Y', strtotime($row->topup_datetime));
                      } else {
                          echo '-';
                      }
                      ?>
                    </td> -->
                    <!-- <td><?php echo $row->placementuid; ?></td> -->
                    <td><span class="ge-amt">$<?php echo $row->topup * 1; ?></span></td>
                    <td><span class="ge-badge ge-badge-level">Level <?php echo $j; ?></span></td>
                    <td>
                      <?php if ($is_active): ?>
                        <span class="ge-badge ge-badge-success">Active</span>
                      <?php else: ?>
                        <span class="ge-badge ge-badge-muted">Inactive</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php
              }
              $j++;
          }

          if (!$has_rows) {
              echo '<tr><td colspan="6" class="ge-empty">No team members found.</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </section>

  <p class="ge-hint">Your downline is grouped by level. Active members have package / topup invested.</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var searchInput = document.getElementById('teamSearchInput');
  var table = document.querySelector('#teamTable tbody');
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
        '<td colspan="6" class="ge-empty"><i class="fas fa-search" style="opacity:0.5;margin-right:6px;"></i> No team members found matching "' +
        searchTerm.replace(/"/g, '') + '"</td>';
      table.appendChild(noResultsRow);
    }
  });

  var copyBtn = document.getElementById('copyRefBtn');
  var refInput = document.getElementById('refLink');
  if (copyBtn && refInput) {
    copyBtn.addEventListener('click', function () {
      var text = copyBtn.getAttribute('data-copy') || refInput.value;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
          copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied';
          setTimeout(function () {
            copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy Link';
          }, 1600);
        });
      } else {
        refInput.select();
        document.execCommand('copy');
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied';
        setTimeout(function () {
          copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy Link';
        }, 1600);
      }
    });
  }
});
</script>

<?php include_once 'footer.php'; ?>
