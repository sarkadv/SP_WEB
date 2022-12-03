<?php

/**
 * Trida ma za ukol pracovat se session, ktera trva do zavreni prohlizece.
 * Diky teto tride se uzivatel muze prihlasit / odhlasit.
 */
class Session
{
  /**
   * Konstruktor pro vytvoreni instance tridy Session.
   */
  public function __construct() {
    session_start();
  }

  /**
   * Metoda nastavi session do globalni promenne.
   * @param string $key   klic session
   * @param mixed $data   data, ktera se ulozi pod klic
   * @return void
   */
  public function setSession(string $key, $data) {
    $_SESSION[$key] = $data;
  }

  /**
   * Metoda odstrani session z globalni promenne podle klice.
   * @param string $key     klic session
   * @return void
   */
  public function unsetSession(string $key) {
    unset($_SESSION[$key]);
  }

  /**
   * Metoda zjisti, zda je pod danym klicem nastavena v globalni promenne session.
   * @param string $key     klic session
   * @return bool           true - sesssion s danym klicem je nastavena / false jinak
   */
  public function isSessionSet(string $key):bool {
    return isset($_SESSION[$key]);
  }

  /**
   * Metoda precte data pod danym klicem v globalni promenne session.
   * @param string $key     klic session
   * @return mixed|null     data session pod danym klicem / null, pokud pod klicem neni session nastavena
   */
  public function readSession(string $key) {
    if(isset($_SESSION[$key])) {
      return $_SESSION[$key];
    }
    return null;
  }

}
