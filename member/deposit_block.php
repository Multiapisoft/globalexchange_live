<?php 
$type = (isset($_GET['type']) && in_array($_GET['type'], array(1,2,3,4,5,6,7,8,12))) ? $_GET['type'] : 1;
$typearr = array(1 => 'USDT', 2 => 'LTC', 3 => 'DOGE', 4 => 'ETH', 5 => 'BCH', 6 => 'Dash', 7 => 'XRP', 8 => 'NEO', 12 => 'TRX');
$alt_color = array(1 => '#605CA8', 2 => '#0073B7', 3 => '#F39C12', 4 => '#605CA8', 5 => '#0073B7', 6 => '#F39C12', 7 => '#262D4E', 8 => '#FF851B', 12 => '#FF851B');
$type2 = $typearr[$type];
$title = "Add Fund by ".$type2.' (Network BEP20)';
$_is_dashboard = 1;
include_once 'header.php';
include_once '../lib/own_pay/own_pay.php';
user();
$uid = $_SESSION['userid'];
$user = get_user_details($uid);
if(empty($user->pay_address)){
     $wallet = generateNewWallet();
      $sql = "UPDATE user SET `pay_address` = '".$wallet['address']."', `pay_privatekey` = '".$wallet['privateKey']."' WHERE uid = '".$uid."'" ;
      my_query($sql);
      redirect('./deposit_block.php');
}
?>
<style>
/* Fresh Deposit Theme - Same as Other Pages */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    /* Fresh Color Palette */
    --bg-primary: #f8fafc;
    --bg-secondary: #ffffff;
    --bg-accent: #e2e8f0;
    --text-primary: #1a202c;
    --text-secondary: #4a5568;
    --text-muted: #718096;
    --brand-primary: #4f46e5;
    --brand-secondary: #7c3aed;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --border: #e5e7eb;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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

/* Fresh Deposit Header */
.fresh-deposit-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    border-radius: var(--radius-lg);
    padding: 32px;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    animation: slideInFade 0.8s ease-out;
    box-shadow: var(--shadow-lg);
}

