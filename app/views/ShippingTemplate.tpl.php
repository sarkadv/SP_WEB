<?php

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - formular pro adresu -->
  <div class="row" id="shipping-main-content">
    <h2 class="shipping-main-heading">
      Doručovací údaje
    </h2>

    <?php
    if($templateData["user_logged"]) {    // uzivatel je prihlaseny

      ?>

      <!-- Pro prihlasene uzivatele -->
      <form action="index.php?page=payment" method="post">
        <div class="row">
          <div class="col-12 shipping-main-item">
            <input type="checkbox" class="form-check-input" id="use-default" name="use-default" checked oninput="disableAdress()">
            <label for="use-default" class="form-label">Použít výchozí adresu účtu</label>
          </div>
        </div>
        <div class="row">
          <div class="col-12 shipping-main-item">
            <label for="street" class="form-label">Ulice a číslo popisné</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-home"></i>
              </div>
              <input type="text" class="form-control" id="street" placeholder="Ulice a číslo popisné" value="<?php echo $templateData["street"];?>" name="street" readonly required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 shipping-main-item">
            <label for="city" class="form-label">Město</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-city"></i>
              </div>
              <input type="text" class="form-control" id="city" placeholder="Město" value="<?php echo $templateData["city"];?>" name="city" readonly required>
            </div>
          </div>
          <div class="col-md-4 shipping-main-item">
            <label for="zip-code" class="form-label">Poštovní směrovací číslo</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="far fa-envelope-open"></i>
              </div>
              <input type="text" class="form-control" id="zip-code" placeholder="PSČ" value="<?php echo $templateData["zip"];?>" name="zip-code" readonly required>
            </div>
          </div>
          <div class="col-md-4 shipping-main-item">
            <label for="planet" class="form-label">Domovská planeta</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-globe"></i>
              </div>
              <input class="form-control" id="planet" list ="planets" placeholder="Domovská planeta" value="<?php echo $templateData["planet"];?>" name="planet" readonly required>
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
        <button type="submit" name="hire" value="adress" class="shipping-main-continue-btn">
          <i class="fas fa-angle-double-right"></i>
          Pokračovat
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
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary" style="width:66.7%"></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function disableAdress() {
      let checkbox = document.getElementById("use-default");

      let street = "<?php echo $templateData["street"];?>";
      let city = "<?php echo $templateData["city"];?>";
      let zip = "<?php echo $templateData["zip"];?>";
      let planet = "<?php echo $templateData["planet"];?>";

      document.getElementById("street").value = street;
      document.getElementById("city").value = city;
      document.getElementById("zip-code").value = zip;
      document.getElementById("planet").value = planet;

      document.getElementById("street").readOnly = checkbox.checked;
      document.getElementById("city").readOnly = checkbox.checked;
      document.getElementById("zip-code").readOnly = checkbox.checked;
      document.getElementById("planet").readOnly = checkbox.checked;
    }
  </script>

<?php

$templateBasics->getFooter();


