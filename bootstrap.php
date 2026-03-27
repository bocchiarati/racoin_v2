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

// Log de chaque requête (Middleware)
$app->add(function ($request, $handler) {
    // On définit le chemin absolu du dossier logs
    $logDir = __DIR__ . '/logs';

    // 1. SI LE DOSSIER N'EXISTE PAS, ON LE CRÉE
    if (!is_dir($logDir)) {
        // Le paramètre 'true' permet de créer les dossiers parents si besoin
        mkdir($logDir, 0777, true);
    }

    // 2. ON PRÉPARE LE LOG
    $log = sprintf("[%s] %s %s | IP: %s\n",
        date('Y-m-d H:i:s'),
        $request->getMethod(),
        $request->getUri()->getPath(),
        $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
    );

    // 3. ON ÉCRIT DANS LE FICHIER
    file_put_contents($logDir . '/access.log', $log, FILE_APPEND);

    return $handler->handle($request);
});

// Chargement des templates
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