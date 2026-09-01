<?php
$title = 'Dashboard';
include_once 'header.php';
$uid = (int) $uid;

$sponsor = get_user_details($user->refer_id);

function get_child_bv_total3($uid, $p = 'L')
{
    $amt = @my_fetch_object(my_query("SELECT (teamb + topup) as amount FROM user WHERE placement_id = '" . $uid . "' AND position = '" . $p . "'"))->amount;
    $amt = ($amt > 0) ? $amt : 0;
    return $amt;
}

$total_in = get_sum('income_royalty', 'amount', "uid='" . $uid . "'") + get_sum('income_growth', 'amount', "uid='" . $uid . "'") + get_sum('income_level', 'amount', "uid='" . $uid . "'") + get_sum('income_direct', 'amount', "uid='" . $uid . "'");
$max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 0,1"))->amount;
$max = ($max) ? $max : 0;
$max2 = 0;
$max3 = $user->teamb - $max - $max2;
$max3 = ($max3 > 0) ? $max3 : 0;

$trading_investment = get_trading_investment($uid);
$capping_multiplier = get_capping_multiplier($uid);
if ($capping_multiplier <= 0 && $trading_investment > 0) {
    $capping_multiplier = 2;
}
$max_earnable = $trading_investment * $capping_multiplier;
$total_earnings_cap = get_total_earnings($uid);
$remaining_amount = max(0, round($max_earnable - $total_earnings_cap, 2));

$active_orders_query = "SELECT COUNT(*) as active_count FROM investments WHERE status = 0 AND uid = '$uid' AND ipid != 4";
$active_result = my_query($active_orders_query);
$active_orders = mysqli_fetch_object($active_result)->active_count;

$hasLiveTradingPlan = my_num_rows(my_query(
    "SELECT recid FROM investments WHERE uid = '$uid' AND status = 0 AND is_closed = 0 AND ipid != 4 LIMIT 1"
)) > 0;

$botSubscriptionCheck = my_query("SELECT * FROM investments WHERE uid = '$uid' AND ipid = 4 AND is_closed = 0 ORDER BY datetime DESC LIMIT 1");
$hasBotSubscription = mysqli_num_rows($botSubscriptionCheck) > 0;
$botSubscriptionData = mysqli_fetch_object($botSubscriptionCheck);

$child_levels = get_child_levels_refer_($uid, $with = 'yes');

$total_members = 0;
foreach ($child_levels as $level) {
    $total_members += count($level);
}

$total_deposits = round(get_sum('deposit_block', 'amount', "uid='" . $uid . "' AND status=1") * 1, 2);
$total_withdrawals = round(get_sum('withdrawal_block', 'amount', "uid='" . $uid . "' AND status=1") * 1, 2);
$total_profits = round(get_sum('income_growth', 'amount', "uid='" . $uid . "'") * 1, 2);
$level_roi = round(get_sum('income_level', 'amount', "uid='$uid' AND type=2") * 1, 2);
$total_trades = round(get_sum('investments', 'amount', "uid='$uid' AND ipid != 4") * 1, 2);
$direct_business = round(get_sum('investments', 'amount', "uid IN (SELECT uid FROM user WHERE status = 0 AND topup>0 AND refer_id='" . $uid . "')") * 1, 2);

$earning_wallet = get_member_earning_wallet($uid);
$package_wallet = get_member_package_wallet($uid);
$total_balance = round($earning_wallet + $package_wallet + ($user->balance * 1), 2);
$available_balance = $earning_wallet;
$referral_link = SITE_URL . '/soft/member/register.php?ref=' . $uid;
?>

