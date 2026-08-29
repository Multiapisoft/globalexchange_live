<?php
$title = "P2P Transfer";
$_is_dashboard = 1;
include_once 'header.php';

$fund_type_arr = get_fund_type();
?>
<style>
    /* Binance-inspired theme */
    body, #page-wrapper {
        background-color: #0b0e11;
        color: #eaecef;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    .content-header {
        display: none;
    }

    .p2p-wrapper {
        padding: 15px;
        color: #eaecef;
        margin: 0 auto;
    }

    /* P2P Header */
    .p2p-header {
        background: linear-gradient(135deg, #181c27 0%, #0b0e11 100%);
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        animation: slideInFade 0.8s ease-out;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .p2p-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #f0b90b, transparent);
    }

    .wallet-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .wallet-card {
        background: linear-gradient(135deg, #1c2127 0%, #121517 100%);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .wallet-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #f0b90b, transparent);
    }

    .wallet-amount {
        font-size: 24px;
        font-weight: 600;
        color: #f0b90b;
        margin-bottom: 5px;
    }

    .wallet-label {
        color: #848e9c;
        font-size: 14px;
    }

    /* Transfer Form */
    .transfer-card {
        background: linear-gradient(135deg, #181c27 0%, #0b0e11 100%);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .transfer-card-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        color: #848e9c;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .form-control {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #eaecef;
        padding: 12px 15px;
        height: auto;
        transition: all 0.3s;
    }

    .form-control:focus {
        background: rgba(0, 0, 0, 0.3);
        border-color: #f0b90b;
        color: #fff;
        box-shadow: none;
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23848e9c' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px;
    }

    .btn-transfer {
        background: #f0b90b;
        border: none;
        border-radius: 8px;
        color: #0b0e11;
        font-weight: 600;
        padding: 12px 25px;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-transfer:hover {
        background: #cea000;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .p2p-header {
            padding: 20px;
        }

        .transfer-card-body {
            padding: 20px;
        }
    }

    /* Add new styles for user ID verification */
    .user-verify {
        font-size: 13px;
        margin-top: 5px;
        display: none;
    }

    .user-verify.success {
        color: #0ecb81;
        display: block;
    }

    .user-verify.error {
        color: #f6465d;
        display: block;
    }

    .input-group {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        width: 100%;
    }

    .input-group .form-control {
        position: relative;
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
        margin-bottom: 0;
    }

    .input-group-append {
        margin-left: -1px;
        display: flex;
    }

    .verify-btn {
        background: #2b3139;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #f0b90b;
        padding: 0 15px;
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        transition: all 0.3s;
    }

    .verify-btn:hover {
        background: #363c45;
    }
</style>

<div class="p2p-wrapper">
    <!-- Wallet Balance Cards -->
    <!--<div class="wallet-cards">-->
    <!--    <div class="wallet-card">-->
    <!--        <div class="wallet-amount">-->
    <!--            <?php echo round($user->wallet_topup*1, 2);?> <?php echo SITE_CURRENCY;?>-->
    <!--        </div>-->
    <!--        <div class="wallet-label">-->
    <!--            <i class="fas fa-wallet"></i> Spot Wallet-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
<div class="wallet-cards">
        <div class="wallet-card">
            <div class="wallet-amount">
                <?php echo round($user->wallet*1, 2);?> <?php echo SITE_CURRENCY;?>
            </div>
            <div class="wallet-label">
                <i class="fas fa-wallet"></i> Wallet
            </div>
        </div>
    </div>
    <!-- Transfer Form -->
    <div class="transfer-card">
        <div class="transfer-card-body">
            <form action="game_transfer_model.php" method="post" id="p2pTransferForm">
                <!-- P2P User ID Field -->
                <div class="form-group">
                    <label class="form-label" for="login_id">Receiver's Phone Number</label>
                    <div class="input-group">
                        <input class="form-control" type="text" id="receiver_id" name="login_id" maxlength="20" required placeholder="Enter receiver's user ID">
                       
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="amount">Amount</label>
                    <input class="form-control" type="text" id="amount" name="amount" maxlength="20" required placeholder="Enter amount">
                </div>

                <div class="form-group">
                    <label class="form-label" for="type">Fund Transfer From</label>
                     <select class="form-control" name="type" id="type" required="required">
                               <option value="wallet">Wallet</option>
                            </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="remark">Remark</label>
                    <textarea class="form-control" id="remark" name="remark" rows="3" required maxlength="250" placeholder="Enter your remark"></textarea>
                </div>

                <button type="submit" class="btn btn-transfer">
                    <i class="fas fa-exchange-alt"></i> Transfer To Game
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function verifyUser() {
    const userId = document.getElementById('receiver_id').value;
    const messageDiv = document.getElementById('userVerifyMessage');
    
    if (!userId) {
        messageDiv.innerHTML = 'Please enter a user ID';
        messageDiv.className = 'user-verify error';
        return;
    }
}

// Form validation
document.getElementById('p2pTransferForm').onsubmit = function(e) {
    const receiverId = document.getElementById('receiver_id').value;
    const amount = document.getElementById('amount').value;
    
    if (!receiverId || !amount) {
        e.preventDefault();
        alert('Please fill in all required fields');
        return false;
    }
    
    if (isNaN(amount) || amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid amount');
        return false;
    }
    
    return true;
};
</script>

<?php include_once 'footer.php'; ?>
