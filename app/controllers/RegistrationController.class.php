<?php

require_once CONTROLLERS_PATH."IController.interface.php";

class RegistrationController implements IController
{
  private $dbconnection;

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
      else if($_POST["action"] == "register") {

        if($this->dbconnection->doesUserExist($_POST["email"])) {
          echo '<script>alert("Uživatel s touto e-mailovou adresou již existuje.")</script>';
        }
        else {
          $result = $this->dbconnection->addUser($_POST["email"], $_POST["pswd1"], $_POST["pswd2"], $_POST["first-name"], $_POST["last-name"], $_POST["birth-date"], $_POST["phone"], $_POST["city"], $_POST["street"], $_POST["zip-code"], $_POST["planet"]);

          if(!$result) {
            echo '<script>alert("Nebyli jste zaregistrováni - chybně vyplněný registrační formulář.")</script>';
          }
        }

      }
    }

    global $templateData;   // globalni promenna s daty pro sablonu

    $templateData["title"] = $title;
    $templateData["user_logged"] = $this->dbconnection->isUserLoggedIn();

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."RegistrationTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
