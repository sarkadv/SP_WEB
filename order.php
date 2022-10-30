<?php

  require_once "php/LoginClass.php";
  $login = new Login;

  require_once "php/HireUFOClass.php";
  $hireUFO = new HireUFO;

  if(isset($_POST["action"])) {
    if($_POST["action"] == "login") {
      if(isset($_POST["email"])) {
        if($_POST["email"] != "") {
          $login->login("email");
        }
      }
    }
    else if($_POST["action"] == "logout") {
      $login->logout();
    }
  }

?>

<!doctype html>
<html lang="cs">

<head>
  <meta charset="utf-8">
  <title>Půjčovna UFO Andromeda</title>
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
            if(!$login->isUserLoggedIn()) {
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
  <div class="row" id="order-main-content">
    <h2 class="order-main-heading">
      Děkujeme za objednávku!
    </h2>

    <div class="row">
      <div class="col-12">
        <h6>
          Vozidlo bude ihned posláno na Vaši adresu.
        </h6>
      </div>
    </div>

    <hr>

    <?php
    if($hireUFO->isUFOSaved()) {
    ?>

    <div class="row">
      <div class="col-12">
        <div class="order-main-heading">
          <h5>
            Shrnutí objednávky
          </h5>
        </div>
      </div>
    </div>

    <div class="jumbotron">
      <div class="row order-main-item">
        <div class="col-sm-4">
          <div class="order-main-item-name">
            <?php echo $hireUFO->getModel() ?>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="order-main-item-days">
            Dny: <?php echo $hireUFO->getDays() ?>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="order-main-item-price">
            <?php echo $hireUFO->getPrice() ?> kreditů
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="order-main-heading">
          Celkem: <?php echo $hireUFO->getPrice() ?> kreditů
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
          Mimozemská 108
        </div>
        <div>
          Megaton, 123 45
        </div>
        <div>
          Gliese 667Cc
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
          1111000011110000
        </div>
      </div>
    </div>

    <?php
    }
    ?>

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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

<!-- Boostrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>

</html>





