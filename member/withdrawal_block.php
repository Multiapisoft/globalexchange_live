<?php
$type = (isset($_GET['type']) && in_array($_GET['type'], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11))) ? $_GET['type'] : 1;
$typearr = array(10 => 'USDT', 11 => 'USDT');
$type2 = $typearr[$type] ?? 'USDT';
$title = 'Withdrawal ' . $type2;
$_is_dashboard = 1;
include_once 'header.php';
$wallet_field = ($type == 11) ? 'wallet_promo' : 'wallet';
$available = $user->$wallet_field * 1;
$withdraw_address = !empty($user->bitcoin) ? $user->bitcoin : '';

$recent_q = my_query("SELECT amount, net_amount, withdrawal_address, status, datetime FROM withdrawal_block WHERE uid='" . (int) $uid . "' ORDER BY datetime DESC LIMIT 5");
$status_map = array(0 => 'Pending', 1 => 'Paid', 2 => 'Rejected');
?>

<style>
/* Withdrawal — match Desktop withdraw.html; OTP skipped */
.content-header { display: none !important; }

.ge-wd {
  max-width: 900px;
  width: 100%;
  margin: 0 auto;
  padding: 0.5rem 0 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  box-sizing: border-box;
  font-family: "Montserrat", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
  color: #fff;
  font-size: 16px;
  line-height: 1.55;
}
.ge-wd *,
.ge-wd *::before,
.ge-wd *::after { box-sizing: border-box; }

.ge-wd-head h1 {
  margin: 0;
  font-size: clamp(1.15rem, 2.5vw, 1.35rem);
  font-weight: 700;
  color: #fff;
}
.ge-wd-head p {
  margin: 0.25rem 0 0;
  font-size: 0.8rem;
  color: #9ca3af;
}

.ge-panel {
  border-radius: 14px;
  border: 1px solid rgba(212, 175, 55, 0.22);
  background: #141414;
  padding: 1.25rem 1.35rem;
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.35);
}
@media (min-width: 640px) {
  .ge-panel { padding: 1.5rem 1.75rem; }
  .ge-panel.form-card { padding: 1.75rem; }
}

.ge-stat-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}
@media (min-width: 640px) {
  .ge-stat-row { grid-template-columns: 1fr 1fr; }
}
.ge-stat-row .lbl {
  margin: 0;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #9ca3af;
}
.ge-stat-row .val {
  margin: 0.45rem 0 0;
  font-size: clamp(1.35rem, 3vw, 1.7rem);
  font-weight: 800;
  letter-spacing: -0.02em;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.ge-stat-row .val.silver {
  background: none;
  -webkit-background-clip: unset;
  background-clip: unset;
  color: #c0c0c0;
}
.ge-stat-row .hint {
  margin: 0.35rem 0 0;
  font-size: 0.78rem;
  color: #9ca3af;
}

.ge-form-head {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  margin-bottom: 1.25rem;
}
.ge-form-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #d4af37;
  background: rgba(212, 175, 55, 0.12);
  border: 1px solid rgba(212, 175, 55, 0.35);
  flex-shrink: 0;
}
.ge-form-head h2 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.ge-form-head p {
  margin: 0.2rem 0 0;
  font-size: 0.85rem;
  color: #9ca3af;
}

.ge-field { margin-bottom: 1rem; }
.ge-field label {
  display: block;
  margin: 0 0 0.5rem;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #d4af37;
}
.ge-field input {
  width: 100%;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.28);
  background: #0a0a0a;
  color: #fff;
  padding: 0.75rem 0.9rem;
  font-size: 0.95rem;
  font-weight: 500;
  font-family: inherit;
  line-height: 1.4;
}
.ge-field input.mono {
  font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
  font-size: 0.85rem;
}
.ge-field input:focus {
  outline: none;
  border-color: #d4af37;
  box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
}
.ge-field input[readonly] {
  color: #9ca3af;
  cursor: default;
}
.ge-field .help {
  display: block;
  margin-top: 0.4rem;
  font-size: 0.78rem;
  color: #9ca3af;
}