<style>
/* Theme dashboard layout helpers (panel.css provides core components) */
.ge-main {
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
  padding: 0.35rem 0 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  box-sizing: border-box;
}
.ge-main *,
.ge-main *::before,
.ge-main *::after {
  box-sizing: border-box;
}
.content-header { display: none !important; }
.ge-welcome-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem 0.75rem;
}
.ge-welcome-row h1 {
  margin: 0;
  font-size: clamp(1.2rem, 2.8vw, 1.45rem);
  font-weight: 600;
  color: #fff;
}
.ge-welcome-row h1 strong { font-weight: 700; }
.ge-extra-grid {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 1rem;
}
.ge-panel-card {
  border-radius: 14px;
  border: 1px solid rgba(255,255,255,0.06);
  background: #121212;
  padding: 1.15rem 1.25rem;
  min-width: 0;
}
.ge-panel-card h3 {
  margin: 0 0 0.9rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: #fff;
}
.ge-wallet-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0,1fr));
  gap: 0.65rem;
}
.ge-wallet-tile {
  border-radius: 12px;
  border: 1px solid rgba(255,255,255,0.06);
  background: rgba(255,255,255,0.02);
  padding: 0.85rem 0.95rem;
  min-width: 0;
}
.ge-wallet-tile .lbl {
  color: #9ca3af;
  font-size: 0.88rem;
  margin-bottom: 0.35rem;
}
.ge-wallet-tile .val {
  color: #fff;
  font-size: clamp(1.05rem, 2.8vw, 1.2rem);
  font-weight: 700;
  word-break: break-word;
}
.ge-ref input {
  width: 100%;
  border-radius: 8px;
  border: 1px solid rgba(212,175,55,0.25);
  background: #0a0a0a;
  color: #e5e5e5;
  padding: 0.7rem 0.85rem;
  font-size: 0.95rem;
  margin-bottom: 0.55rem;
}
.ge-alert {
  border-radius: 14px;
  border: 1px solid rgba(255,255,255,0.08);
  background: #121212;
  padding: 1rem 1.15rem;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
}
.ge-alert.bot { border-color: rgba(34,197,94,0.35); background: linear-gradient(90deg, rgba(34,197,94,0.08), transparent); }
.ge-alert .t { color:#fff; font-weight:700; font-size:1.05rem; }
.ge-alert .d { color:#9ca3af; font-size:0.95rem; margin-top:2px; }
.ge-news-item { padding: 0.7rem 0; border-bottom: 1px solid rgba(255,255,255,0.06); }
.ge-news-item:last-child { border-bottom: 0; }
.ge-news-item .date { color:#d4af37; font-size:0.85rem; }
.ge-news-item h4 { margin: 0.2rem 0; color:#fff; font-size:1.05rem; }
.ge-news-item p { margin:0; color:#9ca3af; font-size:0.95rem; }
.stat-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0.85rem;
}
.promo-side-img {
  position: absolute;
  right: 0; bottom: 0; top: 0;
  width: 52%;
  pointer-events: none;
  overflow: hidden;
}
.balance-card .balance-inner {
  position: relative;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 1.25rem;
  padding: 1.35rem 1.5rem;
}
@media (max-width: 1100px) {
  .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 900px) {
  .ge-extra-grid { grid-template-columns: 1fr; }
}
@media (max-width: 767px) {
  .promo-side-img { display: none; }
  .ge-main { padding: 0.5rem 0 1.5rem; gap: 0.9rem; }
  .balance-card .balance-inner { padding: 1.1rem 1rem; gap: 0.9rem; }
  .ge-panel-card { padding: 1rem; }
  .promo-banner { min-height: 160px; }
}
@media (max-width: 520px) {
  .stat-grid { grid-template-columns: 1fr; }
  .ge-wallet-grid { grid-template-columns: 1fr; }
  .qa-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
}
</style>

<div class="ge-main">
  <div class="ge-welcome-row">
    <h1>Welcome Back, <strong><?php echo htmlspecialchars($user->name); ?></strong></h1>
  </div>

  <?php if ($hasBotSubscription): ?>
  <div class="ge-alert bot animate-in">
    <div>
      <div class="t"><i class="fas fa-robot"></i> Bot Subscription Active</div>
      <div class="d">Invested $<?php echo number_format($botSubscriptionData->amount ?? 0, 2); ?> · <?php echo (int) $active_orders; ?> active orders</div>
    </div>
    <a href="live_trading.php" class="btn-trade">Manage</a>
  </div>
  <?php endif; ?>

  <?php if ($hasLiveTradingPlan): ?>
  <div class="ge-alert animate-in">
    <div>
      <div class="t"><i class="fas fa-chart-line" style="color:#22c55e"></i> Live Trading Chart</div>
      <div class="d">View real-time market data and your active trading plan performance.</div>
    </div>
    <a href="live.php" class="btn-trade">Open Live Chart</a>
  </div>
  <?php endif; ?>

  <!-- Total Balance (theme) -->
  <section class="balance-card animate-in">
    <div class="absolute inset-0 pointer-events-none" style="position:absolute;inset:0;pointer-events:none;">
      <img src="images/dashboard/imgi_2_photo-1451187580459-43490279c0fa.jpeg" alt="" style="position:absolute;right:0;top:0;height:100%;width:55%;object-fit:cover;opacity:0.25;mix-blend-mode:screen;" />
      <div style="position:absolute;inset:0;background:linear-gradient(90deg,#0f0e0a 0%,rgba(15,14,10,0.92) 42%,transparent 100%);"></div>
      <div style="position:absolute;right:-40px;top:50%;transform:translateY(-50%);width:256px;height:256px;border-radius:999px;background:rgba(212,175,55,0.1);filter:blur(48px);"></div>
    </div>
    <div style="position:relative;display:flex;flex-wrap:wrap;align-items:center;gap:1.25rem;padding:1.35rem 1.5rem;" class="balance-inner">
      <div class="icon-bubble gold" style="width:56px;height:56px;border-radius:16px;">
        <i class="fas fa-wallet" style="font-size:1.35rem;"></i>
      </div>
      <div style="flex:1;min-width:180px;">
        <p style="margin:0 0 0.25rem;font-size:1rem;color:#d1d5db;">Total Balance</p>
        <div style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.75rem 1rem;">
          <p style="margin:0;font-size:clamp(2rem,4.5vw,2.5rem);font-weight:800;letter-spacing:-0.02em;color:#fff;">$ <?php echo number_format($total_balance, 2); ?></p>
          <p class="trend-up" style="margin:0;display:inline-flex;align-items:center;gap:0.25rem;padding-bottom:0.2rem;">
            <i class="fas fa-arrow-up" style="font-size:0.85rem;"></i>
            <?php echo (int) $active_orders; ?> Active Orders
          </p>
        </div>
        <p style="margin:0.4rem 0 0;font-size:0.95rem;color:#6b7280;">Available Balance · $ <?php echo number_format($available_balance, 2); ?> · ID <?php echo htmlspecialchars($user->login_id); ?></p>
      </div>
    </div>
  </section>

  <!-- Stats -->
  <section class="stat-grid">
    <div class="stat-card green animate-in d1">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:0.75rem;">
        <p style="margin:0;font-size:1rem;color:#9ca3af;">Total Deposits</p>
        <div class="icon-bubble green"><i class="fas fa-wallet"></i></div>
      </div>
      <p style="margin:0;font-size:1.65rem;font-weight:700;letter-spacing:-0.02em;">$ <?php echo number_format($total_deposits, 2); ?></p>
      <p class="trend-up" style="margin:0.5rem 0 0;display:inline-flex;align-items:center;gap:0.25rem;"><i class="fas fa-arrow-up" style="font-size:0.8rem;"></i> Confirmed deposits</p>
    </div>
    <div class="stat-card teal animate-in d2">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:0.75rem;">
        <p style="margin:0;font-size:1rem;color:#9ca3af;">Total Withdrawals</p>
        <div class="icon-bubble teal"><i class="fas fa-hand-holding-usd"></i></div>
      </div>
      <p style="margin:0;font-size:1.65rem;font-weight:700;letter-spacing:-0.02em;">$ <?php echo number_format($total_withdrawals, 2); ?></p>
      <p style="margin:0.5rem 0 0;font-size:0.9rem;color:#6b7280;">Approved payouts</p>
    </div>
    <div class="stat-card purple animate-in d3">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:0.75rem;">
        <p style="margin:0;font-size:1rem;color:#9ca3af;">Total Profits</p>
        <div class="icon-bubble purple"><i class="fas fa-chart-line"></i></div>
      </div>
      <p style="margin:0;font-size:1.65rem;font-weight:700;letter-spacing:-0.02em;">$ <?php echo number_format($total_profits, 2); ?></p>
      <p class="trend-up" style="margin:0.5rem 0 0;display:inline-flex;align-items:center;gap:0.25rem;"><i class="fas fa-arrow-up" style="font-size:0.8rem;"></i> Trading income</p>
    </div>
  </section>

  <!-- Promo banner -->
  <section class="promo-banner animate-in d2">
    <div style="position:absolute;inset:0;">
      <img src="images/dashboard/imgi_3_photo-1611974789855-9c2a0a7236a3.jpeg" alt="" style="width:100%;height:100%;object-fit:cover;opacity:0.3;" />
      <div style="position:absolute;inset:0;background:linear-gradient(90deg,#000 0%,rgba(0,0,0,0.85) 45%,rgba(0,0,0,0.4) 100%);"></div>
    </div>
    <div class="promo-side-img">
      <img src="images/dashboard/imgi_4_photo-1590283603385-17ffb3a7f29f.jpeg" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.4;" />
      <img src="images/dashboard/promo-bull.jpg" alt="Bull market" style="position:absolute;right:-4%;bottom:-8%;height:120%;width:auto;max-width:none;object-fit:cover;opacity:0.95;filter:sepia(0.45) saturate(1.4) brightness(1.05);-webkit-mask-image:linear-gradient(90deg,transparent 5%,black 40%);mask-image:linear-gradient(90deg,transparent 5%,black 40%);" />
      <div style="position:absolute;inset:0;background:linear-gradient(270deg,transparent,transparent,rgba(0,0,0,0.5));"></div>
    </div>
    <div style="position:relative;z-index:10;display:flex;flex-direction:column;justify-content:center;padding:1.75rem 2rem;max-width:36rem;min-height:200px;">
      <h2 style="margin:0 0 1.15rem;font-size:clamp(1.75rem,4vw,2.75rem);font-weight:800;font-style:italic;line-height:1.15;letter-spacing:-0.02em;">
        <span style="color:#fff;">Trade Smart,</span><br />
        <span class="text-gold">Grow Faster</span>
      </h2>
      <a href="trade.php" class="btn-trade" style="width:fit-content;">
        Start Trading Now
        <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </section>

  <!-- Quick Actions -->
  <section class="quick-actions animate-in d3">
    <p style="margin:0 0 1rem;font-size:1.05rem;color:#9ca3af;font-weight:600;">Quick Actions</p>
    <div class="qa-grid">
      <a href="deposit_block.php" class="qa-btn">
        <span class="qa-icon"><i class="fas fa-arrow-down"></i></span>
        <span class="qa-label">Deposit</span>
      </a>
      <a href="withdrawal_block.php" class="qa-btn">
        <span class="qa-icon"><i class="fas fa-arrow-up"></i></span>
        <span class="qa-label">Withdraw</span>
      </a>
      <a href="trade.php" class="qa-btn">
        <span class="qa-icon"><i class="fas fa-chart-bar"></i></span>
        <span class="qa-label">Invest Now</span>
      </a>
      <?php if ($hasLiveTradingPlan): ?>
      <a href="live.php" class="qa-btn">
        <span class="qa-icon"><i class="fas fa-chart-line"></i></span>
        <span class="qa-label">Live Trading</span>
      </a>
      <?php endif; ?>
      <a href="fund_transfer.php" class="qa-btn">
        <span class="qa-icon"><i class="fas fa-exchange-alt"></i></span>
        <span class="qa-label">Transfer</span>
      </a>
      <a href="report_invest.php" class="qa-btn">
        <span class="qa-icon"><i class="fas fa-history"></i></span>
        <span class="qa-label">History</span>
      </a>
    </div>
  </section>

  <div class="ge-extra-grid">
    <div class="ge-panel-card animate-in">
      <h3>Portfolio Overview</h3>
      <div class="ge-wallet-grid">
        <div class="ge-wallet-tile">
          <div class="lbl">Earning Wallet</div>
          <div class="val"><?php echo number_format($earning_wallet, 2); ?> USDT</div>
        </div>
        <div class="ge-wallet-tile">
          <div class="lbl">Package Wallet</div>
          <div class="val"><?php echo number_format($package_wallet, 2); ?> USDT</div>
        </div>
        <div class="ge-wallet-tile">
          <div class="lbl">Total Trades</div>
          <div class="val"><?php echo number_format($total_trades, 2); ?> USDT</div>
        </div>
        <div class="ge-wallet-tile">
          <div class="lbl">Total Income</div>
          <div class="val"><?php echo number_format($total_in, 2); ?> USDT</div>
        </div>
        <div class="ge-wallet-tile">
          <div class="lbl">Remaining Cap</div>
          <div class="val"><?php echo number_format($remaining_amount, 2); ?> USDT</div>
        </div>
        <div class="ge-wallet-tile">
          <div class="lbl">Team Business</div>
          <div class="val"><?php echo number_format($user->teamb * 1, 2); ?> USDT</div>
        </div>
      </div>
    </div>

    <div class="ge-panel-card animate-in">
      <h3>Grow Your Network</h3>
      <div class="ge-wallet-grid" style="margin-bottom:0.85rem;">
        <div class="ge-wallet-tile">
          <div class="lbl">Level ROI</div>
          <div class="val">$<?php echo number_format($level_roi, 2); ?></div>
        </div>
      </div>
      <div class="ge-ref">
        <label style="display:block;color:#9ca3af;font-size:0.9rem;margin-bottom:0.35rem;">Your Referral Link</label>
        <input type="text" id="referral-link1" value="<?php echo htmlspecialchars($referral_link); ?>" readonly>
        <button type="button" class="btn-outline-gold" onclick="CopyToClipboard('referral-link1')" id="referral-link1_copy"><i class="fas fa-copy"></i> Copy Link</button>
      </div>
      <p style="margin:0.75rem 0 0;color:#9ca3af;font-size:0.95rem;">
        Team: <strong style="color:#fff;"><?php echo (int) $user->teamc; ?></strong>
        · Direct Biz: <strong style="color:#fff;">$<?php echo number_format($direct_business, 2); ?></strong>
      </p>
    </div>
  </div>

  <div class="ge-panel-card animate-in">
    <h3>Latest News & Updates</h3>
    <div style="max-height:260px;overflow:auto;">
      <?php
      $news_res = my_query('SELECT title, description, datetime FROM cms WHERE mid=1 ORDER BY datetime DESC LIMIT 12');
      $has_news = false;
      while ($news = my_fetch_object($news_res)) {
          $has_news = true;
          $date = new DateTime($news->datetime);
          ?>
          <div class="ge-news-item">
            <span class="date"><?php echo $date->format('M d, Y'); ?></span>
            <h4><?php echo htmlspecialchars($news->title); ?></h4>
            <p><?php echo htmlspecialchars($news->description); ?></p>
          </div>
      <?php }
      if (!$has_news) {
          echo '<p style="color:#9ca3af;margin:0;">No news updates yet.</p>';
      }
      ?>
    </div>
  </div>
</div>

<script>
function CopyToClipboard(containerid) {
  var el = document.getElementById(containerid);
  if (!el) return;
  el.select();
  el.setSelectionRange(0, 99999);
  try {
    document.execCommand('Copy');
    var btn = document.getElementById(containerid + '_copy');
    if (btn) {
      var old = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-check"></i> Copied';
      setTimeout(function () { btn.innerHTML = old; }, 1600);
    }
  } catch (e) {}
}
</script>

<?php
$hot_news = my_fetch_object(my_query('SELECT * FROM hot_news WHERE recid=1'));
$show_hot_news_modal = ($hot_news && !empty($hot_news->image) && (int) $hot_news->status === 0);
$hot_news_version = $show_hot_news_modal
    ? md5((string) $hot_news->datetime . '|' . (string) $hot_news->image)
    : '';
$hot_news_img = $show_hot_news_modal
    ? '../uploads/' . ltrim((string) $hot_news->image, '/')
    : '';
if ($show_hot_news_modal) { ?>
  <div id="myModal-22" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
       data-hot-news-version="<?php echo htmlspecialchars($hot_news_version); ?>">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="background-color:#121212;border:1px solid rgba(212,175,55,0.3);">
        <div class="modal-header" style="background-color:#121212;border-bottom:1px solid rgba(212,175,55,0.2);">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1;">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" style="color:#d4af37;"><?php echo SITE_NAME; ?></h4>
        </div>
        <div class="modal-body text-center">
          <img src="<?php echo htmlspecialchars($hot_news_img); ?>" class="img-responsive" style="margin:0 auto;max-width:100%;" alt="News">
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<?php include_once 'footer.php'; ?>

<?php if ($show_hot_news_modal) { ?>
<script>
(function () {
  var storageKey = 'hot_news_seen_v';
  var version = <?php echo json_encode($hot_news_version); ?>;

  function alreadySeen() {
    try {
      return localStorage.getItem(storageKey) === version;
    } catch (e) {
      return false;
    }
  }

  function markSeen() {
    try {
      localStorage.setItem(storageKey, version);
    } catch (e) {}
  }

  function openHotNewsModal() {
    if (alreadySeen()) return true;
    if (typeof jQuery === 'undefined' || typeof jQuery.fn.modal !== 'function') {
      return false;
    }
    var $modal = jQuery('#myModal-22');
    if (!$modal.length) return false;
    // Bootstrap modals break inside transformed/overflow parents — move to body
    if ($modal.parent()[0] !== document.body) {
      $modal.appendTo('body');
    }
    $modal.off('hidden.bs.modal.hotnews').on('hidden.bs.modal.hotnews', markSeen);
    $modal.modal({
      backdrop: true,
      keyboard: true,
      show: true
    });
    return true;
  }

  function tryOpen(attempt) {
    if (openHotNewsModal()) return;
    if (attempt < 20) {
      setTimeout(function () { tryOpen(attempt + 1); }, 150);
    }
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(function () { tryOpen(0); }, 100);
  } else {
    document.addEventListener('DOMContentLoaded', function () {
      setTimeout(function () { tryOpen(0); }, 100);
    });
  }
})();
</script>
<?php } ?>

<?php if (SITE_CURRENCY_ == 'BNB') { ?>
  <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
  <script type="text/javascript" src="../contract/bnb/index.js"></script>
  <script type="text/javascript" src="../contract/bnb/login.js"></script>
<?php } else { ?>
  <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
  <script src="../contract/eth/index.js"></script>
  <script src="../contract/eth/login.js"></script>
<?php } ?>
