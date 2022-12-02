<?php

require_once CONTROLLERS_PATH."IController.interface.php";

class ChangeInfoController implements IController
{
  private DatabaseConnection $dbconnection;

  public function __construct() {
    require_once MODELS_PATH."DatabaseConnection.class.php";
    $this->dbconnection = new DatabaseConnection();
  }

  /**
   * Metoda vrati HTML kod uvodni stranky.
   * @param string $title      nazev stranky
   * @return string     HTML kod uvodni stranky.
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
      else if($_POST["action"] == "modify") {
          $result = $this->dbconnection->modifyUser($_POST["pswd1"], $_POST["pswd2"], $_POST["first-name"], $_POST["last-name"], $_POST["birth-date"], $_POST["phone"], $_POST["city"], $_POST["street"], $_POST["zip-code"], $_POST["planet"]);

          if(!$result) {
            echo '<script>alert("Údaje nebyly upraveny - chybně vyplněný formulář.")</script>';
          }
          else {
            header("LOCATION: index.php?page=account");
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
      $adress = $this->dbconnection->getAdressByNumber($user["c_adresy_fk"]);
      $city = $this->dbconnection->getCityByNumber($adress["c_mesta_fk"]);

      $templateData["name"] = $user["jmeno"];
      $templateData["surname"] = $user["prijmeni"];
      $templateData["birthday"] = $user["d_narozeni"];
      $templateData["phone"] = $user["tel_cislo"];
      $templateData["street"] = $adress["ulice"];
      $templateData["city"] = $city["nazev"];
      $templateData["zip"] = $city["psc"];
      $templateData["planet"] = $adress["planeta"];
    }

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."ChangeInfoTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
