<?php

class DatabaseConnection
{
  private $conn;

  /* Pro prihlasovani / odhlasovani uzivatele */
  private Session $session;

  /* Pro vytvareni vypujcek */
  private HireUFO $hireUFO;

  private const KEY_USER = "user";

  public function __construct () {
    require_once "databaseSettings.inc.php";
    $this->conn = new PDO("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $this->conn->exec("set names utf8");

    require_once "php/SessionClass.php";
    $this->session = new Session();

    require_once "php/HireUFOClass.php";
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
    $query = "SELECT * FROM ".TABLE_USER." WHERE email='$email' AND heslo='$password'";

    $result = $this->query($query);

    if(count($result) == 1) {
      $this->session->setSession(self::KEY_USER, $result[0]["email"]);
      return true;
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
    $userNumber = $this->getUserCount() + 1;
    $birthDate = date('Y-m-d', strtotime($rawDate));

    if($password1 != $password2) {
      return false;
    }

    // mesto noveho uzivatele jeste neni v tabulce MESTO
    if(!$this->doesCityExist($city)) {
      $cityNumber = $this->getCityCount() + 1;
      $query = "INSERT INTO ".TABLE_CITY." (c_mesta_pk, nazev, psc) "."VALUES ('$cityNumber', '$city', '$zip')";
      $this->query($query);

      if(!$this->doesCityExist($city)) {  // mesto se nepodarilo vlozit
        return false;
      }

      // pokud mesto neexistovalo, adresa urcite take neexistuje
      $adressNumber = $this->getAdressCount() + 1;
      $query = "INSERT INTO ".TABLE_ADRESS." (c_adresy_pk, ulice, planeta, c_mesta_fk) "."VALUES ('$adressNumber', '$street', '$planet', '$cityNumber')";
      $this->query($query);

      if(!$this->doesAdressExist($city, $street, $planet)) {  // adresu se nepodarilo vlozit
        return false;
      }
    }
    else {
      // mesto noveho uzivatele je v tabulace MESTO

      // mesto existuje, ale adresa neexistuje
      if(!$this->doesAdressExist($city, $street, $planet)) {
        $adressNumber = $this->getAdressCount() + 1;
        $cityNumber = $this->getCityNumber($city);
        $query = "INSERT INTO ".TABLE_ADRESS." (c_adresy_pk, ulice, planeta, c_mesta_fk) "."VALUES ('$adressNumber', '$street', '$planet', '$cityNumber')";
        $this->query($query);

        if(!$this->doesAdressExist($city, $street, $planet)) {  // adresu se nepodarilo vlozit
          return false;
        }
      }
    }

    $adressNumber = $this->getAdressNumber($city, $street, $planet);

    $query = "INSERT INTO ".TABLE_USER." (c_uzivatele_pk, email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)"
    ." VALUES ('$userNumber', '$email', '$password1', '$name', '$surname', '$birthDate', '$tel', '3', '$adressNumber')";

    $this->query($query);

    if(!$this->doesUserExist($email)){  // uzivatel nebyl vlozen
      return false;
    }
    else {
      $this->loginUser($email, $password1);
      return true;
    }
  }

  public function getUserCount():int {
    $query = "SELECT * FROM ".TABLE_USER;

    $result = $this->query($query);

    return count($result);
  }

  public function getCityCount():int {
    $query = "SELECT * FROM ".TABLE_CITY;

    $result = $this->query($query);

    return count($result);
  }

  public function getAdressCount():int {
    $query = "SELECT * FROM ".TABLE_ADRESS;

    $result = $this->query($query);

    return count($result);
  }

  public function getHireCount():int {
    $query = "SELECT * FROM ".TABLE_HIRE;

    $result = $this->query($query);

    return count($result);
  }

  public function getReviewCount():int {
    $query = "SELECT * FROM ".TABLE_REVIEW;

    $result = $this->query($query);

    return count($result);
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

    $result = $this->query($query);

    return $result;
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

  /*
   * Vrati primarni klic prvniho dostupneho UFO podle modelu.
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

  /*
   * Metoda vytvori novou vypujcku a vlozi ji do tabulky VYPUJCKA.
   * Vraci primarni klice vsech vytvorenych vypujcek.
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
        $cityNumber = $this->getCityCount() + 1;
        $queryCity = "INSERT INTO ".TABLE_CITY." (c_mesta_pk, nazev, psc)"." VALUES ('$cityNumber', '$cityName', '$zip')";
        $this->query($queryCity);

        if(!$this->doesCityExist($cityName)) {  // nepodarilo se vlozit nove mesto
          return [];
        }

        // mesto neexistovalo, proto adresa take urcite neexistuje
        $adressNumber = $this->getAdressCount() + 1;
        $queryAdress = "INSERT INTO ".TABLE_ADRESS." (c_adresy_pk, ulice, planeta, c_mesta_fk)"." VALUES ('$adressNumber', '$street', '$planet', '$cityNumber')";
        $this->query($queryAdress);

        if(!$this->doesAdressExist($cityName, $street, $planet)) {  // nepodarilo se vlozit novou adresu
          return [];
        }
      }
      else if(!$this->doesAdressExist($cityName, $street, $planet)) {   // mesto existuje, ale adresa ne
        $cityNumber = $this->getCityNumber($cityName);
        $adressNumber = $this->getAdressCount() + 1;
        $queryAdress = "INSERT INTO ".TABLE_ADRESS." (c_adresy_pk, ulice, planeta, c_mesta_fk)"." VALUES ('$adressNumber', '$street', '$planet', '$cityNumber')";
        $this->query($queryAdress);

        if(!$this->doesAdressExist($cityName, $street, $planet)) {  // nepodarilo se vlozit novou adresu
          return [];
        }
      }

      $hireNumber = $this->getHireCount() + 1;
      $adressNumber = $this->getAdressNumber($cityName, $street, $planet);

      // vytvorime novy radek v tabulce VYPUJCKA
      $queryHire = "INSERT INTO ".TABLE_HIRE." (c_vypujcky_pk, d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)"
        ." VALUES ('$hireNumber', '$dateNow', '$dateEnd', '$accountNumber', '$userNumber', '$availableUFONumber', '$adressNumber')";

      $this->query($queryHire);

      if(!$this->doesHireExist($hireNumber)) {  // nepodarilo se vlozit novou vypujcku
        return [];
      }

      array_push($hireResult, $hireNumber);

      $i++;
    }

    // odstranime cookies pro vypujcku
    $this->hireUFO->removeAllHireCookies();
    return $hireResult;
  }

  public function getReviewsByModelNumber($modelNumber):array {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_modelu_fk='$modelNumber'";

    $result = $this->query($query);

    return $result;

  }

  public function createNewReview(int $rating, string $text, int $modelNumber):bool {
    $user = $this->getLoggedUser();
    $userNumber = $user["c_uzivatele_pk"];
    $reviewNumber = $this->getReviewCount() + 1;
    $datetime = date("Y-m-d H:i:s");

    if($rating < 0 || $rating > 5) {
      return false;
    }

    if($text == null) {
      $text = "";
    }

    $query = "INSERT INTO ".TABLE_REVIEW." (c_recenze_pk, text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk)"
      ." VALUES ('$reviewNumber', '$text', '$rating', '$datetime', '$modelNumber', '$userNumber')";
    $this->query($query);

    if(!$this->doesReviewExist($reviewNumber)) {
      return false;
    }

    return true;

  }
}
