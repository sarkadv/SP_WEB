<?php

// konstanty pro praci s databazi
define("DATABASE_SERVER","localhost");
define("DATABASE_NAME","SP");
define("DATABASE_USER","root");
define("DATABASE_PASSWORD","root");

// nazvy tabulek do anglictiny
define("TABLE_USER","UZIVATEL");
define("TABLE_MODEL","MODEL");
define("TABLE_UFO","UFO");
define("TABLE_REVIEW","RECENZE");
define("TABLE_HIRE","VYPUJCKA");
define("TABLE_CITY","MESTO");
define("TABLE_ROLE","PRAVO");
define("TABLE_ADRESS","ADRESA");

// defaultni stranka webu
const DEFAULT_PAGE_KEY = "introduction";

// vsechny stranky webu
const PAGES = array(
  "introduction" => array(
    "title" => "Půjčovna UFO Andromeda",                // nadpis stranky
    "controller_file_name" => "IntroductionController.class.php",   // kontrolery
    "controller_class_name" => "IntroductionController"
  ),

  "registration" => array(
    "title" => "Registrace",
    "controller_file_name" => "RegistrationController.class.php",
    "controller_class_name" => "RegistrationController"
  ),

  "products" => array(
    "title" => "UFO Modely",
    "controller_file_name" => "ProductsController.class.php",
    "controller_class_name" => "ProductsController"
  ),

  "model" => array(
    "title" => "Model",
    "controller_file_name" => "ModelController.class.php",
    "controller_class_name" => "ModelController"
  ),

  "cart" => array(
    "title" => "Košík",
    "controller_file_name" => "CartController.class.php",
    "controller_class_name" => "CartController"
  ),

  "shipping" => array(
    "title" => "Doručovací Údaje",
    "controller_file_name" => "ShippingController.class.php",
    "controller_class_name" => "ShippingController"
  ),

  "payment" => array(
    "title" => "Platební Údaje",
    "controller_file_name" => "PaymentController.class.php",
    "controller_class_name" => "PaymentController"
  ),

  "order" => array(
    "title" => "Objednávka",
    "controller_file_name" => "OrderController.class.php",
    "controller_class_name" => "OrderController"
  ),

  "account" => array(
    "title" => "Můj Účet",
    "controller_file_name" => "AccountController.class.php",
    "controller_class_name" => "AccountController"
  ),

  "change_info" => array(
    "title" => "Změnit osobní údaje",
    "controller_file_name" => "ChangeInfoController.class.php",
    "controller_class_name" => "ChangeInfoController"
  ),

  "management" => array(
    "title" => "Správa webu",
    "controller_file_name" => "ManagementController.class.php",
    "controller_class_name" => "ManagementController"
  ),

  "administration" => array(
    "title" => "Administrace webu",
    "controller_file_name" => "AdministrationController.class.php",
    "controller_class_name" => "AdministrationController"
  ),
);

// cesty ke kontrolerum, modelum, sablonam
const CONTROLLERS_PATH = "app/controllers/";
const MODELS_PATH = "app/models/";
const VIEWS_PATH = "app/views/";


