<?php
// Check if vendor/autoload.php exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    // Composer dependencies not installed
    die('<div style="background: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px; font-family: Arial, sans-serif;">
        <h3>⚠️ Composer Dependencies Missing</h3>
        <p><strong>Error:</strong> Required PHP packages are not installed.</p>
        <p><strong>Solution:</strong> Please install Composer and run the following commands:</p>
        <pre style="background: #f1f1f1; padding: 10px; border-radius: 4px;">
cd ' . __DIR__ . '
composer install</pre>
        <p><strong>Alternative:</strong> Contact your system administrator to install the required dependencies.</p>
        <hr>
        <p><small>Required packages: Web3, Ethereum TX, Keccak, Elliptic PHP, BIP39, BIP32</small></p>
    </div>');
}
use Web3p\EvmTool\Utils;
use GuzzleHttp\Client;
use Elliptic\EC;
use kornrunner\Keccak;
use Web3p\EthereumTx\Transaction;
use Web3p\EthereumTx\EIP1559Transaction;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
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

class WalletMonitor {
    private $provider;
    private $usdtReceiveWallet; 
    private $gasWallet; 
    private $gasPrivateKey; 
    private $usdtContract;
    
     // USDT BEP20 Contract ABI (only necessary functions)
    private $usdtAbi;

