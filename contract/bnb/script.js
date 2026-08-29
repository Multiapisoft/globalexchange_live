async function connectWallet()
{
    // mainnet 
    // var web3js = new Web3('https://bsc-dataseed1.binance.org:443');
    // testnet
    // var web3js = new Web3('https://data-seed-prebsc-1-s1.binance.org:8545');

    var web3js = new Web3(web3.currentProvider);


    // Modern dapp browsers...
    if (window.ethereum) {
        try {
            await ethereum.enable();
            console.log("UserAddress",web3js.givenProvider.selectedAddress);
            document.getElementById('address').value = web3js.givenProvider.selectedAddress;
           
        } catch (error) {
            console.log(error);
        }
    }
    // Legacy dapp browsers...
    else if (window.web3) {
        console.log("UserAddress",web3js.givenProvider.selectedAddress);
        document.getElementById('address').value = web3js.givenProvider.selectedAddress;

    }
    // Non-dapp browsers...
    else {
        alert('Non-Ethereum/Binance browser detected. You should consider trying MetaMask/Binance Smart chain Extension!');
    }
}



        



async function fetchContractPrice()
{
    // mainnet 
    // var web3js = new Web3('https://bsc-dataseed1.binance.org:443');
    // testnet
    // var web3js = new Web3('https://data-seed-prebsc-1-s1.binance.org:8545');
    var web3js = new Web3(web3.currentProvider);


    var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];

    // convert amounts to Wei


    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    // console.log(getData);

    // Modern dapp browsers...
    if (window.ethereum) {
        try {
            await ethereum.enable();
            var contractPrice = await contractInstance.methods.tokenPriceBnb().call();
            var priceBnb = contractPrice/1000000000000000000;
            console.log(priceBnb);
            document.getElementById('response').value = priceBnb;
           
        } catch (error) {
            console.log(error);
        }
    }
    // Legacy dapp browsers...
    else if (window.web3) {
        try {
            var contractPrice = await contractInstance.methods.tokenPriceBnb().call();
            var priceBnb = contractPrice/1000000000000000000;
            console.log(priceBnb);
            document.getElementById('response').value = priceBnb;
        } catch (error) {
            console.log(error);
        }
    }
    // Non-dapp browsers...
    else {
        alert('Non-Ethereum/Binance browser detected. You should consider trying MetaMask/Binance Smart chain Extension!');
    }
}


function myDetails(){
    var web3js = new Web3(web3.currentProvider);


    var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];
    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    // myDetails();
    (async () => {
        // Modern dapp browsers...
        if (window.ethereum) {
            try {
                var contractPrice = await contractInstance.methods.tokenPriceBnb().call();
                var priceBnb = contractPrice/1000000000000000000;
                console.log(priceBnb);
               
                //document.getElementById("priceToken").innerHTML = priceBnb;
            } catch (error) {
                console.log(error);
            }

        }

        // Non-dapp browsers...
        else {
            document.getElementById("priceToken").innerHTML = "Connect to Wallet";
        }
    })();
}

function calculateToken() {
    var web3js = new Web3(web3.currentProvider);
    var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];
    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    myDetails();
    tokenRemaining();
    (async () => {
        // Modern dapp browsers...
        if (window.ethereum) {
            try {
                var contractPrice = await contractInstance.methods.tokenPriceBnb().call();
                var priceBnb = contractPrice/1000000000000000000;
                console.log(priceBnb);
                var x = document.getElementById("bnb").value;
                var amount = parseFloat(x / priceBnb);
                console.log(amount);
                document.getElementById("cbai").value = amount;

            } catch (error) {
                console.log(error);
            }

        }

        // Non-dapp browsers...
        else {
            document.getElementById("cbai").value = "Connect to Wallet";
        }
    })();
}

