<?php
/**
 * CLI worker for Brevo mail (used when Apache PHP has no curl).
 * Usage: php send_brevo_mail_cli.php /path/to/payload.json
 */
if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only\n");
    exit(2);
}
if (!function_exists('curl_init')) {
    fwrite(STDERR, "curl missing in CLI\n");
    exit(3);
}
$file = $argv[1] ?? '';
if (!$file || !is_file($file)) {
    fwrite(STDERR, "payload missing\n");
    exit(4);
}
$data = json_decode(file_get_contents($file), true);
if (!is_array($data) || empty($data['api_key']) || empty($data['payload'])) {
    fwrite(STDERR, "invalid payload\n");
    exit(5);
}

$ch = curl_init('https://api.brevo.com/v3/smtp/email');
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => json_encode($data['payload']),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Api-Key: ' . $data['api_key'],
        'Content-Type: application/json',
    ),
));
$result = curl_exec($ch);
$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($result === false || $code < 200 || $code >= 300) {
    fwrite(STDERR, "fail http={$code} err={$err} body={$result}\n");
    exit(1);
}
echo "ok\n";
exit(0);
