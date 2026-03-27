<?php
require 'vendor/autoload.php';

use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use db\connection;

connection::createConn();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$loader = new FilesystemLoader(__DIR__ . '/template');
$twig   = new Environment($loader);

// Tes variables de config
$menu = [['href' => './index.php', 'text' => 'Accueil']];
$chemin = dirname($_SERVER['SCRIPT_NAME']);
$cat = new service\CategorieService();
$dpt = new service\DepartmentService();

// ON INJECTE LES VARIABLES DANS LES ROUTES
$routes = require_once __DIR__ . '/routes.php';
$app = $routes($app, $twig, $menu, $chemin, $cat, $dpt);

return $app;