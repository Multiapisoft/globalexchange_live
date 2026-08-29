<?php include_once '../lib/config.php';
user();
$arr = array(
    'bitcoin' => '',
    'bnb_address' => '',
);

if(isset($_POST)){
    $uid = tres($_POST['uid']);
    $user = get_user_details($uid);
    
    $name = isset($_POST['name']) ? tres($_POST['name']) : '';
    $dob = isset($_POST['dob']) ? date('Y-m-d', strtotime(tres($_POST['dob']))) : date('Y-m-d');
    $gender = isset($_POST['gender']) ? tres($_POST['gender']) : 'Male';
    $email = isset($_POST['email']) ? tres($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? tres($_POST['phone']) : '';
    $mobile = isset($_POST['mobile']) ? tres($_POST['mobile']) : '';
    $address = isset($_POST['address']) ? tres($_POST['address']) : '';
    $city = isset($_POST['city']) ? tres($_POST['city']) : '';
    $state = isset($_POST['state']) ? tres($_POST['state']) : '';
    $country = isset($_POST['country']) ? tres($_POST['country']) : 'IN';

    foreach ($arr as $key => $value) {
        ${$key} = isset($_POST[$key]) ? tres($_POST[$key]) : $value;
    }
    
    if(checkMobile($mobile)==0){
        setMessage('Invalid mobile.','error');
    }
    elseif(checkMobileAvailability($mobile, $uid)==0){
        setMessage('Mobile already axist.','error');
    }
    elseif(checkEmail($email)==0){
        setMessage('Invalid email.','error');
    }
    elseif(checkEmailAvailability($email, $uid)==0){
        setMessage('Email already axist.','error');
    }
    else{
        $sql = "UPDATE user SET name='".$name."', dob='".$dob."', gender='".$gender."', email='".$email."', phone='".$phone."'";
        $sql .= ", mobile='".$mobile."', address='".$address."', city='".$city."', state='".$state."', country='".$country."'";
        
        foreach ($arr as $key => $value) {
            if (isset($_POST[$key])) {
                $sql .= ", $key = '" . ${$key} . "'";
            }
        }
        
        $sql .= " WHERE uid='".$uid."'";
        my_query( $sql);
        
        setMessage('Profile edit successfully.', 'success');
    }
    redirect('./profile.php');
}
else{
    redirect('./profile.php');
}
?>