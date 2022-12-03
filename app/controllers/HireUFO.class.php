<?php

/**
 * Trida zpracovava vstup od uzivatele behem nakupu, od pridani UFO do kosiku, pres zadani adresy, po odstraneni techto dat
 * pri objednani nakupu.
 * Data jsou prubezne ukladana do cookies.
 */
class HireUFO
{
  /**
   * @var Cookies promenna pro praci s cookies
   */
  private Cookies $cookie;

  // v cookies jsou ulozena vsechna data potrebna pro vytvoreni radku v tabulce VYPUJCKA
  private const COOKIE_KEY_UFO = "hired_ufo";
  private const KEY_MODEL = "model";
  private const KEY_DAYS = "days";
  private const KEY_PRICE = "price";

  // tato data se pridavaji postupne, jak uzivatel prochazi procesem objednani
  private const COOKIE_KEY_ADRESS = "adress";
  private const KEY_CITY = "city";
  private const KEY_ZIP = "zip";
  private const KEY_STREET = "street";
  private const KEY_PLANET = "planet";

  // poradi UFO v kosiku
  private const KEY_INDEX = "index";

  /**
   * Konstruktor pro vytvoreni instance tridy HireUFO.
   */
  public function __construct() {
    require_once "Cookies.class.php";

    $this->cookie = new Cookies();
  }

  /**
   * Metoda zjisti, zda pod danym klicem+indexem je nastaveno cookie s UFO.
   * @param int $index    index UFO v cookie
   * @return bool         true - v cookie na tomto indexu je UFO / false jinak
   */
  public function isUFOSaved(int $index):bool {
    return $this->cookie->isCookieSet(self::COOKIE_KEY_UFO."$index");
  }

  /**
   * Metoda nacte data o UFO z cookie na danem indexu.
   * @param int $index      index cookie s UFO
   * @return mixed|null     asociativni pole s daty o UFO / null, pokud v cookie UFO neni
   */
  public function loadUFOData(int $index) {
    if($this->isUFOSaved($index)) {
      return json_decode($_COOKIE[self::COOKIE_KEY_UFO."$index"], true);
    }
    return null;
  }

  /**
   * Metoda nastavi zakladni informace o vypujcce pri pridani UFO do kosiku.
   * @param int $model    cislo modelu
   * @param int $days     pocet dni vypujcky
   * @param int $price    celkova cena vypujcky tohoto modelu na urcity pocet dni
   * @return void
   */
  public function saveUFOData(int $model, int $days, int $price) {
    $index = $this->getSavedCount();
    $data = [self::KEY_MODEL => $model, self::KEY_DAYS => $days, self::KEY_PRICE => $price, self::KEY_INDEX => $index];
    $this->cookie->setCookie(self::COOKIE_KEY_UFO."$index", json_encode($data));
  }

  /**
   * Metoda nastavi adresu vypujcky pri objednavani.
   * @param string $city        nazev mesta
   * @param string $zip         PSC
   * @param string $street      ulice
   * @param string $planet      planeta
   * @return void
   */
  public function saveAdressData(string $city, string $zip, string $street, string $planet) {
    $data = [self::KEY_CITY => $city, self::KEY_ZIP => $zip, self::KEY_STREET => $street, self::KEY_PLANET => $planet];
    $this->cookie->setCookie(self::COOKIE_KEY_ADRESS, json_encode($data));
  }

  /**
   * Metoda z cookie nacte data o adrese.
   * @return mixed|null     asociativni pole s adresou / null, pokud v cookie adresa neni ulozena.
   */
  public function loadAdressData() {
    if($this->cookie->isCookieSet(self::COOKIE_KEY_ADRESS)) {
      return json_decode($_COOKIE[self::COOKIE_KEY_ADRESS], true);
    }
    return null;
  }