function calculateTokenSell() {
    var web3js = new Web3(web3.currentProvider);
    var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];
    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    
    (async () => {
        // Modern dapp browsers...
        if (window.ethereum) {
            try {
                var contractPrice = await contractInstance.methods.tokenPriceBnbSell().call();
                var priceBnb = contractPrice/1000000000000000000;
                console.log(priceBnb);
                var x = document.getElementById("bnbsell").value;
                var amount = parseFloat(x / priceBnb);
                console.log(amount);
                document.getElementById("cbaisell").value = amount;
                
                var allowance = await allowanceToken_();
                if(allowance >= amount){
                    $("#sellbtn").html('Sell');
                    //document.getElementById("sellbtn").text = 'Sell';
                }
                else{
                    $("#sellbtn").html('Approve');
                    //document.getElementById("sellbtn").text = 'Approve';
                }

            } catch (error) {
                console.log(error);
            }

        }

        // Non-dapp browsers...
        else {
            document.getElementById("cbai").value = "Connect to Wallet";
        }
    })();
}



function calculateToken_() {
    var web3js = new Web3(web3.currentProvider);
    var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];
    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    myDetails();
    tokenRemaining();
    (async () => {
        // Modern dapp browsers...
        if (window.ethereum) {
            try {
                var contractPrice = await contractInstance.methods.tokenPriceBnb().call();
                var priceBnb = contractPrice/1000000000000000000;
                console.log(priceBnb);
                var x = document.getElementById("cbai").value;
                var amount = parseFloat(x * priceBnb);
                console.log(amount);
                document.getElementById("bnb").value = amount;

            } catch (error) {
                console.log(error);
            }

        }

        // Non-dapp browsers...
        else {
            document.getElementById("bnb").value = "Connect to Wallet";
        }
    })();
}

function calculateTokenSell_() {
    var web3js = new Web3(web3.currentProvider);
    var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];
    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    
    (async () => {
        // Modern dapp browsers...
        if (window.ethereum) {
            try {
                var contractPrice = await contractInstance.methods.tokenPriceBnbSell().call();
                var priceBnb = contractPrice/1000000000000000000;
                console.log(priceBnb);
                var x = document.getElementById("cbaisell").value;
                var amount = parseFloat(x * priceBnb);
                console.log(amount);
                document.getElementById("bnbsell").value = amount;
                
                var allowance = await allowanceToken_();
                if(allowance >= amount){
                    $("#sellbtn").html('Sell');
                    //document.getElementById("sellbtn").text = 'Sell';
                }
                else{
                    $("#sellbtn").html('Approve');
                    //document.getElementById("sellbtn").text = 'Approve';
                }

            } catch (error) {
                console.log(error);
            }

        }

        // Non-dapp browsers...
        else {
            document.getElementById("bnbsell").value = "Connect to Wallet";
        }
    })();
}

