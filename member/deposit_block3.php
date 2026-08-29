<?php
$title = 'Deposit SAI Token';
include_once 'header.php';
$min = 1;
$max = 500000;
?>
<style>
    .loader-overlay {
        -ms-opacity: 0.9;
        background: #444;
        display: block;
        height: 100%;
        left: 0;
        opacity: 0.9;
        position: fixed;
        top: 0;
        vertical-align: middle;
        width: 100%;
        z-index: 100000;
    }

    .loader-content {
        margin-left: auto;
        margin-top: auto;
        width: 50%;
    }

    .loader-center {
        -moz-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        -o-transform: translate(-50%, -50%);
        -webkit-transform: translate(-50%, -50%);   
        left: 50%;
        display: block;
        position: fixed;
        top: 50%;
        transform: translate(-50%, -55%);
    }

    .loader-text {
        color: #FFF;
        font-size: 18px;
        height: 50%;
    }
    .wrap-login100 {
        background: #062D54;
    }
</style>
<div id="loadingOverlay" class="loader-overlay" style="display:none;">
    <div class="loader-content loader-center">
        <img src="../extra/img/loader.gif" class="loader-center" alt=""/>
        <div class="loader-center loader-text">Transaction pending waiting for comfirmation</div>
    </div>
</div>
<h2><span class="count-number2"><?php echo round($user->wallet_topup * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY;?></span></h2>



<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <?php if($min != $max){?>
                <div class="form-group">
                    <div for="amount">You Deposit <?php echo SITE_CURRENCY_TKN; ?> * </div>
                    <input class="form-control" type="text" id="damount" name="amount" maxlength="20" required="required">
                </div>
                <?php /*<div class="form-group">
                    <div for="amount">You Get <?php echo SITE_CURRENCY; ?> <?php /*<span style="float: right;">Balance: <?php echo round($user->wallet_topup * 1, 2); ?></span>*?></div>
                    <input class="form-control" type="text" id="tcoin" name="tcoin" maxlength="20" readonly="readonly">
                </div>*/?>
                <?php /*<div class="form-group dprice">
                    <div for="amount">Price <span style="float: right;"><?php echo SITE_COIN_RATE*1; ?> BUSD per <?php echo SITE_CURRENCY; ?></span></div>
                </div>*/?>
                <?php }?>
            </div>
            <div class="panel-footer text-left">
                <input type="hidden" name="uid" value="<?php echo $_SESSION['userid'];?>" />
                <?php if($min == $max){?>
                <input type="hidden" name="amount" id="iamount" value="<?php echo $min;?>" />
                <?php }?>
                <input type="hidden" name="min" value="<?php echo $min;?>" />
                <input type="hidden" name="max" value="<?php echo $max;?>" />
                <button id="buy_btn" type="button" class="btn btn-success" onclick="deposit_();">Deposit <?php echo SITE_CURRENCY_TKN; ?> Now</button>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
<script type="text/javascript" src="../contract/bnb/indexd.js"></script>
<script>
    $("#damount").keyup(function(){
        //checkAllow();
    });
    
    $("#damount").keyup(function(){
        //checkAllow();
        // var amt = $('#damount').val();
        // var tcoin = amt/<?php echo TKN_RATE_USD;?>;
        // $('#tcoin').val(parseFloat(tcoin).toFixed(2));
    });
    /*$(window).load(function() {
        getTokenBalance();
    });*/
</script>