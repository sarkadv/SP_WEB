<?php

class Login
{
  private Session $session;
  private const SESSION_KEY = "user";
  private const KEY_NAME = "name";
  private const KEY_DATE = "date";

  public function __construct() {
    require_once("SessionClass.php");
    $this->session = new Session();
  }

  public function isUserLoggedIn():bool {
    return $this->session->isSessionSet(self::SESSION_KEY);
  }

  public function login(string $userName) {
    $data = [self::KEY_NAME => $userName, self::KEY_DATE => date("d. m. Y, G:i:s")];
    $this->session->setSession(self::SESSION_KEY, $data);
  }

  public function logout() {
    $this->session->unsetSession(self::SESSION_KEY);
  }
}

?>