function tokenRemaining() {
    var web3js = new Web3(web3.currentProvider);


    var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];
    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    (async () => {
        // Modern dapp browsers...
        if (window.ethereum) {
            try {
                var contractPrice = await contractInstance.methods.tokenBalance().call();
                var priceBnb = contractPrice/1000000000000000000;
            
                document.getElementById("tokenBal").value = priceBnb;

            } catch (error) {
                console.log(error);
            }

        }

        // Non-dapp browsers...
        else {
            document.getElementById("tokenBal").value = "Connect to Wallet";
        }
    })();
}

            async function buyToken() {

                var web3js = new Web3(web3.currentProvider);

                var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
                var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];


                var contractInstance = new web3js.eth.Contract(AbiOfContract);
                //var getData = contractInstance.methods.ExchangeBNBforTokenMannual(document.getElementById("ref").value).encodeABI();
                var getData = contractInstance.methods.ExchangeBNBforTokenMannual().encodeABI();
                // console.log(getData);
                var mainAmount = document.getElementById("bnb").value;
                console.log(mainAmount);
                var contractInstance2 = new web3js.eth.Contract(AbiOfContract,contractAddress);
                var contractPrice = await contractInstance2.methods.tokenPriceBnb().call();
                var priceBnb = contractPrice/1000000000000000000;
                console.log(priceBnb);
                var amount = parseFloat(mainAmount / priceBnb);
                console.log(amount);
                
                if(amount < 100){
                    alert("Minimum token buy is 100");
                    return false;
                }

                // Mobile
                if (window.ethereum.isMetaMask) {
                    handleEthereum();
                } else if (window.ethereum && window.ethereum.isTrust) {
                    handleMobile();
                } else {
                    window.addEventListener('ethereum#initialized', handleEthereum, {
                        once: true,
                    });

                    // If the event is not dispatched by the end of the timeout,
                    // the user probably doesn't have MetaMask installed.
                    setTimeout(handleEthereum, 3000); // 3 seconds
                }

                async function handleEthereum() {
                    if (ethereum && ethereum.isMetaMask) {
                        console.log('Ethereum successfully detected!');
                        var fromAdd = await (ethereum.request({
                            method: 'eth_requestAccounts'
                        }));
                        var contractInstance2 = new web3js.eth.Contract(AbiOfContract,contractAddress);
                        var contractPrice = await contractInstance2.methods.tokenPriceBnb().call();
                        // Access the decentralized web!
                        try {
                            transaction = ({
                                from: fromAdd.toString(),
                                to: contractAddress,
                                data: getData,
                                value: web3js.utils.toWei(String(mainAmount), "ether"),
                                gas: "2100000"
                            });
                            // console.log(transaction);

                            web3js.eth.sendTransaction(transaction)
                                    .on('transactionHash', function (hash) {
                                        console.log(hash);
                                        //document.getElementById("cbai").value = hash;
                                        $('#loadingOverlay').show();
                                        
                                        var priceBnb = contractPrice/1000000000000000000;
                                        console.log(priceBnb);
                                        var amount = parseFloat(mainAmount / priceBnb);
                                        console.log(amount);
                                        
                                        $.post("buy_hash_model.php", {hash: hash, amount: amount}, function (data) {
                                            console.log(data);
                                            $('.loading').show();
                                        });
                                    })
                                    .on('receipt', function (receipt) {
                                        //receipt example
                                        console.log(receipt);
                                        if (receipt.status) {
                                            $('#loadingOverlay').hide();
                                            //alert("Transaction success");
                                            //location.reload();
                                            location.href = "dashboard.php";
                                        } else {
                                            $('#loadingOverlay').hide();
                                            //alert("Transaction failed");
                                            //location.reload();
                                            location.href = "dashboard.php";
                                        }
                                    });
                        } catch (error) {
                            console.log(error);
                            document.getElementById("cbai").value = error;
                        }
                    } else {
                        console.log('Please install MetaMask!');
                    }
                }

                async function handleMobile() {
                    if (ethereum && ethereum.isTrust) {
                        console.log('Ethereum successfully detected!');
                        var fromAdd = await (ethereum.request({
                            method: 'eth_requestAccounts'
                        }));
                        var contractInstance2 = new web3js.eth.Contract(AbiOfContract,contractAddress);
                        var contractPrice = await contractInstance2.methods.tokenPriceBnb().call();
                        // Access the decentralized web!
                        try {
                            transaction = ({
                                from: fromAdd.toString(),
                                to: contractAddress,
                                data: getData,
                                value: web3js.utils.toWei(String(mainAmount), "ether"),
                                gas: "210000"
                            });
                            // console.log(transaction);

                            web3js.eth.sendTransaction(transaction)
                                    .on('transactionHash', function (hash) {
                                        console.log(hash);
                                        //document.getElementById("cbai").value = hash;
                                        $('#loadingOverlay').show();
                                        
                                        var priceBnb = contractPrice/1000000000000000000;
                                        console.log(priceBnb);
                                        var amount = parseFloat(mainAmount / priceBnb);
                                        console.log(amount);
                                        
                                        $.post("buy_hash_model.php", {hash: hash, amount: amount}, function (data) {
                                            console.log(data);
                                            $('.loading').show();
                                            setTimeout(()=>{location.href = "dashboard.php";}, 30000);
                                        });
                                    })
                                    .on('confirmation', function(confirmationNumber, receipt){
                                        $('#loadingOverlay').hide();
                                        location.href = "dashboard.php";
                                    })
                                    .on('receipt', function (receipt) {
                                        //receipt example
                                        console.log(receipt);
                                        if (receipt.status) {
                                            $('#loadingOverlay').hide();
                                            //alert("Transaction success");
                                            //location.reload();
                                            location.href = "dashboard.php";
                                        } else {
                                            $('#loadingOverlay').hide();
                                            //alert("Transaction failed");
                                            //location.reload();
                                            location.href = "dashboard.php";
                                        }
                                    });
                        } catch (error) {
                            console.log(error);
                            document.getElementById("cbai").value = error;
                        }
                    } else {
                        console.log('Please install MetaMask!');
                    }
                }
            }



        //<!-- On Event -->
        
            document.getElementById("bnb").addEventListener("input", calculateToken);
            document.getElementById("bnbsell").addEventListener("input", calculateTokenSell);
            
            document.getElementById("cbai").addEventListener("input", calculateToken_);
            document.getElementById("cbaisell").addEventListener("input", calculateTokenSell_);
            //document.getElementById("priceToken").addEventListener("load", myDetails);
            window.onload = function () {
                setInterval(myDetails, 5000);
            }
        

            async function sellToken() {

                var web3js = new Web3(web3.currentProvider);

                var contractAddress = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
                var AbiOfContract = [{"inputs":[{"internalType":"address","name":"_tokenAddr","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"BnbTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"beneficiary","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"TokenTransfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"addr","type":"address"},{"indexed":true,"internalType":"address","name":"upline","type":"address"}],"name":"Upline","type":"event"},{"inputs":[],"name":"ExchangeBNBforTokenMannual","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"ExchangeTokenforBNBMannual","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address payable","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balances","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bnbDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"depositCrypto","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[],"name":"minBuy","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"ref_bonuses","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenAddr","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenBalance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenDecimal","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"tokenExchanged","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnb","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenPriceBnbSell","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newTokenAddr","type":"address"}],"name":"updateTokenAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newDecimal","type":"uint256"}],"name":"updateTokenDecimal","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"newTokenValue","type":"uint256"}],"name":"updateTokenPriceSell","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"bnb_paid","type":"uint256"},{"internalType":"uint40","name":"deposit_time","type":"uint40"},{"internalType":"uint256","name":"total_deposits","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address payable","name":"beneficiary","type":"address"}],"name":"withdrawCrypto","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"beneficiary","type":"address"}],"name":"withdrawTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}];


                var contractInstance = new web3js.eth.Contract(AbiOfContract);
                var mainAmount = document.getElementById("cbaisell").value;
                var getData = contractInstance.methods.ExchangeTokenforBNBMannual(web3js.utils.toWei(String(mainAmount), "ether")).encodeABI();
                // console.log(getData);
                
                console.log(mainAmount);

                // Mobile
                if (window.ethereum.isMetaMask) {
                    var allowance = await allowanceToken_();
                    console.log(allowance, '====');
                    if(allowance >= mainAmount){
                        console.log(allowance, '====1');
                        handleEthereum();
                    }
                    else{
                        console.log(allowance, '====2');
                        await approveToken_(mainAmount);
                        //handleEthereum();
                    }
                } else if (window.ethereum && window.ethereum.isTrust) {
                    var allowance = await allowanceToken_();
                    if(allowance >= mainAmount){
                        handleMobile();
                    }
                    else{
                        await approveToken_(mainAmount);
                        //handleMobile();
                    }

                } else {
                    window.addEventListener('ethereum#initialized', handleEthereum, {
                        once: true,
                    });

                    // If the event is not dispatched by the end of the timeout,
                    // the user probably doesn't have MetaMask installed.
                    setTimeout(handleEthereum, 3000); // 3 seconds
                }

                async function handleEthereum() {
                    if (ethereum && ethereum.isMetaMask) {
                        console.log('Ethereum successfully detected!');
                        var fromAdd = await (ethereum.request({
                            method: 'eth_requestAccounts'
                        }));
                        // Access the decentralized web!
                        try {
                            transaction = ({
                                from: fromAdd.toString(),
                                to: contractAddress,
                                data: getData,
                                gas: "210000",
                                //: web3js.utils.toWei(String(mainAmount), "ether")
                            });
                            // console.log(transaction);

                            web3js.eth.sendTransaction(transaction)
                                    .on('transactionHash', function (hash) {
                                        console.log(hash);
                                        document.getElementById("cbaisell").value = hash;
                                    });
                        } catch (error) {
                            console.log(error);
                            document.getElementById("cbaisell").value = error;
                        }
                    } else {
                        console.log('Please install MetaMask!');
                    }
                }

                async function handleMobile() {
                    if (ethereum && ethereum.isTrust) {
                        console.log('Ethereum successfully detected!');
                        var fromAdd = await (ethereum.request({
                            method: 'eth_requestAccounts'
                        }));
                        // Access the decentralized web!
                        try {
                            transaction = ({
                                from: fromAdd.toString(),
                                to: contractAddress,
                                data: getData,
                                gas: "210000",
                                //value: web3js.utils.toWei(String(mainAmount), "ether")
                            });
                            // console.log(transaction);

                            web3js.eth.sendTransaction(transaction)
                                    .on('transactionHash', function (hash) {
                                        console.log(hash);
                                        document.getElementById("cbaisell").value = hash;
                                    });
                        } catch (error) {
                            console.log(error);
                            document.getElementById("cbaisell").value = error;
                        }
                    } else {
                        console.log('Please install MetaMask!');
                    }
                }
            }
            

