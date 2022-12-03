<?php
require_once CONTROLLERS_PATH."IController.interface.php";

/**
 * Trida predstavuje kontroler pro stranku Nabidka.
 * Kontroler ma za ukol zpracovat vstup od uzivatele / z databaze, dat data do sablony a jeji vystup vratit.
 */
class ProductsController implements IController
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

    else if(isset($_POST["hire"])) {
      $model = $this->dbconnection->getUFOModelByNumber($_POST["hire"]);

      if($model != null) {
        $this->hireUFO->saveUFOData($_POST["hire"], $_POST["days"], $_POST["days"] * $model["cena_den"]);
      }

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

    $modelsInfo = array();
    $allModels = $this->dbconnection->getAllUFOModels();
    $i = 0;
    foreach($allModels as $model) {
      $modelsInfo[$i] = array(
        "img" => $model["obrazek_url"],
        "name" => $model["nazev"],
        "description" => $model["popis_kratky"],
        "model_number" => $model["c_modelu_pk"],
        "available" => $this->dbconnection->getNumberOfUFOsAvailableByModelNumber($model["c_modelu_pk"]),
      );

      $i++;
    }

    $templateData["models"] = $modelsInfo;

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."ProductsTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
