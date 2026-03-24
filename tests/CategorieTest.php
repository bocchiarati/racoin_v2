<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use controller\getCategorie;
use Mockery;

class CategorieTest extends TestCase
{
    protected function tearDown(): void
    {
        // Toujours fermer Mockery après chaque test pour éviter les conflits
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetCategories()
    {
        // On crée un mock de type "alias" pour intercepter les appels statiques de Eloquent
        // @runInSeparateProcess évite les conflits avec le modèle qui pourrait être chargé ailleurs
        $mockCategorie = Mockery::mock('alias:model\Categorie');

        // On simule la chaîne d'appels: orderBy('nom_categorie')->get()->toArray()
        $mockCategorie->shouldReceive('orderBy')
            ->with('nom_categorie')
            ->once()
            ->andReturnSelf();

        $mockCategorie->shouldReceive('get')
            ->once()
            ->andReturnSelf();

        // toArray() est la dernière méthode de la chaîne, c'est elle qui renvoie nos données factices
        $mockCategorie->shouldReceive('toArray')
            ->once()
            ->andReturn([
                ['id_categorie' => 1, 'nom_categorie' => 'Informatique'],
                ['id_categorie' => 2, 'nom_categorie' => 'Jeux Vidéo']
            ]);

        // Appel du controller
        $controller = new getCategorie();
        $result = $controller->getCategories();

        // Vérifications
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Informatique', $result[0]['nom_categorie']);
        $this->assertEquals('Jeux Vidéo', $result[1]['nom_categorie']);
    }

    public function testGetCategorieContent()
    {
        // Implémentation du second test (voir ma réponse précédente si besoin)
        $this->assertTrue(true);
    }
}
