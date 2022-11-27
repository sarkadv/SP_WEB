<?php

require_once CONTROLLERS_PATH."IController.interface.php";

class ModelController implements IController
{
  private $dbconnection;
  private $hireUFO;

  public function __construct() {
    require_once MODELS_PATH."DatabaseConnection.class.php";
    $this->dbconnection = new DatabaseConnection();

    require_once CONTROLLERS_PATH."HireUFO.class.php";
    $this->hireUFO = new HireUFO();
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

    else if(isset($_POST["hire"])) {
      $model = $this->dbconnection->getUFOModelByNumber($_POST["hire"]);

      if($model != null) {
        $this->hireUFO->saveUFOData($_POST["hire"], $_POST["days"], $_POST["days"] * $model["cena_den"]);
      }

      header("Refresh:0");
    }

    $UFOModel = null;

    if(isset($_GET["page"])) {
      $modelNumber = (int) filter_var($_GET["page"], FILTER_SANITIZE_NUMBER_INT);
      if(!empty($modelNumber) && (ctype_digit($modelNumber) || is_int($modelNumber))) {
        $UFOModel = $this->dbconnection->getUFOModelByNumber($modelNumber);
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

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."ModelTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
