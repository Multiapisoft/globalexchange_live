<?php
$title = 'Investment Plans';
$_is_dashboard = 1;
include_once 'header.php';
user();

$check = my_query("SHOW COLUMNS FROM `investments_plan` LIKE 'action'");
if (mysqli_num_rows($check) == 0) {
    $query = "ALTER TABLE `investments_plan` ADD `action` TINYINT NOT NULL DEFAULT '1' COMMENT '1. active\r\n,0. Deactive' AFTER `status`;";
    my_query($query);
}

$check_pair = my_query("SHOW COLUMNS FROM `investments` LIKE 'exchange_pair'");
if (mysqli_num_rows($check_pair) == 0) {
    $query_pair = "ALTER TABLE `investments` ADD `exchange_pair` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Trading pair like BTC/USDT' AFTER `invest_hour`;";
    my_query($query_pair);
}

$check_coin = my_query("SHOW COLUMNS FROM `investments` LIKE 'exchange_coin'");
if (mysqli_num_rows($check_coin) == 0) {
    $query_coin = "ALTER TABLE `investments` ADD `exchange_coin` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Selected cryptocurrency' AFTER `exchange_pair`;";
    my_query($query_coin);
}

$botActivationCheck = my_query("SELECT recid FROM investments WHERE uid = '" . (int) $_SESSION['userid'] . "' AND ipid = 1 LIMIT 1");
$hasBotActivation = mysqli_num_rows($botActivationCheck) > 0;

$botSubscriptionCheck = my_query("SELECT * FROM investments WHERE uid = '" . (int) $_SESSION['userid'] . "' AND ipid = 4 AND is_closed = 0 ORDER BY datetime DESC LIMIT 1");
$hasBotSubscription = mysqli_num_rows($botSubscriptionCheck) > 0;

$query = "SELECT * FROM investments_plan WHERE status = 0 AND action = 1 ORDER BY recid ASC";
$all_plans = array();
$plans_result = my_query($query);
while ($plan = mysqli_fetch_object($plans_result)) {
    $all_plans[] = $plan;
}

$wallet_topup = 0;
if (isset($_SESSION['userdata']) && isset($_SESSION['userdata']->wallet_topup)) {
    $wallet_topup = (float) $_SESSION['userdata']->wallet_topup;
} elseif (isset($user->wallet_topup)) {
    $wallet_topup = (float) $user->wallet_topup;
}

function ge_plan_tier($plan) {
    $title = strtolower((string) $plan->title);
    $recid = (int) $plan->recid;
    if ($recid === 1 || strpos($title, 'bot') !== false || strpos($title, 'activation') !== false) {
        return 'bot';
    }
    if ($recid === 2 || strpos($title, 'silver') !== false) {
        return 'silver';
    }
    if ($recid === 3 || strpos($title, 'gold') !== false) {
        return 'gold';
    }
    return 'gold';
}
?>

<style>
.content-header { display: none !important; }

.ge-trade {
  max-width: 1100px;
  width: 100%;
  margin: 0 auto;
  padding: 0.5rem 0 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  box-sizing: border-box;
  font-family: "Montserrat", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
  color: #fff;
  font-size: 16px;
  line-height: 1.55;
}
.ge-trade *,
.ge-trade *::before,
.ge-trade *::after { box-sizing: border-box; }

.ge-trade-head h1 {
  margin: 0;
  font-size: clamp(1.15rem, 2.5vw, 1.35rem);
  font-weight: 700;
  color: #fff;
}
.ge-trade-head p {
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
}

