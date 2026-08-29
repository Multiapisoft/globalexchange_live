<?php
$from_name = $_ENV['SMTP_BREVO_FROM_NAME'] ?? 'GlobalExchange';
$from_email = $_ENV['SMTP_BREVO_FROM_EMAIL'] ?? '';
$api_key = $_ENV['SMTP_BREVO_API_KEY'] ?? '';

if (!function_exists('ge_email_wrap')) {
    $emailTemplate = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'email_template.php';
    if (is_file($emailTemplate)) {
        require_once $emailTemplate;
    }
}

/**
 * Send transactional email via Brevo API.
 * Prefer cURL (IPv4). If Apache cannot load curl, fall back to PHP CLI worker.
 */
function _sendMail($url, $to, $subject, $txt)
{
    global $from_name;
    global $from_email;
    global $api_key;

    if (!$to || !$from_email || !$api_key) {
        return false;
    }

    if (function_exists('ge_email_wrap') && strpos((string) $txt, '<!--GE_EMAIL-->') === false) {
        $txt = ge_email_wrap($subject, $txt);
    }

    $payload = array(
        'sender' => array(
            'name' => $from_name,
            'email' => $from_email,
        ),
        'to' => array(
            array('email' => $to),
        ),
        'htmlContent' => $txt,
        'subject' => $subject,
    );

    if (function_exists('curl_init')) {
        return _sendMailCurl($payload, $api_key);
    }

    return _sendMailViaCli($payload, $api_key);
}

function _sendMailCurl($payload, $api_key)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if (defined('CURL_IPRESOLVE_V4')) {
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Api-Key: ' . $api_key,
        'Content-Type: application/json',
    ));
    $result = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($result === false) {
        return false;
    }
    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    }
    $decoded = json_decode((string) $result, true);
    return is_array($decoded) && !empty($decoded['messageId']);
}

function _sendMailViaCli($payload, $api_key)
{
    $phpCli = _detectPhpCli();
    if (!$phpCli) {
        return false;
    }

    $worker = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'send_brevo_mail_cli.php';
    if (!is_file($worker)) {
        return false;
    }

    $tmp = tempnam(sys_get_temp_dir(), 'brevo_');
    if ($tmp === false) {
        return false;
    }
    $payloadFile = $tmp . '.json';
    @unlink($tmp);
    file_put_contents($payloadFile, json_encode(array(
        'api_key' => $api_key,
        'payload' => $payload,
    )));

    $cmd = escapeshellarg($phpCli) . ' ' . escapeshellarg($worker) . ' ' . escapeshellarg($payloadFile);
    $output = array();
    $exitCode = 1;
    @exec($cmd, $output, $exitCode);
    @unlink($payloadFile);

    if ($exitCode === 0) {
        return true;
    }
    return false;
}

function _detectPhpCli()
{
    $candidates = array(
        'C:\\xampp\\php\\php.exe',
        dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'php.exe',
        'php',
    );
    foreach ($candidates as $bin) {
        if ($bin === 'php') {
            return 'php';
        }
        if (is_file($bin)) {
            return $bin;
        }
    }
    return '';
}
