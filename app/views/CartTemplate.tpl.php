<?php
// sablona pro stranku Kosik

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - obsah kosiku -->
  <div class="row" id="cart-main-content">
    <h2 class="cart-main-heading">
      Košík
    </h2>

    <?php
    $allUFOsInfo = $templateData["ufos_info"];

    if(count($allUFOsInfo) > 0) {   // kosik neni prazdny - obsahuje alespon jednu polozku
      $i = 0;

      foreach ($allUFOsInfo as $UFO) {
        $modelName = $UFO["name"];
        $days = $UFO["days"];
        $price = $UFO["price"];
        echo "
            <div class='jumbotron'>
              <div class='row cart-main-item'>
                <div class='col-sm-4'>
                  <div class='cart-main-item-name'>
                    $modelName
                  </div>
                </div>
                <div class='col-sm-4'>
                  <div class='cart-main-item-days'>
                    Dny: $days
                  </div>
                </div>
                <div class='col-sm-3'>
                  <div class='cart-main-item-price'>
                    $price kreditů
                  </div>
                </div>
                <div class='col-sm-1'>
                  <form method='post'>
                    <div class='cart-main-item-remove'>
                      <button type='submit' class='cart-main-item-remove-btn' name='remove' value=$i>
                        <i class='fas fa-times-circle'></i>
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          ";

        $i++;
      }

      $totalPrice = $templateData["total_price"];
      echo "
          <div class='row'>
            <div class='col-12'>
                <div class='cart-main-price'>
                    Celkem: $totalPrice kreditů
                </div>
            </div>
          </div>
        ";
    }
    else {  // kosik je prazdny
      echo '
          <div class="row">
            <div class="col-12">
              Košík je prázdný.
            </div>
          </div>
        ';
    }

    if($templateData["user_logged"] && count($allUFOsInfo) > 0) {

      ?>

      <!-- Pro prihlasene uzivatele kdyz kosik neni prazdny -->
      <div class="row">
        <div class="col-12">
          <button type="button" class="cart-main-continue-btn" onclick="location.href='index.php?page=shipping'">
            <i class="fas fa-angle-double-right"></i>
            Pokračovat
          </button>
        </div>
      </div>

      <?php
    }
    else if (!$templateData["user_logged"]){
      ?>

      <!-- Pro neprihlasene uzivatele -->
      <div class="row">
        <div class="col-12">
          <div class="cart-main-info">
            Pro pokračování v nákupu se nejdříve musíte
            <a href="" data-bs-toggle="offcanvas" data-bs-target="#demo-sidebar">
              přihlásit
            </a>
          </div>
        </div>
      </div>

      <?php
    }
    ?>

    <div class="row cart-main-progress">
      <div class="col-12">
        <div class="progress">
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary" style="width:33.3%"></div>
        </div>
      </div>
    </div>
  </div>

<?php

$templateBasics->getFooter();


