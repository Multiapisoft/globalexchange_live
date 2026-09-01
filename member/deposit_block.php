<?php
$type = (isset($_GET['type']) && in_array($_GET['type'], array(1, 2, 3, 4, 5, 6, 7, 8, 12))) ? $_GET['type'] : 1;
$typearr = array(1 => 'USDT', 2 => 'LTC', 3 => 'DOGE', 4 => 'ETH', 5 => 'BCH', 6 => 'Dash', 7 => 'XRP', 8 => 'NEO', 12 => 'TRX');
$alt_color = array(1 => '#605CA8', 2 => '#0073B7', 3 => '#F39C12', 4 => '#605CA8', 5 => '#0073B7', 6 => '#F39C12', 7 => '#262D4E', 8 => '#FF851B', 12 => '#FF851B');
$type2 = $typearr[$type];
$title = "Add Fund by " . $type2 . ' (Network BEP20)';
$_is_dashboard = 1;
include_once 'header.php';
include_once '../lib/own_pay/own_pay.php';
user();
$uid = $_SESSION['userid'];
$user = get_user_details($uid);
if (empty($user->pay_address)) {
    $wallet = generateNewWallet();
    $sql = "UPDATE user SET `pay_address` = '" . $wallet['address'] . "', `pay_privatekey` = '" . $wallet['privateKey'] . "' WHERE uid = '" . $uid . "'";
    my_query($sql);
    redirect('./deposit_block.php');
}

$pay_address = $user->pay_address;
$currency_label = defined('SITE_CURRENCY') ? SITE_CURRENCY : 'USDT';
$topup_balance = round($user->wallet_topup * 1, 2);
?>

<style>
/* Deposit — match Desktop deposit.html */
.content-header { display: none !important; }

