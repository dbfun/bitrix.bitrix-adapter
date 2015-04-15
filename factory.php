<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */


abstract class BFactory {

  /**
   * Load additional classes
   *
   */

  public function load($_baseClassName)
  {
    if(is_array($_baseClassName) && count($_baseClassName) == 0) return false;
    if(empty($_baseClassName)) return false;
    $baseClassNames = (array)$_baseClassName;
    foreach($baseClassNames as $baseClassName)
    {
      $fullClassName = 'BitrixAdapter'.ucfirst($baseClassName);
      if(!class_exists($fullClassName)) CModule::AddAutoloadClasses('', array($fullClassName => "/bitrixadapter/lib/$fullClassName.php"));
    }
    return true;
  }

  /**
   * Init common classes
   *
   */
   
  private static $dir;
  public static function init()
  {
    self::$dir = dirname(__FILE__);
    CModule::AddAutoloadClasses('', array(
      'BitrixAdapterBlock' => '/bitrixadapter/core/block.php',
      'BitrixAdapterMenu' => '/bitrixadapter/core/menu.php',
      'BitrixAdapterDb' => '/bitrixadapter/core/db.php',
      'BitrixAdapterRouter' => '/bitrixadapter/core/router.php',
      
      'BAController' => '/bitrixadapter/mvc/controller.php',
      'BAView' => '/bitrixadapter/mvc/view.php',
      'BAModel' => '/bitrixadapter/mvc/model.php',
      'BALayout' => '/bitrixadapter/mvc/layout.php',
      ));
  }
  
  /**
	 * Get library directory.
	 *
	 */
  
  public static function getDir()
  {
    return self::$dir;
  }

  /**
	 * Get a database object.
	 *
	 */

  public static $database = null;
	public static function getDbo()
	{
		if (!self::$database)
		{
			global $DB;
			self::$database =& $DB;
		}

		return self::$database;
	}
  
  /**
	 * Get a user object.
	 *
	 */
  
  public static $user = null;
	public static function getUser()
	{
		if (!self::$user)
		{
			global $USER;
			self::$user =& $USER;
		}

		return self::$user;
	}  
  
  /**
	 * Get an application object.
	 *
	 */
  
  public static $application = null;
	public static function getApplication()
	{
		if (!self::$application)
		{
			global $APPLICATION;
			self::$application =& $APPLICATION;
		}

		return self::$application;
	}
  
  /**
	 * Get a Block object.
	 *
	 */
  
  public static $block = null;
	public static function getBlock()
	{
		if (!self::$block)
		{
			self::$block = new BitrixAdapterBlock();
		}
		return self::$block;
	}
  
  /**
   * Set current language.
   *
   */
  public static function setLanguage($language)
  {
    $block = self::getBlock();
    $block->setLanguage($language);
  }  
  
  /**
   * Get current language.
   *
   */
  public static function getLanguage()
  {
    $block = self::getBlock();
    return $block->getLanguage();
  }
  
  /**
   * Alias to self::getBlock().
   *
   */
  public static function _() 
  {
    return self::getBlock();
  }
  
  /**
   * Include template file.
   *
   */
  
  public static function module($fileName)
  {
  $app = self::getApplication();
  $app->IncludeComponent
    (
    "bitrix:main.include", "", Array("AREA_FILE_SHOW" => "file", 
    "PATH" => "/bitrix_app/templates/".$fileName,"EDIT_TEMPLATE" => "")
    );
  }
  
  /**
	 * Get a BitrixAdapterJS object.
	 * Usage: BitrixAdapterJS::load('site'), BitrixAdapterJS::enable('site/common'), BitrixAdapterJS::dump();
   *
	 */

  public static $jso = null;
	public static function getJso()
	{
		if (!self::$jso)
		{
      self::load('JS');
      self::$jso = new BitrixAdapterJS();
		}
		return self::$jso;
	}
  
  
  /**
	 * Get BitrixAdapterRouter object
   *
	 */
  
  public static function route($name)
  {
    return BitrixAdapterRouter::_($name);
  }
  
  /**
   * Set global option
   *
   */  
  
  private static $options = array();
  public static function setOpt($name, $value)
  {
    self::$options[$name] = $value;
  }
  
  /**
   * Get global option
   *
   */
  
  public static function getOpt($name)
  {
    return isset(self::$options[$name]) ? self::$options[$name] : null;
  } 
  
}

