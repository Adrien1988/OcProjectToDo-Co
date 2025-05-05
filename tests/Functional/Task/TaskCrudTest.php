<?php
namespace Tests\Functional\Task;

use Tests\Functional\BaseWebTestCase;

/**
 * Scénarios couvert :
 *  1) Création par un utilisateur
 *  2) Édition par son propriétaire
 *  3) Toggle (done/undone) par son propriétaire
 *  4) Suppression par son propriétaire
 *  5) Suppression d’une tâche « anonyme » par l’admin
 *  6) Interdiction de suppression / édition par un autre user
 *  7) 404 quand l’id n’existe pas  ➜ branche « throw createNotFoundException »
 */
class TaskCrudTest extends BaseWebTestCase
{
    /* ---------------------------------------------------------------
       Helpers
    ----------------------------------------------------------------*/
    private function login(string $username, string $password = 'password')
    {
        $c = $this->client;
        $c->request('GET', '/login');
        $c->submitForm('Se connecter', [
            '_username' => $username,
            '_password' => $password,
        ]);
        $c->followRedirect();
        return $c;
    }

    /** Petite aide pour générer /tasks/{id}/xxx */
    private static function taskUrl(int $id, string $suffix): string
    {
        return "/tasks/$id/$suffix";
    }

    /* ---------------------------------------------------------------
       1) Création
    ----------------------------------------------------------------*/
    public function test_user_can_create_task(): void
    {
        $c = $this->login('user1');

        $c->request('GET', '/tasks/create');
        $c->submitForm('Ajouter', [
            'task[title]'   => 'Aller courir',
            'task[content]' => '30 min dans le parc',
        ]);
        $c->followRedirect();

        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('.alert-success');
        $this->assertStringContainsString('Aller courir', $c->getResponse()->getContent());
    }

    /* ---------------------------------------------------------------
       2) Édition de SA propre tâche
    ----------------------------------------------------------------*/
    public function test_owner_can_edit_task(): void
    {
        $c = $this->login('user1');

        $c->request('GET', self::taskUrl(1, 'edit'));   // id 1 = « Ma tâche »
        $this->assertResponseIsSuccessful();

        $c->submitForm('Enregistrer', [
            'task[title]'   => 'Ma tâche modifiée',
            'task[content]' => 'Nouveau contenu',
        ]);
        $c->followRedirect();

        $this->assertSelectorExists('.alert-success');
        $this->assertStringContainsString('Ma tâche modifiée', $c->getResponse()->getContent());
    }

    /* ---------------------------------------------------------------
       3) Toggle done / undone
    ----------------------------------------------------------------*/
    public function test_owner_can_toggle_task(): void
    {
        $c = $this->login('user1');

        $c->request('GET', self::taskUrl(1, 'toggle'));
        $c->followRedirect();

        $this->assertSelectorExists('.alert-success');
    }

    /* ---------------------------------------------------------------
       4) Suppression de SA propre tâche
    ----------------------------------------------------------------*/
    public function test_owner_can_delete_task(): void
    {
        $c = $this->login('user1');

        $c->request('GET', self::taskUrl(1, 'delete'));
        $c->followRedirect();

        $this->assertSelectorExists('.alert-success');
        $this->assertStringNotContainsString('Ma tâche', $c->getResponse()->getContent());
    }

    /* ---------------------------------------------------------------
       5) Suppression d’une tâche anonyme par ADMIN
    ----------------------------------------------------------------*/
    public function test_admin_can_delete_anonymous_task(): void
    {
        $c = $this->login('admin', 'root');

        $c->request('GET', self::taskUrl(2, 'delete'));   // id 2 = « Orphan »
        $c->followRedirect();

        $this->assertSelectorExists('.alert-success');
    }

    /* ---------------------------------------------------------------
       6) Interdiction de suppression / édition pour un AUTRE user
    ----------------------------------------------------------------*/
    public function test_user_cannot_edit_or_delete_others_task(): void
    {
        $c = $this->login('user2');

        // édition interdite
        $c->request('GET', self::taskUrl(1, 'edit'));   // tâche de user1
        $this->assertResponseStatusCodeSame(403);

        // suppression interdite
        $c->request('GET', self::taskUrl(1, 'delete'));
        $this->assertResponseStatusCodeSame(403);
    }

    /* ---------------------------------------------------------------
       7) 404 quand l’id n’existe pas  (couvre les branches throw 404)
    ----------------------------------------------------------------*/
    /**
     * @dataProvider notFoundUrls
     */
    public function test_routes_return_404(string $method, string $url): void
    {
        $c = $this->login('user1');   // n’importe quel utilisateur connecté

        $c->request($method, $url);
        $this->assertResponseStatusCodeSame(404);
    }

    /** Les URLs à tester (provider **statique** ⇒ plus de dépréciation) */
    public static function notFoundUrls(): array
    {
        $id = 999;   // n’existe pas dans les fixtures
        return [
            'edit'   => ['GET',  self::taskUrl($id, 'edit')],
            'toggle' => ['GET',  self::taskUrl($id, 'toggle')],
            'delete' => ['GET',  self::taskUrl($id, 'delete')],
        ];
    }
}
