<?php
session_start();
require 'vendor/autoload.php';
use Web3p\EvmTool\Utils;
use GuzzleHttp\Client;
use Elliptic\EC;
use kornrunner\Keccak;
use Web3p\EthereumTx\Transaction;
use Web3p\EthereumTx\EIP1559Transaction;


$config_loaded = false;
foreach ($config_paths as $path) {
    if (file_exists($path)) {
        include_once $path;
        $config_loaded = true;
        SecureLogger::info("Config loaded from: " . $path);
        break;
    }
}

if (!$config_loaded) {
    SecureLogger::warning("secure_config.php not found in any expected location");
}

include_once '../connection.php';
include_once '../function_lib.php';
user();
$uid = $_SESSION['userid'];
$user = get_user_details($uid);
class WalletGenerator {
    private $provider;

    public function __construct() {
        $this->provider = new Client(['base_uri' => 'https://bsc-dataseed.binance.org']);
    }

    public function generateWallet() {
        $ec = new EC('secp256k1');
        $keyPair = $ec->genKeyPair();

        $privateKey = $keyPair->getPrivate('hex');
        $publicKey = $keyPair->getPublic(false, 'hex');

        $publicKey = substr($publicKey, 2);
        $address = '0x' . substr(Keccak::hash(hex2bin($publicKey), 256), 24);

        return [
            'address' => $address,
            'privateKey' => '0x' . $privateKey,
        ];
    }
}
class SecureLogger {
    private static function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}";
        if (!empty($context)) {
            $logEntry .= " Context: " . json_encode($context);
        }
        error_log($logEntry);
    }

    public static function info($message, $context = []) {
        self::log('INFO', $message, $context);
    }

    public static function warning($message, $context = []) {
        self::log('WARNING', $message, $context);
    }

    public static function error($message, $context = []) {
        self::log('ERROR', $message, $context);
    }
}
class WalletMonitor {
    private $provider;
    private $usdtReceiveWallet; // Wallet to receive USDT (no private key needed)
    private $gasWallet; // Wallet for sending gas
    private $gasPrivateKey; // Private key for gas operations
    private $usdtContract;
    
    // USDT BEP20 Contract ABI (only necessary functions)
    private $usdtAbi = '[{"constant":true,"inputs":[{"name":"_owner","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transfer","outputs":[{"name":"","type":"bool"}],"type":"function"}]';

