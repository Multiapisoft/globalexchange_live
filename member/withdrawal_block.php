<?php
$type = (isset($_GET['type']) && in_array($_GET['type'], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11))) ? $_GET['type'] : 1;
$typearr = array(10 => 'USDT', 11 => 'USDT');
$type2 = $typearr[$type] ?? '';
$title = 'Withdrawal ' . $type2;
$_is_dashboard = 1;
include_once 'header.php';
$wallet_field = ($type == 11) ? 'wallet_promo' : 'wallet';
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
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Fresh Withdrawal Header */
    .fresh-withdrawal-header {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
        border-radius: var(--radius-lg);
        padding: 32px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .fresh-withdrawal-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    .fresh-withdrawal-header-content {
        position: relative;
        z-index: 2;
    }

    .fresh-withdrawal-header h1 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 12px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
    }

    .fresh-withdrawal-header p {
        font-size: 2rem;
        font-weight: 600;
        opacity: 0.9;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .fresh-withdrawal-header-icon {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.9;
    }

    /* Fresh Balance Display */
    .fresh-balance-display {
        background: rgba(255, 255, 255, 0.15);
        border-radius: var(--radius);
        padding: 20px;
        margin-top: 24px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        text-align: center;
    }

    .fresh-balance-value {
        font-size: 2.2rem;
        font-weight: 800;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .fresh-balance-label {
        font-size: 2rem;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 600;
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

    /* Fresh Card Body */
    .fresh-card-body {
        padding: 40px;
    }

    /* Fresh Form Title */
    .fresh-form-title {
        text-align: center;
        margin-bottom: 32px;
        color: var(--brand-primary);
        font-size: 2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    /* Fresh Form Styling */
    .fresh-form-group {
        margin-bottom: 28px;
    }

    .fresh-form-label {
        display: block;
        margin-bottom: 12px;
        font-size: 2rem;
        color: var(--text-secondary);
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .fresh-form-control {
        width: 100%;
        background: var(--bg-secondary);
        border: 2px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        color: var(--text-primary);
        font-size: 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .fresh-form-control:focus {
        outline: none;
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        transform: translateY(-2px);
    }

    .fresh-form-control::placeholder {
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Fresh Form Help Text */
    .fresh-form-help {
        color: var(--text-muted);
        margin-top: 8px;
        display: block;
        font-size: 2rem;
        font-weight: 500;
    }

    /* Fresh Button */
    .fresh-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 16px 32px;
        border-radius: var(--radius);
        font-size: 2rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: var(--shadow);
    }

    .fresh-btn-success {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
    }

    .fresh-btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, var(--success) 100%);
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .fresh-btn-success:active {
        transform: translateY(-1px);
    }

    /* Fresh Card Footer */
    .fresh-card-footer {
        padding: 24px 40px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: center;
        background: var(--bg-accent);
    }

    /* Fresh Mobile Responsive */
    @media (max-width: 768px) {
        .fresh-container {
            padding: 16px;
        }

        .fresh-withdrawal-header {
            padding: 24px;
        }

        .fresh-withdrawal-header h1 {
            font-size: 2rem;
            font-weight: 900;
            flex-direction: column;
            gap: 12px;
        }

        .fresh-withdrawal-header p {
            font-size: 1rem;
            font-weight: 600;
        }

        .fresh-card-body {
            padding: 24px;
        }

        .fresh-card-footer {
            padding: 20px 24px;
        }

        .fresh-form-title {
            font-size: 1.5rem;
            flex-direction: column;
            gap: 8px;
        }

        .fresh-btn {
            width: 100%;
            padding: 18px 24px;
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

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
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
    .d-none{
        display : none !important;
    }
    .d-block{
        display : block !important;
    }
</style>

<!-- Fresh Withdrawal Container -->
<div class="fresh-container">
    <!-- Fresh Withdrawal Header -->
    <div class="fresh-withdrawal-header">
        <div class="fresh-withdrawal-header-content">
            <div class="fresh-withdrawal-header-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <h1><i class="fas fa-arrow-up"></i> Withdraw <?php echo $type2; ?></h1>
            <p>Secure and fast withdrawal process</p>

            <!-- Fresh Balance Display -->
            <div class="fresh-balance-display">
                <div class="fresh-balance-value">
                    <i class="fas fa-wallet"></i>
                    <span class="count-number"><?php echo $user->$wallet_field * 1; ?></span>
                    <span class="currency"> <?php echo SITE_CURRENCY; ?></span>
                </div>
                <div class="fresh-balance-label">
                    <?php echo ($type == 11) ? 'Working Wallet Balance' : 'Wallet Balance'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Fresh Withdrawal Card -->
    <div class="fresh-card">
        <div class="fresh-card-body">
            <h3 class="fresh-form-title">
                <i class="fas fa-hand-holding-usd"></i>
                Request Withdrawal
            </h3>

            <form action="withdrawal_block_model.php" method="post" onSubmit="return abc_();">
                <div class="fresh-form-group">
                    <label for="amount" class="fresh-form-label"><?php echo $type2; ?> Amount *</label>
                    <input class="fresh-form-control" type="text" id="amount" name="amount" value="" maxlength="10"
                        required="required" placeholder="Enter amount to withdraw">
                    <small class="fresh-form-help">Available balance: <?php echo $user->$wallet_field * 1; ?>
                        <?php echo SITE_CURRENCY; ?> | Min $10 | 2% Admin & Service Charge applies</small>
                </div>

                <div class="fresh-form-group d-none" id="OtpInputFieldSection">
                    <label for="otp" class="fresh-form-label">Email OTP *</label>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <input class="fresh-form-control" type="text" id="otp" name="otp" value="" maxlength="20"
                            inputmode="numeric" autocomplete="one-time-code"
                            required="required" placeholder="Enter 6-digit OTP">
                    </div>
                    <small class="fresh-form-help">Click on "Send OTP" to receive a code on your registered email, then
                        enter it here to verify withdrawal.</small>
                </div>

                <div class="fresh-card-footer" id="sendOtpBtnFooter">
                    <button type="button" id="sendOtpBtn" class="fresh-btn fresh-btn-success"
                        style="white-space: nowrap;">
                        <i class="fas fa-envelope" style="margin-right: 8px;"></i> Send OTP
                    </button>
                </div>
                <div class="fresh-card-footer d-none" id="submitFormBtnFooter">
                    <input type="hidden" name="type" value="<?php echo $type; ?>" />
                    <button type="submit" class="fresh-btn fresh-btn-success">
                        <i class="fas fa-paper-plane" style="margin-right: 12px;"></i> Submit Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<?php if (SITE_CURRENCY_ == 'TRX') { ?>
    <script src="https://cdn.jsdelivr.net/npm/tronweb@2.4.1/dist/TronWeb.node.min.js"></script>
    <?php /* <script src="../contract/tron/TronWeb.js"></script> */ ?>
    <script src="../contract/tron/index.js"></script>
    <script src="../contract/tron/login.js"></script>
<?php } elseif (SITE_CURRENCY_ == 'USDT') { ?>
    <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
    <script type="text/javascript" src="../contract/USDT/index.js"></script>
    <script type="text/javascript" src="../contract/USDT/login.js"></script>
    <script type="text/javascript" src="../contract/USDT/script.js"></script>
<?php } else { ?>
    <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
    <script src="../contract/eth/index.js"></script>
    <script src="../contract/eth/login.js"></script>
<?php } ?>
<script>
    // Fresh Withdrawal Enhancements
    document.addEventListener('DOMContentLoaded', function () {
        // Add focus animations to form elements
        const formControl = document.querySelector('.fresh-form-control');
        const submitBtn = document.querySelector('.fresh-btn-success');

        if (formControl) {
            formControl.addEventListener('focus', function () {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 8px 25px rgba(79, 70, 229, 0.15)';
            });

            formControl.addEventListener('blur', function () {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1)';
            });

            // Add input validation styling
            formControl.addEventListener('input', function () {
                const value = parseFloat(this.value);
                const balance = parseFloat('<?php echo $user->$wallet_field * 1; ?>');

                if (value > balance) {
                    this.style.borderColor = 'var(--danger)';
                    this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
                } else if (value > 0) {
                    this.style.borderColor = 'var(--success)';
                    this.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
                } else {
                    this.style.borderColor = 'var(--border)';
                    this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1)';
                }
            });
        }

        // Add button hover animations
        if (submitBtn) {
            submitBtn.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-3px) scale(1.02)';
            });

            submitBtn.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0) scale(1)';
            });
        }

        // Add pulse animation to balance value
        const balanceValue = document.querySelector('.fresh-balance-value');
        if (balanceValue) {
            setTimeout(() => {
                balanceValue.style.animation = 'pulse 2s ease-in-out';
            }, 500);
        }
    });

    // Enhanced authorization function
    async function abc_() {
        const submitBtn = document.querySelector('.fresh-btn-success');
        const otpInput = document.getElementById('otp');

        // Add loading state
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 12px;"></i> Processing...';
            submitBtn.disabled = true;
        }

        try {
            if (!otpInput || !otpInput.value.replace(/\D+/g, '')) {
                showErrorMessage('Please enter the OTP sent to your registered email.');
                return false;
            }
            otpInput.value = otpInput.value.replace(/\D+/g, '');

            if (typeof web3 !== 'undefined') {
                await ethereum.enable();
                var waddress = await getAccounts();
                if (typeof waddress !== 'undefined' && waddress == '<?php echo $user->USDT_address; ?>') {
                    return true;
                }
                else {
                    showErrorMessage('Authorization error: Wallet address mismatch');
                    return false;
                }
            }
            else {
                showErrorMessage('Authorization error: Web3 not found');
                return false;
            }
        } catch (error) {
            showErrorMessage('Authorization error: ' + error.message);
            return false;
        } finally {
            // Reset button state
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-paper-plane" style="margin-right: 12px;"></i> Submit Withdrawal';
                submitBtn.disabled = false;
            }
        }
    }

    // Enhanced error message function
    function showErrorMessage(message) {
        const toast = document.createElement('div');
        toast.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                box-shadow: var(--shadow-lg);
                z-index: 10000;
                font-weight: 600;
                font-size: 1rem;
                animation: slideInLeft 0.3s ease-out;
                max-width: 400px;
            ">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                ${message}
            </div>
        `;
        document.body.appendChild(toast);

        // Remove toast after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeInUp 0.3s ease-out reverse';
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 5000);
    }

    // Send withdrawal OTP to registered email
    document.addEventListener('DOMContentLoaded', function () {
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        const otpInputEl = document.getElementById('otp');
        if (otpInputEl) {
            otpInputEl.addEventListener('input', function () {
                this.value = this.value.replace(/\D+/g, '').slice(0, 6);
            });
            otpInputEl.addEventListener('paste', function (e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text') || '';
                this.value = text.replace(/\D+/g, '').slice(0, 6);
            });
        }
        if (!sendOtpBtn) return;

        sendOtpBtn.addEventListener('click', function () {
            sendOtpBtn.disabled = true;
            const originalHtml = sendOtpBtn.innerHTML;
            sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> Sending...';

            fetch('otp.php?type=withdrawal', { credentials: 'same-origin' })
                .then(async (response) => {
                    const text = await response.text();
                    let data = null;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        throw new Error((text || '').replace(/<[^>]+>/g, ' ').trim().slice(0, 120) || 'Invalid OTP server response');
                    }
                    return data;
                })
                .then(data => {
                    if (data && data._status == 1) {
                        alert('OTP sent successfully to your registered email.');

                        const otpSection = document.getElementById('OtpInputFieldSection');
                        const submitFooter = document.getElementById('submitFormBtnFooter');
                        const sendOtpFooter = document.getElementById('sendOtpBtnFooter');

                        if (otpSection) {
                            otpSection.classList.remove('d-none');
                            otpSection.classList.add('d-block');
                        }

                        if (submitFooter) {
                            submitFooter.classList.remove('d-none');
                            submitFooter.classList.add('d-block');
                        }

                        if (sendOtpFooter) {
                            sendOtpFooter.classList.add('d-none');
                        }
                    } else {
                        const msg = data && data.msg ? data.msg : 'Failed to send OTP.';
                        showErrorMessage(msg);
                    }
                })
                .catch(err => {
                    showErrorMessage(err && err.message ? err.message : 'Unable to send OTP. Please try again.');
                })
                .finally(() => {
                    sendOtpBtn.disabled = false;
                    sendOtpBtn.innerHTML = originalHtml;
                });
        });
    });
</script>