  /**
   * Metoda odstrani z daneho klice+indexu cookie s ulozenym UFO.
   * Ostatni cookies maji zmeneny index, aby v seznamu cookies nevznikla "dira".
   * @param int $index    index UFO v seznamu cookies
   * @return void
   */
  public function deleteUFOData(int $index) {
    $count = $this->getSavedCount();

    for($i = 0; $i < $count; $i++) {  // projdeme vsechny nastavene cookie
      if($i == $index) {
        $this->cookie->unsetCookie(self::COOKIE_KEY_UFO."$index");
        continue;
      }

      $temp = $this->cookie->readCookie(self::COOKIE_KEY_UFO."$i");
      $this->cookie->unsetCookie(self::COOKIE_KEY_UFO."$i");      // odstranime cookie na indexu

      if($i < $index) {
        $this->cookie->setCookie(self::COOKIE_KEY_UFO."$i", $temp);   // cookies pred indexem se nemusi posunout doleva
      }
      if($i > $index) {
        $newIndex = $i - 1;
        $this->cookie->setCookie(self::COOKIE_KEY_UFO."$newIndex", $temp);  // cookies po indexu se posunou doleva
      }

    }
  }

  /**
   * Metoda z cookie na danem indexu nacte cislo modelu UFO.
   * @param int $index      index cookie s UFO
   * @return mixed|null     cislo modelu UFO / null, pokud v cookie UFO neni
   */
  public function getModel(int $index) {
    $data = $this->loadUFOData($index);

    if($data != null) {
      if(array_key_exists(self::KEY_MODEL, $data)) {
        return $data[self::KEY_MODEL];
      }
    }
    return null;
  }

  /**
   * Metoda z cookie na danem indexu nacte pocet dni vypujcky.
   * @param int $index      index cookie s UFO
   * @return mixed|null     pocet dni vypujcky / null, pokud v cookie UFO neni
   */
  public function getDays(int $index) {
    $data = $this->loadUFOData($index);

    if($data != null) {
      if(array_key_exists(self::KEY_DAYS, $data)) {
        return $data[self::KEY_DAYS];
      }
    }
    return null;
  }

  /**
   * Metoda z cookie na danem indexu nacte celkovou cenu vypujcky.
   * @param int $index      index cookie s UFO
   * @return mixed|null     celkova cena vypujcky / null, pokud v cookie UFO neni
   */
  public function getPrice(int $index) {
    $data = $this->loadUFOData($index);

    if($data != null) {
      if(array_key_exists(self::KEY_PRICE, $data)) {
        return $data[self::KEY_PRICE];
      }
    }
    return 0;
  }

  /**
   * Metoda zjisti, kolik UFO je celkem v kosiku ulozeno.
   * @return int      pocet UFO v kosiku.
   */
  public function getSavedCount():int {
    $i = 0;

    while($this->cookie->isCookieSet(self::COOKIE_KEY_UFO."$i")) {
      $i++;
    }

    return $i;
  }

  /**
   * Metoda vrati vsechna cookies s ulozenymi UFO v kosiku.
   * @return array|int[]    pole s UFO ulozenymi v kosiku
   */
  public function getAllSavedUFOs():array {
    if($this->getSavedCount() == 0) {
      return [];
    }

    $data = [$this->getSavedCount()];
    for($i = 0; $i < $this->getSavedCount(); $i++) {
      $data[$i] = $this->cookie->readCookie(self::COOKIE_KEY_UFO."$i");
    }

    return $data;
  }

  /**
   * Metoda zjisti celkovou cenu za vsechny UFO v kosiku za zvoleny pocet dni.
   * @return int    celkova cena objednavky
   */
  public function getTotalPrice():int {
    $sum = 0;
    $count = $this->getSavedCount();

    for($i = 0; $i < $count; $i++) {
      $sum += $this->getPrice($i);
    }

    return $sum;
  }

  /**
   * Metoda odstrani vsechny cookies souvisejici s nakupem (UFO v kosiku, adresa).
   * @return void
   */
  public function removeAllHireCookies() {
    $UFOCount = $this->getSavedCount();

    for($i = 0; $i < $UFOCount; $i++) {
      $this->cookie->unsetCookie(self::COOKIE_KEY_UFO."$i");
    }

    $this->cookie->unsetCookie(self::COOKIE_KEY_ADRESS);
  }
}