    public function __construct($usdtReceiveWallet, $gasWallet, $gasPrivateKey) {
        try {
            $this->provider = new Client(['base_uri' => 'https://bsc-dataseed.binance.org']);
            $this->usdtReceiveWallet = $usdtReceiveWallet;
            $this->gasWallet = $gasWallet;
            $this->gasPrivateKey = $gasPrivateKey;
            $this->usdtContract = '0x55d398326f99059fF775485246999027B3197955';
            
            // Validate addresses
            if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $gasWallet)) {
                throw new Exception("Invalid gas wallet address format");
            }
            if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $usdtReceiveWallet)) {
                throw new Exception("Invalid USDT receive wallet address format");
            }
        } catch (Exception $e) {
            echo "Error in constructor: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function getBNBBalance($address) {
        try {
            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_getBalance',
                    'params' => [$address, 'latest'],
                    'id' => 1
                ],
                'verify' => false
            ]);

            $body = json_decode($response->getBody(), true);
            
            // Debug output
            // echo "API Response for address {$address}: " . print_r($body, true) . "\n";
            
            if (!isset($body['result'])) {
                // If result is missing, return 0 instead of throwing error
                echo "Warning: Invalid response from node for address {$address}\n";
                return 0;
            }

            return hexdec($body['result']) / (10 ** 18); // Convert Wei to BNB
        } catch (Exception $e) {
            echo "Error fetching BNB balance: " . $e->getMessage() . "\n";
            return 0;
        }
    }

    public function getUSDTBalance($address) {
        try {
            // Create function signature for balanceOf(address)
            $methodID = substr(Keccak::hash('balanceOf(address)', 256), 0, 8);
            $params = str_pad(substr($address, 2), 64, '0', STR_PAD_LEFT);
            $data = '0x' . $methodID . $params;

            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_call',
                    'params' => [[
                        'to' => $this->usdtContract,
                        'data' => $data
                    ], 'latest'],
                    'id' => 1
                ],
                'verify' => false
            ]);

            // Use big integer arithmetic to avoid precision loss
            $body = json_decode($response->getBody(), true);
            $hex = isset($body['result']) ? $body['result'] : '0x0';
            if (strpos($hex, '0x') === 0) {
                $hex = substr($hex, 2);
            }
            if ($hex === '' || $hex === null) {
                $hex = '0';
            }
            $wei = gmp_strval(gmp_init($hex, 16), 10);
            return (float) bcdiv($wei, bcpow('10', '18'), 18); // Convert wei to USDT using 18 decimals
        } catch (Exception $e) {
            echo "Error fetching USDT balance: " . $e->getMessage() . "\n";
            return 0;
        }
    }

    private function getUSDTBalanceWei($address) {
        try {
            $methodID = substr(Keccak::hash('balanceOf(address)', 256), 0, 8);
            $params = str_pad(substr($address, 2), 64, '0', STR_PAD_LEFT);
            $data = '0x' . $methodID . $params;

            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_call',
                    'params' => [[
                        'to' => $this->usdtContract,
                        'data' => $data
                    ], 'latest'],
                    'id' => 1
                ],
                'verify' => false
            ]);

            $body = json_decode($response->getBody(), true);
            $hex = isset($body['result']) ? $body['result'] : '0x0';
            if (strpos($hex, '0x') === 0) {
                $hex = substr($hex, 2);
            }
            if ($hex === '' || $hex === null) {
                $hex = '0';
            }
            return gmp_strval(gmp_init($hex, 16), 10); // return decimal string in wei
        } catch (Exception $e) {
            echo "Error fetching USDT balance (wei): " . $e->getMessage() . "\n";
            return '0';
        }
    }


    public function monitorAndTransfer($wallet) {
        try {
            echo "Checking wallet: " . $wallet['address'] . "\n";
            
            // Get initial balances
            $bnbBalance = $this->getBNBBalance($wallet['address']);
            $usdtBalance = $this->getUSDTBalance($wallet['address']);
            
            echo "BNB Balance: " . $bnbBalance . " BNB\n";
            echo "USDT Balance: " . $usdtBalance . " USDT\n";

            // Define minimum thresholds
            $MIN_USDT_THRESHOLD = 0.00001; // Minimum USDT amount worth processing
            $MIN_BNB_REQUIRED = 0.005;     // Minimum BNB needed for gas

            // First check if USDT balance is worth processing
            if ($usdtBalance < $MIN_USDT_THRESHOLD) {
                echo "USDT balance too small to process (< {$MIN_USDT_THRESHOLD})\n";
                return [
                    'found' => false,
                    'message' => 'No significant USDT balance found'
                ];
            }

            // If USDT balance is significant, ensure we have enough BNB
            if ($bnbBalance < $MIN_BNB_REQUIRED) {
                echo "Insufficient BNB for gas. Attempting to send from main wallet...\n";
                
                // Try to send BNB from gas wallet
                if (!$this->sendGasFromMainWallet($wallet['address'])) {
                    echo "Failed to send BNB for gas\n";
                    return [
                        'found' => false,
                        'message' => 'Failed to send BNB for gas'
                    ];
                }

                // Wait for BNB transfer to confirm
                sleep(15);
                
                // Verify BNB was received
                $bnbBalance = $this->getBNBBalance($wallet['address']);
                if ($bnbBalance < $MIN_BNB_REQUIRED) {
                    echo "BNB transfer failed to arrive\n";
                    return [
                        'found' => false,
                        'message' => 'BNB transfer failed to arrive'
                    ];
                }
            }

            // Now proceed with USDT transfer
            echo "Proceeding with USDT transfer...\n";
            $success = $this->transferUSDT($wallet['address'], $wallet['privateKey'], $usdtBalance);
            
            if (!$success) {
                echo "USDT transfer failed\n";
                // Return remaining BNB to gas wallet
                $finalBnbBalance = $this->getBNBBalance($wallet['address']);
                if ($finalBnbBalance > 0.001) {
                    $this->transferBNB($wallet['address'], $wallet['privateKey'], $finalBnbBalance);
                    echo "Returned remaining BNB to gas wallet after failed USDT transfer\n";
                }
                return [
                    'found' => false,
                    'message' => 'USDT transfer failed'
                ];
            }

            // If USDT transfer succeeded, wait and verify
            sleep(15);
            $finalUsdtBalance = $this->getUSDTBalance($wallet['address']);
            
            if ($finalUsdtBalance < $MIN_USDT_THRESHOLD) {
                // USDT transfer was successful, now return remaining BNB
                echo "USDT transfer successful, returning remaining BNB...\n";
                $finalBnbBalance = $this->getBNBBalance($wallet['address']);
                if ($finalBnbBalance > 0.001) {
                    $this->transferBNB($wallet['address'], $wallet['privateKey'], $finalBnbBalance);
                    echo "Returned remaining BNB to gas wallet\n";
                }
                
                return [
                    'found' => true,
                    'amount' => $usdtBalance,
                    'currency' => 'USDT',
                    'message' => 'Transfer completed successfully'
                ];
            } else {
                echo "USDT transfer verification failed\n";
                return [
                    'found' => false,
                    'message' => 'USDT transfer verification failed'
                ];
            }

        } catch (Exception $e) {
            echo "Error in monitoring: " . $e->getMessage() . "\n";
            return [
                'found' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    private function approveUSDT($fromAddress, $privateKey, $amount) {
        try {
            echo "Approving USDT transfer...\n";
            
            $privateKey = str_replace('0x', '', $privateKey);
            
            // Create approve function data
            $methodID = substr(Keccak::hash('approve(address,uint256)', 256), 0, 8);
            $spender = str_pad(substr($this->usdtReceiveWallet, 2), 64, '0', STR_PAD_LEFT);
            $amountInWei = bcmul(sprintf('%.8f', $amount), bcpow('10', '18'));
            $amountHex = str_pad(dechex($amountInWei), 64, '0', STR_PAD_LEFT);
            $data = '0x' . $methodID . $spender . $amountHex;

            $nonce = $this->getTransactionCount($fromAddress);
            
            $txParams = [
                'nonce' => '0x' . dechex($nonce),
                'to' => $this->usdtContract,
                'value' => '0x0',
                'data' => $data,
                'gas' => '0x186A0', // 100000 gas limit
                'gasPrice' => '0x' . dechex(5 * 10 ** 9), // 5 Gwei
                'chainId' => 56
            ];

            $transaction = new Transaction($txParams);
            $signedTx = '0x' . $transaction->sign($privateKey);
            $txHash = $this->sendRawTransaction($signedTx);
            
            // Wait for approval confirmation
            for ($i = 0; $i < 30; $i++) {
                $status = $this->getTransactionStatus($txHash);
                if ($status === true) {
                    echo "Approval confirmed\n";
                    sleep(5); // Wait a bit for blockchain to update
                    return true;
                } else if ($status === false) {
                    echo "Approval transaction failed\n";
                    return false;
                }
                sleep(5);
            }
            
            echo "Approval transaction timeout\n";
            return false;
        } catch (Exception $e) {
            echo "Error in approveUSDT: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function transferUSDT($fromAddress, $privateKey, $amount) {
        try {
            // Compute exact token balance in wei to avoid rounding above balance
            $amountWei = $this->getUSDTBalanceWei($fromAddress);
            if ($amountWei === '0') {
                echo "Invalid user or amount\n";
                return false;
            }
            // Derive a 6-decimal display amount, truncated (never rounded up)
            $amountDecimal = bcdiv($amountWei, bcpow('10', '18'), 6);

            // Get user info first - using safe escaping
            $escapedAddress = addslashes($fromAddress);
            $result = my_query("SELECT uid FROM user WHERE pay_address='" . $escapedAddress . "' LIMIT 1");
            $row = mysqli_fetch_assoc($result);

            if (!$row) {
                echo "Invalid user or amount\n";
                return false;
            }

            $uid = $row['uid'];
            $fee = '0';
            $net_amount = bcsub($amountDecimal, $fee, 6);

            // Step 1: Create pending transaction record (status = 0 for pending)
            $pendingTxId = 'pending_' . uniqid() . '_' . time();
            $escapedUid = addslashes($uid);
            $escapedAmount = addslashes($amountDecimal);
            $escapedFee = addslashes($fee);
            $escapedNetAmount = addslashes($net_amount);
            $escapedPendingTxId = addslashes($pendingTxId);

            my_query("INSERT INTO deposit_block (uid, datetime, status, amount, fee, net_amount, amount_coin, txid, data, type)
                      VALUES ('" . $escapedUid . "', '" . date('c') . "', 0, '" . $escapedAmount . "', '" . $escapedFee . "', '" . $escapedNetAmount . "', '" . $escapedAmount . "', '" . $escapedPendingTxId . "', 'PENDING', 'USDT.BEP20')");

            echo "Created pending transaction record with ID: $pendingTxId\n";

            // Step 2: Prepare and send blockchain transaction
            $methodID = substr(Keccak::hash('transfer(address,uint256)', 256), 0, 8);
            $address = str_pad(substr($this->usdtReceiveWallet, 2), 64, '0', STR_PAD_LEFT);
            $amountHex = str_pad(gmp_strval(gmp_init($amountWei, 10), 16), 64, '0', STR_PAD_LEFT);

            $txParams = [
                'nonce' => '0x' . dechex($this->getTransactionCount($fromAddress)),
                'to' => $this->usdtContract,
                'value' => '0x0',
                'data' => '0x' . $methodID . $address . $amountHex,
                'gas' => '0x186A0', // 100000 gas
                'gasPrice' => '0x' . dechex(3 * 10 ** 9), // 3 Gwei
                'chainId' => 56
            ];

            $transaction = new Transaction($txParams);
            $signedTx = '0x' . $transaction->sign(str_replace('0x', '', $privateKey));

            // Step 3: Send transaction to blockchain
            $txHash = $this->sendSecureRawTransaction($signedTx);
            if (!$txHash) {
                // Transaction failed to send - remove pending record
                $escapedPendingTxId = addslashes($pendingTxId);
                my_query("DELETE FROM deposit_block WHERE txid = '" . $escapedPendingTxId . "'");
                echo "Failed to send transaction to blockchain\n";
                return false;
            }

            echo "Transaction sent to blockchain with hash: $txHash\n";

            // Step 4: Update pending record with real transaction hash
            $escapedTxHash = addslashes($txHash);
            $escapedPendingTxId = addslashes($pendingTxId);
            my_query("UPDATE deposit_block SET txid = '" . $escapedTxHash . "', data = 'SENT_TO_BLOCKCHAIN' WHERE txid = '" . $escapedPendingTxId . "'");

            // Step 5: Wait for transaction confirmation (CRITICAL SECURITY STEP)
            $confirmed = $this->waitForTransactionConfirmation($txHash, 60); // Wait up to 60 attempts (5 minutes)

            if ($confirmed) {
                // Step 6: ONLY NOW update database with success and credit user
                $escapedTxHash = addslashes($txHash);
                $escapedNetAmount = addslashes($net_amount);
                $escapedFromAddress = addslashes($fromAddress);

                my_query("UPDATE deposit_block SET status = 1, data = 'CONFIRMED', datetime = '" . date('c') . "' WHERE txid = '" . $escapedTxHash . "'");
                my_query("UPDATE user SET got = 1, wallet_topup = wallet_topup + '" . $escapedNetAmount . "' WHERE pay_address = '" . $escapedFromAddress . "'");

                echo "Transaction confirmed! User credited with $net_amount USDT\n";
                return true;
            } else {
                // Step 7: Transaction failed - mark as failed, DO NOT credit user
                $escapedTxHash = addslashes($txHash);
                my_query("UPDATE deposit_block SET status = -1, data = 'FAILED_OR_TIMEOUT' WHERE txid = '" . $escapedTxHash . "'");
                echo "Transaction failed or timed out - user NOT credited\n";
                return false;
            }

        } catch (Exception $e) {
            echo "Error in transferUSDT: " . $e->getMessage() . "\n";
            // Clean up any pending records on exception
            if (isset($pendingTxId)) {
                $escapedPendingTxId = addslashes($pendingTxId);
                my_query("DELETE FROM deposit_block WHERE txid = '" . $escapedPendingTxId . "'");
            }
            return false;
        }
    }

    private function transferBNB($fromAddress, $privateKey, $amount) {
        try {
            // Convert amount to Wei for precise calculations
            $amountInWei = $amount * (10 ** 18);
            
            // Gas limit in Wei (21000 gas for simple transfer)
            $gasLimit = 21000;
            
            // Gas price in Wei (3 Gwei)
            $gasPriceInWei = 3 * (10 ** 9);
            
            // Calculate total gas cost in Wei
            $gasCostInWei = $gasLimit * $gasPriceInWei;
            
            // Check if we have enough for gas + transfer
            if ($amountInWei <= $gasCostInWei) {
                echo "Amount too small to transfer after gas costs\n";
                return false;
            }

            // Calculate amount to send (total - gas cost)
            $sendAmountInWei = $amountInWei - $gasCostInWei;
            $sendAmount = $sendAmountInWei / (10 ** 18);

            echo "Transferring " . $sendAmount . " BNB to gas wallet: " . $this->gasWallet . "\n";
            echo "Gas cost: " . ($gasCostInWei / (10 ** 18)) . " BNB\n";

            // Remove '0x' if present from private key
            $privateKey = str_replace('0x', '', $privateKey);

            $nonce = $this->getTransactionCount($fromAddress);
            
            $txParams = [
                'nonce' => '0x' . dechex($nonce),
                'to' => $this->gasWallet,
                'value' => '0x' . dechex($sendAmountInWei),
                'gas' => '0x' . dechex($gasLimit),
                'gasPrice' => '0x' . dechex($gasPriceInWei),
                'chainId' => 56
            ];

            $transaction = new Transaction($txParams);
            $signedTx = '0x' . $transaction->sign($privateKey);
            $txHash = $this->sendRawTransaction($signedTx);
            
            echo "Transaction sent successfully! TxHash: " . $txHash . "\n";

            // Wait for confirmation
            $maxAttempts = 30;
            for ($i = 0; $i < $maxAttempts; $i++) {
                $receipt = $this->getDetailedTransactionReceipt($txHash);
                
                if ($receipt === null) {
                    echo "Waiting for transaction confirmation... Attempt " . ($i + 1) . "/" . $maxAttempts . "\n";
                    sleep(2);
                    continue;
                }
                
                if ($receipt['status'] === '0x1') {
                    echo "BNB transfer confirmed\n";
                    return true;
                } else {
                    echo "BNB transfer failed\n";
                    return false;
                }
            }
            
            echo "Transaction confirmation timeout\n";
            return false;
        } catch (Exception $e) {
            echo "Error in transferBNB: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function getTransactionCount($address) {
        $response = $this->provider->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'eth_getTransactionCount',
                'params' => [$address, 'latest'],
                'id' => 1
            ],
            'verify' => false
        ]);
        $body = json_decode($response->getBody(), true);
        return hexdec($body['result']);
    }

    private function sendRawTransaction($signedTx) {
        $response = $this->provider->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'eth_sendRawTransaction',
                'params' => [$signedTx],
                'id' => 1
            ],
            'verify' => false
        ]);

        $body = json_decode($response->getBody(), true);

        if (isset($body['error'])) {
            throw new Exception("Error sending transaction: " . $body['error']['message']);
        }
        
        echo "Transaction sent successfully! TxHash: " . $body['result'] . "\n";
        return $body['result'];
    }
    private function sendSecureRawTransaction($signedTx) {
        try {
            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_sendRawTransaction',
                    'params' => [$signedTx],
                    'id' => 1
                ],
                'verify' => false
            ]);

            $body = json_decode($response->getBody(), true);

            if (isset($body['error'])) {
                echo "Error sending transaction: " . $body['error']['message'] . "\n";
                return false;
            }

            echo "Transaction sent successfully! TxHash: " . $body['result'] . "\n";
            return $body['result'];
        } catch (Exception $e) {
            echo "Exception sending transaction: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function waitForTransactionConfirmation($txHash, $maxAttempts = 60) {
        echo "Waiting for transaction confirmation: $txHash\n";

        for ($i = 0; $i < $maxAttempts; $i++) {
            try {
                $status = $this->getTransactionStatus($txHash);

                if ($status === true) {
                    // Double-check with detailed status to ensure USDT transfer occurred
                    $detailedStatus = $this->getDetailedTransactionStatus($txHash);
                    if ($detailedStatus === true) {
                        echo "Transaction confirmed successfully after " . ($i + 1) . " attempts\n";
                        return true;
                    }
                } else if ($status === false) {
                    echo "Transaction failed on blockchain\n";
                    return false;
                }

                echo "Waiting for confirmation... Attempt " . ($i + 1) . "/" . $maxAttempts . "\n";
                sleep(5); // Wait 5 seconds between checks

            } catch (Exception $e) {
                echo "Error checking transaction status: " . $e->getMessage() . "\n";
                sleep(5);
            }
        }

        echo "Transaction confirmation timeout after " . ($maxAttempts * 5) . " seconds\n";
        return false;
    }

    private function sendRawTransaction2($signedTx, $row) {
        $response = $this->provider->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'eth_sendRawTransaction',
                'params' => [$signedTx],
                'id' => 1
            ],
            'verify' => false
        ]);

        $body = json_decode($response->getBody(), true);

        if (isset($body['error'])) {
            echo "Error sending transaction: " . $body['error']['message'] . "\n";
        } else {
            echo "Transaction sent successfully! TxHash: " . $body['result'] . "\n";
            my_query("UPDATE deposit_block SET txid = '" . $body['result'] . "' WHERE uid = '" . $row['uid'] . "' ORDER BY recid DESC LIMIT 1");

        }
    }
    private function transferBNBFromGasWallet($toAddress, $amount) {
        try {
            echo "Sending {$amount} BNB from gas wallet to {$toAddress}\n";
            
            // Convert amount to Wei for precise calculations
            $amountInWei = $amount * (10 ** 18);
            
            // Gas limit in Wei (21000 gas for simple transfer)
            $gasLimit = 21000;
            
            // Gas price in Wei (3 Gwei)
            $gasPriceInWei = 3 * (10 ** 9);
            
            // Calculate total gas cost in Wei
            $gasCostInWei = $gasLimit * $gasPriceInWei;
            
            // Total amount needed including gas
            $totalAmountNeeded = $amountInWei + $gasCostInWei;
            
            // Check if gas wallet has enough balance
            $gasWalletBalance = $this->getBNBBalance($this->gasWallet);
            $gasWalletBalanceWei = $gasWalletBalance * (10 ** 18);
            
            if ($gasWalletBalanceWei < $totalAmountNeeded) {
                echo "Insufficient balance in gas wallet. Required: " . ($totalAmountNeeded / (10 ** 18)) . " BNB, Available: " . $gasWalletBalance . " BNB\n";
                return false;
            }

            // Remove '0x' if present from private key
            $privateKey = str_replace('0x', '', $this->gasPrivateKey);

            $nonce = $this->getTransactionCount($this->gasWallet);
            
            $txParams = [
                'nonce' => '0x' . dechex($nonce),
                'to' => $toAddress,
                'value' => '0x' . dechex($amountInWei),
                'gas' => '0x' . dechex($gasLimit),
                'gasPrice' => '0x' . dechex($gasPriceInWei),
                'chainId' => 56
            ];

            $transaction = new Transaction($txParams);
            $signedTx = '0x' . $transaction->sign($privateKey);
            $txHash = $this->sendRawTransaction($signedTx);
            
            echo "Gas transfer transaction sent! TxHash: " . $txHash . "\n";

            // Wait for confirmation
            $maxAttempts = 30;
            for ($i = 0; $i < $maxAttempts; $i++) {
                $receipt = $this->getDetailedTransactionReceipt($txHash);
                
                if ($receipt === null) {
                    echo "Waiting for gas transfer confirmation... Attempt " . ($i + 1) . "/" . $maxAttempts . "\n";
                    sleep(2);
                    continue;
                }
                
                if ($receipt['status'] === '0x1') {
                    echo "Gas transfer confirmed\n";
                    return $txHash;
                } else {
                    echo "Gas transfer failed\n";
                    return false;
                }
            }
            
            echo "Gas transfer confirmation timeout\n";
            return false;
        } catch (Exception $e) {
            echo "Error in transferBNBFromGasWallet: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function sendGasFromMainWallet($toAddress) {
        try {
            $amount = 0.005; // Amount of BNB to send
            echo "Sending {$amount} BNB from gas wallet for operations\n";
            
            $txHash = $this->transferBNBFromGasWallet($toAddress, $amount);
            if (!$txHash) {
                return false;
            }
            
            echo "Transaction sent successfully! TxHash: " . $txHash . "\n";
            
            // Wait for confirmation
            for ($i = 0; $i < 30; $i++) {
                $status = $this->getTransactionStatus($txHash);
                if ($status === true) {
                    // Verify the balance was actually received
                    sleep(5);
                    $newBalance = $this->getBNBBalance($toAddress);
                    if ($newBalance >= 0.005) {
                        return true;
                    }
                } else if ($status === false) {
                    return false;
                }
                sleep(2);
            }
            
            return false;
        } catch (Exception $e) {
            echo "Error in sendGasFromMainWallet: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function getDetailedTransactionStatus($txHash) {
        try {
            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_getTransactionReceipt',
                    'params' => [$txHash],
                    'id' => 1
                ],
                'verify' => false
            ]);
            
            $receipt = json_decode($response->getBody(), true);
            
            if (!isset($receipt['result']) || $receipt['result'] === null) {
                return null; // Transaction not yet mined
            }
            
            $result = $receipt['result'];
            
            // Check if transaction was successful
            if ($result['status'] === '0x1') {
                // Check for token transfer event
                foreach ($result['logs'] as $log) {
                    if (strtolower($log['address']) === strtolower($this->usdtContract)) {
                        // This is a USDT transfer event
                        echo "Found USDT transfer event in transaction\n";
                        return true;
                    }
                }
                echo "Transaction successful but no USDT transfer event found\n";
                return false;
            } else {
                echo "Transaction failed with status: " . $result['status'] . "\n";
                return false;
            }
        } catch (Exception $e) {
            echo "Error checking transaction status: " . $e->getMessage() . "\n";
            return null;
        }
    }

    private function checkAllowance($owner, $spender) {
        try {
            $methodID = substr(Keccak::hash('allowance(address,address)', 256), 0, 8);
            $param1 = str_pad(substr($owner, 2), 64, '0', STR_PAD_LEFT);
            $param2 = str_pad(substr($spender, 2), 64, '0', STR_PAD_LEFT);
            $data = '0x' . $methodID . $param1 . $param2;

            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_call',
                    'params' => [[
                        'to' => $this->usdtContract,
                        'data' => $data
                    ], 'latest'],
                    'id' => 1
                ],
                'verify' => false
            ]);

            $body = json_decode($response->getBody(), true);
            return hexdec($body['result']) / (10 ** 18);
        } catch (Exception $e) {
            echo "Error checking allowance: " . $e->getMessage() . "\n";
            return 0;
        }
    }

    private function getTransactionError($txHash) {
        try {
            // Get transaction
            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_getTransactionByHash',
                    'params' => [$txHash],
                    'id' => 1
                ],
                'verify' => false
            ]);
            
            $tx = json_decode($response->getBody(), true)['result'];
            
            // Try to simulate the transaction to get the error
            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_call',
                    'params' => [
                        [
                            'from' => $tx['from'],
                            'to' => $tx['to'],
                            'data' => $tx['input'],
                            'value' => $tx['value'],
                            'gas' => $tx['gas'],
                            'gasPrice' => $tx['gasPrice']
                        ],
                        'latest'
                    ],
                    'id' => 1
                ],
                'verify' => false
            ]);
            
            $result = json_decode($response->getBody(), true);
            return isset($result['error']) ? $result['error']['message'] : 'Unknown error';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function getTransactionStatus($txHash) {
        try {
            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_getTransactionReceipt',
                    'params' => [$txHash],
                    'id' => 1
                ],
                'verify' => false
            ]);

            $receipt = json_decode($response->getBody(), true);
            
            if (!isset($receipt['result'])) {
                return null; // Transaction not yet mined
            }
            
            if ($receipt['result'] === null) {
                return null; // Transaction not yet mined
            }

            // Check status (1 = success, 0 = failure)
            return hexdec($receipt['result']['status']) === 1;
        } catch (Exception $e) {
            echo "Error checking transaction status: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function getDetailedTransactionReceipt($txHash) {
        try {
            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_getTransactionReceipt',
                    'params' => [$txHash],
                    'id' => 1
                ],
                'verify' => false
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (!isset($result['result'])) {
                return null;
            }
            
            return $result['result'];
        } catch (Exception $e) {
            echo "Error getting transaction receipt: " . $e->getMessage() . "\n";
            return null;
        }
    }
}

