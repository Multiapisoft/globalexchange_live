<?php
$title = "Fund Deduct";
include_once 'header.php';
$fund_type_arr = get_fund_type();
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="fund_deduct_model.php" method="post">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="login_id" class="col-sm-3 col-form-label">User Id *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="login_id" name="login_id" maxlength="100" required="required">
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
                    <button type="submit" class="btn btn-success">Deduct</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>