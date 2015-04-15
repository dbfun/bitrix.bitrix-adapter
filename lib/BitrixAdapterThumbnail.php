<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

class BitrixAdapterThumbnail {

  /**
   * Gets dummy picture.
   *
   */
   
  public static function getDummy() 
  {
    return '/image/no-foto.jpg';
  }
  
  /**
   * Gets filename from various types.
   *
   */
  
  private static function pullFilename($fileName)
  {
    return is_array($fileName) ? $fileName['SRC'] : $fileName;
  }
  
  /**
   * Crop image and return link.
   *
   */
  
  private static $pathToCropImg = 'upload/resize_cache/iblock/';
  static function crop($_filename, $width, $height) {
    $filename = self::pullFilename($_filename);
    if (empty($filename)) return null;
    $absPath = $_SERVER["DOCUMENT_ROOT"].'/';
    $absPathSource = $absPath;
    $absPathSourceFileName = substr($filename, 1, 1) == '/' ? $absPathSource.$filename : $absPathSource.'/'.$filename;
    $md5Filename = md5($filename)."-c-$width-$height";
    
    $imgExt = pathinfo($filename, PATHINFO_EXTENSION);

    if (file_exists($absPath.self::$pathToCropImg.$md5Filename.'.'.$imgExt)) return '/'.self::$pathToCropImg.$md5Filename.'.'.$imgExt;
    
    if (!file_exists($absPathSourceFileName)) return null;
    
    list($width_orig, $height_orig) = getimagesize($absPathSourceFileName);
    $width_orig_  = $width_orig;
    $height_orig_ = $height_orig;
    
    //////////// определение пропорций, под которые подгонять картинку.
    $k = $width_orig / $height_orig;
    $k_out = $width / $height;
    if ( $k > $k_out) $width_orig = $height_orig * $k_out ;  else $height_orig = $width_orig / $k_out;
    
    ///////////////////////// если картинка не попадает под пропорции, то часть оригинала нужно обрезать.
    $ratio_orig = $width_orig/$height_orig;
    if ($width/$height > $ratio_orig) {$width = $height*$ratio_orig;} else {$height = $width/$ratio_orig;}
    
    //// отцентровать будущую картинку отцентровать по центру оригинала
    $x_shift =  ($width_orig_  - $width_orig) /2;
    $y_shift =  ($height_orig_  - $height_orig) /2;
    
    $image_p = imagecreatetruecolor($width, $height);
    switch (exif_imagetype($absPathSourceFileName)) {
      case IMAGETYPE_JPEG:
        $image = ImageCreateFromJPEG($absPathSourceFileName);
        imagecopyresampled($image_p, $image, 0, 0, $x_shift, $y_shift, $width, $height, $width_orig, $height_orig);
        ImageJPEG($image_p, $absPath.self::$pathToCropImg.$md5Filename.'.'.$imgExt, 87);
        return '/'.self::$pathToCropImg.$md5Filename.'.'.$imgExt;
      case IMAGETYPE_GIF:
        $image = ImageCreateFromGIF($absPathSourceFileName);
        imagecopyresampled($image_p, $image, 0, 0, $x_shift, $y_shift, $width, $height, $width_orig, $height_orig);
        ImageGIF($image_p, $absPath.self::$pathToCropImg.$md5Filename.'.'.$imgExt);
        return '/'.self::$pathToCropImg.$md5Filename.'.'.$imgExt;
      case IMAGETYPE_PNG:
        $image = ImageCreateFromPNG($absPathSourceFileName);
        imagecopyresampled($image_p, $image, 0, 0, $x_shift, $y_shift, $width, $height, $width_orig, $height_orig);
        ImagePNG($image_p, $absPath.self::$pathToCropImg.$md5Filename.'.'.$imgExt);
        return '/'.self::$pathToCropImg.$md5Filename.'.'.$imgExt;
      case IMAGETYPE_WBMP:
        $image = ImageCreateFromWBMP($absPathSourceFileName);
        imagecopyresampled($image_p, $image, 0, 0, $x_shift, $y_shift, $width, $height, $width_orig, $height_orig);
        ImageWBMP($image_p, $absPath.self::$pathToCropImg.$md5Filename.'.'.$imgExt);
        return '/'.self::$pathToCropImg.$md5Filename.'.'.$imgExt;
      default: return $filename;
      }
    }
  
  /**
   * Fit image and return link.
   *
   */
   
