<?php
require 'vendor/autoload.php';
use Web3p\EvmTool\Utils;
use GuzzleHttp\Client;
use Elliptic\EC;
use kornrunner\Keccak;
use Web3p\EthereumTx\Transaction;
use Web3p\EthereumTx\EIP1559Transaction;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

class WalletWithdrawal {
    private $provider;
    private $contract;
    private $fromAddress;
    private $privateKey;
    private $toAddress;
    
    public function __construct($toAddress) {
        try {
            $this->provider = new Client(['base_uri' => 'https://bsc-dataseed.binance.org']);
            $this->contract = $_ENV['WD_CONTRACT'];
            $this->fromAddress = $_ENV['WD_FROM_ADDRESS']; // Gas wallet address
            $this->privateKey = $_ENV['WD_PRIVATE_KEY']; // Gas wallet private key
            $this->toAddress = $toAddress;
            
            if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->fromAddress)) {
                throw new Exception("Invalid gas wallet address format");
            }
            if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $toAddress)) {
                throw new Exception("Invalid receive wallet address format");
            }
        } catch (Exception $e) {
            echo "Error in constructor: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    public function getBalance($address) {
        try {
            $methodID = substr(Keccak::hash('balanceOf(address)', 256), 0, 8);
            $params = str_pad(substr($address, 2), 64, '0', STR_PAD_LEFT);
            $data = '0x' . $methodID . $params;

            $response = $this->provider->post('', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_call',
                    'params' => [[
                        'to' => $this->contract,
                        'data' => $data
                    ], 'latest'],
                    'id' => 1
                ],
                'verify' => false
            ]);

            $body = json_decode($response->getBody(), true);
            return hexdec($body['result']) / (10 ** 18);
        } catch (Exception $e) {
            echo "Error fetching balance: " . $e->getMessage() . "\n";
            return 0;
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
    
    public function transfer($amount) {
        try {
            $actualBalance = $this->getBalance($this->fromAddress);
            if($actualBalance < $amount){
                echo 'Low Balance';
                return false;
            }
            $amount = round($amount, 6);

            // Direct transfer without allowance
            $methodID = substr(Keccak::hash('transfer(address,uint256)', 256), 0, 8);
            $address = str_pad(substr($this->toAddress, 2), 64, '0', STR_PAD_LEFT);
            $amountInWei = bcmul(sprintf('%.6f', $amount), bcpow('10', '18', 0), 0);
            $amountHex = str_pad(gmp_strval(gmp_init($amountInWei, 10), 16), 64, '0', STR_PAD_LEFT);
            
            $txParams = [
                'nonce' => '0x' . dechex($this->getTransactionCount($this->fromAddress)),
                'to' => $this->contract,
                'value' => '0x0',
                'data' => '0x' . $methodID . $address . $amountHex,
                'gas' => '0x186A0', // 100000 gas
                'gasPrice' => '0x' . dechex(3 * 10 ** 9), // 3 Gwei
                'chainId' => 56
            ];

            $transaction = new Transaction($txParams);
            $txHash = $this->sendRawTransaction('0x' . $transaction->sign(str_replace('0x', '', $this->privateKey)));
            echo $txHash;
            return $txHash;
        } catch (Exception $e) {
            return false;
        }
    }
    
}

function startWalletWithdrawal($toAddress, $amount, $c = "USDT") {
    try {
        echo "Starting wd with:\n";
        echo "Receive Wallet: {$toAddress}\n";
        echo "Amount: {$amount}\n";
        
        $withdrawal = new WalletWithdrawal($toAddress);
        
        return $withdrawal->transfer($amount);
    } catch (Exception $e) {
        echo "Error in startMonitoring: " . $e->getMessage() . "\n";
        return false;
    }
}

// Only run this if the file is being executed directly
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    // Test code
    $toAddress = "";
    $amount = 0.0002;
    $wd = startWalletWithdrawal($toAddress, $amount);
    print($wd);
}