    public function __construct($usdtReceiveWallet, $gasWallet, $gasPrivateKey) {
        try {
            $this->provider = new Client(['base_uri' => 'https://bsc-dataseed.binance.org']);
            $this->usdtReceiveWallet = $usdtReceiveWallet;
            $this->gasWallet = $gasWallet;
            $this->gasPrivateKey = $gasPrivateKey;
            $this->usdtContract = $_ENV['USDT_CONTRACT'];
            $this->usdtAbi = $_ENV['USDT_ABI'];
            if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $gasWallet)) {
                throw new Exception("Invalid gas wallet address format");
            }
            if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $usdtReceiveWallet)) {
                throw new Exception("Invalid usdt receive wallet address format");
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
            
            // echo "API Response for address {$address}: " . print_r($body, true) . "\n";
            
            if (!isset($body['result'])) {
                echo "Warning: Invalid response from node for address {$address}\n";
                return 0;
            }

            return hexdec($body['result']) / (10 ** 18); 
        } catch (Exception $e) {
            echo "Error fetching BNB balance: " . $e->getMessage() . "\n";
            return 0;
        }
    }

    public function getusdtBalance($address) {
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
            return hexdec($body['result']) / (10 ** 18);
        } catch (Exception $e) {
            echo "Error fetching usdt balance: " . $e->getMessage() . "\n";
            return 0;
        }
    }


    public function monitorAndTransfer($wallet) {
        try {
            echo "Checking wallet: " . $wallet['address'] . "\n";
            
            $bnbBalance = $this->getBNBBalance($wallet['address']);
            $usdtBalance = $this->getusdtBalance($wallet['address']);
            
            echo "BNB Balance: " . $bnbBalance . " BNB\n";
            echo "usdt Balance: " . $usdtBalance . " usdt\n";

            $MIN_usdt_THRESHOLD = 0.00001; 
            $MIN_BNB_REQUIRED = 0.005;    
            if ($usdtBalance < $MIN_usdt_THRESHOLD) {
                echo "usdt balance too small to process (< {$MIN_usdt_THRESHOLD})\n";
                return [
                    'found' => false,
                    'message' => 'No significant usdt balance found'
                ];
            }
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
                sleep(15);
                
                $bnbBalance = $this->getBNBBalance($wallet['address']);
                if ($bnbBalance < $MIN_BNB_REQUIRED) {
                    echo "BNB transfer failed to arrive\n";
                    return [
                        'found' => false,
                        'message' => 'BNB transfer failed to arrive'
                    ];
                }
            }
            echo "Proceeding with usdt transfer...\n";
            $success = $this->transferusdt($wallet['address'], $wallet['privateKey'], $usdtBalance);
            
            if (!$success) {
                echo "usdt transfer failed\n";
                $finalBnbBalance = $this->getBNBBalance($wallet['address']);
                if ($finalBnbBalance > 0.001) {
                    $this->transferBNB($wallet['address'], $wallet['privateKey'], $finalBnbBalance);
                    echo "Returned remaining BNB to gas wallet after failed usdt transfer\n";
                }
                return [
                    'found' => false,
                    'message' => 'usdt transfer failed'
                ];
            }

            sleep(15);
            $finalusdtBalance = $this->getusdtBalance($wallet['address']);
            
            if ($finalusdtBalance < $MIN_usdt_THRESHOLD) {
                echo "usdt transfer successful, returning remaining BNB...\n";
                $finalBnbBalance = $this->getBNBBalance($wallet['address']);
                if ($finalBnbBalance > 0.001) {
                    $this->transferBNB($wallet['address'], $wallet['privateKey'], $finalBnbBalance);
                    echo "Returned remaining BNB to gas wallet\n";
                }
                
                return [
                    'found' => true,
                    'amount' => $usdtBalance,
                    'currency' => 'usdt',
                    'message' => 'Transfer completed successfully'
                ];
            } else {
                echo "usdt transfer verification failed\n";
                return [
                    'found' => false,
                    'message' => 'usdt transfer verification failed'
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

    private function approveusdt($fromAddress, $privateKey, $amount) {
        try {
            echo "Approving usdt transfer...\n";
            
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
            echo "Error in approveusdt: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function transferusdt($fromAddress, $privateKey, $amount) {
        try {
            $actualBalance = $this->getusdtBalance($fromAddress);
            $amount = min($amount, $actualBalance);
            $amount = round($amount, 6);

            // Direct transfer without allowance
            $methodID = substr(Keccak::hash('transfer(address,uint256)', 256), 0, 8);
            $address = str_pad(substr($this->usdtReceiveWallet, 2), 64, '0', STR_PAD_LEFT);
            $amountInWei = bcmul(sprintf('%.6f', $amount), bcpow('10', '18', 0), 0);
            $amountHex = str_pad(gmp_strval(gmp_init($amountInWei, 10), 16), 64, '0', STR_PAD_LEFT);
            
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
            $result = my_query("SELECT uid FROM user WHERE pay_address='" . $fromAddress . "' LIMIT 1");
            $row = mysqli_fetch_assoc($result);
            // $this->sendRawTransaction2($signedTx,$row );
            
            if ($row && $amount>0) {
                $uid = $row['uid'];
                
                $txid = $amountHex;
                $amount_coin = $amount;
                $fee = 0;
                $net_amount = $amount - $fee;
            
                // Insert data into deposit_block table
                my_query("INSERT INTO deposit_block (uid, datetime, status, amount, fee, net_amount, amount_coin, txid, data, type) 
                          VALUES ('" . $uid . "', '" . date('c') . "', 1, '" . $amount . "', '" . $fee . "', '" . $net_amount . "', '" . $amount_coin . "', '" . $txid . "', '', 'usdt.BEP20')");
            
                // Update user's wallet_admin for usdt dear 
                my_query("UPDATE user SET got=1, wallet_topup = wallet_topup + '" . $net_amount . "' WHERE pay_address='" . $fromAddress . "'");
            } 
            $txHash = $this->sendRawTransaction2('0x' . $transaction->sign(str_replace('0x', '', $privateKey)), $row);
            
            sleep(5); // Wait for transaction confirmation
            return $this->getusdtBalance($fromAddress) < 0.0001;
        } catch (Exception $e) {
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
                        // This is a usdt transfer event
                        echo "Found usdt transfer event in transaction\n";
                        return true;
                    }
                }
                echo "Transaction successful but no usdt transfer event found\n";
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
    // Replace these with your actual wallet addresses and private key
    $usdtReceiveWallet = $_ENV['OWN_PAY_RECEIVEWALLET']; // Wallet to receive usdt
    $gasWallet = $_ENV['OWN_PAY_GAS_WALLET']; // Gas wallet address
    $gasPrivateKey = $_ENV['OWN_PAY_GAS_PRIVATE_KEY']; // Gas wallet private key
    try {
        echo "Starting monitoring with:\n";
        echo "usdt Receive Wallet: {$usdtReceiveWallet}\n";
        echo "Gas Wallet: {$gasWallet}\n";
        echo "Monitored Wallet: {$walletAddress}\n";
        
        $monitor = new WalletMonitor($usdtReceiveWallet, $gasWallet, $gasPrivateKey);
        
        $wallet = [
            'address' => $walletAddress,
            'privateKey' => $walletPrivateKey
        ];
        
        return $monitor->monitorAndTransfer($wallet);
        
    } catch (Exception $e) {
        echo "Error in startMonitoring: " . $e->getMessage() . "\n";
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