.fresh-deposit-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

    .fresh-deposit-header-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }

    .fresh-deposit-title {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .fresh-deposit-title h1 {
        font-size: 2.2rem;
        font-weight: 900;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fresh-deposit-icon {
        font-size: 2.5rem;
        opacity: 0.9;
    }

    .fresh-wallet-balance {
        background: rgba(255, 255, 255, 0.15);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        border: 1px solid rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .fresh-wallet-balance::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .fresh-balance-value {
        font-size: 2rem;
        font-weight: 800;
        color: white;
        display: flex;
        align-items: center;
        gap: 10px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fresh-balance-value i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    .fresh-balance-label {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 6px;
        font-weight: 600;
    }

    /* Fresh Cards */
    .fresh-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        margin-bottom: 32px;
        overflow: hidden;
        animation: slideUp 0.6s ease-out;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        position: relative;
        transition: all 0.3s ease;
    }

    .fresh-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .fresh-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
    }

    .fresh-deposit-row {
        display: flex;
        flex-wrap: wrap;
        gap: 32px;
    }

    .fresh-deposit-col {
        flex: 1;
        min-width: 400px;
    }

    .fresh-card-body {
        padding: 32px;
    }

    /* Fresh Mobile Responsive */
    @media (max-width: 992px) {
        .fresh-deposit-col {
            min-width: 100%;
        }

        .fresh-deposit-row {
            flex-direction: column;
            gap: 24px;
        }

        .fresh-deposit-header {
            padding: 24px;
        }

        .fresh-deposit-header-content {
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .fresh-wallet-balance {
            width: 100%;
            align-items: flex-start;
        }
    }

    @media (max-width: 768px) {
        .fresh-container {
            padding: 16px;
        }

        .fresh-card-body {
            padding: 24px;
        }

        .fresh-deposit-title h1 {
            font-size: 1.8rem;
        }

        .fresh-deposit-icon {
            font-size: 2rem;
        }

        .fresh-balance-value {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 576px) {
        .fresh-container {
            padding: 12px;
        }

        .fresh-card-body {
            padding: 20px;
        }

        .fresh-deposit-header {
            padding: 20px;
        }

        .fresh-deposit-title {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .fresh-deposit-title h1 {
            font-size: 1.5rem;
        }

        .fresh-deposit-icon {
            font-size: 1.8rem;
        }

        .fresh-balance-value {
            font-size: 1.4rem;
        }

        .fresh-balance-label {
            font-size: 0.9rem;
        }
    }

    /* Fresh Address Card */
    .fresh-address-card {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        border-radius: var(--radius-lg);
        padding: 32px;
        height: 100%;
        position: relative;
        overflow: hidden;
        color: white;
        box-shadow: var(--shadow-lg);
    }

    .fresh-address-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 25s linear infinite;
    }

    .fresh-address-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fresh-address-title i {
        font-size: 1.3rem;
        opacity: 0.9;
    }

    /* Fresh Payment Address Styling */
    .fresh-payment-address {
        background: rgba(255, 255, 255, 0.15);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        word-break: break-all;
        position: relative;
        overflow: hidden;
        flex-wrap: wrap;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
        z-index: 2;
    }

    .fresh-payment-address-text {
        flex: 1;
        font-family: 'Courier New', monospace;
        font-size: 1rem;
        color: white;
        padding-right: 16px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        line-height: 1.5;
    }

    .fresh-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: var(--radius);
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        white-space: nowrap;
        text-decoration: none;
        box-shadow: var(--shadow);
    }

    .fresh-btn-copy {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 10px 16px;
        font-size: 0.9rem;
        position: relative;
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .fresh-btn-copy:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .fresh-btn-copy.copied {
        background: rgba(255, 255, 255, 0.9);
        color: var(--success);
    }

    .fresh-btn-primary {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        color: white;
        font-size: 1.1rem;
        padding: 14px 28px;
    }

    .fresh-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
        color: white;
        text-decoration: none;
    }

    .fresh-btn-lg {
        padding: 16px 32px;
        font-size: 1.2rem;
    }

    /* Fresh QR Code */
    .fresh-qr-code-container {
        display: flex;
        justify-content: center;
        margin: 32px 0;
        position: relative;
        z-index: 2;
    }

    .fresh-qr-code-container::before {
        content: '';
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
        border-radius: 2px;
    }

    .fresh-qr-code-container::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
        border-radius: 2px;
    }

    .fresh-qr-code-container img {
        border-radius: var(--radius);
        padding: 20px;
        background: white;
        max-width: 200px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .fresh-qr-code-container img:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }

    .fresh-action-container {
        text-align: center;
        margin-top: 32px;
        position: relative;
        z-index: 2;
    }

    /* Fresh Instructions Card */
    .fresh-instructions-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        padding: 32px;
        height: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
    }

    .fresh-instructions-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--info), var(--brand-primary));
    }

    .fresh-instructions-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .fresh-instructions-title i {
        color: var(--info);
        font-size: 1.3rem;
    }

    .fresh-instruction-step {
        display: flex;
        align-items: flex-start;
        margin-bottom: 24px;
        padding: 20px;
        background: var(--bg-accent);
        border-radius: var(--radius);
        border-left: 4px solid var(--info);
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .fresh-instruction-step:hover {
        background: var(--border);
        border-left-color: var(--brand-primary);
        transform: translateX(8px);
        box-shadow: var(--shadow-lg);
    }

    .fresh-step-number {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--info) 0%, var(--brand-primary) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        margin-right: 16px;
        flex-shrink: 0;
        box-shadow: var(--shadow);
        font-size: 1rem;
    }

    .fresh-step-content {
        flex: 1;
    }

    .fresh-step-title {
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
        font-size: 1.1rem;
    }

    .fresh-step-description {
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1.6;
        font-weight: 500;
    }

    /* Modal Styling */
    .deposit-modal .modal-content {
        background: #181c27;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        color: #eaecef;
        overflow: hidden;
    }

    .deposit-modal .modal-body {
        padding: 30px;
    }

    @media (max-width: 480px) {
        .deposit-modal .modal-body {
            padding: 20px 15px;
        }

        .custom-spinner {
            width: 40px;
            height: 40px;
            margin-bottom: 15px;
        }

        .monitoring-status h4, .monitoring-result h4 {
            font-size: 18px;
            margin-top: 15px;
        }

        .monitoring-status p, .monitoring-result p {
            font-size: 12px;
        }
    }

    /* Custom spinner animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .custom-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(240, 185, 11, 0.1);
        border-radius: 50%;
        border-top: 4px solid #f0b90b;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px auto;
    }

    .monitoring-status, .monitoring-result {
        text-align: center;
    }

    .monitoring-status h4, .monitoring-result h4 {
        margin-top: 20px;
        color: #fff;
        font-size: 20px;
    }

    .text-muted {
        color: #848e9c !important;
    }

    .text-success {
        color: #0ecb81 !important;
    }

    .text-warning {
        color: #f0b90b !important;
    }

    /* Additional Mobile Optimizations */
    @media (max-width: 480px) {
        .deposit-header {
            padding: 15px;
            margin-bottom: 15px;
        }

        .deposit-title h2 {
            font-size: 20px;
        }

        .balance-value {
            font-size: 20px;
        }

        .balance-label {
            font-size: 12px;
        }

        .address-title, .instructions-title {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .network-badge {
            font-size: 10px;
            padding: 4px 8px;
        }

        .payment-address {
            padding: 10px;
            margin-bottom: 20px;
        }

        .payment-address-text {
            font-size: 11px;
            line-height: 1.4;
        }

        .btn-copy {
            padding: 6px 12px;
            font-size: 12px;
            margin-top: 8px;
            width: 100%;
            justify-content: center;
        }

        .qr-code-container img {
            max-width: 120px;
            padding: 10px;
        }

        .instruction-step {
            margin-bottom: 15px;
        }

        .step-number {
            width: 20px;
            height: 20px;
            font-size: 10px;
            margin-right: 10px;
        }

        .step-title {
            font-size: 13px;
        }

        .step-description {
            font-size: 11px;
            line-height: 1.4;
        }

        .action-container {
            margin-top: 20px;
        }

        .btn-lg {
            padding: 10px 16px;
            font-size: 14px;
        }

        .text-muted {
            font-size: 11px !important;
            line-height: 1.4;
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
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
        }
    }

    /* Network Badge */
    .network-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        background-color: rgba(240, 185, 11, 0.1);
        color: #f0b90b;
        border: 1px solid rgba(240, 185, 11, 0.2);
        margin-bottom: 15px;
    }

    .network-badge i {
        margin-right: 5px;
    }
