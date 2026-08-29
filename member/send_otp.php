<?php
session_start();

// // Include PHPMailer and any other necessary files
// include '../lib/PHPMailer/index.php';
include_once '../lib/config.php';
// // Check if the request method is POST
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $email = $_POST['email'];
//     if(checkEmailAvailability($email)==0){
        
        
//         echo json_encode(['success' => false, 'error' => 'Email Already Exists. Kindly login via registered email']);
//     }
//     // Validate and sanitize the email input
//     elseif (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
//         $to = $_POST['email'];
//         $otp = rand(100000, 999999);  // Generate 6-digit OTP

//         // Save OTP in session
//         $_SESSION['otp'] = $otp;
//         $_SESSION['otp_email'] = $to;

//         // Prepare email content
//         $subject = "Your OTP Code";
//         $txt = "Use the following OTP to complete your registration: $otp";
//         // $headers = "From: no-reply@lizacoin.live\r\n";
//         $headers .= "MIME-Version: 1.0\r\n";
//         $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

//         // Send OTP via email using your defined function
//         if (_sendMail("support@lizacoin.live", $to, $subject, $txt)) {
//             echo json_encode(['success' => true, 'message'=>'Mail sent, if you are not received, please resend after 60 seconds.']);
//         } else {
//             echo json_encode(['success' => false, 'error' => 'Failed to send email.']);
//         }
//     } else {
//         echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
//     }
// } else {
//     echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
// }


// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize the email input
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST['email'];

        // Prepare OTP API request
        $apiKey = "<api-key>";  // Replace with your actual API key
        $clientId = "P98523UACEH3TI00FNRJ9AEOK5XR3YRA";  // Replace with your client ID
        $clientSecret = "h3q3p9csujhp640kdc4muvrzxm8r4vu8";  // Replace with your client secret

        // The OTP API endpoint
        $url = "https://auth.otpless.app/auth/v1/initiate/otp";

        // Data for the OTP request
        $data = [
            'email' => $email,  // Email address to which OTP will be sent
            'expiry' => 120,    // OTP expiration time in seconds
            'otpLength' => 4,   // OTP length (can be 4 or 6)
            'channels' => ['EMAIL'],  // Channel through which OTP will be sent (EMAIL in this case)
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

            if (isset($responseData['requestId'])) {
                // Store requestId in session for later OTP verification
                $_SESSION['requestId'] = $responseData['requestId'];
                
                echo json_encode([
                    'success' => true,
                    'message' => 'OTP sent successfully to your email!',
                    'requestId' => $responseData['requestId']
                ]);
            } else {
                // Handle failure in sending OTP
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to send OTP: ' . json_encode($responseData)
                ]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}


?>
