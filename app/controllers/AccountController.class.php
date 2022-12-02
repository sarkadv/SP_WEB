<?php

require_once CONTROLLERS_PATH."IController.interface.php";

class AccountController implements IController
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

    if(isset($_POST["review"])) {
      if($_POST["review"] == "modify") {
        if(isset($_POST["rating"]) && isset($_POST["text"]) && isset($_POST["model"])) {
          $result = $this->dbconnection->createNewReview($_POST["rating"], $_POST["text"], $_POST["model"]);

          if(!$result) {
            echo '<script>alert("Recenzi se nepodařilo upravit.")</script>';
          }
        }
      }
    }

    global $templateData;   // globalni promenna s daty pro sablonu

    $templateData["title"] = $title;
    $templateData["user_logged"] = $this->dbconnection->isUserLoggedIn();

    // data o uzivateli
    if($templateData["user_logged"]) {
      $user = $this->dbconnection->getLoggedUser();
      $adress = $this->dbconnection->getAdressByNumber($user["c_adresy_fk"]);
      $city = $this->dbconnection->getCityByNumber($adress["c_mesta_fk"]);

      $templateData["email"] = $user["email"];
      $templateData["name"] = $user["jmeno"];
      $templateData["surname"] = $user["prijmeni"];
      $templateData["birthday"] = $user["d_narozeni"];
      $templateData["phone"] = $user["tel_cislo"];
      $templateData["street"] = $adress["ulice"];
      $templateData["city"] = $city["nazev"];
      $templateData["zip"] = $city["psc"];
      $templateData["planet"] = $adress["planeta"];

      // vypujcky uzivatele
      $hires = $this->dbconnection->getHiresByUser($user["c_uzivatele_pk"]);
      $hiresInfo = array();
      $i = 0;
      $totalPrice = 0;
      foreach($hires as $hire) {
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

      // recenze uzivatele
      $recentReviews = $this->dbconnection->getReviewsByUser($user["c_uzivatele_pk"]);
      $reviewsInfo = array();
      $i = 0;
      foreach($recentReviews as $review) {
        $user = $this->dbconnection->getUserByNumber($review["c_uzivatele_fk"]);
        $model = $this->dbconnection->getUFOModelByNumber($review["c_modelu_fk"]);
        $reviewsInfo[$i] = array(
          "text" => $review["text"],
          "datetime" => $review["datum_cas"],
          "rating" => $review["hodnoceni"],
          "model_name" => $model["nazev"],
          "model_number" => $model["c_modelu_pk"],
        );

        $i++;
      }
      $templateData["reviews"] = $reviewsInfo;
    }

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."AccountTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
