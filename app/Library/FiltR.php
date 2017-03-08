<?php

/**
 * FiltR.php
 *
 * Image treatement database based on Imagick
 *
 * @author     Valentin Dufois
 * @version    0.1 - 06 March 2017
 */


class FiltR
{
    public static $formatsAllowed = ["jpg", "jpeg", "png", "bmp", "bmp2", "bmp3", "tiff"];
    public static $availableFilters = ["amaro", "brannan", "clarendon", "earlybird", "hefe", "hudson", "inkwell", "kelvin", "lark", "lofi", "mayfair", "moon", "nashville", "reyes", "rise", "sierra", "sutro", "toaster",  "valencia", "walden", "willow", "xproii"];



    private static $BlackVignetteOverlay = null;
    private static $BlackVignetteOverlayPath = "/Eikona/app/Library/FiltR/BlackVignetteOverlay.png";

    private static $WhiteSpot = null;
    private static $WhiteSpotPath = "/Eikona/app/Library/FiltR/CenterWhite.png";




    /*************************************************/
    /********************* Basics ********************/
    /*************************************************/

    /**
     * Try to get the given image, and silence errors for better handling
     * @param  [[Type]] $src [[Description]]
     * @return boolean  This function return the image on success, false on failure
     */
    private static function getImage($src)
    {
        try
        {
            $img = new Imagick($src);
        }
        catch(Exception $e)
        {
            return false;
        }

        //Make sure rotation is OK
        self::autoRotateImage($img);

        return $img;
    }




    private static function autoRotateImage($image)
    {
        $orientation = $image->getImageOrientation();

        switch($orientation) {
            case imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateimage("#000", 180); // rotate 180 degrees
                break;

            case imagick::ORIENTATION_RIGHTTOP:
                $image->rotateimage("#000", 90); // rotate 90 degrees CW
                break;

            case imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateimage("#000", -90); // rotate 90 degrees CCW
                break;
        }

        // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
        $image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
    }



    /**
     * Save the given image to the given destination, convert image if necessary
     * @param  string         $srcIMG  source Image
     * @param  string         $destIMG destination Image
     * @return boolean Result of the operation
     */
    public static function saveTo($srcIMG, $destIMG)
    {
        $img = self::getImage($srcIMG);

        if(!$img)
            return false;

        //Allowed format?
        if(!in_array(strtolower($img->getImageFormat()), self::$formatsAllowed))
            return false;

        //Resize Image
        self::resize($img, 1536, 1536);

        //Remove alpha channel
        $img->setImageBackgroundColor('white');
        $img->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);

