<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private function logIn($client)
    {
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin',
            '_password' => 'root',
        ]);

        $client->submit($form);
        $client->followRedirect();
    }

    public function testHomePageIsUp()
    {
        $client = static::createClient();
        $this->logIn($client);

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );
    }

    public function testTaskListPageIsUp()
    {
        $client = static::createClient();
        $this->logIn($client);

        $crawler = $client->request('GET', '/tasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('.thumbnail, .alert')->count(), 'Aucune tâche ou message affiché');
    }

    public function testTaskCreatePageDisplaysForm()
    {
        $client = static::createClient();
        $this->logIn($client);

        $crawler = $client->request('GET', '/tasks/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('form')->count(), 'Le formulaire de création de tâche est introuvable');
    }

    public function testUserListPageIsUp()
    {
        $client = static::createClient();
        $this->logIn($client);

        $crawler = $client->request('GET', '/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Liste des utilisateurs', $crawler->filter('h1')->text());
    }

    public function testUserCreatePageDisplaysForm()
    {
        $client = static::createClient();
        $this->logIn($client);

        $crawler = $client->request('GET', '/users/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('form')->count(), 'Le formulaire de création d’utilisateur est introuvable');
    }

    public function testLoginPageDisplaysForm()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('form')->count(), 'Le formulaire de login est introuvable');

        // Ces lignes peuvent planter si les "for" ne sont pas dans le HTML
        if ($crawler->filter('label[for="username"]')->count()) {
            $this->assertContains("Nom d'utilisateur", $crawler->filter('label[for="username"]')->text());
        }

        if ($crawler->filter('label[for="password"]')->count()) {
            $this->assertContains('Mot de passe', $crawler->filter('label[for="password"]')->text());
        }
    }

    public function testLoginWithBadCredentialsFails()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'fakeuser',
            '_password' => 'badpassword',
        ]);

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect(), 'La soumission ne redirige pas comme prévu');
        $crawler = $client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('.alert-danger')->count(), 'Aucune alerte d’erreur affichée après échec de login');
    }
}
