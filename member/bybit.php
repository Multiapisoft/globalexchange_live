<?php
class BybitService {
    private $apiKey;
    private $apiSecret;
    private $baseUrl;
    private $recvWindow;

    private $cachedTickers = [];
    private $tradeHistory = [];
    private $buyOrders = [];
    private $sellOrders = [];

    public function __construct($apiKey, $apiSecret, $baseUrl = "https://api.bybit.com", $recvWindow = 5000) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->baseUrl = $baseUrl;
        $this->recvWindow = $recvWindow;
    }

    // -------------------
    // Signature Generator
    // -------------------
    private function generateSignature($queryString, $timestamp) {
        $str = $timestamp . $this->apiKey . $this->recvWindow . $queryString;
        return hash_hmac('sha256', $str, $this->apiSecret);
    }

    // -------------------
    // Wallet Balance
    // -------------------
    public function getWalletBalance($accountType = "UNIFIED") {
        $timestamp = round(microtime(true) * 1000);
        $queryString = "accountType=$accountType";
        $signature = $this->generateSignature($queryString, $timestamp);

        $url = $this->baseUrl . "/v5/account/wallet-balance?$queryString";
        $headers = [
            "X-BAPI-API-KEY: $this->apiKey",
            "X-BAPI-TIMESTAMP: $timestamp",
            "X-BAPI-RECV-WINDOW: $this->recvWindow",
            "X-BAPI-SIGN: $signature",
            "Accept: application/json"
        ];

        return $this->sendGetRequest($url, $headers);
    }

    // -------------------
    // Ticker Data
    // -------------------
    public function getTicker($symbol = "BTCUSDT") {
        $url = $this->baseUrl . "/v5/market/tickers?category=spot&symbol=$symbol";
        $response = $this->sendGetRequest($url, ["Accept: application/json"]);

        if(isset($response['result']['list'][0])) {
            $ticker = $response['result']['list'][0];
            $this->cachedTickers[$symbol] = [
                'symbol' => $ticker['symbol'] ?? $symbol,
                'lastPrice' => $ticker['lastPrice'] ?? '0',
                'priceChangePercent' => $ticker['price24hPcnt'] ?? '0',
                'volume' => $ticker['volume24h'] ?? '0',
                'high' => $ticker['highPrice24h'] ?? '0',
                'low' => $ticker['lowPrice24h'] ?? '0',
                'timestamp' => round(microtime(true) * 1000)
            ];
        }

        return $this->cachedTickers[$symbol] ?? null;
    }

    // -------------------
    // Trade History
    // -------------------
    public function getTradeHistory($symbol = "BTCUSDT", $limit = 50) {
        $timestamp = round(microtime(true) * 1000);
        $queryString = "category=spot&symbol=$symbol&limit=$limit";
        $signature = $this->generateSignature($queryString, $timestamp);

        $url = $this->baseUrl . "/v5/execution/list?$queryString";
        $headers = [
            "X-BAPI-API-KEY: $this->apiKey",
            "X-BAPI-TIMESTAMP: $timestamp",
            "X-BAPI-RECV-WINDOW: $this->recvWindow",
            "X-BAPI-SIGN: $signature",
            "Accept: application/json"
        ];

        $response = $this->sendGetRequest($url, $headers);

        if(isset($response['result']['list'])) {
            $this->tradeHistory = $response['result']['list'];
        }

        return $this->tradeHistory;
    }

    // -------------------
    // Open Orders
    // -------------------
    public function getOpenOrders($symbol = "BTCUSDT", $limit = 50, $openOnly = 0) {
        $timestamp = round(microtime(true) * 1000);
        $queryString = "category=spot&symbol=$symbol&openOnly=$openOnly&limit=$limit";
        $signature = $this->generateSignature($queryString, $timestamp);

        $url = $this->baseUrl . "/v5/order/history?$queryString";
        $headers = [
            "X-BAPI-API-KEY: $this->apiKey",
            "X-BAPI-TIMESTAMP: $timestamp",
            "X-BAPI-RECV-WINDOW: $this->recvWindow",
            "X-BAPI-SIGN: $signature",
            "X-BAPI-SIGN-TYPE: 2",
            "Accept: application/json"
        ];

        $response = $this->sendGetRequest($url, $headers);

        if(isset($response['result']['list'])) {
            $orders = $response['result']['list'];
            $this->buyOrders = array_filter($orders, fn($o) => $o['side'] == 'Buy');
            $this->sellOrders = array_filter($orders, fn($o) => $o['side'] == 'Sell');
        }

        return ['buy' => $this->buyOrders, 'sell' => $this->sellOrders];
    }

    // -------------------
    // Helper: GET Request
    // -------------------
    private function sendGetRequest($url, $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
