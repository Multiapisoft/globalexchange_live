<?php
$title = "Email";
include_once 'header.php';
if (!isset($_GET['recid'])) {
    redirect('./email_inbox.php');
    die();
}
$recid = $_GET['recid'];
$query = "SELECT m.`recid`, m.`sender`, m.`receiver`, m.`subject`, m.`message`, m.`filename`, m.`datetime`, m.`read`"
        . ", s.login_id, s.name, r.login_id as r_login_id, r.name as r_name FROM `message` as m"
        . " LEFT JOIN user as s ON s.uid=m.sender"
        . " LEFT JOIN user as r ON r.uid=m.receiver"
        . " WHERE m.recid='" . $recid . "'";
$result = my_query($query);
if (mysqli_num_rows($result) != 1) {
    redirect('./email_inbox.php');
    die();
} else {
    $row = mysqli_fetch_object($result);
    if ($row->receiver == 0) {
        my_query("UPDATE `message` SET `read`=1 WHERE `recid`='" . $recid . "'");
    }
}
?>
<div class="row">
    <div class="col-sm-12">
        <div class="mailbox">
            <div class="mailbox-header">
                <div class="row">
                    <div class="col-xs-4">
                        <div class="inbox-avatar"><img src="assets/dist/img/user2-160x160.png" class="img-circle border-green" alt="">
                            <div class="inbox-avatar-text hidden-xs hidden-sm">
                                <div class="avatar-name">Admin</div>
                                <div><small>Mailbox</small></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-8">
                        <div class="inbox-toolbar btn-toolbar">
                            <div class="btn-group">
                                <a href="email_inbox.php" class="btn btn-default"><span class="fa fa-long-arrow-left"></span></a>
                            </div>
                            <div class="btn-group">
                                <a href="email_compose_mail.php" class="btn btn-success"><span class="fa fa-pencil-square-o"></span></a>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default" onclick="myFunction()"><span class="fa fa-print"></span></button>
                            </div>
                            <!--<div class="hidden-xs hidden-sm btn-group">-->
                            <!--    <button type="button" class="btn btn-danger"><span class="fa fa-trash"></span></button>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="mailbox-body">
                <div class="row m-0">
                    <div class="col-sm-3 p-0 inbox-nav hidden-xs hidden-sm">
                        <div class="mailbox-sideber">
                            <div class="profile-usermenu">
                                <h6>Mailbox</h6>
                                <ul class="nav">
                                    <li><a href="email_inbox.php"><i class="fa fa-inbox"></i>Inbox</a></li>
                                    <li><a href="email_compose_mail.php"><i class="fa fa-envelope-o"></i>Send Mail</a></li>
                                    <li><a href="email_sent_mail.php"><i class="fa fa-star-o"></i>Sent mail</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-9 p-0 inbox-mail">
                        <div class="inbox-avatar p-20 border-btm">
                            <img src="assets/dist/img/avatar5.png" class="border-green hidden-xs hidden-sm" alt="">
                            <div class="inbox-avatar-text">
                                <div class="avatar-name"><strong><?php if($row->sender!=0){echo "From";}else{echo 'To';}?>: </strong>
                                    <?php /*if($row->sender!=0){echo $row->name;}elseif($row->receiver!=0){echo $row->r_name;} echo ' - ';*/?><em><?php if($row->sender!=0){echo $row->login_id;}elseif($row->receiver!=0){echo $row->r_login_id;}?></em>
                                </div>
                                <div><small><strong>Subject: </strong> <?php echo $row->subject;?></small></div>
                            </div>
                            <div class="inbox-date text-right hidden-xs hidden-sm">
                                <div><small><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></small></div>
                            </div>
                        </div>
                        <div class="inbox-mail-details p-20">
                            <?php echo $row->message;?>
                            <hr>
                            <?php if(file_exists("../member/uploads/attachment/".$row->filename) && $row->filename!=''){?>
                            <h4> <i class="fa fa-paperclip"></i> Attachments </h4>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <a href="../uploads/attachment/<?php echo $row->filename;?>" target="_blank">&nbsp;<?php echo $row->filename;?></a>
                                </div>
                            </div>
                            <?php }?>
                            <?php if($row->sender!=0){?>
                            <div class="m-t-20 border-all p-20">
                                <p class="p-b-20">click here to <a href="email_compose_mail.php?uid=<?php echo $row->sender;?>">Reply</a></p>
                            </div> 
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>