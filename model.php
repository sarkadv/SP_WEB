<?php

  require_once "php/DatabaseConnectionClass.php";
  $dbconnection = new DatabaseConnection();

  require_once "php/HireUFOClass.php";
  $hireUFO = new HireUFO;

  if(isset($_POST["action"])) {
    if($_POST["action"] == "login") {
      if(isset($_POST["email"]) && isset($_POST["pswd"])) {
        if($_POST["email"] != "" && $_POST["pswd"] != "") {
          $result = $dbconnection->loginUser($_POST["email"], $_POST["pswd"]);

          if(!$result) {
            echo '<script>alert("Nesprávný e-mail nebo heslo.")</script>';
          }
        }
      }
    }
    else if($_POST["action"] == "logout") {
      $dbconnection->logoutUser();
    }
  }

  else if(isset($_POST["hire"])) {
    $model = $dbconnection->getUFOModelByNumber($_POST["hire"]);

    if($model != null) {
      $hireUFO->saveUFOData($model["nazev"], $_POST["days"], $_POST["days"] * $model["cena_den"]);
    }

    header("Refresh:0");
  }

  $UFOModel = null;

  if(isset($_GET["examine"])) {
    if(ctype_digit($_GET["examine"]) || is_int($_GET["examine"])) {
      $UFOModel = $dbconnection->getUFOModelByNumber($_GET["examine"]);
    }
  }

?>

<!doctype html>
<html lang="cs">

<head>
  <meta charset="utf-8">
  <title>Půjčovna UFO Andromeda</title>
  <link rel="icon" type="image/x-icon" href="img/logo.png">
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="css/main.css">

  <!-- Boostrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

</head>

<body>

