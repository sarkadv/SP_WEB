<?php
// sablona pro stranku Zmena osobnich udaju

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - formular pro upravu osobnich udaju - stejny jako pri registraci krome emailu -->
  <div class="row" id="registration-main-content">
    <h2 class="registration-main-heading">
      Změna osobních údajů
    </h2>

    <?php
    if($templateData["user_logged"]) {
      ?>

      <!-- Pro prihlasene uzivatele -->
      <form action="" method="POST" oninput="pswdcheck.value=(pswd1.value === pswd2.value)?'':'Hesla se neshodují.'">
        <div class="row">
          <div class="col-md-6 registration-main-item">
            <label for="psw1" class="form-label">Nové heslo</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-key"></i>
              </div>
              <input type="password" class="form-control" id="pswd1" placeholder="Heslo" name="pswd1" maxlength="60" required>
            </div>
          </div>
          <div class="col-md-6 registration-main-item">
            <label for="pswd2" class="form-label">Nové heslo podruhé</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-lock"></i>
              </div>
              <input type="password" class="form-control" id="pswd2" placeholder="Heslo podruhé" name="pswd2" maxlength="60" required>
            </div>
          </div>
        </div>

        <div class="row">
          <output class="registration-main-check" name="pswdcheck" for="pswd1 pswd2"></output>
        </div>

        <div class="row">
          <div class="col-md-6 registration-main-item">
            <label for="first-name" class="form-label">Jméno</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-user-circle"></i>
              </div>
              <input type="text" class="form-control" id="first-name" placeholder="Jméno" name="first-name" value="<?php echo $templateData["name"];?>" maxlength="50" required>
            </div>
          </div>
          <div class="col-md-6 registration-main-item">
            <label for="last-name" class="form-label">Příjmení</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="far fa-user-circle"></i>
              </div>
              <input type="text" class="form-control" id="last-name" placeholder="Příjmení" name="last-name" value="<?php echo $templateData["surname"];?>" maxlength="50" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 registration-main-item">
            <label for="birth-date" class="form-label">Datum narození</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-birthday-cake"></i>
              </div>
              <input type="date" class="form-control" id="birth-date" placeholder="Datum narození" name="birth-date" value="<?php echo $templateData["birthday"];?>" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 registration-main-item">
            <label for="phone" class="form-label">Telefonní číslo</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-phone"></i>
              </div>
              <input type="tel" class="form-control" id="phone" placeholder="628 267 170" name="phone" value="<?php echo $templateData["phone"];?>" maxlength="12" required>
            </div>
          </div>
        </div>

        <h4 class="registration-main-heading-secondary">
          Adresa
        </h4>
        <div class="row">
          <div class="col-12 registration-main-item">
            <label for="street" class="form-label">Ulice a číslo popisné</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-home"></i>
              </div>
              <input type="text" class="form-control" id="street" placeholder="Mimozemská 108" name="street" value="<?php echo $templateData["street"];?>" maxlength="50" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 registration-main-item">
            <label for="city" class="form-label">Město</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-city"></i>
              </div>
              <input type="text" class="form-control" id="city" placeholder="Město" name="city" value="<?php echo $templateData["city"];?>" maxlength="50" required>
            </div>
          </div>
          <div class="col-md-4 registration-main-item">
            <label for="zip-code" class="form-label">Poštovní směrovací číslo</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="far fa-envelope-open"></i>
              </div>
              <input type="text" class="form-control" id="zip-code" placeholder="123 45" name="zip-code" value="<?php echo $templateData["zip"];?>" maxlength="5" required>
            </div>
          </div>
          <div class="col-md-4 registration-main-item">
            <label for="planet" class="form-label">Domovská planeta</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-globe"></i>
              </div>
              <input class="form-control" id="planet" list ="planets" placeholder="Domovská planeta" name="planet" value="<?php echo $templateData["planet"];?>" maxlength="50" required>
            </div>
            <datalist id="planets">
              <option value="Kepler-452b">
              <option value="Gliese 667Cc">
              <option value="Kepler-69c">
              <option value="Země">
              <option value="Proxima Centauri b">
            </datalist>
          </div>
        </div>
        <button type="submit" name="action" value="modify" id="registration-main-btn-submit">Upravit osobní údaje</button>
      </form>

      <?php
    }
    else {
      ?>
      <!-- Pro neprihlasene uzivatele -->
      <div class="row">
        <div class="col-12 registration-main-item">
          <p>
            Pro úpravu osobních informací se nejdříve musíte přihlásit.
          </p>
        </div>
      </div>

      <?php
    }
    ?>

  </div>

<?php

$templateBasics->getFooter();



