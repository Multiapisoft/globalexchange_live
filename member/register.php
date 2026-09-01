<?php
include_once '../lib/config.php';
if (SITE_WORKING_STATUS) {
    echo '<center style="position: relative; top: 100px;"><h1>This site is under maintenance</h1></center>';
    die;
}
if (isset($_GET['r']) && !empty($_GET['r'])) {
    $uid = $_GET['r'];
} elseif (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $uid = $_GET['ref'];
} elseif (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
    $uid = $_SESSION['userid'];
} else {
    $uid = '';
}

$refer_login_id = '';
$refer_locked = false;
if (!empty($uid)) {
    $ref_user = get_user_details($uid);
    if ($ref_user && isset($ref_user->login_id)) {
        $refer_login_id = $ref_user->login_id;
        $refer_locked = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo SITE_NAME; ?> | Member Register</title>
    <link rel="shortcut icon" href="theme/assets/favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
        integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="theme/css/panel.css" rel="stylesheet" type="text/css" />
    <link href="theme/css/login-auth.css" rel="stylesheet" type="text/css" />
    <link href="theme/css/register-auth.css" rel="stylesheet" type="text/css" />
</head>

<body class="ge-login-page">

    <div class="ge-login-shell">
        <aside class="ge-login-chart" aria-hidden="true">
            <div class="ge-login-chart-head">
                <div class="pair">
                    <span class="live-dot" aria-hidden="true"></span>
                    GBP/USD · Live Forex
                </div>
                <span class="pair"><span>●</span> Market Open</span>
            </div>
            <div class="ge-tradingview-wrap">
                <div class="tradingview-widget-container">
                    <div class="tradingview-widget-container__widget"></div>
                    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                    {
                        "autosize": true,
                        "symbol": "FX:GBPUSD",
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

        <main class="ge-login-panel ge-register-panel">
            <div class="ge-login-card balance-card animate-in">
                <form action="register_model.php" id="loginForm" class="ge-register-form" method="post">
                    <?php echo getMessage(); ?>

                    <a href="index.php">
                        <img src="theme/assets/logo.png" class="ge-login-logo" alt="<?php echo SITE_NAME; ?>">
                    </a>
                    <h1 class="ge-login-title">Create <span class="text-gold">Account</span></h1>
                    <p class="ge-login-subtitle">Trade Smart, Grow Faster — join <?php echo SITE_NAME; ?> today</p>

                    <div class="sec1 showSection">
                        <p class="ge-reg-step-label"><i class="fa fa-user-circle"></i> Account Information</p>

                        <div class="ge-login-field">
                            <label for="refer_id">Referral Code</label>
                            <input type="text" class="form-control"
                                placeholder="<?php echo $refer_locked ? $refer_login_id : 'Enter referral code'; ?>"
                                id="refer_id" name="refer_id" maxlength="20"
                                value="<?php echo htmlspecialchars($refer_login_id); ?>"
                                required="required"
                                <?php echo $refer_locked ? 'readonly' : ''; ?>
                                onBlur="check_active_user(this.value);">
                            <span id="sponser" name="sponser" class="form-hint"></span>
                        </div>

                        <div class="ge-login-field">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" placeholder="Enter your full name" id="name"
                                name="name" maxlength="50" required="required" pattern="[a-zA-Z ]+"
                                onBlur="check_name(this.value);">
                            <span id="name_error" name="name_error" class="form-hint"></span>
                        </div>

                        <div class="ge-login-field">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" placeholder="Enter your email address"
                                onBlur="check_email_domain(this.value);" id="email" name="email" maxlength="100"
                                required="required">
                            <span id="email_error" name="email_error" class="form-hint"></span>
                        </div>

                        <button type="button" class="btn-login-gold nextStep" disabled>
                            Continue <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="sec3">
                        <p class="ge-reg-step-label"><i class="fa fa-shield-alt"></i> Security &amp; Verification</p>

                        <div class="ge-login-field">
                            <label for="country">Country / Region</label>
                            <select class="form-control" id="country" name="country" required="required">
                                <option value="" disabled="disabled" selected="selected">Search and select your country</option>
                                <?php
                                $result2 = my_query("SELECT country_id, short_name, calling_code FROM country");
                                while ($row2 = my_fetch_object($result2)) {
                                    echo '<option value="' . (int) $row2->country_id . '">'
                                        . htmlspecialchars($row2->short_name . ' (+' . $row2->calling_code . ')')
                                        . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="ge-login-field">
                            <label for="mobile">Mobile Number</label>
                            <input type="text" class="form-control" placeholder="Enter your mobile number"
                                id="mobile" name="mobile" maxlength="10" required="required" pattern="[0-9]{10,10}">
                            <span id="mobile_error" name="mobile_error" class="form-hint"></span>
                        </div>

                        <p class="ge-reg-step-label ge-reg-step-label--sm"><i class="fa fa-lock"></i> Create Password</p>

                        <div class="ge-login-field">
                            <label for="password">Password</label>
                            <div class="ge-password-wrap">
                                <input type="password" class="form-control" placeholder="Create a strong password"
                                    id="password" name="password" maxlength="20" required="required"
                                    onchange="form.confirm_password.pattern = this.value;">
                                <span class="password-toggle" onclick="togglePasswordVisibility('password')">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                            <div class="password-strength-meter">
                                <div class="strength-bar"></div>
                                <span class="strength-text">Password strength</span>
                            </div>
                        </div>

                        <div class="ge-login-field">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="ge-password-wrap">
                                <input type="password" class="form-control" placeholder="Confirm your password"
                                    id="confirm_password" name="confirm_password" maxlength="20" required="required">
                                <span class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="ge-login-check ge-reg-terms">
                            <input id="checkbox3" type="checkbox" required="required">
                            <label for="checkbox3">I agree to the <a href="term.php" target="_blank">Terms and Conditions</a></label>
                        </div>

                        <div class="ge-reg-actions">
                            <button type="button" class="ge-reg-back prevStep2">
                                <i class="fa fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" id="submit" class="btn-login-gold" disabled>
                                <i class="fa fa-user-plus"></i> Create Account
                            </button>
                        </div>
                    </div>

                    <div class="ge-login-footer">
                        Already have an account?
                        <a href="index.php">Login</a>
                    </div>
                </form>
            </div>

            <p class="ge-login-tagline">Trade · Grow · Earn · Empower</p>
            <p class="ge-login-copy">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </main>
    </div>

    <script src="../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#country').select2({
                placeholder: 'Search and select your country',
                width: '100%',
                dropdownCssClass: 'trading-dropdown',
                allowClear: true,
                minimumInputLength: 0,
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    if (typeof data.text === 'undefined') {
                        return null;
                    }
                    var searchTerm = params.term.toLowerCase();
                    var optionText = data.text.toLowerCase();
                    if (optionText.indexOf(searchTerm) > -1) {
                        return data;
                    }
                    return null;
                }
            });
        });

        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const icon = event.currentTarget.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        let isLoginValid = true;
        let isNameValid = false;
        let isMobileValid = false;
        let isEmailValid = false;
        let isSendOtp = false;

        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            if (/[0-9]/.test(name)) {
                $("#name_error").html("Name cannot contain numbers").css("color", "red");
                isNameValid = false;
            } else if (name.length < 3) {
                $("#name_error").html("Name must be at least 3 characters").css("color", "#d4af37");
                isNameValid = false;
            } else {
                $("#name_error").html("Valid name").css("color", "green");
                isNameValid = true;
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.querySelector('.strength-bar');
            const strengthText = document.querySelector('.strength-text');
            let strength = 0;
            if (password.length > 6) strength += 20;
            if (password.length > 10) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            strengthBar.style.width = strength + '%';
            if (strength < 40) {
                strengthBar.style.backgroundColor = '#ef4444';
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#ef4444';
            } else if (strength < 80) {
                strengthBar.style.backgroundColor = '#d4af37';
                strengthText.textContent = 'Medium password';
                strengthText.style.color = '#d4af37';
            } else {
                strengthBar.style.backgroundColor = '#22c55e';
                strengthText.textContent = 'Strong password';
                strengthText.style.color = '#22c55e';
            }
        });

        function toggleInputsForInactiveUser(disable) {
            const $fields = $('input, select, textarea, button').not('#refer_id');
            if (disable) {
                $fields.each(function() {
                    if (!$(this).prop('disabled')) {
                        $(this).data('lockedInactive', true);
                    }
                    $(this).prop('disabled', true);
                });
            } else {
                $fields.each(function() {
                    if ($(this).data('lockedInactive')) {
                        $(this).prop('disabled', false);
                        $(this).removeData('lockedInactive');
                    }
                });
            }
        }

        function check_active_user(refer_id) {
            if (!refer_id) {
                return;
            }
            $.get("../lib/get_availability.php", {
                action: 'active_user',
                refer_id: refer_id
            }, function(data) {
                if (data.invalid) {
                    $("#sponser").html(data.name + " - User is active.").css("color", "green");
                    toggleInputsForInactiveUser(false);
                } else {
                    $("#sponser").html("User is not active.").css("color", "red");
                    toggleInputsForInactiveUser(true);
                }
            }, "json").fail(function() {
                $("#sponser").html("Error validating user.");
            });
        }

        function check_name(name) {
            const hasNumbers = /[0-9]/.test(name);
            if (hasNumbers) {
                $("#name_error").html("Name cannot contain numbers").css("color", "red");
                $("#submit").prop("disabled", true);
                $('.nextStep').prop("disabled", true);
                isNameValid = false;
                return false;
            } else if (name.length < 3) {
                $("#name_error").html("Name must be at least 3 characters").css("color", "#d4af37");
                $("#submit").prop("disabled", true);
                $('.nextStep').prop("disabled", true);
                isNameValid = false;
                return false;
            } else {
                $("#name_error").html("Valid name").css("color", "green");
                isNameValid = true;
                if (isLoginValid && isEmailValid && isSendOtp) {
                    $('.nextStep').prop("disabled", false);
                }
                return true;
            }
        }

        function check_email_domain(email) {
            const allowedDomains = [
                'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
                'live.com', 'msn.com', 'rediffmail.com', 'ymail.com',
                'aol.com', 'icloud.com', 'protonmail.com', 'zoho.com'
            ];
            const emailParts = email.trim().toLowerCase().split('@');
            if (emailParts.length !== 2) {
                $("#email_error").html("Invalid email format").css("color", "red");
                $("#submit").prop("disabled", true);
                $('.nextStep').prop("disabled", true);
                isEmailValid = false;
                return false;
            }
            const domainPart = emailParts[1];
            if (!allowedDomains.includes(domainPart)) {
                $("#email_error").html("Please use allowed domains only: Gmail, Yahoo, Hotmail, Outlook, etc.").css("color", "red");
                $("#submit").prop("disabled", true);
                $('.nextStep').prop("disabled", true);
                isEmailValid = false;
                return false;
            }
            $("#email_error").html("Domain verified").css("color", "green");
            $("#submit").prop("disabled", false);
            $('.nextStep').prop("disabled", false);
            isEmailValid = true;
            return true;
        }

        $('.nextStep').on('click', function() {
            isNameValid = check_name($("#name").val());
            if (!$(this).prop('disabled') && isEmailValid && isLoginValid && isNameValid) {
                $(".sec1").removeClass("showSection").hide();
                $(".sec3").show().css("display", "block").addClass("showSection");
            } else {
                if (!isNameValid) {
                    $("#name_error").html("Please enter a valid name").css("color", "red");
                }
                if (!isEmailValid) {
                    $("#email_error").html("Please verify your email").css("color", "red");
                }
            }
        });

        $('.prevStep2').on('click', function() {
            $(".sec3").hide().removeClass("showSection");
            $(".sec1").show().css("display", "block").addClass("showSection");
        });

        document.addEventListener("contextmenu", function(event) {
            event.preventDefault();
        });
    </script>
</body>

</html>
