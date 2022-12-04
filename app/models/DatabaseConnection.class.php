<?php

/**
 * Trida pro volani metod, ktere provadeji dotazy nad databazi.
 */
class DatabaseConnection
{
  /**
   * @var PDO promenna pro praci s databazi
   */
  private PDO $conn;

  /**
   * @var Session promenna pro praci se session - prihlasovani a odhlasovani uzivatele
   */
  private Session $session;

  /**
   * @var HireUFO promenna pro vytvareni vypujcek
   */
  private HireUFO $hireUFO;

  /**
   * klic do session, pod kterym bude prihlaseny uzivatel ulozen
   */
  private const KEY_USER = "user";

  /**
   * Metoda vytvori instanci tridy DatabaseConnection.
   */
  public function __construct () {
    require_once "settings.inc.php";
    $this->conn = new PDO("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $this->conn->exec("set names utf8");

    require_once CONTROLLERS_PATH."Session.class.php";
    $this->session = new Session();

    require_once CONTROLLERS_PATH."HireUFO.class.php";
    $this->hireUFO = new HireUFO();
  }

  /**
   * Metoda provede dotaz nad databazi bez osetreni SQL injection.
   * @param string $query     dotaz
   * @return array            ziskana data
   */
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

  /**
   * Metoda zpracuje zadany email a heslo, pokud odpovidaji uzivateli v databazi, prihlasi jej.
   * @param string $email         email
   * @param string $password      heslo
   * @return bool                 true - uzivatel byl prihlasen / false - takovy uzivatel v databazi neexistuje
   */
  public function loginUser(string $email, string $password):bool {
    $email = htmlspecialchars($email);
    $password = htmlspecialchars($password);

    $query = "SELECT * FROM ".TABLE_USER." WHERE email=?";
    $result = $this->conn->prepare($query);

    if($result->execute(array($email))) {
      $result = $result->fetchAll();
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

  /**
   * Metoda odhlasi aktualniho uzivatele (odstrani ho ze session).
   * @return void
   */
  public function logoutUser() {
    if(isset($_SESSION[self::KEY_USER])) {
      $this->session->unsetSession(self::KEY_USER);
    }
  }

  /**
   * Metoda zjisti, zda je nejaky uzivatel prihlasen.
   * @return bool     true - uzivatel je prihlasen / false jinak
   */
  public function isUserLoggedIn():bool {
    return $this->session->isSessionSet(self::KEY_USER);
  }

  /**
   * Metoda vrati prave prihlaseneho uzivatele.
   * @return mixed|null     prave prihlaseny uzivatel / null, pokud uzivatel prihlasen neni
   */
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

  /**
   * Metoda prida noveho uzivatele do databaze pri registraci.
   * @param string $email         email
   * @param string $password1     heslo z prvniho pole formulare
   * @param string $password2     heslo z druheho pole formulare
   * @param string $name          jmeno
   * @param string $surname       prijmeni
   * @param string $rawDate       datum narozeni
   * @param string $tel           telefonni cislo
   * @param string $city          nazev mesta
   * @param string $street        ulice
   * @param string $zip           PSC
   * @param string $planet        planeta
   * @return bool                 true - uzivatel byl zaregistrovan / false jinak.
   */
  public function addUser(string $email, string $password1, string $password2, string $name, string $surname, string $rawDate, string $tel, string $city, string $street, string $zip, string $planet):bool {
    $email = htmlspecialchars($email);
    $password1 = htmlspecialchars($password1);
    $password2 = htmlspecialchars($password2);
    $name = htmlspecialchars($name);
    $surname = htmlspecialchars($surname);
    $rawDate = htmlspecialchars($rawDate);
    $tel = htmlspecialchars($tel);
    $city = htmlspecialchars($city);
    $street = htmlspecialchars($street);
    $zip = htmlspecialchars($zip);
    $planet = htmlspecialchars($planet);

    $birthDate = date('Y-m-d', strtotime($rawDate));

    if($password1 != $password2) {
      return false;
    }

    // mesto noveho uzivatele jeste neni v tabulce MESTO
    if(!$this->doesCityExist($city)) {
      $query = "INSERT INTO ".TABLE_CITY." (nazev, psc) "."VALUES (?, ?)";
      $result = $this->conn->prepare($query);

      if(!$result->execute(array($city, $zip))) {  // mesto se nepodarilo vlozit
        return false;
      }

      $cityNumber = $this->getCityNumber($city);

      // pokud mesto neexistovalo, adresa urcite take neexistuje
      $query = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk) "."VALUES (?, ?, ?)";
      $result = $this->conn->prepare($query);

      if(!$result->execute(array($street, $planet, $cityNumber))) {  // adresu se nepodarilo vlozit
        return false;
      }
    }
    else {
      // mesto noveho uzivatele je v tabulace MESTO

      // mesto existuje, ale adresa neexistuje
      if(!$this->doesAdressExist($city, $street, $planet)) {
        $cityNumber = $this->getCityNumber($city);
        $query = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk) "."VALUES (?, ?, ?)";
        $result = $this->conn->prepare($query);

        if(!$result->execute(array($street, $planet, $cityNumber))) {  // adresu se nepodarilo vlozit
          return false;
        }
      }
    }

    $adressNumber = $this->getAdressNumber($city, $street, $planet);

    $encryptedPassword = password_hash($password1, PASSWORD_DEFAULT);

    $query = "INSERT INTO ".TABLE_USER." (email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)"
    ." VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $result = $this->conn->prepare($query);

    if(!$result->execute(array($email, $encryptedPassword, $name, $surname, $birthDate, $tel, '3', $adressNumber))){  // uzivatel nebyl vlozen
      return false;
    }
    else {
      return true;
    }
  }

  /**
   * Metoda modifikuje udaje prave prihlaseneho uzivatele (krome emailu).
   * @param string $password1     heslo z prvniho pole formulare
   * @param string $password2     heslo z druheho pole formulare
   * @param string $name          jmeno
   * @param string $surname       prijmeni
   * @param string $rawDate       datum narozeni
   * @param string $tel           telefonni cislo
   * @param string $city          nazev mesta
   * @param string $street        ulice
   * @param string $zip           PSC
   * @param string $planet        planeta
   * @return bool                 true - uzivatel byl modifikovan / false jinak.
   */
  public function modifyUser(string $password1, string $password2, string $name, string $surname, string $rawDate, string $tel, string $city, string $street, string $zip, string $planet):bool {
    $password1 = htmlspecialchars($password1);
    $password2 = htmlspecialchars($password2);
    $name = htmlspecialchars($name);
    $surname = htmlspecialchars($surname);
    $rawDate = htmlspecialchars($rawDate);
    $tel = htmlspecialchars($tel);
    $city = htmlspecialchars($city);
    $street = htmlspecialchars($street);
    $zip = htmlspecialchars($zip);
    $planet = htmlspecialchars($planet);

    $user = $this->getLoggedUser();
    $birthDate = date('Y-m-d', strtotime($rawDate));

    if($password1 != $password2) {
      return false;
    }

    // mesto uzivatele jeste neni v tabulce MESTO
    if(!$this->doesCityExist($city)) {
      $query = "INSERT INTO ".TABLE_CITY." (nazev, psc) "."VALUES (?, ?)";
      $result = $this->conn->prepare($query);

      if(!$result->execute(array($city, $zip))) {  // mesto se nepodarilo vlozit
        return false;
      }

      $cityNumber = $this->getCityNumber($city);

      // pokud mesto neexistovalo, adresa urcite take neexistuje
      $query = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk) "."VALUES (?, ?, ?)";
      $result = $this->conn->prepare($query);

      if(!$result->execute(array($street, $planet, $cityNumber))) {  // adresu se nepodarilo vlozit
        return false;
      }
    }
    else {
      // mesto uzivatele je v tabulace MESTO

      // mesto existuje, ale adresa neexistuje
      if(!$this->doesAdressExist($city, $street, $planet)) {
        $cityNumber = $this->getCityNumber($city);
        $query = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk) "."VALUES (?, ?, ?)";
        $result = $this->conn->prepare($query);

        if(!$result->execute(array($street, $planet, $cityNumber))) {  // adresu se nepodarilo vlozit
          return false;
        }
      }
    }

