<?php

class AppStart
{
  public function __construct() {
    require_once CONTROLLERS_PATH."IController.interface.php";
  }

  public function start() {
    if(isset($_GET["page"])) {
      if(array_key_exists($_GET["page"], PAGES)) {
        $pageName = $_GET["page"];
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