        //Write new image
        return $img->writeImage($destIMG);
    }




    /*************************************************/
    /******************* Utilities *******************/
    /*************************************************/


    /**
     * Save the given image to the given destination, convert image if necessary
     * @param  ImagickObject  $img  source Image
     * @param  string         $destIMG destination Image
     * @return ImagickObject  Result of the operation
     */
    public static function resize(Imagick $img, $maxWidth = 1536, $maxHeight = 1536, $conserveProportions = true, $filter = imagick::FILTER_CATROM)
    {
        $img->resizeImage($maxWidth, $maxHeight, $filter, .6, $conserveProportions);
        $img->unsharpMaskImage(.5, 1, 1.7, .02);

        return $img;
    }




    private static function getClut($filter, $destWidth, $destHeight)
    {
        if($destWidth >= 1024 || $destHeight >= 1024)
        {
            //echo "CLUT16";
            $clut = self::getImage($_SERVER['DOCUMENT_ROOT']."/Eikona/app/Library/FiltR/halds/".$filter."-16.png");
        }
        else
        {
            //echo "CLUT8";
            $clut = self::getImage( $_SERVER['DOCUMENT_ROOT']."/Eikona/app/Library/FiltR/halds/".$filter.".png");
        }

        return $clut;
    }



    /**
     * Print a proof of all the filter applied to the picture
     * @param [[Type]] [$sampleWidth = 150] [[Description]]
     */
    public static function proof($srcIMG, $destIMG)
    {
        $img = self::getImage($srcIMG);

        if(!$img)
            return false;



        $sampleSize = 150;



        $img->setImageCompressionQuality(40);

        //Resize image to output size
        self::resize($img, $sampleSize, $sampleSize);

        $geo = $img->getImageGeometry();

        //Create and fill the stack
        $stack = new Imagick();

        $stack->addImage($img);

        foreach(self::$availableFilters as $filter)
        {
            $method = "_".$filter;

            $temp = $img->getImage();

            $stack->addImage(self::$method($temp));
        }

        $montage = $stack->montageImage(new ImagickDraw(), '3', $geo['width']."x".$geo['height'], 0, '0');

        return $montage->writeImage($destIMG);
    }





    private static function getBlackVignette($sizeX, $sizeY, $factor = 1)
    {
        if(is_null(self::$BlackVignetteOverlay))
            self::$BlackVignetteOverlay = self::getImage($_SERVER['DOCUMENT_ROOT'].self::$BlackVignetteOverlayPath);

        $vignette = self::$BlackVignetteOverlay->getImage();

        self::resize($vignette, $sizeX, $sizeY, false, imagick::FILTER_TRIANGLE);

        if($factor != 1)
            $vignette->evaluateImage(Imagick::EVALUATE_MULTIPLY, $factor, Imagick::CHANNEL_ALPHA);

        return $vignette;
    }





    private static function getWhiteSpot($sizeX, $sizeY, $factor = 1)
    {
        if(is_null(self::$WhiteSpot))
            self::$WhiteSpot = self::getImage($_SERVER['DOCUMENT_ROOT'].self::$WhiteSpotPath);

        $vignette = self::$WhiteSpot->getImage();

        self::resize($vignette, $sizeX, $sizeY, false, imagick::FILTER_TRIANGLE);

        if($factor != 1)
            $vignette->evaluateImage(Imagick::EVALUATE_MULTIPLY, $factor, Imagick::CHANNEL_ALPHA);

        return $vignette;
    }





    /*************************************************/
    /******************** Filters ********************/
    /*************************************************/


    /**
     * Handle calls to the filters
     * @param string $filter Filter to apply
     * @param string         Source file
     * @param string         Destination file
     * @return boolan true on success, false on failure
     */
    public static function __callStatic($filter, $args)
    {
        //Confirm filter
        if(!in_array($filter, self::$availableFilters))
            return false;

        $method = "_".$filter;

        //Load source
        if(!file_exists($args[0]))
            return false;

        $srcIMG = $args[0];
        $destIMG = $args[1];



        $img = self::getImage($srcIMG);

        if(!$img)
            return false;



        ////////////// Speed up testing
        //self::resize($img, 2048, 2048);
        //////////////



        //Apply the filter
        self::$method($img);

        if(!$img)
            return false;

        //Write new image
        return $img->writeImage($destIMG);
    }







    /**
     * Amaro Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _amaro(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("amaro", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*2, $geo['height']*2, 1.5);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY,  $geo['width']*-0.5, $geo['height']*-0.5);

        //Apply center white dot
        $overlay = self::getWhiteSpot($geo['width'], $geo['height'], 1);
        $img->compositeImage($overlay, imagick::COMPOSITE_SOFTLIGHT, 0, 0);

        return $img;
    }


    /**
     * Brannan Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _Brannan(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("brannan", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply center white dot
        $overlay = self::getWhiteSpot($geo['width'], $geo['height'], 1);
        $img->compositeImage($overlay, imagick::COMPOSITE_SOFTLIGHT, 0, 0);

        return $img;
    }


    /**
     * Clarendon Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _clarendon(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("clarendon", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        return $img;
    }


    /**
     * EarlyBird Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _earlybird(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("earlybird", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*1.8, $geo['height']*1.8, 1.5);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY,  $geo['width']*-0.4, $geo['height']*-0.4);


        //Apply center white dot
        $overlay = self::getWhiteSpot($geo['width'], $geo['height'], 1);
        $img->compositeImage($overlay, imagick::COMPOSITE_SOFTLIGHT, 0, 0);

        return $img;
    }


    /**
     * Hefe Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _hefe(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("hefe", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getImage($_SERVER['DOCUMENT_ROOT']."/Eikona/app/Library/FiltR/BlackSquareVignette.png");
        $vignette->resizeImage($geo['width'] * 1.05, $geo['height'] * 1.05, imagick::FILTER_TRIANGLE, 1);

        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY , $geo['width'] * -0.025, $geo['height'] * -0.025);


        return $img;
    }


    /**
     * Hudson Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _hudson(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("hudson", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*1.4, $geo['height']*1.4, 1);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY,  $geo['width']*-0.2, $geo['height']*-0.2);

        return $img;
    }


    /**
     * Inkwell Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _inkwell(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("inkwell", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        return $img;
    }


    /**
     * Inkwell Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _kelvin(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("kelvin", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        return $img;
    }


    /**
     * Lark Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _lark(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("lark", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        return $img;
    }


    /**
     * Lo-fi Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _lofi(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("lo-fi", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*1.5, $geo['height']*1.5, 1.2);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY, $geo['width'] * -0.25, $geo['height'] * -0.25);


        //Apply center white dot
        $overlay = self::getWhiteSpot($geo['width'], $geo['height'], 1);
        $img->compositeImage($overlay, imagick::COMPOSITE_SOFTLIGHT, 0, 0);

        return $img;
    }


    /**
     * Mayfair Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _mayfair(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("mayfair", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*2, $geo['height']*2, 1.5);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY,  $geo['width']*-0.5, $geo['height']*-0.5);

        //Apply whites flares
        $overlayPath = $_SERVER['DOCUMENT_ROOT']."/Eikona/app/Library/FiltR/ThreeDots.png";
        $overlay = self::getImage($overlayPath);

        $overlay->resizeImage($geo['width'], $geo['height'], imagick::FILTER_TRIANGLE, 1);
        $overlay->evaluateImage(Imagick::EVALUATE_DIVIDE, 2, Imagick::CHANNEL_ALPHA);

        $img->compositeImage($overlay, imagick::COMPOSITE_OVERLAY , 0, 0);

        return $img;
    }


    /**
     * Moon Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _moon(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("moon", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        return $img;
    }


    /**
     * nashville Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _nashville(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("nashville", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        return $img;
    }


    /**
     * Reyes Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _reyes(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("reyes", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        return $img;
    }


    /**
     * Rise Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _rise(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("rise", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*1.8, $geo['height']*1.8, 2);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY,  $geo['width']*-0.4, $geo['height']*-0.4);


        //Apply center white dot
        $overlay = self::getWhiteSpot($geo['width'], $geo['height'], 1/3);
        $img->compositeImage($overlay, imagick::COMPOSITE_SOFTLIGHT, 0, 0);

        return $img;
    }


    /**
     * Sierra Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _sierra(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("sierra", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette-
        $vignette = self::getBlackVignette($geo['width']*1.8, $geo['height']*1.8, 2);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY,  $geo['width']*-0.4, $geo['height']*-0.4);


        //Apply center white dot
        $overlay = self::getWhiteSpot($geo['width'], $geo['height'], 1/2);
        $img->compositeImage($overlay, imagick::COMPOSITE_SOFTLIGHT, 0, 0);

        return $img;
    }


    /**
     * Sutro Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _sutro(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("sutro", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*1.5, $geo['height']*1.5, 1.2);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY, $geo['width'] * -0.25, $geo['height'] * -0.25);

        return $img;
    }


    /**
     * Toaster Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _toaster(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("toaster", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*1.5, $geo['height']*1.5, 2);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY, $geo['width'] * -0.25, $geo['height'] * -0.25);


        //Apply center white dot
        $overlay = self::getWhiteSpot($geo['width'], $geo['height'], 1/3);
        $img->compositeImage($overlay, imagick::COMPOSITE_SOFTLIGHT, 0, 0);

        return $img;
    }


    /**
     * Valencia Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _valencia(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("valencia", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        return $img;
    }


    /**
     * Walden Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _walden(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("walden", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply Vignette
        $vignette = self::getBlackVignette($geo['width']*1.5, $geo['height']*1.5, 1);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY, $geo['width'] * -0.25, $geo['height'] * -0.25);

        return $img;
    }


    /**
     * Willow Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _willow(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("willow", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        //Apply vignette
        $vignette = self::getBlackVignette($geo['width']*2, $geo['height']*2, 1.5);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY,  $geo['width']*-0.5, $geo['height']*-0.5);

        //Apply whites flares
        $overlayPath = $_SERVER['DOCUMENT_ROOT']."/Eikona/app/Library/FiltR/ThreeDots.png";
        $overlay = self::getImage($overlayPath);

        $overlay->resizeImage($geo['width'], $geo['height'], imagick::FILTER_TRIANGLE, 1);
        $overlay->evaluateImage(Imagick::EVALUATE_DIVIDE, 2, Imagick::CHANNEL_ALPHA);

        $img->compositeImage($overlay, imagick::COMPOSITE_OVERLAY , 0, 0);

        return $img;
    }


    /**
     * X Pro II Filter
     * @private
     * @param  Imagick $img Image to apply the filter to
     * @return Imagick Image with filter applied
     */
    private static function _XProII(Imagick $img)
    {
        $geo = $img->getImageGeometry();

        $clut = self::getClut("XProII", $geo['width'], $geo['height']);

        $img->haldClutImage($clut);

        $vignette = self::getBlackVignette($geo['width']*2, $geo['height']*2, 1.5);
        $img->compositeImage($vignette, imagick::COMPOSITE_OVERLAY,  $geo['width']*-0.5, $geo['height']*-0.5);

        return $img;
    }
}

