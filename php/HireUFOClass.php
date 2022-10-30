<?php

class HireUFO
{
  private Cookies $cookie;

  private const COOKIE_KEY = "ufo";
  private const KEY_MODEL = "model";
  private const KEY_DAYS = "days";
  private const KEY_PRICE = "price";

  public function __construct() {
    require_once "CookiesClass.php";

    $this->cookie = new Cookies();
  }

  public function isUFOSaved():bool {
    return $this->cookie->isCookieSet(self::COOKIE_KEY);
  }

  public function saveUFOData(string $model, int $days, int $price) {
    $data = [self::KEY_MODEL => $model, self::KEY_DAYS => $days, self::KEY_PRICE => $price];
    $this->cookie->setCookie(self::COOKIE_KEY, json_encode($data));
  }

  public function loadUFOData() {
    if($this->isUFOSaved()) {
      return json_decode($_COOKIE[self::COOKIE_KEY], true);
    }
    return null;
  }

  public function deleteUFOData() {
    $this->cookie->unsetCookie(self::COOKIE_KEY);
  }

  public function getModel() {
    $data = $this->loadUFOData();

    if($data != null) {
      if(array_key_exists(self::KEY_MODEL, $data)) {
        return $data[self::KEY_MODEL];
      }
    }
    return null;
  }

  public function getDays() {
    $data = $this->loadUFOData();

    if($data != null) {
      if(array_key_exists(self::KEY_DAYS, $data)) {
        return $data[self::KEY_DAYS];
      }
    }
    return null;
  }

  public function getPrice() {
    $data = $this->loadUFOData();

    if($data != null) {
      if(array_key_exists(self::KEY_PRICE, $data)) {
        return $data[self::KEY_PRICE];
      }
    }
    return null;
  }
}
