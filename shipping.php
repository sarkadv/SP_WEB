<?php

  require_once "php/DatabaseConnectionClass.php";
  $dbconnection = new DatabaseConnection();

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

  <!-- Stred stranky - obsah kosiku -->
  <div class="row" id="shipping-main-content">
    <h2 class="shipping-main-heading">
      Doručovací údaje
    </h2>

    <?php
    if($dbconnection->isUserLoggedIn()) {
      $user = $dbconnection->getLoggedUser();
      $adress = $dbconnection->getAdressByNumber($user["c_adresy_fk"]);
      $street = $adress["ulice"];
      $city = ($dbconnection->getCityByNumber($adress["c_mesta_fk"]))["nazev"];
      $zip = ($dbconnection->getCityByNumber($adress["c_mesta_fk"]))["psc"];
      $planet = $adress["planeta"];
    ?>

    <!-- Pro prihlasene uzivatele -->
    <form action="payment.php" method="post">
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
            <input type="text" class="form-control" id="street" placeholder="Ulice a číslo popisné" value="<?php echo $street;?>" name="street" readonly required>
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
            <input type="text" class="form-control" id="city" placeholder="Město" value="<?php echo $city;?>" name="city" readonly required>
          </div>
        </div>
        <div class="col-md-4 shipping-main-item">
          <label for="zip-code" class="form-label">Poštovní směrovací číslo</label>
          <div class="input-group">
            <div class="input-group-text">
              <i class="far fa-envelope-open"></i>
            </div>
            <input type="text" class="form-control" id="zip-code" placeholder="PSČ" value="<?php echo $zip;?>" name="zip-code" readonly required>
          </div>
        </div>
        <div class="col-md-4 shipping-main-item">
          <label for="planet" class="form-label">Domovská planeta</label>
          <div class="input-group">
            <div class="input-group-text">
              <i class="fas fa-globe"></i>
            </div>
            <input class="form-control" id="planet" list ="planets" placeholder="Domovská planeta" value="<?php echo $planet;?>" name="planet" readonly required>
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
  function disableAdress() {
    var checkbox = document.getElementById("use-default");

    var street = "<?php echo $street;?>";
    var city = "<?php echo $city;?>";
    var zip = "<?php echo $zip;?>";
    var planet = "<?php echo $planet;?>";

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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

<!-- Boostrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>

</html>



