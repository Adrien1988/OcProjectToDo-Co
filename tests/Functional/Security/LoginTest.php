<?php
namespace Tests\Functional\Security;

use Tests\Functional\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends BaseWebTestCase
{
    /* ------------------------------------------------------------------
       1°) L’URL /login doit être accessible et afficher un formulaire
    ------------------------------------------------------------------ */
    public function test_login_page_is_accessible(): void
    {
        $client  = $this->client;
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();          // code 200
        $this->assertSelectorExists('form');          // un <form> est présent
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
    }

    /* ------------------------------------------------------------------
       2°) Mauvaises informations → même page + message d’erreur
    ------------------------------------------------------------------ */
    public function test_login_with_bad_credentials_shows_error(): void
    {
        $client = $this->client;
        $client->request('GET', '/login');

        $client->submitForm('Se connecter', [
            '_username' => 'toto',
            '_password' => 'bad-password',
        ]);

        // Symfony renvoie un 302 vers /login (échec → redirection)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        // On revient sur /login et un message d’erreur est affiché
        $this->assertRouteSame('login');
        $this->assertSelectorExists('.alert-danger');
    }

    /* ------------------------------------------------------------------
       3°) Bonnes informations → redirection vers la page d’accueil
    ------------------------------------------------------------------ */
    public function test_login_with_good_credentials_redirects_home(): void
    {
        $client = $this->client;
        $client->request('GET', '/login');

        $client->submitForm('Se connecter', [
            '_username' => 'admin',   // définis dans TestFixtures
            '_password' => 'root',
        ]);

        // Succès d’authentification → redirection 302
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        // On doit arriver sur la route d’accueil
        $this->assertRouteSame('homepage');           // adapte si besoin
        $this->assertSelectorTextContains('body', 'Bienvenue');
    }

    /* ------------------------------------------------------------------
       4°) Route /logout : Symfony intercepte, on obtient un 302
          (on ne teste pas la logique, juste que la route existe)
    ------------------------------------------------------------------ */
    public function test_logout_route_is_intercepted(): void
    {
        $client = $this->client;
        $client->request('GET', '/logout');

        // Le firewall déclenche une redirection (vers /)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
