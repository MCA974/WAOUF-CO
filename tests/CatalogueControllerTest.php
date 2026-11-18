<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CatalogueControllerTest extends WebTestCase
{
    public function testPageAccueilEtCatalogue(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'WOUAF');
        // Vérifier la présence d'articles
        $this->assertGreaterThan(0, $crawler->filter('.brutal-card')->count());
    }

    public function testFicheAnimalEtCarnetVeto(): void
    {
        $client = static::createClient();
        $client->request('GET', '/article/rocky-golden-retriever');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Rocky');
        // Vérifier que le carnet vétérinaire est affiché
        $this->assertSelectorTextContains('h3', 'Carnet de Santé Vétérinaire');
        $this->assertSelectorTextContains('body', '250269608991234');
    }

    public function testPagePanierVide(): void
    {
        $client = static::createClient();
        $client->request('GET', '/panier');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Votre Panier');
    }

    public function testEspaceClientNecessiteConnexion(): void
    {
        $client = static::createClient();
        $client->request('GET', '/mon-compte');

        // Redirection vers login pour utilisateur non authentifié
        $this->assertResponseRedirects('/connexion');
    }

    public function testDashboardAdminSecurise(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        // Redirection vers login pour utilisateur non authentifié
        $this->assertResponseRedirects('/connexion');
    }
}
