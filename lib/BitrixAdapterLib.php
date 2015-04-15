<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

class BitrixAdapterLib
{

  /**
   * Split array to some parts (vertically).
   *
   */
  
  public static function splitArray($items, $numCols)
  {
    if (!is_array($items) || count($items) == 0) return array();
    $numCols = (int)$numCols; if ($numCols < 2) return array(1 => $items);
    $ret = array(); $i = 0; $s = 0;
    foreach ($items as $item) {
      $i++; if($i > $numCols) {$s++; $i = 1;}
      $ret[$s][$i] = $item;
    }
    return $ret;
  }
  
  /**
   * Split array to some parts (horizontally).
   *
   */
    
  public static function altSplitArray($items, $numCols)
  {
    if (!is_array($items) || count($items) == 0) return array();
    $numCols = (int)$numCols;
    if ($numCols < 2) return array(1 => $items);
    $ret = array(); $i = 0;
    foreach ($items as $item)
    {
      $ret[$i++ % $numCols][] = $item;
    }
    return $ret;
  }
  
  /**
   * Format Bitrix date with format.
   *
   */
  
  public function dateFormat($format, $_date)
  {
    $date = is_int($_date) ? $_date : strtotime($_date);
    $ret = date($format, $date);
    $lang = BFactory::getLanguage();
    if ($lang != 'en')
    {
      $ret = preg_replace('#january|february|march|april|may|june|july|august|september|october|november|december#i',
        self::getMonthName(date('n', $date), $lang),
        $ret
        );
    }
    return $ret;
  }
  
  /**
   * Gets month name by its number.
   *
   */
  
  private static $fullMonths = array(
    'ru' => array(1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня', 
      7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11=> 'ноября', 12 => 'декабря')
    );
  public function getMonthName($month, $lang)
  {
    return self::$fullMonths[$lang][$month];
  }
  
  public static function explodeAndTrim($delimiter, $string)
  {
    $ret = explode($delimiter, $string);
    if(count($ret) > 0) foreach ($ret as &$item)
    {
      $item = trim($item);
    }
    return $ret;
  }
  
  public static function mb_ucfirst($str, $enc = 'utf-8')
  {
    return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc); 
  }
  
  public static function cutString($string, $maxlen, $endText = '...') {
    $len = (mb_strlen($string) > $maxlen)
        ? mb_strripos(mb_substr($string, 0, $maxlen), ' ')
        : $maxlen;
    $cutStr = mb_substr($string, 0, $len);
    return (mb_strlen($string) > $maxlen)
        ? $cutStr . $endText
        : $cutStr;
  }


}