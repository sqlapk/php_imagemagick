<?php

$razm_image=$_SERVER['DOCUMENT_ROOT'] .'/img/38.jpg';
$image=new Imagick($razm_image);
function getBlurAmount($image){
    $size = getimagesize($image);
    $image = imagecreatefromjpeg($image);
    imagefilter($image, IMG_FILTER_EDGEDETECT);    
    $blur = 0;
    for ($x = 0; $x < $size[0]; $x++) {
        for ($y = 0; $y < $size[1]; $y++) {
            $blur += imagecolorat($image, $x, $y) & 0xFF;
        }
    }
    return $blur;
}

$e1 = getBlurAmount($razm_image);
echo "razm 27912060 <br>";
echo "img 84272760 <br>";
echo "38  84674703 <br>";
echo "DSC_0183  <br>";
echo "current".$e1;

// $e2 = getBlurAmount('http://upload.wikimedia.org/wikipedia/commons/thumb/0/01/Jonquil_flowers_at_f5.jpg/800px-Jonquil_flowers_at_f5.jpg');

// echo "Relative blur amount: first image " . $e1 / min($e1, $e2) . ", second image " . $e2 / min($e1, $e2);


?>