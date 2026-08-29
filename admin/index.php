<?php include '../lib/config.php';
if (isset($_SESSION['adminid']) && !empty($_SESSION['adminid'])) {
    redirect('./dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Administrator Login</title>
        <link rel="shortcut icon" href="dist/images/nexabot-logo.png" type="image/x-icon">
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
        <script>
            WebFont.load({
                google: {
                    families: ['Alegreya+Sans:100,100i,300,300i,400,400i,500,500i,700,700i,800,800i,900,900i', 'Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i', 'Open Sans']
                }
            });
        </script>
        <!-- Bootstrap -->
        <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <!-- Bootstrap rtl -->
        <!--<link href="../assets/bootstrap-rtl/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/>-->
        <!-- Pe-icon-7-stroke -->
        <link href="../assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <!-- Theme style -->
        <link href="../assets/dist/css/component_ui.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/dist/css/skins/component_ui_black.css" rel="stylesheet" type="text/css"/>
        <!-- Theme style rtl -->
        <!--<link href="../assets/dist/css/component_ui_rtl.css" rel="stylesheet" type="text/css"/>-->
        <!-- Custom css -->
        <link href="../assets/dist/css/custom.css" rel="stylesheet" type="text/css"/>
    </head>
      <style>
    /* Base Styles - Trading Platform Look */
    body, #page-wrapper {
        background-color: #0b0e11;
        color: #eaecef;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        min-height: 100vh;
    }

    /* Form Controls */
    .form-control {
        background: #1e2126;
        color: #eaecef;
        border: 1px solid #2c3137;
        border-radius: 8px;
        padding: 12px 15px;
        height: auto;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background: #262b33;
        border-color: #f0b90b;
        box-shadow: 0 0 0 2px rgba(240, 185, 11, 0.2);
        color: #fff;
    }

    .form-control::placeholder {
        color: #848e9c;
        opacity: 1;
    }

    .form-control:-ms-input-placeholder {
        color: #848e9c;
    }

    .form-control::-ms-input-placeholder {
        color: #848e9c;
    }

    /* Trading Form Container */
    .form-signin {
        background: #1e2126 !important;
        border: 1px solid #2c3137 !important;
        border-radius: 12px !important;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2) !important;
        position: relative;
    }

    .form-signin::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #f0b90b, transparent);
    }

    .form-signin::after {
        content: '';
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(240, 185, 11, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 0;
    }

    /* Button Styling */
    .btn {
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .btn-primary {
        background: linear-gradient(135deg, #f0b90b 0%, #e6a800 100%) !important;
        border: none !important;
        color: #000 !important;
        box-shadow: 0 4px 12px rgba(240, 185, 11, 0.3);
    }

    .btn-primary:hover, .btn-primary:focus {
        background: linear-gradient(135deg, #e6a800 0%, #f0b90b 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(240, 185, 11, 0.4);
        color: #000 !important;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: rotate(30deg);
        transition: all 0.5s ease;
        opacity: 0;
    }

    .btn-primary:hover::before {
        animation: shimmerEffect 1.5s infinite;
    }

    @keyframes shimmerEffect {
        0% {
            transform: translateX(-100%) rotate(30deg);
            opacity: 0.5;
        }
        100% {
            transform: translateX(100%) rotate(30deg);
            opacity: 0;
        }
    }

    /* Trading Theme Elements */
    .trading-theme-element {
        position: absolute;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(240, 185, 11, 0.05) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 0;
        animation: floatAnimation 8s infinite alternate ease-in-out;
    }

    .element-1 {
        top: -75px;
        left: -75px;
        animation-delay: 0s;
    }

    .element-2 {
        bottom: -75px;
        right: -75px;
        animation-delay: 2s;
    }

    .element-3 {
        top: 50%;
        right: -75px;
        animation-delay: 4s;
    }

    @keyframes floatAnimation {
        0% { transform: translate(0, 0) scale(1); opacity: 0.5; }
        100% { transform: translate(10px, 10px) scale(1.1); opacity: 0.8; }
    }

    /* Text Styling */
    h5 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        background: linear-gradient(90deg, #f0b90b, #e6a800);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: titleGlow 3s infinite alternate;
    }

    @keyframes titleGlow {
        0% { text-shadow: 0 0 5px rgba(240, 185, 11, 0.3); }
        100% { text-shadow: 0 0 15px rgba(240, 185, 11, 0.6); }
    }

    /* Form Elements */
    .form-check-label {
        color: #848e9c !important;
    }

    .form-check-input:checked {
        background-color: #f0b90b !important;
        border-color: #f0b90b !important;
    }

    /* Links */
    a {
        color: #f0b90b !important;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    a:hover {
        text-decoration: underline;
    }

    /* Background Overlay */
    .bg-overlay {
        background: linear-gradient(135deg, #0b0e11 0%, #1e2126 100%) !important;
    }

    /* Input with Icon */
    .input-with-icon {
        position: relative;
        margin-bottom: 20px;
    }

    .input-with-icon i {
        position: absolute;
        left: 15px;
        top: 72%;
        transform: translateY(-50%);
        color: #848e9c;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .input-with-icon input {
        padding-left: 40px;
    }

    .input-with-icon input:focus + i {
        color: #f0b90b;
    }

    /* Label Styling */
    label {
        color: #848e9c;
        font-size: 14px;
        margin-bottom: 8px;
        display: block;
    }
    </style>
    <body>
       
        <section class="bg-home d-flex align-items-center position-relative" style="background: url('dist/images/bg/trading.jpg') center;">
              <div class="bg-overlay opacity-8"></div>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="form-signin p-4 rounded shadow-md">
                             <!-- Trading Theme Elements -->
                            <div class="trading-theme-element element-1"></div>
                            <div class="trading-theme-element element-2"></div>
                            <div class="trading-theme-element element-3"></div>
                            <form action="login_model.php" id="loginForm" novalidate  method="post">
                                <?php echo getMessage();?>
                                <a href="index.html"><img src="dist/images/nexabot-logo.png" class="avatar  mb-4 d-block mx-auto" alt="" style="width : 180px"></a>
                                <h5 class="mb-3">Admin Login</h5>

                                <p class="text-muted">Please enter your credentials to login.</p>
                            
                                <div class=" mb-3">
                                    <input type="text" class="form-control" id="login_id" name="login_id" placeholder="Username" maxlength="20">
                                    <label for="floatingInput">Username</label>
                                </div>
                                <div class=" mb-3">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="******" maxlength="20">
                                    <label for="floatingInput">Password</label>
                                </div>
                <div class="form-check mb-3">
                                    <input class="form-check-input" id="checkbox3" type="checkbox" value="" id="flexCheckDefault">
                                    <label class="form-check-label text-muted" for="flexCheckDefault">Keep me signed in</label>
                                </div>
                                <button class="btn btn-primary w-100" type="submit">Login</button>
                                
                               
                        
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
          <!-- javascript -->
        <script src="dist/js/bootstrap.bundle.min.js"></script>
        <script src="dist/js/feather.min.js"></script>
        
        <!-- Main Js -->
        <script src="dist/js/plugins.init.js"></script>
        <script src="dist/js/app.js"></script>
       
          <link href="dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="dist/css/materialdesignicons.min.css" rel="stylesheet" type="text/css" />
        <link href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" rel="stylesheet">
        <!-- Main css -->
        <link href="dist/css/style.css" rel="stylesheet" type="text/css" id="theme-opt" />
        <link href="dist/css/colors/default.css" rel="stylesheet" id="color-opt">
       
       
       
       
       
       
        <!-- Content Wrapper -->
         <?php /* <div class="login-wrapper">
            <div class="container-center">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="view-header">
                            <div class="header-icon">
                                <i class="pe-7s-unlock"></i>
                            </div>
                            <div class="header-title">
                                <h3>Login</h3>
                                <small><strong>Please enter your credentials to login.</strong></small>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <form action="login_model.php" id="loginForm" novalidate  method="post">
                            <?php echo getMessage();?>
                            <?php /*<!--Social Buttons--> 
                            <div class="social">
                                <strong>Sign in using social network:</strong><br>
                                <div class="twitter_bg"><i class="fa fa-twitter"></i><a href="#" class="btn_1">Login Twitter</a></div>
                                <div class="fb_bg"><i class="fa fa-facebook"></i><a href="#" class="btn_2">Login Facebook</a></div>
                            </div>*/?>
                             <?php /* <div class="form-group">
                                <label class="control-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input type="text" class="form-control" id="login_id" name="login_id" placeholder="Username" maxlength="20">
                                </div>
                                <?php /* <span class="help-block small">Your unique username</span>
                            </div> */ ?>
                              <?php /*<div class="form-group">
                                <label class="control-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="******" maxlength="20">
                                </div>
                               <span class="help-block small">Your unique username</span>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary pull-right">Login</button>
                                <div class="checkbox checkbox-success">
                                    <input id="checkbox3" type="checkbox">
                                    <label for="checkbox3">Keep me signed in</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> */ ?>
                <?php /*<div id="bottom_text">
                    Don't have an account? <a href="register.php">Sign Up</a><br>
                    Remind <a href="forget.php">Password</a>
                </div>
            </div>
        </div>*/?>
        <!-- /.content-wrapper -->
        <!-- jQuery -->
        <script src="../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
        <!-- bootstrap js -->
        <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>