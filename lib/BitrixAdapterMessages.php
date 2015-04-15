<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

class BitrixAdapterMessages {
  
  private $data = array('success' => true);
  
  public function __construct() {}
  
  
  public function addError($text, $errNo = 0) {
    $this->data['success'] = false;
    $this->data['messages'][] = $text;
    $this->data['codes'][] = $errNo;
    return $this;
  }  
  
  public function addInfo($text) {
    $this->data['messages'][] = $text;
    return $this;
  }
  
  public function purgeMessages() {
    $this->data['messages'][] = array();
    return $this;
  }  
  
  public function reset() {
    $this->purgeMessages();
    $this->data['success'] = true;
    return $this;
  }
  
  public function isSuccess() {
    return $this->data['success'];
  }  
  
  public function isError() {
    return !$this->isSuccess();
  }
  
  public function finish() {
    if(isset($this->data['codes'])) $this->data['codes'] = array_unique($this->data['codes']);
    $this->data['message'] = implode(', ', $this->data['messages']);
    die(json_encode($this->data));
  }
  
   
  
}