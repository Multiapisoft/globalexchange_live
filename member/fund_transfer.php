<?php
$title = "P2P Transfer";
$_is_dashboard = 1;
include_once 'header.php';

$fund_type_arr = get_fund_type();
?>
<style>
    /* P2P page styled according to dashboard theme variables */
    .content-header {
        display: none;
    }

    .p2p-wrapper {
        padding: 15px;
        color: var(--text-primary);
            margin: 0px 67px 0 19px;
    }

    /* P2P Header */
    .p2p-header {
        background: linear-gradient(135deg, var(--secondary-bg) 0%, var(--primary-bg) 100%);
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        animation: slideInFade 0.8s ease-out;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border-color);
    }

    .p2p-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #4f46e5, transparent);
    }

    .wallet-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .wallet-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .wallet-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #4f46e5, transparent);
    }

    .wallet-amount {
        font-size: 24px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 5px;
    }

    .wallet-label {
        color: var(--text-secondary);
        font-size: 14px;
    }

    /* Transfer Form */
    .transfer-card {
        background: var(--secondary-bg);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border-color);
    }

    .transfer-card-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        color: var(--text-secondary);
        margin-bottom: 10px;
        font-weight: 500;
    }

    .form-control {
        background: var(--primary-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        padding: 12px 15px;
        height: auto;
        transition: all 0.3s;
    }

    .form-control:focus {
        background: var(--hover-bg);
        border-color: #4f46e5;
        color: var(--text-primary);
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
        background: #4f46e5;
        border: none;
        border-radius: 8px;
        color: #ffffff;
        font-weight: 600;
        padding: 12px 25px;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-transfer:hover {
        background: #4338ca;
        color: #ffffffff;
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
        color: var(--success-color);
        display: block;
    }

    .user-verify.error {
        color: var(--danger-color);
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
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
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
    <div class="wallet-cards">
        <div class="wallet-card">
            <div class="wallet-amount">
                <?php echo round($user->wallet_topup * 1, 2); ?> <?php echo SITE_CURRENCY; ?>
            </div>
            <div class="wallet-label">
                <i class="fas fa-wallet"></i> Funding Wallet  <!-- Changed "Spot Wallet" to "Funding Wallet" -->
            </div>
        </div>
    </div>

    <!-- Transfer Form -->
    <div class="transfer-card">
        <div class="transfer-card-body">
            <form action="fund_transfer2_model.php" method="post" id="p2pTransferForm">
                <!-- P2P User ID Field -->
                <div class="form-group">
                    <label class="form-label" for="receiver_id">Receiver's User ID</label>
                    <div class="input-group">
                        <input class="form-control" type="text" id="receiver_id" name="receiver_id" maxlength="20" required placeholder="Enter receiver's user ID"
                            onBlur="check_sponser(this.value);">
                    </div>
                    <span id="sponser" name="sponser" class="form-hint"></span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="amount">Amount</label>
                    <input class="form-control" type="text" id="amount" name="amount" maxlength="20" required placeholder="Enter amount">
                </div>

                <div class="form-group">
                    <label class="form-label" for="type">Fund Type</label>
                    <select class="form-control" name="type" id="type" required>
                        <option value="1">Package Wallet</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="remark">Remark</label>
                    <textarea class="form-control" id="remark" name="remark" rows="3" required maxlength="250" placeholder="Enter your remark"></textarea>
                </div>

                <button type="submit" class="btn btn-transfer">
                    <i class="fas fa-exchange-alt"></i> Transfer Funds
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


    function check_sponser(refer_id) {
        if (!refer_id) {
            $("#sponser").html("Please enter a receiver's user ID").css("color", "blue");
            return;
        }

        $.get("../lib/get_availability.php", {
                action: 'sponsor',
                refer_id: refer_id
            }, function(data) {
                if (data.invalid) {
                    $("#sponser").html("Invalid Receiver's user ID.").css("color", "red");
                } else {
                    $("#sponser").html(`${data.name} - Valid Receiver's user ID.`).css("color", "green");

                }
            }, "json")
            .fail(function() {
                $("#sponser").html("Error validating Receiver's user ID.");
            });
    };
</script>

<?php include_once 'footer.php'; ?>