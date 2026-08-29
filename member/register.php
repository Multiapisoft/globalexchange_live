<?php include_once '../lib/config.php';
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


//redirect('../register.php?r='.$uid);die;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $title_name = isset($title) ? SITE_NAME . ' | ' . $title : SITE_NAME . ' | Member Register'; ?></title>
    <link rel="shortcut icon" href="images/nexabot-logo.png" type="image/x-icon">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ['Alegreya+Sans:100,100i,300,300i,400,400i,500,500i,700,700i,800,800i,900,900i', 'Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i', 'Open Sans']
            }
        });
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (Ensure jQuery is loaded first) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 JS (after jQuery) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Bootstrap -->
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap rtl -->
    <!--<link href="../assets/bootstrap-rtl/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/>-->
    <!-- Pe-icon-7-stroke -->
    <link href="../assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet" type="text/css" />
    <link href="../assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="../assets/dist/css/component_ui.min.css" rel="stylesheet" type="text/css" />
    <!--<link href="../assets/dist/css/skins/component_ui_black.css" rel="stylesheet" type="text/css"/>-->
    <!-- Theme style rtl -->
    <!--<link href="../assets/dist/css/component_ui_rtl.css" rel="stylesheet" type="text/css"/>-->
    <!-- Custom css -->
    <link href="../assets/dist/css/custom.css" rel="stylesheet" type="text/css" />
    <style>
        /* Base Styles - Modern Dark Theme */
        body,
        #page-wrapper {
            background: linear-gradient(135deg, #0b0e11 0%, #181c27 100%);
            color: #eaecef;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            min-height: 100vh;
        }

        .register-wrapper {
            padding: 20px;
            color: #eaecef;
            margin: 0 auto;
            background: linear-gradient(135deg, #0b0e11 0%, #181c27 100%);
            min-height: 100vh;
        }

        .view-header {
            overflow: visible !important;
        }

        .view-header .header-title {
            margin-left: 0px !important;
            overflow: visible !important;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.2);
            color: #eaecef;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 14px 18px;
            height: auto;
            transition: all 0.3s ease;
            font-size: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.3);
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25), 0 4px 12px rgba(0, 0, 0, 0.3);
            color: #ffffff;
            outline: none;
        }

        .form-control::placeholder {
            color: #848e9c !important;
            opacity: 1;
        }

        .form-control:-ms-input-placeholder {
            color: #848e9c !important;
        }

        .form-control::-ms-input-placeholder {
            color: #848e9c !important;
        }

        /* Additional placeholder fixes for all input types */
        input::placeholder,
        select::placeholder,
        textarea::placeholder {
            color: #848e9c !important;
            opacity: 1;
        }

        input:-ms-input-placeholder,
        select:-ms-input-placeholder,
        textarea:-ms-input-placeholder {
            color: #848e9c !important;
        }

        input::-ms-input-placeholder,
        select::-ms-input-placeholder,
        textarea::-ms-input-placeholder {
            color: #848e9c !important;
        }

        .view-header .header-icon {
            font-size: 60px;
            margin-bottom: 19px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container-center.lg {
            max-width: 800px !important;
            width: 100%;
        }

        .panel-heading {
            padding: 20px !important;
        }

        #login {
            color: #0ecb81;
        }

        .form-group>input {
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            background: rgba(0, 0, 0, 0.2) !important;
            color: #eaecef !important;
            padding: 14px 18px;
            height: auto;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .form-group>input:focus {
            background: rgba(0, 0, 0, 0.3) !important;
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25), 0 4px 12px rgba(0, 0, 0, 0.3) !important;
            color: #ffffff !important;
            outline: none;
        }

        .form-group>select {
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            background: rgba(0, 0, 0, 0.2) !important;
            color: #eaecef !important;
            padding: 14px 18px;
            height: auto;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .form-group>select:focus {
            background: rgba(0, 0, 0, 0.3) !important;
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25), 0 4px 12px rgba(0, 0, 0, 0.3) !important;
            color: #ffffff !important;
            outline: none;
        }

        @media (max-width: 768px) {
            .register-wrapper {
                padding: 10px;
            }

            .container-center.lg,
            .container-center.lg .panel {
                max-width: 100% !important;
                width: 100% !important;
            }

            .panel.panel-bd {
                border-radius: 0;
                box-shadow: none;
            }

            .panel-heading {
                padding: 15px !important;
            }

            .sameSection {
                padding: 20px;
                border-radius: 14px;
            }

            .header-title {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
        }

        .sameSection {
            background: linear-gradient(135deg, #181c27 0%, #0b0e11 100%);
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), 0 4px 12px rgba(0, 0, 0, 0.2);
            border-radius: 20px;
            margin: 20px 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .sameSection::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #06b6d4);
            border-radius: 20px 20px 0 0;
        }

        .sameSection::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .btn {
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
            text-transform: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            color: #ffffff !important;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(79, 70, 229, 0.5);
            color: #ffffff !important;
        }

        .btn-primary:disabled {
            background: rgba(79, 70, 229, 0.35);
            color: rgba(226, 232, 240, 0.7) !important;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-primary:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .btn-primary i {
            font-size: 14px;
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

        .nextStep,
        .nextStep2 {
            padding: 12px 28px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nextStep:hover,
        .nextStep2:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(79, 70, 229, 0.5);
        }

        .prevStep,
        .prevStep2 {
            padding: 12px 28px;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
            color: #eaecef !important;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .prevStep:hover,
        .prevStep2:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .sec2,
        .sec3 {
            display: none;
        }

        @keyframes slideUpBounce {
            0% {
                transform: translateY(30px);
                opacity: 0;
            }

            70% {
                transform: translateY(-5px);
                opacity: 0.9;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .showSection {
            animation: slideUpBounce 0.6s cubic-bezier(0.25, 1, 0.5, 1) forwards;
            display: block;
            opacity: 1;
        }

        .panel-bd {
            background: linear-gradient(135deg, #181c27 0%, #0b0e11 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 4px 12px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .panel-bd>.panel-heading {
            color: #eaecef;
            background: linear-gradient(135deg, #1e2329 0%, #181c27 100%);
            border-color: rgba(255, 255, 255, 0.1);
            border-top-right-radius: 20px;
            border-top-left-radius: 20px;
            position: relative;
            overflow: visible;
        }

        .panel-bd>.panel-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #4f46e5, #7c3aed, transparent);
        }

        /* Form Elements Enhancement */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            color: #eaecef !important;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        /* Input Focus Animation */
        .form-group>input:focus,
        .form-group>select:focus {
            animation: inputFocus 0.3s ease-out;
        }

        @keyframes inputFocus {
            0% {
                transform: scale(0.98);
            }

            50% {
                transform: scale(1.01);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Checkbox Styling */
        .checkbox-success input[type="checkbox"] {
            position: relative;
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: none;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .checkbox-success input[type="checkbox"]:checked {
            background: #4f46e5;
            border-color: #4f46e5;
        }

        .checkbox-success input[type="checkbox"]:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .checkbox-success input[type="checkbox"]:checked::before {
            content: '✓';
            position: absolute;
            top: 1px;
            left: 5px;
            font-size: 12px;
            color: #ffffff;
            font-weight: bold;
        }

        .checkbox-success label {
            color: #eaecef !important;
            cursor: pointer;
            font-weight: 500;
        }

        .checkbox-success label a {
            color: #4f46e5 !important;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .checkbox-success label a:hover {
            color: #4338ca !important;
            text-decoration: underline;
        }

        /* OTP Field Styling */
        #otpField,
        #otp2 {
            position: relative;
            margin-top: 15px;
        }

        #otpStatus {
            display: block;
            margin-top: 8px;
            font-size: 13px;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            background-color: rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            height: 50px;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25), 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #eaecef;
            line-height: 50px;
            padding-left: 18px;
            font-size: 15px;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #848e9c;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            right: 15px;
        }

        .select2-dropdown {
            background-color: #1e2329;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            z-index: 9999;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #4f46e5;
            color: #ffffff;
        }

        .select2-container--default .select2-search--dropdown {
            padding: 10px;
            background: #181c27;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.1);
            color: #eaecef;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #f1f5f9;
            color: #334155;
        }

        .select2-container--default .select2-results__option {
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .select2-container--default .select2-results__option:hover {
            background-color: #f8fafc;
        }

        /* Search icon in dropdown */
        .select2-search--dropdown::before {
            content: '\f002';
            font-family: 'FontAwesome';
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            transition: all 0.3s ease;
            z-index: 1;
            pointer-events: none;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            padding-left: 35px;
        }

        /* Bottom Text - Dark Theme */
        #bottom_text {
            text-align: center;
            padding: 20px;
            color: #eaecef !important;
            /* border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(135deg, #181c27 0%, #0b0e11 100%); */
        }

        #bottom_text a {
            color: #4f46e5 !important;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
            background: rgba(79, 70, 229, 0.1);
            border: 1px solid rgba(79, 70, 229, 0.35);
        }

        #bottom_text a:hover {
            color: #ffffff !important;
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            border-color: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        /* Verification Buttons */
        #sendOtpBtn,
        #veriFyOtp,
        #sendOtpBtn2,
        #verifyOtpBtn2 {
            margin-top: 10px;
            width: auto;
            display: inline-block;
        }

        /* Error Messages */
        #login,
        #email_error,
        #mobile_error,
        #sponser,
        #otpStatus {
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        /* Animation for Success Messages */
        .text-success {
            color: #0ecb81 !important;
            animation: successPulse 2s infinite alternate;
        }

        @keyframes successPulse {
            0% {
                opacity: 0.8;
            }

            100% {
                opacity: 1;
            }
        }

        /* Animation for Error Messages */
        .text-danger {
            color: #f6465d !important;
            animation: errorShake 0.5s ease-in-out;
        }

        @keyframes errorShake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        /* Trading Platform Theme Elements */
        .trading-theme-element {
            position: absolute;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, transparent 70%);
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
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 0.5;
            }

            100% {
                transform: translate(10px, 10px) scale(1.1);
                opacity: 0.8;
            }
        }

        /* Header Title Enhancement - Dark Theme */
        .header-title {
            overflow: visible !important;
        }

        .header-title h4 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            padding: 5px 0 10px 0;
            line-height: 1.5;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: left;
            display: inline-block;
            overflow: visible;
            height: auto;
            position: relative;
            text-shadow: none;
            min-height: 48px;
        }

        @keyframes titleGlow {
            0% {
                text-shadow: 0 0 5px rgba(79, 70, 229, 0.3);
            }

            100% {
                text-shadow: 0 0 15px rgba(79, 70, 229, 0.6);
            }
        }

        /* Login Link Enhancement - Dark Theme */
        .header-title a {
            position: relative;
            transition: all 0.3s ease;
            color: #eaecef !important;
            text-decoration: none;
            font-size: 14px;
        }

        .header-title a:hover {
            transform: translateY(-1px);
            color: #4f46e5 !important;
        }

        .header-title a span {
            color: #4f46e5 !important;
            font-weight: 700;
            position: relative;
        }

        .header-title a span::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #4f46e5;
            transition: width 0.3s ease;
        }

        .header-title a:hover span::after {
            width: 100%;
        }

        /* Input with Icon Styling */
        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .input-with-icon input,
        .input-with-icon select {
            padding-left: 50px;
        }

        .input-with-icon input:focus+i,
        .input-with-icon select:focus+i {
            color: #4f46e5;
        }

        /* Section Title */
        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 24px;
            color: #eaecef !important;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            padding-bottom: 12px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .section-title i {
            color: #4f46e5 !important;
            font-size: 18px;
        }

        /* Form Hint */
        .form-hint {
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* Verification Section */
        .verification-section {
            margin: 15px 0;
            padding: 15px;
            background: rgba(79, 70, 229, 0.08);
            border-radius: 8px;
            border: 1px dashed rgba(79, 70, 229, 0.35);
        }

        .verification-btn {
            margin-right: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #848e9c;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: #4f46e5;
        }

        /* Password Strength Meter */
        .password-strength-meter {
            margin-top: 8px;
            height: 4px;
            background: #2c3137;
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .strength-text {
            font-size: 11px;
            color: #848e9c;
            margin-top: 4px;
            display: block;
        }

        /* Form Navigation - Dark Theme */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            .form-navigation {
                flex-direction: column-reverse;
            }

            .form-navigation button {
                width: 100%;
                justify-content: center;
            }
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #eaecef !important;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 12px 28px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary i {
            font-size: 14px;
        }

        /* Terms Section - Dark Theme */
        .terms-section {
            margin: 24px 0;
            padding: 20px;
            background: rgba(240, 185, 11, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(240, 185, 11, 0.2);
            backdrop-filter: blur(10px);
        }

        .terms-section .checkbox-success {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .terms-section .checkbox-success input[type="checkbox"]:checked {
            background: #f0b90b;
            border-color: #f0b90b;
        }

        /* Trading Dropdown */
        .trading-dropdown {
            background: #1e2329;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>

<body>
    <!-- Content Wrapper -->
    <div class="register-wrapper">
        <div class="container-center lg">
            <div class="panel panel-bd">
                <!-- Trading Theme Elements -->
                <div class="trading-theme-element element-1"></div>
                <div class="trading-theme-element element-2"></div>
                <div class="trading-theme-element element-3"></div>

                <div class="panel-heading">
                    <div class="view-header">
                        <div class="header-icon">
                            <img src="images/nexabot-logo.png" style="width: 173px;">
                        </div>

                        <div class="header-title" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <h4>Sign up</h4>
                            <a href="index.php">
                                Already have an account? <span>Login</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <form action="register_model.php" id="loginForm" method="post">
                        <?php echo getMessage(); ?>
                        <!--Social Buttons-->
                        <?php /*<div class="social">
                                <strong>Register in using social network:</strong><br>
                                <div class="twitter_bg"><i class="fa fa-twitter"></i><a href="#" class="btn_1">Login Twitter</a></div>
                                <div class="fb_bg"><i class="fa fa-facebook"></i><a href="#" class="btn_2">Login Facebook</a></div>
                            </div>*/ ?>
                        <?php /*<div class="form-group has-feedback">
                                <input type="text" class="form-control" placeholder="Pin" id="pin_no" name="pin_no" value="<?php echo (isset($_GET['p'])) ? $_GET['p'] : '';?>" maxlength="30" required="required" pattern="[0-9]{8,30}" onblur="check_pin(this.value);" /><span id="pin_error" name="pin_error"></span>
                            </div>*/ ?>


                        <!--------------------------------------------------------------------------------------------------------------------------------------------------------------------->





                        <!--  SECTION 1 OF FORM -->
                        <div class="sec1 sameSection">
                            <div class="section-title">
                                <i class="fa fa-user-circle"></i> Account Information
                            </div>
                            <div class="form-group has-feedback">
                                <label for="refer_id">Referral Code</label>
                                <div class="input-with-icon">
                                    <i class="fa fa-user-plus"></i>
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="<?php echo $uid ? $uid : 'Enter referral code '; ?>"
                                        id="refer_id"
                                        name="refer_id"
                                        maxlength="20"
                                        value="<?php echo get_user_details($uid)->login_id; ?>"
                                        required="required"
                                        <?php echo $uid ? 'readonly' : ''; ?>
                                        onBlur="check_active_user(this.value);" />
                                </div>
                                <span id="sponser" name="sponser" class="form-hint"></span>
                            </div>
                            <?php /*<div class="form-group has-feedback">
                                <select name="position" id="position" class="form-control" style="width:100%;" required="required">
                                    <option value="" disabled="disabled" selected="selected">-- Select Position --</option>
                                    <option value="L" <?php if((isset($_SESSION['position']) && $_SESSION['position']=='L') || (isset($_GET['p']) && $_GET['p']=='L')){echo "selected='selected'";}elseif(isset($_SESSION['position']) && $_SESSION['position']=='R'){echo "disabled='disabled'";}?>>Left</option>
                                    <option value="R" <?php if((isset($_SESSION['position']) && $_SESSION['position']=='R') || (isset($_GET['p']) && $_GET['p']=='R')){echo "selected='selected'";}elseif(isset($_SESSION['position']) && $_SESSION['position']=='L'){echo "disabled='disabled'";}?>>Right</option>
                                </select>
                            </div>*/?>
                            <div class="form-group has-feedback">
                                <label for="name">Full Name</label>
                                <div class="input-with-icon">
                                    <i class="fa fa-user"></i>
                                    <input type="text" class="form-control" placeholder="Enter your full name" id="name" name="name" maxlength="50" required="required" pattern="[a-zA-Z ]+" onBlur="check_name(this.value);" />
                                </div>
                                <span id="name_error" name="name_error" class="form-hint"></span>
                            </div>

                            <!--<div class="form-group has-feedback">-->
                            <!--    <label for="login_id">Username</label>-->
                            <!--    <div class="input-with-icon">-->
                            <!--        <i class="fa fa-id-card"></i>-->
                            <!--        <input type="text" class="form-control" placeholder="Choose a unique username" id="login_id" name="login_id" maxlength="20" required="required" onBlur="check_login_id(this.value);" />-->
                            <!--    </div>-->
                            <!--    <span id="login" name="login" class="form-hint"></span>-->
                            <!--</div>-->

                            <div class="form-group has-feedback">
                                <label for="email">Email Address</label>
                                <div class="input-with-icon">
                                    <i class="fa fa-envelope"></i>
                                    <!-- <input type="email" class="form-control" placeholder="Enter your email address" onBlur="check_email(this.value);" id="email" name="email" maxlength="100" required="required" /> -->
                                    <input type="email" class="form-control" placeholder="Enter your email address" onBlur="check_email_domain(this.value);" id="email" name="email" maxlength="100" required="required" />
                                </div>
                                <span id="email_error" name="email_error" class="form-hint"></span>
                            </div>

                            <!--<div class="verification-section">-->
                            <!--    <button id="sendOtpBtn" class="btn btn-primary verification-btn" disabled>-->
                            <!--        <i class="fa fa-paper-plane"></i> Send OTP-->
                            <!--    </button>-->

                            <!--    <div id="otpField" class="form-group has-feedback">-->
                            <!--        <label for="otp">Email Verification Code</label>-->
                            <!--        <div class="input-with-icon">-->
                            <!--            <i class="fa fa-key"></i>-->
                            <!--            <input type="text" class="form-control" placeholder="Enter verification code" id="otp" name="otp" maxlength="20" required="required" />-->
                            <!--        </div>-->
                            <!--        <button id="veriFyOtp" class="btn btn-primary verification-btn">-->
                            <!--            <i class="fa fa-check-circle"></i> Verify Code-->
                            <!--        </button>-->
                            <!--        <span id="otpStatus" class="form-hint"></span>-->
                            <!--    </div>-->
                            <!--</div>-->

                            <div class="form-navigation">
                                <button type="" id="" class="nextStep btn btn-primary" disabled>
                                    Continue <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>




                        <div class="sec3 sameSection">
                            <div class="section-title">
                                <i class="fa fa-shield"></i> Security & Verification
                            </div>

                            <div class="form-group has-feedback">
                                <label for="country">Country/Region <small style="color: #848e9c !important; font-weight: normal;">(Type to search)</small></label>
                                <div class="input-with-icon">
                                    <i class="fa fa-globe"></i>
                                    <select class="form-control" id="country" name="country" required="required" style="width: 100%">
                                        <option value="" disabled="disabled" selected="selected">Type to search and select your country</option>
                                        <?php
                                        $result2 = my_query("SELECT country_id, short_name, calling_code FROM country");
                                        while ($row2 = my_fetch_object($result2)) {
                                        ?>
                                            <option value="<?php echo $row2->country_id; ?>"><?php echo $row2->short_name . ' (+' . $row2->calling_code . ')'; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <script>
                                var $jq = jQuery.noConflict();

                                // Initialize Select2 once the DOM is ready and jQuery/Select2 are loaded
                                $jq(document).ready(function() {
                                    $jq('#country').select2({
                                        placeholder: 'Search and select your country',
                                        width: '100%', // Ensures full-width selection box
                                        dropdownCssClass: "trading-dropdown",
                                        allowClear: true, // Allows clearing the selection
                                        minimumInputLength: 0, // Start searching from first character
                                        matcher: function(params, data) {
                                            // If there are no search terms, return all data
                                            if ($.trim(params.term) === '') {
                                                return data;
                                            }

                                            // Do not display the item if there is no 'text' property
                                            if (typeof data.text === 'undefined') {
                                                return null;
                                            }

                                            // `params.term` is the user's search term
                                            var searchTerm = params.term.toLowerCase();
                                            var optionText = data.text.toLowerCase();

                                            // Check if the text contains the search term
                                            if (optionText.indexOf(searchTerm) > -1) {
                                                return data;
                                            }

                                            // Return `null` if the term should not be displayed
                                            return null;
                                        }
                                    });
                                });
                            </script>

                            <div class="form-group has-feedback">
                                <label for="mobile">Mobile Number</label>
                                <div class="input-with-icon">
                                    <i class="fa fa-phone"></i>
                                    <!-- <input type="text" class="form-control" placeholder="Enter your mobile number" id="mobile" name="mobile" maxlength="10" onBlur="check_mobile(this.value);" required="required" pattern="[0-9]{10,10}" /> -->
                                    <input type="text" class="form-control" placeholder="Enter your mobile number" id="mobile" name="mobile" maxlength="10"  required="required" pattern="[0-9]{10,10}" />
                                </div>
                                <span id="mobile_error" name="mobile_error" class="form-hint"></span>
                            </div>

                            <!--<div class="verification-section">-->
                            <!--    <button type="button" id="sendOtpBtn2" class="btn btn-primary verification-btn" disabled>-->
                            <!--        <i class="fa fa-paper-plane"></i> Send OTP-->
                            <!--    </button>-->

                            <!--    <div class="form-group">-->
                            <!--        <label for="otp2">Mobile Verification Code</label>-->
                            <!--        <div class="input-with-icon">-->
                            <!--            <i class="fa fa-key"></i>-->
                            <!--            <input type="text" class="form-control" placeholder="Enter verification code" id="otp2" name="otp" maxlength="4" />-->
                            <!--        </div>-->
                            <!--        <span id="otp_error" name="otp_error" class="form-hint"></span>-->
                            <!--    </div>-->

                            <!--    <button type="button" id="verifyOtpBtn2" class="btn btn-primary verification-btn" disabled>-->
                            <!--        <i class="fa fa-check-circle"></i> Verify Code-->
                            <!--    </button>-->
                            <!--</div>-->
                            <script>
                                document.getElementById("sendOtpBtn2").addEventListener("click", function() {
                                    const mobile = document.getElementById("mobile").value;
                                    if (mobile && mobile.length === 10) {
                                        $("#sendOtpBtn2").attr("disabled", "true");
                                        sendOtp(mobile);
                                        setTimeout(() => {
                                            $("#sendOtpBtn2").removeAttr("disabled");
                                        }, 60000);
                                    } else {
                                        alert("Please enter a valid mobile number");
                                    }
                                });

                                document.getElementById("verifyOtpBtn2").addEventListener("click", function() {
                                    const otp = document.getElementById("otp2").value;
                                    const requestId = sessionStorage.getItem("requestId");
                                    if (otp && requestId) {
                                        verifyOtp2(requestId, otp);
                                    } else {
                                        alert("Please enter OTP");
                                    }
                                });

                                function sendOtp(mobile) {
                                    const apiKey = "<api-key>"; // Replace with your actual API key
                                    const clientId = "SKDIAGIQVENDA8ID3H2T53SV4Z8QEEFS"; // Replace with your client ID
                                    const clientSecret = "805r9z2uzpqylxrrrt0bh0y67l8z1qv3"; // Replace with your client secret

                                    const url = "https://auth.otpless.app/auth/v1/initiate/otp";
                                    const data = {
                                        phoneNumber: `91${mobile}`, // Prepending +91 for Indian numbers
                                        expiry: 120, // OTP expiration time in seconds
                                        otpLength: 4, // OTP length (can also be 6)
                                        channels: ["SMS"], // The channel(s) through which OTP will be sent (as an array)
                                    };

                                    fetch(url, {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json",
                                                "clientId": clientId, // Authorization header with client ID
                                                "clientSecret": clientSecret // Authorization header with client secret
                                            },
                                            body: JSON.stringify(data)
                                        })
                                        .then(response => {
                                            console.log("Response Status:", response.status); // Log response status
                                            return response.json(); // Parse the JSON response
                                        })
                                        .then(data => {
                                            console.log("Response Data:", data); // Log the response data

                                            if (data.requestId) { // Check if requestId exists in the response
                                                sessionStorage.setItem("requestId", data.requestId); // Store requestId for later OTP verification
                                                document.getElementById("verifyOtpBtn2").disabled = false; // Enable OTP verification button
                                                alert("OTP sent successfully!");
                                            } else {
                                                alert("Failed to send OTP: " + JSON.stringify(data)); // Log error details if requestId is missing
                                            }
                                        })
                                        .catch(error => {
                                            console.error("Error:", error); // Log full error details
                                            alert("An error occurred while sending OTP: " + error.message); // Display error message
                                        });
                                }


                                function verifyOtp2(requestId, otp) {
                                    const apiKey = "<api-key>";
                                    const clientId = "SKDIAGIQVENDA8ID3H2T53SV4Z8QEEFS"; // Replace with your client ID
                                    const clientSecret = "805r9z2uzpqylxrrrt0bh0y67l8z1qv3"; // Replace with your client secret

                                    const url = "https://auth.otpless.app/auth/v1/verify/otp";
                                    const data = {
                                        requestId: requestId,
                                        otp: otp
                                    };

                                    fetch(url, {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json",
                                                "clientId": clientId,
                                                "clientSecret": clientSecret
                                            },
                                            body: JSON.stringify(data)
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.isOTPVerified) {
                                                alert("OTP verified successfully!");
                                                document.getElementById("verifyOtpBtn2").disabled = true;
                                                $("#verifyOtpBtn2").attr("disabled", "true");
                                                $("#sendOtpBtn2").attr("disabled", "true");
                                                document.getElementById("submit").disabled = false;
                                                // Proceed with the form submission or other actions
                                            } else {
                                                alert("Invalid OTP");
                                            }
                                        })
                                        .catch(error => {
                                            console.error("Error:", error);
                                            alert("An error occurred while verifying OTP");
                                        });
                                }
                            </script>


                            <div class="password-section">
                                <div class="section-title">
                                    <i class="fa fa-lock"></i> Create Password
                                </div>

                                <div class="form-group has-feedback">
                                    <label for="password">Password</label>
                                    <div class="input-with-icon">
                                        <i class="fa fa-lock"></i>
                                        <input type="password" class="form-control" placeholder="Create a strong password" id="password" name="password" maxlength="20" required="required" onchange="form.confirm_password.pattern = this.value;" />
                                        <span class="password-toggle" onclick="togglePasswordVisibility('password')">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="password-strength-meter">
                                        <div class="strength-bar"></div>
                                        <span class="strength-text">Password strength</span>
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <label for="confirm_password">Confirm Password</label>
                                    <div class="input-with-icon">
                                        <i class="fa fa-lock"></i>
                                        <input type="password" class="form-control" placeholder="Confirm your password" id="confirm_password" name="confirm_password" maxlength="20" required="required" />
                                        <span class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>


                            </div>

                            <div class="terms-section">
                                <div class="checkbox checkbox-success">
                                    <input id="checkbox3" type="checkbox" required="required">
                                    <label for="checkbox3">I agree to the <a href="term.php" target="_blank">Terms and Conditions</a></label>
                                </div>
                            </div>

                            <div class="form-navigation">
                                <button type="button" class="prevStep2 btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>

                                <button type="submit" id="submit" class="btn btn-primary" disabled>
                                    <i class="fa fa-user-plus"></i> Create Account
                                </button>
                            </div>



                            <?php /*<div class="form-group has-feedback">
                                <select name="position" id="position" class="form-control" style="width:100%;" required="required">
                                    <option value="" disabled="disabled" selected="selected">-- Select Position --</option>
                                    <option value="L" <?php if((isset($_SESSION['position']) && $_SESSION['position']=='L') || (isset($_GET['p']) && $_GET['p']=='L')){echo "selected='selected'";}elseif(isset($_SESSION['position']) && $_SESSION['position']=='R'){echo "disabled='disabled'";}?>>Left</option>
                                    <option value="R" <?php if((isset($_SESSION['position']) && $_SESSION['position']=='R') || (isset($_GET['p']) && $_GET['p']=='R')){echo "selected='selected'";}elseif(isset($_SESSION['position']) && $_SESSION['position']=='L'){echo "disabled='disabled'";}?>>Right</option>
                                </select>
                            </div>*/ ?>
                            <?php /*<input type="hidden" name="refer_id" value="<?php echo get_user_details($uid)->login_id;?>" />*/ ?>





                            <?php /*<div class="form-group has-feedback">
                                <input type="text" class="form-control" placeholder="HTC Address" id="bnb_address" name="bnb_address" maxlength="100" />
                            </div>*/ ?>

                    </form>
                </div>
                <div id="bottom_text">
                    <a href="index.php">Go to Home</a>
                </div>
                <div class="col-12 text-center mt-4">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->
    <!-- jQuery -->
    <script src="../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
    <!-- bootstrap js -->
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script>
        // Password visibility toggle function
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

        // Real-time name validation
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            // Check for numbers in real-time
            if (/[0-9]/.test(name)) {
                $("#name_error").html("Name cannot contain numbers").css("color", "red");
                isNameValid = false;
            } else if (name.length < 3) {
                $("#name_error").html("Name must be at least 3 characters").css("color", "#efc816");
                isNameValid = false;
            } else {
                $("#name_error").html("Valid name").css("color", "green");
                isNameValid = true;
            }
        });

        // Password strength meter
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.querySelector('.strength-bar');
            const strengthText = document.querySelector('.strength-text');

            // Calculate password strength
            let strength = 0;
            if (password.length > 6) strength += 20;
            if (password.length > 10) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;

            // Update strength bar
            strengthBar.style.width = strength + '%';

            // Set color based on strength
            if (strength < 40) {
                strengthBar.style.backgroundColor = '#f6465d';
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#f6465d';
            } else if (strength < 80) {
                strengthBar.style.backgroundColor = '#f0b90b';
                strengthText.textContent = 'Medium password';
                strengthText.style.color = '#f0b90b';
            } else {
                strengthBar.style.backgroundColor = '#0ecb81';
                strengthText.textContent = 'Strong password';
                strengthText.style.color = '#0ecb81';
            }
        });

        function validateForm() {
            // Validate OTP if the field is visible
            const otpField = $('#otpField').is(':visible');
            if (otpField) {
                const otp = $('#otp').val();
                if (!otp) {
                    $('#otpError').text('Please enter the OTP.');
                    return false; // Prevent form submission
                }
            }
            return true; // Allow form submission
        }

        function check_pin(pin_no) {
            $.get("../lib/get_availability.php", {
                'action': 'pin_no',
                'pin_no': pin_no
            }, function(data) {
                if (data.invalid) {
                    $("#submit").attr("disabled", "true");
                    //document.getElementById('amount').value='';
                    $("#pin_error").html("Invalid pin");
                } else {
                    $("#pin_error").html("Valid pin");
                    //document.getElementById('amount').value=data.amount;
                    $("#submit").removeAttr("disabled");
                }
            }, "json");
        }

        function check_sponser(refer_id) {
            if (!refer_id) {
                // $("#sponser").html("Please enter a reference code.").css("color", "blue");
                return;
            }

            $.get("../lib/get_availability.php", {
                    action: 'sponsor',
                    refer_id: refer_id
                }, function(data) {
                    if (data.invalid) {
                        $("#sponser").html("Invalid Reference Code.").css("color", "red");
                    } else {
                        $("#sponser").html(`${data.name} - Valid reference code.`).css("color", "green");

                    }
                }, "json")
                .fail(function() {
                    $("#sponser").html("Error validating reference code.");
                });
        }
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
                // $("#sponser").html("Please enter a reference code.").css("color", "blue");
                return;
            }

            $.get("../lib/get_availability.php", {
                    action: 'active_user',
                    refer_id: refer_id
                }, function(data) {
                    if (data.invalid) {
                        $("#sponser").html(`${data.name} - User is active.`).css("color", "green");
                        toggleInputsForInactiveUser(false);
                    } else {
                        $("#sponser").html("User is not active.").css("color", "red");
                        toggleInputsForInactiveUser(true);
                    }
                }, "json")
                .fail(function() {
                    $("#sponser").html("Error validating user.");
                });
        }

        // Function to check name validity (no numeric characters)
        function check_name(name) {
            // Check if name contains any numeric characters
            const hasNumbers = /[0-9]/.test(name);

            if (hasNumbers) {
                $("#name_error").html("Name cannot contain numbers").css("color", "red");
                $("#submit").prop("disabled", true);
                $('.nextStep').prop("disabled", true);
                isNameValid = false;
                return false;
            } else if (name.length < 3) {
                $("#name_error").html("Name must be at least 3 characters").css("color", "#efc816");
                $("#submit").prop("disabled", true);
                $('.nextStep').prop("disabled", true);
                isNameValid = false;
                return false;
            } else {
                $("#name_error").html("Valid name").css("color", "green");
                // Set the name as valid
                isNameValid = true;

                // Check if we can enable the Next button (if other validations are also true)
                if (isLoginValid && isEmailValid && isSendOtp) {
                    $('.nextStep').prop("disabled", false);
                }

                return true;
            }
        }

        let isLoginValid = true;
        let isNameValid = false;

        function check_login_id(login_id) {
            $.get("../lib/get_availability.php", {
                'action': 'login_id',
                'login_id': login_id
            }, function(data) {
                if (data.invalid) {
                    $("#submit").attr("disabled", "true");
                    $("#login").html("<?php echo SITE_LOGIN_ID_TEXT; ?> already exists").css("color", "red");
                    $('.nextStep').attr("disabled", "true"); // Keep the Next button disabled
                } else if (login_id.length < 6 || login_id.length > 20) {
                    $("#submit").attr("disabled", "true");
                    $("#login").html("<?php echo SITE_LOGIN_ID_TEXT; ?> min 6 or max 20 characters").css("color", "#efc816");
                    $('.nextStep').attr("disabled", "true"); // Keep the Next button disabled
                } else {
                    $("#login").html("Valid <?php echo strtolower(SITE_LOGIN_ID_TEXT); ?>").css("color", "green");
                    $("#submit").removeAttr("disabled");
                    $('.nextStep').attr("disabled", "false");
                    isLoginValid = true;
                }
            }, "json");
        }

        let isMobileValid = false;
        let isEmailValid = false;
        // Function to check mobile validity
        function check_mobile(mobile) {
            console.log(mobile)
            const pattern = /^[0-9]+$/;
            $.get("../lib/get_availability.php", {
                action: 'mobile',
                mobile: mobile
            }, function(data) {
                console.log(data)
                if (data.invalid) {
                    $("#submit").prop("disabled", true);
                    $("#mobile_error").html("Mobile already exists").css("color", "red");
                    $('.nextStep').prop("disabled", true); // Keep the Next button disabled
                } else if (mobile.length !== 10) {
                    $("#submit").prop("disabled", true);
                    $("#mobile_error").html("Mobile must be 10 digits").css("color", "#efc816");
                    $('.nextStep').prop("disabled", true); // Keep the Next button disabled
                } else if (!pattern.test(mobile)) {
                    $("#submit").prop("disabled", true);
                    $("#mobile_error").html("Mobile must contain only numbers").css("color", "red");
                    $('.nextStep').prop("disabled", true); // Keep the Next button disabled
                } else {
                    $("#mobile_error").html("Valid mobile number").css("color", "green");
                    $("#submit").prop("disabled", false);
                    $("#sendOtpBtn2").removeAttr('disabled');
                    $("#mobile").attr('readonly', 'readonly');
                    $('.nextStep').prop("disabled", false); // Enable the Next button
                    isMobileValid = true;
                }
            }, "json");
        }
        document.addEventListener("contextmenu", function(event) {
            event.preventDefault();
        });

        // Function to check email validity
        function check_email(email) {
            // Updated pattern to allow multiple popular email providers
            const allowedDomains = [
                'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
                'live.com', 'msn.com', 'rediffmail.com', 'ymail.com',
                'aol.com', 'icloud.com', 'protonmail.com', 'zoho.com'
            ];

            // Create regex pattern for allowed domains
            const domainPattern = allowedDomains.join('|').replace(/\./g, '\\.');
            const pattern = new RegExp(`^[a-zA-Z0-9._%+-]+@(${domainPattern})$`);

            $.get("../lib/get_availability.php", {
                action: 'email',
                email: email
            }, function(data) {
                if (data.invalid) {
                    // If the email already exists in the database
                    $("#submit").prop("disabled", true);
                    $('.nextStep').prop("disabled", true); // Keep the Next button disabled
                    $("#email_error").html("Email ID already exists").css("color", "red");
                } else if (!pattern.test(email)) {
                    // If the email does not match allowed domains
                    $("#submit").prop("disabled", true);
                    $('.nextStep').prop("disabled", true); // Keep the Next button disabled
                    $("#email_error").html("Please use email from: Gmail, Yahoo, Hotmail, Outlook, etc.").css("color", "red");
                } else {
                    // If the email is valid
                    $("#sendOtpBtn").removeAttr('disabled');
                    $("#email").attr('readonly', 'readonly');

                    $("#email_error").html("Valid email ID").css("color", "green");
                    $("#submit").prop("disabled", false);
                    $('.nextStep').prop("disabled", false); // Enable the Next button
                    isEmailValid = true;
                }
            }, "json");
        }

        // Lightweight domain-only validator used when we only care about the part after "@"
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

        let isSendOtp = false;
        // jQuery to handle Next Step functionality
        $('.nextStep').on('click', function() {
            // Update isNameValid status before proceeding
            isNameValid = check_name($("#name").val());

            if (!$(this).prop('disabled') && isEmailValid && isLoginValid && isNameValid) {
                // Only proceed if all validations pass
                $(".sec1").removeClass("showSection").hide(); // Hide Section 1
                $(".sec3").show().css("display", "block").addClass("showSection"); // Show Section 3
            } else {
                // Show error message if any validation fails
                if (!isNameValid) {
                    $("#name_error").html("Please enter a valid name").css("color", "red");
                }
                if (!isEmailValid) {
                    $("#email_error").html("Please verify your email").css("color", "red");
                }
                if (!isLoginValid) {
                    $("#login").html("Please enter a valid username").css("color", "red");
                }
                if (!isSendOtp) {
                    $("#otpStatus").html("Please verify your email with OTP").css("color", "red");
                }
            }
        });

        $('#otpField').hide();
        // Handle Send OTP Button Click
        $('#sendOtpBtn').on('click', function() {
            const email = $('#email').val();
            if (!email) {
                $('#otpStatus').html('Please enter a valid email address.');
                return;
            }

            $.post('send_otp.php', {
                email: email
            }, function(response) {
                if (response.success) {
                    $("#sendOtpBtn").html("Resend OTP");
                    $("#sendOtpBtn").attr("disabled", "true");
                    $('#otpStatus').html(response.message); // Fixed string interpolation
                    $('#otpField').show();


                    setTimeout(() => {
                        $("#sendOtpBtn").removeAttr("disabled");
                    }, 80000);
                } else {
                    $('#otpStatus').html(response.error).css("color", "red"); // Use response.message if available
                }
            }, 'json').fail(function() {
                $('#otpStatus').html('An error occurred while sending the OTP.'); // Handle AJAX failure
            });
        });
        $('#veriFyOtp').on('click', function() {
            const otp = $('#otp').val();
            $("#veriFyOtp").attr("disabled", "true");
            console.log(otp);
            if (!otp) {
                $('#otpStatus').html('Please enter a valid otp.').css("color", "red");
                $("#veriFyOtp").removeAttr("disabled");
                return;
            }

            $.post('verify_otp.php', {
                otp: otp
            }, function(response) {
                if (response.success) {

                    $('#otpStatus').html(response.message).css("color", "green"); // Fixed string

                    isSendOtp = true


                } else {

                    $('#otpStatus').html(response.error).css("color", "red"); // Use response.message if available
                    $("#veriFyOtp").removeAttr("disabled");
                }
            }, 'json').fail(function() {
                $('#otpStatus').html('An error occurred while Verifying the OTP.'); // Handle AJAX failure
                $("#veriFyOtp").removeAttr("disabled");
            });
        });

        $('.prevStep2').on('click', function() {
            $(".sec3").hide(); // Show Section 1
            $(".sec3").removeClass("showSection");
            $(".sec1").show(); // Hide Section 2
            $(".sec1").css("display", "block").addClass("showSection");

        });
    </script>
</body>

</html>