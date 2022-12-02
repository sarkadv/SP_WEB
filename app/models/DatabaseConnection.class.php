<?php

class DatabaseConnection
{
  private PDO $conn;

  /* Pro prihlasovani / odhlasovani uzivatele */
  private Session $session;

  /* Pro vytvareni vypujcek */
  private HireUFO $hireUFO;

  private const KEY_USER = "user";

  public function __construct () {
    require_once "settings.inc.php";
    $this->conn = new PDO("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $this->conn->exec("set names utf8");

    require_once CONTROLLERS_PATH."Session.class.php";
    $this->session = new Session();

    require_once CONTROLLERS_PATH."HireUFO.class.php";
    $this->hireUFO = new HireUFO();
  }

  public function query(string $query):array {
    $result = $this->conn->query($query);

    if($result != null) {
      return $result->fetchAll();
    }
    else {  // dotazem jsme neziskali zadna data
      $error = $this->conn->errorInfo();
      echo $error[2];
      return [];
    }
  }

  public function loginUser(string $email, string $password):bool {
    $query = "SELECT * FROM ".TABLE_USER." WHERE email='$email'";

    $result = $this->query($query);

    if(count($result) == 1) {
      if(password_verify($password, $result[0]["heslo"])) {
        $this->session->setSession(self::KEY_USER, $result[0]["email"]);
        return true;
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }

  public function logoutUser() {
    if(isset($_SESSION[self::KEY_USER])) {
      $this->session->unsetSession(self::KEY_USER);
    }
  }

  public function isUserLoggedIn():bool {
    return $this->session->isSessionSet(self::KEY_USER);
  }

  public function getLoggedUser() {
    if($this->isUserLoggedIn()) {
      $email = $this->session->readSession(self::KEY_USER);

      $query = "SELECT * FROM ".TABLE_USER." WHERE email='$email'";
      $result = $this->query($query);

      if(count($result) == 1) {
        return $result[0];
      }
      else {
        return null;
      }
    }
    else {
      return null;
    }
  }

  public function addUser(string $email, string $password1, string $password2, string $name, string $surname, string $rawDate, string $tel, string $city, string $street, string $zip, string $planet):bool {
    $birthDate = date('Y-m-d', strtotime($rawDate));

    if($password1 != $password2) {
      return false;
    }

    // mesto noveho uzivatele jeste neni v tabulce MESTO
    if(!$this->doesCityExist($city)) {
      $query = "INSERT INTO ".TABLE_CITY." (nazev, psc) "."VALUES ('$city', '$zip')";
      $this->query($query);

      if(!$this->doesCityExist($city)) {  // mesto se nepodarilo vlozit
        return false;
      }

      $cityNumber = $this->getCityNumber($city);

      // pokud mesto neexistovalo, adresa urcite take neexistuje
      $query = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk) "."VALUES ('$street', '$planet', '$cityNumber')";
      $this->query($query);

      if(!$this->doesAdressExist($city, $street, $planet)) {  // adresu se nepodarilo vlozit
        return false;
      }
    }
    else {
      // mesto noveho uzivatele je v tabulace MESTO

      // mesto existuje, ale adresa neexistuje
      if(!$this->doesAdressExist($city, $street, $planet)) {
        $cityNumber = $this->getCityNumber($city);
        $query = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk) "."VALUES ('$street', '$planet', '$cityNumber')";
        $this->query($query);

        if(!$this->doesAdressExist($city, $street, $planet)) {  // adresu se nepodarilo vlozit
          return false;
        }
      }
    }

    $adressNumber = $this->getAdressNumber($city, $street, $planet);

    $encryptedPassword = password_hash($password1, PASSWORD_DEFAULT);

    $query = "INSERT INTO ".TABLE_USER." (email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)"
    ." VALUES ('$email', '$encryptedPassword', '$name', '$surname', '$birthDate', '$tel', '3', '$adressNumber')";

    $this->query($query);

    if(!$this->doesUserExist($email)){  // uzivatel nebyl vlozen
      return false;
    }
    else {
      $this->loginUser($email, $password1);
      return true;
    }
  }

  public function modifyUser(string $password1, string $password2, string $name, string $surname, string $rawDate, string $tel, string $city, string $street, string $zip, string $planet):bool {
    $user = $this->getLoggedUser();
    $birthDate = date('Y-m-d', strtotime($rawDate));

    if($password1 != $password2) {
      return false;
    }

    // mesto uzivatele jeste neni v tabulce MESTO
    if(!$this->doesCityExist($city)) {
      $query = "INSERT INTO ".TABLE_CITY." (nazev, psc) "."VALUES ('$city', '$zip')";
      $this->query($query);

      if(!$this->doesCityExist($city)) {  // mesto se nepodarilo vlozit
        return false;
      }

      $cityNumber = $this->getCityNumber($city);

      // pokud mesto neexistovalo, adresa urcite take neexistuje
      $query = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk) "."VALUES ('$street', '$planet', '$cityNumber')";
      $this->query($query);

      if(!$this->doesAdressExist($city, $street, $planet)) {  // adresu se nepodarilo vlozit
        return false;
      }
    }
    else {
      // mesto uzivatele je v tabulace MESTO

      // mesto existuje, ale adresa neexistuje
      if(!$this->doesAdressExist($city, $street, $planet)) {
        $cityNumber = $this->getCityNumber($city);
        $query = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk) "."VALUES ('$street', '$planet', '$cityNumber')";
        $this->query($query);

        if(!$this->doesAdressExist($city, $street, $planet)) {  // adresu se nepodarilo vlozit
          return false;
        }
      }
    }

