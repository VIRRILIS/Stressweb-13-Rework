<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */

@ini_set( 'display_errors', '1' );
@error_reporting( E_ALL );
 
class Captcha
{
    private static $im;
    private static $width = 60;
    private static $height = 20;
    private static $code = 'click me';
    private static $flag = false;

    public static function getInstance()
    {
        self::create();
        self::bg();
        if ( isset($_GET['rndval']) ) {
            self::bg2();
            self::noize();
            self::setcode();
            self::$flag = true;
        }
        self::show();
    }

    private static function create()
    {
        self::$im = imagecreate( self::$width, self::$height ) or die( "You must activate GD library on your web server" );
    }

    private static function gen_code( $num = 5 )
    {
        $key = '';
        $chaine = "ABCDEFGHKLMNPRSTUVWXYZ123456789";
        for ( $i = 0; $i < $num; $i++ )
            $key .= $chaine[rand() % strlen( $chaine )];
        return $key;
    }

    private static function bg()
    {
        $black = imagecolorallocate( self::$im, 0, 0, 0 );
    }

    private static function bg2()
    {
        $white = imagecolorallocate( self::$im, 255, 255, 255 );
        $grey = imagecolorallocate( self::$im, 128, 128, 128 );
    }

    private static function noize()
    {
        for ( $i = 0; $i < rand(2,20); $i++ ) {
            $color = imagecolorallocate( self::$im, rand(0, 128), rand(0, 128), rand(0, 128) );
            imageline( self::$im, rand(0, self::$width), rand(0, self::$height), rand(0, self::$width), rand(0, self::$height), $color );
            imageellipse( self::$im, rand(0, self::$width), rand(0, self::$height), 10, 10, $color );
        }
    }

    private static function setcode()
    {
        self::$code = self::gen_code( rand(5,6) );
        $_SESSION['seccode'] = self::$code;
    }

    private static function show()
    {
        if ( self::$flag ) {
            for ( $i = 0; $i < strlen(self::$code); $i++ )
                $key[$i] = substr( self::$code, $i, 1 );

            $i = 5;
            foreach ( $key as $value ) {
                $color = imagecolorallocate( self::$im, rand(128, 255), rand(128, 255), rand(128, 255) );
                imagestring( self::$im, rand(2,7), $i, rand(0, 6), $value, $color );
                $i += rand(7,10);
            }
            $quality = 50;
        } else {
            imagestring( self::$im, 2, 5, 3, self::$code, imagecolorallocate(self::$im, 14, 233, 91) );
            $quality = 100;
        }
        imagejpeg( self::$im, null, $quality );
    }
}
session_start();
header( "Content-type: image/jpeg" );
Captcha::getInstance();

?>