.ge-btn-gold {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.8rem 1.4rem;
  border: none;
  border-radius: 12px;
  font-family: inherit;
  font-size: 0.95rem;
  font-weight: 700;
  color: #1a1408;
  cursor: pointer;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  box-shadow: 0 6px 18px rgba(212, 175, 55, 0.28);
}
.ge-btn-gold:hover { filter: brightness(1.06); }
.ge-btn-gold:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  filter: none;
}

.ge-hint {
  margin: 1rem 0 0;
  font-size: 0.78rem;
  color: #9ca3af;
}

.ge-section-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1.1rem 1.25rem;
  border-bottom: 1px solid rgba(212, 175, 55, 0.12);
}
.ge-section-bar h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #fff;
}
.ge-panel.table-card { padding: 0; overflow: hidden; }

.ge-table-wrap { overflow-x: auto; }
.ge-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 520px;
}
.ge-table th {
  text-align: left;
  padding: 0.85rem 1rem;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #d4af37;
  background: #111;
  border-bottom: 1px solid rgba(212, 175, 55, 0.18);
}
.ge-table td {
  padding: 0.9rem 1rem;
  font-size: 0.9rem;
  color: #e5e5e5;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  background: #141414;
}
.ge-table tr:last-child td { border-bottom: none; }
.ge-table .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 0.78rem; color: #9ca3af; }
.ge-table .amt { color: #f5c842; font-weight: 700; }

.ge-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.65rem;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 700;
}
.ge-badge-pending {
  background: rgba(212, 175, 55, 0.15);
  color: #f5c842;
  border: 1px solid rgba(212, 175, 55, 0.4);
}
.ge-badge-paid {
  background: rgba(34, 197, 94, 0.15);
  color: #22c55e;
  border: 1px solid rgba(34, 197, 94, 0.35);
}
.ge-badge-rejected {
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
  border: 1px solid rgba(239, 68, 68, 0.35);
}

.ge-empty {
  text-align: center;
  padding: 2rem 1rem;
  color: #9ca3af;
}

.ge-warn {
  border-color: rgba(212, 175, 55, 0.35);
  background: rgba(212, 175, 55, 0.06);
  color: #f5c842;
  font-size: 0.88rem;
}

