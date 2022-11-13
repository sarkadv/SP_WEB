<?php

class HireUFO
{
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

  // poradi UFA v kosiku
  private const KEY_INDEX = "index";

  public function __construct() {
    require_once "CookiesClass.php";

    $this->cookie = new Cookies();
  }

  public function isUFOSaved(int $index):bool {
    return $this->cookie->isCookieSet(self::COOKIE_KEY_UFO."$index");
  }

  public function loadUFOData(int $index) {
    if($this->isUFOSaved($index)) {
      return json_decode($_COOKIE[self::COOKIE_KEY_UFO."$index"], true);
    }
    return null;
  }

  /*
   * Nastavi zakladni informace o vypujcce pri pridani UFO do kosiku.
   */
  public function saveUFOData(int $model, int $days, int $price) {
    $index = $this->getSavedCount();
    $data = [self::KEY_MODEL => $model, self::KEY_DAYS => $days, self::KEY_PRICE => $price, self::KEY_INDEX => $index];
    $this->cookie->setCookie(self::COOKIE_KEY_UFO."$index", json_encode($data));
  }

  /*
   * Nastavi adresu vypujcky pri objednavani.
   */
  public function saveAdressData(string $city, string $zip, string $street, string $planet) {
    $data = [self::KEY_CITY => $city, self::KEY_ZIP => $zip, self::KEY_STREET => $street, self::KEY_PLANET => $planet];
    $this->cookie->setCookie(self::COOKIE_KEY_ADRESS, json_encode($data));
  }

  public function loadAdressData() {
    if($this->cookie->isCookieSet(self::COOKIE_KEY_ADRESS)) {
      return json_decode($_COOKIE[self::COOKIE_KEY_ADRESS], true);
    }
    return null;
  }

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

  public function getModel(int $index) {
    $data = $this->loadUFOData($index);

    if($data != null) {
      if(array_key_exists(self::KEY_MODEL, $data)) {
        return $data[self::KEY_MODEL];
      }
    }
    return null;
  }

  public function getDays(int $index) {
    $data = $this->loadUFOData($index);

    if($data != null) {
      if(array_key_exists(self::KEY_DAYS, $data)) {
        return $data[self::KEY_DAYS];
      }
    }
    return null;
  }

  public function getPrice(int $index) {
    $data = $this->loadUFOData($index);

    if($data != null) {
      if(array_key_exists(self::KEY_PRICE, $data)) {
        return $data[self::KEY_PRICE];
      }
    }
    return 0;
  }

  public function getSavedCount():int {
    $i = 0;

    while($this->cookie->isCookieSet(self::COOKIE_KEY_UFO."$i")) {
      $i++;
    }

    return $i;
  }

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

  public function getTotalPrice() {
    $sum = 0;
    $count = $this->getSavedCount();

    for($i = 0; $i < $count; $i++) {
      $sum += $this->getPrice($i);
    }

    return $sum;
  }

  public function removeAllHireCookies() {
    $UFOCount = $this->getSavedCount();

    for($i = 0; $i < $UFOCount; $i++) {
      $this->cookie->unsetCookie(self::COOKIE_KEY_UFO."$i");
    }

    $this->cookie->unsetCookie(self::COOKIE_KEY_ADRESS);
  }
}
