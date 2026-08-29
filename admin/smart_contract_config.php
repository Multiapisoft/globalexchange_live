<?php
/**
 * NexaBot Smart Contract Configuration
 * 
 * This file contains the configuration for the NexaBot smart contract
 * withdrawal system using MetaMask for admin approvals.
 */

// Smart Contract Configuration
define('NEXABOT_CONTRACT_ADDRESS', '0xd2a424d55cd2baefe9c607cf27ba01a9d138fcc5'); // Replace with your actual contract address
define('USDT_CONTRACT_ADDRESS', '0x55d398326f99059fF775485246999027B3197955'); // USDT contract on BSC
define('BSC_CHAIN_ID', 56); // BSC Mainnet
define('BSC_RPC_URL', 'https://bsc-dataseed.binance.org/');
define('BSC_EXPLORER_URL', 'https://bscscan.com/');

// Batch Configuration
define('MAX_BATCH_SIZE', 20); // Maximum addresses per batch
define('MIN_BATCH_SIZE', 1);  // Minimum addresses per batch

// Gas Configuration
define('DEFAULT_GAS_LIMIT', 500000); // Default gas limit for transactions
define('GAS_BUFFER_PERCENT', 20);    // Add 20% buffer to estimated gas

// Contract ABI for NexaBot transfers function
$NEXABOT_CONTRACT_ABI = [
    [
        "inputs" => [
            ["internalType" => "address[]", "name" => "_users", "type" => "address[]"],
            ["internalType" => "uint256[]", "name" => "_amount", "type" => "uint256[]"]
        ],
        "name" => "transfers",
        "outputs" => [],
        "stateMutability" => "nonpayable",
        "type" => "function"
    ],
    [
        "inputs" => [],
        "name" => "owner",
        "outputs" => [["internalType" => "address", "name" => "", "type" => "address"]],
        "stateMutability" => "view",
        "type" => "function"
    ],
    [
        "inputs" => [["internalType" => "address", "name" => "_user", "type" => "address"]],
        "name" => "getUserDetails",
        "outputs" => [
            ["internalType" => "uint256", "name" => "amountReceived", "type" => "uint256"],
            ["internalType" => "uint256", "name" => "lastClaimTime", "type" => "uint256"]
        ],
        "stateMutability" => "view",
        "type" => "function"
    ]
];

// USDT Contract ABI (minimal for balance checking)
$USDT_CONTRACT_ABI = [
    [
        "inputs" => [["internalType" => "address", "name" => "account", "type" => "address"]],
        "name" => "balanceOf",
        "outputs" => [["internalType" => "uint256", "name" => "", "type" => "uint256"]],
        "stateMutability" => "view",
        "type" => "function"
    ],
    [
        "inputs" => [],
        "name" => "decimals",
        "outputs" => [["internalType" => "uint8", "name" => "", "type" => "uint8"]],
        "stateMutability" => "view",
        "type" => "function"
    ]
];

// Status Constants
define('WITHDRAWAL_STATUS_PENDING', 0);
define('WITHDRAWAL_STATUS_SUCCESS', 1);
define('WITHDRAWAL_STATUS_FAILED', 2);
define('WITHDRAWAL_STATUS_PROCESSING', 3); // New status for smart contract processing

define('BATCH_STATUS_PENDING', 0);
define('BATCH_STATUS_SUCCESS', 1);
define('BATCH_STATUS_FAILED', 2);

// Helper Functions
function getContractABI() {
    global $NEXABOT_CONTRACT_ABI;
    return json_encode($NEXABOT_CONTRACT_ABI);
}

function getUSDTContractABI() {
    global $USDT_CONTRACT_ABI;
    return json_encode($USDT_CONTRACT_ABI);
}

function validateBatchSize($size) {
    return $size >= MIN_BATCH_SIZE && $size <= MAX_BATCH_SIZE;
}

function formatAddress($address) {
    if (strlen($address) !== 42 || substr($address, 0, 2) !== '0x') {
        return false;
    }
    return strtolower($address);
}

function isValidTxHash($hash) {
    return strlen($hash) === 66 && substr($hash, 0, 2) === '0x';
}

function getBSCExplorerLink($txHash) {
    return BSC_EXPLORER_URL . 'tx/' . $txHash;
}

function getAddressExplorerLink($address) {
    return BSC_EXPLORER_URL . 'address/' . $address;
}