.animate-in {
  animation: geFadeUp 0.4s ease both;
}
@keyframes geFadeUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="ge-wd">
  <div class="ge-wd-head">
    <h1>Withdraw</h1>
    <p>Financial freedom · from Earning Wallet</p>
  </div>

  <section class="ge-stat-row animate-in">
    <div class="ge-panel">
      <p class="lbl">Available (Earning)</p>
      <p class="val silver">$<?php echo number_format((float) $available, 2); ?></p>
    </div>
    <div class="ge-panel">
      <p class="lbl">Est. charge (2%)</p>
      <p class="val">Configurable</p>
      <p class="hint">Admin &amp; service charge as per rules</p>
    </div>
  </section>

  <?php if (empty($withdraw_address)): ?>
  <div class="ge-panel ge-warn animate-in">
    Please add your <strong>USDT BEP-20 address</strong> in <a href="profile.php" style="color:#ffe566;">Profile</a> before requesting a withdrawal.
  </div>
  <?php endif; ?>

  <section class="ge-panel form-card animate-in">
    <div class="ge-form-head">
      <div class="ge-form-icon"><i class="fas fa-shield-alt"></i></div>
      <div>
        <h2>Secure withdrawal</h2>
        <p>USDT BEP-20 · subject to verification</p>
      </div>
    </div>

    <form action="withdrawal_block_model.php" method="post" id="withdrawalForm" onsubmit="return submitWithdrawal();">
      <div class="ge-field">
        <label for="account">USDT BEP-20 address</label>
        <input class="mono" type="text" id="account" name="account" value="<?php echo htmlspecialchars($withdraw_address); ?>" placeholder="0x..." <?php echo empty($withdraw_address) ? '' : 'readonly="readonly"'; ?> required="required">
        <span class="help">Address from your profile (USDT.BEP20)</span>
      </div>

      <div class="ge-field">
        <label for="amount"><?php echo htmlspecialchars($type2); ?> Amount *</label>
        <input type="text" id="amount" name="amount" value="" maxlength="10" required="required" inputmode="decimal" placeholder="Enter amount to withdraw">
        <span class="help">
          Available: $<?php echo number_format((float) $available, 2); ?> <?php echo SITE_CURRENCY; ?>
          · Min $10 · Multiple of $10 · 2% charge · Processing 24–72 hours
        </span>
      </div>

      <input type="hidden" name="type" value="<?php echo (int) $type; ?>" />
      <button type="submit" class="ge-btn-gold" id="submitWithdrawalBtn" <?php echo empty($withdraw_address) ? 'disabled' : ''; ?>>
        <i class="fas fa-paper-plane"></i> Request Withdrawal
      </button>
      <p class="ge-hint">Enter amount and submit. OTP is not required. Network / processing charges may apply.</p>
    </form>
  </section>

  <section class="ge-panel table-card animate-in">
    <div class="ge-section-bar">
      <h3>Recent requests</h3>
      <a href="report_withdrawal_block.php" style="color:#f5c842;font-size:0.85rem;font-weight:600;text-decoration:none;">View all</a>
    </div>
    <div class="ge-table-wrap">
      <table class="ge-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Address</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $has_recent = false;
          if ($recent_q) {
              while ($row = my_fetch_object($recent_q)) {
                  $has_recent = true;
                  $st = isset($status_map[(int) $row->status]) ? (int) $row->status : 0;
                  $badge = ($st === 1) ? 'ge-badge-paid' : (($st === 2) ? 'ge-badge-rejected' : 'ge-badge-pending');
                  $addr = (string) $row->withdrawal_address;
                  $short = (strlen($addr) > 12) ? (substr($addr, 0, 6) . '…' . substr($addr, -4)) : $addr;
                  ?>
                  <tr>
                    <td><?php echo date('d M Y', strtotime($row->datetime)); ?></td>
                    <td class="amt">$<?php echo number_format((float) $row->amount, 2); ?></td>
                    <td class="mono" title="<?php echo htmlspecialchars($addr); ?>"><?php echo htmlspecialchars($short); ?></td>
                    <td><span class="ge-badge <?php echo $badge; ?>"><?php echo htmlspecialchars($status_map[$st]); ?></span></td>
                  </tr>
                  <?php
              }
          }
          if (!$has_recent) {
              echo '<tr><td colspan="4" class="ge-empty">No withdrawal requests yet.</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<?php include_once 'footer.php'; ?>

<script>
function submitWithdrawal() {
  var btn = document.getElementById('submitWithdrawalBtn');
  var amountEl = document.getElementById('amount');
  var accountEl = document.getElementById('account');
  var amount = parseFloat((amountEl && amountEl.value) ? amountEl.value : '0');
  var balance = parseFloat('<?php echo (float) $available; ?>');

  if (!accountEl || !accountEl.value.trim()) {
    showToast('Please add a USDT BEP-20 address in Profile first.');
    return false;
  }
  if (!amount || amount < 10) {
    showToast('Minimum withdrawal amount is $10.');
    return false;
  }
  if (amount % 10 !== 0) {
    showToast('Amount must be a multiple of $10.');
    return false;
  }
  if (amount > balance) {
    showToast('Insufficient fund.');
    return false;
  }

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
  }
  return true;
}

function showToast(message) {
  var toast = document.createElement('div');
  toast.innerHTML =
    '<div style="position:fixed;top:20px;right:20px;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;padding:14px 20px;border-radius:12px;box-shadow:0 8px 28px rgba(0,0,0,.45);z-index:10000;font-weight:600;font-size:.95rem;max-width:380px;">' +
    '<i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i>' + message + '</div>';
  document.body.appendChild(toast);
  setTimeout(function () {
    if (document.body.contains(toast)) document.body.removeChild(toast);
  }, 4500);
}
</script>
