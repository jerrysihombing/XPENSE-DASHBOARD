<?php
 
date_default_timezone_set("asia/jakarta"); 
 
$data = str_replace(" ", "+", $_POST['binData']);
$data = base64_decode($data);
$fileName = "/tmp/" . date("ymdhis") . ".jpg";
$im = imagecreatefromstring($data);
 
if ($im !== false) {
    // Save image in the specified location
    header("Content-Type: image/jpeg");
    imagejpeg($im, $fileName);
    imagedestroy($im);
    echo "Saved successfully";
}
else {
    echo "An error occurred.";
}

?>