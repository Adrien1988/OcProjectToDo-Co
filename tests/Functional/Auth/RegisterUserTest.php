<?php
namespace Tests\Functional\Auth;

use Tests\Functional\BaseWebTestCase;

class RegisterUserTest extends BaseWebTestCase
{
    public function test_visitor_can_register(): void
    {
        /* ------------------------------------------------------------------
           1°) Affichage du formulaire public /register
        ------------------------------------------------------------------ */
        $client  = $this->client;
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        /* ------------------------------------------------------------------
           2°) Soumission du formulaire “registration_form”
        ------------------------------------------------------------------ */
        $client->submitForm('Enregistrer', [
            'registration_form[username]'            => 'bob',
            'registration_form[email]'               => 'bob@example.com',
            'registration_form[password][first]'     => 'Passw0rd!',
            'registration_form[password][second]'    => 'Passw0rd!',
        ]);

        /* ------------------------------------------------------------------
           3°) Redirection → /login + message de succès
        ------------------------------------------------------------------ */
        $client->followRedirect();
        $this->assertRouteSame('login');
        $this->assertSelectorTextContains('.alert-success', 'compte a bien été créé');
    }
}
