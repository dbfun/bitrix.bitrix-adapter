<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

 
class BitrixAdapter {

  /**
   * Init common classes
   *
   */

  public function init() 
  {
    CModule::AddAutoloadClasses('', array(
      'BFactory' => '/bitrixadapter/factory.php'
      ));
    BFactory::init();
  }
}