<?php

require_once CONTROLLERS_PATH."IController.interface.php";

/**
 * Trida predstavuje kontroler pro stranku Sprava.
 * Kontroler ma za ukol zpracovat vstup od uzivatele / z databaze, dat data do sablony a jeji vystup vratit.
 */
class ManagementController implements IController
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

    if(isset($_POST["review"])) {
      if($_POST["review"] == "delete") {
        if(isset($_POST["review-number"])) {
          $result = $this->dbconnection->deleteReview($_POST["review-number"]);

          if(!$result) {
            echo '<script>alert("Recenzi se nepodařilo odstranit.")</script>';
          }
        }
      }
    }

    // upload noveho modelu
    if(isset($_POST["model"])) {
      if($_POST["model"] == "new") {
        if(isset($_POST["model-name"]) && isset($_POST["model-price"]) && isset($_POST["model-desc-short"]) && isset($_POST["model-desc-long"])
          && isset($_POST["model-people"]) && isset($_POST["model-battery"]) && isset($_POST["model-speed"]) && isset($_POST["model-units"])) {

          // upload obrazku
          $directory = "img/";
          $file = $directory.basename($_FILES["model-img"]["name"]);
          $success = 1;
          $fileType = strtolower(pathinfo($file,PATHINFO_EXTENSION));

          if(!file_exists($file)) {
            // omezeni formatu souboru
            if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif") {
              $success = 0;
            }

            if ($success == 0) {
              echo '<script>alert("Obrázek se nepodařilo nahrát.")</script>';
            }
            else {
              if (!move_uploaded_file($_FILES["model-img"]["tmp_name"], $file)) {
                echo '<script>alert("Obrázek se nepodařilo nahrát.")</script>';
              }
            }
          }

          $result = $this->dbconnection->createNewModel($_POST["model-name"],$_POST["model-price"], $_POST["model-desc-short"], $_POST["model-desc-long"],
            $_POST["model-people"], $_POST["model-battery"], $_POST["model-speed"], $file, $_POST["model-units"]);

          if($result) {
            echo '<script>alert("Nový model byl vytvořen.")</script>';
          }
          else {
            echo '<script>alert("Nový model se nepodařilo vytvořit.")</script>';
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

    // vsechny recenze
    $reviews = $this->dbconnection->getAllReviews();
    $reviewsInfo = array();
    $i = 0;
    foreach($reviews as $review) {
      $user = $this->dbconnection->getUserByNumber($review["c_uzivatele_fk"]);
      $model = $this->dbconnection->getUFOModelByNumber($review["c_modelu_fk"]);
      $reviewsInfo[$i] = array(
        "email" => $user["email"],
        "datetime" => $review["datum_cas"],
        "rating" => $review["hodnoceni"],
        "text" => $review["text"],
        "model" => $model["nazev"],
        "review_number" => $review["c_recenze_pk"],
      );

      $i++;
    }
    $templateData["reviews"] = $reviewsInfo;

    // odchycovani vystupu (html kodu) do bufferu
    ob_start();

    // ziskani vystupu ze sablony
    require_once VIEWS_PATH."ManagementTemplate.tpl.php";

    // vratime obsah bufferu
    return ob_get_clean();
  }
}
