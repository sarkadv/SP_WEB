<?php
// sablona pro stranku Uvod

require_once VIEWS_PATH."TemplateBasics.class.php";

global $templateData;   // vsechna data pro sablonu

$templateBasics = new TemplateBasics();

//--------------------------------------

$templateBasics->getHeader($templateData["title"]);
$templateBasics->getMenu($templateData["user_logged"], $templateData["user_role"]);
$templateBasics->getLoginSidebar();

?>

  <!-- Stred stranky - seznam a tabulka-->
  <div class="row">
    <h1 id="home-main-about">
      Půjčovna UFO Andromeda
    </h1>
  </div>

  <div class="row" id="home-main-content">
    <!-- Seznam -->
    <div class="col-md-6">
      <h2 class="home-main-heading">
        Proč si vybrat právě nás?
      </h2>
      <div class="jumbotron">
        <p>
        <ul id="home-main-list">
          <li class="home-main-list-item">
            <i class="fas fa-clock fa-2x home-main-list-icon"></i>
            <span class="home-main-list-text">Více než <b>800 let</b> zkušeností v oboru</span>
          </li>
          <li class="home-main-list-item">
            <i class="fas fa-wrench fa-2x home-main-list-icon"></i>
            <span class="home-main-list-text">Servisní služby po celé <b>Místní skupině galaxií</b></span>
          </li>
          <li class="home-main-list-item">
            <i class="fas fa-rocket fa-2x home-main-list-icon"></i>
            <span class="home-main-list-text"><b>Široký výběr</b> nejrůznějších modelů</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Tabulka -->
    <div class="col-md-6">
      <h2 class="home-main-heading">
        Poslední recenze
      </h2>
      <p>
        Nejnovější recenze od našich spokojených zákazníků.
      </p>
      <table class="table table-hover table-striped">
        <thead class="table-danger">
        <tr>
          <th>Uživatel</th>
          <th>Datum a čas</th>
          <th>Hodnocení</th>
          <th>Model</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $reviews = $templateData["reviews"];

        foreach($reviews as $review) {
          $firstName = $review["username"];
          $datetime = $review["datetime"];
          $rating = $review["rating"];
          $model = $review["model"];

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
            <td><?php echo $model; ?></td>
          </tr>
          <?php
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Nadpis pro carousel -->
  <div class="row">
    <h2 class="home-main-heading" id="home-carousel-favorites">
      Přehled nejoblíbenějších modelů
    </h2>
  </div>

<?php

// modely zobrazene v carouselu
$UFO1 = $templateData["ufo_1"];
$UFO2 = $templateData["ufo_2"];
$UFO3 = $templateData["ufo_3"];

?>

  <!-- Carousel -->
  <div class="row">
    <div id="demo" class="carousel slide" data-bs-ride="carousel">

      <!-- Dolní indikátory -->
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
      </div>

      <!-- Obrazky -->
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="<?php echo $UFO1["obrazek_url"]; ?>" alt="<?php echo $UFO1["nazev"]; ?>" class="d-block w-100">
          <p class="home-carousel-bottom-paragraph"></p>
          <div class="carousel-caption">
            <h3><?php echo $UFO1["nazev"]; ?></h3>
            <p><?php echo $UFO1["popis_kratky"]; ?></p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="<?php echo $UFO2["obrazek_url"]; ?>" alt="<?php echo $UFO2["nazev"]; ?>" class="d-block w-100">
          <p class="home-carousel-bottom-paragraph"></p>
          <div class="carousel-caption">
            <h3><?php echo $UFO2["nazev"]; ?></h3>
            <p><?php echo $UFO2["popis_kratky"]; ?></p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="<?php echo $UFO3["obrazek_url"]; ?>" alt="<?php echo $UFO3["nazev"]; ?>" class="d-block w-100">
          <p class="home-carousel-bottom-paragraph"></p>
          <div class="carousel-caption">
            <h3><?php echo $UFO3["nazev"]; ?></h3>
            <p><?php echo $UFO3["popis_kratky"]; ?></p>
          </div>
        </div>
      </div>

      <!-- Leve a prave ovladaci tlacitko -->
      <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </div>

<?php

$templateBasics->getFooter();

