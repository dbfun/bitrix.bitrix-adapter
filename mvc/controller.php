<?php
abstract class BAController
  {
  protected $arParams, $name, $data, $componentDir;
  public function __construct($arParams)
  {
    $this->name = static::NAME;
    $this->arParams = $arParams;
    $this->data = new stdClass();
    CModule::IncludeModule("iblock");
  }
  
  public function getName() { return $this->name; }
  
  protected $modelName;
  protected function getModel()
  {
    $modelClassName = $this->getModelClassName($this->modelName);
    require("{$this->componentDir}/models/$modelClassName.php");
    return new $modelClassName($this->arParams);
  }  
  
  protected $viewName;
  protected function getView()
  {
    $viewClassName = $this->getViewClassName($this->viewName);
    require("{$this->componentDir}/views/$viewClassName.php");
    return new $viewClassName();
  }
  
  protected function getLayout()
  {
    return new BALayout();
  }

  protected $layout;
  public function execute() {
    $model = $this->getModel();
    $view = $this->getView();
    $this->layout = $this->getLayout();
    $view->bindModel($model);
    $view->display($this->layout);
  }
  
  protected $redirectLink;
  public function redirect() {
    if (isset($this->redirectLink)) {
      LocalRedirect($this->redirectLink);
    }
  }
  
  protected function getModelClassName($modelName)
  {
    return $this->name . ucfirst($modelName) . 'Model';
  }  
  
  protected function getViewClassName($modelName)
  {
    return $this->name . ucfirst($modelName) . 'View';
  }

}