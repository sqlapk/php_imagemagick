<?php	
/* Чтение изображения */
// $im = new Imagick("C:/OpenServer/domains/localhost/img/img.jpg");

// путь до картинки 

$real_image=$_SERVER['DOCUMENT_ROOT'] .'/img/20150729_134058.jpg';
$path_image=$_SERVER['DOCUMENT_ROOT'] .'/img/dark.jpg';
$new_image=$_SERVER['DOCUMENT_ROOT'] .'/img/new.jpg';
// echo $path_image."<br>";
$im = new Imagick($tmp_image);
//$i=getImageGamma($im);



$target_mean = 46000;
$Img = new Imagick($real_image);
$mean = $Img->getImageChannelMean(imagick::CHANNEL_ALL)['mean'];

if($target_mean > $mean * 1.05){
	/* не изменится, если яркость в пределах 5% */
    $perc_diff = ($target_mean / $mean) * 100;
    $Img->modulateImage($perc_diff,100,100);
    $Img->writeImage($new_image);
    echo "Созданно новое изображение";
}
else{
	echo "Изображение валидно";
}



 $brightness=30;
 $contrast=11;
 $channel=4;
 $gdHandle=$path_image;

 function getBrightness($gdHandle) {
        $width = imagesx($gdHandle);
        $height = imagesy($gdHandle);

        $totalBrightness = 0;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($gdHandle, $x, $y);

                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;

                $totalBrightness += (max($red, $green, $blue) + min($red, $green, $blue)) / 2;
            }
        }

        imagedestroy($gdHandle);

        return ($totalBrightness / ($width * $height)) / 2.55;
    }



function brightnessContrastImage($path_image, $brightness, $contrast, $channel) {
    $imagick = new Imagick(realpath($path_image));
    $imagick->brightnessContrastImage($brightness, $contrast, $channel);
    header("Content-Type: image/jpeg");
    echo $imagick->getImageBlob();
}



function getColorStatistics($histogramElements, $colorChannel) {
    $colorStatistics = [];

    foreach ($histogramElements as $histogramElement) {
        $color = $histogramElement->getColorValue($colorChannel);
        $color = intval($color * 255);
        $count = $histogramElement->getColorCount();

        if (array_key_exists($color, $colorStatistics)) {
            $colorStatistics[$color] += $count;
        }
        else {
            $colorStatistics[$color] = $count;
        }
    }

    ksort($colorStatistics);
    
    return $colorStatistics;
}
    


function getImageHistogram($imagePath) {

    $backgroundColor = 'black';

    $draw = new \ImagickDraw();
    $draw->setStrokeWidth(0); //make the lines be as thin as possible

    $imagick = new \Imagick();
    $imagick->newImage(500, 500, $backgroundColor);
    $imagick->setImageFormat("png");
    $imagick->drawImage($draw);

    $histogramWidth = 256;
    $histogramHeight = 100; // the height for each RGB segment

    $imagick = new \Imagick(realpath($imagePath));
    //Resize the image to be small, otherwise PHP tends to run out of memory
    //This might lead to bad results for images that are pathologically 'pixelly'
    $imagick->adaptiveResizeImage(200, 200, true);
    $histogramElements = $imagick->getImageHistogram();

    $histogram = new \Imagick();
    $histogram->newpseudoimage($histogramWidth, $histogramHeight * 3, 'xc:black');
    $histogram->setImageFormat('png');

    $getMax = function ($carry, $item)  {
        if ($item > $carry) {
            return $item;
        }
        return $carry;
    };

    $colorValues = [
        'red' => getColorStatistics($histogramElements, \Imagick::COLOR_RED),
        'lime' => getColorStatistics($histogramElements, \Imagick::COLOR_GREEN),
        'blue' => getColorStatistics($histogramElements, \Imagick::COLOR_BLUE),
    ];

    $max = array_reduce($colorValues['red'] , $getMax, 0);
    $max = array_reduce($colorValues['lime'] , $getMax, $max);
    $max = array_reduce($colorValues['blue'] , $getMax, $max);

    $scale =  $histogramHeight / $max;

    $count = 0;
    foreach ($colorValues as $color => $values) {
        $draw->setstrokecolor($color);

        $offset = ($count + 1) * $histogramHeight;

        foreach ($values as $index => $value) {
            $draw->line($index, $offset, $index, $offset - ($value * $scale));
        }
        $count++;
    }

    $histogram->drawImage($draw);
    
    header( "Content-Type: image/png" );
    echo $histogram;
}



// getImageHistogram($path_image);

// echo getBrightness($path_image);

//brightnessContrastImage($path_image, $brightness, $contrast, $channel);



/* Миниатюра изображения */
// $im->thumbnailImage(200, null);

// /* Создание рамки для изображения */
// $im->borderImage(new ImagickPixel("white"), 5, 5);

// /* Клонируем изображение и зеркально поворачиваем его */
// $reflection = $im->clone();
// $reflection->flipImage();

// /* Создаём градиент. Это будет наложением для отражения */
// $gradient = new Imagick();

// /* Градиент должен быть достаточно большой для изображения и его рамки */
// $gradient->newPseudoImage($reflection->getImageWidth() + 10, $reflection->getImageHeight() + 10, "gradient:transparent-black");

// /* Наложение градиента на отражение */
// $reflection->compositeImage($gradient, imagick::COMPOSITE_OVER, 0, 0);

//  Добавляем прозрачность. Требуется ImageMagick 6.2.9 или выше 
// $reflection->setImageOpacity( 0.3 );

// /* Создаём пустой холст */
// $canvas = new Imagick();

// /* Холст должен быть достаточно большой, чтобы вместить оба изображения */
// $width = $im->getImageWidth() + 40;
// $height = ($im->getImageHeight() * 2) + 30;
// $canvas->newImage($width, $height, new ImagickPixel("black"));
// $canvas->setImageFormat("jpeg");

// /* Наложение оригинального изображения и отражения на холст */
// $canvas->compositeImage($im, imagick::COMPOSITE_OVER, 20, 10);
// $canvas->compositeImage($reflection, imagick::COMPOSITE_OVER, 20, $im->getImageHeight() + 10);

// /* Вывод изображения */

//header("Content-Type: image/jpeg; charset=utf-8");
//echo $canvas."wtf";
error_reporting(E_ALL);

?>