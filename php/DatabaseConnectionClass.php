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

  public function addUser(string $email, string $password1, string $password2, string $name, string $surname, string $rawDate, int $tel, string $city, string $street, int $zip, string $planet):bool {
    $user_number = $this->getUserCount() + 1;
    $date = date('Y-m-d', strtotime($rawDate));

    if($password1 != $password2) {
      return false;
    }

    $query = "INSERT INTO ".TABLE_USER." (c_uzivatele, email, heslo, pravo, jmeno, prijmeni, d_narozeni, tel_cislo, mesto, ulice, psc, planeta)"
    ." VALUES ('$user_number', '$email', '$password1', 'zakaznik', '$name', '$surname', '$date', '$tel', '$city', '$street', '$zip', '$planet')";

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

  public function getUFOModelByNumber (int $number) {
    $query = "SELECT * FROM ".TABLE_MODEL." WHERE c_modelu='$number'";

    $result = $this->query($query);

    if(count($result) > 0) {
      return $result[0];
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
}
