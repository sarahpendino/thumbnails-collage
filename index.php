<?php
/**
 * Combined collage
 * A simple PHP script to generate a combined collage of thumbnails.
 * @author Sarah Pendino <sarah.pendino@gmail.com>
 * @link https://dogma2020.com
 */
/**

//Uncomment the following lines during development, to see the errors that may appear.
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require_once "lib/ResizeImage.php";

define('PATH',dirname(__FILE__).'/img/');//
define('TEMP_PATH',dirname(__FILE__).'/temp/');


/*
Some options:
-------------
(*) $output   : output file name with all images combined in collage,
(*) $minWidth : if the directory has small images that you want to ignore, set this
           constant. If you want to combine all the images, you must set 0,
(*) $width    : final width for full collage resized
(*) $height   : desired for the collage complete, before resize it.

*/
 
$output = 'output.jpg';
$minWidth = 25; //25px
$width = 500; //500px
$height = 350; //350px

$images = scandir(PATH);
//exclude current and parent directory from the array.
$images= array_diff( $images, array(".", "..") );

foreach ($images as $img) {
    $resize = new ResizeImage(PATH.$img);
    if ($resize->origWidth <= $minWidth){
        //delete thoses images with a width less than defined
        unlink($path.$tapa);
    } else {
        if ($resize->origHeight > $height){
            //adjust the images to the desired heigth, keeping aspect ratio
            $relWidth = $height;
            $resize->resizeTo($relWidth, $height, 'maxHeight');//by height
            $resize->saveImage(TEMP_PATH.$img);
        } else {
            //just copy move the image fro processing
            copy(PATH.$img,TEMP_PATH.$img);
        }
    }
}

//Once the images have been processed, we proceed to combine the images from the temporary directory.

$temp = scandir(TEMP_PATH);
$data = array();
$collage = new Imagick();
foreach ($temp as $file) {
    if ($file <> '.' & $file <> '..' and !is_dir($file)) {
         $imagick = new Imagick(TEMP_PATH.$file);
         $collage->addImage($imagick);
     }
}

$collage->resetIterator();

$canva = $collage->appendImages(false); //true -> for vertical collage

$canva->setImageFormat('jpg');
$canva->getImageBlob();
// give a name to file
$canva->setImageFilename($output);
// write the image
$canva->writeImage();

$temp = scandir(TEMP_PATH);
//empty the temp directory
foreach ($temp as $file) {
    if ($file <> '.' & $file <> '..' and !is_dir($file)) {
        unlink(TEMP_PATH.$file);
     }
}


$resize = new ResizeImage(dirname(__FILE__).'/'.$output);
$relHeight = $width;
$resize->resizeTo($width,$relHeight, 'maxWidth');//by width
$resize->saveImage(dirname(__FILE__).'/'.$output);


//redirect to generated collage
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$destination = $url.$output;

header('Location: '.$destination);

//That's all... Enjoy! :)



  