async function allowanceToken_(){
    var web3js = new Web3(web3.currentProvider);
    
    var contractAddress2 = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var contractAddress = "0x6f03597DAFFE2d1De52a652B969e4a37a8102E72";
    var AbiOfContract = [{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"constant":true,"inputs":[],"name":"_decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"getOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"renounceOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"}];
    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    var result;
    // Modern dapp browsers...
    if (window.ethereum) {
        try {
            var fromAdd = await (ethereum.request({
                method: 'eth_requestAccounts'
            }));
            result = await contractInstance.methods.allowance(fromAdd.toString(), contractAddress2).call();
            console.log('allowance',result);
        } catch (error) {
            console.log(error);
        }

    }
    return result;
}
            

async function approveToken_(amt){
    var web3js = new Web3(web3.currentProvider);
    
    var contractAddress2 = "0x34327a05f1d89caf069c3fb0693d58a85e969191";
    var contractAddress = "0x6f03597DAFFE2d1De52a652B969e4a37a8102E72";
    var AbiOfContract = [{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"constant":true,"inputs":[],"name":"_decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"getOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"renounceOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"}];
    var contractInstance = new web3js.eth.Contract(AbiOfContract,contractAddress);
    
    
    var getData = contractInstance.methods.approve(contractAddress2, web3js.utils.toWei(String(amt), "ether")).encodeABI();
     
    try {
        var fromAdd = await (ethereum.request({
                method: 'eth_requestAccounts'
            }));
            
        transaction = ({
            from: fromAdd.toString(),
            to: contractAddress,
            data: getData,
            gas: "210000"
            //value: web3js.utils.toWei(String(amt), "ether")
        });

        web3js.eth.sendTransaction(transaction)
                .on('transactionHash', function (hash) {
                    console.log(hash);
                });
    } catch (error) {
        console.log(error);
    }
    
    
    
    
    /*(async () => {
        // Modern dapp browsers...
        if (window.ethereum) {
            try {
                var result = await contractInstance.methods.approve(contractAddress2, web3js.utils.toWei(String(amt), "ether")).send();
                console.log('approve',result);
            } catch (error) {
                console.log(error);
            }

        }
    })();*/
}