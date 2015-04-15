<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

class BitrixAdapterBlock
{

  /**
   * Loads configuration file and initialization.
   *
   */
  private $config;
  public function __construct()
  {
    $this->config = json_decode(file_get_contents(BFactory::getDir().'/config/block.json'));
    $this->setLanguage($this->config->languages->default);
    CModule::IncludeModule("iblock");
    CModule::AddAutoloadClasses('', array(
      'BitrixAdapterBlockPrototype' => '/bitrixadapter/core/prototype.php',
      ));
  }
  
  /**
   * Sets current language.
   *
   */
  private $language;
  public function setLanguage($language) 
  {
    $this->language = $language;
  }
  
  /**
   * Gets current language.
   *
   */
  
  public function getLanguage() 
  {
    return $this->language;
  }
  
  private $references = array();
  public function getById($blockId) {
    $alias = isset($this->references[$blockId]) ? $this->references[$blockId] : $this->getAliasById($blockId);
    return $alias;
  }
  
  private function getAliasById($blockId)
  {
    $arFilter = array("ID" => $blockId);
    $itemsList = CIBlock::GetList(array(), $arFilter, false);
    if ($itemsList->SelectedRowsCount() > 0) {
      $item = $itemsList->Fetch();
      try {
        $this->__call($item['CODE']);
        return $item['CODE'];
      } catch (Exception $e) {
        return null;
      }
    }
  }
  
  /**
   * Gets block object.
   *
   */
  
  private $data = array();
  public function __call($blockAlias, $arguments = null)
  {
    $language = isset($arguments[0]) ? $arguments[0] : $this->language;
    if(isset($this->data[$language][$blockAlias])) return $this->data[$language][$blockAlias];
    $blockObj = $this->getConstantBlockId($blockAlias, $language);
    if (!isset($blockObj)) $blockObj = $this->getVariableBlockId($blockAlias, $language);
    $this->data[$language][$blockAlias] = $blockObj;
    $this->references[$blockObj->getId()] = $blockAlias;
    return $blockObj;
  }
  
  /**
   * Gets block object via Bitrix API by its alias.
   *
   */
  
  private function getVariableBlockId($_blockAlias, $language)
  {
    $blockAlias = str_replace('{ALIAS}', $_blockAlias, $this->config->variables->{$language}->aliasPattern);
    $blocksList = CIBlock::GetList(array(), array("CODE" => $blockAlias), false);
    if ($blocksList->SelectedRowsCount() != 0)
    {
      $block = $blocksList->Fetch();
      $blockId = $block['ID'];
    } else {
      throw new Exception("Access denied for '$blockAlias' block!");
      $blockId = null;
    }
    return new BitrixAdapterBlockPrototype($blockId);
  }
  
  public function getDummyBlock()
  {
    return new BitrixAdapterBlockPrototype(null);
  }
  
  /**
   * Gets block object from config file.
   *
   */
  
  private function getConstantBlockId($blockAlias, $language) 
  {
    if (!isset($this->config->constants->$blockAlias))
    {
      return null;
    }
    else
    {
      $blockId = isset($this->config->constants->$blockAlias->{$language}) ?
        $this->config->constants->$blockAlias->{$language} : 
        $this->config->constants->$blockAlias->{$this->config->languages->default};
    }
    return new BitrixAdapterBlockPrototype($blockId);
  }
  
  /**
   * Gets iBlock ID from its alias
   *
   */
  
  public function getId($alias) 
  {
    return $this->__call($alias, null)->getId();
  }
  
  /**
   * Works as property, gets iBlock ID from its alias. Alias to self::getId().
   *
   */
  
  public function __get($alias)
  {
    return self::getId($alias);
  }
  
  
}