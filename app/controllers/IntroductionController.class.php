<?php

require_once CONTROLLERS_PATH."IController.interface.php";

/**
 * Trida predstavuje kontroler pro stranku Uvod.
 * Kontroler ma za ukol zpracovat vstup od uzivatele / z databaze, dat data do sablony a jeji vystup vratit.
 */
class IntroductionController implements IController
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

    if (isset($_POST["action"])) {
      if ($_POST["action"] == "login") {
        if (isset($_POST["email"]) && isset($_POST["pswd"])) {
          if ($_POST["email"] != "" && $_POST["pswd"] != "") {
            $result = $this->dbconnection->loginUser($_POST["email"], $_POST["pswd"]);

            if (!$result) {
              echo '<script>alert("Nesprávný e-mail nebo heslo.")</script>';
            }
          }
        }
      } else if ($_POST["action"] == "logout") {
        $this->dbconnection->logoutUser();
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

    // modely pro zobrazeni v carouselu
    $templateData["ufo_1"] = $this->dbconnection->getUFOModelByNumber(1);
    $templateData["ufo_2"] = $this->dbconnection->getUFOModelByNumber(2);
    $templateData["ufo_3"] = $this->dbconnection->getUFOModelByNumber(3);

    $recentReviews = $this->dbconnection->getNewestReviews(3);
    $reviewsInfo = array();
    $i = 0;
    foreach($recentReviews as $review) {
      $user = $this->dbconnection->getUserByNumber($review["c_uzivatele_fk"]);
      $model = $this->dbconnection->getUFOModelByNumber($review["c_modelu_fk"]);
      $reviewsInfo[$i] = array(
        "username" => $user["jmeno"],
        "datetime" => $review["datum_cas"],
        "rating" => $review["hodnoceni"],
        "model" => $model["nazev"],
      );

      $i++;
    }
    $templateData["reviews"] = $reviewsInfo;

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."IntroductionTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
