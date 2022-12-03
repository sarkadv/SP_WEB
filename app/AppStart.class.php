<?php

/**
 * Trida slouzi k zavolani spravneho kontroleru vybrane webove stranky.
 */
class AppStart
{
  /**
   * Konstruktor pro vytvoreni instance tridy AppStart.
   */
  public function __construct() {
    require_once CONTROLLERS_PATH."IController.interface.php";
  }

  /**
   * Metoda zjisti, zda existuje zvolena stranka. Pokud ne, je vybrana defaultni uvodni stranka.
   * Pote je vytvorena spravna instance kontroleru a zavolana jeho metoda show() pro vypis obsahu stranky.
   * Ziskany HTML kod je ihned vypsan.
   * @return void
   */
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