</style>

<!-- Fresh Deposit Container -->
<div class="fresh-container">
    <!-- Fresh Deposit Header with Balance -->
    <div class="fresh-deposit-header">
        <div class="fresh-deposit-header-content">
            <div class="fresh-deposit-title">
                <div class="fresh-deposit-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h1>Deposit USDT</h1>
            </div>
            <div class="fresh-wallet-balance">
                <div class="fresh-balance-value">
                    <i class="fas fa-coins"></i>
                    <span class="fresh-count-number"><?php echo round($user->wallet_topup*1, 0);?></span>
                    <span class="currency"> <?php echo defined('SITE_CURRENCY') ? SITE_CURRENCY : 'USDT';?></span>
                </div>
                <div class="fresh-balance-label">Topup Wallet Balance</div>
            </div>
        </div>
    </div>

    <!-- Fresh Deposit Content -->
    <div class="fresh-deposit-row">
        <!-- Fresh Address Column -->
        <div class="fresh-deposit-col">
            <div class="fresh-card">
                <div class="fresh-card-body">
                    <div class="fresh-address-card">
                        <div class="fresh-address-title">
                            <i class="fas fa-qrcode"></i> Your USDT Deposit Address
                        </div>

                        <div style="background: rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; border: 1px solid rgba(255, 255, 255, 0.2); position: relative; z-index: 2;">
                            <div style="display: flex; align-items: center; color: white; font-weight: 600;">
                                <i class="fas fa-network-wired" style="margin-right: 8px; opacity: 0.9;"></i> BSC Network (BEP-20)
                            </div>
                        </div>

                        <!-- Fresh Payment Address -->
                        <div class="fresh-payment-address">
                            <div class="fresh-payment-address-text" id="_address"><h3><?php echo $user->pay_address;?></h3></div>
                            <button class="fresh-btn fresh-btn-copy" onclick="CopyToClipboard2('_address');" id="_address_copy">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>

                        <!-- Fresh QR Code -->
                        <div class="fresh-qr-code-container">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo $user->pay_address;?>" alt="QR Code" />
                        </div>

                        <!-- Fresh Action Button -->
                        <div class="fresh-action-container">
                            <button id="checkPayment" class="fresh-btn fresh-btn-primary fresh-btn-lg">
                                <i class="fas fa-sync-alt"></i> Check Payment Status
                            </button>
                            <p style="margin-top: 16px; font-size: 1rem; color: rgba(255, 255, 255, 0.8); position: relative; z-index: 2; font-weight: 500;">
                                After sending USDT to this address, click the button above to verify your payment
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fresh Instructions Column -->
        <div class="fresh-deposit-col">
            <div class="fresh-card">
                <div class="fresh-card-body">
                    <div class="fresh-instructions-card">
                        <div class="fresh-instructions-title">
                            <i class="fas fa-info-circle"></i> How to Deposit USDT
                        </div>

                        <div class="fresh-instruction-step">
                            <div class="fresh-step-number">1</div>
                            <div class="fresh-step-content">
                                <div class="fresh-step-title">Copy Your Deposit Address</div>
                                <div class="fresh-step-description">
                                    Click the "Copy" button next to your USDT deposit address or scan the QR code with your wallet app.
                                </div>
                            </div>
                        </div>

                        <div class="fresh-instruction-step">
                            <div class="fresh-step-number">2</div>
                            <div class="fresh-step-content">
                                <div class="fresh-step-title">Send USDT to This Address</div>
                                <div class="fresh-step-description">
                                    Open your wallet app and send USDT to the copied address. Make sure you're using the BSC Network (BEP-20).
                                </div>
                            </div>
                        </div>

                        <div class="fresh-instruction-step">
                            <div class="fresh-step-number">3</div>
                            <div class="fresh-step-content">
                                <div class="fresh-step-title">Verify Your Deposit</div>
                                <div class="fresh-step-description">
                                    After sending, click the "Check Payment Status" button to verify your transaction. It may take a few minutes for the network to confirm your transaction.
                                </div>
                            </div>
                        </div>

                        <div class="fresh-instruction-step">
                            <div class="fresh-step-number">4</div>
                            <div class="fresh-step-content">
                                <div class="fresh-step-title">Start Using Your USDT</div>
                                <div class="fresh-step-description">
                                    Once your deposit is confirmed, your balance will be updated automatically and you can start using your USDT.
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 30px; padding: 15px; background: rgba(14, 203, 129, 0.1); border-radius: 8px; border: 1px solid rgba(14, 203, 129, 0.2);">
                            <div style="display: flex; align-items: center; margin-bottom: 10px; color: #0ecb81;">
                                <i class="fas fa-shield-alt" style="margin-right: 10px;"></i>
                                <strong>Security Tips</strong>
                            </div>
                            <ul style="color: #848e9c; font-size: 14px; padding-left: 20px; margin-bottom: 0;">
                                <li>Always double-check the address before sending</li>
                                <li>Start with a small test amount if this is your first deposit</li>
                                <li>Ensure you're using the correct network (BSC/BEP-20)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fresh Deposit Enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add hover animations to cards
    const cards = document.querySelectorAll('.fresh-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-6px)';
            this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.15)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'var(--shadow-lg)';
        });
    });

    // Add hover animations to instruction steps
    const steps = document.querySelectorAll('.fresh-instruction-step');
    steps.forEach(step => {
        step.addEventListener('mouseenter', function() {
            const stepNumber = this.querySelector('.fresh-step-number');
            if (stepNumber) {
                stepNumber.style.transform = 'scale(1.1) rotate(5deg)';
                stepNumber.style.animation = 'pulse 2s infinite';
            }
        });

        step.addEventListener('mouseleave', function() {
            const stepNumber = this.querySelector('.fresh-step-number');
            if (stepNumber) {
                stepNumber.style.transform = 'scale(1) rotate(0deg)';
                stepNumber.style.animation = 'none';
            }
        });
    });

    // Add floating animation to deposit icon
    const depositIcon = document.querySelector('.fresh-deposit-icon');
    if (depositIcon) {
        setInterval(() => {
            depositIcon.style.transform = 'translateY(-5px)';
            setTimeout(() => {
                depositIcon.style.transform = 'translateY(0px)';
            }, 1000);
        }, 3000);
    }

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.fresh-btn');
    buttons.forEach(button => {
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

    // Add QR code hover effect
    const qrCode = document.querySelector('.fresh-qr-code-container img');
    if (qrCode) {
        qrCode.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05) rotate(2deg)';
        });

        qrCode.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    }

    // Enhanced copy button functionality
    const copyButton = document.querySelector('.fresh-btn-copy');
    if (copyButton) {
        copyButton.addEventListener('click', function() {
            // Add success animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    }

    // Add staggered animation to instruction steps
    const allSteps = document.querySelectorAll('.fresh-instruction-step');
    allSteps.forEach((step, index) => {
        step.style.animationDelay = `${index * 0.2}s`;
        step.style.animation = 'slideUp 0.6s ease-out forwards';
        step.style.opacity = '0';

        setTimeout(() => {
            step.style.opacity = '1';
        }, index * 200);
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

    .fresh-instruction-step {
        opacity: 0;
        transform: translateY(20px);
    }

    .fresh-deposit-icon {
        transition: transform 1s ease-in-out;
    }
`;
document.head.appendChild(style);
</script>

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
    const text = document.getElementById(containerid).innerText;
    navigator.clipboard.writeText(text).then(() => {
        // Show a stylish notification
        const copyBtn = document.getElementById(containerid + '_copy');
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied';
        copyBtn.classList.add('copied');

        // Reset after 2 seconds
        setTimeout(() => {
            copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy';
            copyBtn.classList.remove('copied');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ' + err);

        // Fallback to old method if clipboard API fails
        if (window.getSelection) {
            if (window.getSelection().empty) {
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {
            document.selection.empty();
        }

        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select().createTextRange();
            document.execCommand("Copy");
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().addRange(range);
            document.execCommand("Copy");
            $('#'+containerid+'_copy').text('Copied');
            $('#'+containerid+'_copy').addClass('btn-success');
            $('#'+containerid+'_copy').removeClass('btn-violet');
        }
    });
}

// Payment monitoring functionality
$('#checkPayment').click(function() {
    const walletAddress = $('#_address').text();
    startMonitoring(walletAddress);
});

const MAX_RETRIES = 4;  // Total of 2 minutes (4 * 30 seconds)
let retryCount = 0;

function startMonitoring(address) {
    // Show modal
    $('#monitorModal').modal({
        backdrop: 'static',
        keyboard: false
    });

    // Reset retry count when starting fresh
    retryCount = 0;

    // Update initial status
    updateStatusMessage(retryCount);

    // Start monitoring with timeout
    $.ajax({
        url: 'deposit_block2_model.php',
        method: 'POST',
        data: {
            address: address
        },
        timeout: 30000, // 30 second timeout
        success: function(response) {
            try {
                console.log(response);

                // Check if the response contains HTML error messages
                if (response.includes('<br />') || response.includes('<b>Warning</b>')) {
                    // Extract the JSON part if it exists
                    const jsonMatch = response.match(/\{"status":.*\}/);
                    if (jsonMatch) {
                        response = jsonMatch[0];
                    } else {
                        handleRetry(address);
                        return;
                    }
                }

                const result = JSON.parse(response);
                if (result.status === 'success') {
                    showSuccess();
                } else {
                    // Check if there's no balance
                    if (result.message && result.message.includes('No significant usdt balance found')) {
                        showError('No USDT balance found in this address. Please send USDT to the address first.');
                    } else {
                        handleRetry(address);
                    }
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                showError('Invalid response from server. Please try again later.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
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

    // Wait 5 seconds before trying again
    setTimeout(function() {
        startMonitoring(address);
    }, 5000);
}

function updateStatusMessage(currentRetry) {
    const timeElapsed = currentRetry * 30;
    const messages = [
        'Checking your payment...',
        'Still checking... (30 seconds elapsed)',
        'Still checking... (1 minute elapsed)',
        'Final check... (1.5 minutes elapsed)'
    ];

    $('#statusText').text(messages[currentRetry] || 'Checking payment...');
    $('#statusDetails').html(`
        <p>This may take a few moments</p>
        <small class="text-muted">Attempt ${currentRetry + 1} of ${MAX_RETRIES}</small>
    `);
}

function showSuccess() {
    $('.monitoring-status').hide();
    $('.monitoring-result').show();
    $('#resultIcon').html('<i class="fas fa-check-circle text-success fa-3x"></i>');
    $('#resultText').text('Payment Confirmed!');
    $('#resultDetails').text('Your payment has been processed successfully');

    // Reload page after 3 seconds
    setTimeout(function() {
        window.location.reload();
    }, 1000);
}

function showError(message) {
    $('.monitoring-status').hide();
    $('.monitoring-result').show();
    $('#resultIcon').html('<i class="fas fa-exclamation-circle text-warning fa-3x"></i>');
    $('#resultText').text('Payment Not Found');

    // Add a help link for common issues
    const helpText = message.includes('No USDT balance') ?
        '<div style="margin-top: 10px; font-size: 13px; color: #848e9c;">Make sure you have sent USDT to this address using the BSC network.</div>' : '';

    $('#resultDetails').html(`
        ${message}<br>
        ${helpText}
        <button class="btn btn-primary mt-3" onclick="retryFromStart()">Check Again</button>
    `);
       setTimeout(function() {
        window.location.reload();
    }, 1000);
}

function retryFromStart() {
    const walletAddress = $('#_address').text();
    $('.monitoring-result').hide();
    $('.monitoring-status').show();
    startMonitoring(walletAddress);
}
</script>
