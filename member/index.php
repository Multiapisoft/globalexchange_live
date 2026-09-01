<?php
include '../lib/config.php';
if (SITE_WORKING_STATUS) {
    echo '<center style="position: relative; top: 100px;"><h1>This site is under maintenance</h1></center>';
    die;
}
if (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
    redirect('./dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo SITE_NAME; ?> | Member Login</title>
    <link rel="shortcut icon" href="theme/assets/favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
        integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="theme/css/panel.css" rel="stylesheet" type="text/css" />
    <link href="theme/css/login-auth.css" rel="stylesheet" type="text/css" />
</head>

<body class="ge-login-page">

    <div class="ge-login-shell">
        <!-- Live Forex chart (TradingView) -->
        <aside class="ge-login-chart" aria-hidden="true">
            <div class="ge-login-chart-head">
                <div class="pair">
                    <span class="live-dot" aria-hidden="true"></span>
                    EUR/USD · Live Forex
                </div>
                <span class="pair"><span>●</span> Market Open</span>
            </div>
            <div class="ge-tradingview-wrap">
                <div class="tradingview-widget-container">
                    <div class="tradingview-widget-container__widget"></div>
                    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                    {
                        "autosize": true,
                        "symbol": "FX:EURUSD",
                        "interval": "15",
                        "timezone": "Etc/UTC",
                        "theme": "dark",
                        "style": "1",
                        "locale": "en",
                        "backgroundColor": "rgba(5, 5, 5, 1)",
                        "gridColor": "rgba(212, 175, 55, 0.08)",
                        "allow_symbol_change": true,
                        "calendar": false,
                        "support_host": "https://www.tradingview.com"
                    }
                    </script>
                </div>
            </div>
        </aside>

        <!-- Login panel -->
        <main class="ge-login-panel">
            <div class="ge-login-card balance-card animate-in">
                <form action="login_model.php" id="loginForm" method="post" novalidate>
                    <?php echo getMessage(); ?>
                    <a href="index.php">
                        <img src="theme/assets/logo.png" class="ge-login-logo" alt="<?php echo SITE_NAME; ?>">
                    </a>
                    <h1 class="ge-login-title">Welcome <span class="text-gold">Back</span></h1>
                    <p class="ge-login-subtitle">Trade Smart, Grow Faster — sign in to your account</p>

                    <div class="ge-login-field">
                        <label for="login_id">Username / Email</label>
                        <input type="text" class="form-control" id="login_id" name="login_id"
                            placeholder="Enter your username" maxlength="20" required autocomplete="username">
                    </div>

                    <div class="ge-login-field">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password" maxlength="20" required autocomplete="current-password">
                    </div>

                    <div class="ge-login-row">
                        <label class="ge-login-check">
                            <input type="checkbox" id="flexCheckDefault">
                            Remember me
                        </label>
                        <a href="forgot-password/forgot.php" class="ge-login-forgot">Forgot password?</a>
                    </div>

                    <button class="btn-login-gold" type="submit">
                        <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                    </button>

                    <div class="ge-login-footer">
                        Don't have an account?
                        <a href="register.php">Register Now</a>
                    </div>
                </form>
            </div>

            <p class="ge-login-tagline">Trade · Grow · Earn · Empower</p>
            <p class="ge-login-copy">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </main>
    </div>

    <script src="../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