    $adressNumber = $this->getAdressNumber($city, $street, $planet);

    $encryptedPassword = password_hash($password1, PASSWORD_DEFAULT);

    $query = "UPDATE ".TABLE_USER." SET heslo='".$encryptedPassword."', jmeno='".$name."', prijmeni='".$surname."', d_narozeni='".$birthDate
      ."', tel_cislo='".$tel."', c_adresy_fk='".$adressNumber."' WHERE c_uzivatele_pk=".$user["c_uzivatele_pk"];

    echo $query;

    $this->query($query);

    return true;
  }

  public function doesUserExist(string $email):bool {
    $query = "SELECT * FROM ".TABLE_USER ." WHERE email='$email'";
    $result = $this->query($query);

    if(count($result) == 0) {
      return false;
    }
    else {
      return true;
    }
  }

  public function doesCityExist(string $city):bool {
    $query = "SELECT * FROM ".TABLE_CITY." WHERE nazev='$city'";
    $result = $this->query($query);

    if(count($result) == 0) {
      return false;
    }
    else {
      return true;
    }
  }

  public function doesAdressExist(string $city, string $street, string $planet):bool {
    $cityNumber = $this->getCityNumber($city);

    if($cityNumber == null) {
      return false;
    }

    $query = "SELECT * FROM ".TABLE_ADRESS." WHERE ulice='$street' AND planeta='$planet' AND c_mesta_fk='$cityNumber'";
    $result = $this->query($query);

    if(count($result) == 0) {
      return false;
    }
    else {
      return true;
    }
  }

  public function doesHireExist(int $hireNumber):bool {
    $query = "SELECT * FROM ".TABLE_HIRE." WHERE c_vypujcky_pk='$hireNumber'";
    $result = $this->query($query);

    if(count($result) == 0) {
      return false;
    }
    else {
      return true;
    }
  }

  public function doesReviewExist(int $reviewNumber):bool {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_recenze_pk='$reviewNumber'";
    $result = $this->query($query);

    if(count($result) == 0) {
      return false;
    }
    else {
      return true;
    }
  }

  public function doesModelExist(int $modelNumber):bool {
    $query = "SELECT * FROM ".TABLE_MODEL." WHERE c_modelu_pk='$modelNumber'";
    $result = $this->query($query);

    if(count($result) == 0) {
      return false;
    }
    else {
      return true;
    }
  }

  public function doesUFOExist(int $UFONumber):bool {
    $query = "SELECT * FROM ".TABLE_UFO." WHERE c_ufo_pk='$UFONumber'";
    $result = $this->query($query);

    if(count($result) == 0) {
      return false;
    }
    else {
      return true;
    }
  }

  public function getUFOModelByNumber ($number) {
    $query = "SELECT * FROM ".TABLE_MODEL." WHERE c_modelu_pk='$number'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0];
    }
    else {
      return null;
    }
  }

  public function getUserByNumber ($number) {
    $query = "SELECT * FROM ".TABLE_USER." WHERE c_uzivatele_pk='$number'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0];
    }
    else {
      return null;
    }
  }

  public function getCityByNumber($number) {
    $query = "SELECT * FROM ".TABLE_CITY." WHERE c_mesta_pk='$number'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0];
    }
    else {
      return null;
    }
  }

  public function getAdressByNumber($number) {
    $query = "SELECT * FROM ".TABLE_ADRESS." WHERE c_adresy_pk='$number'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0];
    }
    else {
      return null;
    }
  }

  public function getUFOByNumber($number) {
    $query = "SELECT * FROM ".TABLE_UFO." WHERE c_ufo_pk='$number'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0];
    }
    else {
      return null;
    }
  }

  public function getHireByNumber($number) {
    $query = "SELECT * FROM ".TABLE_HIRE." WHERE c_vypujcky_pk='$number'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0];
    }
    else {
      return null;
    }
  }

  public function getReviewByNumber($number) {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_recenze_pk='$number'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0];
    }
    else {
      return null;
    }
  }

  public function getCityNumber($city) {
    $query = "SELECT * FROM ".TABLE_CITY." WHERE nazev='$city'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0]["c_mesta_pk"];
    }
    else {
      return null;
    }

  }

  public function getAdressNumber($city, $street, $planet) {
    $cityNumber = $this->getCityNumber($city);

    $query = "SELECT * FROM ".TABLE_ADRESS." WHERE ulice='$street' AND planeta='$planet' AND c_mesta_fk='$cityNumber'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0]["c_adresy_pk"];
    }
    else {
      return null;
    }

  }

  public function getAllUFOModels():array {
    $query = "SELECT * FROM ".TABLE_MODEL;

    return $this->query($query);
  }

  public function getAllReviews():array {
    $query = "SELECT * FROM ".TABLE_REVIEW." ORDER BY c_modelu_fk";

    return $this->query($query);
  }

  public function getNumberOfUFOsAvailableByModelNumber(int $modelNumber):int {
    $query = "SELECT * FROM ".TABLE_UFO." WHERE c_modelu_fk='$modelNumber'";
    $allUFOs = $this->query($query);

    // napocitame pocet vozidel, ktera nejsou v tabulce VYPUJCKA
    $count = 0;
    foreach($allUFOs as $UFO) {
      if($this->isUFOFree($UFO["c_ufo_pk"])) {
        $count++;
      }
    }

    // odecteme pocet vozidel, ktera jsou v kosiku
    $UFOsInCart = $this->hireUFO->getAllSavedUFOs();

    foreach($UFOsInCart as $UFO) {
      $UFO = json_decode($UFO, true);

      if($UFO["model"] == $modelNumber) {
        $count--;
      }
    }

    return $count;
  }

  /**
   * Vrati primarni klic prvniho dostupneho UFO podle modelu.
   * @param int $modelNumber    cislo modelu UFO
   * @return mixed|null         primarni klic UFO / null
   */
  public function getAvailableUFONumberByModelNumber(int $modelNumber) {
    $query = "SELECT * FROM ".TABLE_UFO." WHERE c_modelu_fk='$modelNumber'";
    $allUFOs = $this->query($query);

    foreach($allUFOs as $UFO) {
      if($this->isUFOFree($UFO["c_ufo_pk"])) {
        return $UFO["c_ufo_pk"];
      }
    }

    return null;
  }

  public function getReviewByUserModel(int $userNumber, int $modelNumber) {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk='$userNumber' AND c_modelu_fk='$modelNumber'";

    $result = $this->query($query);

    if(count($result) == 1) {
      return $result[0];
    }
    else {
      return null;
    }
  }

  public function isUFOFree($UFONumber):bool {
    $query = "SELECT * FROM ".TABLE_HIRE." WHERE c_ufo_fk='$UFONumber'";

    $result = $this->query($query);

    if(count($result) == 0) {
      return true;
    }
    else {
      foreach($result as $hire) {
        $dateNow = date("Y-m-d");

        if($hire["d_vypujceni"] > $dateNow) {
          return true;
        }
        else if($hire["d_vraceni"] < $dateNow) {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * Metoda vytvori novou vypujcku a vlozi ji do tabulky VYPUJCKA.
   * Vraci primarni klice vsech vytvorenych vypujcek.
   * @param string $accountNumber   cislo uctu pro zaplaceni vypujcky
   * @return array                  pole primarnich klicu vytvorenych vypujcek
   */
  public function createNewHire(string $accountNumber):array {
    $allUFOS = $this->hireUFO->getAllSavedUFOs();
    $hireResult = [];

    if(count($allUFOS) == 0) {  // zadne UFO k vypujceni
      return [];
    }

    $adress = $this->hireUFO->loadAdressData();

    if($adress == null) {
      return [];
    }

    if($accountNumber == null) {
      return [];
    }

    $dateNow = date("Y-m-d");

    $i = 0;
    foreach($allUFOS as $UFO) {
      $UFO = json_decode($UFO, true);
      $modelNumber = $UFO["model"];

      $availableUFONumber = $this->getAvailableUFONumberByModelNumber($modelNumber);

      if($availableUFONumber == null) {   // UFO je momentalne nedostupne
        return [];
      }

      $days = $UFO["days"];
      $dateEnd = date('Y-m-d', strtotime($dateNow. ' + '.$days.' days'));
      $userNumber = $this->getLoggedUser()["c_uzivatele_pk"];
      $cityName = $adress["city"];
      $street = $adress["street"];
      $planet = $adress["planet"];
      $zip = $adress["zip"];

      if(!$this->doesCityExist($cityName)) { // mesto jeste neexistuje -> vytvorime ho
        $queryCity = "INSERT INTO ".TABLE_CITY." (nazev, psc)"." VALUES ('$cityName', '$zip')";
        $this->query($queryCity);

        if(!$this->doesCityExist($cityName)) {  // nepodarilo se vlozit nove mesto
          return [];
        }

        // mesto neexistovalo, proto adresa take urcite neexistuje
        $cityNumber = $this->getCityNumber($cityName);
        $queryAdress = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk)"." VALUES ('$street', '$planet', '$cityNumber')";
        $this->query($queryAdress);

        if(!$this->doesAdressExist($cityName, $street, $planet)) {  // nepodarilo se vlozit novou adresu
          return [];
        }
      }
      else if(!$this->doesAdressExist($cityName, $street, $planet)) {   // mesto existuje, ale adresa ne
        $cityNumber = $this->getCityNumber($cityName);
        $queryAdress = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk)"." VALUES ('$street', '$planet', '$cityNumber')";
        $this->query($queryAdress);

        if(!$this->doesAdressExist($cityName, $street, $planet)) {  // nepodarilo se vlozit novou adresu
          return [];
        }
      }

      $adressNumber = $this->getAdressNumber($cityName, $street, $planet);

      // vytvorime novy radek v tabulce VYPUJCKA
      $queryHire = "INSERT INTO ".TABLE_HIRE." (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)"
        ." VALUES ('$dateNow', '$dateEnd', '$accountNumber', '$userNumber', '$availableUFONumber', '$adressNumber')";

      $this->query($queryHire);

      $id = $this->conn->lastInsertId();   // primarni klic nove vypujcky

      if(!$this->doesHireExist($id)) {  // nepodarilo se vlozit novou vypujcku
        return [];
      }

      array_push($hireResult, $id);

      $i++;
    }

    // odstranime cookies pro vypujcku
    $this->hireUFO->removeAllHireCookies();
    return $hireResult;
  }

  public function getReviewsByModelNumber($modelNumber):array {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_modelu_fk='$modelNumber'";

    return $this->query($query);

  }

  public function createNewReview(int $rating, string $text, int $modelNumber):bool {
    $user = $this->getLoggedUser();
    $userNumber = $user["c_uzivatele_pk"];

    $datetime = date("Y-m-d H:i:s");

    if($rating < 1 || $rating > 5) {
      return false;
    }

    if($text == null) {
      $text = "";
    }

    if($this->doesReviewByThisUserExist($user["c_uzivatele_pk"], $modelNumber)) {
      // smazeme starou recenzi
      $query = "DELETE FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk=".$userNumber." AND c_modelu_fk=".$modelNumber;
      $this->query($query);
    }

    $query = "INSERT INTO ".TABLE_REVIEW." (text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk)"
      ." VALUES ('$text', '$rating', '$datetime', '$modelNumber', '$userNumber')";
    $this->query($query);

    $id = $this->conn->lastInsertId();    // primarni klic nove recenze

    if(!$this->doesReviewExist($id)) {
      return false;
    }

    return true;
  }

  /**
   * Metoda zjisti, zda uz uzivatel napsal recenzi na tento model.
   * @param int $userNumber       primarni klic uzivatele
   * @param int $modelNumber      primarni klic modelu
   * @return bool             true - uzivatel uz napsal na tento model recenzi / false jinak
   */
  public function doesReviewByThisUserExist($userNumber, $modelNumber):bool {
      $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk=".$userNumber." AND c_modelu_fk=".$modelNumber;

      $result = $this->query($query);

      if(count($result) > 0) {
        return true;
      }
      else {
        return false;
      }
  }

  /**
   * Metoda zjisti, zda si uzivatel nekdy v minulosti / pritomnosti tento model vypujcil.
   * @param int $userNumber     primarni klic uzivatele
   * @param int $modelNumber    primarni klic modelu
   * @return bool               true - uzivatel si nekdy model zapujcil / false jinak
   */
  public function hasUserEverHiredThisModel($userNumber, $modelNumber):bool {

    $query = "SELECT * FROM ".TABLE_HIRE." INNER JOIN ".TABLE_UFO." ON ".TABLE_HIRE.".c_ufo_fk = ".TABLE_UFO.".c_ufo_pk WHERE c_uzivatele_fk=".$userNumber." AND c_modelu_fk=".$modelNumber;

    $result = $this->query($query);

    if(count($result) > 0) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Metoda ziska urcity pocet nejnovejsich recenzi podle parametru.
   * @param int $numberOfReviews    kolik nejnovejsich recenzi chceme
   * @return array                  pole nejnovejsich recenzi
   */
  public function getNewestReviews(int $numberOfReviews):array {
    $query = "SELECT c_recenze_pk FROM ".TABLE_REVIEW;
    $primaryKeys = $this->query($query);

    $i = 0;
    foreach($primaryKeys as $row) {
      $primaryKeys[$i] = $row["c_recenze_pk"];
      $i++;
    }
    sort($primaryKeys, SORT_NUMERIC);   // seradime primarni klice recenzi od nejnizsiho do nejvyssiho
    $count = count($primaryKeys);

    $result = array();    // do vysledku dame nekolik nejnovejsich recenzi
    for($i = 0; $i < $numberOfReviews && $i < $count; $i++) {
      $review = $this->getReviewByNumber($primaryKeys[$count - $i - 1]);
      array_push($result, $review);
    }

    return $result;

  }

  /**
   * Metoda vrati vsechny vypujcky, ktere realizoval konkretni uzivatel.
   * @param int $userNumber   primarni klic uzivatele
   * @return array            pole vypujcek uzivatele
   */
  public function getHiresByUser(int $userNumber):array {
    $query = "SELECT * FROM ".TABLE_HIRE." WHERE c_uzivatele_fk=".$userNumber;

    return $this->query($query);
  }

  /**
   * Metoda vrati vsechny recenze, ktere napsal konkretni uzivatel.
   * @param int $userNumber   primarni klic uzivatele
   * @return array            pole recenzi uzivatele
   */
  public function getReviewsByUser(int $userNumber):array {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk=".$userNumber;

    return $this->query($query);
  }

  /**
   * Metoda odstrani recenzi podle jejiho primarniho klice.
   * @param int $reviewNumber   primarni klic recenze
   * @return bool               true - recenze byla odstranena / false jinak
   */
  public function deleteReview(int $reviewNumber):bool {
    $query = "DELETE FROM ".TABLE_REVIEW." WHERE c_recenze_pk=".$reviewNumber;

    $this->query($query);

    if($this->doesReviewExist($reviewNumber)) {   // recenzi se nepodarilo odstranit
      return false;
    }
    else {
      return true;
    }
  }

  /**
   * Metoda vlozi do databaze novy UFO model.
   * @param string $name          nazev modelu
   * @param int $price            cena za den
   * @param string $descShort     kratky popisek
   * @param string $descLong      dlouhy popis
   * @param int $people           pocet osob
   * @param int $battery          vydrz baterie [hod]
   * @param int $speed            rychlost [ly]
   * @param string $img           cesta k souboru obrazku
   * @param int $units            pocet UFO tohoto modelu na sklade
   * @return bool                 true - model se podarilo vlozit / false jinak
   */
  public function createNewModel(string $name, int $price, string $descShort, string $descLong, int $people, int $battery, int $speed, string $img, int $units):bool {
    if($descLong == null) {
      $descLong = "";
    }

    if(is_dir($img)) {
      $img = "img/default_ufo1.png";
    }
    if(!file_exists($img)) {
      $img = "img/default_ufo1.png";
    }


    $query = "INSERT INTO ".TABLE_MODEL." (nazev, cena_den, pocet_osob, vydrz_baterie, rychlost_ly, popis_kratky, popis_dlouhy, obrazek_url)"
      ." VALUES ('$name', '$price', '$people', '$battery', '$speed', '$descShort', '$descLong', '$img')";

    $this->query($query);

    $id = $this->conn->lastInsertId();   // primarni klic noveho modelu

    if(!$this->doesModelExist($id)) {  // nepodarilo se vlozit novy model
      return false;
    }
    else {
      for($i = 0; $i < $units; $i++) {
        $result = $this->createNewUFO($id);

        if(!$result) {
          return false;
        }
      }

      return true;
    }
  }

  public function createNewUFO($modelNumber):bool {
    $query = "INSERT INTO ".TABLE_UFO." (c_modelu_fk) VALUES ('$modelNumber')";

    $this->query($query);

    $id = $this->conn->lastInsertId();   // primarni klic noveho UFO

    if(!$this->doesUFOExist($id)) {  // nepodarilo se vlozit nove UFO
      return false;
    }
    else {
      return true;
    }
  }
}