.ge-status {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  font-size: 0.88rem;
}
.ge-status i { margin-top: 0.15rem; flex-shrink: 0; }
.ge-status.warn {
  border-color: rgba(212, 175, 55, 0.35);
  background: rgba(212, 175, 55, 0.08);
  color: #f5e6a8;
}
.ge-status.warn i { color: #d4af37; }
.ge-status.ok {
  border-color: rgba(16, 185, 129, 0.35);
  background: rgba(16, 185, 129, 0.08);
  color: #a7f3d0;
}
.ge-status.ok i { color: #34d399; }

.ge-plan-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}
@media (min-width: 768px) {
  .ge-plan-grid { grid-template-columns: 1fr 1fr; }
  .ge-plan-grid .ge-plan-card.tier-bot { grid-column: 1 / -1; }
}

.ge-plan-card {
  position: relative;
  overflow: hidden;
  border-radius: 14px;
  border: 1px solid rgba(212, 175, 55, 0.22);
  background: #141414;
  padding: 1.35rem 1.4rem;
  cursor: pointer;
  transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.28);
}
.ge-plan-card::before {
  content: "";
  position: absolute;
  inset: 0 0 auto 0;
  height: 3px;
  background: linear-gradient(90deg, #ffe566, #d4af37, #b8860b);
}
.ge-plan-card.tier-silver::before {
  background: linear-gradient(90deg, #e8e8e8, #c0c0c0, #9a9a9a);
}
.ge-plan-card:hover:not(.locked) {
  border-color: rgba(212, 175, 55, 0.5);
  transform: translateY(-2px);
}
.ge-plan-card.active {
  border-color: rgba(212, 175, 55, 0.65);
  box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.25), 0 10px 30px rgba(0, 0, 0, 0.4);
}
.ge-plan-card.locked {
  opacity: 0.72;
  cursor: not-allowed;
}
.ge-plan-card.locked .ge-plan-body { filter: grayscale(0.25); }

.ge-plan-lock {
  position: absolute;
  inset: 0;
  z-index: 5;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  padding: 1rem;
  text-align: center;
  background: rgba(10, 10, 10, 0.78);
  backdrop-filter: blur(2px);
}
.ge-plan-lock i { font-size: 1.5rem; color: #d4af37; }
.ge-plan-lock.ok i { color: #34d399; }
.ge-plan-lock strong { color: #fff; font-size: 0.95rem; }
.ge-plan-lock span { color: #9ca3af; font-size: 0.78rem; max-width: 220px; }

.ge-plan-top {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 0.85rem;
}
.ge-plan-top h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
}
.ge-plan-card.tier-bot .ge-plan-top h2,
.ge-plan-card.tier-gold .ge-plan-top h2 {
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.ge-plan-card.tier-silver .ge-plan-top h2 { color: #c0c0c0; }

.ge-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.2rem 0.55rem;
  border-radius: 999px;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.ge-badge.req {
  background: rgba(16, 185, 129, 0.15);
  border: 1px solid rgba(16, 185, 129, 0.4);
  color: #6ee7b7;
}
.ge-badge.gold {
  background: rgba(212, 175, 55, 0.15);
  border: 1px solid rgba(212, 175, 55, 0.4);
  color: #d4af37;
}
.ge-badge.silver {
  background: rgba(192, 192, 192, 0.12);
  border: 1px solid rgba(192, 192, 192, 0.35);
  color: #c0c0c0;
}

.ge-plan-desc {
  margin: 0 0 1rem;
  font-size: 0.85rem;
  color: #9ca3af;
}
.ge-plan-price {
  margin: 0;
  font-size: clamp(1.5rem, 3vw, 1.85rem);
  font-weight: 800;
  letter-spacing: -0.02em;
}
.ge-plan-card.tier-bot .ge-plan-price,
.ge-plan-card.tier-gold .ge-plan-price {
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.ge-plan-card.tier-silver .ge-plan-price { color: #c0c0c0; }
.ge-plan-price-sub {
  margin: 0.25rem 0 1rem;
  font-size: 0.75rem;
  color: #9ca3af;
}

.ge-plan-features {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
}
.ge-plan-features li {
  display: flex;
  gap: 0.5rem;
  font-size: 0.84rem;
  color: #b7bdc6;
}
.ge-plan-features li i {
  color: #d4af37;
  margin-top: 0.2rem;
  flex-shrink: 0;
}
.ge-plan-card.tier-silver .ge-plan-features li i { color: #c0c0c0; }

.ge-plan-cta {
  margin-top: 1.15rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  width: 100%;
  padding: 0.7rem 1rem;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.4);
  background: transparent;
  color: #d4af37;
  font-size: 0.88rem;
  font-weight: 700;
  font-family: inherit;
  pointer-events: none;
}
.ge-plan-card.active .ge-plan-cta,
.ge-plan-card.tier-gold .ge-plan-cta {
  border: none;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  color: #0a0a0a;
}
.ge-plan-card.tier-silver:not(.active) .ge-plan-cta {
  border-color: rgba(192, 192, 192, 0.4);
  color: #c0c0c0;
}

.ge-form-wrap { display: none; }
.ge-form-wrap.show { display: block; }

.ge-form-head {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.25rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.ge-form-head h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.ge-form-head p {
  margin: 0.25rem 0 0;
  font-size: 0.8rem;
  color: #9ca3af;
}
.ge-bal {
  text-align: right;
}
.ge-bal span {
  display: block;
  font-size: 0.72rem;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.ge-bal strong {
  font-size: 1.25rem;
  font-weight: 800;
  color: #d4af37;
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
.ge-field input,
.ge-field select {
  width: 100%;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.28);
  background: #0a0a0a;
  color: #fff;
  padding: 0.75rem 0.9rem;
  font-size: 0.95rem;
  font-family: inherit;
  outline: none;
}
.ge-field input:focus,
.ge-field select:focus {
  border-color: rgba(212, 175, 55, 0.65);
  box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.12);
}
.ge-field input[readonly] {
  opacity: 0.85;
  cursor: not-allowed;
}
.ge-field .hint {
  margin: 0.4rem 0 0;
  font-size: 0.78rem;
  color: #9ca3af;
}
.ge-amount-wrap { position: relative; }
.ge-amount-wrap .pfx {
  position: absolute;
  left: 0.9rem;
  top: 50%;
  transform: translateY(-50%);
  color: #d4af37;
  font-weight: 700;
}
.ge-amount-wrap input { padding-left: 1.75rem; }

.ge-form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0 1rem;
}
@media (min-width: 700px) {
  .ge-form-grid { grid-template-columns: 1fr 1fr; }
  .ge-form-grid .span-2 { grid-column: 1 / -1; }
}

.ge-quick {
  margin-top: 0.65rem;
}
.ge-quick p {
  margin: 0 0 0.4rem;
  font-size: 0.72rem;
  color: #9ca3af;
}
.ge-quick-btns {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.4rem;
}
.ge-quick-btns button {
  padding: 0.45rem 0.35rem;
  border-radius: 8px;
  border: 1px solid rgba(212, 175, 55, 0.28);
  background: #0a0a0a;
  color: #d4af37;
  font-size: 0.78rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
}
.ge-quick-btns button:hover {
  border-color: rgba(212, 175, 55, 0.55);
  background: rgba(212, 175, 55, 0.1);
}

.ge-price-box {
  border-radius: 12px;
  border: 1px solid rgba(212, 175, 55, 0.25);
  background: rgba(212, 175, 55, 0.05);
  padding: 1rem;
}
.ge-price-box h4 {
  margin: 0 0 0.85rem;
  text-align: center;
  font-size: 0.95rem;
  color: #d4af37;
}
.ge-price-cols {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.65rem;
  margin-bottom: 0.75rem;
}
@media (min-width: 600px) {
  .ge-price-cols { grid-template-columns: 1fr 1fr; }
}
.ge-price-col {
  background: #0a0a0a;
  border-radius: 10px;
  padding: 0.85rem;
  border: 1px solid rgba(255, 255, 255, 0.06);
}
.ge-price-col .nm { font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem; }
.ge-price-col .px { font-size: 1.25rem; font-weight: 800; color: #fff; }
.ge-price-diff {
  text-align: center;
  padding: 0.75rem;
  border-radius: 10px;
  background: rgba(212, 175, 55, 0.1);
  border: 1px solid rgba(212, 175, 55, 0.2);
}
.ge-price-diff .lbl { font-size: 0.75rem; color: #9ca3af; }
.ge-price-diff .val { font-size: 1.05rem; font-weight: 800; color: #d4af37; margin-top: 0.2rem; }
.ge-price-diff .arb { font-size: 0.75rem; color: #b7bdc6; margin-top: 0.35rem; }
.ge-price-note {
  margin: 0.65rem 0 0;
  text-align: center;
  font-size: 0.72rem;
  color: #6b7280;
}

.ge-benefits {
  margin: 0.5rem 0 1rem;
  padding: 1rem;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: #0a0a0a;
}
.ge-benefits h4 {
  margin: 0 0 0.65rem;
  font-size: 0.9rem;
  color: #fff;
}
.ge-benefits ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}
.ge-benefits li {
  display: flex;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #b7bdc6;
}
.ge-benefits li i { color: #d4af37; margin-top: 0.15rem; }

.ge-btn-gold {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  width: 100%;
  padding: 0.9rem 1.25rem;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  color: #0a0a0a;
  font-size: 0.95rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  box-shadow: 0 6px 18px rgba(212, 175, 55, 0.28);
}
.ge-btn-gold:hover { filter: brightness(1.06); }
</style>

<div class="ge-trade">
  <div class="ge-trade-head">
    <h1>Investment Packages</h1>
    <p>Bot Activation · Silver · Gold</p>
  </div>

  <?php if (!$hasBotActivation) { ?>
  <div class="ge-panel ge-status warn">
    <i class="fas fa-lock"></i>
    <div>Silver &amp; Gold packages unlock after you buy <strong>Bot Activation Account</strong>.</div>
  </div>
  <?php } else { ?>
  <div class="ge-panel ge-status ok">
    <i class="fas fa-check-circle"></i>
    <div>Bot Activation completed. Trading packages are unlocked.</div>
  </div>
  <?php } ?>

  <div class="ge-plan-grid">
    <?php foreach ($all_plans as $plan) {
      $tier = ge_plan_tier($plan);
      $isPlanLocked = false;
      $lockReason = '';
      if ((int) $plan->recid === 1 && $hasBotActivation) {
          $isPlanLocked = true;
          $lockReason = 'activated';
      } elseif (!$hasBotActivation && in_array((int) $plan->recid, array(2, 3), true)) {
          $isPlanLocked = true;
          $lockReason = 'need_bot';
      }
      $amount_label = ($plan->amount_from == $plan->amount_to)
          ? '$' . number_format((float) $plan->amount_from)
          : '$' . number_format((float) $plan->amount_from) . ' – $' . number_format((float) $plan->amount_to);
      $onclick = $isPlanLocked ? '' : "selectPackage(this, 'tab{$plan->recid}', 'plan-{$plan->recid}')";
    ?>
    <div class="ge-plan-card tier-<?php echo htmlspecialchars($tier); ?><?php echo $isPlanLocked ? ' locked' : ''; ?>"
         <?php if ($onclick) { ?>onclick="<?php echo $onclick; ?>"<?php } ?>
         data-tab="tab<?php echo (int) $plan->recid; ?>">
      <?php if ($isPlanLocked) { ?>
      <div class="ge-plan-lock<?php echo $lockReason === 'activated' ? ' ok' : ''; ?>">
        <?php if ($lockReason === 'activated') { ?>
          <i class="fas fa-check-circle"></i>
          <strong>Already Activated</strong>
          <span>Bot Activation purchased — one time only</span>
        <?php } else { ?>
          <i class="fas fa-lock"></i>
          <strong>Locked</strong>
          <span>Buy Bot Activation Account first</span>
        <?php } ?>
      </div>
      <?php } ?>

      <div class="ge-plan-body">
        <div class="ge-plan-top">
          <h2><?php echo htmlspecialchars($plan->title); ?></h2>
          <div>
            <?php if ($tier === 'bot') { ?>
              <span class="ge-badge req">Required</span>
            <?php } elseif ($tier === 'gold') { ?>
              <span class="ge-badge gold">Popular</span>
            <?php } else { ?>
              <span class="ge-badge silver">Trading</span>
            <?php } ?>
          </div>
        </div>
        <?php if (!empty($plan->line1)) { ?>
          <p class="ge-plan-desc"><?php echo htmlspecialchars($plan->line1); ?></p>
        <?php } ?>
        <p class="ge-plan-price"><?php echo $amount_label; ?></p>
        <p class="ge-plan-price-sub">Investment amount</p>
        <ul class="ge-plan-features">
          <?php if (!empty($plan->line1)) { ?>
          <li><i class="fas fa-check"></i><span><?php echo htmlspecialchars($plan->line1); ?></span></li>
          <?php } ?>
          <?php if (!empty($plan->line2)) { ?>
          <li><i class="fas fa-check"></i><span><?php echo htmlspecialchars($plan->line2); ?></span></li>
          <?php } ?>
          <li><i class="fas fa-check"></i><span>24/7 Customer Support</span></li>
          <li><i class="fas fa-check"></i><span>Secure &amp; Regulated Platform</span></li>
        </ul>
        <?php if (!$isPlanLocked) { ?>
        <div class="ge-plan-cta"><i class="fas fa-arrow-right"></i> Select Package</div>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
  </div>

  <?php foreach ($all_plans as $plan) {
    if ((int) $plan->recid === 1 && $hasBotActivation) {
        continue;
    }
    if (!$hasBotActivation && in_array((int) $plan->recid, array(2, 3), true)) {
        continue;
    }
    $is_fixed_amount = ($plan->amount_from == $plan->amount_to);
    $fixed_amount_value = $is_fixed_amount ? number_format((float) $plan->amount_from, 2, '.', '') : '';
    $pid = (int) $plan->recid;
  ?>
  <div class="ge-panel ge-form-wrap" id="tab<?php echo $pid; ?>">
    <div class="ge-form-head">
      <div>
        <h3><?php echo htmlspecialchars($plan->title); ?></h3>
        <p>Complete the form below to invest</p>
      </div>
      <div class="ge-bal">
        <span>Available Balance</span>
        <strong>$<?php echo number_format($wallet_topup, 2); ?></strong>
      </div>
    </div>

    <form action="invest_now_model.php" method="POST">
      <input type="hidden" name="recid" value="<?php echo $pid; ?>">
      <input type="hidden" name="type" value="0">

      <div class="ge-form-grid">
        <div class="ge-field">
          <label for="amount_input_<?php echo $pid; ?>">Investment Amount</label>
          <div class="ge-amount-wrap">
            <span class="pfx">$</span>
            <input type="number" name="amount" id="amount_input_<?php echo $pid; ?>"
              placeholder="Enter amount" required
              min="<?php echo htmlspecialchars($plan->amount_from); ?>"
              max="<?php echo htmlspecialchars($plan->amount_to); ?>"
              <?php if ($is_fixed_amount || $pid == 4) { ?>
                value="<?php echo $fixed_amount_value ? $fixed_amount_value : number_format((float) $plan->amount_from, 2, '.', ''); ?>"
                readonly
              <?php } ?>>
          </div>
          <p class="hint">
            <?php if ($is_fixed_amount || $pid == 4) { ?>
              Fixed amount: $<?php echo number_format((float) $plan->amount_from, 2); ?>
            <?php } else { ?>
              Min: $<?php echo number_format((float) $plan->amount_from); ?> — Max: $<?php echo number_format((float) $plan->amount_to); ?>
            <?php } ?>
          </p>

          <?php if (in_array($pid, array(2, 3), true)) { ?>
          <div class="ge-quick">
            <p><i class="fas fa-bolt"></i> Quick Select</p>
            <div class="ge-quick-btns">
              <button type="button" onclick="setPercentageAmount<?php echo $pid; ?>(25)">25%</button>
              <button type="button" onclick="setPercentageAmount<?php echo $pid; ?>(50)">50%</button>
              <button type="button" onclick="setPercentageAmount<?php echo $pid; ?>(75)">75%</button>
              <button type="button" onclick="setPercentageAmount<?php echo $pid; ?>(100)">100%</button>
            </div>
          </div>
          <script>
          function setPercentageAmount<?php echo $pid; ?>(percentage) {
            const minAmount = <?php echo json_encode((float) $plan->amount_from); ?>;
            const maxAmount = <?php echo json_encode((float) $plan->amount_to); ?>;
            // Quick select by package min–max range (whole numbers only)
            let calculatedAmount = Math.round(minAmount + ((maxAmount - minAmount) * percentage) / 100);
            if (calculatedAmount < minAmount) calculatedAmount = Math.ceil(minAmount);
            if (calculatedAmount > maxAmount) calculatedAmount = Math.floor(maxAmount);
            document.getElementById('amount_input_<?php echo $pid; ?>').value = String(calculatedAmount);
          }
          </script>
          <?php } ?>
        </div>

        <?php if (in_array($pid, array(2, 3), true)) { ?>
        <div class="ge-field">
          <label for="exchange_pair_<?php echo $pid; ?>">Exchange Pair</label>
          <select name="exchange_pair" id="exchange_pair_<?php echo $pid; ?>"
            onchange="fetchPrices<?php echo $pid; ?>()" required>
            <option value="">Select Exchange Pair</option>
            <option value="binance-bybit">Binance vs Bybit</option>
            <option value="binance-kucoin">Binance vs KuCoin</option>
            <option value="binance-okx">Binance vs OKX</option>
            <option value="bybit-kucoin">Bybit vs KuCoin</option>
            <option value="bybit-okx">Bybit vs OKX</option>
          </select>
        </div>

        <div class="ge-field">
          <label for="exchange_coin_<?php echo $pid; ?>">Cryptocurrency</label>
          <select name="exchange_coin" id="exchange_coin_<?php echo $pid; ?>"
            onchange="fetchPrices<?php echo $pid; ?>()" required>
            <option value="">Select Coin</option>
            <option value="bitcoin">Bitcoin (BTC)</option>
            <option value="ethereum">Ethereum (ETH)</option>
            <option value="binancecoin">Binance Coin (BNB)</option>
            <option value="ripple">Ripple (XRP)</option>
            <option value="cardano">Cardano (ADA)</option>
            <option value="solana">Solana (SOL)</option>
          </select>
        </div>

        <div class="ge-field span-2" id="price_display_<?php echo $pid; ?>" style="display:none;">
          <div class="ge-price-box">
            <h4><i class="fas fa-chart-line"></i> Live Price Comparison</h4>
            <div class="ge-price-cols">
              <div class="ge-price-col">
                <div class="nm"><i class="fas fa-exchange-alt"></i> <span id="exchange1_name_<?php echo $pid; ?>">Exchange 1</span></div>
                <div class="px" id="exchange1_price_<?php echo $pid; ?>"><i class="fas fa-spinner fa-spin"></i></div>
              </div>
              <div class="ge-price-col">
                <div class="nm"><i class="fas fa-exchange-alt"></i> <span id="exchange2_name_<?php echo $pid; ?>">Exchange 2</span></div>
                <div class="px" id="exchange2_price_<?php echo $pid; ?>"><i class="fas fa-spinner fa-spin"></i></div>
              </div>
            </div>
            <div class="ge-price-diff">
              <div class="lbl">Price Difference</div>
              <div class="val" id="price_difference_<?php echo $pid; ?>">Calculating...</div>
              <div class="arb" id="arbitrage_opportunity_<?php echo $pid; ?>"></div>
            </div>
            <p class="ge-price-note"><i class="fas fa-sync-alt"></i> Prices update every 30 seconds</p>
          </div>
        </div>

        <script>
        let priceInterval<?php echo $pid; ?> = null;
        function fetchPrices<?php echo $pid; ?>() {
          const exchangePair = document.getElementById('exchange_pair_<?php echo $pid; ?>').value;
          const coin = document.getElementById('exchange_coin_<?php echo $pid; ?>').value;
          const priceDisplay = document.getElementById('price_display_<?php echo $pid; ?>');
          if (priceInterval<?php echo $pid; ?>) clearInterval(priceInterval<?php echo $pid; ?>);
          if (!exchangePair || !coin) {
            priceDisplay.style.display = 'none';
            return;
          }
          priceDisplay.style.display = 'block';
          const exchanges = exchangePair.split('-');
          const exchange1 = exchanges[0].charAt(0).toUpperCase() + exchanges[0].slice(1);
          const exchange2 = exchanges[1].charAt(0).toUpperCase() + exchanges[1].slice(1);
          document.getElementById('exchange1_name_<?php echo $pid; ?>').textContent = exchange1;
          document.getElementById('exchange2_name_<?php echo $pid; ?>').textContent = exchange2;
          updatePrices<?php echo $pid; ?>(coin, exchange1, exchange2);
          priceInterval<?php echo $pid; ?> = setInterval(function () {
            updatePrices<?php echo $pid; ?>(coin, exchange1, exchange2);
          }, 30000);
        }
        async function updatePrices<?php echo $pid; ?>(coin, exchange1, exchange2) {
          try {
            const response = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=' + coin + '&vs_currencies=usd&include_24hr_change=true');
            const data = await response.json();
            if (data[coin] && data[coin].usd) {
              const basePrice = data[coin].usd;
              const variation1 = (Math.random() * 0.4 + 0.1) / 100;
              const variation2 = (Math.random() * 0.4 + 0.1) / 100;
              const price1 = basePrice * (1 + variation1);
              const price2 = basePrice * (1 - variation2);
              document.getElementById('exchange1_price_<?php echo $pid; ?>').textContent =
                '$' + price1.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              document.getElementById('exchange2_price_<?php echo $pid; ?>').textContent =
                '$' + price2.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              const difference = Math.abs(price1 - price2);
              const percentageDiff = ((difference / Math.min(price1, price2)) * 100).toFixed(2);
              const higherExchange = price1 > price2 ? exchange1 : exchange2;
              const lowerExchange = price1 > price2 ? exchange2 : exchange1;
              document.getElementById('price_difference_<?php echo $pid; ?>').innerHTML =
                '$' + difference.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
                ' <span style="font-size:0.8rem">(' + percentageDiff + '%)</span>';
              document.getElementById('arbitrage_opportunity_<?php echo $pid; ?>').innerHTML =
                '<i class="fas fa-info-circle"></i> Buy on ' + lowerExchange + ', Sell on ' + higherExchange;
            }
          } catch (error) {
            console.error('Error fetching prices:', error);
            document.getElementById('exchange1_price_<?php echo $pid; ?>').innerHTML = '<span style="color:#f87171;font-size:0.85rem">Error</span>';
            document.getElementById('exchange2_price_<?php echo $pid; ?>').innerHTML = '<span style="color:#f87171;font-size:0.85rem">Error</span>';
          }
        }
        </script>
        <?php } ?>
      </div>

      <?php if (in_array($pid, array(1, 4), true)) { ?>
      <div class="ge-benefits">
        <h4>Plan Benefits</h4>
        <ul>
          <li><i class="fas fa-check-circle"></i><span>24/7 Customer Support</span></li>
          <li><i class="fas fa-check-circle"></i><span>Secure &amp; Regulated Platform</span></li>
          <?php if ($pid == 4) { ?>
          <li><i class="fas fa-check-circle"></i><span>Lifetime Bot Usage &amp; Networking Rewards</span></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <button type="submit" class="ge-btn-gold"><i class="fas fa-check-circle"></i> Invest Now</button>
    </form>
  </div>
  <?php } ?>
</div>

<script>
function selectPackage(element, tabId, packageName) {
  document.querySelectorAll('.ge-plan-card').forEach(function (card) {
    card.classList.remove('active');
  });
  element.classList.add('active');
  document.querySelectorAll('.ge-form-wrap').forEach(function (tab) {
    tab.classList.remove('show');
  });
  var tab = document.getElementById(tabId);
  if (tab) {
    tab.classList.add('show');
    tab.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  var selectedPackageInput = document.getElementById('selectedPackage');
  if (selectedPackageInput) {
    selectedPackageInput.value = packageName;
  }
}
</script>

<?php include_once 'footer.php'; ?>
