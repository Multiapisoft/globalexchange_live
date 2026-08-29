<?php date_default_timezone_set("Asia/kolkata");
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
// error_reporting(-1);
$dbdriver = "mysqli";
$dbhost = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost';
$dbuser = isset($_ENV['DB_USERNAME']) ? $_ENV['DB_USERNAME'] : 'root';
$password = isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : '';
$dbname = isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : '';
$link = my_connect($dbhost, $dbuser, $password, $dbname);

function my_connect($server, $username, $password, $db) {
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        $link = mysqli_connect($server, $username, $password, $db) or die("Could not connect to server");
    } else {
        $link = mysql_connect($server, $username, $password) or die("Could not connect to server");
        mysql_select_db($db, $link);
    }
    return $link;
}

function my_query($sql) {
    global $link;
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        mysqli_query($link, "SET sql_mode=''");
        return mysqli_query($link, $sql);
    } else {
        mysql_query("SET sql_mode=''");
        return mysql_query($sql);
    }
}

function my_real_escape_string($text) {
    global $link;
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        return mysqli_real_escape_string($link, $text);
    } else {
        return mysql_real_escape_string($text);
    }
}

function my_insert_id() {
    global $link;
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        return mysqli_insert_id($link);
    } else {
        return mysql_insert_id();
    }
}

function my_num_rows($rs) {
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        return mysqli_num_rows($rs);
    } else {
        return mysql_num_rows($rs);
    }
}

function my_fetch_object($rs) {
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        return mysqli_fetch_object($rs);
    } else {
        return mysql_fetch_object($rs);
    }
}

function my_fetch_array($rs) {
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        return mysqli_fetch_array($rs);
    } else {
        return mysql_fetch_array($rs);
    }
}

function my_fetch_assoc($rs) {
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        return mysqli_fetch_assoc($rs);
    } else {
        return mysql_fetch_assoc($rs);
    }
}

function my_close() {
    global $link;
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        return mysqli_close($link);
    } else {
        return mysql_close($link);
    }
}

function my_error() {
    global $link;
    global $dbdriver;
    if ($dbdriver == 'mysqli') {
        return mysqli_error($link);
    } else {
        return mysql_error($link);
    }
}

function RemoveXSS(&$input, $key) {
    $output = $input;
    do {
        // Treat $input as buffer on each loop, faster than new var
        $input = $output;

        // Remove unwanted tags
        $output = strip_tags_input($input);
        $output = strip_encoded_entities($output);
    } while ($output !== $input);

    //$input = addslashes($output);
    $input = escape_str($output);

    return $input;
}

function strip_encoded_entities($input) {
    // Fix &entity\n;
    $input = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $input);
    $input = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $input);
    $input = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $input);
    $input = html_entity_decode($input, ENT_COMPAT, 'UTF-8');
    // Remove any attribute starting with "on" or xmlns
    $input = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+[>\b]?#iu', '$1>', $input);
    // Remove javascript: and vbscript: protocols
    $input = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $input);
    $input = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $input);
    $input = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $input);
    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $input);
    $input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $input);
    $input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $input);
    return $input;
}

function strip_tags_input($input) {
    // Remove tags
    $input = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $input);
    $input = preg_replace('/<\?(php)/i', "&lt;?\\1", $input);
    // Remove namespaced elements
    $input = preg_replace('#</*\w+:\w[^>]*+>#i', '', $input);
    return $input;
}

if (!empty($_POST)) array_walk_recursive($_POST, 'RemoveXSS');
if (!empty($_GET)) array_walk_recursive($_GET, 'RemoveXSS');
if (!empty($_COOKIE)) array_walk_recursive($_COOKIE, 'RemoveXSS');
if (!empty($_SERVER)) array_walk_recursive($_SERVER, 'RemoveXSS');
//if (!empty($_SESSION)) array_walk_recursive($_SESSION, 'RemoveXSS');
if (!empty($_REQUEST)) array_walk_recursive($_REQUEST, 'RemoveXSS');
if (!empty($_FILES)) array_walk_recursive($_REQUEST, 'RemoveXSS');

function escape_str($str, $like = FALSE) {
    global $link;
    if (is_array($str)) {
        foreach ($str as $key => $val) {
            $str[$key] = $this->escape_str($val, $like);
        }
        return $str;
    }

    if (function_exists('mysqli_real_escape_string') AND is_resource($link)) {
        $str = mysqli_real_escape_string($link, $str);
    } elseif (function_exists('mysql_escape_string')) {
        //$str = mysql_escape_string($str);
        $str = mysqli_real_escape_string($link, $str);
    } else {
        $str = addslashes($str);
    }
    // escape LIKE condition wildcards
    if ($like === TRUE) {
        $str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
    }
    return $str;
}
?>