<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use model\Annonce;
use model\Annonceur;
use model\Categorie;
use model\Departement;

return function(App $app, $twig, $menu, $chemin, $cat, $dpt): App {

    // --- ROUTES WEB ---

    $app->get('/', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
        $index = new controller\AnnonceController();
        $index->displayAllAnnonce($twig, $menu, $chemin, $cat->getCategories());
        return $response;
    });

    $app->get('/item/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat) {
        $item = new controller\item();
        $item->afficherItem($twig, $menu, $chemin, $arg['n'], $cat->getCategories());
        return $response;
    });

    $app->get('/add', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat, $dpt) {
        $ajout = new controller\addItem();
        $ajout->addItemView($twig, $menu, $chemin, $cat->getCategories(), $dpt->getAllDepartments());
        return $response;
    });

    $app->post('/add', function (Request $request, Response $response) use ($twig, $menu, $chemin) {
        $ajout = new controller\addItem();
        $ajout->addNewItem($twig, $menu, $chemin, $request->getParsedBody());
        return $response;
    });

    $app->get('/item/{id}/edit', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin) {
        $item = new controller\item();
        $item->modifyGet($twig, $menu, $chemin, $arg['id']);
        return $response;
    });

    $app->post('/item/{id}/edit', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat, $dpt) {
        $item = new controller\item();
        $item->modifyPost($twig, $menu, $chemin, $arg['id'], $request->getParsedBody(), $cat->getCategories(), $dpt->getAllDepartments());
        return $response;
    });

    $app->map(['GET', 'POST'], '/item/{id}/confirm', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin) {
        $item = new controller\item();
        $item->edit($twig, $menu, $chemin, $arg['id'], $request->getParsedBody());
        return $response;
    });

    $app->get('/search', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
        (new controller\Search())->show($twig, $menu, $chemin, $cat->getCategories());
        return $response;
    });

    $app->post('/search', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
        (new controller\Search())->research($request->getParsedBody(), $twig, $menu, $chemin, $cat->getCategories());
        return $response;
    });

    $app->get('/annonceur/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat) {
        (new controller\AnnonceurController())->afficherAnnonceur($twig, $menu, $chemin, $arg['n'], $cat->getCategories());
        return $response;
    });

    $app->get('/del/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin) {
        (new controller\item())->supprimerItemGet($twig, $menu, $chemin, $arg['n']);
        return $response;
    });

    $app->post('/del/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat) {
        (new controller\item())->supprimerItemPost($twig, $menu, $chemin, $arg['n'], $cat->getCategories());
        return $response;
    });

    $app->get('/cat/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat) {
        (new controller\CategorieController())->displayCategorie($twig, $menu, $chemin, $cat->getCategories(), $arg['n']);
        return $response;
    });

    // --- GROUPE API ---

    // --- GROUPE API ---
    $app->group('/api', function ($group) use ($twig, $chemin, $cat) {

        // Page d'accueil de l'API
        $group->get('[/]', function (Request $request, Response $response) use ($twig, $chemin) {
            $template = $twig->load('api.html.twig');
            $menu_api = [
                ['href' => $chemin, 'text' => 'Accueil'],
                ['href' => $chemin . '/api', 'text' => 'Api']
            ];
            $response->getBody()->write($template->render(['breadcrumb' => $menu_api, 'chemin' => $chemin]));
            return $response;
        });

        // Sous-groupe Annonce
        $group->group('/annonce', function ($groupAnnonce) {
            $groupAnnonce->get('/{id}', function (Request $request, Response $response, array $arg) {
                $id = $arg['id'];
                $return = model\Annonce::find($id);
                if ($return) {
                    $return->categorie = model\Categorie::find($return->id_categorie);
                    $return->annonceur = model\Annonceur::select('email', 'nom_annonceur', 'telephone')->find($return->id_annonceur);
                    $return->departement = model\Departement::select('id_departement', 'nom_departement')->find($return->id_departement);
                    $return->links = ['self' => ['href' => '/api/annonce/' . $id]];
                    $response->getBody()->write($return->toJson());
                    return $response->withHeader('Content-Type', 'application/json');
                }
                return $response->withStatus(404);
            });
        });

        // Liste des annonces
        $group->get('/annonces[/]', function (Request $request, Response $response) {
            $a = model\Annonce::all(['id_annonce', 'prix', 'titre', 'ville']);
            $response->getBody()->write($a->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });

        // --- CATEGORIE (Unique) ---
        $group->get('/categorie/{id}', function (Request $request, Response $response, array $arg) {
            $id = $arg['id'];
            $a = model\Annonce::select('id_annonce', 'prix', 'titre', 'ville')
                ->where('id_categorie', '=', $id)
                ->get();
            $c = model\Categorie::find($id);
            if ($c) {
                $c->annonces = $a;
                $response->getBody()->write($c->toJson());
                return $response->withHeader('Content-Type', 'application/json');
            }
            return $response->withStatus(404);
        });

        // --- CATEGORIES (Liste) ---
        $group->get('/categories[/]', function (Request $request, Response $response) {
            $c = model\Categorie::all();
            $response->getBody()->write($c->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });

        // --- KEY GENERATOR ---
        $group->get('/key', function (Request $request, Response $response) use ($twig, $chemin, $cat) {
            $kg = new controller\KeyGeneratorController();
            $kg->show($twig, [], $chemin, $cat->getCategories());
            return $response;
        });

        $group->post('/key', function (Request $request, Response $response) use ($twig, $chemin, $cat) {
            $params = $request->getParsedBody();
            $nom = $params['nom'] ?? '';
            $kg = new controller\KeyGeneratorController();
            $kg->generateKey($twig, [], $chemin, $cat->getCategories(), $nom);
            return $response;
        });
    });

    return $app;
};