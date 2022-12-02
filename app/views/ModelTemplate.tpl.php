<?php

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - obrazek UFO a zakladni informace -->
  <?php
  $UFOModel = $templateData["ufo"];

  if($UFOModel == null) {
    ?>
    <!-- Neexistuje UFO model s pozadovanym cislem -->
    <div class="row" id="model-main-content">
      <h2 class="model-main-heading">
        Požadovaná stránka neexistuje.
      </h2>
    </div>

    <?php
  }
  else {
    $modelName = $UFOModel["nazev"];
    $pictureUrl = $UFOModel["obrazek_url"];
    $people = $UFOModel["pocet_osob"];
    $battery = $UFOModel["vydrz_baterie"];
    $speed = $UFOModel["rychlost_ly"];
    $price = $UFOModel["cena_den"];
    $modelNumber = $UFOModel["c_modelu_pk"];
    $shortDesc = $UFOModel["popis_kratky"];
    $longDesc = $UFOModel["popis_dlouhy"];
    ?>

  <!-- Existuje UFO model s pozadovanym cislem -->
  <div class="row" id="model-main-content">
    <h2 class="model-main-heading">
      <?php echo $modelName; ?>
    </h2>
    <div class="col-lg-8">
      <div class="zoom" onmousemove="zoom(event)" style="background-image: url('<?php echo $pictureUrl; ?>')">
        <img src="<?php echo $pictureUrl; ?>" class="model-main-img" alt="Obrázek modelu">
      </div>
    </div>
    <div class="col-lg-4">
      <ul class="list-group model-main-content-list">
        <li class="list-group-item list-group-item-action">
          <span class="model-main-content-list-key">
            Počet osob
          </span>
          <span class="model-main-content-list-value">
            <?php echo $people; ?>
          </span>
        </li>
        <li class="list-group-item list-group-item-action">
          <span class="model-main-content-list-key">
            Výdrž baterie
          </span>
          <span class="model-main-content-list-value">
            <?php echo $battery; ?> hodin
          </span>
        </li>
        <li class="list-group-item list-group-item-action">
          <span class="model-main-content-list-key">
            Maximální rychlost
          </span>
          <span class="model-main-content-list-value">
            <?php echo $speed; ?> ly / hod
          </span>
        </li>
      </ul>
      <div class="model-main-content-price">
        <?php echo $price; ?> kreditů / den
      </div>

      <!-- Formular pro vypujceni vozidla -->
      <?php
      if($templateData["available"] > 0) {
        ?>
        <!-- Model neni vyprodany -->
        <form method="post">
          <label for="days" class="form-label">Počet dnů:</label>
          <input type="number" class="form-control" id="days" placeholder="1" min="1" max="14" name="days" required>
          <button type="submit" class="products-main-btn-product" name="hire" value="<?php echo $modelNumber; ?>">
            <i class="fas fa-shopping-basket"></i>
            Vypůjčit
          </button>
        </form>

        <?php
      }
      else {
        ?>

        <!-- Model je vyprodany -->
        <p class="model-main-info">Vyprodáno.</p>

        <?php
      }
      ?>

    </div>
  </div>

  <!-- Stred stranky - informace o modelu a recenze -->
  <div class="row" id="model-other-content">
    <div id="accordion">

      <div class="card">
        <div class="card-header">
          <a class="btn" data-bs-toggle="collapse" href="#collapseOne">
            <h4>
              Další informace o modelu
            </h4>
          </a>
        </div>
        <div id="collapseOne" class="collapse show" data-bs-parent="#accordion">
          <div class="card-body">
            <h5>
              <?php echo $shortDesc; ?>
            </h5>
            <p>
              <?php echo $longDesc; ?>
            </p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwo">
            <h4>
              Všechny recenze
            </h4>
          </a>
        </div>
        <div id="collapseTwo" class="collapse" data-bs-parent="#accordion">
          <div class="card-body">

            <!-- Rozbalovaci formular pro napsani nove recenze -->
            <button type="button" class="btn btn-secondary model-main-content-form-collapse-btn" data-bs-toggle="collapse" data-bs-target="#review-form">
              Napsat recenzi
            </button>

            <?php
            if($templateData["user_logged"] && $templateData["can_review"]) {   // uzivatel je prihlaseny a model si nekdy vypujcil
              ?>
              <!-- Prihlaseny uzivatel vidi formular pro recenzi -->
              <form action="" method="post" id="review-form" class="model-main-content-form collapse">
                <label for="range-rating" class="form-label">Celkové hodnocení</label>
                <output id="star-rating">
                  5
                </output>
                <i class="fas fa-star"></i>
                <input type="range" id="range-rating" name="rating" class="form-range" value="5" min="1" max="5" oninput="document.getElementById('star-rating').value = this.value">

                <label for="review-text">Zde napište text recenze:</label>
                <textarea class="form-control" rows="5" id="review-text" name="text"></textarea>

                <button type="submit" class="model-main-content-form-submit-btn" name="review" value="create">
                  Potvrdit recenzi
                </button>
              </form>

              <?php
                if($templateData["rewrite"]) {    // uzivatel uz recenzi na tento model napsal
              ?>

                <div>
                  Na tento model už jste recenzi napsali. Napsáním nové recenze tu starou přepíšete.
                </div>

              <?php
                }
              }
              else if ($templateData["user_logged"] && !$templateData["can_review"]) {  // uzivatel si model jeste nevypujcil
              ?>

                <div>
                  Tento model jste si nikdy nevypůjčili, a nemůžete na něj tedy napsat recenzi.
                </div>

              <?php
              }
              else {  // uzivatel neni prihlaseny
              ?>

              <!-- Pro neprihlasene uzivatele - nemohou napsat recenzi-->
              <div>
                Pro napsání recenze se nejdříve musíte
                <a href="" data-bs-toggle="offcanvas" data-bs-target="#demo-sidebar">
                  přihlásit
                </a>
              </div>

            <?php
            }
            ?>

            <hr>
            <?php
            $reviews = $templateData["reviews"];
            $count = 0;
            $averageRating = 0;

            if(count($reviews) > 0) {

              foreach($reviews as $review) {
                $averageRating += $review["rating"];
                $count++;
              }

              $averageRating /= $count;
            }

            if(count($reviews) > 0) {
              ?>
              <p class="model-main-content-rating">
                Průměrné hodnocení:
                <?php echo number_format((float)$averageRating, 1, '.', ''); ?>
                / 5
                <i class="fas fa-star"></i>
              </p>
              <table class="table table-hover table-striped">
                <thead class="table-danger">
                <tr>
                  <th>Uživatel</th>
                  <th>Datum a čas</th>
                  <th>Hodnocení</th>
                  <th>Text recenze</th>
                </tr>
                </thead>
                <tbody>

                <?php
                foreach($reviews as $review) {
                  $firstName = $review["username"];
                  $datetime = $review["datetime"];
                  $rating = $review["rating"];
                  $text = $review["text"];

                  if($text == null) {
                    $text = "";
                  }
                  ?>
                  <tr>
                    <td><?php echo $firstName; ?></td>
                    <td><?php echo $datetime; ?></td>
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
                  </tr>
                  <?php
                }
                ?>
                </tbody>
              </table>

              <?php
            }
            else {
              ?>
              <p>
                Pro tento model ještě nebyly napsány žádné recenze.
              </p>
              <?php
            }
            ?>

          </div>
        </div>
      </div>

    </div>
  </div>

    <script>
      function zoom(e){
        let x, y, offsetX, offsetY;
        let zoomer = e.currentTarget;
        e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX;
        e.offsetY ? offsetY = e.offsetY : offsetY = e.touches[0].pageY;
        x = offsetX/zoomer.offsetWidth*100;
        y = offsetY/zoomer.offsetHeight*100;
        zoomer.style.backgroundPosition = x + '% ' + y + '%';
      }
    </script>

  <?php
}
?>

<?php

$templateBasics->getFooter();


