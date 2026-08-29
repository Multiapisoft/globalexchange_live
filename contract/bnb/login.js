var is_test = 0;
function SigninPage(){
    if (typeof web3 !== 'undefined') {
        $('#lgambtn').show();
    }
}

async function SignupPage(){
    var amt2 = 20*usd_coin_rate;
    var allowance = await allowanceToken();
    if(allowance >= amt2){
        $("#lgmbtnsup").html('Sign up');
    }
    else{
        $("#lgmbtnsup").html('Approve TBC for Sign up');
    }
}

async function automatically(){
    var waddress = await getAccounts();
    if (typeof waddress !== 'undefined'){
        $('#ethaddress').val(waddress);
        $('#lgmbtn').click();
    }
}

async function SignUp(){
    var refer_id = $('[name="referral"]').val();
    //var amount = parseFloat($('[name="amount"]').val());
    var amount = 20;
    if (refer_id.length === 0) {
        $('.error').html('Enter referral code');
        $('.error').show();
        return false;
    }
    else if(amount<20 || amount>50){
        $('.error').html('Minimum investment is 20 USD and Maximum investment is 50 USD');
        $('.error').show();
        return false;
    }
    else{
        /*var amt2 = amount*usd_coin_rate;
        var allowance = await allowanceToken();
        if(allowance >= amt2){*/
            $.get("lib/get_availability.php",{'action':'sponsor2','refer_id':refer_id},function(data){
                if(data.invalid){
                    $('.error').html('Invalid referral code');
                    $('.error').show();
                    return false;
                }
                else{
                    SignUpProcess(data.name, amount);
                }
            },"json");
        /*}
        else{
            await approveToken(amt2);
        }*/
    }
}

async function SignUpProcess(refer_id, amount){
    if (typeof web3 !== 'undefined') {
        await ethereum.enable();
        web3.eth.getAccounts().then(function(result){
            account = result[0];
            if(refer_id == account){
                $('.error').html('USDT address - '+account+' already exists');
                $('.error').show();
                return false;
            }
            else{
                $.get("lib/get_availability.php",{'action':'login_id','login_id':account},function(data){
                    if(data.invalid){
                        $('.error').html('USDT address - '+account+' already exists');
                        $('.error').show();
                        return false;
                    }
                    else{
                        if(1 || is_test){
                            var refer_id2 = $('[name="referral"]').val();
                            //var position = $('#position').val();
                            var position = '';
                            $.post("register_model.php", {refer_id: refer_id2, login_id: account, amount: amount, position: position}, function(result, status){
                                if (result.Success) {
                                    $('.error').hide();
                                    location.href = "member/dashboard.php";
                                } else {
                                    $('.error').html(result.Message);
                                    $('.error').show();
                                }
                            }, 'json');
                        }
                        else{
                            SignUpPay(account, amount);
                        }
                    }
                },"json");
            }
        });
    }
    else {
        console.log("Metamask not found")
    }
}

async function SignUpPay(account, amt3) {
    var refer_id = $('[name="referral"]').val();
    var position = $('#position').val();
    //amt3 = 1;
    console.log(account);
    //Note: 1BNB = 1000000000000000000
    amt3 = amt3*usd_coin_rate;
    var amt2 = amt3;
    var amt = BigInt(parseFloat(amt3) * 1e18);
    
    var allowance = await allowanceToken();
    console.log(allowance, '====');
    if(allowance >= amt2){
        console.log(allowance, '====1');
    }
    else{
        await approveToken(amt2);
    }
    
    //console.log(web3.utils.toWei(amt3.toString(), 'ether'));
    //await contractInstance.methods.transferGas(web3.utils.toWei(amt3.toString(), 'ether')).send({from: account, value: web3.utils.toWei(amt3.toString(), 'ether')})
    
    var getData = contractInstance.methods.transferGas(String(amt)).encodeABI();
    
    var transaction = ({
        from: account.toString(),
        data: getData,
        to: contractAddress,
        //value: web3.utils.toWei(String(amt2), "ether"),
        //value: 0,
        gas: "510000"
    });

    await web3.eth.sendTransaction(transaction)
    .on('transactionHash', function (hash) {
        console.log(hash);
        $('.error').html('Pending : Transaction waiting for comfirmation');
        $('.error').show();
        $.post("hash_model.php", {refer_id: refer_id, account: account, hash: hash, amount: amt2, position: position}, function(data){
            console.log(data);
        });
    })
    .on('receipt', function (receipt) {
        //receipt example
        console.log(receipt);
        if (receipt.status) {
            receipt.refer_id = refer_id;
            receipt.login_id = account;
            receipt.amount = amt2;
            receipt.position = position;
            alert("success");
            $.post("register_model.php", receipt, function(result, status){
                if (result.Success) {
                    $('.error').hide();
                    location.href = "member/dashboard.php";
                } else {
                    $('.error').html(result.Message);
                    $('.error').show();
                }
            }, 'json');
        }
        else {
            $('.error').html('Transaction Failed');
            $('.error').show();
        }
    })
    .on('error', console.error);
}

