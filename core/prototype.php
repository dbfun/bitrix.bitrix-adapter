<?php
/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

class BitrixAdapterBlockPrototype
{
  private $id;
  public function __construct($id)
  {
    $this->id = $id;
  }
  
  public function getId()
  {
    return $this->id;
  }
  
  private $data;
  public function __get($name)
  {
    if (!isset($this->id)) return null; // For not existing iBlock
    if (isset($this->data)) return $this->data->{$name};
    $this->loadData();
    return $this->data->{$name};
  }
  
  private function loadData()
  {
    $elementsList = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $this->id), false, false, array('ID', 'CODE'));
    if ($elementsList->SelectedRowsCount() > 0)
    {
      while ($element = $elementsList->Fetch())
      {
        if (!empty($element['CODE'])) {
          $this->data->{$element['CODE']} = $element['ID'];
          $this->references[$element['ID']] = $element['CODE'];
        }
      }
    }
  }
  
  private $references;
  public function getSectionById($sectionId)
  {
    if (isset($this->references)) return $this->references[$sectionId];
    $this->loadData();
    return $this->references[$sectionId];
  }


}