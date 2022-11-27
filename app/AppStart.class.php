<?php

class AppStart
{
  public function __construct() {
    require_once CONTROLLERS_PATH."IController.interface.php";
  }

  public function start() {
    if(isset($_GET["page"])) {
      $value = $_GET["page"];
      $value = preg_replace('/[0-9]+/', '', $value);

      if(array_key_exists($value, PAGES)) {
        $pageName = $value;
      }
      else {
        $pageName = DEFAULT_PAGE_KEY;
      }
    }
    else {
      $pageName = DEFAULT_PAGE_KEY;
    }

    $pageControllerInfo = PAGES[$pageName];

    require_once CONTROLLERS_PATH.$pageControllerInfo["controller_file_name"];
    $controller = new $pageControllerInfo["controller_class_name"];

    echo $controller->show($pageControllerInfo["title"]);

  }
}
