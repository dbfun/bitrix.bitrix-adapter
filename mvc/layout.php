<?php
class BALayout {
  public function __construct()
  {
  
  }
  
  public function __set($name, $value)
  {
    $this->$name = $value;
  }
  
  private $_layout = 'template';
  public function getLayout()
  {
    return $this->_layout;
  }
  
  public function setLayout($layout)
  {
    return $this->_layout = $layout;
  }

}