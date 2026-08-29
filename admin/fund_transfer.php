<?php


$title = "Fund Transfer";
include_once 'header.php';

// Define fund types if function doesn't exist
if (!function_exists('get_fund_type')) {
    function get_fund_type() {
        return array(
            1 => 'Topup Wallet',
            2 => 'Promo Wallet',
            3 => 'Withdrawal Wallet'
        );
    }
}
$fund_type_arr = get_fund_type();
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="fund_transfer_model.php" method="post">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="login_id" class="col-sm-3 col-form-label">User Id *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="login_id" name="login_id" maxlength="100" required="required"  placeholder="Enter Login ID"  onBlur="check_sponser(this.value);">
                            <div id="sponser" style="margin-top: 5px; font-weight: bold; color: #28a745;"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="amount" class="col-sm-3 col-form-label">Amount *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="amount" name="amount" maxlength="20" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="type" class="col-sm-3 col-form-label">Fund Type *</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="type" id="type" required="required">
                                <?php foreach ($fund_type_arr as $key => $value){?>
                                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="remark" class="col-sm-3 col-form-label">Remark *</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="remark" name="remark" rows="3" required="required" maxlength="250"></textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-left">
                    <button type="submit" class="btn btn-success">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById("login_id").addEventListener('input',(e)=>{
        let data = get_user_details(e.target.value);
        console.log(data)
    })



     function check_sponser(refer_id) {
        if (!refer_id) {
            $("#sponser").html("Please enter a receiver's user ID").css("color", "blue");
            return;
        }

        $.get("../lib/get_availability.php", {
                action: 'sponsor',
                refer_id: refer_id
            }, function(data) {
                if (data.invalid) {
                    $("#sponser").html("Invalid Receiver's user ID.").css("color", "red");
                } else {
                    $("#sponser").html(`${data.name} - Valid Receiver's user ID.`).css("color", "green");

                }
            }, "json")
            .fail(function() {
                $("#sponser").html("Error validating Receiver's user ID.");
            });
    };
</script>


<?php include_once 'footer.php'; ?>