async function buy_automatically(address, plan_id, refer_id){
    var amt = parseFloat($('#invest_amount').val());
    var waddress = await getAccounts();
    
    if(amt<0.025 || amt>52){
        $('.error').html('Minimum investment is 0.025 ETH and Maximum investment is 52 ETH');
        $('.error').show();
        return false;
    }
    else if (typeof waddress !== 'undefined' && waddress == address){
        if(is_test){
            $.post("../buy_model.php", {address: address, plan_id: plan_id}, function(result, status){
                if (result.Success) {
                    $('.error').hide();
                    location.href = "dashboard.php";
                } else {
                    $('.error').html(result.Message);
                    $('.error').show();
                }
            }, 'json');
        }
        else{
            BuyPay(address, amt, plan_id, refer_id);
        }
    }
    else{
        alert('Authorization error');
    }
}

async function BuyPay(account, amt2, plan_id, refer_id) {
    //Note: 1ETH = 1000000000000000000
    var amt = amt2*1000000000000000000;
    var amt2 = amt2;
    await ethersprime.methods.transferEth(amt).send({from: account, value: web3.utils.toWei(amt2.toString(), 'ether')})
    .on('transactionHash', function (hash) {
        $("#buy_btn").attr("disabled", "true");
        console.log(hash);
        $('.error').html('Pending : Transaction waiting for comfirmation');
        $('.error').show();
        $.post("../hash_model.php", {refer_id: refer_id, account: account, hash: hash, amount: amt2, plan_id : plan_id}, function(data){
            console.log(data);
        });
    })
    .on('receipt', function (receipt) {
        //receipt example
        console.log(receipt);
        if (receipt.status) {
            receipt.refer_id = refer_id;
            receipt.login_id = account;
            receipt.address = address;
            receipt.plan_id = plan_id;
            receipt.amount = amt2;
            //alert("success");
            $.post("../buy_model.php", receipt, function(result, status){
                if (result.Success) {
                    $('.error').hide();
                    location.href = "dashboard.php";
                } else {
                    $('.error').html(result.Message);
                    $('.error').show();
                }
            }, 'json');
        }
        else {
            $('.error').html('Transaction Failed');
            $('.error').show();
        }
    })
    .on('error', console.error);
}

async function deposit_(uaddress, amount, plan_id, refer_id, rate) {
    $('.buy_btn').prop('disabled', true);
    var amt = parseFloat($('#damount').val());
    //var amt = parseFloat(amount);
    if (typeof web3 !== 'undefined') {
        await ethereum.enable();
        web3.eth.getAccounts().then(async function (result) {
            var address = result[0];
            if((isNaN(amt) || amt<1 || amt>500000)){
                alert('Invest min 1 and max 500000 multiple of 1 TBC.');
                location.reload();
            }
            else if(address != uaddress){
                alert('Authorization error');
            }
            else if (typeof address !== 'undefined') {
                var amt2 = amt;
                var allowance = await allowanceTokenI();
                if(allowance >= amt2){
                    console.log('DepositPay_');
                    DepositPay_(amt, address, plan_id, refer_id, rate);
                }
                else{
                    console.log('approveTokenI');
                    await approveTokenI(amt2);
                }
            }
        });
    } else {
        alert('Authorization error');
    }
}

