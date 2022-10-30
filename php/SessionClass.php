<?php

class Session
{
  public function __construct() {
    session_start();
  }

  public function setSession(string $key, $data) {
    $_SESSION[$key] = $data;
  }

  public function unsetSession(string $key) {
    unset($_SESSION[$key]);
  }

  public function isSessionSet(string $key):bool {
    return isset($_SESSION[$key]);
  }

  public function removeAllSessions(){
    session_unset();
  }

}
