<?php
include_once '../lib/config.php';
session_start();
$uid = $_SESSION['userid'];
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors in the browser

// header('Content-Type: application/json'); // Ensure JSON response

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $response = [];
//     // Check if OTP is provided
//     if (!isset($_POST['otp'])) {
//         $response['success'] = false;
//         $response['error'] = 'OTP not provided.';
//         echo json_encode($response);
//         exit;
//     }

//     $inputOtp = trim($_POST['otp']); // Clean input
//     // Check if the session OTP is set and matches the input OTP
//     if (isset($_SESSION['otp']) && $inputOtp == $_SESSION['otp']) {
//         // Unset session variables after successful verification
//         unset($_SESSION['otp']);
//         unset($_SESSION['otp_email']);

//         $response['success'] = true;
//         $response['message'] = 'OTP verified successfully.';
//     } else {
//         $response['success'] = false;
//         $response['error'] = 'Invalid OTP.';
//     }
//     echo json_encode($response);
//     exit;
// }

// // Invalid request method
// echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
// exit;


// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get OTP from POST data
    if (isset($_POST['otp']) && isset($_SESSION['requestId'])) {
        $otp = $_POST['otp'];
        $requestId = $_SESSION['requestId'];  // The requestId stored during OTP initiation

        // Prepare OTP Verification API request
        $apiKey = "<api-key>";  // Replace with your actual API key
        $clientId = "P98523UACEH3TI00FNRJ9AEOK5XR3YRA";  // Replace with your client ID
        $clientSecret = "h3q3p9csujhp640kdc4muvrzxm8r4vu8";  // Replace with your client secret

        // OTP verification API endpoint
        $url = "https://auth.otpless.app/auth/v1/verify/otp";

        // Data for the OTP verification request
        $data = [
            'otp' => $otp,  // OTP entered by the user
            'requestId' => $requestId,  // The requestId from OTP initiation
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data), // JSON encoded data
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "clientId: $clientId",  // Your client ID
                "clientSecret: $clientSecret",  // Your client secret
            ],
        ]);

        // Execute the request and check for errors
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            echo json_encode(['success' => false, 'error' => "cURL Error #: $err"]);
        } else {
            // Decode the response from the OTP API
            $responseData = json_decode($response, true);

            if (isset($responseData['isOTPVerified']) && $responseData['isOTPVerified']) {
                  $updateQuery = "UPDATE user SET verified = 1 WHERE uid = '".$uid."'";
        my_query($updateQuery);
                // OTP verified successfully
                echo json_encode([
                    'success' => true,
                    'message' => 'OTP verified successfully!',
                    'requestId' => $responseData['requestId']
                ]);
            } else {
                // OTP verification failed
                echo json_encode([
                    'success' => false,
                    'error' => 'OTP verification failed: Incorrect or Expired Otp '
                ]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'OTP or Request ID missing.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

?>