<!-- Hlavicka stranky -->
<div class="container-fluid" id="body-container">
  <div class="row">
    <img src="img/banner.png" class="img-fluid" id="header-img" alt="Úvodní obrázek">
  </div>

  <!-- Hlavni menu -->
  <div class="row">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <div class="container-fluid">
        <div class="navbar-brand">
          <img src="img/logo.png" alt="Logo Andromeda">
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="index.php">
                <i class="fas fa-home"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="products.php">Nabídka</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">O Nás</a>
            </li>
          </ul>
          <div class="btn-group">

            <?php
            if(!$dbconnection->isUserLoggedIn()) {
              ?>
              <!-- Pro neprihlasene uzivatele -->
              <button type="button" id="btn-account" data-bs-toggle="offcanvas" data-bs-target="#demo-sidebar">
                <i class="fas fa-user"></i>
                Přihlásit se
              </button>
              <button type="button" id="btn-cart" onclick="location.href='cart.php'">
                <i class="fas fa-shopping-basket"></i>
              </button>

              <?php
            }
            else {
              ?>
              <!-- Pro prihlasene uzivatele -->
              <button type="button" id="btn-account" onclick="location.href='#'">
                <i class="fas fa-user"></i>
                Můj účet
              </button>

              <form method="post">
                <button type="submit" id="btn-logout" name="action" value="logout">
                  <i class="fas fa-sign-out-alt"></i>
                  Odhlásit se
                </button>
              </form>

              <button type="button" id="btn-cart" onclick="location.href='cart.php'">
                <i class="fas fa-shopping-basket"></i>
              </button>

              <?php
            }
            ?>

          </div>
        </div>
      </div>
    </nav>
  </div>

  <!-- Sidebar pro prihlaseni -->
  <div class="offcanvas offcanvas-start" id="demo-sidebar">
    <div class="offcanvas-header">
      <h2 class="offcanvas-title">Přihlásit se</h2>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <form method="post">
        <div class="row">
          <div class="col-12 registration-main-item">
            <label for="email" class="form-label">E-mail</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-at"></i>
              </div>
              <input type="email" class="form-control" id="email" placeholder="ufon@gmail.com" name="email" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 registration-main-item">
            <label for="pwd" class="form-label">Heslo</label>
            <div class="input-group">
              <div class="input-group-text">
                <i class="fas fa-key"></i>
              </div>
              <input type="password" class="form-control" id="pwd" placeholder="Heslo" name="pswd" required>
            </div>
          </div>
        </div>
        <button type="submit" id="registration-main-btn-submit" name="action" value="login">Přihlásit se</button>
      </form>
      <hr>
      <p>
        Ještě u nás nemáte účet?
        <a href="registration.php">
          Zaregistrujte se.
        </a>
      </p>
    </div>
  </div>

  <!-- Stred stranky - obrazek UFO a zakladni informace -->
  <?php
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
      $modelNumber = $_GET["examine"];
      $shortDesc = $UFOModel["popis_kratky"];
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
        if($dbconnection->getNumberOfUFOsAvailableByModelNumber($modelNumber) > 0) {
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
        <p class="model-main-info">Tento model je momentálně nedostupný.</p>

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
              Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed convallis magna eu sem. Aenean vel massa quis mauris vehicula lacinia. Etiam posuere lacus quis dolor. Mauris elementum mauris vitae tortor. Morbi imperdiet, mauris ac auctor dictum, nisl ligula egestas nulla, et sollicitudin sem purus in lacus. Etiam commodo dui eget wisi. Vestibulum fermentum tortor id mi. Nulla accumsan, elit sit amet varius semper, nulla mauris mollis quam, tempor suscipit diam nulla vel leo. Fusce tellus. Integer tempor. Nullam eget nisl. Maecenas libero. Pellentesque ipsum.
            </p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwo">
            <h4>
              Recenze
            </h4>
          </a>
        </div>
        <div id="collapseTwo" class="collapse" data-bs-parent="#accordion">
          <div class="card-body">
            <table class="table table-hover table-striped">
              <thead class="table-danger">
              <tr>
                <th>Uživatel</th>
                <th>Hodnocení</th>
                <th>Text recenze</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>John</td>
                <td>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                </td>
                <td class="home-main-table-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</td>
              </tr>
              <tr>
                <td>Mary</td>
                <td>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                </td>
                <td class="home-main-table-text">Maecenas convallis lectus pretium lectus luctus suscipit.</td>
              </tr>
              <tr>
                <td>July</td>
                <td>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                  <i class="fas fa-star home-main-table-stars"></i>
                </td>
                <td class="home-main-table-text">Donec facilisis egestas enim et maximus.</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  <?php
    }
  ?>

  <!-- Paticka stranky -->
  <div class="row" id="footer">
    <div class="col-12 col-md-4" id="home-footer-left">
      <h5>Andromeda</h5>
      <img src="img/logo.png" alt="Logo Andromeda">
    </div>
    <div class="col-6 col-md-4" id="home-footer-center">
      <h5>Kontakty</h5>
      <ul id="home-footer-list">
        <li class="home-footer-list-item">
          <i class="fas fa-phone home-footer-list-icon"></i>
          <span class="home-footer-list-text">+000 739 606 281</span>
        </li>
        <li class="home-footer-list-item">
          <i class="fas fa-comment-dots home-footer-list-icon"></i>
          <span class="home-footer-list-text">andromeda@mail.u</span>
        </li>
        <li class="home-footer-list-item">
          <i class="fas fa-clock home-footer-list-icon"></i>
          <span class="home-footer-list-text">nonstop</span>
        </li>
      </ul>
    </div>
    <div class="col-6 col-md-4" id="home-footer-right">
      <h5>Hlavní pobočka</h5>
      <span>Andromeda HQ</span><br>
      <span>Technická 1100</span><br>
      <span>Megaton, Kepler-22b</span><br>
      <span>Mléčná dráha</span>
    </div>
  </div>

  <!-- Druha paticka stranky -->
  <div class="row" id="copyright">
    <span>© Šárka Dvořáková 2022</span>
  </div>

</div>

<script>
  function zoom(e){
    var zoomer = e.currentTarget;
    e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX;
    e.offsetY ? offsetY = e.offsetY : offsetY = e.touches[0].pageY;
    x = offsetX/zoomer.offsetWidth*100;
    y = offsetY/zoomer.offsetHeight*100;
    zoomer.style.backgroundPosition = x + '% ' + y + '%';
  }
</script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

<!-- Boostrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>

</html>



