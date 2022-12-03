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
<div class="row" id="management-main-content">
  <h2 class="management-main-heading">
    Správa webu
  </h2>

  <?php
    if($templateData["user_logged"] && $templateData["user_role"] <= 2) {   // jen pro prihlasene spravce + adminy
  ?>

    <div class="row">
      <div class="col-12">
        <div class="management-main-heading">
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
                <th>Uživatel</th>
                <th>Datum</th>
                <th>Hodnocení</th>
                <th>Text</th>
                <th> </th>
              </tr>
              </thead>
              <tbody>

        <?php
        foreach($reviews as $review) {
          $email = $review["email"];
          $datetime = $review["datetime"];
          $rating = $review["rating"];
          $text = $review["text"];
          $model = $review["model"];
          $reviewNumber = $review["review_number"];
        ?>

            <tr>
              <td class="home-main-table-text"><?php echo $model; ?></td>
              <td class="home-main-table-text"><?php echo $email; ?></td>
              <td class="home-main-table-text"><?php echo $datetime; ?></td>
              <td>
                <?php
                for($i = 0; $i < $rating; $i++) {
                  ?>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <?php
                }
                ?>
              </td>
              <td class="home-main-table-text"><?php echo $text; ?></td>
              <td>
                <form action="" method="post">
                  <input type="hidden" name="review-number" value="<?php echo $reviewNumber; ?>">
                  <button type="submit" class="btn btn-danger" name="review" value="delete">
                    Vymazat
                  </button>
                </form>
              </td>
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
        <div class="management-main-heading">
          <h5>
            Vložit nový model
          </h5>
        </div>
      </div>
    </div>

    <form action="" method="post" enctype="multipart/form-data">
       <div class="row">
         <div class="col-md-6 management-form-main-item">
           <label for="model-name" class="form-label">Název</label>
           <div class="input-group">
             <div class="input-group-text">
               <i class="fas fa-rocket"></i>
             </div>
             <input type="text" class="form-control" id="model-name" placeholder="The Cubicle" name="model-name" maxlength="40" required>
           </div>
         </div>
         <div class="col-md-6 management-form-main-item">
           <label for="model-price" class="form-label">Cena / den</label>
           <div class="input-group">
             <div class="input-group-text">
               <i class="fas fa-gem"></i>
             </div>
             <input type="number" class="form-control" id="model-price" min=1 placeholder="30000" name="model-price" step="1" required>
           </div>
         </div>
       </div>

      <div class="row">
        <div class="col-12 management-form-main-item">
          <label for="model-desc-short" class="form-label">Krátký popisek</label>
          <div class="input-group">
            <div class="input-group-text">
              <i class="fas fa-keyboard"></i>
            </div>
            <input type="text" class="form-control" id="model-desc-short" placeholder="Krátký popis modelu." name="model-desc-short" maxlength="100" required>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12 management-form-main-item">
          <label for="model-desc-long" class="form-label">Dlouhý popis</label>
          <textarea class="form-control" rows="5" id="model-desc-long" name="model-desc-long"></textarea>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 management-form-main-item">
          <label for="model-people" class="form-label">Počet osob</label>
          <div class="input-group">
            <div class="input-group-text">
              <i class="fas fa-child"></i>
            </div>
            <input type="number" class="form-control" id="model-people" placeholder="4" min=1 name="model-people" step="1" required>
          </div>
        </div>
        <div class="col-md-4 management-form-main-item">
          <label for="model-battery" class="form-label">Výdrž baterie [hod]</label>
          <div class="input-group">
            <div class="input-group-text">
              <i class="fas fa-clock"></i>
            </div>
            <input type="number" class="form-control" id="model-battery" placeholder="50" min=1 name="model-battery" step="1" required>
          </div>
        </div>

        <div class="col-md-4 management-form-main-item">
          <label for="model-speed" class="form-label">Rychlost [ly]</label>
          <div class="input-group">
            <div class="input-group-text">
              <i class="fas fa-tachometer-alt"></i>
            </div>
            <input type="number" class="form-control" id="model-speed" placeholder="2000" min=1 name="model-speed" step="1" required>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12 management-form-main-item">
          <label for="model-img" class="form-label">Obrázek (PNG / JPG / JPEG / GIF)</label>
          <input type="file" class="form-control" id="model-img" name="model-img">
        </div>
      </div>

      <div class="row">
        <div class="col-12 management-form-main-item">
          <label for="model-units" class="form-label">Počet položek na skladě</label>
          <div class="input-group">
            <div class="input-group-text">
              <i class="fas fa-file"></i>
            </div>
            <input type="number" class="form-control" id="model-units" placeholder="5" min=0 name="model-units" step="1" required>
          </div>
        </div>
      </div>

      <button type="submit" name="model" value="new" id="management-main-btn-submit">Vložit nový model</button>
    </form>

  <?php
    }
    else {
    ?>
    <!-- Pro neprihlasene uzivatele nebo zakazniky -->
    <div class="row">
      <div class="col-12 management-main-heading">
        <p>
          Požadovanou stránku můžete zobrazit pouze jako správce.
        </p>
      </div>
    </div>

    <?php
  }
  ?>

</div>

<?php
$templateBasics->getFooter();





