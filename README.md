# Půjčovna UFO Andromeda

Web půjčovny UFO. Funguje stejně jako půjčovna aut, jen si zákazníci místo aut půjčují mimozemská vozidla.
Napsáno v PHP s architekturou MVC. Semestrální práce z předmětu KIV/WEB.

## Role uživatelů

- **Nepřihlášený uživatel** si může prohlížet nabídku modelů UFO a jejich recenze.
- **Zákazník** si navíc může vozidlo vypůjčit (objednat), psát recenze a vidět historii svých objednávek a
  recenzí.
- **Správce** může vkládat nové modely UFO a spravovat recenze.
- **Administrátor** spravuje uživatele a správce.

## Použité technologie

- **PHP** (architektura MVC, komunikace s databází přes PDO)
- **MySQL**
- **HTML5, CSS, Bootstrap 5** (responzivní design), ikony Font Awesome
- **JavaScript** jen místy, např. přiblížení obrázku po najetí myší

Web je zabezpečený proti útokům XSS a SQL injection a hesla uživatelů jsou v databázi zašifrovaná.

## Architektura

- `index.php` je vstupní bod aplikace. Načte nastavení ze `settings.inc.php` a zavolá metodu `start()` třídy
  `AppStart`.
- `app/AppStart.class.php` podle zvolené stránky vytvoří příslušný kontroler a vypíše HTML kód, který vrátí
  jeho metoda `show()`. Pokud stránka neexistuje, zobrazí se úvodní stránka.
- `app/controllers` obsahuje kontrolery stránek. Každý získá data z modelu, předá je šabloně a vrátí její
  výstup. Třídy `Cookies` a `Session` slouží k práci s cookies a session, třída `HireUFO` ukládá obsah košíku
  a doručovací adresu do cookies během objednávky.
- `app/models/DatabaseConnection.class.php` je model, který komunikuje s databází přes PDO.
- `app/views` obsahuje šablony stránek. Třída `TemplateBasics` vytváří hlavičku, menu a patičku společné
  pro všechny stránky.
