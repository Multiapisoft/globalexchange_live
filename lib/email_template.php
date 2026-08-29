<?php
/**
 * Branded HTML email layouts for GlobalExchange transactional mail.
 * Table + inline CSS so Gmail / Outlook / Apple Mail render consistently.
 */

function ge_email_brand()
{
    $name = defined('SITE_NAME') && SITE_NAME ? SITE_NAME : 'GlobalExchange';
    $host = defined('SITE_URL') ? preg_replace('#^https?://#i', '', rtrim((string) SITE_URL, '/')) : '';
    $href = $host ? ('https://' . $host) : '#';
    return array(
        'name' => $name,
        'host' => $host,
        'href' => $href,
        'login' => $host ? ('https://' . $host . '/member/') : '#',
    );
}

function ge_email_esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function ge_email_otp_boxes($otp)
{
    $otp = preg_replace('/\s+/', '', (string) $otp);
    $digits = $otp === '' ? array() : preg_split('//u', $otp, -1, PREG_SPLIT_NO_EMPTY);
    if (!$digits || count($digits) > 8) {
        return '<div style="display:inline-block;padding:14px 28px;background:#0b0e11;border:1px solid #f0b90b;border-radius:10px;color:#f0b90b;font-size:28px;font-weight:700;letter-spacing:8px;font-family:\'Courier New\',Courier,monospace;">'
            . ge_email_esc($otp)
            . '</div>';
    }

    $cells = '';
    $first = true;
    foreach ($digits as $digit) {
        if (!$first) {
            $cells .= '<td style="width:8px;font-size:0;line-height:0;">&nbsp;</td>';
        }
        $first = false;
        $cells .= '<td align="center" valign="middle" style="width:44px;height:54px;background-color:#0b0e11;border:1px solid #f0b90b;border-radius:8px;color:#f0b90b;font-size:24px;line-height:54px;font-weight:700;font-family:\'Courier New\',Courier,monospace;">'
            . ge_email_esc($digit)
            . '</td>';
    }

    return '<table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:0 auto;"><tr>'
        . $cells
        . '</tr></table>';
}

/**
 * Wrap inner HTML in the GlobalExchange dark / gold shell.
 *
 * @param string $subject
 * @param string $innerHtml
 * @param array  $options  otp, cta_url, cta_label, eyebrow, footer_note
 */
function ge_email_wrap($subject, $innerHtml, $options = array())
{
    $brand = ge_email_brand();
    $year = date('Y');
    $eyebrow = isset($options['eyebrow']) ? $options['eyebrow'] : 'Secure notification';
    $ctaUrl = isset($options['cta_url']) ? $options['cta_url'] : '';
    $ctaLabel = isset($options['cta_label']) ? $options['cta_label'] : 'Open Dashboard';
    $otp = isset($options['otp']) ? $options['otp'] : '';
    $footerNote = isset($options['footer_note'])
        ? $options['footer_note']
        : 'This is an automated message. Please do not reply to this email.';

    $otpBlock = '';
    if ($otp !== '') {
        $otpBlock = '
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:8px 0 24px;">
            <tr>
                <td align="center" style="padding:22px 16px;background-color:#0b0e11;border:1px solid #2c3137;border-radius:14px;">
                    <p style="margin:0 0 12px;color:#848e9c;font-size:12px;letter-spacing:2px;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;">Your verification code</p>
                    ' . ge_email_otp_boxes($otp) . '
                    <p style="margin:16px 0 0;color:#f0b90b;font-size:28px;line-height:1.2;font-weight:700;letter-spacing:6px;font-family:\'Courier New\',Courier,monospace;">' . ge_email_esc(preg_replace('/\D+/', '', (string) $otp)) . '</p>
                    <p style="margin:14px 0 0;color:#848e9c;font-size:12px;font-family:Arial,Helvetica,sans-serif;">Valid for 10 minutes &middot; Do not share this code</p>
                </td>
            </tr>
        </table>';
    }

    $ctaBlock = '';
    if ($ctaUrl !== '') {
        $safeUrl = ge_email_esc($ctaUrl);
        $safeLabel = ge_email_esc($ctaLabel);
        $ctaBlock = '
        <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:8px auto 28px;">
            <tr>
                <td align="center" bgcolor="#f0b90b" style="border-radius:8px;background-color:#f0b90b;">
                    <!--[if mso]>
                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="' . $safeUrl . '" style="height:48px;v-text-anchor:middle;width:220px;" arcsize="12%" fillcolor="#f0b90b" strokecolor="#f0b90b">
                    <w:anchorlock/>
                    <center style="color:#0b0e11;font-family:Arial,sans-serif;font-size:15px;font-weight:bold;">' . $safeLabel . '</center>
                    </v:roundrect>
                    <![endif]-->
                    <!--[if !mso]><!-->
                    <a href="' . $safeUrl . '" style="display:inline-block;padding:14px 32px;background-color:#f0b90b;color:#0b0e11;text-decoration:none;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;border-radius:8px;letter-spacing:0.3px;">' . $safeLabel . '</a>
                    <!--<![endif]-->
                </td>
            </tr>
        </table>';
    }

    $safeSubject = ge_email_esc($subject);
    $safeName = ge_email_esc($brand['name']);
    $safeHref = ge_email_esc($brand['href']);
    $safeHost = ge_email_esc($brand['host']);
    $initial = strtoupper(substr($brand['name'], 0, 1));

    return '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>' . $safeSubject . '</title>
    <!--GE_EMAIL-->
</head>
<body style="margin:0;padding:0;background-color:#0b0e11;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">' . $safeSubject . ' &mdash; ' . $safeName . '</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#0b0e11" style="background-color:#0b0e11;margin:0;padding:0;">
        <tr>
            <td align="center" style="padding:28px 12px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;">
                    <tr>
                        <td style="height:5px;line-height:5px;font-size:0;background-color:#f0b90b;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td bgcolor="#1e2329" style="background-color:#1e2329;padding:28px 36px 8px;border-left:1px solid #2c3137;border-right:1px solid #2c3137;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td valign="middle">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td valign="middle" style="width:38px;height:38px;background-color:#f0b90b;border-radius:10px;color:#0b0e11;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:800;text-align:center;line-height:38px;">' . ge_email_esc($initial) . '</td>
                                                <td valign="middle" style="padding-left:12px;color:#eaecef;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:700;letter-spacing:0.4px;">' . $safeName . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td align="right" valign="middle" style="color:#848e9c;font-family:Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;">' . ge_email_esc($eyebrow) . '</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#1e2329" style="background-color:#1e2329;padding:8px 36px 0;border-left:1px solid #2c3137;border-right:1px solid #2c3137;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="height:1px;line-height:1px;font-size:0;background-color:#2c3137;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#1e2329" style="background-color:#1e2329;padding:28px 36px 8px;border-left:1px solid #2c3137;border-right:1px solid #2c3137;color:#eaecef;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.7;">
                            ' . $innerHtml . '
                            ' . $otpBlock . '
                            ' . $ctaBlock . '
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#161a1e" style="background-color:#161a1e;padding:22px 36px 28px;border:1px solid #2c3137;border-top:0;border-radius:0 0 14px 14px;">
                            <p style="margin:0 0 8px;color:#848e9c;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;">' . $footerNote . '</p>
                            <p style="margin:0;color:#5e6673;font-family:Arial,Helvetica,sans-serif;font-size:12px;">
                                &copy; ' . $year . ' ' . $safeName . '
                                ' . ($safeHost ? ' &middot; <a href="' . $safeHref . '" style="color:#f0b90b;text-decoration:none;">' . $safeHost . '</a>' : '') . '
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}

