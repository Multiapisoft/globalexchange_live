<?php
die;
class resizeImage {

    function do_resize($w, $h, $filename = array(), $savepath, $forceSize = 0, $prefix = '', $quality = 100) {

        //get the filename of the image
        $name = $filename['name'];

        // get the temporary filename of the image
        $tmpname = $filename['tmp_name'];

        //get the extension of the image
        $extension = @strtolower(end(explode(".", $name)));

        // check wether supported files were uploaded or not
        if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "JPEG")) {
            echo ' Unsupported File Format';
        } else {

            // set the name of the resized image which is "[current timestamp][-][defined width][defined height][extension]"
            //$newf = $prefix."-".time()."-".str_replace(array(" ","_"),"-",$name);
            $rand = rand(10000000000, 99999999999);
            $newf = time() . "." . str_replace(array(" ", "_"), ".", $extension);

            // check the format and apply php function 
            if ($extension == "jpg" || $extension == "jpeg") {
                $im = imagecreatefromjpeg($tmpname);
            } else if ($extension == "png") {
                $im = imagecreatefrompng($tmpname);
            } else if ($extension == "gif") {
                $im = imagecreatefromgif($tmpname);
            }

            // $W is original width of the image
            // $H is original height of the image
            // $w is given width of the image
            // $h is given height of the image

            $W = imagesx($im);
            $H = imagesy($im);

            if ($w == 0) {
                $w = ($W / $H) * $h;
            } else if ($h == 0) {
                $h = ($H / $W) * $w;
            }

            // variable declared for calculation of width and height of the image
            $newH = 0;
            $newW = 0;

            if ($W > $w) {
                if ($H > $h) {
                    if (($H / $h) > ($W / $w)) {
                        $newH = $h;
                        $newW = $h * ($W / $H);
                    } else {
                        $newW = $w;
                        $newH = $w * ($H / $W);
                    }
                } else {
                    $newW = $w;
                    $newH = $w * ($H / $W);
                }
            } else {
                if ($H > $h) {
                    $newH = $h;
                    $newW = $h * ($W / $H);
                } else {
                    $newH = $H;
                    $newW = $W;
                }
            }

            // IF FORCESIZE FLAG IS SET. INGORE THE ASPECT RATIO 
            if ($forceSize == 1) {
                $newW = $w;
                $newH = $h;
            }

            // new temporary file is generated width new width and new height 
            $new_temp_image = imagecreatetruecolor($newW, $newH);

            if ($extension == "jpg" || $extension == "jpeg") {
                imagecopyresampled($new_temp_image, $im, 0, 0, 0, 0, $newW, $newH, $W, $H);

                // imageinterlace is used for generating progressive image
                imageinterlace($new_temp_image, true);

                // finally create a new resized image
                imagejpeg($new_temp_image, $savepath . $newf, $quality);
            } else if ($extension == "png") {
                imagealphablending($new_temp_image, false);
                imagesavealpha($new_temp_image, true);
                $transparent = imagecolorallocatealpha($new_temp_image, 255, 255, 255, 127);

                imagefilledrectangle($new_temp_image, 0, 0, $newW, $newH, $transparent);

                imagecopyresampled($new_temp_image, $im, 0, 0, 0, 0, $newW, $newH, $W, $H);

                imagepng($new_temp_image, $savepath . $newf, 9);
            } else if ($extension == "gif") {

                imagecopyresampled($new_temp_image, $im, 0, 0, 0, 0, $newW, $newH, $W, $H);

                // set the transpatent color in the given image
                $tranparent = imagecolortransparent($im);

                // check if we have a specific transparent color
                if ($tranparent >= 0) {

                    // get the original rgb value
                    $transparent_color = imagecolorsforindex($im, $tranparent);

                    // Allocate the same color in the new image resource
                    $tranparent = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);

                    // fill the background of the new image with allocated color.
                    imagefill($new_temp_image, 0, 0, $tranparent);

                    // Set the background color for new image to transparent
                    imagecolortransparent($new_temp_image, $tranparent);
                }

                imagegif($new_temp_image, $savepath . $newf);
            }

            imagedestroy($new_temp_image);

            // after success this class returns the name of the new file
            return $newf;
        }
    }

}

?>