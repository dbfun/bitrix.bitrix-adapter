<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

class BitrixAdapterMenu {
  
  private $menu = array();
  private function __construct() {}
  
  /**
   * Convert stupid plain menu to array
   * TODO: more than double nesting
   */
  
  static function plainToArray($_arResult) 
  {
    $arResult = (array)$_arResult;
    $menu = new self();
    
    if(count($arResult) == 0) return $menu->menu;
    
    foreach($arResult as $item) 
    {
      $menu->add($item);
    }
    
    return $menu->menu;
  }
  
  private $i = 0;
  private function add($item) 
  {
    if ($item["DEPTH_LEVEL"] == 1) 
    {
      $this->i++;
      $this->menu[$this->i] = $item;
    }
    elseif ($item["DEPTH_LEVEL"] == 2)
    {
      $this->menu[$this->i]['CHILDS'][] = $item;
    }
  }

}