    $adressNumber = $this->getAdressNumber($city, $street, $planet);

    $encryptedPassword = password_hash($password1, PASSWORD_DEFAULT);

    $query = "UPDATE ".TABLE_USER." SET heslo=?, jmeno=?, prijmeni=?, d_narozeni=?, tel_cislo=?, c_adresy_fk=? WHERE c_uzivatele_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($encryptedPassword, $name, $surname, $birthDate, $tel, $adressNumber, $user["c_uzivatele_pk"]))) {  // adresu se nepodarilo vlozit
      return false;
    }

    return true;
  }

  /**
   * Metoda povysi uzivatele - zmensi cislo prava o 1.
   * @param int $userNumber   primarni klic uzivatele
   * @return bool             true - povyseni se podarilo / false jinak
   */
  public function promoteUser(int $userNumber):bool {
    $roleNumber = $this->getUserByNumber($userNumber)["c_prava_fk"] - 1;

    if($roleNumber <= 0) {
      return false;
    }

    $query = "UPDATE ".TABLE_USER." SET c_prava_fk=? WHERE c_uzivatele_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($roleNumber, $userNumber))) {
      return false;
    }
    else {
      return true;
    }
  }

  /**
   * Metoda zbavi uzivatele funkce - zvysi cislo prava o 1.
   * @param int $userNumber     primarni klic uzivatele
   * @return bool               true - zbaveni funkce se podarilo / false jinak
   */
  public function demoteUser(int $userNumber):bool {
    $roleNumber = $this->getUserByNumber($userNumber)["c_prava_fk"] + 1;

    if($roleNumber >= 4) {
      return false;
    }

    $query = "UPDATE ".TABLE_USER." SET c_prava_fk=? WHERE c_uzivatele_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($roleNumber, $userNumber))) {
      return false;
    }
    else {
      return true;
    }
  }

  /**
   * Metoda zjisti, zda existuje uzivatel s danym emailem.
   * @param string $email     email
   * @return bool             true - uzivatel s takovym emailem existuje / false jinak
   */
  public function doesUserExist(string $email):bool {
    $email = htmlspecialchars($email);

    $query = "SELECT * FROM ".TABLE_USER ." WHERE email=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($email))) {
      return false;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return true;
      }
      else {
        return false;
      }
    }
  }

  /**
   * Metoda zjisti, zda existuje mesto s danym nazvem.
   * @param string $city      nazev mesta
   * @return bool             true - mesto s takovym nazvem existuje / false jinak
   */
  public function doesCityExist(string $city):bool {
    $city = htmlspecialchars($city);

    $query = "SELECT * FROM ".TABLE_CITY." WHERE nazev=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($city))) {
      return false;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return true;
      }
      else {
        return false;
      }
    }
  }

  /**
   * Metoda zjisti, zda existuje adresa s takovym nazvem mesta, ulici a planetou.
   * @param string $city        nazev mesta
   * @param string $street      ulice
   * @param string $planet      planeta
   * @return bool               true - takova adresa existuje / false jinak
   */
  public function doesAdressExist(string $city, string $street, string $planet):bool {
    $city = htmlspecialchars($city);
    $street = htmlspecialchars($street);
    $planet = htmlspecialchars($planet);

    $cityNumber = $this->getCityNumber($city);

    if($cityNumber == null) {
      return false;
    }

    $query = "SELECT * FROM ".TABLE_ADRESS." WHERE ulice=? AND planeta=? AND c_mesta_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($street, $planet, $cityNumber))) {
      return false;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return true;
      }
      else {
        return false;
      }
    }
  }

  /**
   * Metoda vrati cely radek UFO modelu podle jeho primarniho klice.
   * @param int $modelNumber      cislo modelu
   * @return mixed|null           radek modelu v databazi / null, pokud cislo modelu zadnemu modelu neodpovida
   */
  public function getUFOModelByNumber (int $modelNumber) {
    $query = "SELECT * FROM ".TABLE_MODEL." WHERE c_modelu_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($modelNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda vrati cely radek uzivatele podle jeho primarniho klice.
   * @param int $userNumber     cislo uzivatele
   * @return mixed|null         radek uzivatele v databazi / null, pokud cislo uzivatele zadnemu uzivateli neodpovida
   */
  public function getUserByNumber (int $userNumber) {
    $query = "SELECT * FROM ".TABLE_USER." WHERE c_uzivatele_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda vrati cely radek mesta podle jeho primarniho klice.
   * @param int $cityNumber     cislo mesta
   * @return mixed|null         radek mesta v databazi / null, pokud cislo mesta zadnemu mestu neodpovida
   */
  public function getCityByNumber(int $cityNumber) {
    $query = "SELECT * FROM ".TABLE_CITY." WHERE c_mesta_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($cityNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda vrati cely radek adresy podle jejiho primarniho klice.
   * @param int $adressNumber     cislo adresy
   * @return mixed|null           radek adresy v databazi / null, pokud cislo adresy zadne adrese neodpovida
   */
  public function getAdressByNumber(int $adressNumber) {
    $query = "SELECT * FROM ".TABLE_ADRESS." WHERE c_adresy_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($adressNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda vrati cely radek UFO vozidla podle jeho primarniho klice.
   * @param int $userNumber     cislo UFO vozidla
   * @return mixed|null         radek UFO vozidla v databazi / null, pokud cislo UFO vozidla zadnemu vozidlu neodpovida
   */
  public function getUFOByNumber(int $UFONumber) {
    $query = "SELECT * FROM ".TABLE_UFO." WHERE c_ufo_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($UFONumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda vrati cely radek vypujcky podle jejiho primarniho klice.
   * @param int $userNumber     cislo vypujcky
   * @return mixed|null         radek vypujcky v databazi / null, pokud cislo vypujcky zadne vypujcce neodpovida
   */
  public function getHireByNumber(int $hireNumber) {
    $query = "SELECT * FROM ".TABLE_HIRE." WHERE c_vypujcky_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($hireNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda vrati cely radek recenze podle jejiho primarniho klice.
   * @param int $userNumber     cislo recenze
   * @return mixed|null         radek recenze v databazi / null, pokud cislo recenze zadne recenzi neodpovida
   */
  public function getReviewByNumber(int $reviewNumber) {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_recenze_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($reviewNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda vrati cely radek prava podle jeho primarniho klice.
   * @param int $userNumber     cislo prava
   * @return mixed|null         radek prava v databazi / null, pokud cislo prava zadnemu pravu neodpovida
   */
  public function getRoleByNumber(int $roleNumber) {
    $query = "SELECT * FROM ".TABLE_ROLE." WHERE c_prava_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($roleNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda zjisti primarni klic mesta podle jeho nazvu.
   * @param string $city      nazev mesta
   * @return mixed|null       primarni klic mesta / null, pokud mesto s takovym nazvem neexistuje
   */
  public function getCityNumber(string $city) {
    $city = htmlspecialchars($city);

    $query = "SELECT * FROM ".TABLE_CITY." WHERE nazev=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($city))) {
      return null;
    }
    else {
      $result = $result->fetchAll();

      if(count($result) > 0) {
        return $result[0]["c_mesta_pk"];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda zjisti primarni klic adresy podle nazvu mesta, ulice a planety.
   * @param string $city        nazev mesta
   * @param string $street      ulice
   * @param string $planet      planeta
   * @return mixed|null         primarni klic adresy / null, pokud takova adresa neexistuje
   */
  public function getAdressNumber(string $city, string $street, string $planet) {
    $city = htmlspecialchars($city);
    $street = htmlspecialchars($street);
    $planet = htmlspecialchars($planet);

    $cityNumber = $this->getCityNumber($city);

    $query = "SELECT * FROM ".TABLE_ADRESS." WHERE ulice=? AND planeta=? AND c_mesta_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($street, $planet, $cityNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();

      if(count($result) > 0) {
        return $result[0]["c_adresy_pk"];
      }
      else {
        return null;
      }
    }

  }

  /**
   * Metoda vrati seznam vsech existujicich UFO modelu v databazi.
   * @return array      vsechny existujici UFO modely
   */
  public function getAllUFOModels():array {
    $query = "SELECT * FROM ".TABLE_MODEL;

    return $this->query($query);
  }

  /**
   * Metoda vrati seznam vsech recenzi v databazi.
   * @return array      vsechny existujici recenze
   */
  public function getAllReviews():array {
    $query = "SELECT * FROM ".TABLE_REVIEW." ORDER BY c_modelu_fk";

    return $this->query($query);
  }

  /**
   * Metoda vrati seznam vsech uzivatelu v databazi.
   * @return array      vsichni existujici uzivatele
   */
  public function getAllUsers():array {
    $query = "SELECT * FROM ".TABLE_USER." ORDER BY c_prava_fk";

    return $this->query($query);
  }

  /**
   * Metoda zjisti pocet volnych UFO vozidel podle modelu.
   * UFO je volne, jestlize neni v kosiku a neni asociovano s vypujckou, ktera prave probiha.
   * @param int $modelNumber      cislo modelu
   * @return int                  pocet volnych UFO vozidel tohoto modelu
   */
  public function getNumberOfUFOsAvailableByModelNumber(int $modelNumber):int {
    $query = "SELECT * FROM ".TABLE_UFO." WHERE c_modelu_fk=?";
    $allUFOs = $this->conn->prepare($query);

    if(!$allUFOs->execute(array($modelNumber))) {
      return 0;
    }

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

      if(intval($UFO["model"]) == $modelNumber) {
        $count--;
      }
    }

    return $count;
  }

  /**
   * Vrati primarni klic prvniho dostupneho UFO podle modelu.
   * @param int $modelNumber    cislo modelu UFO
   * @return mixed|null         primarni klic UFO / null, pokud zadne UFO neni dostupne
   */
  public function getAvailableUFONumberByModelNumber(int $modelNumber) {
    $query = "SELECT * FROM ".TABLE_UFO." WHERE c_modelu_fk=?";
    $allUFOs = $this->conn->prepare($query);

    if(!$allUFOs->execute(array($modelNumber))) {
      return null;
    }

    foreach($allUFOs as $UFO) {
      if($this->isUFOFree($UFO["c_ufo_pk"])) {
        return $UFO["c_ufo_pk"];
      }
    }

    return null;
  }

  /**
   * Metoda vrati recenzi daneho uzivatele na dany model.
   * @param int $userNumber       cislo uzivatele
   * @param int $modelNumber      cislo modelu
   * @return mixed|null           recenze uzivatele na dany model / null, pokud takova recenze neexistuje
   */
  public function getReviewByUserModel(int $userNumber, int $modelNumber) {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk=? AND c_modelu_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber, $modelNumber))) {
      return null;
    }
    else {
      $result = $result->fetchAll();
      if(count($result) > 0) {
        return $result[0];
      }
      else {
        return null;
      }
    }
  }

  /**
   * Metoda zjisti, zda je UFO vozidlo volne.
   * Vozidlo je volne, pokud neni asociovano s vypujckou, ktera prave probiha.
   * @param int $UFONumber      cislo UFO vozidla
   * @return bool               true - vozidlo je prave volne / false jinak
   */
  public function isUFOFree(int $UFONumber):bool {
    $query = "SELECT * FROM ".TABLE_HIRE." WHERE c_ufo_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($UFONumber))) {
      return false;
    }
    else {
      $result = $result->fetchAll();

      if(count($result) == 0) {
        return true;
      }
      else {
        foreach ($result as $hire) {
          $dateNow = date("Y-m-d");

          if ($hire["d_vypujceni"] > $dateNow) {
            return true;
          } else if ($hire["d_vraceni"] < $dateNow) {
            return true;
          }
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
    $accountNumber = htmlspecialchars($accountNumber);

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
      $modelNumber = intval($UFO["model"]);

      $availableUFONumber = $this->getAvailableUFONumberByModelNumber($modelNumber);

      if($availableUFONumber == null) {   // UFO je momentalne nedostupne
        return [];
      }

      $days = intval($UFO["days"]);
      $dateEnd = date('Y-m-d', strtotime($dateNow. ' + '.$days.' days'));
      $userNumber = $this->getLoggedUser()["c_uzivatele_pk"];
      $cityName = htmlspecialchars($adress["city"]);
      $street = htmlspecialchars($adress["street"]);
      $planet = htmlspecialchars($adress["planet"]);
      $zip = htmlspecialchars($adress["zip"]);

      if(!$this->doesCityExist($cityName)) { // mesto jeste neexistuje -> vytvorime ho
        $queryCity = "INSERT INTO ".TABLE_CITY." (nazev, psc)"." VALUES (?, ?)";
        $result = $this->conn->prepare($queryCity);

        if(!$result->execute(array($cityName, $zip))) {  // nepodarilo se vlozit nove mesto
          return [];
        }

        // mesto neexistovalo, proto adresa take urcite neexistuje
        $cityNumber = $this->getCityNumber($cityName);
        $queryAdress = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk)"." VALUES (?, ?, ?)";
        $result = $this->conn->prepare($queryAdress);

        if(!$result->execute(array($street, $planet, $cityNumber))) {  // nepodarilo se vlozit novou adresu
          return [];
        }
      }
      else if(!$this->doesAdressExist($cityName, $street, $planet)) {   // mesto existuje, ale adresa ne
        $cityNumber = $this->getCityNumber($cityName);
        $queryAdress = "INSERT INTO ".TABLE_ADRESS." (ulice, planeta, c_mesta_fk)"." VALUES (?, ?, ?)";
        $result = $this->conn->prepare($queryAdress);

        if(!$result->execute(array($street, $planet, $cityNumber))) {  // nepodarilo se vlozit novou adresu
          return [];
        }
      }

      $adressNumber = $this->getAdressNumber($cityName, $street, $planet);

      // vytvorime novy radek v tabulce VYPUJCKA
      $queryHire = "INSERT INTO ".TABLE_HIRE." (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)"
        ." VALUES (?, ?, ?, ?, ?, ?)";

      $result = $this->conn->prepare($queryHire);

      if(!$result->execute(array($dateNow, $dateEnd, $accountNumber, $userNumber, $availableUFONumber, $adressNumber))) {
        return [];
      }

      $id = $this->conn->lastInsertId();   // primarni klic nove vypujcky
      array_push($hireResult, $id);

      $i++;
    }

    // odstranime cookies pro vypujcku
    $this->hireUFO->removeAllHireCookies();
    return $hireResult;
  }

  /**
   * Metoda ziska vsechny recenze na dany model.
   * @param int $modelNumber      cislo modelu
   * @return array                radky vsech recenzi na dany model
   */
  public function getReviewsByModelNumber(int $modelNumber):array {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_modelu_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($modelNumber))) {
      return [];
    }
    else {
      return $result->fetchAll();
    }

  }

  /**
   * Metoda vytvori novou recenzi a vlozi ji do tabulky RECENZE.
   * @param int $rating         hvezdickove ohodnoceni
   * @param string $text        text recenze
   * @param int $modelNumber    cislo modelu
   * @return bool               true - recenze byla vytvorena / false jinak
   */
  public function createNewReview(int $rating, string $text, int $modelNumber):bool {
    $text = htmlspecialchars($text);

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
      $query = "DELETE FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk=? AND c_modelu_fk=?";
      $result = $this->conn->prepare($query);

      if(!$result->execute(array($userNumber, $modelNumber))) {
        return false;
      }
    }

    $query = "INSERT INTO ".TABLE_REVIEW." (text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk) VALUES (?, ?, ?, ?, ?)";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($text, $rating, $datetime, $modelNumber, $userNumber))) {
      return false;
    }

    return true;
  }

  /**
   * Metoda zjisti, zda uz uzivatel napsal recenzi na tento model.
   * @param int $userNumber       primarni klic uzivatele
   * @param int $modelNumber      primarni klic modelu
   * @return bool                 true - uzivatel uz napsal na tento model recenzi / false jinak
   */
  public function doesReviewByThisUserExist(int $userNumber, int $modelNumber):bool {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk=? AND c_modelu_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber, $modelNumber))) {
      return false;
    }
    else {
      $result = $result->fetchAll();

      if(count($result) > 0) {
        return true;
      }
      else {
        return false;
      }
    }
  }

  /**
   * Metoda zjisti, zda si uzivatel nekdy v minulosti / pritomnosti tento model vypujcil.
   * @param int $userNumber     primarni klic uzivatele
   * @param int $modelNumber    primarni klic modelu
   * @return bool               true - uzivatel si nekdy model zapujcil / false jinak
   */
  public function hasUserEverHiredThisModel($userNumber, $modelNumber):bool {
    $query = "SELECT * FROM ".TABLE_HIRE." INNER JOIN ".TABLE_UFO." ON ".TABLE_HIRE.".c_ufo_fk = ".TABLE_UFO.".c_ufo_pk WHERE c_uzivatele_fk=? AND c_modelu_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber, $modelNumber))) {
      return false;
    }
    else {
      $result = $result->fetchAll();

      if(count($result) > 0) {
        return true;
      }
      else {
        return false;
      }
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
    $query = "SELECT * FROM ".TABLE_HIRE." WHERE c_uzivatele_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber))) {
      return [];
    }
    else {
      return $result->fetchAll();
    }
  }

  /**
   * Metoda vrati vsechny recenze, ktere napsal konkretni uzivatel.
   * @param int $userNumber   primarni klic uzivatele
   * @return array            pole recenzi uzivatele
   */
  public function getReviewsByUser(int $userNumber):array {
    $query = "SELECT * FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber))) {
      return [];
    }
    else {
      return $result->fetchAll();
    }
  }

  /**
   * Metoda odstrani recenzi podle jejiho primarniho klice.
   * @param int $reviewNumber   primarni klic recenze
   * @return bool               true - recenze byla odstranena / false jinak
   */
  public function deleteReview(int $reviewNumber):bool {
    $query = "DELETE FROM ".TABLE_REVIEW." WHERE c_recenze_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($reviewNumber))) {
      return false;
    }
    return true;
  }

  /**
   * Metoda odstrani uzivatele podle jeho primarniho klice.
   * Nejdrive se museji vymazat vsechny vypujcky a recenze tohoto uzivatele.
   * @param int $userNumber     primarni klic uzivatele
   * @return bool               true - uzivatel byl odstranen / false jinak
   */
  public function deleteUser(int $userNumber):bool {
    $user = $this->getUserByNumber($userNumber);
    $email = $user["email"];

    // odstraneni vypujcek
    $query = "DELETE FROM ".TABLE_HIRE." WHERE c_uzivatele_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber))) {
      return false;
    }

    // odstraneni recenzi
    $query = "DELETE FROM ".TABLE_REVIEW." WHERE c_uzivatele_fk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber))) {
      return false;
    }

    // odstraneni uzivatele
    $query = "DELETE FROM ".TABLE_USER." WHERE c_uzivatele_pk=?";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($userNumber))) {
      return false;
    }

    return true;
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
    $name = htmlspecialchars($name);
    $descShort = htmlspecialchars($descShort);
    $descLong = htmlspecialchars($descLong);
    $img = htmlspecialchars($img);

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
      ." VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $result = $this->conn->prepare($query);

    if(!$result->execute(array($name, $price, $people, $battery, $speed, $descShort, $descLong, $img))) {
      return false;
    }

    $id = $this->conn->lastInsertId();   // primarni klic noveho modelu

    for($i = 0; $i < $units; $i++) {
      $result = $this->createNewUFO($id);

      if(!$result) {
        return false;
      }
    }

    return true;

  }

  /**
   * Metoda vytvori nove UFO vozidlo daneho modelu a vlozi ho do tabulky UFO.
   * @param int $modelNumber      cislo modelu
   * @return bool                 true - vozidlo bylo vytvoreno / false jinak
   */
  public function createNewUFO(int $modelNumber):bool {
    $query = "INSERT INTO ".TABLE_UFO." (c_modelu_fk) VALUES (?)";
    $result = $this->conn->prepare($query);

    if(!$result->execute(array($modelNumber))) {
      return false;
    }
    return true;
  }

}
