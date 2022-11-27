<?php

class Cookies
{
  private $expirationS;

  public function __construct($expiration = 3 * 24 * 60 * 60) {
    $this->expirationS = $expiration;
  }

  public function setCookie($key, $value) {
    setcookie($key, $value, time() + $this->expirationS);
  }

  public function isCookieSet($key):bool {
    return isset($_COOKIE[$key]);
  }

  public function unsetCookie($key) {
    setcookie($key, null, 0);
  }

  public function readCookie($key) {
    if($this->isCookieSet($key)) {
      return $_COOKIE[$key];
    }
    return null;
  }
}
