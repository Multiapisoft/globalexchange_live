<?php
$title = "Fund Transfer to Game";
$_is_dashboard = 1;
include_once 'header.php';
//if(!isset($_SESSION['transaction']) || empty($_SESSION['transaction']) || $_SESSION['transaction']!=$uid){ redirect('./transaction_password.php?url=fund_transfer.php');}
$fund_type_arr = get_fund_type();
?>
<div class=row>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-1">
            <h2><span class="count-number2"><?php echo round($user->wallet*1, 2);?></span><span class="slight"> <?php echo SITE_CURRENCY;?></span></h2>
            <div class="small">Wallet</div>
            <i class="ti-server statistic_icon"></i>
            <div class="sparkline1 text-center"></div>
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-1">
            <h2><span class="count-number2"><?php echo round($user->wallet_admin*1, 2);?></span><span class="slight"> <?php echo SITE_CURRENCY;?></span></h2>
            <div class="small">Game Wallet</div>
            <i class="ti-server statistic_icon"></i>
            <div class="sparkline1 text-center"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="fund_transfer2_model.php" method="post">
                <div class="panel-body">
                    <?php /*<div class="form-group row">
                        <label for="login_id" class="col-sm-3 col-form-label">User Id *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="login_id" name="login_id" maxlength="20" required="required">
                        </div>
                    </div>*/?>
                    <div class="form-group row">
                        <label for="amount" class="col-sm-3 col-form-label">Amount *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="amount" name="amount" maxlength="20" required="required">
                        </div>
                    </div>
                    <?php /*<div class="form-group row">
                        <label for="type" class="col-sm-3 col-form-label">Fund Type *</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="type" id="type" required="required">
                                <option value="1">Reharge Wallet</option>
                                <?php /*foreach ($fund_type_arr as $key => $value){?>
                                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                <?php }*?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="remark" class="col-sm-3 col-form-label">Remark *</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="remark" name="remark" rows="3" required="required" maxlength="250"></textarea>
                        </div>
                    </div>*/?>
                    <?php if(SITE_OTP){?>
                    <div class="form-group row">
                        <label for="otp" class="col-sm-3 col-form-label">Enter OTP *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="otp" name="otp" value="" maxlength="10" required="required"><span id="email_error" name="email_error"></span>
                            <button type="button" class="btn btn-primary pull-right" onclick="otp_email();">Sent OTP</button>
                        </div>
                    </div>
                    <?php }?>
                </div>
                <div class="panel-footer text-left">
                    <button type="submit" type="id" class="btn btn-success">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script>
    function check_sponser(refer_id){
        $.get("../lib/get_availability.php",{'action':'sponsor','refer_id':refer_id},function(data){
            if(data.invalid){
                $("#submit").attr("disabled", "true"); 
                $("#sponser").html("Invalid user id");
            }
            else{
                $("#sponser").html(data.name+" - Valid user id");
                $("#submit").removeAttr("disabled");
            }
        },"json");
    }
    function otp_email(){
        $.get("otp.php",{'type':'transfer'},function(data){
            if(data._status !== 1){
                $("#submit").attr("disabled", "true"); 
                $("#email_error").html(data.msg);
            } 
            else{
                $("#email_error").html(data.msg);
                $("#submit").removeAttr("disabled");
            }
            
        },"json");
    }
</script>