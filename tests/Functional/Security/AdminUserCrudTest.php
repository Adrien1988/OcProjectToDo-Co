<?php
namespace Tests\Functional\Security;

use Tests\Functional\BaseWebTestCase;

/**
 * CRUD complet sur les utilisateurs côté admin.
 */
class AdminUserCrudTest extends BaseWebTestCase
{
    /* ================================================================
       Helpers
    =================================================================*/
    private function login(string $user, string $pwd = 'password')
    {
        $c = $this->client;
        $c->request('GET', '/login');
        $c->submitForm('Se connecter', ['_username'=>$user, '_password'=>$pwd]);
        $c->followRedirect();
        return $c;
    }

    /* ================================================================
       LIST
    =================================================================*/
    public function test_admin_can_see_user_list(): void
    {
        $c = $this->login('admin', 'root');
        $c->request('GET', '/users');

        $this->assertRouteSame('admin_user_list');
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertSelectorExists('table tbody tr');
    }

    /* ================================================================
       CREATE
    =================================================================*/
    public function test_admin_can_create_user(): void
    {
        $c = $this->login('admin', 'root');

        $c->request('GET', '/users/create');
        $c->submitForm('Ajouter', [
            'registration_form[username]'         => 'alice',
            'registration_form[email]'            => 'alice@example.com',
            'registration_form[password][first]'  => 'S3cret!',
            'registration_form[password][second]' => 'S3cret!',
            'registration_form[roles]'            => 'ROLE_USER',
        ]);
        $c->followRedirect();

        $this->assertRouteSame('admin_user_list');
        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('tbody', 'alice');
    }

    /* ================================================================
       EDIT ❶ (sans changement de mot de passe)
    =================================================================*/
    public function test_admin_can_edit_user_without_password_change(): void
    {
        $c = $this->login('admin', 'root');

        // Alice possède normalement l’id 3 (créée juste avant)
        $c->request('GET', '/users/3/edit');
        $this->assertResponseIsSuccessful();

        $c->submitForm('Enregistrer', [
            'registration_form[email]' => 'alice@new.com',
            // mot de passe laissé vide
            'registration_form[roles]' => 'ROLE_ADMIN',
        ]);
        $c->followRedirect();

        $this->assertRouteSame('admin_user_list');
        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('tbody', 'alice@new.com');
    }

    /* ================================================================
       EDIT ❷ (avec changement de mot de passe)
       -> couvre la branche `if ($plain) { … }`
    =================================================================*/
    public function test_admin_can_edit_user_with_password_change(): void
    {
        $c = $this->login('admin', 'root');

        $c->request('GET', '/users/3/edit');
        $this->assertResponseIsSuccessful();

        $c->submitForm('Enregistrer', [
            'registration_form[email]'            => 'alice@pwd.com',
            'registration_form[password][first]'  => 'N3wPass!',
            'registration_form[password][second]' => 'N3wPass!',
            'registration_form[roles]'            => 'ROLE_ADMIN',
        ]);
        $c->followRedirect();

        $this->assertRouteSame('admin_user_list');
        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('tbody', 'alice@pwd.com');
    }

    /* ================================================================
       EDIT 404
    =================================================================*/
    /**
     * @dataProvider notFoundIds
     */
    public function test_edit_returns_404_when_user_does_not_exist(int $id): void
    {
        $c = $this->login('admin', 'root');
        $c->request('GET', "/users/$id/edit");

        $this->assertResponseStatusCodeSame(404);
    }

    public static function notFoundIds(): array
    {
        return [[9999], [123456]];
    }

    /* ================================================================
       DELETE
    =================================================================*/
    public function test_admin_can_delete_user(): void
    {
        $c = $this->login('admin', 'root');

        // On supprime Alice (id 3)
        $c->request('GET', '/users/3/delete');
        $c->followRedirect();

        $this->assertSelectorExists('.alert-success');
        $this->assertStringNotContainsString('alice', $c->getResponse()->getContent());
    }

    /* ================================================================
       Restrictions pour ROLE_USER
    =================================================================*/
    /**
     * @dataProvider protectedUrls
     */
    public function test_user_cannot_access_admin_urls(string $method, string $url): void
    {
        $c = $this->login('user1');   // ROLE_USER

        $c->request($method, $url);
        $this->assertResponseStatusCodeSame(403);
    }

    public static function protectedUrls(): array
    {
        return [
            ['GET', '/users'],
            ['GET', '/users/create'],
            ['GET', '/users/1/edit'],
            ['GET', '/users/1/delete'],
        ];
    }
}
