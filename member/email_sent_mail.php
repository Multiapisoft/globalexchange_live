<?php
$title = "Sent Mail";
include_once 'header.php';
$query = "SELECT m.`recid`, m.`sender`, m.`receiver`, LEFT(m.subject,50) as subject, LEFT(m.message,100) as message, m.`filename`, m.`datetime`, m.`read`"
        . ", r.login_id, r.name FROM `message` as m"
        . " LEFT JOIN user as s ON s.uid=m.sender"
        . " LEFT JOIN user as r ON r.uid=m.receiver"
        . " WHERE m.sender='$uid'"
        . " ORDER BY m.datetime DESC";
$result = my_query($query);
$i = 0;
?>

<style>
    /* Dark theme for mailbox sent page */
    .mailbox {
        background-color: #1e2329;
        color: #eaecef;
        border-radius: 12px;
        border: 1px solid #2c3137;
    }

    .mailbox-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #ffffff;
        border-bottom: 1px solid #2c3137;
    }

    .mailbox-body,
    .inbox-mail,
    .mailbox-sideber,
    .mailbox-content {
        background-color: #1e2329;
        color: #eaecef;
    }

    .mailbox-sideber h6,
    .mailbox-sideber .nav li a {
        color: #eaecef;
    }

    .mailbox-sideber .nav li.active a,
    .mailbox-sideber .nav li a:hover {
        background-color: #2b3139;
    }

    .inbox_item {
        background-color: #1e2329;
        border-bottom: 1px solid #2c3137;
        color: #eaecef;
    }

    .inbox_item.unread {
        background-color: #0b0e11;
    }

    .inbox_item:hover {
        background-color: #2b3139;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="mailbox">
            <div class="mailbox-header">
                <div class="row">
                    <div class="col-xs-4">
                        <div class="inbox-avatar"><img src="images/eleFAVICON.png" class="img-circle border-green" alt="">
                            <div class="inbox-avatar-text hidden-xs hidden-sm">
                                <div class="avatar-name"><?php echo $user->login_id;?></div>
                                <div><small>Mailbox</small></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-8">
                        <div class="inbox-toolbar btn-toolbar">
                            <div class="btn-group">
                                <a href="email_compose_mail.php" class="btn btn-success"><span class="fa fa-pencil-square-o"></span></a>
                            </div>
                            <div class="hidden-xs hidden-sm btn-group">
                                <button type="button" class="btn btn-danger"><span class="fa fa-trash"></span></button>
                            </div>
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
                                    <li class="active"><a href="email_sent_mail.php"><i class="fa fa-star-o"></i>Sent mail</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-9 p-0 inbox-mail">
                        <div class="mailbox-content">
                            <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                            <a href="email.php?recid=<?php echo $row->recid;?>" class="inbox_item <?php if($row->read==0){echo ' unread';}?>">
                                <div class="inbox-avatar">
                                    <div class="i-check">
                                        <input tabindex="9" type="checkbox" name="recid[]" value="<?php echo $row->recid;?>">
                                    </div>
                                    <div class="inbox-avatar-text">
                                        <div class="avatar-name"><?php if($row->receiver!=0){echo $row->name;}else{echo 'Admin';}?><span><?php if($row->receiver!=0){echo ' ('.$row->login_id.')';}?></div>
                                        <div><small><span><strong><?php echo $row->subject;?>: </strong><span> <?php echo $row->message;?> ...</span></span></small></div>
                                    </div>
                                    <div class="inbox-date hidden-sm hidden-xs hidden-md">
                                        <div class="date"><?php echo date("h:i A", strtotime($row->datetime));?></div>
                                        <div><small><?php echo date("d M Y", strtotime($row->datetime));?></small></div>
                                    </div>
                                </div>
                            </a>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>