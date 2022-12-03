<?php

require_once CONTROLLERS_PATH."IController.interface.php";

/**
 * Trida predstavuje kontroler pro stranku Kosik.
 * Kontroler ma za ukol zpracovat vstup od uzivatele / z databaze, dat data do sablony a jeji vystup vratit.
 */
class CartController implements IController
{
  /**
   * @var DatabaseConnection promenna pro praci s databazi
   */
  private DatabaseConnection $dbconnection;

  /**
   * @var HireUFO promenna pro praci s cookies k ukladani prubehu nakupu
   */
  private HireUFO $hireUFO;

  /**
   * Konstruktor pro vytvoreni instance tridy kontroleru.
   */
  public function __construct() {
    require_once MODELS_PATH."DatabaseConnection.class.php";
    $this->dbconnection = new DatabaseConnection();

    require_once CONTROLLERS_PATH."HireUFO.class.php";
    $this->hireUFO = new HireUFO();
  }

  /**
   * Metoda vrati HTML kod stranky odpovidajici kontroleru.
   * @param string $title       nazev stranky
   * @return string             HTML kod stranky
   */
  public function show(string $title): string
  {

    if(isset($_POST["action"])) {
      if($_POST["action"] == "login") {
        if(isset($_POST["email"]) && isset($_POST["pswd"])) {
          if($_POST["email"] != "" && $_POST["pswd"] != "") {
            $result = $this->dbconnection->loginUser($_POST["email"], $_POST["pswd"]);

            if(!$result) {
              echo '<script>alert("Nesprávný e-mail nebo heslo.")</script>';
            }
          }
        }
      }
      else if($_POST["action"] == "logout") {
        $this->dbconnection->logoutUser();
      }
    }

    else if(isset($_POST["remove"])) {
      $this->hireUFO->deleteUFOData($_POST["remove"]);

      header("Refresh:0");
    }

    global $templateData;   // globalni promenna s daty pro sablonu

    $templateData["title"] = $title;
    $templateData["user_logged"] = $this->dbconnection->isUserLoggedIn();
    $templateData["user_role"] = 4;

    if($templateData["user_logged"]) {
      $user = $this->dbconnection->getLoggedUser();
      $templateData["user_role"] = $user["c_prava_fk"];
    }

    // pole vsech ulozenych UFO v kosiku, jejich nazev, pocet dnu vypujcky, cena
    $allUFOsInfo = array();
    $allUFOs = $this->hireUFO->getAllSavedUFOs();
    $i = 0;
    foreach($allUFOs as $UFO) {
      $model = $this->hireUFO->getModel($i);
      $modelName = $this->dbconnection->getUFOModelByNumber($model)["nazev"];
      $days = $this->hireUFO->getDays($i);
      $price = $this->hireUFO->getPrice($i);

      $allUFOsInfo[$i] = array(
        "name" => $modelName,
        "days" => $days,
        "price" => $price
      );

      $i++;
    }

    $templateData["ufos_info"] = $allUFOsInfo;
    $templateData["total_price"] = $this->hireUFO->getTotalPrice();

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."CartTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
