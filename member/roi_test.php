<?php
include_once '../lib/config.php';
header('Content-Type: text/plain; charset=utf-8');

global $link;

$loginId = isset($_GET['login_id']) ? trim($_GET['login_id']) : '';
$uidParam = isset($_GET['uid']) ? (int) $_GET['uid'] : 0;

if ($loginId === '' && $uidParam <= 0) {
    echo "Please provide ?login_id=TBXXXXXX or ?uid=123456\n";
    exit;
}

if ($loginId !== '') {
    $loginIdEscaped = mysqli_real_escape_string($link, $loginId);
    $user = my_fetch_object(my_query("SELECT * FROM user WHERE login_id='" . $loginIdEscaped . "' LIMIT 1"));
} else {
    $user = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $uidParam . "' LIMIT 1"));
}

if (!$user) {
    echo "User not found.\n";
    exit;
}

echo "Testing ROI eligibility for login_id: " . $user->login_id . " | UID: " . $user->uid . "\n";
echo str_repeat('-', 80) . "\n";

$investmentQuery = my_query("SELECT * FROM investments WHERE uid='" . $user->uid . "' ORDER BY datetime ASC");
if (my_num_rows($investmentQuery) === 0) {
    echo "No investments found for this user.\n";
    exit;
}

$now = strtotime(date('c'));

while ($row = my_fetch_object($investmentQuery)) {
    $plan = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . (int) $row->ipid . "'"));
    if (!$plan) {
        echo "Investment #" . $row->recid . " -> Plan not found (ipid=" . $row->ipid . ")\n";
        continue;
    }

    $timeFloor = floor(($now - strtotime($row->datetime)) / (60 * 60 * 24));
    $isDaily = ($plan->daily == 0 || $plan->daily == 1) ||
        ((($timeFloor % 30) == 0) && $plan->daily == 30) ||
        ((($timeFloor % 7) == 0) && $plan->daily == 7) ||
        ((int) $row->ipid == 2 && (int) $row->trade_status == 1);
    $isTradeActivated = ((int) $row->ipid != 2) || ((int) $row->trade_status == 1);

    $minPer = $plan->percentage;
    $maxPer = $plan->percentage_to;
    $randomPer = round((mt_rand($minPer * 1000, $maxPer * 1000) / 1000) / 30, 3);
    $rawAmount = ($row->amount * $randomPer) / 100;
    $cappedAmount = check_3x($user->uid, $rawAmount);

    echo "Investment #" . $row->recid . " | Plan: " . $plan->title . "\n";
    echo "  Amount: " . $row->amount . " | Days: " . $row->days . "/" . $plan->days . " | Status: " . $row->status . "\n";
    echo "  Trade status: " . $row->trade_status . " | Daily flag: " . $plan->daily . "\n";
    echo "  Time since start (days): " . $timeFloor . "\n";
    echo "  isDailyPayout? " . ($isDaily ? 'YES' : 'NO') . " | isTradeActivated? " . ($isTradeActivated ? 'YES' : 'NO') . "\n";
    echo "  Random percentage sample: " . $randomPer . "% -> Raw ROI: " . $rawAmount . "\n";
    echo "  check_3x result: " . $cappedAmount . "\n";

    if ($row->days >= $plan->days) {
        echo "  Reason: Max plan days completed. ROI stops.\n";
    } elseif (!$isDaily) {
        echo "  Reason: Daily trigger condition not satisfied for this plan/day.\n";
    } elseif (!$isTradeActivated) {
        echo "  Reason: Self Trade requires activation (trade_status = 1).\n";
    } elseif ($cappedAmount <= 0) {
        echo "  Reason: 3X cap reached (check_3x returned <= 0).\n";
    } else {
        echo "  ROI would be credited in cron (amount > 0).\n";
    }

    echo str_repeat('-', 80) . "\n";
}

echo "End of diagnostics.\n";
