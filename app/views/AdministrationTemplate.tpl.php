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
                <th> </th>
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
                      <button type="submit" class="btn btn-danger administration-main-form-btn" name="user" value="delete">
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

                  <?php
                  if($roleNumber > 2) {  // jde povysit jen uzivatele s roli zakaznik (3)
                    ?>

                    <td>
                      <form action="" method="post">
                        <input type="hidden" name="user-number" value="<?php echo $userNumber; ?>">
                        <button type="submit" class="btn btn-info administration-main-form-btn" name="user" value="promote">
                          Povýšit
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

                  <?php
                  if($roleNumber == 2) {  // jde zbavit funkce jen uzivatele s roli spravce (2)
                    ?>

                    <td>
                      <form action="" method="post">
                        <input type="hidden" name="user-number" value="<?php echo $userNumber; ?>">
                        <button type="submit" class="btn btn-dark administration-main-form-btn" name="user" value="demote">
                          Zbavit funkce
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






