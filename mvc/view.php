<?php
abstract class BAView {
  protected $model;
  public function bindModel(BAModel $model)
  {
    $this->model = $model;
  }
  
  abstract function display(BALayout $layout);
  
  public function getTemplate()
  {
  
  }

}