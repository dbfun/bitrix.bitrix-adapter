<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

class BitrixAdapterRouter
{
  private static $instances = array();
  public static function _($type)
  {
    if (in_array($type, self::$instances)) return self::$instances[$type];
    $className = ucfirst($type).'LinkRouter';
    if (!class_exists($className)) return new CommonLinkRouter($type);
    self::$instances[$type] = new $className($type);
    return self::$instances[$type];
  }
}
  
class CommonLinkRouter 
{
  protected $type;
  public function __construct($type) 
  {
    $this->type = $type;
  }
  
  public function __call($name, $arguments) 
  {
    return $this->trim($this->type.'/#');
  }
  
  protected function trim($uri) 
  {
    return '/'.ltrim(preg_replace('/\/{2,}/', '/', $uri), '/');
  }
  
  protected function getId($item) 
  {
    if (is_array($item)) return $item["ID"];
    return $item;
  }
    
  public function index() 
  {
    return $this->trim($this->type.'/');
  }
  
  public function detail($item) 
  {
    return $this->trim($this->type.'/detail.php?ID='.$this->getId($item));
  }
  
  public function section($item) 
  {
    return $this->trim($this->type.'/list.php?SECTION_ID='.$this->getId($item));
  }
}
  
class NewsLinkRouter extends CommonLinkRouter 
{
  public function detail($item) {
    return $this->trim('/news/detail.php?ID='.$this->getId($item));
  }
  
  public function index() {
    return $this->trim('/news/');
  }
}

class SearchLinkRouter extends CommonLinkRouter 
{
  public function detail($arItem) {
    if ($arItem["MODULE_ID"] != "iblock") return $arItem["URL"];
    $arFilter = array("ID" => $arItem['ITEM_ID']);
    $arSelect = array("ID", "CODE", "IBLOCK_ID", "IBLOCK_SECTION_ID", "PROPERTY_SECTION");
    $itemsList = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    if ($itemsList->SelectedRowsCount() > 0) {
      $item = $itemsList->Fetch();
      try {
        $link = BFactory::route(BFactory::_()->getById($item['IBLOCK_ID']))->detail($item);
        return $link;
      } catch (Exception $e) {
        return $arItem["URL"];
      }
    }
    return '#';
  }
}

class SmiLinkRouter extends CommonLinkRouter 
{
  public function detail($item) {
    return $this->trim('/about/smi/detail.php?ID='.$this->getId($item));
  }
}

class CenyLinkRouter extends CommonLinkRouter 
{
  public function detail($item) {
    $sectionAlias = BFactory::_()->sections()->getSectionById($item['PROPERTY_SECTION_VALUE']);
    return $this->trim("/$sectionAlias/ceny/");
  }
}

class ArticlesLinkRouter extends CommonLinkRouter 
{
  public function detail($item) {
    $sectionAlias = BFactory::_()->sections()->getSectionById($item['PROPERTY_SECTION_VALUE']);
    return $this->trim("/$sectionAlias/stati/detail.php?ID=".$this->getId($item));
  }
}

class AkciiLinkRouter extends CommonLinkRouter 
{
  public function detail($item) {
    $sectionAlias = BFactory::_()->sections()->getSectionById($item['PROPERTY_SECTION_VALUE']);
    return $this->trim("/$sectionAlias/akcii/detail.php?ID=".$this->getId($item));
  }
}

class PhotoLinkRouter extends CommonLinkRouter 
{
  public function detail($item) {
    // $sectionAlias = BFactory::_()->sections()->getSectionById($item['PROPERTY_SECTION_VALUE']);
    // return $this->trim("/$sectionAlias/akcii/detail.php?ID=".$this->getId($item));
    // return $this->trim("/$sectionAlias/fotogalereya/?SECTION_ID=".$item['IBLOCK_SECTION_ID']);
    return $this->trim("/fotogalereya/");
  }
}

class AnnouncesLinkRouter extends CommonLinkRouter 
{
  public function detail($item) {
    return $this->trim("/about/registraciya_zakaz_online/detail.php?ID=".$this->getId($item));
  }
}

class PartneryLinkRouter extends CommonLinkRouter 
{
  public function detail($item) {
    return $this->trim('/about/partnery/');
  }
}