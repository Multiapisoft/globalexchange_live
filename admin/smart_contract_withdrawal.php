<?php
$title = "Arbibotx Smart Contract Withdrawal System";
ini_set('display_errors', 0);
error_reporting(E_ALL);
include_once 'header.php';
include_once '../lib/config.php';
include_once 'smart_contract_config.php';
// Create batch transactions table if not exists

$check = my_query("SHOW COLUMNS FROM `withdrawal_block` LIKE 'widthdrawal_type'");

if (mysqli_num_rows($check) == 0) {
    $query = "ALTER TABLE `withdrawal_block` ADD `widthdrawal_type` ENUM('USDT', 'INR') NULL AFTER `type2`;";
    my_query($query);
}

$create_batch_table = "CREATE TABLE IF NOT EXISTS `smart_contract_batches` (
    `batch_id` int(11) NOT NULL AUTO_INCREMENT,
    `tx_hash` varchar(66) DEFAULT NULL,
    `admin_address` varchar(42) NOT NULL,
    `total_addresses` int(11) NOT NULL,
    `total_amount` decimal(20,8) NOT NULL,
    `status` tinyint(1) DEFAULT 0 COMMENT '0=Pending, 1=Success, 2=Failed',
    `gas_used` varchar(20) DEFAULT NULL,
    `block_number` varchar(20) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `confirmed_at` timestamp NULL DEFAULT NULL,
    `withdrawal_ids` text NOT NULL COMMENT 'JSON array of withdrawal recids',
    `error_message` text DEFAULT NULL,
    PRIMARY KEY (`batch_id`),
    KEY `tx_hash` (`tx_hash`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
my_query($create_batch_table);

$type = (isset($_GET['type']) && (int) $_GET['type'] <= 2) ? (int) $_GET['type'] : 0;
$status_arr = array('Pending', 'Success', 'Failed');

// Function to get total amounts by status
function getTotalAmountByStatus($status)
{
    $query = "SELECT SUM(net_amount) as total_amount FROM withdrawal_block WHERE status = '" . $status . "'";
    $result = my_query($query);
    $row = my_fetch_object($result);
    return $row->total_amount ? floatval($row->total_amount) : 0;
}

// Get batch statistics
function getBatchStats()
{
    $pending = my_fetch_object(my_query("SELECT COUNT(*) as count FROM smart_contract_batches WHERE status = 0"))->count;
    $success = my_fetch_object(my_query("SELECT COUNT(*) as count FROM smart_contract_batches WHERE status = 1"))->count;
    $failed = my_fetch_object(my_query("SELECT COUNT(*) as count FROM smart_contract_batches WHERE status = 2"))->count;
    return array('pending' => $pending, 'success' => $success, 'failed' => $failed);
}

// Get totals for each status
$total_pending = getTotalAmountByStatus(0);
$total_success = getTotalAmountByStatus(1);
$total_failed = getTotalAmountByStatus(2);
$batch_stats = getBatchStats();
?>
<!-- jQuery (Load only if not already loaded) -->
<script>
    if (typeof jQuery === 'undefined') {
        document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
    }
</script>
<style>
    .smart-contract-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .contract-info {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .batch-card {
        border-left: 4px solid #007bff;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .withdrawal-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-metamask {
        background: #f6851b;
        border-color: #f6851b;
        color: white;
    }

    .btn-metamask:hover {
        background: #e2761b;
        border-color: #e2761b;
        color: white;
    }

    /* .status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}
.status-pending { background: #fff3cd; color: #856404; }
.status-success { background: #d4edda; color: #155724; }
.status-failed { background: #f8d7da; color: #721c24; } */



    /* Status Badges */
    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    .status-success {
        background: #d4edda;
        color: #155724;
    }

    .status-failed {
        background: #f8d7da;
        color: #721c24;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-actions {
        display: flex;
        gap: 6px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .status-actions button {
        padding: 5px 10px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        transition: 0.2s ease;
    }

    .status-actions .btn-approve {
        background: #28a745;
        color: #fff;
    }

    .status-actions .btn-approve:hover {
        background: #218838;
    }

    .status-actions .btn-reject {
        background: #dc3545;
        color: #fff;
    }

    .status-actions .btn-reject:hover {
        background: #c82333;
    }

    /* Responsive table buttons */
    @media (max-width: 600px) {
        .status-actions {
            flex-direction: column;
            gap: 4px;
        }

        .status-actions button {
            width: 100%;
            font-size: 14px;
        }
    }
</style>

<!-- Smart Contract Header -->
<div class="smart-contract-header">
    <div class="row">
        <div class="col-md-8">
            <h2><i class="fa fa-cube"></i> Arbibotx Smart Contract Withdrawal System</h2>
            <p class="mb-0">Secure bulk withdrawals using MetaMask approval system</p>
        </div>
        <div class="col-md-4 text-right">
            <button type="button" class="btn btn-metamask btn-lg" id="connectWallet">
                <i class="fa fa-plug"></i> Connect MetaMask
            </button>
        </div>
    </div>
</div>

<!-- Contract Information -->
<div class="contract-info">
    <div class="row">
        <div class="col-md-6">
            <h5><i class="fa fa-info-circle"></i> Contract Information</h5>
            <p><strong>Contract Address:</strong> <br><span id="contractAddress" class="text-muted">Not Connected</span></p>
            <p><strong>Network:</strong> BSC Mainnet (Chain ID: 56)</p>
        </div>
        <div class="col-md-6">
            <h5><i class="fa fa-user"></i> Admin Wallet</h5>
            <p><strong>Connected Address:</strong> <br><span id="adminWallet" class="text-muted">Not Connected</span></p>
            <p><strong>Status:</strong> <span id="connectionStatus" class="status-badge status-pending">Disconnected</span></p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-3">
        <div class="panel panel-warning batch-card">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-clock-o fa-3x" style="color: #f0ad4e;"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 24px; font-weight: bold; color: #f0ad4e;">
                            <?php echo number_format($total_pending, 2); ?>
                        </div>
                        <div style="font-size: 14px; color: #666;">Pending Amount (USDT)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-success batch-card">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-check-circle fa-3x" style="color: #5cb85c;"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 24px; font-weight: bold; color: #5cb85c;">
                            <?php echo number_format($total_success, 2); ?>
                        </div>
                        <div style="font-size: 14px; color: #666;">Approved Amount (USDT)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-info batch-card">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-cubes fa-3x" style="color: #5bc0de;"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 24px; font-weight: bold; color: #5bc0de;">
                            <?php echo $batch_stats['success']; ?>
                        </div>
                        <div style="font-size: 14px; color: #666;">Successful Batches</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-danger batch-card">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-times-circle fa-3x" style="color: #d9534f;"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 24px; font-weight: bold; color: #d9534f;">
                            <?php echo $batch_stats['pending']; ?>
                        </div>
                        <div style="font-size: 14px; color: #666;">Pending Batches</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Withdrawal Management Panel -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd withdrawal-table">
            <div class="panel-body">
                <div class="form-group row">
                    <label for="type" class="col-sm-2 col-form-label">Filter Status:</label>
                    <div class="col-sm-4">
                        <select name="type" id="type" class="form-control" onchange="set_type(this.value);">
                            <?php foreach ($status_arr as $key => $value) { ?>
                                <option value="<?php echo $key; ?>" <?php if (isset($_GET['type']) && $_GET['type'] == $key) {
                                                                        echo "selected='selected'";
                                                                    } ?>><?php echo $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-6 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success" id="bulkApprove" disabled>
                                <i class="fa fa-check-circle"></i> Smart Contract Approve
                            </button>
                            <button type="button" class="btn btn-danger" id="bulkReject" disabled>
                                <i class="fa fa-times-circle"></i> Reject & Refund
                            </button>
                            <button type="button" class="btn btn-warning" id="viewBatches">
                                <i class="fa fa-list"></i> View Batches
                            </button>
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="badge badge-info" id="selectedCount">0 selected (Max 20)</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="withdrawalTable" class="table table-bordered table-striped table-hover">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th width="5%">#</th>
                                <th width="5%"><input type="checkbox" onclick="selectAll(this)" id="selectall" /></th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Fee</th>
                                <th>Net Amount</th>
                                <th>Wallet Address</th>
                                <th>Withdrawal Type</th>
                                <th>Status</th>
                                <th>Batch ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Smart Contract Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h4 class="modal-title" id="approvalModalLabel">
                    <i class="fa fa-cube"></i> Smart Contract Batch Approval
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Important:</strong> This will create a batch transaction on the BSC network.
                    Please ensure you have enough BNB for gas fees.
                </div>

                <div id="batchSummary">
                    <h5>Batch Summary</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Total Addresses:</strong> <span id="batchAddressCount">0</span></p>
                            <p><strong>Total Amount:</strong> <span id="batchTotalAmount">0</span> USDT</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Contract:</strong> <span id="modalContractAddress">-</span></p>
                            <p><strong>Admin Wallet:</strong> <span id="modalAdminWallet">-</span></p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Address</th>
                                <th>Amount (USDT)</th>
                            </tr>
                        </thead>
                        <tbody id="batchDetailsTable">
                        </tbody>
                    </table>
                </div>

                <div id="transactionProgress" style="display: none;">
                    <hr>
                    <h5>Transaction Progress</h5>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">
                            <span id="progressText">Preparing transaction...</span>
                        </div>
                    </div>
                    <div id="transactionDetails" style="margin-top: 10px;">
                        <p><strong>Transaction Hash:</strong> <span id="txHash">-</span></p>
                        <p><strong>Status:</strong> <span id="txStatus">Pending</span></p>
                        <p><strong>Gas Used:</strong> <span id="gasUsed">-</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApproval">
                    <i class="fa fa-check"></i> Approve with MetaMask
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #d9534f; color: white;">
                <h4 class="modal-title" id="rejectModalLabel">
                    <i class="fa fa-times-circle"></i> Reject Withdrawals & Refund
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This will reject the selected withdrawals and refund the amounts back to users' wallets.
                </div>

                <div id="rejectSummary">
                    <h5>Rejection Summary</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Total Withdrawals:</strong> <span id="rejectCount">0</span></p>
                            <p><strong>Total Refund Amount:</strong> <span id="rejectTotalAmount">0</span> USDT</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Action:</strong> Reject & Refund to User Wallets</p>
                            <p><strong>Status Update:</strong> Failed</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="rejectReason">Rejection Reason:</label>
                    <textarea class="form-control" id="rejectReason" rows="3" placeholder="Enter reason for rejection (optional)"></textarea>
                </div>

                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Address</th>
                                <th>Amount (USDT)</th>
                                <th>Refund To</th>
                            </tr>
                        </thead>
                        <tbody id="rejectDetailsTable">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">
                    <i class="fa fa-times"></i> Confirm Rejection & Refund
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Batch History Modal -->
<div class="modal fade" id="batchHistoryModal" tabindex="-1" role="dialog" aria-labelledby="batchHistoryModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="batchHistoryModalLabel">
                    <i class="fa fa-history"></i> Smart Contract Batch History
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="batchHistoryTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Batch ID</th>
                                <th>Date</th>
                                <th>Admin Address</th>
                                <th>Addresses</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Transaction Hash</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Web3 and Smart Contract Integration -->
<script src="https://cdn.jsdelivr.net/npm/web3@1.8.0/dist/web3.min.js"></script>
<script type="text/javascript">
    // Smart Contract Configuration
    const CONTRACT_ADDRESS = '0x3dAC42FF6357Ef3257Ea4f65b52538f2CE828b61'; // Replace with your Arbibotx contract address
    const USDT_CONTRACT = '0x55d398326f99059fF775485246999027B3197955'; // USDT contract address
    const CONTRACT_ABI = [{
            "inputs": [{
                    "internalType": "address[]",
                    "name": "_users",
                    "type": "address[]"
                },
                {
                    "internalType": "uint256[]",
                    "name": "_amount",
                    "type": "uint256[]"
                }
            ],
            "name": "transfers",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "owner",
            "outputs": [{
                "internalType": "address",
                "name": "",
                "type": "address"
            }],
            "stateMutability": "view",
            "type": "function"
        }
    ];

    let web3;
    let contract;
    let adminAccount;
    let selectedWithdrawals = [];

    $(document).ready(function() {
        // Initialize DataTable
        var withdrawalTable = $('#withdrawalTable').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                url: "smart_contract_withdrawal_list.php?type=<?php echo $type; ?>",
                type: "POST"
            },
            "columnDefs": [{
                "orderable": false,
                "targets": [1, 10]
            }]
        });

        // Update selected count when checkboxes change
        $(document).on('change', '.withdrawal-checkbox', updateSelectedCount);
        $(document).on('change', '#selectall', updateSelectedCount);

        // MetaMask connection
        $('#connectWallet').click(connectMetaMask);

        // Bulk approval
        $('#bulkApprove').click(function() {
            if (selectedWithdrawals.length === 0) {
                alert('Please select withdrawals to approve');
                return;
            }
            if (selectedWithdrawals.length > 20) {
                alert('Maximum 20 addresses allowed per batch');
                return;
            }
            showApprovalModal();
        });

        // Bulk reject
        $('#bulkReject').click(function() {
            if (selectedWithdrawals.length === 0) {
                alert('Please select withdrawals to reject');
                return;
            }
            showRejectModal();
        });

        // View batches
        $('#viewBatches').click(function() {
            loadBatchHistory();
            $('#batchHistoryModal').modal('show');
        });

        // Confirm approval
        $('#confirmApproval').click(executeSmartContractApproval);

        // Confirm reject
        $('#confirmReject').click(executeRejectWithdrawals);
    });

    async function connectMetaMask() {
        try {
            if (typeof window.ethereum !== 'undefined') {
                web3 = new Web3(window.ethereum);

                // Request account access
                const accounts = await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });
                adminAccount = accounts[0];

                // Check if we're on BSC network
                const chainId = await web3.eth.getChainId();
                if (chainId !== 56) { // BSC Mainnet
                    await switchToBSC();
                }

                // Initialize contract
                contract = new web3.eth.Contract(CONTRACT_ABI, CONTRACT_ADDRESS);

                // Verify admin is contract owner
                try {
                    const owner = await contract.methods.owner().call();
                    if (owner.toLowerCase() !== adminAccount.toLowerCase()) {
                        alert('Warning: Connected wallet is not the contract owner!');
                    }
                } catch (e) {
                    console.log('Could not verify contract owner');
                }

                // Update UI
                $('#adminWallet').text(adminAccount);
                $('#contractAddress').text(CONTRACT_ADDRESS);
                $('#connectionStatus').removeClass('status-pending').addClass('status-success').text('Connected');
                $('#connectWallet').text('Connected').removeClass('btn-metamask').addClass('btn-success');

                updateSelectedCount();

            } else {
                alert('MetaMask is not installed. Please install MetaMask to continue.');
            }
        } catch (error) {
            console.error('Error connecting to MetaMask:', error);
            alert('Error connecting to MetaMask: ' + error.message);
        }
    }

    async function switchToBSC() {
        try {
            await window.ethereum.request({
                method: 'wallet_switchEthereumChain',
                params: [{
                    chainId: '0x38'
                }], // BSC Mainnet
            });
        } catch (switchError) {
            if (switchError.code === 4902) {
                try {
                    await window.ethereum.request({
                        method: 'wallet_addEthereumChain',
                        params: [{
                            chainId: '0x38',
                            chainName: 'Binance Smart Chain',
                            nativeCurrency: {
                                name: 'BNB',
                                symbol: 'BNB',
                                decimals: 18,
                            },
                            rpcUrls: ['https://bsc-dataseed.binance.org/'],
                            blockExplorerUrls: ['https://bscscan.com/'],
                        }],
                    });
                } catch (addError) {
                    throw addError;
                }
            } else {
                throw switchError;
            }
        }
    }

    function updateSelectedCount() {
        selectedWithdrawals = [];
        $('.withdrawal-checkbox:checked').each(function() {
            selectedWithdrawals.push({
                recid: $(this).val(),
                address: $(this).data('address'),
                amount: $(this).data('amount'),
                user: $(this).data('user')
            });
        });

        const count = selectedWithdrawals.length;
        $('#selectedCount').text(count + ' selected (Max 20)');

        // Enable/disable bulk action buttons
        const hasSelection = count > 0;
        const validSelection = count <= 20;
        const isConnected = adminAccount !== undefined;

        $('#bulkApprove').prop('disabled', !hasSelection || !validSelection || !isConnected);
        $('#bulkReject').prop('disabled', !hasSelection); // Reject doesn't need MetaMask

        if (count > 20) {
            $('#selectedCount').removeClass('badge-info').addClass('badge-danger');
        } else {
            $('#selectedCount').removeClass('badge-danger').addClass('badge-info');
        }
    }

    function set_type(type) {
        window.location = "smart_contract_withdrawal.php?type=" + type;
    }

    function selectAll(source) {
        var checkboxes = document.querySelectorAll('.withdrawal-checkbox');
        for (i = 0; i < checkboxes.length; i++)
            checkboxes[i].checked = source.checked;
        updateSelectedCount();
    }

    function showApprovalModal() {
        if (selectedWithdrawals.length === 0) return;

        // Populate modal data
        $('#batchAddressCount').text(selectedWithdrawals.length);
        $('#modalContractAddress').text(CONTRACT_ADDRESS);
        $('#modalAdminWallet').text(adminAccount);

        let totalAmount = 0;
        let tableHtml = '';

        selectedWithdrawals.forEach(function(withdrawal) {
            totalAmount += parseFloat(withdrawal.amount);
            tableHtml += `
            <tr>
                <td>${withdrawal.user}</td>
                <td style="font-family: monospace; font-size: 12px;">${withdrawal.address}</td>
                <td>${parseFloat(withdrawal.amount).toFixed(2)}</td>
            </tr>
        `;
        });

        $('#batchTotalAmount').text(totalAmount.toFixed(2));
        $('#batchDetailsTable').html(tableHtml);

        // Reset progress
        $('#transactionProgress').hide();
        $('#confirmApproval').prop('disabled', false);

        $('#approvalModal').modal('show');
    }

    async function executeSmartContractApproval() {
        try {
            $('#confirmApproval').prop('disabled', true);
            $('#transactionProgress').show();
            updateProgress(10, 'Preparing transaction data...');

            // Prepare transaction data
            const addresses = selectedWithdrawals.map(w => w.address);
            const amounts = selectedWithdrawals.map(w => web3.utils.toWei(w.amount.toString(), 'ether'));
            const withdrawalIds = selectedWithdrawals.map(w => w.recid);

            updateProgress(30, 'Creating batch record...');

            // Create batch record in database
            const batchResponse = await $.post('smart_contract_processor.php', {
                action: 'create_batch',
                withdrawal_ids: withdrawalIds,
                admin_address: adminAccount,
                tx_hash: ''
            });

            if (!batchResponse.success) {
                throw new Error(batchResponse.message);
            }

            const batchId = batchResponse.batch_id;
            updateProgress(50, 'Sending transaction to blockchain...');

            // Estimate gas
            const gasEstimate = await contract.methods.transfers(addresses, amounts).estimateGas({
                from: adminAccount
            });

            updateProgress(70, 'Waiting for MetaMask confirmation...');

            // Send transaction
            const transaction = await contract.methods.transfers(addresses, amounts).send({
                from: adminAccount,
                gas: Math.floor(gasEstimate * 1.2) // Add 20% buffer
            });

            updateProgress(90, 'Transaction confirmed! Updating records...');
            $('#txHash').text(transaction.transactionHash);
            $('#txStatus').text('Confirmed');
            $('#gasUsed').text(transaction.gasUsed);

            // Update batch status
            await $.post('smart_contract_processor.php', {
                action: 'update_batch_status',
                batch_id: batchId,
                tx_hash: transaction.transactionHash,
                status: 1,
                gas_used: transaction.gasUsed,
                block_number: transaction.blockNumber
            });

            updateProgress(100, 'Batch approval completed successfully!');

            // Refresh table
            $('#withdrawalTable').DataTable().ajax.reload();

            setTimeout(function() {
                $('#approvalModal').modal('hide');
                alert('Batch approval completed successfully!\nTransaction Hash: ' + transaction.transactionHash);
            }, 2000);

        } catch (error) {
            console.error('Smart contract approval failed:', error);

            // Update batch status as failed if batch was created
            if (typeof batchId !== 'undefined') {
                await $.post('smart_contract_processor.php', {
                    action: 'update_batch_status',
                    batch_id: batchId,
                    status: 2,
                    error_message: error.message
                });
            }

            $('#txStatus').text('Failed');
            updateProgress(0, 'Transaction failed: ' + error.message);
            $('#confirmApproval').prop('disabled', false);

            alert('Transaction failed: ' + error.message);
        }
    }

    function updateProgress(percent, text) {
        $('.progress-bar').css('width', percent + '%');
        $('#progressText').text(text);
    }

    async function loadBatchHistory() {
        try {
            const response = await $.post('smart_contract_processor.php', {
                action: 'get_batch_history',
                limit: 50,
                offset: 0
            });

            if (response.success) {
                let tableHtml = '';
                response.batches.forEach(function(batch) {
                    const txLink = batch.tx_hash ?
                        `<a href="https://bscscan.com/tx/${batch.tx_hash}" target="_blank" style="font-family: monospace; font-size: 12px;">${batch.tx_hash.substring(0, 10)}...</a>` :
                        '-';

                    tableHtml += `
                    <tr>
                        <td>#${batch.batch_id}</td>
                        <td>${batch.created_at}</td>
                        <td style="font-family: monospace; font-size: 12px;">${batch.admin_address.substring(0, 10)}...</td>
                        <td>${batch.total_addresses}</td>
                        <td>${batch.total_amount} USDT</td>
                        <td><span class="status-badge ${batch.status_class}">${batch.status}</span></td>
                        <td>${txLink}</td>
                        <td>
                            <button class="btn btn-xs btn-info" onclick="viewBatchDetails(${batch.batch_id})">
                                <i class="fa fa-eye"></i> Details
                            </button>
                        </td>
                    </tr>
                `;
                });

                $('#batchHistoryTable tbody').html(tableHtml);
            }
        } catch (error) {
            console.error('Error loading batch history:', error);
        }
    }

    async function viewBatchDetails(batchId) {
        try {
            const response = await $.post('smart_contract_processor.php', {
                action: 'get_batch_details',
                batch_id: batchId
            });

            if (response.success) {
                const batch = response.batch;
                alert(`Batch #${batch.batch_id} Details:\n\nTotal Addresses: ${batch.total_addresses}\nTotal Amount: ${batch.total_amount} USDT\nTransaction Hash: ${batch.tx_hash || 'N/A'}\nCreated: ${batch.created_at}`);
            }
        } catch (error) {
            console.error('Error loading batch details:', error);
        }
    }





    // update status 


    function updateStatus(recid, newStatus) {
        if (confirm("Are you sure you want to change status?")) {
            fetch("smart_contract_withdrawal_manuwal_status_update.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "recid=" + recid + "&status=" + newStatus
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Status updated successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                });
        }
    }
</script>



<!-- 
<script>
    function updateStatus(recid, newStatus) {
        if (confirm("Are you sure you want to change status?")) {
            fetch("update_status.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "recid=" + recid + "&status=" + newStatus
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Status updated successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                });
        }
    }
</script> -->




<?php include_once 'footer.php'; ?>