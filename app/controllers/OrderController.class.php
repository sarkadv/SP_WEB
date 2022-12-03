<?php

require_once CONTROLLERS_PATH."IController.interface.php";

/**
 * Trida predstavuje kontroler pro stranku Shrnuti objednavky.
 * Kontroler ma za ukol zpracovat vstup od uzivatele / z databaze, dat data do sablony a jeji vystup vratit.
 */
class OrderController implements IController
{
  /**
   * @var DatabaseConnection promenna pro praci s databazi
   */
  private DatabaseConnection $dbconnection;

  /**
   * Konstruktor pro vytvoreni instance tridy kontroleru.
   */
  public function __construct() {
    require_once MODELS_PATH."DatabaseConnection.class.php";
    $this->dbconnection = new DatabaseConnection();
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

    $hireResult = [];

    if(isset($_POST["hire"])) {
      if($_POST["hire"] == "payment") {
        if($_POST["account-number"] != "") {
          $hireResult = $this->dbconnection->createNewHire($_POST["account-number"]);
        }
      }
    }

    global $templateData;   // globalni promenna s daty pro sablonu

    $templateData["title"] = $title;
    $templateData["user_logged"] = $this->dbconnection->isUserLoggedIn();
    $templateData["user_role"] = 4;

    if($templateData["user_logged"]) {
      $user = $this->dbconnection->getLoggedUser();
      $templateData["user_role"] = $user["c_prava_fk"];
    }

    if(count($hireResult) > 0) {
      $hire = $this->dbconnection->getHireByNumber($hireResult[0]);
      $adress = $this->dbconnection->getAdressByNumber($hire["c_adresy_fk"]);
      $templateData["street"] = $adress["ulice"];
      $templateData["planet"] = $adress["planeta"];

      $city = $this->dbconnection->getCityByNumber($adress["c_mesta_fk"]);
      $templateData["city"] = $city["nazev"];
      $templateData["zip"] = $city["psc"];

      $templateData["account"] = $hire["c_platebniho_uctu"];

      $hiresInfo = array();
      $i = 0;
      $totalPrice = 0;
      foreach($hireResult as $hireNumber) {
        $hire = $this->dbconnection->getHireByNumber($hireNumber);
        $UFO = $this->dbconnection->getUFOByNumber($hire["c_ufo_fk"]);
        $model = $this->dbconnection->getUFOModelByNumber($UFO["c_modelu_fk"]);
        $days = ceil((strtotime($hire["d_vraceni"]) - strtotime($hire["d_vypujceni"])) / (60 * 60 * 24));

        $hiresInfo[$i] = array(
          "model_name" => $model["nazev"],
          "dates" => $hire["d_vypujceni"]." až ".$hire["d_vraceni"],
          "days" => $days,
          "price" => $days * $model["cena_den"]
        );

        $totalPrice += $days * $model["cena_den"];
        $i++;
      }

      $templateData["hires"] = $hiresInfo;
      $templateData["total_price"] = $totalPrice;
    }

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."OrderTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
