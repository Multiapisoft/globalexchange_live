<?php
$title = "Compose Mail";
include_once 'header.php';
?>
<div class="row">
    <div class="col-sm-12">
        <div class="mailbox">
            <div class="mailbox-header">
                <div class="row">
                    <div class="col-xs-4">
                        <div class="inbox-avatar"><img src="../assets/dist/img/user.png" class="img-circle border-green" alt="">
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
                                <a href="" class="hidden-xs hidden-sm btn btn-default"><span class="fa fa-reply-all"></span></a>
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
                                    <li class="active"><a href="email_compose_mail.php"><i class="fa fa-envelope-o"></i>Send Mail</a></li>
                                    <li><a href="email_sent_mail.php"><i class="fa fa-star-o"></i>Sent mail</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-9 p-0 inbox-mail p-20">
                        <form class="form-horizontal" action="email_compose_mail_model.php" method="post">
                            <div class="form-group row">
                                <label class="col-sm-3 col-md-2 col-form-label text-right">To :</label>
                                <div class="col-sm-9 col-md-10">
                                    <input class="form-control" type="text" id="to" name="to" maxlength="100" required="required" value="<?php if(isset($_GET['uid']) && $_GET['uid']){echo get_user_details((int) $_GET['uid'])->login_id;}?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-md-2 col-form-label text-right">Subject :</label>
                                <div class="col-sm-9 col-md-10">
                                    <input class="form-control" type="text" id="subject" name="subject" maxlength="100" required="required">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 col-md-12">
                                    <textarea class="form-control" name="message" id="textarea" rows="8" required="required"></textarea>
                                </div>
                            </div>
                            <!-- summernote -->
                            <?php /*<div id="summernote"></div>*/?>
                            <div class="hidden-xs hidden-sm btn-group">
                                <a href="email_inbox.php" class="text-center btn btn-default">DISCARD</a>
                            </div>
                            <div class="btn-group pull-right">
                                <button type="submit" type="button" class="btn btn-success">SEND</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>