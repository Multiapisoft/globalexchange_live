<?php
include_once '../lib/config.php';
admin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <meta name="description" content="<?php echo isset($description) ? $description : ''; ?>">
    <meta name="keywords" content="<?php echo isset($keywords) ? $keywords : ''; ?>" />
    <title><?php echo $title_name = isset($title) ? SITE_NAME . ' | ' . $title : SITE_NAME . ' | Admin Panel'; ?></title>
    <link rel="shortcut icon" href="dist/images/nexabot-logo.png" type="image/x-icon">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ['Alegreya+Sans:100,100i,300,300i,400,400i,500,500i,700,700i,800,800i,900,900i', 'Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i', 'Open Sans']
            }
        });
    </script>
    <!-- START GLOBAL MANDATORY STYLE -->
    <link href="../assets/dist/css/base.css" rel="stylesheet" type="text/css" />
    <!-- START PAGE LABEL PLUGINS -->
    <link href="../assets/plugins/datatables/dataTables.min.css" rel="stylesheet" type="text/css" />
    <?php if ($_SERVER["PHP_SELF"] == '/soft/admin/dashboard.php') { ?>
        <link href="../assets/plugins/toastr/toastr.min.css" rel=stylesheet type="text/css" />
        <link href="../assets/plugins/emojionearea/emojionearea.min.css" rel=stylesheet type="text/css" />
        <link href="../assets/plugins/monthly/monthly.min.css" rel=stylesheet type="text/css" />
        <link href="../assets/plugins/amcharts/export.css" rel=stylesheet type="text/css" />
    <?php } ?>
    <!-- START THEME LAYOUT STYLE -->
    <link href="../assets/dist/css/component_ui.min.css" rel="stylesheet" type="text/css" />
    <?php /*<link id="defaultTheme" href="../assets/dist/css/skins/component_ui_black.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/dist/css/component_ui_black.css" rel="stylesheet" type="text/css"/>*/ ?>

    <link id="defaultTheme" href="../assets/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />

    <link href="../assets/dist/css/custom.css" rel="stylesheet" type="text/css" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>