async function DepositPay_(amt2, account, plan_id, refer_id, rate) {
    //Note: 1BNB = 1000000000000000000
    amt = BigInt(parseFloat(amt2) * 1e18);
    
    var allowance = await allowanceTokenI();
    console.log(allowance, '====');
    if(allowance >= amt2){
        console.log(allowance, '====1');
    }
    else{
        await approveTokenI(amt2);
    }
    
    var getData = contractInstance.methods.transferGas(String(amt)).encodeABI();
    
    var transaction = ({
        from: account.toString(),
        data: getData,
        to: contractAddress,
        //value: web3.utils.toWei(String(amt2), "ether"),
        //value: 0,
        gas: "510000"
    });

    await web3.eth.sendTransaction(transaction)
    .on('transactionHash', function (hash) {
        console.log(hash);
        $('.error').html('Pending : Transaction waiting for comfirmation');
        $('.error').show();
        $('#loadingOverlay').show();
        $.post("../dhash_model.php", {refer_id: refer_id, account: account, hash: hash, amount: amt2, plan_id: plan_id, rate: rate}, function (data) {
            console.log(data);
            $('.loading').show();
            setTimeout(()=>{location.reload();}, 30000);
        });
    })
    .on('receipt', function (receipt) {
        //receipt example
        console.log(receipt);
        if (receipt.status) {
            receipt.refer_id = refer_id;
            receipt.login_id = account;
            receipt.address = account;
            receipt.plan_id = plan_id;
            receipt.amount = amt2;
            receipt.rate = rate;
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

async function invest_(uaddress, amount, plan_id, refer_id, rate) {
    $('.invbtn').prop('disabled', true);
    //var amt = parseFloat($('#i_id_' + plan_id).val());
    var amt = parseFloat(amount);
    if (typeof web3 !== 'undefined') {
        await ethereum.enable();
        web3.eth.getAccounts().then(async function (result) {
            var address = result[0];
            /*if ((plan_id == 1) && (isNaN(amt) || amt < 500 || amt > 500)) {
                alert('Minimum investment is 100 TBC and Maximum investment is 12500 TBC');
                location.reload();
            } else if (plan_id == 2 && (isNaN(amt) || amt < 12501 || amt > 25000)) {
                alert('Minimum investment is 12501 TBC and Maximum investment is 25000 TBC');
                location.reload();
            } else if (plan_id == 3 && (isNaN(amt) || amt < 25001 || amt > 5000000)) {
                alert('Minimum investment is 25001 TBC and Maximum investment is 5000000 TBC');
                location.reload();
            } else if (plan_id == 4 && (isNaN(amt) || amt < 0.05 || amt > 5000000)) {
                alert('Minimum deposit is 0.05 TBC and Maximum deposit is 5000000 TBC');
                location.reload();
            } else*/ if((isNaN(amt) || amt<500 || amt>100000)){
                alert('Invest min 500 and max 100000 multiple of 1 TBC.');
                location.reload();
            }
            else if(!is_test && address != uaddress){
                alert('Authorization error');
            }
            else if (typeof address !== 'undefined') {
                if (is_test) {
                    $.post("../buy_model.php", {amount: amt, address: address, plan_id: plan_id, refer_id: refer_id, rate: rate}, function (result, status) {
                        if (result.Success) {
                            location.reload();
                        } else {
                            alert(result.Message);
                            location.reload();
                        }
                    }, 'json');
                } else {
                    var amt2 = amt;
                    var allowance = await allowanceTokenI();
                    if(allowance >= amt2){
                        investBuyPay_(amt, address, plan_id, refer_id, rate);
                    }
                    else{
                        await approveTokenI(amt2);
                    }
                }
            }
        });
    } else {
        alert('Authorization error');
    }
}

async function investBuyPay_(amt2, account, plan_id, refer_id, rate) {
    //Note: 1BNB = 1000000000000000000
    amt = BigInt(parseFloat(amt2) * 1e18);
    
    var allowance = await allowanceTokenI();
    console.log(allowance, '====');
    if(allowance >= amt2){
        console.log(allowance, '====1');
    }
    else{
        await approveTokenI(amt2);
    }
    
    var getData = contractInstance.methods.transferGas(String(amt)).encodeABI();
    
    var transaction = ({
        from: account.toString(),
        data: getData,
        to: contractAddress,
        //value: web3.utils.toWei(String(amt2), "ether"),
        //value: 0,
        gas: "510000"
    });

    await web3.eth.sendTransaction(transaction)
    .on('transactionHash', function (hash) {
        console.log(hash);
        $('.error').html('Pending : Transaction waiting for comfirmation');
        $('.error').show();
        $('#loadingOverlay').show();
        $.post("../hash_model.php", {refer_id: refer_id, account: account, hash: hash, amount: amt2, plan_id: plan_id, rate: rate}, function (data) {
            console.log(data);
            $('.loading').show();
            setTimeout(()=>{location.reload();}, 30000);
        });
    })
    .on('receipt', function (receipt) {
        //receipt example
        console.log(receipt);
        if (receipt.status) {
            receipt.refer_id = refer_id;
            receipt.login_id = account;
            receipt.address = account;
            receipt.plan_id = plan_id;
            receipt.amount = amt2;
            receipt.rate = rate;
            //alert("success");
            $.post("../buy_model.php", receipt, function(result, status){
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

async function invest(uaddress, amount, plan_id, refer_id, rate){
    $('#buy_btn').prop('disabled', true);
    //var amt = parseFloat($('#i_id_'+plan_id).val());
    //var amt = parseFloat($('#invest_amount').val());
    var amt = parseFloat(amount);
    if (typeof web3 !== 'undefined') {
        await ethereum.enable();
        web3.eth.getAccounts().then(function (result) {
            var address = result[0];
        /*if((plan_id == 1 || plan_id == 2) && (isNaN(amt) || amt<100 || amt>1000000)){
            alert('Minimum investment is 100 TRX and Maximum investment is 1000000 TRX');
            location.reload();
        }
        else if(plan_id == 3 && (isNaN(amt) || amt<10000 || amt>1000000)){
            alert('Minimum token buy value is 10000 SPT and Maximum token buy value is 1000000 SPT');
            location.reload();
        }*/
        if((isNaN(amt) || amt<500 || amt>100000)){
            alert('Invest min 500 and max 100000 multiple of 1 TBC.');
            location.reload();
        }
        else if(!is_test && address != uaddress){
            alert('Authorization error');
        }
        else if (typeof address !== 'undefined'){
            if(is_test){
                $.post("../buy_model.php", {amount: amt, address: address, plan_id: plan_id, refer_id: refer_id, rate: rate}, function(result, status){
                    if (result.Success) {
                        location.reload();
                    } else {
                        alert(result.Message);
                        location.reload();
                    }
                }, 'json');
            }
            else{
                investBuyPay(amt, address, plan_id, refer_id, rate);
            }
        }
        });
    }
    else{
        alert('Authorization error');
    }
}

async function investBuyPay(amt2, account, plan_id, refer_id, rate) {
    //Note: 1TRX = 1000000
    amt = parseFloat(amt2)*parseFloat(rate)*1000000;
    
    await tronContract.transferGas(1000).send({
        callValue: parseFloat(amt) + (10*1000000),
        //callValue: amt,
        shouldPollResponse: false
    }).then(function (hash) {
        console.log(hash);
        $('.error').html('Pending : Transaction waiting for comfirmation');
        $('.error').show();
        $('#loadingOverlay').show();
        $.post("../hash_model.php", {refer_id: refer_id, account: account, hash: hash, amount: amt2, plan_id : plan_id, rate: rate}, function(data){
            console.log(data);
            var txid = hash;
            var int_id = setInterval(() => {
                getTxDetailBuy({refer_id: refer_id, login_id: account, transactionHash: hash, amount: amt2, plan_id : plan_id, rate: rate}, txid, int_id);
            }, 1000);
        });
        return true;
    }, function (err) {
        console.log(err);
        return false;
    });
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

async function allowanceToken(){
    const accounts = await web3.eth.getAccounts();
    var result = 0;
    // Modern dapp browsers...
    try {
        result = await tokenInstance.methods.allowance(accounts[0].toString(), contractAddress).call();
        console.log('allowance',result*1e-18);
    } catch (error) {
        console.log(error);
    }
    return result*1e-18;
}

async function approveToken(amt){
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
            $('.error').html('Pending : Transaction waiting for comfirmation');
            $('.error').show();
            $('#loadingOverlay').show();
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
                $('.error').html('Transaction Failed');
                $('.error').show();
                alert('Approve failed');
                location.reload();
            }
        });
    } catch (error) {
        console.log(error);
    }
}

async function checkAllow(){
    //if(!is_test){
        var amt2 = parseFloat($('#damount').val());
        var allowance = await allowanceTokenI();
        if(allowance >= amt2){
            $("#buy_btn").html('Deposit Now');
        }
        else{
            $("#buy_btn").html('Approve TBC for Deposit');
        }
    //}
}