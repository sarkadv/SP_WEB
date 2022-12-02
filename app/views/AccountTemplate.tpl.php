<?php

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - informace o uctu -->
  <div class="row" id="account-main-content">
    <h2 class="account-main-heading">
      Můj Účet
    </h2>

    <?php
    if($templateData["user_logged"]) {
      ?>

      <!-- Pro prihlasene uzivatele -->
      <div class="row">
        <div class="col-12">
          <div class="account-main-heading">
            <h5>
              Osobní údaje
            </h5>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b>E-mail:</b> <?php echo $templateData["email"]; ?>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b>Jméno a příjmení:</b> <?php echo $templateData["name"]; ?> <?php echo $templateData["surname"]; ?>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b>Datum narození:</b> <?php echo $templateData["birthday"]; ?>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
            <b>Telefonní číslo:</b> <?php echo $templateData["phone"]; ?>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-12">
            <b>
              Adresa
            </b>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div>
            <?php echo $templateData["street"]; ?>
          </div>
          <div>
            <?php echo $templateData["city"].", ".$templateData["zip"]; ?>
          </div>
          <div>
            <?php echo $templateData["planet"]; ?>
          </div>
        </div>
      </div>

      <form action="index.php?page=change_info" method="post">
        <button type="submit" class="account-main-content-form-submit-btn" name="info" value="modify">
          Změnit osobní údaje
        </button>
      </form>

      <div class="row">
        <div class="col-12">
          <div class="account-main-heading">
            <h5>
              Výpůjčky
            </h5>
          </div>
        </div>
      </div>

    <?php
      $hireInfos = $templateData["hires"];

      if(count($hireInfos) > 0) {
        foreach($hireInfos as $hire) {
          $modelName = $hire["model_name"];
          $dates = $hire["dates"];
          $price = $hire["price"];
          $days = $hire["days"];
    ?>

    <div class="jumbotron">
      <div class="row order-main-item">
        <div class="col-sm-4">
          <div class="order-main-item-name">
            <?php echo $modelName; ?>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="order-main-item-days">
            Dny: <?php echo $days. " (".$dates.")"; ?>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="order-main-item-price">
            <?php echo $price; ?> kreditů
          </div>
        </div>
      </div>
    </div>

    <?php
        }
      }
      else {
    ?>

        <div>
          Zatím nemáte žádné výpůjčky.
        </div>

      <?php
        }
      ?>

      <div class="row">
        <div class="col-12">
          <div class="account-main-heading">
            <h5>
              Recenze
            </h5>
          </div>
        </div>
      </div>

        <?php
        $reviews = $templateData["reviews"];

        if(count($reviews) > 0) {
        ?>
        <div class="row">
        <div class="col-12">
        <table class="table table-hover table-striped">
          <thead class="table-danger">
          <tr>
            <th>Model</th>
            <th>Datum a čas</th>
            <th>Hodnocení</th>
            <th>Text recenze</th>
            <th> </th>
            <th> </th>
          </tr>
          </thead>
          <tbody>

          <?php
          $count = 0;
          foreach($reviews as $review) {
            $modelName = $review["model_name"];
            $modelNumber = $review["model_number"];
            $datetime = $review["datetime"];
            $rating = $review["rating"];
            $text = $review["text"];

            if($text == null) {
              $text = "";
            }
            ?>
            <tr>
              <td><?php echo $modelName; ?></td>
              <td><?php echo $datetime; ?></td>
              <td>
                <?php
                for($i = 0; $i < $rating; $i++) {
                  ?>
                  <i class="fas fa-star account-main-table-stars"></i>
                  <?php
                }
                ?>
              </td>
              <td class="account-main-table-text"><?php echo $text; ?></td>
              <td class="account-main-table-text">
                <!-- Rozbalovaci formular pro upravu recenze -->
                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#review-form-<?php echo $count; ?>" onclick="collapse(<?php echo $count; ?>)">
                  Upravit
                </button>
              </td>
              <td class="account-main-table-text">
                <!-- Formular pro odstraneni recenze -->
                <form action="" method="post">
                  <input type="hidden" name="model-number" value="<?php echo $modelNumber; ?>">
                  <button type="submit" name="review" value="delete" class="btn btn-danger">
                    Vymazat
                  </button>
                </form>
              </td>
            </tr>
            <?php
            $count++;
          }
          ?>
          </tbody>
        </table>

        <?php
          $count = 0;
          foreach ($reviews as $review) {
            $modelNumber = $review["model_number"];
        ?>

            <form action="" method="post" id="review-form-<?php echo $count; ?>" class="account-main-content-form collapse">
              <label for="range-rating" class="form-label">Celkové hodnocení</label>
              <output id="star-rating-<?php echo $count; ?>">
                <?php echo $review["rating"];?>
              </output>
              <i class="fas fa-star"></i>
              <input type="range" id="range-rating" name="rating" class="form-range" value="<?php echo $review["rating"];?>" min="1" max="5" oninput="document.getElementById('star-rating-<?php echo $count; ?>').value = this.value">

              <label for="review-text">Zde napište text recenze:</label>
              <textarea class="form-control" rows="5" id="review-text" name="text"><?php echo $review["text"];?></textarea>

              <input type="hidden" name="model" value="<?php echo $modelNumber; ?>">

              <button type="submit" class="account-main-content-form-submit-btn" name="review" value="modify">
                Upravit recenzi
              </button>
            </form>

        <?php
          $count++;
          }
        ?>

      </div>
    </div>

      <?php
      }
      else {
      ?>
      <div>
        Zatím jste nenapsali žádné recenze.
      </div>

      <?php
        }
      }
      else {
      ?>
      <!-- Pro neprihlasene uzivatele -->
      <div class="row">
        <div class="col-12 account-main-heading">
          <p>
            Pro zobrazení detailů o Vašem účtu se musíte nejprve přihlásit.
          </p>
        </div>
      </div>

      <?php
    }
    ?>

  </div>

<?php
$templateBasics->getFooter();
?>

<script>
  function collapse(formNumber){
    let count = <?php echo count($templateData["reviews"]);?>;
    let form = "review-form-";

    for(let i = 0; i < count; i++) {
      document.getElementById(form.concat(i)).classList.remove("show");
    }

    document.getElementById(form.concat(formNumber)).classList.add("show");
  }
</script>



