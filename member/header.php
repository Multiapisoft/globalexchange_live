<?php
include_once '../lib/config.php';
user();
$uid = $_SESSION['userid'];
$user = get_user_details($uid);
$_address = strtolower(SITE_CURRENCY_) . '_address';
$typearr = array(6 => 'DOT', 7 => 'TRX', 8 => 'LINK', 9 => 'BNB', 10 => 'BTC', 11 => 'ETC');
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <meta name="description" content="<?php echo isset($description) ? $description : ''; ?>">
    <meta name="keywords" content="<?php echo isset($keywords) ? $keywords : ''; ?>" />
    <title>
        <?php echo $title_name = isset($title) ? SITE_NAME . ' | ' . str_replace('COIN_NAME', SITE_CURRENCY, $title) : SITE_NAME . ' | Member Panel'; ?>
    </title>
    <link rel="shortcut icon" href="images/nexabot-logo.png" type="image/x-icon">
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

    <?php if ($_SERVER["PHP_SELF"] == '/soft/admin/dashboard.php' || (isset($_is_dashboard) && $_is_dashboard)) { ?>
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
        integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <link href="../assets/dist/css/custom.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <?php /*<link id="defaultTheme" href="../assets/dist/css/skins/skin-red-dark.css" rel="stylesheet" type="text/css"/>*/ ?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->



    <!-- tailwind Css  -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            /* Dark theme variables */
            --primary-bg: #0b0e11;
            --secondary-bg: #1e2329;
            --hover-bg: #2b3139;
            --text-primary: #eaecef;
            --text-secondary: #848e9c;
            --accent-color: #f0b90b;
            --border-color: #2c3137;
            --success-color: #02c076;
            --danger-color: #f6465d;
            --card-bg: #2b3139;
        }

        /* Light theme variables */
        [data-theme="light"] {
            --primary-bg: #ffffff;
            --secondary-bg: #f5f5f5;
            --hover-bg: #e8e8e8;
            --text-primary: #1e2329;
            --text-secondary: #707a8a;
            --border-color: #e8e8e8;
            --card-bg: #ffffff;
        }

        /* Global dark theme styles */
        body {
            background-color: var(--primary-bg);
            color: var(--text-primary);
        }

        #page-wrapper {
            background-color: var(--primary-bg);
            color: var(--text-primary);
        }

        .content {
            background-color: var(--primary-bg);
            color: var(--text-primary);
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        .slide-in {
            animation: slideIn 0.3s ease-in-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Enhanced Navbar */
        .navbar {
            background-color: #5b60e8;
            border-bottom: 1px solid var(--border-color);
            height: 64px;
            padding: 0 24px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1100;
            display: flex;
            backdrop-filter: blur(10px);
        }

        @media (min-width: 769px) {
            #page-wrapper {
                position: inherit;
                margin-left: 70px;
                padding: 0 30px 30px;
            }

            .sidebar {
                width: 80px;
                left: 0 !important;
                display: block !important;
            }
        }

        @media (max-width: 768px) {
            .menu-text {
                display: block !important;
                opacity: 1 !important;
                transition: opacity 0.3s ease;
            }

            #page-wrapper {
                margin-left: 0 !important;
                padding: 10px 15px 30px 15px !important;
                width: 100% !important;
            }

            .content {
                width: 100%;
                padding: 0;
            }
        }

        .navbar-brand {
            padding: 12px 0;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 100px;
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        /* Enhanced Sidebar */
        .sidebar {
            width: 80px;
            position: fixed;
            height: 100%;
            left: 0;
            top: 64px;
            background-color: var(--secondary-bg);
            transition: all 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
        }

        .sidebar-inner {
            width: 100%;
            height: 100%;
            overflow-y: auto;
            position: relative;
            z-index: 1051;
        }

        .sidebar.expanded {
            width: 260px;
        }

        /* Remove Bootstrap collapse conflicts */
        .sidebar.collapse {
            display: block;
        }
        
        .sidebar.collapsing {
            display: block;
        }

        /* Mobile Sidebar */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed !important;
                left: -100% !important;
                top: 64px !important;
                width: 280px !important;
                height: calc(100vh - 64px) !important;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
                transition: left 0.3s ease !important;
                z-index: 1050 !important;
            }

            .sidebar.in {
                left: 0 !important;
            }

            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 64px;
                left: 0;
                width: 100%;
                height: calc(100vh - 64px);
                background: rgba(0, 0, 0, 0.5);
                z-index: 1049;
                transition: opacity 0.3s ease;
            }

            .sidebar-backdrop.show {
                display: block;
                opacity: 1;
            }
        }

        /* Menu Items */
        #side-menu li {
            position: relative;
        }

        #side-menu li a {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #eaecef !important;
            text-decoration: none;
            white-space: nowrap;
            position: relative;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        /* Submenu styling for collapsed state */
        .sidebar:not(.expanded) .nav-second-level {
            position: fixed;
            z-index: 1052 !important;
            left: 80px;
            min-width: 200px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            background: var(--card-bg);
            border-radius: 4px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
            opacity: 0;
            visibility: hidden;
            transform: translateX(10px);
            transition: all 0.3s ease;
            padding: 8px 0;
            border: 1px solid var(--border-color);
            pointer-events: none;
        }

        /* Show submenu on hover */
        .sidebar:not(.expanded) #side-menu li:hover>.nav-second-level {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
            pointer-events: auto;
        }

        /* Submenu items in collapsed state */
        .sidebar:not(.expanded) .nav-second-level li a {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #eaecef !important;
        }

        .sidebar:not(.expanded) .nav-second-level li a:hover {
            background: var(--hover-bg);
        }

        /* Submenu styling for expanded state */
        .sidebar.expanded .nav-second-level {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar.expanded .nav-second-level.show {
            max-height: 500px;
        }

        /* Arrow indicator */
        .fa.arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .sidebar.expanded li a[aria-expanded="true"] .fa.arrow {
            transform: rotate(180deg);
        }

        /* Hover effect for main menu items */
        #side-menu>li>a:hover {
            background: var(--hover-bg);
            transition: background 0.2s ease;
        }
        
        /* Desktop hover state for all menu items */
        @media (min-width: 769px) {
            #side-menu>li>a:hover {
                background: var(--hover-bg);
                border-left-color: var(--accent-color);
            }
            
            /* Submenu items hover */
            .nav-second-level li a:hover {
                background: var(--hover-bg);
                padding-left: 35px;
                transition: all 0.2s ease;
            }
        }

        /* Animation for submenu items */
        .nav-second-level li {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .nav-second-level.show li,
        .sidebar:not(.expanded) #side-menu li:hover>.nav-second-level li {
            opacity: 1;
            transform: translateX(0);
        }

        /* Delayed animation for submenu items */
        .nav-second-level li:nth-child(1) {
            transition-delay: 0.1s;
        }

        .nav-second-level li:nth-child(2) {
            transition-delay: 0.2s;
        }

        .nav-second-level li:nth-child(3) {
            transition-delay: 0.3s;
        }

        .nav-second-level li:nth-child(4) {
            transition-delay: 0.4s;
        }

        .nav-second-level li:nth-child(5) {
            transition-delay: 0.5s;
        }

        /* Tooltip for collapsed state - disabled for cleaner look */
        /* Users can directly see icons and remember their function */

        /* Icon styling */
        #side-menu li a i {
            min-width: 24px;
            text-align: center;
            font-size: 20px;
        }
        
        /* Ensure items without submenu are always clickable */
        #side-menu > li > a:not([data-has-submenu]) {
            pointer-events: auto !important;
            cursor: pointer;
        }
        
        /* Remove any pseudo-elements from non-submenu items */
        #side-menu > li > a:not([data-has-submenu])::after {
            display: none !important;
            content: none !important;
        }

        /* Menu text */
        .menu-text {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            color: #eaecef !important;
        }

        .sidebar.expanded .menu-text {
            display: block;
            opacity: 1;
            color: #eaecef !important;
        }

        /* Toggle button */
        .menu-toggle {
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            margin-right: 15px;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            color: var(--accent-color);
            transform: scale(1.1);
        }

        /* Active state */
        #side-menu li a.active {
            background: var(--hover-bg);
            border-left-color: var(--accent-color);
        }

        /* Arrow for submenu */
        .fa.arrow {
            margin-left: auto;
            font-size: 12px;
            transition: transform 0.3s ease;
            opacity: 0;
        }

        .sidebar.expanded .fa.arrow {
            opacity: 1;
        }

        /* Submenu */
        .nav-second-level {
            display: none;
            padding-left: 15px;
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar.expanded .nav-second-level {
            display: block;
        }

        .nav-second-level li a {
            padding: 10px 15px;
            font-size: 14px;
            color: #eaecef !important;
        }

        /* Arrow icon adjustments */
        .fa.arrow {
            opacity: 0;
            transition: all 0.3s ease;
        }

        .sidebar.expanded .fa.arrow {
            opacity: 1;
        }

        /* Submenu adjustments */
        .nav-second-level {
            padding-left: 0;
        }

        .sidebar.expanded .nav-second-level {
            padding-left: 15px;
        }

        /* Fix for arrow alignment */
        .fa.arrow {
            margin-left: auto;
            font-size: 12px;
            transition: transform 0.2s ease;
        }

        #side-menu li a[aria-expanded="true"] .fa.arrow {
            transform: rotate(180deg);
        }

        /* Remove any additional borders or lines */
        #side-menu li {
            border: none;
        }

        /* Hover styles are defined above */

        /* Fix for nested menu items */
        .nav-second-level {
            padding-left: 15px;
        }

        .nav-second-level li a {
            padding-left: 30px !important;
        }

        /* Remove any unwanted borders */
        .sidebar-nav {
            border: none;
        }

        .nav.nav-second-level {
            background: transparent;
        }

        /* Theme Switcher */
        .theme-switcher {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background-color: var(--card-bg);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .theme-switcher:hover {
            transform: scale(1.1);
        }

        /* Settings Panel */
        .settings-panel {
            position: fixed;
            right: -300px;
            top: 64px;
            width: 300px;
            height: calc(100vh - 64px);
            background-color: var(--card-bg);
            border-left: 1px solid var(--border-color);
            transition: right 0.3s ease;
            z-index: 998;
            padding: 24px;
        }

        .settings-panel.active {
            right: 0;
        }

        /* Top Navigation Icons */
        .navbar-top-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .navbar-top-links .nav-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
            position: relative;
        }

        .navbar-top-links .nav-icon:hover {
            background-color: var(--hover-bg);
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .mobile-logout {
            display: none;
        }

        .custom-logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(145deg, #f0b90b, #e6a908);
            border-radius: 50px;
            color: #000;
            transition: all 0.3s ease;
            border: 2px solid #f0b90b;
        }

        .custom-logout-btn:hover {
            background: #000;
            color: #f0b90b;
            border-color: #f0b90b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(240, 185, 11, 0.2);
        }

        .logout-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-text {
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media screen and (max-width: 768px) {
            .logout-text {
                display: none;
            }

            .custom-logout-btn {
                padding: 8px;
            }
        }

        .logout-btn i {
            font-size: 16px;
        }

        @media screen and (max-width: 768px) {
            .mobile-logout {
                display: block;
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 999;
            }

            .logout-btn {
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            }

            /* Hide desktop logout if it exists */
            .desktop-logout {
                display: none;
            }
        }

        #page-wrapper {
            padding: 0px 11px 0px 2px !important;
            min-height: 568px;
            width: 100%;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
        }

        #wrapper {
            position: relative;
            margin-top: 69px;
            width: 100%;
            overflow-x: hidden;
        }

        .content {
            max-width: 100%;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
        }

        /* Container alignment fixes */
        @media (max-width: 768px) {
            body {
                overflow-x: hidden;
            }

            #wrapper {
                margin-top: 64px;
            }

            .container-fluid {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }

            /* Fix for dashboard cards */
            .row {
                margin-left: -10px;
                margin-right: -10px;
            }

            .row > [class*='col-'] {
                padding-left: 10px;
                padding-right: 10px;
            }
        }

        @media screen and (max-width: 480px) {
            .mobile-logout {
                bottom: 15px;
                right: 15px;
            }

            .logout-btn {
                padding: 8px 12px;
            }

            .logout-btn span {
                display: none;
            }

            .logout-btn i {
                font-size: 20px;
            }
        }

        .navbar-right {
            position: absolute;
            right: 0;
        }

        .navbar-top-links>li.log_out a {
            padding: 15px 15px !important;
        }

        /* Mobile Navbar Toggle Button */
        .navbar-toggle {
            display: none;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 8px 12px;
            margin-right: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1001;
        }

        .navbar-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .navbar-toggle:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.7);
        }

        .navbar-toggle i {
            color: white;
            font-size: 24px;
        }

        @media (max-width: 768px) {
            .navbar-toggle {
                display: block !important;
            }
            
            .navbar {
                z-index: 1100;
            }
        }


    </style>
    <style type="text/tailwindcss">
        @theme {
         --primary: #4f46e5;
        --primary-hover: #4338ca;
        --secondary: #7c3aed;
         --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --border-color: #e2e8f0;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
      }
    </style>