<body>
    <div id="wrapper" class="wrapper animsition">
        <!-- Navigation -->
        <nav class="navbar navbar-fixed-top">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <i class="material-icons">apps</i>
                </button>
                <a class="navbar-brand" href="dashboard.php">
                    <!-- <img class="main-logo" src="extra/img/logo.png" id="bg" alt=""> -->
                    <span><?php echo SITE_NAME; ?></span>
                </a>
            </div>
            <div class="nav-container">
                <!-- /.navbar-header -->
                <ul class="nav navbar-nav hidden-xs">
                    <li><a id="fullscreen" href="#"><i class="material-icons">fullscreen</i> </a></li>
                </ul>
                <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" href="email_inbox.php">
                            <i class="material-icons">chat</i>
                            <span class="label label-danger"><?php echo get_unread_message_count(); ?></span>
                        </a>
                    </li><!-- /.Dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
                            <i class="material-icons">person_add</i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="profile.php"><i class="ti-user"></i>&nbsp; Profile</a></li>
                            <li><a href="email_inbox.php"><i class="ti-email"></i>&nbsp; My Messages</a></li>
                            <li><a href="settings.php"><i class="ti-settings"></i>&nbsp; Settings</a></li>
                            <li><a href="login_details.php"><i class="ti-lock"></i>&nbsp; Login Details</a></li>
                            <li><a href="logout.php"><i class="ti-layout-sidebar-left"></i>&nbsp; Logout</a></li>
                        </ul><!-- /.dropdown-user -->
                    </li><!-- /.Dropdown -->
                    <li class="log_out">
                        <a href="logout.php">
                            <i class="material-icons">power_settings_new</i>
                        </a>
                    </li><!-- /.Log out -->
                </ul> <!-- /.navbar-top-links -->
            </div>
        </nav>
        <!-- /.Navigation -->
        <div class="sidebar">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-heading "> <span>Main Navigation</span></li>
                    <li><a href="dashboard.php" class="material-ripple"><i class="material-icons">home</i> Dashboard</a></li>
                    <li>
                        <a href="javascript:void(0);" class="material-ripple"><i class="material-icons">bubble_chart</i> Users<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="users.php">All Users</a></li>
                            <li><a href="downline.php">Downline</a></li>
                            <li><a href="report_login.php">Login Details</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="material-ripple"><i class="material-icons">business</i> Reports<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="report_invest.php">Investments</a></li>
                            <li><a href="report_growth.php">ROI Income</a></li>
                            <li><a href="report_level.php?type=2">Level ROI Income</a></li>
                            <?php /*<li><a href="report_direct.php">Referral Income</a></li>
                            <li><a href="report_level.php?type=1">Level Income</a></li>
                            <li><a href="report_royalty.php">Reward Income</a></li>
                            <li><a href="report_royalty.php?type=2">Royalty Income</a></li>*/?>

                            <?php /*
                            <li><a href="report_royalty.php?type=1">Fast Track Bonus</a></li>
                            <li><a href="report_level.php">Bot subscription package- Generation Distribution</a></li>
                            <li><a href="report_binary.php">Bot Matching Income</a></li>
                            <li><a href="report_level.php">subscription package- Generation Distribution</a></li>
                                <li><a href="report_level.php?type=2">Level ROI Income</a></li>*/ ?>
                            <!--<li><a href="report_royalty.php?type=2">Special Reward Income</a></li>-->
                            <?php /*<li><a href="report_level.php?type=3">Gold Auto Pool Income</a></li>
                                <li><a href="report_level.php?type=4">Diamond Auto Pool Income</a></li>*/ ?>

                            <?php /*<li><a href="report_royalty.php?type=2">Airdrop Income</a></li>
                                <li><a href="report_direct.php?type=2">Direct Airdrop Income</a></li>*/ ?>

                            <?php /*
                                <li><a href="report_direct.php?type=1">Stacking Referral Income</a></li>
                                <li><a href="report_level.php?type=2">Super Jsckpot Income</a></li>
                                <li><a href="report_level.php?type=3">Dream Income</a></li>
                                
                                <li><a href="report_growth.php">Stacking Income</a></li>
                                <li><a href="report_royalty.php?type=1">Community Development Income</a></li>
                                <li><a href="report_royalty.php?type=3">Pool Income</a></li>*/ ?>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="material-ripple"><i class="material-icons">business</i> Fund Reports<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="report_fund_transfer.php">Fund Transfer</a></li>
                            <li><a href="report_fund_deduct.php">Fund Deduct</a></li>
                            <li><a href="report_deposit_block.php">Deposit Crypto</a></li>
                            <li><a href="smart_contract_withdrawal.php">Withdrawal Crypto</a></li>
                            <?php /*<li><a href="report_walfare_fund.php">Walfare Fund</a></li>*/?>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="material-ripple"><i class="material-icons">insert_emoticon</i> Fund Management<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="fund_transfer.php">Transfer Fund</a></li>
                            <li><a href="fund_deduct.php">Deduct Fund</a></li>
                        </ul>
                    </li>
                    <?php /*<li>
                            <a href="#" class="material-ripple"><i class="material-icons">stay_current_portrait</i> Recharge<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="report_recharge.php">History</a></li>
                            </ul>
                        </li>*/ ?>
                    <li>
                        <a href="#" class="material-ripple"><i class="material-icons">widgets</i> CMS<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="cms_package.php">Package</a></li>
                            <li><a href="cms_hot_news.php">Hot News</a></li>
                            <?php $rs = my_query("SELECT * FROM cms_menu WHERE status = 0");
                            while ($r = my_fetch_object($rs)) {
                            ?>
                                <li><a href="cms.php?mid=<?php echo $r->recid; ?>"><?php echo $r->title . (($r->type) ? 's' : ''); ?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="material-ripple"><i class="material-icons">drafts</i> Mailbox<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="email_compose_mail.php">Compose</a></li>
                            <li><a href="email_inbox.php">Inbox</a></li>
                            <li><a href="email_sent_mail.php">Sent</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.Left Sidebar-->
        <!-- /.Navbar  Static Side -->
        <div class="control-sidebar-bg"></div>
        <!-- Page Content -->
        <div id="page-wrapper">
            <!-- main content -->
            <div class="content">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="header-icon"><i class="pe-7s-<?php echo isset($titleicon) ? $titleicon : 'graph1'; ?>"></i></div>
                    <div class="header-title">
                        <h1><?php echo isset($title) ? $title : ''; ?></h1>
                    </div>
                </div> <!-- /. Content Header (Page header) -->
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <?php echo getMessage(); ?>
                    </div>
                </div>