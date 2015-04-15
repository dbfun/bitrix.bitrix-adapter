<?php
abstract class BAModel {
  protected $arParams;
  public function __construct($arParams)
  {
    $this->arParams = $arParams;
  }
  
}