//FiltR::amaro("FiltR/src3.jpg", "FiltR/testRun/amaro.jpg");
//FiltR::brannan("FiltR/src3.jpg", "FiltR/testRun/brannan.jpg");
//FiltR::clarendon("FiltR/src3.jpg", "FiltR/testRun/clarendon.jpg");
//FiltR::earlybird("FiltR/src3.jpg", "FiltR/testRun/earlybird.jpg");
//FiltR::hefe("FiltR/src3.jpg", "FiltR/testRun/hefe.jpg");
//FiltR::hudson("FiltR/src3.jpg", "FiltR/testRun/hudson.jpg");
//FiltR::inkwell("FiltR/src3.jpg", "FiltR/testRun/inkwell.jpg");
//FiltR::kelvin("FiltR/src3.jpg", "FiltR/testRun/kelvin.jpg");
//FiltR::lark("FiltR/src3.jpg", "FiltR/testRun/lark.jpg");
//FiltR::lofi("FiltR/Ressources/src3.jpg", "FiltR/testRun/lofi.jpg");
//FiltR::moon("FiltR/src3.jpg", "FiltR/testRun/moon.jpg");
//FiltR::mayfair("FiltR/src3.jpg", "FiltR/testRun/mayfair.jpg");
//FiltR::nashville("FiltR/src3.jpg", "FiltR/testRun/nashville.jpg");
//FiltR::reyes("FiltR/src3.jpg", "FiltR/testRun/reyes.jpg");
//FiltR::rise("FiltR/src3.jpg", "FiltR/testRun/rise.jpg");
//FiltR::sierra("FiltR/src3.jpg", "FiltR/testRun/sierra.jpg");
//FiltR::sutro("FiltR/src3.jpg", "FiltR/testRun/sutro.jpg");
//FiltR::toaster("FiltR/src3.jpg", "FiltR/testRun/toaster.jpg");
//FiltR::valencia("FiltR/src3.jpg", "FiltR/testRun/valencia.jpg");
//FiltR::walden("FiltR/src3.jpg", "FiltR/testRun/walden.jpg");
//FiltR::willow("FiltR/src3.jpg", "FiltR/testRun/willow.jpg");
//FiltR::XProII("FiltR/src3.jpg", "FiltR/testRun/xpro2.jpg");

//FiltR::proof("FiltR/Ressources/src3.jpg");

?>
