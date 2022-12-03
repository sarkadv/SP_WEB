<?php

require_once CONTROLLERS_PATH."IController.interface.php";

/**
 * Trida predstavuje kontroler pro stranku Administrativa.
 * Kontroler ma za ukol zpracovat vstup od uzivatele / z databaze, dat data do sablony a jeji vystup vratit.
 */
class AdministrationController implements IController
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

    if(isset($_POST["user"])) {
      if($_POST["user"] == "delete") {
        if(isset($_POST["user-number"])) {
          $result = $this->dbconnection->deleteUser($_POST["user-number"]);

          if(!$result) {
            echo '<script>alert("Uživatele se nepodařilo odstranit.")</script>';
          }
        }
      }

      else if($_POST["user"] == "promote") {
        if(isset($_POST["user-number"])) {
          $result = $this->dbconnection->promoteUser($_POST["user-number"]);

          if(!$result) {
            echo '<script>alert("Uživatele se nepodařilo povýšit.")</script>';
          }
        }
      }

      else if($_POST["user"] == "demote") {
        if(isset($_POST["user-number"])) {
          $result = $this->dbconnection->demoteUser($_POST["user-number"]);

          if(!$result) {
            echo '<script>alert("Uživatele se nepodařilo zbavit funkce.")</script>';
          }
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

