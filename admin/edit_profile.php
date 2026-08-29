<?php
$title = "Edit Profile";
include_once 'header.php';
if (!isset($_GET['uid'])) {
    redirect('./users.php');
    die();
}
$uid = $_GET['uid'];

$query = "SELECT u.*, r.login_id as sponsor_login_id, r.name as sponsor_name FROM user as u"
        . " LEFT JOIN user as r ON r.uid=u.refer_id WHERE u.uid='" . $uid . "'";
$row = mysqli_fetch_object(my_query($query));
$user_type_arr = get_user_type();
$user_status_arr = array('Active', 'Block');
$user_royalty_arr = array(0 => '0%', 1 => '1%', 2 => '2%');
$locarr = array(
    'city' => array(
        'a' => 1,
        'ml' => 50,
        'rq' => 0,
    ),
    'state' => array(
        'a' => 1,
        'ml' => 50,
        'rq' => 0,
    )
);
$otherarr = 
$acarr = array(
    'bitcoin' => array(
        'a' => 1,
        'ml' => 100,
        'rq' => 0,
        'nm' => 'USDT.BEP20 Address',
    )
);
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <form class="form-horizontal" action="edit_profile_model.php" method="post">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="sponsor_login_id" class="col-sm-3 col-form-label">Sponsor Id</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="sponsor_login_id" name="sponsor_login_id" value="<?php echo $row->sponsor_login_id?>" disabled="disabled" maxlength="100">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="datetime" class="col-sm-3 col-form-label">DOJ</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="datetime" name="datetime" value="<?php echo date("d M, Y h:i A", strtotime($row->datetime));?>" disabled="disabled">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="login_id" class="col-sm-3 col-form-label">User Id *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="login_id" name="login_id" value="<?php echo $row->login_id?>" maxlength="100" required="required" pattern="\w{6,100}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="password" id="password" name="password" value="" placeholder="Enter password" maxlength="20" style="margin-bottom: 20px;" onchange="form.confirm_password.pattern = this.value;">
                            <input class="form-control" type="password" id="confirm_password" name="confirm_password" value="" placeholder="Enter password again" maxlength="20">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="working_status" class="col-sm-3 col-form-label">User Type *</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="type" id="type" required="required">
                                <?php foreach ($user_type_arr as $key => $value){?>
                                <option value="<?php echo $key;?>" <?php if($key==$row->type){echo "selected='selected'";}?>><?php echo $value;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="working_status" class="col-sm-3 col-form-label">User Status *</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="status" id="status" required="required">
                                <?php foreach ($user_status_arr as $key => $value){?>
                                <option value="<?php echo $key;?>" <?php if($key==$row->status){echo "selected='selected'";}?>><?php echo $value;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-3 col-form-label">Name *</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="name" name="name" value="<?php echo $row->name?>" maxlength="50" required="required" pattern="[a-zA-Z ]+">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gender" class="col-sm-3 col-form-label">Gender *</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="gender" name="gender" required="required">
                                <option value="" disabled="disabled" selected="selected">-- Select Gender --</option>
                                <option value="Male" <?php if($row->gender == "Male"){echo "selected='selected'";}?>>Male</option>
                                <option value="Female" <?php if($row->gender == "Female"){echo "selected='selected'";}?>>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-3 col-form-label">Email </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="email" id="email" name="email" value="<?php echo $row->email?>" maxlength="50" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mobile" class="col-sm-3 col-form-label">Mobile </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="mobile" name="mobile" value="<?php echo $row->mobile?>" maxlength="10" pattern="[0-9]{10,10}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="country" class="col-sm-3 col-form-label">Country *</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="country" name="country" required="required">
                                <?php 
                                $result2 = my_query("SELECT country_id, short_name FROM country");
                                while ($row2 = my_fetch_object($result2)){
                                ?>
                                <option value="<?php echo $row2->country_id;?>" <?php if($row2->country_id==$row->country){echo "selected='selected'";}?>><?php echo $row2->short_name;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <?php foreach ($otherarr as $key => $value) {

                        if($value['a']){
                    ?>
                    <div class="form-group row">
                        <label for="<?php echo $key;?>" class="col-sm-3 col-form-label"><?php echo ucwords(str_replace('_', ' ', (isset($value['nm']) && $value['nm']) ? $value['nm'] : $key));?> <?php echo isset($value['rq']) && $value['rq'] ? '*' : '';?></label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="<?php echo $key;?>" name="<?php echo $key;?>" value="<?php echo $row->$key?>" maxlength="<?php echo isset($value['ml']) && $value['ml'] ? $value['ml'] : 100;?>" <?php echo isset($value['rq']) && $value['rq'] ? ' required="required"' : '';?>>
                        </div>
                    </div>
                    <?php }}?>
                </div>
                <div class="panel-footer text-left">
                    <input type="hidden" name="uid" value="<?php echo $row->uid?>" />
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>