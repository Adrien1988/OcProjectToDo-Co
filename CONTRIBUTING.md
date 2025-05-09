Contribuer au projet ToDo&Co
Merci de vouloir contribuer au projet ToDo&Co ! Ce guide explique clairement comment contribuer, les règles à respecter, et les standards qualité du projet.

🚀 Processus général de contribution
Voici les étapes à suivre pour contribuer efficacement :

1. Cloner et configurer le projet localement

```bash
git clone https://github.com/Adrien1988/OcProjectToDo-Co.git
cd OcProjectToDo-Co
composer install
cp .env .env.local # Complétez DATABASE_URL avec votre configuration
symfony console doctrine:database:create --if-not-exists
symfony console doctrine:migrations:migrate
symfony serve -d
```

2. Créer une branche dédiée à votre modification
Le nom de la branche doit respecter le format suivant :

Fonctionnalité : feature/<numero-issue>-courte-description

Correction : bugfix/<numero-issue>-courte-description

Exemple :

```bash
git checkout -b feature/12-doc-contribution
```

3. Développer la fonctionnalité / Correction
Effectuez les changements nécessaires.

Respectez les normes de codage et de qualité du projet (voir ci-dessous).

4. Committer vos modifications clairement
Utilisez la convention suivante pour vos messages de commit :

type(scope): description courte et claire

Exemples :
- feat(auth): ajout de la vérification CSRF
- fix(template): correction affichage page login
- docs(readme): amélioration procédure installation

5. Pousser votre branche

```bash
git push origin feature/12-doc-contribution
```

6. Ouvrir une Pull Request
Depuis GitHub :

Sélectionnez votre branche et ouvrez une Pull Request vers la branche main.

Décrivez clairement :

le contexte et la solution choisie ;

référencez l’issue (ex. : « Fixes #12 »).

Exemple :

```bash
Fixes #12 : Documentation pour les futurs contributeurs

Cette PR ajoute une documentation complète des standards qualité et du processus de contribution pour faciliter l’intégration des nouveaux contributeurs.

```

7. Revue et validation de la Pull Request
Une revue de code est obligatoire avant toute fusion (merge).

Corrigez les éventuelles remarques faites par les reviewers.

Une fois la PR approuvée, un responsable du projet effectue la fusion (squash and merge privilégié).

✅ Standards Qualité du projet
Pour garantir un haut niveau de qualité, voici les règles précises à respecter :

📌 Normes de codage
Respect strict de la norme PSR-12.

Toujours indiquer explicitement le typage des variables et des retours de méthodes :

public function exemple(string $data): array

Aucun code commenté ni « code mort » laissé dans le dépôt.

Pas de debug (var_dump(), dd()) présent dans le dépôt.

📌 Structure du projet
src/ : contient le code métier organisé par type (Controller, Entity, Service, Security...).

templates/ : regroupe toutes les vues Twig.

config/ : rassemble toutes les configurations applicatives (Symfony, routes, security).

📌 Tests automatisés (PHPUnit)
Chaque nouvelle fonctionnalité doit être accompagnée de tests unitaires et/ou fonctionnels.

Lancez toujours les tests avant de pousser votre code :

```bash
composer test
```

qui comprend les commandes suivantes : 

"scripts": {
    "cache:test:clear": "SET APP_ENV=test&& php bin/console cache:clear --env=test --no-debug",
    "test:unit":        "vendor\\bin\\phpunit.bat --testsuite unit",
    "test:functional":  "vendor\\bin\\phpunit.bat --testsuite functional",
    "cov:all":          "SET \"XDEBUG_MODE=coverage\" && vendor\\bin\\phpunit.bat --testsuite unit,functional --coverage-html build\\coverage --coverage-clover build\\clover.xml -d --min-coverage=70",
    "test": [
      "@cache:test:clear",
      "@test:unit",
      "@test:functional",
      "@cov:all"
    ]
  }

  📌 Analyse statique du code (PHP_CodeSniffer, PHP-CS-Fixer)
Le projet utilise deux outils complémentaires pour garantir la qualité et l’uniformité du code :

1. PHP_CodeSniffer (vérification des standards PSR-12)

PHP_CodeSniffer s'assure que votre code respecte les standards de codage PHP PSR-12.

Mise en place :
Un fichier de configuration nommé phpcs.xml.dist doit être présent à la racine du projet :

<?xml version="1.0"?>
<ruleset name="ToDoCo">
    <rule ref="PSR12"/>
    <file>src</file>
    <file>tests</file>
</ruleset>

Utilisation :

Pour analyser votre code, exécutez cette commande :

```bash
php vendor/bin/phpcs
```
Pour corriger automatiquement les erreurs détectées, utilisez :

```bash
php vendor/bin/phpcbf
```

2. PHP-CS-Fixer (formatage automatique)

PHP-CS-Fixer corrige automatiquement les erreurs mineures de mise en forme.

Mise en place :
Un fichier de configuration nommé .php-cs-fixer.dist.php doit également être à la racine du projet, avec une configuration minimale :

<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);

Utilisation :

Pour appliquer les corrections automatiquement, lancez la commande :

```bash
php vendor/bin/php-cs-fixer fix
```
Pensez à exécuter ces commandes systématiquement avant chaque commit afin de maintenir une qualité optimale du code.

Ces fichiers doivent impérativement être en place et versionnés dans votre dépôt afin que chaque contributeur applique exactement les mêmes règles qualité.

📌 Sécurité
Assurez-vous de la sécurité de vos développements (CSRF, injection SQL, XSS...).

Les mots de passe et clés secrètes doivent être gérés uniquement via les variables d’environnement (.env).

📌 Bonnes pratiques Git
Historique clair : privilégiez les petits commits fréquents avec un message descriptif.

Avant ouverture d’une Pull Request, rebasez toujours vos branches sur main :

```bash
git checkout feature/12-doc-contribution
git fetch origin main
git rebase origin/main
```

🔍 Checklist avant d’ouvrir une Pull Request
Assurez-vous d’avoir vérifié les points suivants :

 Mon code respecte les normes PSR-12 et les bonnes pratiques définies.

 Les tests (composer test) passent sans erreurs.

 PHP_CodeSniffer ne retourne aucune erreur (composer phpcs).

 PHP-CS-Fixer est exécuté (composer cs).

 Ma branche est à jour avec main (rebase effectué).

 Ma PR décrit clairement les modifications apportées.

📫 Questions et communication
Si vous avez des questions ou besoin d’aide, contactez l’équipe via :

Les canaux de communication dédiés

Les commentaires GitHub sur les issues et PRs.

Merci pour votre contribution ! 🎉