// Database Schema for smart_contract_batches table
$BATCH_TABLE_SCHEMA = "
CREATE TABLE IF NOT EXISTS `smart_contract_batches` (
    `batch_id` int(11) NOT NULL AUTO_INCREMENT,
    `tx_hash` varchar(66) DEFAULT NULL,
    `admin_address` varchar(42) NOT NULL,
    `total_addresses` int(11) NOT NULL,
    `total_amount` decimal(20,8) NOT NULL,
    `status` tinyint(1) DEFAULT 0 COMMENT '0=Pending, 1=Success, 2=Failed',
    `gas_used` varchar(20) DEFAULT NULL,
    `gas_price` varchar(30) DEFAULT NULL,
    `block_number` varchar(20) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `confirmed_at` timestamp NULL DEFAULT NULL,
    `withdrawal_ids` text NOT NULL COMMENT 'JSON array of withdrawal recids',
    `addresses` text NOT NULL COMMENT 'JSON array of withdrawal addresses',
    `amounts` text NOT NULL COMMENT 'JSON array of withdrawal amounts',
    `error_message` text DEFAULT NULL,
    `retry_count` int(11) DEFAULT 0,
    PRIMARY KEY (`batch_id`),
    KEY `tx_hash` (`tx_hash`),
    KEY `status` (`status`),
    KEY `admin_address` (`admin_address`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Smart contract batch transactions for withdrawals';
";

// Network Configuration
$BSC_NETWORK_CONFIG = [
    'chainId' => '0x38', // 56 in hex
    'chainName' => 'Binance Smart Chain',
    'nativeCurrency' => [
        'name' => 'BNB',
        'symbol' => 'BNB',
        'decimals' => 18
    ],
    'rpcUrls' => [BSC_RPC_URL],
    'blockExplorerUrls' => [BSC_EXPLORER_URL]
];

function getBSCNetworkConfig() {
    global $BSC_NETWORK_CONFIG;
    return $BSC_NETWORK_CONFIG;
}

// Error Messages
$ERROR_MESSAGES = [
    'METAMASK_NOT_INSTALLED' => 'MetaMask is not installed. Please install MetaMask to continue.',
    'WRONG_NETWORK' => 'Please switch to Binance Smart Chain (BSC) network.',
    'NOT_CONTRACT_OWNER' => 'Connected wallet is not the contract owner.',
    'INSUFFICIENT_GAS' => 'Insufficient BNB for gas fees.',
    'BATCH_TOO_LARGE' => 'Maximum ' . MAX_BATCH_SIZE . ' addresses allowed per batch.',
    'BATCH_TOO_SMALL' => 'Minimum ' . MIN_BATCH_SIZE . ' address required per batch.',
    'INVALID_ADDRESS' => 'Invalid withdrawal address format.',
    'TRANSACTION_FAILED' => 'Smart contract transaction failed.',
    'NO_WITHDRAWALS_SELECTED' => 'Please select withdrawals to process.',
    'BATCH_NOT_FOUND' => 'Batch record not found.',
    'WITHDRAWAL_NOT_PENDING' => 'Withdrawal is not in pending status.'
];

function getErrorMessage($key) {
    global $ERROR_MESSAGES;
    return isset($ERROR_MESSAGES[$key]) ? $ERROR_MESSAGES[$key] : 'Unknown error occurred.';
}

// Success Messages
$SUCCESS_MESSAGES = [
    'BATCH_CREATED' => 'Batch created successfully.',
    'TRANSACTION_CONFIRMED' => 'Smart contract transaction confirmed successfully.',
    'WITHDRAWALS_CANCELLED' => 'Selected withdrawals cancelled successfully.',
    'METAMASK_CONNECTED' => 'MetaMask connected successfully.',
    'NETWORK_SWITCHED' => 'Successfully switched to BSC network.'
];

function getSuccessMessage($key) {
    global $SUCCESS_MESSAGES;
    return isset($SUCCESS_MESSAGES[$key]) ? $SUCCESS_MESSAGES[$key] : 'Operation completed successfully.';
}

// Logging Configuration
define('LOG_SMART_CONTRACT_TRANSACTIONS', true);
define('LOG_FILE_PATH', '../logs/smart_contract.log');

function logSmartContractActivity($message, $data = []) {
    if (!LOG_SMART_CONTRACT_TRANSACTIONS) return;
    
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'data' => $data,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    $logLine = json_encode($logEntry) . "\n";
    
    // Ensure log directory exists
    $logDir = dirname(LOG_FILE_PATH);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents(LOG_FILE_PATH, $logLine, FILE_APPEND | LOCK_EX);
}
?>
