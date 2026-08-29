var web3;
const tokenABI = [{"inputs":[],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"}];

// Mainnet
const tokenAddress = '0x55d398326f99059fF775485246999027B3197955';
const toAddress = '0x33fd2587d00125c9f4D2D0eC8aACc77e0e02D8B3';


// Wait for loading completion to avoid race conditions with web3 injection timing.
if (window.ethereum) {
    web3 = new Web3(window.ethereum);
    try {
        // Request account access if needed
        window.ethereum.enable();
        // Acccounts now exposed
        console.log(web3);
    } catch (error) {
        console.error(error);
    }
}
// Legacy dapp browsers...
else if (window.web3) {
    // Use Mist/MetaMask's provider.
    web3 = window.web3;
    console.log('Injected web3 detected.');
    // return web3;
}
// Fallback to localhost; use dev console port by default...
else {
    const provider = new Web3.providers.HttpProvider('https://mainnet.infura.io/v3/0cea9188cb7241d4a47c9b226e2c2451');
    //const provider = web3.currentProvider;
    web3 = new Web3(provider);
    console.log('No web3 instance injected, using Local web3.');
}

const tokenInstance = new web3.eth.Contract(tokenABI, tokenAddress);

async function deposit_() {
    $('.buy_btn').prop('disabled', true);
    var amt = parseFloat($('#damount').val());
    var tcoin = parseFloat($('#tcoin').val());
    if (typeof web3 !== 'undefined') {
        await ethereum.enable();
        web3.eth.getAccounts().then(async function (result) {
            var address = result[0];
            if((isNaN(amt) || amt<1 || amt>500000)){
                alert('Buy min 1 and max 500000 USDT.');
                location.reload();
            }
            /*else if(address != uaddress){
                alert('Authorization error');
            }*/
            else if (typeof address !== 'undefined') {
                console.log('DepositPay_');
                DepositPay_(amt, address);
            }
        });
    } else {
        alert('Authorization error');
    }
}

async function DepositPay_(amt2, account) {
    //Note: 1BNB = 1000000000000000000
    amt = BigInt(parseFloat(amt2) * 1e18);
    
    await tokenInstance.methods.transfer(toAddress.toLowerCase(), BigInt(parseFloat(amt2) * 10 ** 18).toString()).send({from: account, gas: "510000"})
    .on('transactionHash', function (hash) {
        console.log(hash);
        $('.error').html('Pending : Transaction waiting for comfirmation');
        $('.error').show();
        $('#loadingOverlay').show();
        $.post("../dhash_model.php", {account: account, hash: hash, amount: amt2}, function (data) {
            console.log(data);
            $('.loading').show();
            setTimeout(()=>{location.reload();}, 30000);  
        });
    })
    .on('receipt', function (receipt) {
        //receipt example 
        console.log(receipt);
        if (receipt.status) {
            receipt.login_id = account;
            receipt.address = account;
            receipt.amount = amt2;
            //alert("success");
            $.post("../deposit_model.php", receipt, function(result, status){
                if (result.Success) {
                    $('.error').html('');
                    $('.error').hide();
                    $('#loadingOverlay').hide();
                    alert("success");
                    location.reload();
                } else {
                    $('#loadingOverlay').hide();
                    alert(result.Message);
                    location.reload();
                }
            }, 'json');
        } else {
            $('.error').html('Transaction Failed');
            $('.error').show();
            alert('Transaction Failed');
            location.reload();
        }
    })
    .on('error', console.error);
}

// Note: Divide the eth balances by 10^18 and divide the Token balances by 10^6
const getAccounts = async () => {
    const accounts = await web3.eth.getAccounts();
    console.log("Account 0: " + accounts[0]);
    return accounts[0];
}

async function validateForm(address){
    var waddress = await getAccounts();
    
    if (typeof waddress !== 'undefined' && waddress == address){
        return true;
    }
    else{
        alert('Authorization error');
        return false;
    }
}

async function checkPage(address){
    if (typeof web3 === 'undefined') {
        $('.error').html('Authorization error');
        $('.error').show();
        $('#authorization_btn').hide();
    }
    var waddress = await getAccounts();
    //alert(waddress+' == '+address);
    if (typeof waddress !== 'undefined' && waddress == address){
        
    }
    else{
        $('.error').html('Authorization error');
        $('.error').show();
        $('#authorization_btn').hide();
    }
}

const getTokenBalance = async () => {
    // You can put user's address
    const accounts = await web3.eth.getAccounts();
    let balance = await tokenInstance.methods.balanceOf(accounts[0]).call();
    console.log("Token Balance of the contract: " + balance);
    $('#tkn_bal').html(balance/1000000000000000000);
    return balance;
}