<?php

/**
 * Trida ma za ukol zpracovavat cookies - vytvoret je, cist, mazat.
 */
class Cookies
{
  /**
   * @var int|float|mixed   doba v sekundach, za jak dlouho cookie expiruje
   */
  private int $expirationS;

  /**
   * Konstruktor pro vytvoreni instance tridy Cookies.
   * @param int $expiration     doba v sekundach, za jak dlouho cookie expiruje (defaultne 3 dny)
   */
  public function __construct(int $expiration = 3 * 24 * 60 * 60) {
    $this->expirationS = $expiration;
  }

  /**
   * Metoda nastavi nove cookie.
   * @param mixed $key      klic, pod kterym se bude cookie ukladat
   * @param mixed $value    hodnota pod klicem
   * @return void
   */
  public function setCookie($key, $value) {
    setcookie($key, $value, time() + $this->expirationS);
  }

  /**
   * Metoda zjisti, zda je pod klicem nastaveno nejake cookie.
   * @param mixed $key      klic k prohledani
   * @return bool           true - pod klicem se nachazi cookie / false jinak
   */
  public function isCookieSet($key):bool {
    return isset($_COOKIE[$key]);
  }

  /**
   * Metoda odstrani cookie pod danym klicem.
   * @param mixed $key    klic cookie, ktere se ma odstranit
   * @return void
   */
  public function unsetCookie($key) {
    setcookie($key, null, 0);
  }

  /**
   * Metoda vrati hodnotu cookie pod danym klicem.
   * @param mixed $key      klic k prohledani
   * @return mixed|null     hodnota cookie / null, pokud cookie pod danym klicem neexistuje
   */
  public function readCookie($key) {
    if($this->isCookieSet($key)) {
      return $_COOKIE[$key];
    }
    return null;
  }
}