.ge-dep {
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
.ge-dep *,
.ge-dep *::before,
.ge-dep *::after { box-sizing: border-box; }

.ge-dep-head h1 {
  margin: 0;
  font-size: clamp(1.15rem, 2.5vw, 1.35rem);
  font-weight: 700;
  color: #fff;
}
.ge-dep-head p {
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

.ge-warn {
  border-color: rgba(239, 107, 107, 0.35);
  background: rgba(239, 107, 107, 0.06);
  color: #fca5a5;
  font-size: 0.92rem;
  font-weight: 500;
}
.ge-warn strong { color: #fecaca; }

.ge-dep-grid {
  display: grid;
  gap: 1.5rem;
}
@media (min-width: 768px) {
  .ge-dep-grid {
    grid-template-columns: 1fr auto;
    align-items: start;
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
.ge-dep-grid h2 {
  margin: 0 0 1rem;
  font-size: 1.25rem;
  font-weight: 700;
  color: #fff;
}
.ge-dep-grid h2 span {
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.ge-copy-row {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
@media (min-width: 640px) {
  .ge-copy-row {
    flex-direction: row;
    align-items: stretch;
  }
}
.ge-copy-row .addr {
  flex: 1;
  min-width: 0;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.28);
  background: #0a0a0a;
  color: #e5e5e5;
  padding: 0.75rem 0.9rem;
  font-size: 0.8rem;
  font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
  word-break: break-all;
  margin: 0;
  line-height: 1.45;
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
  text-decoration: none;
}
.ge-btn-gold:hover {
  filter: brightness(1.06);
  color: #1a1408;
  text-decoration: none;
}
.ge-btn-gold.copied {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: #fff;
}

.ge-btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.7rem 1.2rem;
  border-radius: 12px;
  font-family: inherit;
  font-size: 0.92rem;
  font-weight: 650;
  color: #f5c842;
  background: transparent;
  border: 1px solid rgba(212, 175, 55, 0.65);
  cursor: pointer;
}
.ge-btn-outline:hover {
  background: rgba(212, 175, 55, 0.1);
  color: #ffe566;
}

.ge-steps {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
}
.ge-steps li {
  display: flex;
  gap: 0.55rem;
  font-size: 0.9rem;
  color: #9ca3af;
}
.ge-steps li span {
  color: #d4af37;
  font-weight: 700;
  flex-shrink: 0;
}

.ge-qr-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.ge-qr {
  width: 176px;
  height: 176px;
  border-radius: 16px;
  background: #fff;
  padding: 12px;
  box-shadow: 0 0 30px rgba(212, 175, 55, 0.2);
}
.ge-qr img {
  width: 100%;
  height: 100%;
  display: block;
}
.ge-qr-note {
  margin: 0.75rem 0 0;
  font-size: 0.72rem;
  color: #9ca3af;
  text-align: center;
}

.ge-balance {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
}
.ge-balance .lbl {
  font-size: 0.78rem;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 600;
  margin: 0 0 0.25rem;
}
.ge-balance .val {
  margin: 0;
  font-size: 1.45rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.ge-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-top: 1.15rem;
}

.ge-tips {
  border-color: rgba(34, 197, 94, 0.28);
  background: rgba(34, 197, 94, 0.06);
}
.ge-tips h3 {
  margin: 0 0 0.5rem;
  font-size: 0.95rem;
  font-weight: 700;
  color: #86efac;
  display: flex;
  align-items: center;
  gap: 0.45rem;
}
.ge-tips ul {
  margin: 0;
  padding-left: 1.15rem;
  color: #9ca3af;
  font-size: 0.88rem;
}
.ge-tips li { margin: 0.25rem 0; }

/* Modal */
.deposit-modal .modal-content {
  background: #141414 !important;
  border: 1px solid rgba(212, 175, 55, 0.28) !important;
  border-radius: 16px !important;
  color: #fff !important;
}
.deposit-modal .modal-body {
  padding: 1.75rem 1.5rem !important;
  text-align: center;
}
.deposit-modal .monitoring-status h4,
.deposit-modal .monitoring-result h4 {
  color: #fff !important;
  margin: 1rem 0 0.35rem;
  font-size: 1.1rem;
}
.deposit-modal .text-muted,
.deposit-modal #statusDetails,
.deposit-modal #resultDetails {
  color: #9ca3af !important;
}
.custom-spinner {
  width: 48px;
  height: 48px;
  margin: 0 auto;
  border: 3px solid rgba(212, 175, 55, 0.2);
  border-top-color: #d4af37;
  border-radius: 50%;
  animation: geSpin 0.8s linear infinite;
}
@keyframes geSpin {
  to { transform: rotate(360deg); }
}

.animate-in {
  animation: geFadeUp 0.4s ease both;
}
@keyframes geFadeUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="ge-dep">
  <div class="ge-dep-head">
    <h1>Deposit USDT</h1>
    <p>BEP-20 network only</p>
  </div>

  <section class="ge-panel ge-balance animate-in">
    <div>
      <p class="lbl">Topup Wallet Balance</p>
      <p class="val">$<?php echo number_format($topup_balance, 2); ?> <?php echo htmlspecialchars($currency_label); ?></p>
    </div>
    <a href="report_deposit_block.php" class="ge-btn-outline">Deposit History</a>
  </section>

  <section class="ge-panel ge-dep-grid animate-in">
    <div>
      <p class="ge-label-gold">Deposit address</p>
      <h2>Official USDT <span>BEP-20</span> address</h2>

      <div class="ge-copy-row">
        <p class="addr" id="_address"><?php echo htmlspecialchars($pay_address); ?></p>
        <button type="button" class="ge-btn-gold" onclick="CopyToClipboard2('_address');" id="_address_copy">
          <i class="fas fa-copy"></i> Copy
        </button>
      </div>

      <ul class="ge-steps">
        <li><span>1.</span> Copy the address or scan QR</li>
        <li><span>2.</span> Send USDT on BNB Smart Chain (BEP-20)</li>
        <li><span>3.</span> Wait for network confirmation — then verify below</li>
        <li><span>4.</span> Funds credit to your Topup Wallet</li>
      </ul>

      <div class="ge-actions">
        <button type="button" id="checkPayment" class="ge-btn-gold">
          <i class="fas fa-sync-alt"></i> Check Payment Status
        </button>
      </div>
    </div>

    <div class="ge-qr-wrap">
      <div class="ge-qr">
        <img
          src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&amp;data=<?php echo urlencode($pay_address); ?>"
          alt="Deposit QR code"
          width="152"
          height="152"
        />
      </div>
      <p class="ge-qr-note">Scan with your wallet app</p>
    </div>
  </section>

  <section class="ge-panel ge-tips animate-in">
    <h3><i class="fas fa-shield-alt"></i> Security tips</h3>
    <ul>
      <li>Always double-check the address before sending</li>
      <li>Start with a small test amount if this is your first deposit</li>
      <li>Ensure you're using the correct network (BSC / BEP-20)</li>
    </ul>
  </section>
</div>

<!-- Loading Modal -->
<div class="modal fade deposit-modal" id="monitorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <div class="monitoring-status">
          <div class="custom-spinner"></div>
          <h4 id="statusText">Checking your payment...</h4>
          <p id="statusDetails" class="text-muted">Please wait while we verify your transaction</p>
        </div>
        <div class="monitoring-result" style="display: none;">
          <div id="resultIcon"></div>
          <h4 id="resultText"></h4>
          <p id="resultDetails" class="text-muted"></p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once 'footer.php'; ?>

<script>
function CopyToClipboard2(containerid) {
  var text = document.getElementById(containerid).innerText;
  var copyBtn = document.getElementById(containerid + '_copy');

  function markCopied() {
    if (!copyBtn) return;
    copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied';
    copyBtn.classList.add('copied');
    setTimeout(function () {
      copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy';
      copyBtn.classList.remove('copied');
    }, 2000);
  }

  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(markCopied).catch(function () {
      fallbackCopy(text);
      markCopied();
    });
  } else {
    fallbackCopy(text);
    markCopied();
  }
}

function fallbackCopy(text) {
  var ta = document.createElement('textarea');
  ta.value = text;
  document.body.appendChild(ta);
  ta.select();
  try { document.execCommand('copy'); } catch (e) {}
  document.body.removeChild(ta);
}

$('#checkPayment').click(function () {
  var walletAddress = $('#_address').text();
  startMonitoring(walletAddress);
});

var MAX_RETRIES = 4;
var retryCount = 0;

function startMonitoring(address) {
  $('#monitorModal').modal({
    backdrop: 'static',
    keyboard: false
  });

  retryCount = 0;
  updateStatusMessage(retryCount);

  $.ajax({
    url: 'deposit_block2_model.php',
    method: 'POST',
    data: { address: address },
    timeout: 30000,
    success: function (response) {
      try {
        if (response.includes('<br />') || response.includes('<b>Warning</b>')) {
          var jsonMatch = response.match(/\{"status":.*\}/);
          if (jsonMatch) {
            response = jsonMatch[0];
          } else {
            handleRetry(address);
            return;
          }
        }

        var result = JSON.parse(response);
        if (result.status === 'success') {
          showSuccess();
        } else if (result.message && result.message.includes('No significant usdt balance found')) {
          showError('No USDT balance found in this address. Please send USDT to the address first.');
        } else {
          handleRetry(address);
        }
      } catch (e) {
        showError('Invalid response from server. Please try again later.');
      }
    },
    error: function (xhr, status) {
      if (status === 'timeout') {
        handleRetry(address);
      } else {
        showError('Connection error occurred. Please try again later.');
      }
    }
  });
}

function handleRetry(address) {
  retryCount++;
  if (retryCount >= MAX_RETRIES) {
    showError('Payment not detected after multiple attempts. Please try again or contact support if you have already sent the payment.');
    return;
  }
  updateStatusMessage(retryCount);
  setTimeout(function () {
    startMonitoring(address);
  }, 5000);
}

function updateStatusMessage(currentRetry) {
  var messages = [
    'Checking your payment...',
    'Still checking... (30 seconds elapsed)',
    'Still checking... (1 minute elapsed)',
    'Final check... (1.5 minutes elapsed)'
  ];
  $('#statusText').text(messages[currentRetry] || 'Checking payment...');
  $('#statusDetails').html(
    '<p>This may take a few moments</p><small class="text-muted">Attempt ' +
    (currentRetry + 1) + ' of ' + MAX_RETRIES + '</small>'
  );
}

function showSuccess() {
  $('.monitoring-status').hide();
  $('.monitoring-result').show();
  $('#resultIcon').html('<i class="fas fa-check-circle text-success fa-3x"></i>');
  $('#resultText').text('Payment Confirmed!');
  $('#resultDetails').text('Your payment has been processed successfully');
  setTimeout(function () {
    window.location.reload();
  }, 1000);
}

function showError(message) {
  $('.monitoring-status').hide();
  $('.monitoring-result').show();
  $('#resultIcon').html('<i class="fas fa-exclamation-circle text-warning fa-3x"></i>');
  $('#resultText').text('Payment Not Found');
  var helpText = message.includes('No USDT balance')
    ? '<div style="margin-top: 10px; font-size: 13px; color: #9ca3af;">Make sure you have sent USDT to this address using the BSC network.</div>'
    : '';
  $('#resultDetails').html(
    message + '<br>' + helpText +
    '<button class="ge-btn-gold mt-3" onclick="retryFromStart()">Check Again</button>'
  );
  setTimeout(function () {
    window.location.reload();
  }, 1000);
}

function retryFromStart() {
  var walletAddress = $('#_address').text();
  $('.monitoring-result').hide();
  $('.monitoring-status').show();
  startMonitoring(walletAddress);
}
</script>
