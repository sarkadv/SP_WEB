<?php
// sablona pro stranku Nabidka

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - ruzna vozidla -->
  <div class="row" id="products-main-content">
    <h2 class="products-main-heading">
      Naše nabídka
    </h2>

    <?php
    $models = $templateData["models"];

    foreach($models as $model) {
      $img = $model["img"];
      $name = $model["name"];
      $description = $model["description"];
      $modelNumber = $model["model_number"];
      $input_id = "days".$modelNumber;
      $available = $model["available"];
      ?>
      <div class='col-lg-4 col-md-6 products-main-item'>
        <div class='card'>
          <div class='zoom' onmousemove='zoom(event)' style='background-image: url(<?php echo $img;?>)'>
            <img class='card-img-top' src='<?php echo $img;?>' alt='<?php echo $name;?>'>
          </div>
          <div class='card-body products-main-card'>
            <!-- Formular pro rozkliknuti stranky vozidla -->
            <form action='index.php?' method='get'>
              <button type='submit' name='page' value='model' class='products-main-name-btn'>
                <?php echo $name;?>
              </button>
              <input type="hidden" name="examine" value='<?php echo $modelNumber;?>'>
            </form>
            <p class='card-text'><?php echo $description;?></p>

            <?php
            if($available > 0) {
              ?>
              <!-- Formular pro vypujceni vozidla -->
              <form method='post'>
                <label for='<?php echo $input_id;?>' class='form-label'>Počet dnů:</label>
                <input type='number' class='form-control' id='<?php echo $input_id;?>' placeholder='1' min='1' max='14' name='days' required>
                <button type='submit' class='products-main-btn-product' name='hire' value='<?php echo $modelNumber;?>'>
                  <i class='fas fa-shopping-basket'></i>
                  Vypůjčit
                </button>
              </form>

              <?php
            }
            else {
              ?>

              <!-- Model je vyprodany -->
              <p class="products-main-info">Vyprodáno.</p>

              <?php
            }
            ?>

          </div>
        </div>
      </div>
      <?php
    }
    ?>

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

$templateBasics->getFooter();



