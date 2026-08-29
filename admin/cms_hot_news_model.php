<?php include_once '../lib/config.php';
include('../lib/imageresize.php');
admin();
die;
if(isset($_POST)){
    $hot_news = my_real_escape_string($_POST['hot_news']);
    $status = $_POST['status'];
    
    if(!empty($_FILES['image']) && !empty($_POST) && $_FILES["image"]["name"] != ''){
        if(isset($_FILES['image']['name']) && array_search($_FILES['image']['type'],array("image/gif", "image/jpeg" , "image/png" ,"image/jpg") ) !== FALSE){
            /*$resize = new resizeImage();
            // upload image in three dimesions

            //$largePath      = "uploads/news/large/";
            //$largeImage     = $resize->do_resize(500,400  ,$_FILES['image'],$largePath,0,"large");

            $smallPath      = "../uploads/";
            $smallImage     = $resize->do_resize(400,800  ,$_FILES['image'],$smallPath,0,"thumb", 90);*/
            
            $fileData = pathinfo(basename($_FILES["image"]["name"]));
            $fileName = uniqid() . '.' . $fileData['extension'];
            $target_path = ("../uploads/" . $fileName);

            while(file_exists($target_path)){
                $fileName = uniqid() . '.' . $fileData['extension'];
                $target_path = ("../uploads/" . $fileName);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)){
                my_query("UPDATE hot_news SET hot_news='".$hot_news."', image='".$fileName."', status='".$status."' WHERE recid=1");
            }
            setMessage('Hot news edit successfully.', 'success');
        }
        else{
            // uploaded file is not a image
            setMessage('Uploaded file is not a image.', 'error');
        }
    }
    else{
        my_query("UPDATE hot_news SET hot_news='".$hot_news."', status='".$status."' WHERE recid=1");
        setMessage('Hot news edit successfully.', 'success');
    }
}
redirect('./cms_hot_news.php');
?>