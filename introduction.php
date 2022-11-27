<?php

  require_once "php/DatabaseConnection.class.php";
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
              <a class="nav-link" href="introduction.php">
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

  <!-- Nadpis pro carousel -->
  <div class="row">
    <h2 class="home-main-heading" id="home-carousel-favorites">
      Přehled nejoblíbenějších modelů
    </h2>
  </div>

  <?php
    // modely zobrazene v carouselu
    $UFO1 = $dbconnection->getUFOModelByNumber(1);
    $UFO2 = $dbconnection->getUFOModelByNumber(2);
    $UFO3 = $dbconnection->getUFOModelByNumber(3);
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
