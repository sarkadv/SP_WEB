<?php

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - formular pro placeni -->
  <div class="row" id="payment-main-content">
    <h2 class="payment-main-heading">
      Platba
    </h2>

    <?php
    if($templateData["user_logged"]){   // uzivatel je prihlaseny
      ?>

      <!-- Pro prihlasene uzivatele -->
      <div class="row">
        <div class="col-12">
          <div class="payment-main-price">
            <?php echo $templateData["total_price"]; ?> kreditů
          </div>
        </div>
      </div>

      <form action="index.php?page=order" method="post">
        <div class="col-12 payment-main-item">
          <label for="account" class="form-label">Číslo účtu</label>
          <div class="input-group">
            <div class="input-group-text">
              <i class="far fa-credit-card"></i>
            </div>
            <input type="number" class="form-control" id="account" placeholder="1111000011110000" name="account-number" min="0" max="1111111111111111" required>
          </div>
          <p class="payment-main-info">
            Po potvrzení platby ve Vašem chytrém zařízení budou peníze automaticky odeslány.
          </p>
        </div>
        <button type="submit" name="hire" value="payment" class="payment-main-continue-btn">
          <i class="fas fa-angle-double-right"></i>
          Objednat
        </button>
      </form>

      <?php
    }
    else {
      ?>

      <!-- Pro neprihlasene uzivatele -->
      <div class="row">
        <div class="col-12">
          <div class="shipping-main-info">
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
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary" style="width:100%"></div>
        </div>
      </div>
    </div>
  </div>

<?php

$templateBasics->getFooter();


