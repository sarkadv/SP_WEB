<?php

interface IController
{
  /**
   * Metoda, kterou musi mit kazdy kontroler.
   * Pro kazdou stranku webu vytvori podle sablony jeji HTML kod, ktery vrati.
   * @param string $title      nazev stranky
   * @return string     HTML kod stranky
   */
  public function show(string $title):string;
}
