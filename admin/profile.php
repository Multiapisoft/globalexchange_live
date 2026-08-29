<?php
$title = "Profile";
include_once 'header.php';
$aid = $_SESSION['adminid'];
$row = mysqli_fetch_object(my_query("SELECT * FROM admin WHERE recid='" . $aid . "'"));
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="profile_model.php" method="post">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="login_id" class="col-sm-3 col-form-label">Login Id</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="login_id" name="login_id" value="<?php echo $row->login_id ?>" maxlength="20" disabled="disabled">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="password" id="old_password" name="old_password" value="" placeholder="Enter old password" maxlength="20" style="margin-bottom: 20px;">
                            <input class="form-control" type="password" id="password" name="password" value="" placeholder="Enter new password" maxlength="20" style="margin-bottom: 20px;" onchange="form.confirm_password.pattern = this.value;">
                            <input class="form-control" type="password" id="confirm_password" name="confirm_password" value="" placeholder="Enter new password again" maxlength="20">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-3 col-form-label">Name *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="name" name="name" value="<?php echo $row->name ?>" maxlength="50" required="required" pattern="[a-zA-Z ]+">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mobile" class="col-sm-3 col-form-label">Mobile *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="mobile" name="mobile" value="<?php echo $row->mobile ?>" maxlength="10" required="required" pattern="[0-9]{10,10}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">Email *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="email" name="email" type="email" value="<?php echo $row->email ?>" maxlength="50" required="required">
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-left">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>