<?php
$title = "Edit Package";
$recid = isset($_GET['recid']) ? (int) $_GET['recid'] : 0;
include '../lib/config.php';
$check = my_query("SHOW COLUMNS FROM `investments_plan` LIKE 'action'");

if (mysqli_num_rows($check) == 0) {
    $query = "ALTER TABLE `investments_plan` ADD `action` TINYINT NOT NULL DEFAULT '1' COMMENT '1. active\r\n,0. Deactive' AFTER `status';";
    my_query($query);
}
$row = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . $recid . "' AND status IN (0)"));
include_once 'header.php';
if (!$row) {
    redirect('./cms_package.php');
    die();
}
$status_arr = array('Inactive', 'Active', 'Comming Soon');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="cms_package_edit_model.php" method="post">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="title" class="col-sm-3 col-form-label">Title *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="title" name="title" value="<?php echo $row->title; ?>" maxlength="100" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="amount_from" class="col-sm-3 col-form-label">Amount From *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="amount_from" name="amount_from" value="<?php echo $row->amount_from * 1; ?>" maxlength="20" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="amount_to" class="col-sm-3 col-form-label">Amount To *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="amount_to" name="amount_to" value="<?php echo $row->amount_to * 1; ?>" maxlength="20" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="percentage" class="col-sm-3 col-form-label">Monthly ROI % *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="percentage" name="percentage" value="<?php echo $row->percentage * 1; ?>" maxlength="10" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="percentage_to" class="col-sm-3 col-form-label">Monthly ROI % To *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="percentage_to" name="percentage_to" value="<?php echo $row->percentage_to * 1; ?>" maxlength="10" required="required">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="line1" class="col-sm-3 col-form-label">Line 1</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="line1" name="line1" value="<?php echo htmlspecialchars($row->line1); ?>" maxlength="255">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="line2" class="col-sm-3 col-form-label">Line 2</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="line2" name="line2" value="<?php echo htmlspecialchars($row->line2); ?>" maxlength="255">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-3 col-form-label">Status *</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="status" id="status" required="required">
                                <?php foreach ($status_arr as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php if ($key == $row->action) {
                                                                            echo "selected='selected'";
                                                                        } ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-left">
                    <input type="hidden" name="recid" value="<?php echo $row->recid; ?>" />
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
