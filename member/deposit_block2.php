<?php $type = (isset($_GET['type']) && in_array($_GET['type'], array(1,2,3,4,5,6,7,8,12))) ? $_GET['type'] : 1;
$typearr = array(1 => 'USDT', 2 => 'TRX', 3 => 'USDT', 4 => 'ETH', 5 => 'BCH', 6 => 'Dash', 7 => 'XRP', 8 => 'NEO', 12 => 'TRX');
$alt_color = array(1 => '#605CA8', 2 => '#0073B7', 3 => '#F39C12', 4 => '#605CA8', 5 => '#0073B7', 6 => '#F39C12', 7 => '#262D4E', 8 => '#FF851B', 12 => '#FF851B');
$type2 = $typearr[$type];
$title = "Add Fund by ".$type2."";
$_is_dashboard = 1;
include_once 'header.php';
include '../lib/coinpayments.php';
$_address = '';
$user = get_user_details($uid);
$ac = '0x0ef2c4f87cfe8ab156872b908e8be285cd366e33';
$ifsc = 'SBIN0000001';
$bank = 'SBI';
$upi = '8447736505@ybl';
?>
<div class=row>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
        <div class="statistic-box statistic-filled-1">
            <h2><span class="count-number"><?php echo round($user->wallet_topup*1, 0);?></span><span class="slight"> <?php echo SITE_CURRENCY;?></span></h2>
            <div class="small">Topup Wallet</div>
            <i class="ti-server statistic_icon"></i>
            <div class="sparkline1 text-center"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <?php /*<div class="row">
                <div class="col-sm-12">
                    <p>&nbsp;</p>
                    <div class="well" style="height: auto;margin: 20px;">
                        <div class="row">
                            <div class="col-md-12">Bank: <span><?php echo $bank;?></span></div>
                        </div>
                    </div>
                    <div class="well" style="height: auto;margin: 20px;">
                        <div class="row">
                            <div class="col-md-9">A/C: <span id="_address"><?php echo $ac;?></span></div>
                            <div class="col-md-3"><a href="javascript:void(0);" onclick="CopyToClipboard2('_address');" class="btn btn-violet pull-right" id="_address_copy">Copy</a></div>
                        </div>
                    </div>
                    <div class="well" style="height: auto;margin: 20px;">
                        <div class="row">
                            <div class="col-md-9">IFSC: <span id="_ifsc"><?php echo $ifsc;?></span></div>
                            <div class="col-md-3"><a href="javascript:void(0);" onclick="CopyToClipboard2('_ifsc');" class="btn btn-violet pull-right" id="_ifsc_copy">Copy</a></div>
                        </div>
                    </div>
                    <p style="text-align: center; margin-bottom: 20px; margin-top: 20px;"><img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=<?php echo $upi;?>" /></p>
                    <?php /*<p style="text-align: center; margin-bottom: 20px; margin-top: 20px;"><img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=<?php echo $_address;?>" /></p>*?>
                </div>
            </div>*/?>
            <div class="row">
                <div class="col-sm-12">
                    <p>&nbsp;</p>
                    <div class="well" style="height: auto;margin: 20px;">
                        <div class="row">
                            <div class="col-md-9">Address: <span id="_address"><?php echo $ac;?></span></div>
                            <div class="col-md-3"><a href="javascript:void(0);" onclick="CopyToClipboard2('_address');" class="btn btn-violet pull-right" id="_address_copy">Copy</a></div>
                        </div>
                    </div>
                    <p style="text-align: center; margin-bottom: 20px; margin-top: 20px;"><img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=<?php echo $ac;?>" /></p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal" method="post" style="margin: 20px;" action="deposit_block2_model.php">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="amount">Amount *</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="amount" value="" maxlength="20" required="required" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="amount">Tx ID / Ref. Number *</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="hash" value="" maxlength="100" required="required" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info col-sm-offset-2">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php /*<div class="col-sm-6">
        <?php if($type == 2){?>
        <div class="panel panel-bd">
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal" method="post" style="margin: 20px;">
                        <h2>Calculate TRX</h2>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="amount">Amount (<?php echo SITE_CURRENCY_S; ?>)*</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="invest_amount" name="amount" value="" maxlength="5" required="required" />
                                <span id="calculator_msg"></span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-info col-sm-offset-2" id="calculate_btc">Calculate</button>
                    </form>
                </div>
            </div>
        </div>
        <?php }?>
    </div>*/?>
</div>
<?php include_once 'footer.php'; ?>
<script>
function CopyToClipboard2(containerid) {
    if (window.getSelection) {
        if (window.getSelection().empty) {  // Chrome
            window.getSelection().empty();
        } else if (window.getSelection().removeAllRanges) {  // Firefox
            window.getSelection().removeAllRanges();
        }
    } else if (document.selection) {  // IE?
        document.selection.empty();
    }
    
    if (document.selection) { 
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select().createTextRange();
        document.execCommand("Copy"); 
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().addRange(range);
        document.execCommand("Copy");
        $('#'+containerid+'_copy').text('Copied');
        $('#'+containerid+'_copy').addClass('btn-success');
        $('#'+containerid+'_copy').removeClass('btn-violet');
    }
}

$( "#_address_copy, #_ifsc_copy" ).mouseleave(function() {
    $(this).text('Copy');
    $(this).addClass('btn-violet');
    $(this).removeClass('btn-success');
});
</script>
<script>
    $("#calculate_btc").click(function(){
        var amt = $('#invest_amount').val();
        $.post('calculate_btc.php', {amount: amt}, function(result){
            $('#calculator_msg').html(result);
        });
    });
</script>