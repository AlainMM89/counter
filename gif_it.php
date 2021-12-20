<?php


//Leave all this stuff as it is
$time_zone = (!empty($_GET['time_zone'])) ? $_GET['time_zone'] : 'Europe/Zurich';
date_default_timezone_set($time_zone);
include 'GIFEncoder.class.php';
//$time = $_GET['time'];
//$future_date = new DateTime(date('r', strtotime($time)));
$future_date = new DateTime(date('r', strtotime("2021-12-24 17:45:12")));
//$future_date = new DateTime(date('r', strtotime('+5 day', time())));
$time_now = time();
$now = new DateTime(date('r', $time_now));
$frames = array();
$delays = array();


// Your image link
// example:
//$pathToImage = 'images/img.png';
$pathToImage = 'images/Countdown_IT.png';

$image = imagecreatefrompng($pathToImage);

$delay = 100;// milliseconds

$font = array(
    'size' => 42, // Font size, in pts usually.
    'angle' => 0, // Angle of the text
    'x-offset' => 47, // The larger the number the further the distance from the left hand side, 0 to align to the left.
    'y-offset' => 95, // The vertical alignment, trial and error between 20 and 60.
    'file' => dirname(__FILE__) . '/' . 'Futura.ttc', // Font path
    'color' => imagecolorallocate($image, 333, 333, 333), // RGB Colour of the text
);
for ($i = 0; $i <= 60; $i++) {

    $interval = date_diff($future_date, $now);

    if ($future_date < $now) {
        // Open the first source image and add the text.
        $image = imagecreatefrompng($pathToImage);
        $text = $interval->format('0      d   0          0');
        imagettftext($image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $text);
        ob_start();
        imagegif($image);
        $frames[] = ob_get_contents();
        $delays[] = $delay;
        $loops = 1;
        ob_end_clean();
        break;
    } else {
        // Open the first source image and add the text.
        $image = imagecreatefrompng($pathToImage);
        $text = $interval->format('%D      %H        %I        %S');
        $textDays = $interval->format('%D'); // Setting format for Hours
        $textHours = $interval->format('%H'); // Setting format for Hours
        $textMinutes = $interval->format('%I'); // Setting format for Minutes
        $textSeconds = $interval->format('%S'); // Setting format for Seconds

        // Set Offset for each number set
        imagettftext(
            $image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $textDays
        );
        imagettftext(
            $image, $font['size'], $font['angle'], $font['x-offset'] +138, $font['y-offset'], $font['color'], $font['file'], $textHours
        );
        imagettftext(
            $image, $font['size'], $font['angle'], $font['x-offset'] + 276, $font['y-offset'], $font['color'], $font['file'], $textMinutes
        );
        imagettftext(
            $image, $font['size'], $font['angle'], $font['x-offset'] + 414, $font['y-offset'], $font['color'], $font['file'], $textSeconds
        );

//        for testing:
//        imagegif($image, '/home/mark/www/mvc-framework/public/test/' . $i . '.gif');

        ob_start();
        imagegif($image);
        $frames[] = ob_get_contents();
        $delays[] = $delay;
        $loops = 0;
        ob_end_clean();
    }
    $now->modify('+1 second');
}


//expire this image instantly
//header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
//header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
//
//header( 'Cache-Control: no-store, no-cache, must-revalidate' );
//header( 'Cache-Control: post-check=0, pre-check=0', false );
//header( 'Pragma: no-cache' );

$gif = new AnimatedGif($frames, $delays, $loops);
$gif->display();
