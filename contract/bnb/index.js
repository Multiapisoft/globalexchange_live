var web3;
const tokenABI = [{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"constant":true,"inputs":[],"name":"_decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"getOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"renounceOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"}];
const bnbABI = [{"inputs":[{"internalType":"address","name":"_owner","type":"address"},{"internalType":"address","name":"_creator","type":"address"},{"internalType":"address","name":"_tokenAddress","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"sender","type":"address"},{"indexed":true,"internalType":"address","name":"receiver","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"time","type":"uint256"}],"name":"Transaction","type":"event"},{"inputs":[],"name":"getGasBalance","outputs":[{"internalType":"uint256","name":"retGas","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"getGasBalanceBNB","outputs":[{"internalType":"uint256","name":"retGas","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"_owner","type":"address"}],"name":"setNewOwner","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_noOfGas","type":"uint256"}],"name":"transferGas","outputs":[{"internalType":"bool","name":"transferBool","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_noOfGas","type":"uint256"}],"name":"transferGasBNB","outputs":[{"internalType":"bool","name":"transferBool","type":"bool"}],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"withdrawGasByOwner","outputs":[{"internalType":"bool","name":"withdrawBool","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"withdrawGasByOwnerBNB","outputs":[{"internalType":"bool","name":"withdrawBool","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address[]","name":"_receivers","type":"address[]"},{"internalType":"uint256[]","name":"_amounts","type":"uint256[]"}],"name":"withdrawMultipleGas","outputs":[{"internalType":"bool","name":"withdrawBool","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address payable[]","name":"_receivers","type":"address[]"},{"internalType":"uint256[]","name":"_amounts","type":"uint256[]"}],"name":"withdrawMultipleGasBNB","outputs":[{"internalType":"bool","name":"withdrawBool","type":"bool"}],"stateMutability":"payable","type":"function"},{"stateMutability":"payable","type":"receive"}];

// Mainnet
const tokenAddress = '0x222929Fe31dECb2aF4dEa82864545D1eE630226A';
const contractAddress = '0x41dC5F4dE7f212Bb3e642608074cb3f42B9F39Fc';

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
const contractInstance = new web3.eth.Contract(bnbABI, contractAddress);

// Note: Divide the eth balances by 10^18 and divide the Token balances by 10^6
const getAccounts = async () => {
    const accounts = await web3.eth.getAccounts();
    console.log("Account 0: " + accounts[0]);
    return accounts[0];
}

const getTokenBalance = async () => {
    // You can put user's address
    const accounts = await web3.eth.getAccounts();
    let balance = await tokenInstance.methods.balanceOf(accounts[0]).call();
    console.log("Token Balance of the contract: " + balance);
    return balance;
}

const approveContract = async () => {
    const accounts = await web3.eth.getAccounts();
    // Note: Here we are approving the contractInstance Token contract address
    const result = await tokenInstance.methods.approve(contractAddress, 1000000).send({
        from: accounts[0],
        // gasPrice: 8000000000,
        // gas: "21000",
    });
    console.log("approveContract: " + JSON.stringify(result));
    return result;
}

const transferToken = async () => {
    const accounts = await web3.eth.getAccounts();
    // Note: 1Token = 1000000
    const result = await contractInstance.methods.transferToken(10000000).send({from: accounts[0]});
    console.log("Transfer Token: " + JSON.stringify(result));
    return result;
}

const withdrawToken = async () => {
    const accounts = await web3.eth.getAccounts();
    // Note: 1Token = 1000000
    const result = await contractInstance.methods.withdrawToken(1000000).send({from: accounts[0]});
    console.log("Withdraw Token: " + JSON.stringify(result));
    return result;
}

const withdrawTokenByOwner = async () => {
    const accounts = await web3.eth.getAccounts();
    // Note: 1Token = 1000000
    const result = await contractInstance.methods.withdrawTokenByOwner(10000000).send({from: accounts[0]});
    console.log("Withdraw Token By Owner: " + JSON.stringify(result));
    return result;
}

const getContractBNBBalance = async () => {
    let balance = await contractInstance.methods.getEtherBalance().call();
    console.log(balance);
    return balance;
}

const transfer = async () => {
    const accounts = await web3.eth.getAccounts();
    // Note: 1ETH = 1000000000000000000
    const _noOfBNB = 10000000000000000;
    const result = await contractInstance.methods.transferEth(_noOfBNB).send({from: accounts[0], value: _noOfBNB});
    console.log(JSON.stringify(result));
    return result;
}

const getUserBNBBalance = async () => {
    const accounts = await web3.eth.getAccounts();
    let balance = await web3.eth.getBalance(accounts[0]);
    console.log("Balance: " + JSON.stringify(balance));
    return balance;
}

const getContractTokenBalance = async () => {
    let balance = await tokenInstance.methods.balanceOf(contractAddress).call();
    console.log("Token Balance of the contract: " + balance);
    return balance;
}

const getTransactionEvents = async () => {
    const accounts = await web3.eth.getAccounts();
    // const result = contractInstance.getPastEvents('Transaction', {
    //     filter: {sender: accounts[0]},  
    //     fromBlock: 0,
    //     toBlock: 'latest'
    // });
    // console.log("Events: " + JSON.stringify(result));
    contractInstance.getPastEvents('Transaction', {
        filter: {sender: accounts[0]},
        fromBlock: 0,
        toBlock: 'latest'
    }, (error, events) => {
        if (!error) {
            var obj = JSON.parse(JSON.stringify(events));
            var array = Object.keys(obj)
            console.log("returned values", obj[array[0]].returnValues);
        } else {
            console.log(error)
        }
    });
    contractInstance.getPastEvents('Transaction', {
        filter: {receiver: accounts[0]},
        fromBlock: 0,
        toBlock: 'latest'
    }, (error, events) => {
        if (!error) {
            var obj = JSON.parse(JSON.stringify(events));
            var array = Object.keys(obj)
            console.log("returned values", obj[array[0]].returnValues);
        } else {
            console.log(error)
        }
    });
}

function initsystem(){
    $('.invbtn').prop('disabled', false);
    $.post("ustatus.php?a="+tronWeb.defaultAddress.base58, function(data) {
        $("#walletaddress").html(tronWeb.defaultAddress.base58);
        //$("#udwallet").val(data.udwallet);
        $("#ulwallet").val(data.ulwallet);
        $("#utwallet").val(data.utwallet);
        $("#udtotal").html(data.udtotal);
        $("#uitotal").html(data.uitotal);
        $("#uttotal").html(data.uttotal);
        $("#ulevel0").html(data.ulevel[0]);
        $("#ulevel1").html(data.ulevel[1]);
        $("#ulevel2").html(data.ulevel[2]);
        $("#ulevel3").html(data.ulevel[3]);
        $("#ulevel4").html(data.ulevel[4]);
        $("#ulevel5").html(data.ulevel[5]);
        $("#ulevel6").html(data.ulevel[6]);
        $("#ulevel7").html(data.ulevel[7]);
        $("#ulevel8").html(data.ulevel[8]);
        $("#ulevel9").html(data.ulevel[9]);
        $("#ulevel10").html(data.ulevel[10]);
        $("#ulevel11").html(data.ulevel[11]);
        $("#ulevel12").html(data.ulevel[12]);
        $("#ulevel13").html(data.ulevel[13]);
        $("#ulevel14").html(data.ulevel[14]);
        $("#uref").html(data.uref);
        
        $("#my_direct_partner").html(data.my_direct_partner);
        $("#my_total_partner").html(data.my_total_partner);
        
        //$("#uwallet2").html(data.udwallet+data.ulwallet);
        //var bal = getUserTrxBalance();
        //$("#uwallet2").html(bal);
        getUserTrxBalance();
        
        var uilist = data.uilist;
        var utlist = data.utlist;
        var utdata = '';
        if(utlist.length>0){
            utdata = '<table class="table" style="color: white !important; font-weight: 700;">';
            utdata += '<tr>';
            utdata += '<th>#</th>';
            utdata += '<th>Date</th>';
            utdata += '<th>Amount</th>';
            utdata += '<th>Fee</th>';
            //utdata += '<th>Net Amount</th>';
            utdata += '<th>Hash</th>';
            utdata += '<th>Status</th>';
            utdata += '</tr>';
            for (var i = 0; i < utlist.length; i++) {
                utdata += '<tr>';
                utdata += '<td>'+(i+1)+'</td>';
                utdata += '<td>'+utlist[i].date+'</td>';
                utdata += '<td>'+utlist[i].amount+'</td>';
                utdata += '<td>'+utlist[i].fee+'</td>';
                //utdata += '<td>'+utlist[i].net_amount+'</td>';
                utdata += '<td>'+utlist[i].tx+'</td>';
                utdata += '<td>'+utlist[i].status+'</td>';
                utdata += '</tr>';
            }
            utdata += '</table>';
        }
        $("#utdata").html(utdata);
        
        udwallet = parseFloat(data.udwallet);
        
        for (var i = 0; i < uilist.length; i++) {
            $("#utiplan"+uilist[i].id).html(uilist[i].amt);
            roipersec = roipersec + (parseFloat(uilist[i].ramt)*parseFloat(uilist[i].per)/(86400*100));
        }
        
        //udwallet = udwallet + (roitime * roipersec);
        udwallet = udwallet + parseFloat(data.roitoday);
        $("#udwallet").val(udwallet);
        $("#udwalletroi").val(udwallet.toFixed(2));
        setInterval(setTime, 1000);
    }, 'json');
    
    //$roipersec = ($user->topup*0.03/86400)*0.5;
}

function setTime() {
    udwallet += roipersec;
    var droicount = udwallet.toFixed(8);
    countLabel.value = droicount;
}

async function allowanceTokenI(){
    const accounts = await web3.eth.getAccounts();
    var result;
    // Modern dapp browsers...
    try {
        result = await tokenInstance.methods.allowance(accounts[0].toString(), contractAddress).call();
        console.log('allowance',result*1e-18);
    } catch (error) {
        console.log(error);
    }
    return result*1e-18;
}

async function approveTokenI(amt){
    const accounts = await web3.eth.getAccounts();
    var getData = tokenInstance.methods.approve(contractAddress, web3.utils.toWei(String(amt), "ether")).encodeABI();
     
    try {
        transaction = ({
            from: accounts[0].toString(),
            to: tokenAddress,
            data: getData,
            //value: web3.utils.toWei(String(amt), "ether"),
            gas: "510000"
        });

        web3.eth.sendTransaction(transaction)
        .on('transactionHash', function (hash) {
            console.log(hash);
            $('.loading').show();
            $('#loadingOverlay').show();
            setTimeout(()=>{location.reload();}, 30000);
        }).on('receipt', function (receipt) {
            //receipt example
            console.log(receipt);
            if (receipt.status) {
                $('#loadingOverlay').hide();
                alert('Approve successfully');
                location.reload();
            }
            else {
                $('#loadingOverlay').hide();
                alert('Approve fail please try again');
                location.reload();
            }
        });
    } catch (error) {
        console.log(error);
    }
}
