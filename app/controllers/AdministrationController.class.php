<?php

require_once CONTROLLERS_PATH."IController.interface.php";

class AdministrationController implements IController
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
    }

    if(isset($_POST["user"])) {
      if($_POST["user"] == "delete") {
        if(isset($_POST["user-number"])) {
          $result = $this->dbconnection->deleteUser($_POST["user-number"]);

          if(!$result) {
            echo '<script>alert("Uživatele se nepodařilo odstranit.")</script>';
          }
        }
      }
      else if($_POST["user"] == "new") {
        if(isset($_POST["email"]) && isset($_POST["pswd1"]) && isset($_POST["pswd2"]) && isset($_POST["first-name"]) && isset($_POST["last-name"]) && isset($_POST["birth-date"]) && isset($_POST["phone"]) && isset($_POST["city"]) && isset($_POST["street"]) && isset($_POST["zip-code"]) && isset($_POST["planet"])) {
          if($this->dbconnection->doesUserExist($_POST["email"])) {
            echo '<script>alert("Uživatel s touto e-mailovou adresou již existuje.")</script>';
          }
          else {
            $result = $this->dbconnection->addUser($_POST["email"], $_POST["pswd1"], $_POST["pswd2"], $_POST["first-name"], $_POST["last-name"], $_POST["birth-date"], $_POST["phone"], $_POST["city"], $_POST["street"], $_POST["zip-code"], $_POST["planet"], 2);

            if(!$result) {
              echo '<script>alert("Správce nebyl přidán - chybně vyplněný registrační formulář.")</script>';
            }
          }
        }
        else {
          echo '<script>alert("Správce nebyl přidán - chybně vyplněný registrační formulář.")</script>';
        }
      }
    }

    global $templateData;   // globalni promenna s daty pro sablonu

    $templateData["title"] = $title;
    $templateData["user_logged"] = $this->dbconnection->isUserLoggedIn();
    $templateData["user_role"] = 4; // neprihlaseny

    // data o uzivateli
    if($templateData["user_logged"]) {
      $user = $this->dbconnection->getLoggedUser();
      $templateData["user_role"] = $user["c_prava_fk"];
    }

    // vsichni uzivatele
    $users = $this->dbconnection->getAllUsers();
    $usersInfo = array();
    $i = 0;
    foreach($users as $user) {
      $role = $this->dbconnection->getRoleByNumber($user["c_prava_fk"]);
      $usersInfo[$i] = array(
        "number" => $user["c_uzivatele_pk"],
        "role_number" => $user["c_prava_fk"],
        "role_name" => $role["nazev"],
        "email" => $user["email"],
        "name" => $user["jmeno"],
        "surname" => $user["prijmeni"],
      );

      $i++;
    }
    $templateData["users"] = $usersInfo;

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."AdministrationTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}