  private static $pathToResizeImg = 'upload/resize_cache/iblock/';
  public static function fit($_filename, $seriaWidth, $seriaHeight, $crop = false, $fillColor = false, $border = false) {
    $filename = self::pullFilename($_filename);
    if (empty($filename)) return null;
    $absPath = $_SERVER["DOCUMENT_ROOT"].'/';
    $absPathSource = $absPath;
    $md5Filename = md5($filename)."-f-$seriaWidth-$seriaHeight".($fillColor ? '-r'.$fillColor[0].'-b'.$fillColor[1].'-g'.$fillColor[2] : null);
    $absPathSourceFileName = substr($filename, 1, 1) == '/' ? $absPathSource.$filename : $absPathSource.'/'.$filename;
    $imgExt = pathinfo($filename, PATHINFO_EXTENSION);
    if (!file_exists($absPath.self::$pathToResizeImg.$md5Filename.'.'.$imgExt)) {
      if (!file_exists($absPathSourceFileName)) return null;
      list($width, $height) = getimagesize($absPathSourceFileName);
      list($new_width, $new_height) = self::getNewSize($seriaWidth, $seriaHeight, $width, $height, $crop);
      $image_p = imagecreatetruecolor($seriaWidth, $seriaHeight);
      if ($fillColor) {
        $ink = imagecolorallocate ($image_p, $fillColor[0], $fillColor[1], $fillColor[2]);
        } else {
        $ink = imagecolorallocate ($image_p, 255, 255, 255);
        }
      imagefill($image_p, 0, 0, $ink);
      
      if ($border) {
        $grey = imagecolorallocate ($image_p, 201, 201, 201);
      }
          
      $newCoordX = ($new_width < $seriaWidth ? round(($seriaWidth - $new_width) / 2) : 0);
      $newCoordY = ($new_height < $seriaHeight ? round(($seriaHeight - $new_height) / 2) : 0);
      
      switch (exif_imagetype($absPathSourceFileName)) {
        case IMAGETYPE_JPEG:
          $out = ImageCreateFromJPEG($absPathSourceFileName);
          imagecopyresampled($image_p, $out, $newCoordX, $newCoordY, 0, 0, $new_width, $new_height, $width, $height);
          if ($border) {
            imagerectangle($image_p, $newCoordX, $newCoordY, ($newCoordX + $new_width), ($newCoordY + $new_height), $grey);
          }
          ImageJPEG($image_p, $absPath.self::$pathToResizeImg.$md5Filename.'.'.$imgExt, 87);
          return '/'.self::$pathToResizeImg.$md5Filename.'.'.$imgExt;
        case IMAGETYPE_GIF:
          $out = ImageCreateFromGIF($absPathSourceFileName);
          imagecopyresampled($image_p, $out, $newCoordX, $newCoordY, 0, 0, $new_width, $new_height, $width, $height);
          if ($border) {
            imagerectangle($image_p, $newCoordX, $newCoordY, ($newCoordX + $new_width), ($newCoordY + $new_height), $grey);
          }
          ImageGIF($image_p, $absPath.self::$pathToResizeImg.$md5Filename.'.'.$imgExt);
          return '/'.self::$pathToResizeImg.$md5Filename.'.'.$imgExt;
        case IMAGETYPE_PNG:
          $out = ImageCreateFromPNG($absPathSourceFileName);
          imagecopyresampled($image_p, $out, $newCoordX, $newCoordY, 0, 0, $new_width, $new_height, $width, $height);
          if ($border) {
            imagerectangle($image_p, $newCoordX, $newCoordY, ($newCoordX + $new_width), ($newCoordY + $new_height), $grey);
          }
          ImagePNG($image_p, $absPath.self::$pathToResizeImg.$md5Filename.'.'.$imgExt);
          return '/'.self::$pathToResizeImg.$md5Filename.'.'.$imgExt;
        default:
          return $filename;

        }
      } else return '/'.self::$pathToResizeImg.$md5Filename.'.'.$imgExt;
    }
  
  /**
   * Gets image new sizes.
   *
   */
   
  private static function getNewSize($wReal, $hReal, $width, $height, $crop) {
    $ratio = $width/$height;
    $r_w = $wReal/$width;
    $r_h = $hReal/$height;
    $zoom = min($r_w, $r_h);
    if (!$crop) {
      if ($r_w > $r_h) return array(round($zoom * $width), $hReal);
      else return array($wReal, round($zoom * $height));
      }
    return array($wReal, $hReal);
    }
  }