function ge_email_otp($otp, $options = array())
{
    $brand = ge_email_brand();
    $greeting = isset($options['greeting']) ? $options['greeting'] : 'Hi,';
    $intro = isset($options['intro']) ? $options['intro'] : 'Use the one-time password below to continue.';
    $subject = isset($options['subject']) ? $options['subject'] : 'Your verification code';
    $eyebrow = isset($options['eyebrow']) ? $options['eyebrow'] : 'Security code';

    $inner = '
        <p style="margin:0 0 6px;color:#f0b90b;font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:700;">' . ge_email_esc($eyebrow) . '</p>
        <h1 style="margin:0 0 16px;color:#eaecef;font-size:24px;line-height:1.3;font-weight:700;">' . ge_email_esc($greeting) . '</h1>
        <p style="margin:0 0 8px;color:#848e9c;font-size:15px;">' . $intro . '</p>';

    return ge_email_wrap($subject, $inner, array(
        'otp' => $otp,
        'eyebrow' => $eyebrow,
        'cta_url' => isset($options['cta_url']) ? $options['cta_url'] : $brand['login'],
        'cta_label' => isset($options['cta_label']) ? $options['cta_label'] : 'Open Dashboard',
        'footer_note' => 'If you did not request this code, you can safely ignore this email. Never share your OTP with anyone.',
    ));
}

function ge_email_welcome($name, $loginId, $password)
{
    $brand = ge_email_brand();
    $safeName = ge_email_esc($name);
    $rowStyle = 'padding:12px 16px;border-bottom:1px solid #2c3137;';
    $labelStyle = 'color:#848e9c;font-size:11px;letter-spacing:1.2px;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;';
    $valueStyle = 'color:#eaecef;font-size:16px;font-weight:700;font-family:Arial,Helvetica,sans-serif;';

    $inner = '
        <p style="margin:0 0 6px;color:#02c076;font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:700;">Account ready</p>
        <h1 style="margin:0 0 12px;color:#eaecef;font-size:24px;line-height:1.3;font-weight:700;">Welcome aboard, ' . $safeName . '</h1>
        <p style="margin:0 0 22px;color:#848e9c;font-size:15px;">Your ' . ge_email_esc($brand['name']) . ' account is live. Keep these credentials safe and log in to start trading.</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 8px;background-color:#0b0e11;border:1px solid #2c3137;border-radius:12px;">
            <tr>
                <td style="' . $rowStyle . '">
                    <div style="' . $labelStyle . '">Login ID</div>
                    <div style="' . $valueStyle . '">' . ge_email_esc($loginId) . '</div>
                </td>
            </tr>
            <tr>
                <td style="padding:12px 16px;">
                    <div style="' . $labelStyle . '">Password</div>
                    <div style="' . $valueStyle . '">' . ge_email_esc($password) . '</div>
                </td>
            </tr>
        </table>
        <p style="margin:16px 0 0;color:#848e9c;font-size:13px;">For your security, change this password after your first login.</p>';

    return ge_email_wrap('Welcome to ' . $brand['name'], $inner, array(
        'eyebrow' => 'Welcome',
        'cta_url' => $brand['login'],
        'cta_label' => 'Login Now',
        'footer_note' => 'If you did not create this account, please contact support immediately.',
    ));
}
