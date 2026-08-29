<?php include_once '../lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
admin();

// Fetch records from database 
$query = my_query("SELECT uid, name, email, mobile, status FROM user ORDER BY recid ASC"); 
 
if($query->num_rows > 0){ 
    $delimiter = ","; 
    $filename = "users-data_" . date('Y-m-d') . ".csv"; 
     
    // Create a file pointer 
    $f = fopen('php://memory', 'w'); 
     
    // Set column headers 
    $fields = array('ID', 'NAME', 'EMAIL', 'MOBILE', 'STATUS'); 
    fputcsv($f, $fields, $delimiter); 
     
    // Output each row of the data, format line as csv and write to file pointer 
    while($row = $query->fetch_assoc()){ 
        $status = ($row['status'] == 0)?'Active':'Inactive'; 
        $lineData = array($row['uid'], $row['name'], $row['email'], $row['mobile'], $status); 
        fputcsv($f, $lineData, $delimiter); 
    } 
     
    // Move back to beginning of file 
    fseek($f, 0); 
     
    // Set headers to download file rather than displayed 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $filename . '";'); 
     
    //output all remaining data on a file pointer 
    fpassthru($f); 
} 
exit; 
?>