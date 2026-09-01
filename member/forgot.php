<?php
session_start();
include_once '../lib/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Forgot Password</title>
  <link rel="shortcut icon" href="../extra/img/favicon.png" type="image/x-icon">
  <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <link href="../assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet" type="text/css"/>
  <link href="../assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
  <link href="../assets/dist/css/component_ui.css" rel="stylesheet" type="text/css"/>
  <link href="../assets/dist/css/skins/component_ui_black.css" rel="stylesheet" type="text/css"/>
  <link href="../assets/dist/css/custom.css" rel="stylesheet" type="text/css"/>
    <link href="css/ge-theme.css" rel="stylesheet" type="text/css" />
  <style>
    .form-control { color: #fff; }
    .form-control::placeholder { color: #fff; opacity: 1; }
    .hidden { display: none; }
    .margin-top { margin-top: 15px; }
  </style>
  <style>
    /* ge-forgot */
    body, .login-wrapper {
      background: #050505 !important;
      color: #f5f5f5 !important;
      min-height: 100vh;
    }
    .panel, .panel-bd {
      background: #121212 !important;
      border: 1px solid rgba(212, 175, 55, 0.22) !important;
      border-radius: 16px !important;
      box-shadow: 0 12px 40px rgba(0,0,0,0.5);
    }
    .panel-heading, .header-title h3 { color: #d4af37 !important; }
    .header-title small { color: #a0a0a0 !important; }
    .form-control {
      background: #0a0a0a !important;
      border: 1px solid rgba(212, 175, 55, 0.22) !important;
      color: #f5f5f5 !important;
      border-radius: 12px !important;
    }
    .input-group-addon {
      background: #1a1a1a !important;
      border-color: rgba(212, 175, 55, 0.22) !important;
      color: #d4af37 !important;
    }
    .btn-primary {
      background: linear-gradient(135deg, #f5d76e, #d4af37, #b8860b) !important;
      border: none !important;
      color: #111 !important;
      font-weight: 700;
      border-radius: 12px !important;
    }
    label { color: #a0a0a0 !important; }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="container-center">
      <div class="panel panel-bd">
        <div class="panel-heading">
          <div class="view-header">
            <div class="header-icon">
              <i class="pe-7s-refresh-2"></i>
            </div>
            <div class="header-title">
              <h3>Forgot Password</h3>
              <small>Please fill the form to recover your password</small>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <!-- STEP 1: Enter User ID and Email -->
          <div id="step1">
            <form id="userDetailsForm">
              <div class="form-group">
                <label>User ID</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                  <input type="text" class="form-control" id="login_id" name="login_id" placeholder="Enter your User ID" maxlength="20" required>
                </div>
              </div>
              <div class="form-group">
                <label>Email</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter your Email" maxlength="100" required>
                </div>
              </div>
              <button type="button" id="sendOtpBtn" class="btn btn-primary pull-right">Send OTP</button>
              <div id="userDetailsError" class="alert alert-danger hidden margin-top"></div>
            </form>
          </div>

          <!-- STEP 2: OTP Verification -->
          <div id="step2" class="hidden">
            <form id="otpForm">
              <div class="form-group">
                <label>OTP</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-key"></i></span>
                  <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP" maxlength="6" required>
                </div>
              </div>
              <button type="button" id="verifyOtpBtn" class="btn btn-primary pull-right">Verify OTP</button>
              <div id="otpError" class="alert alert-danger hidden margin-top"></div>
            </form>
          </div>

          <!-- STEP 3: Create New Password -->
          <div id="step3" class="hidden">
            <form id="changePasswordForm" action="forgot_model.php?step=3" method="post" novalidate>
              <div class="form-group">
                <label>New Password</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                  <input type="password" class="form-control" name="password" placeholder="Enter new password" required>
                </div>
              </div>
              <div class="form-group">
                <label>Confirm New Password</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                  <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password" required>
                </div>
              </div>
              <!-- Pass along the login_id and email for final submission -->
              <input type="hidden" name="login_id" id="hidden_login_id">
              <input type="hidden" name="email" id="hidden_email">
              <button type="submit" id="finalSubmitBtn" class="btn btn-primary pull-right">Submit</button>
              <div id="changePwdError" class="alert alert-danger hidden margin-top"></div>
            </form>
          </div>
        </div>
      </div>
      <div id="bottom_text">
        <a href="index.php">Login</a>
      </div>
    </div>
  </div>

  <!-- jQuery and Bootstrap JS -->
  <script src="../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
  <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function(){
      // STEP 1: Send OTP
      
      $('#sendOtpBtn').click(function(){
        // Clear any previous error messages.
        $('#userDetailsError').addClass('hidden').text('');
        
        var login_id = $('#login_id').val().trim();
        var email = $('#email').val().trim();
        
        
        if (login_id === "" || email === "") {
          $('#userDetailsError').removeClass('hidden').text('Both User ID and Email are required.');
          return;
        }
        $("#sendOtpBtn").attr("disabled", "true");
        // Save the entered values in hidden fields for later submission.
        $('#hidden_login_id').val(login_id);
        $('#hidden_email').val(email);
        
        $.ajax({
          url: 'send_otp.php',
          type: 'POST',
          dataType: 'json',
          data: { email: email },
          success: function(response) {
            if (response.success) {
              // Hide the Send OTP button after a successful response.
              $('#sendOtpBtn').hide();
              $('#step2').removeClass('hidden');
              // Show the OTP input and Verify OTP button.
              $('#step2').slideDown();
            } else {
              $('#userDetailsError').removeClass('hidden').text(response.error || 'Failed to send OTP.');
            }
          },
          error: function(xhr, status, error) {
            $('#userDetailsError').removeClass('hidden').text('Error: ' + error);
          }
        });
      });
      
      // STEP 2: Verify OTP
      $('#verifyOtpBtn').click(function(){
        $('#otpError').addClass('hidden').text('');
        var otp = $('#otp').val().trim();
        if (otp === "") {
          $('#otpError').removeClass('hidden').text('Please enter the OTP.');
          return;
        }
        $("#verifyOtpBtn").attr("disabled", "true");
        $.ajax({
          url: 'verify_otp.php',
          type: 'POST',
          dataType: 'json',
          data: { otp: otp },
          success: function(response) {
            if (response.success) {
              // Hide the Verify OTP button upon successful OTP verification.
              $('#verifyOtpBtn').hide();
              $('#step3').removeClass('hidden');
              // Hide the OTP input section.
              $('#step2').slideUp();
              // Show the password change section.
              $('#step3').slideDown();
            } else {
                 $("#verifyOtpBtn").removeAttr("disabled");
              $('#otpError').removeClass('hidden').text(response.error || 'OTP verification failed.');
            }
          },
          error: function(xhr, status, error) {
            $('#otpError').removeClass('hidden').text('Error: ' + error);
          }
        });
      });
      
      // Client-side validation for the password fields before final submission.
      $('#changePasswordForm').submit(function(e){
        var pwd = $(this).find('input[name="password"]').val();
        var cpwd = $(this).find('input[name="confirm_password"]').val();
        if(pwd !== cpwd){
          e.preventDefault();
          $('#changePwdError').removeClass('hidden').text('Passwords do not match.');
        }
      });
    });
  </script>
</body>
</html>
