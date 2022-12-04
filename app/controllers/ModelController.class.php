<?php

require_once CONTROLLERS_PATH."IController.interface.php";

/**
 * Trida predstavuje kontroler pro stranku Model.
 * Kontroler ma za ukol zpracovat vstup od uzivatele / z databaze, dat data do sablony a jeji vystup vratit.
 */
class ModelController implements IController
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

    $UFOModel = null;

    if(isset($_GET["examine"])) {
      if((ctype_digit($_GET["examine"]) || is_int($_GET["examine"]))) {
        $UFOModel = $this->dbconnection->getUFOModelByNumber($_GET["examine"]);
      }
    }

    if(isset($_POST["review"])) {
      if($_POST["review"] == "create") {
        if(isset($_POST["rating"]) && isset($_POST["text"])) {
          if($UFOModel != null) {
            $result = $this->dbconnection->createNewReview($_POST["rating"], $_POST["text"], $UFOModel["c_modelu_pk"]);

            if(!$result) {
              echo '<script>alert("Recenzi se nepodařilo vytvořit.")</script>';
            }
          }
        }
      }
    }

    global $templateData;   // globalni promenna s daty pro sablonu

    $templateData["title"] = $title;
    $templateData["user_logged"] = $this->dbconnection->isUserLoggedIn();
    $templateData["user_role"] = 4;
    $templateData["ufo"] = $UFOModel;

    if($UFOModel != null) {
      $modelNumber = $UFOModel["c_modelu_pk"];
      $templateData["available"] = $this->dbconnection->getNumberOfUFOsAvailableByModelNumber($modelNumber);

      $reviewsInfo = array();
      $allReviews = $this->dbconnection->getReviewsByModelNumber($modelNumber);
      $i = 0;
      foreach ($allReviews as $review) {
        $user = $this->dbconnection->getUserByNumber($review["c_uzivatele_fk"]);
        $reviewsInfo[$i] = array(
          "username" => $user["jmeno"],
          "datetime" => $review["datum_cas"],
          "rating" => $review["hodnoceni"],
          "text" => $review["text"]
        );

        $i++;
      }

      $templateData["reviews"] = $reviewsInfo;
    }

    if($templateData["user_logged"] && $UFOModel != null) {
      $user = $this->dbconnection->getLoggedUser();
      $templateData["user_role"] = $user["c_prava_fk"];
      $userNumber = $user["c_uzivatele_pk"];

      $templateData["rewrite"] = $this->dbconnection->doesReviewByThisUserExist($userNumber, $modelNumber);
      $templateData["can_review"] = $this->dbconnection->hasUserEverHiredThisModel($userNumber, $modelNumber);
    }

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."ModelTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
