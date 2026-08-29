<?php
$title = "Settings";
include_once 'header.php';


$check1 = my_query("SHOW COLUMNS FROM `admin` LIKE 'bot_liquidity'");
$check2 = my_query("SHOW COLUMNS FROM `admin` LIKE 'bot_profit'");

if (mysqli_num_rows($check1) == 0 || mysqli_num_rows($check2) == 0) {
    $query = "ALTER TABLE `admin` 
              ADD COLUMN `bot_liquidity` INT NULL AFTER `otp`, 
              ADD COLUMN `bot_profit` INT NULL AFTER `bot_liquidity`";
    my_query($query);
}



$aid = $_SESSION['adminid'];
$row = mysqli_fetch_object(my_query("SELECT * FROM admin WHERE recid='" . $aid . "'"));
$status_arr = array('Active', 'Block');
$status_arr2 = array('Block', 'Active');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="settings_model.php" method="post">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="phone" class="col-sm-3 col-form-label">Phone *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="phone" name="phone" value="<?php echo $row->phone ?>" maxlength="10" required="required" pattern="[0-9]{10,10}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tds" class="col-sm-3 col-form-label">TDS *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="tds" name="tds" value="<?php echo $row->tds ?>" maxlength="10" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="service_tax" class="col-sm-3 col-form-label">Service Tax *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="service_tax" name="service_tax" value="<?php echo $row->service_tax ?>" maxlength="10" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="service" class="col-sm-3 col-form-label">Service Charge *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="service" name="service" value="<?php echo $row->service ?>" maxlength="10" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="working_status" class="col-sm-3 col-form-label">Site Working Status *</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="working_status" id="working_status" required="required">
                                <?php foreach ($status_arr as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php if ($key == $row->working_status) {
                                                                            echo "selected='selected'";
                                                                        } ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="otp" class="col-sm-3 col-form-label">Site OTP Status *</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="otp" id="otp" required="required">
                                <?php foreach ($status_arr2 as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php if ($key == $row->otp) {
                                                                            echo "selected='selected'";
                                                                        } ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="bot_liquidity" class="col-sm-3 col-form-label">Total Bot Liquidity*</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="number" id="bot_liquidity" name="bot_liquidity" value="<?php echo $row->bot_liquidity ?>" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bot_profit" class="col-sm-3 col-form-label">Total Bot Profit *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="number" id="bot_profit" name="bot_profit" value="<?php echo $row->bot_profit ?>" required="required">
                        </div>
                    </div>
                    <!--<div class="form-group row">-->
                    <!--    <label for="b_rate" class="col-sm-3 col-form-label">Buy Rate (1 Token = ? USD) *</label>-->
                    <!--    <div class="col-sm-9">-->
                    <!--        <input class="form-control" type="text" id="b_rate" name="b_rate" value="<?php echo $row->b_rate * 1 ?>" maxlength="10">-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="form-group row">-->
                    <!--    <label for="s_rate" class="col-sm-3 col-form-label">Sell Rate (1 Token = ? USD) *</label>-->
                    <!--    <div class="col-sm-9">-->
                    <!--        <input class="form-control" type="text" id="s_rate" name="s_rate" value="<?php echo $row->s_rate * 1 ?>" maxlength="10">-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="form-group row">-->
                    <!--    <label for="s_rate" class="col-sm-3 col-form-label">Coin Rate (1 Token = ? USD) *</label>-->
                    <!--    <div class="col-sm-9">-->
                    <!--        <input class="form-control" type="text" id="coin_rate" name="coin_rate" value="<?php echo $row->coin_rate * 1 ?>" maxlength="10">-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!--<div class="form-group row">-->
                    <!--    <label for="roi" class="col-sm-3 col-form-label">ROI % *</label>-->
                    <!--    <div class="col-sm-9">-->
                    <!--        <input class="form-control" type="text" id="roi" name="roi" value="<?php echo $row->roi * 1 ?>" maxlength="10">-->
                    <!--    </div>-->
                    <!--</div>-->


                </div>
                <div class="panel-footer text-left">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>