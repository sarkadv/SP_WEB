<?php

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - sprava recenzi a produktu -->
  <div class="row" id="administration-main-content">
    <h2 class="administration-main-heading">
      Administrace webu
    </h2>

    <?php
    if($templateData["user_logged"] && $templateData["user_role"] <= 1) {   // jen pro prihlasene adminy
      ?>

      <div class="row">
        <div class="col-12">
          <div class="administration-main-heading">
            <h5>
              Uživatelé
            </h5>
          </div>
        </div>
      </div>

      <?php
      $users = $templateData["users"];
      if(count($users) > 0) {
        ?>
        <div class="row">
          <div class="col-12">
            <table class="table table-hover table-striped">
              <thead class="table-danger">
              <tr>
                <th>Role</th>
                <th>E-mail</th>
                <th>Jméno</th>
                <th>Příjmení</th>
                <th> </th>
              </tr>
              </thead>
              <tbody>

              <?php
              foreach($users as $user) {
                $userNumber = $user["number"];
                $roleNumber = $user["role_number"];
                $roleName = $user["role_name"];
                $email = $user["email"];
                $name = $user["name"];
                $surname = $user["surname"];
                ?>

                <tr>
                  <td><?php echo $roleName; ?></td>
                  <td><?php echo $email; ?></td>
                  <td><?php echo $name; ?></td>
                  <td><?php echo $surname; ?></td>

                  <?php
                    if($roleNumber != 1) {  // adminy nepujde vymazat
                  ?>

                  <td>
                    <form action="" method="post">
                      <input type="hidden" name="user-number" value="<?php echo $userNumber; ?>">
                      <button type="submit" class="btn btn-danger administration-main-delete-btn" name="user" value="delete">
                        Vymazat
                      </button>
                    </form>
                  </td>

                  <?php
                    } else {
                  ?>

                      <td></td>

                  <?php
                    }
                  ?>

                </tr>

                <?php
              }
              ?>
              </tbody>
            </table>
          </div>
        </div>

        <?php
      } else {
        ?>

        <div>
          Zatím nebyly vloženy žádné recenze.
        </div>

        <?php
      }
      ?>

      <div class="row">
        <div class="col-12">
          <div class="administration-main-heading">
            <h5>
              Vložit nového správce
            </h5>
          </div>
        </div>
      </div>

      <form action="" method="POST" oninput="pswdcheck.value=(pswd1.value === pswd2.value)?'':'Hesla se neshodují.'">
        <div class="row">
          <div class="col-12 registration-main-item">
            <label for="email" class="form-label">E-mail</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-at"></i>
              </div>
              <input type="email" oninput="" class="form-control" id="email" placeholder="ufon@gmail.com" name="email" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 registration-main-item">
            <label for="psw1" class="form-label">Heslo</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-key"></i>
              </div>
              <input type="password" class="form-control" id="pswd1" placeholder="Heslo" name="pswd1" required>
            </div>
          </div>
          <div class="col-md-6 registration-main-item">
            <label for="pswd2" class="form-label">Heslo podruhé</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-lock"></i>
              </div>
              <input type="password" class="form-control" id="pswd2" placeholder="Heslo podruhé" name="pswd2" required>
            </div>
          </div>
        </div>

        <div class="row">
          <output class="registration-main-check" name="pswdcheck" for="pswd1 pswd2"></output>
        </div>

        <h4 class="registration-main-heading-secondary">
          Osobní údaje
        </h4>
        <div class="row">
          <div class="col-md-6 registration-main-item">
            <label for="first-name" class="form-label">Jméno</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-user-circle"></i>
              </div>
              <input type="text" class="form-control" id="first-name" placeholder="Jméno" name="first-name" required>
            </div>
          </div>
          <div class="col-md-6 registration-main-item">
            <label for="last-name" class="form-label">Příjmení</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="far fa-user-circle"></i>
              </div>
              <input type="text" class="form-control" id="last-name" placeholder="Příjmení" name="last-name" required>
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
              <input type="date" class="form-control" id="birth-date" placeholder="Datum narození" name="birth-date" required>
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
              <input type="tel" class="form-control" id="phone" placeholder="628 267 170" name="phone" required>
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
              <input type="text" class="form-control" id="street" placeholder="Mimozemská 108" name="street" required>
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
              <input type="text" class="form-control" id="city" placeholder="Město" name="city" required>
            </div>
          </div>
          <div class="col-md-4 registration-main-item">
            <label for="zip-code" class="form-label">Poštovní směrovací číslo</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="far fa-envelope-open"></i>
              </div>
              <input type="text" class="form-control" id="zip-code" placeholder="123 45" name="zip-code" required>
            </div>
          </div>
          <div class="col-md-4 registration-main-item">
            <label for="planet" class="form-label">Domovská planeta</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-globe"></i>
              </div>
              <input class="form-control" id="planet" list ="planets" placeholder="Domovská planeta" name="planet" required>
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
        <button type="submit" name="user" value="new" id="registration-main-btn-submit">Vložit nového správce</button>
      </form>

      <?php
    }
    else {
      ?>
      <!-- Pro neprihlasene uzivatele nebo zakazniky -->
      <div class="row">
        <div class="col-12 management-main-heading">
          <p>
            Požadovaná stránka neexistuje.
          </p>
        </div>
      </div>

      <?php
    }
    ?>

  </div>

<?php
$templateBasics->getFooter();






