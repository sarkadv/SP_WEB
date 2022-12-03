<?php
// sablona pro stranku Objednavka

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - obsah kosiku -->
  <div class="row" id="order-main-content">

    <?php
    if(isset($templateData["hires"])) {   // vypujcka byla uspesne realizovana
      $hireInfos = $templateData["hires"];
      ?>

      <h2 class="order-main-heading">
        Děkujeme za objednávku!
      </h2>

      <div class="row">
        <div class="col-12">
          <h6>
            Vybraná vozidla budou ihned poslána na Vaši adresu.
          </h6>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-12">
          <div class="order-main-heading">
            <h5>
              Shrnutí objednávky
            </h5>
          </div>
        </div>
      </div>

      <?php

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
      ?>

      <div class="row">
        <div class="col-12">
          <div class="order-main-heading">
            Celkem: <?php echo $templateData["total_price"]; ?> kreditů
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="order-main-heading">
            <h5>
              Doručovací adresa
            </h5>
          </div>
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

      <div class="row">
        <div class="col-12">
          <div class="order-main-heading">
            <h5>
              Číslo účtu
            </h5>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div>
            <?php echo $templateData["account"]; ?>
          </div>
        </div>
      </div>

      <?php
    }
    else {
      ?>

      <h3 class="order-main-info">
        Při objednávce došlo k chybě.
      </h3>

      <?php
    }
    ?>

  </div>

<?php

$templateBasics->getFooter();