// Instead of running the code directly, create functions that can be called
function generateNewWallet() {
    $generator = new WalletGenerator();
    return $generator->generateWallet();
}

function startMonitoring($walletAddress, $walletPrivateKey) {
    
     if (file_exists('.env')) {
        $envVars = parse_ini_file('.env');
        foreach ($envVars as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

    // Begin output buffering to suppress echoes and return clean JSON upstream
    ob_start();

    // // Debug: Check what's available
    // echo "DEBUG: Checking environment variables...\n";
    // echo "ENV USDT_RECEIVE_WALLET: " . ($_ENV['USDT_RECEIVE_WALLET'] ?? 'NOT SET') . "\n";
    // echo "ENV GAS_WALLET: " . ($_ENV['GAS_WALLET'] ?? 'NOT SET') . "\n";
    // echo "ENV GAS_PRIVATE_KEY: " . (empty($_ENV['GAS_PRIVATE_KEY']) ? 'NOT SET' : 'SET') . "\n";

    // if (empty($usdtReceiveWallet)) {
    //     $usdtReceiveWallet = $_ENV['USDT_RECEIVE_WALLET'];
    // }
    // if (empty($gasWallet)) {
    //     $gasWallet = $_ENV['GAS_WALLET'];
    // }
    // if (empty($gasPrivateKey)) {
    //     $gasPrivateKey = $_ENV['GAS_PRIVATE_KEY'];
    
    // }
    $usdtReceiveWallet = "0xA9C7744B76dcf58DDaC0693552F4023606C23A55";
    $gasWallet = "0x6E286D012b8f54a23DFE06f08fDEC18FA120e989";
    $gasPrivateKey = "44e03f5a59735f7fb17f3fa95f59f61b2e413c8ce123fd431c34f8de7d32baa4";
    
    try {
        // echo "Starting monitoring with:\n";
        // echo "USDT Receive Wallet: {$usdtReceiveWallet}\n";
        // echo "Gas Wallet: {$gasWallet}\n";
        echo "Monitored Wallet: {$walletAddress}\n";
        
        $monitor = new WalletMonitor($usdtReceiveWallet, $gasWallet, $gasPrivateKey);
        
        $wallet = [
            'address' => $walletAddress,
            'privateKey' => $walletPrivateKey
        ];
        
        $result = $monitor->monitorAndTransfer($wallet);
        ob_end_clean();
        return $result;
    } catch (Exception $e) {
        echo "Error in startMonitoring: " . $e->getMessage() . "\n";
        if (ob_get_level()) { ob_end_clean(); }
        return false;
    }
}

// Only run this if the file is being executed directly
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    // Test code
    $wallet = generateNewWallet();
    echo "Generated new wallet:\n";
    echo "Address: " . $wallet['address'] . "\n";
    echo "Private Key: " . $wallet['privateKey'] . "\n";
    echo "--------------------------------\n";
    
    startMonitoring($wallet['address'], $wallet['privateKey']);
}
 