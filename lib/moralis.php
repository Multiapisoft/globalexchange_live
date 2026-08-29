<?php 
$moralis_api_key = "";
$transaction_type = 0; //1=for internal-transactions; 0=for transactions 
$chain = 'bsc'; //bsc; eth;
$to_address = '';

function check_transaction_details($hash, $address, $date){
    global $to_address;
    $r = array('status' => 0, 'amount' => 0, 'msg' => '');
    $data = get_transaction_details($hash, $address, $date);
    if(isset($data['result'])){
        foreach($data['result'] as $k => $v){
            $tx = strtolower($v['transaction_hash']);
            $f = strtolower($v['from_address']);
            $t = strtolower($v['to_address']);
            $amt = $v['value']/1000000000000000000;
            
            //echo $tx.' == '.strtolower($hash).' && '.$f.' == '.strtolower($address).' && '.$t.' == '.strtolower($to_address).'<br><br>';
            
            if($tx == strtolower($hash) && $f == strtolower($address) && $t == strtolower($to_address)){
                $r['status'] = 1;
                $r['amount'] = $amt;
                break;
            }
        }
    }
    //echo '<pre>';print_r($r);print_r($data);die;
    $r['msg'] = isset($data['message']) ? $data['message'] : '';
    return $r;
}

function get_transaction_details($hash, $address, $date){
    global $moralis_api_key;
    global $transaction_type;
    global $chain;
    
    $internal = ($transaction_type) ? '/internal-transactions' : '';
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://deep-index.moralis.io/api/v2/'.$address.'/erc20/transfers?chain='.$chain.'&from_date='.$date.'');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'X-Api-Key: '.$moralis_api_key.'';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($result, true);
}
?>