</head>

<body>


    <div id="wrapper" class="wrapper animsition">
        <!-- Navigation -->
        <nav class="navbar navbar-fixed-top">
            <!-- <div class="navbar-header"> -->
            <button type="button" class="navbar-toggle collapsed" id="mobileMenuToggle" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <i class="material-icons">menu</i>
            </button>

            <a class="navbar-brand" href="dashboard.php">
                <?php if (file_exists('images/nexabot-logo.png')) { ?>
                    <img class="main-logo" src="images/nexabot-logo.png" id="bg" alt="<?php echo SITE_NAME; ?>">
                <?php } else { ?>
                    <span><?php echo SITE_NAME; ?></span>
                <?php } ?>
            </a>
            <!--<ul class="nav navbar-nav hidden-xs">-->
            <!--    <li><a id="fullscreen" href="#"><i class="material-icons">fullscreen</i> </a></li>-->
            <!--</ul>-->
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" href="email_inbox.php">
                        <i class="material-icons">chat</i>
                        <span class="label label-danger"><?php echo get_unread_message_count($uid); ?></span>
                    </a>
                </li><!-- /.Dropdown -->
                <?php /*<li class="dropdown">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
    <li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
            <i class="material-icons">person_add</i>
            </a>
        <ul class="dropdown-menu dropdown-user">
            <?php /*<li><a href="profile.php"><i class="ti-user"></i>&nbsp; Profile</a></li>*/ ?>
                <?php /*<li><a href="report_login.php"><i class="ti-lock"></i>&nbsp; Login Details</a></li>*?>
            <li><a href="logout.php"><i class="ti-layout-sidebar-left"></i>&nbsp; Logout</a></li>
            </ul><!-- /.dropdown-user -->
            </li><!-- /.Dropdown -->*/ ?>
                <li class="log_out">
                    <a href="logout.php" class="custom-logout-btn">
                        <span class="logout-icon-wrapper">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>
                </li><!-- /.Log out -->
            </ul> <!-- /.navbar-top-links -->
            <!-- </div> -->

        </nav>
        <!-- /.Navigation -->
        
        <!-- Mobile Sidebar Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
        
        <div class="sidebar">
            <div class="sidebar-inner">
                <ul class="nav" id="side-menu">

                    <li>
                        <a href="dashboard.php" data-title="Dashboard" class="material-ripple">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="menu-text">Dashboard</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><a href="dashboard.php" class="material-ripple">Dashboard</a></li>
                        </ul>
                    </li>
                    <!--<li><a href="dashboard2.php" class="material-ripple"><i class="fas fa-chart-line"></i> Validator Dashboard</a></li>-->

                    <!-- Profile Section -->
                    <li>
                        <a href="#" class="material-ripple">
                            <i class="fas fa-user-circle"></i>
                            <span class="menu-text">Profile</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><a href="profile.php"><i class="fas fa-id-badge"></i> My Profile</a></li>
                            <li><a href="change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="material-ripple">
                            <i class="fas fa-user-friends"></i>
                            <span class="menu-text">Team</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><a href="direct_referral.php" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/hpivxauj.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon>
                                    My Direct</a></li>
                            <li><a href="downline.php" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/eszyyflr.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon> My Team</a>
                            </li>
                            <?php /*<li><a href="tree_view.php" class="material-ripple"><i
                                        class="material-icons">bubble_chart</i> My Tree</a></li>*/?>
                        </ul>
                    </li>

                    <li>
                        <a href="trade.php" data-title="Trade" class="material-ripple">
                            <i class="fas fa-chart-bar icon"></i>
                            <span class="menu-text">Trade</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><a href="trade.php" class="material-ripple">Trade</a></li>
                        </ul>
                    </li>

                    <!--<li><a href="invest_new.php?type=3" class="material-ripple"><i class="fas fa-money-bill"></i> Stacking Coins</a></li>-->

                    <li>
                        <a href="#" class="material-ripple">
                            <i class="fas fa-money-bill-wave"></i>
                            <span class="menu-text">Reports</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><a href="report_invest.php" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/etqbfrgp.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon> Investments History</a></li>
                            <li><a href="report_growth.php" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/xyboiuok.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon>Daily Trading Income</a></li>

                                    <?php /*<li><a href="report_direct.php" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/ajkxzzfb.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon> Direct Income</a></li> */?>

                            <?php /*<li><a href="report_level.php?type=1" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/ajkxzzfb.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon> Level Income</a></li>*/?>

                            <?php /*<li><a href="report_binary.php" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/ajkxzzfb.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon> Matching Income</a></li>*/?>

                            <li><a href="report_level.php?type=2" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/eszyyflr.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon>Level ROI Income</a></li>

                                    <?php /* <li><a href="report_royalty.php" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/lobpqdog.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon>Reward Income</a></li>

                            <li><a href="report_royalty.php?type=1" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/eszyyflr.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon>Fast Track Bonus</a></li>

                            <li><a href="report_royalty.php?type=2" class="material-ripple"><lord-icon
                                        src="https://cdn.lordicon.com/eszyyflr.json" trigger="hover"
                                        style="width:30px;height:30px">
                                    </lord-icon>Royalty Income</a></li>*/?>

                            <?php /*
                 <li><a href="report_royalty.php?type=2" class="material-ripple"><i class="fa fa-usd" aria-hidden="true"></i>Special Reward Income</a></li>
                 <li><a href="report_level.php?type=1" class="material-ripple"><i class="fa fa-usd" aria-hidden="true"></i>Upline Income</a></li>
                 <li><a href="report_level.php?type=2" class="material-ripple"><i class="fa fa-usd" aria-hidden="true"></i>Level Generation Income</a></li>
                 <li><a href="report_royalty.php?type=2" class="material-ripple"><i class="material-icons">business</i> Airdrop Income</a></li>
                 <li><a href="report_direct.php?type=2" class="material-ripple"><i class="material-icons">business</i> Referral Airdrop Rewad Income</a></li>
                 <li><a href="report_level.php?type=1" class="material-ripple"><i class="material-icons">business</i> Level Airdrop Income</a></li>*/ ?>


                        </ul>
                    </li>

                    <!--<li>-->
                    <!--    <a href="#" class="material-ripple"><i class="fa fa-video-camera" aria-hidden="true"></i> Adding-->
                    <!--        Videos<span class="fa arrow"></span></a>-->
                    <!--    <ul class="nav nav-second-level">-->
                    <!--        <li><a href="video.php" class="material-ripple"><i class="fa fa-plus-circle"-->
                    <!--                    aria-hidden="true"></i> Submit Video</a></li>-->
                    <!--        <li><a href="report_videos.php" class="material-ripple"><i class="fa fa-video-camera"-->
                    <!--                    aria-hidden="true"></i> Videos</a></li>-->
                    <!--    </ul>-->
                    <!--</li>-->

                    <li>
                        <a href="#" class="material-ripple">
                            <i class="fas fa-exchange-alt"></i>
                            <span class="menu-text">Fund Management</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <?php /*<li><a href="deposit_block.php" class="material-ripple"><i class="fas fa-cart-plus"></i> Add Fund by <?php echo SITE_CURRENCY_TKN; ?></a></li>*/ ?>
                            <li><a href="deposit_block.php" class="material-ripple"><i class="fas fa-cart-plus"></i> Add
                                    Fund by <?php echo SITE_CURRENCY_TKN; ?></a></li>
                            <li><a href="report_deposit_block.php" class="material-ripple"><i
                                        class="fas fa-history"></i> Deposit History</a></li>
                            <li><a href="withdrawal_block.php?type=10" class="material-ripple"><i
                                        class="material-icons">insert_emoticon</i> Withdrawal</a></li>
                            <li><a href="report_withdrawal_block.php" class="material-ripple"><i
                                        class="fas fa-history"></i> Withdrawal History</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="material-ripple">
                            <i class="fas fa-money-bill-alt"></i>
                            <span class="menu-text">Fund Transfer</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <!--<li><a href="fund_transfer.php?type=1" class="material-ripple"><i class="fas fa-exchange-alt"></i> Topup Fund Transfer</a></li>-->
                            <li><a href="fund_transfer3.php?type=1" class="material-ripple"><i
                                        class="fas fa-exchange-alt"></i> Self Fund Transfer</a></li>
                            <li><a href="fund_transfer.php" class="material-ripple"><i
                                        class="fas fa-exchange-alt"></i> P2P Fund Transfer</a></li>
                            <!-- <li><a href="game_fund_transfer.php" class="material-ripple"><i class="fas fa-exchange-alt"></i> Fund Transfer to Game</a></li> -->
                            <li><a href="report_fund_transfer.php" class="material-ripple"><i
                                        class="fas fa-history"></i> Fund Transfer History</a></li>
                        </ul>
                    </li>
                    <!--<li>-->
                    <!--    <a href="report_deposit_block.php" data-title="Deposit History">-->
                    <!--        <i class="fas fa-history">-->
                    <!--        <span class="menu-text">Dashboard</span>-->
                    <!--    </a>-->
                    <!--</li>-->

                    <li>
                        <a href="#" class="material-ripple">
                            <i class="fas fa-mail-bulk"></i>
                            <span class="menu-text">Support</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><a href="email_compose_mail.php">Compose</a></li>
                            <li><a href="email_inbox.php">Inbox</a></li>
                            <li><a href="email_sent_mail.php">Sent</a></li>
                        </ul>
                    </li>
                    <!-- <li>
                        <a href="https://skillclash.live/home" data-title="Go To Game">
                            <i class="fas fa-share icon"></i>
                            <span class="menu-text">Go To Game</span>
                        </a>
                    </li> -->
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
                <?php /*<div class="row">
    <div class="col-sm-12 col-md-6">
    <a style="cursor:pointer;color:#fff;border-radius:10px;padding: 5px 10px;align-items: center;display:inline-flex;background: #5b69bc;margin-top: 10px;margin-bottom: 10px;" href="<?php echo (SITE_CURRENCY_ == 'TRX') ? 'https://tronscan.org/#/contract' : ((SITE_CURRENCY_ == 'BNB') ? 'https://bscscan.com/address' : 'https://etherscan.io/address'); ?>/<?php echo CONTRACT_ADDRESS;?>" target="_blank">Contract Address: <?php echo CONTRACT_ADDRESS;?> <i class="fa fa-external-link"></i></a>
    </div>
    <div class="col-sm-12 col-md-6">
    <a style="cursor:pointer;color:#fff;border-radius:10px;padding: 5px 10px;align-items: center;display:inline-flex;background: #5b69bc;margin-top: 10px;margin-bottom: 10px;" href="<?php echo (SITE_CURRENCY_ == 'TRX') ? 'https://tronscan.org/#/address' : ((SITE_CURRENCY_ == 'BNB') ? 'https://bscscan.com/address' : 'https://etherscan.io/address'); ?>/<?php echo $user->bnb_address;?>" target="_blank">Your Address: <?php echo $user->bnb_address;?> <i class="fa fa-external-link"></i></a>
    </div>
    </div>*/ ?>
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="header-icon"><i
                            class="pe-7s-<?php echo isset($titleicon) ? $titleicon : 'graph1'; ?>"></i></div>
                    <div class="header-title">
                        <h1><?php echo isset($title) ? $title : ''; ?></h1>
                    </div>
                </div> <!-- /. Content Header (Page header) -->
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <?php echo getMessage(); ?>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Prevent Bootstrap from controlling the sidebar
                        $('.sidebar').off('hide.bs.collapse show.bs.collapse');
                        
                        // Add has-dropdown class to menu items with dropdowns
                        const menuItems = document.querySelectorAll('#side-menu li');
                        menuItems.forEach(item => {
                            if (item.querySelector('.nav-second-level')) {
                                item.classList.add('has-dropdown');
                            }
                        });

                        // Dynamic submenu positioning for desktop
                        const menuItemsWithSubmenu = document.querySelectorAll('#side-menu > li');
                        menuItemsWithSubmenu.forEach(item => {
                            const submenu = item.querySelector('.nav-second-level');
                            if (submenu) {
                                item.addEventListener('mouseenter', function() {
                                    if (window.innerWidth > 768) {
                                        const rect = this.getBoundingClientRect();
                                        const submenuHeight = submenu.offsetHeight;
                                        const viewportHeight = window.innerHeight;
                                        
                                        // Calculate optimal position
                                        let topPosition = rect.top;
                                        
                                        // Check if submenu would go below viewport
                                        if (topPosition + submenuHeight > viewportHeight) {
                                            // Position it so it fits in viewport
                                            topPosition = viewportHeight - submenuHeight - 20;
                                        }
                                        
                                        // Ensure it doesn't go above navbar
                                        if (topPosition < 70) {
                                            topPosition = 70;
                                        }
                                        
                                        submenu.style.top = topPosition + 'px';
                                    }
                                });
                            }
                        });

                        // Mobile Menu Toggle
                        const navbarToggle = document.getElementById('mobileMenuToggle');
                        const sidebar = document.querySelector('.sidebar');
                        const backdrop = document.getElementById('sidebarBackdrop');
                        const isMobile = () => window.innerWidth <= 768;

                        // Debug: Check if elements exist
                        console.log('Toggle button:', navbarToggle);
                        console.log('Sidebar:', sidebar);
                        console.log('Backdrop:', backdrop);
                        console.log('Window width:', window.innerWidth);
                        console.log('Sidebar classes:', sidebar ? sidebar.className : 'N/A');

                        // Toggle menu on button click
                        if (navbarToggle && sidebar && backdrop) {
                            navbarToggle.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                console.log('=== Menu toggle clicked! ===');
                                console.log('Current classes:', sidebar.className);
                                console.log('Has "in" class:', sidebar.classList.contains('in'));
                                
                                // Get computed style before
                                const styleBefore = window.getComputedStyle(sidebar);
                                console.log('Left position BEFORE:', styleBefore.left);
                                
                                // Force toggle
                                const wasOpen = sidebar.classList.contains('in');
                                sidebar.classList.toggle('in');
                                backdrop.classList.toggle('show');
                                
                                console.log('After toggle - Has "in":', sidebar.classList.contains('in'));
                                console.log('New classes:', sidebar.className);
                                
                                // Check computed style after
                                setTimeout(() => {
                                    const styleAfter = window.getComputedStyle(sidebar);
                                    console.log('Left position AFTER:', styleAfter.left);
                                    console.log('Display:', styleAfter.display);
                                    console.log('Visibility:', styleAfter.visibility);
                                    console.log('Opacity:', styleAfter.opacity);
                                }, 50);
                                
                                // Update aria-expanded
                                const isExpanded = sidebar.classList.contains('in');
                                this.setAttribute('aria-expanded', isExpanded);
                                
                                // Prevent body scroll when menu is open
                                if (isExpanded) {
                                    document.body.style.overflow = 'hidden';
                                    console.log('✅ Menu should be OPEN now');
                                } else {
                                    document.body.style.overflow = '';
                                    console.log('❌ Menu should be CLOSED now');
                                }
                            });
                        } else {
                            console.error('Missing elements - Toggle:', !!navbarToggle, 'Sidebar:', !!sidebar, 'Backdrop:', !!backdrop);
                        }

                        // Close menu function
                        function closeMenu() {
                            sidebar.classList.remove('in');
                            backdrop.classList.remove('show');
                            document.body.style.overflow = '';
                            if (navbarToggle) {
                                navbarToggle.setAttribute('aria-expanded', 'false');
                            }
                        }
                        // expose globally so other scripts can reuse
                        window.closeSidebarMenu = closeMenu;

                        // Close menu when clicking backdrop
                        if (backdrop) {
                            backdrop.addEventListener('click', function() {
                                console.log('Backdrop clicked');
                                closeMenu();
                            });
                        }

                        // Close menu when clicking a menu item (mobile only)
                        // Close sidebar only for items without submenus
                        const menuLinks = document.querySelectorAll('#side-menu > li > a');
                        menuLinks.forEach(link => {
                            const hasSubmenu = link.nextElementSibling && link.nextElementSibling.classList.contains('nav-second-level');
                            if (!hasSubmenu) {
                                link.addEventListener('click', function(e) {
                                    if (isMobile()) {
                                        console.log('Menu link clicked');
                                        setTimeout(() => {
                                            closeMenu();
                                        }, 200);
                                    }
                                });
                            }
                        });

                        // Handle mobile responsiveness
                        function adjustForMobile() {
                            if (!isMobile()) {
                                sidebar.classList.remove('in');
                                backdrop.classList.remove('show');
                                document.body.style.overflow = '';
                            }
                        }

                        window.addEventListener('resize', adjustForMobile);
                        adjustForMobile();
                    });
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Theme Switcher
                        const themeSwitcher = document.getElementById('themeSwitcher');
                        const html = document.documentElement;

                        themeSwitcher.addEventListener('click', function () {
                            const currentTheme = html.getAttribute('data-theme');
                            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                            html.setAttribute('data-theme', newTheme);

                            // Update icon
                            const icon = this.querySelector('i');
                            icon.textContent = newTheme === 'dark' ? 'dark_mode' : 'light_mode';

                            // Save preference
                            localStorage.setItem('theme', newTheme);
                        });

                        // Settings Panel Toggle
                        const settingsToggle = document.getElementById('settingsToggle');
                        const settingsPanel = document.getElementById('settingsPanel');

                        settingsToggle.addEventListener('click', function (e) {
                            e.preventDefault();
                            settingsPanel.classList.toggle('active');
                        });

                        // Sidebar Toggle
                        const sidebarToggle = document.getElementById('sidebarToggle');
                        const sidebar = document.querySelector('.sidebar');

                        sidebarToggle.addEventListener('click', function () {
                            sidebar.classList.toggle('active');
                        });

                        // Add slide-in animation to menu items
                        const menuItems = document.querySelectorAll('#side-menu li a');
                        menuItems.forEach((item, index) => {
                            item.style.animationDelay = `${index * 0.1}s`;
                            item.classList.add('slide-in');
                        });

                        // Load saved theme preference (default to dark)
                        const savedTheme = localStorage.getItem('theme') || 'dark';
                        html.setAttribute('data-theme', savedTheme);
                        if (themeSwitcher && themeSwitcher.querySelector('i')) {
                            themeSwitcher.querySelector('i').textContent =
                                savedTheme === 'dark' ? 'dark_mode' : 'light_mode';
                        }
                    });
                </script>
                <style>
                    /* Base styles */
                    .nav-second-level {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                        background: var(--card-bg);
                        border: 1px solid rgba(255, 255, 255, 0.1);
                        border-radius: 4px;
                        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
                    }

                    /* Ensure all menu and submenu text is light */
                    #side-menu li a,
                    #side-menu li a .menu-text,
                    .nav-second-level li a,
                    .nav-second-level li a i {
                        color: #eaecef !important;
                    }

                    /* Desktop styles (hover) */
                    @media (min-width: 769px) {
                        .nav-second-level {
                            position: fixed;
                            left: 80px;
                            min-width: 200px;
                            max-height: calc(100vh - 100px);
                            overflow-y: auto;
                            display: none;
                            z-index: 1052 !important;
                        }

                        #side-menu li:hover>.nav-second-level {
                            display: block;
                            animation: fadeInUp 0.3s ease forwards;
                            z-index: 1052 !important;
                        }
                        
                        /* Menu item hover z-index boost */
                        #side-menu li:hover {
                            z-index: 1053 !important;
                        }
                    }

                    /* Mobile styles */
                    @media (max-width: 768px) {
                        .nav-second-level {
                            position: static !important;
        display: none !important;
                            background: rgba(0, 0, 0, 0.15);
                            border: none !important;
                            box-shadow: none !important;
                            border-radius: 0;
                            max-height: 0;
                            overflow: hidden;
                            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                            opacity: 1 !important;
                            visibility: visible !important;
                            transform: none !important;
                            pointer-events: auto !important;
                            left: auto !important;
                        }

                        .nav-second-level.show {
        display: block !important;
                            max-height: 800px;
                            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                        }

                        #side-menu > li > a {
                            padding: 15px 20px;
                            display: flex;
                            /* justify-content: space-between; */
                            align-items: center;
                        }

                        .nav-second-level li a {
                            padding: 12px 20px 12px 50px !important;
                            font-size: 14px;
                            color: #eaecef !important;
                        }

                        /* Show arrow on mobile for items with submenu */
                        #side-menu > li > a[data-has-submenu] .fa.arrow {
                            opacity: 1 !important;
                            display: inline-block !important;
                            position: absolute;
                            right: 20px;
                        }

                        /* Rotate arrow when submenu is open */
                        #side-menu > li > a[aria-expanded="true"] .fa.arrow {
                            transform: rotate(180deg) !important;
                        }
                        
                        /* Ensure menu text is visible on mobile */
                        .sidebar .menu-text {
                            display: block !important;
                            opacity: 1 !important;
                        }
                        
                        /* Submenu items styling on mobile */
                        .sidebar .nav-second-level li {
                            transition-delay: 0s !important;
                        }
                    }

                    /* Animation keyframes */
                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translateY(5px);
                        }

                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }

                    /* Custom scrollbar for submenu */
                    .nav-second-level::-webkit-scrollbar {
                        width: 6px;
                    }

                    .nav-second-level::-webkit-scrollbar-track {
                        background: rgba(0, 0, 0, 0.1);
                        border-radius: 3px;
                    }

                    .nav-second-level::-webkit-scrollbar-thumb {
                        background: var(--text-secondary);
                        border-radius: 3px;
                    }

                    .nav-second-level::-webkit-scrollbar-thumb:hover {
                        background: var(--text-primary);
                    }
                </style>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const isMobile = () => window.innerWidth <= 768;
                        const sidebar = document.querySelector('.sidebar');

                        // Handle submenu toggle on mobile
                        const menuItemsWithSubmenu = document.querySelectorAll('#side-menu > li > a');
                        menuItemsWithSubmenu.forEach(item => {
                            const submenu = item.nextElementSibling;
                            const hasSubmenu = submenu && submenu.classList.contains('nav-second-level');
                            
                            if (hasSubmenu) {
                                // Mark as having submenu
                                item.setAttribute('data-has-submenu', 'true');
                                item.setAttribute('aria-expanded', 'false');
                                
                                item.addEventListener('click', function(e) {
                                    if (isMobile()) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        
                                        const isOpen = submenu.classList.contains('show');
                                        
                                        // Close all other submenus
                                        document.querySelectorAll('.nav-second-level').forEach(menu => {
                                            if (menu !== submenu) {
                                                menu.classList.remove('show');
                                                const parentLink = menu.previousElementSibling;
                                                if (parentLink) {
                                                    parentLink.setAttribute('aria-expanded', 'false');
                                                }
                                            }
                                        });
                                        
                                        // Toggle current submenu
                                        submenu.classList.toggle('show');
                                        
                                        // Update aria-expanded
                                        this.setAttribute('aria-expanded', !isOpen);
                                        
                                        // Prevent sidebar from auto-closing when opening dropdown
                                        return;
                                    }
                                });
                            } else {
                                // For items without submenu, allow normal navigation
                                item.removeAttribute('data-has-submenu');
                            }
                        });

                        function adjustForMobile() {
                            if (isMobile()) {
                                // Force show menu text in mobile
                                const menuTexts = document.querySelectorAll('.menu-text');
                                menuTexts.forEach(text => {
                                    text.style.display = 'block';
                                    text.style.opacity = '1';
                                });
                            } else {
                                // Reset to default desktop state
                                const menuTexts = document.querySelectorAll('.menu-text');
                                menuTexts.forEach(text => {
                                    text.style.display = '';
                                    text.style.opacity = '';
                                });
                                
                                // Close all mobile submenus
                                document.querySelectorAll('.nav-second-level').forEach(menu => {
                                    menu.classList.remove('show');
                                });
                            }
                        }

                        // Initial setup
                        adjustForMobile();

                        // Handle resize events
                        let resizeTimer;
                        window.addEventListener('resize', () => {
                            clearTimeout(resizeTimer);
                            resizeTimer = setTimeout(adjustForMobile, 250);
                        });

                        // Close sidebar when submenu links are selected on mobile
                        const submenuLinks = document.querySelectorAll('.nav-second-level a');
                        submenuLinks.forEach(link => {
                            link.addEventListener('click', function () {
                                if (isMobile() && typeof window.closeSidebarMenu === 'function') {
                                    setTimeout(() => window.closeSidebarMenu(), 150);
                                }
                            });
                        });
                    });
                </script>