<?php

class DatabaseConnection
{
  private $conn;

  private Session $session;

  private const KEY_USER = "user";

  public function __construct () {
    require_once "databaseSettings.inc.php";
    $this->conn = new PDO("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $this->conn->exec("set names utf8");

    require_once "php/SessionClass.php";
    $this->session = new Session();
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

      // pokud mesto neexistovalo, adresa urcite take neexistuje
      $adressNumber = $this->getAdressCount() + 1;
      $query = "INSERT INTO ".TABLE_ADRESS." (c_adresy_pk, ulice, planeta, c_mesta_fk) "."VALUES ('$adressNumber', '$street', '$planet', '$cityNumber')";
      $this->query($query);
    }
    else {
      // mesto noveho uzivatele je v tabulace MESTO

      // mesto existuje, ale adresa neexistuje
      if(!$this->doesAdressExist($city, $street, $planet)) {
        $adressNumber = $this->getAdressCount() + 1;
        $cityNumber = $this->getCityNumber($city);
        $query = "INSERT INTO ".TABLE_ADRESS." (c_adresy_pk, ulice, planeta, c_mesta_fk) "."VALUES ('$adressNumber', '$street', '$planet', '$cityNumber')";
        $this->query($query);
      }
    }

    $adressNumber = $this->getAdressNumber($city, $street, $planet);

    $query = "INSERT INTO ".TABLE_USER." (c_uzivatele_pk, email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)"
    ." VALUES ('$userNumber', '$email', '$password1', '$name', '$surname', '$birthDate', '$tel', '3', '$adressNumber')";

    $result = $this->query($query);

    if($result == 0){ // bylo vlozeno 0 radku
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

  public function getUFOModelByNumber (int $number) {
    $query = "SELECT * FROM ".TABLE_MODEL." WHERE c_modelu_pk='$number'";

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

    $count = 0;
    foreach($allUFOs as $UFO) {
      if($this->isUFOFree($UFO["c_ufo_pk"])) {
        $count++;
      }
    }

    return $count;
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
}
