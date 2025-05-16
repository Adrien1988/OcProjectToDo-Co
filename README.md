# 📋 ToDo&Co – Gestionnaire de tâches collaboratif

Projet Symfony 6.4 permettant à des utilisateurs de gérer leurs tâches personnelles ou collaboratives, avec un système d’authentification et une interface sécurisée.

## 🚀 Installation locale du projet

### ⚙️ Prérequis

Assurez-vous d’avoir installé localement :

- PHP 8.1+
- Composer 2+
- Symfony CLI (optionnel mais recommandé)
- MySQL 8 ou MariaDB
- Git

### 📦 Récupération du projet

```bash
git clone https://github.com/Adrien1988/OcProjectToDo-Co.git
cd OcProjectToDo-Co
```
📁 Configuration de l’environnement
Copiez le fichier .env de base et adaptez la configuration :

```bash
cp .env .env.local
```

Dans le fichier .env.local, renseignez l’URL de connexion à la base :

```bash
DATABASE_URL="mysql://root:root@127.0.0.1:3306/app?serverVersion=8.0&charset=utf8mb4"
```

💾 Installation des dépendances PHP

```bash
composer install
```

🛠️ Création de la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

🔐 Connexion à un compte administrateur (déjà fourni)
Un utilisateur administrateur est déjà disponible via les fixtures Doctrine :

Nom d’utilisateur : admin

Mot de passe : root

🔄 Si besoin, vous pouvez recharger les fixtures en exécutant :

```bash
php bin/console doctrine:fixtures:load
```

🧪 Lancement des tests
Les tests sont déjà configurés dans les scripts Composer. Tu peux les exécuter simplement via :

```bash
composer test
```
Cela déclenche les étapes suivantes automatiquement :

Nettoyage du cache en environnement test

Lancement des tests unitaires (test:unit)

Lancement des tests fonctionnels (test:functional)

Génération du rapport de couverture (cov:all) dans :

📁 build/coverage (HTML)

📄 build/clover.xml (pour Codacy)

ℹ️ Le minimum de couverture exigé est fixé à 70%.

▶️ Démarrage du serveur

```bash
symfony serve -d
# ou en PHP natif :
php -S 127.0.0.1:8000 -t public/
```
Accédez à l’application via : http://127.0.0.1:8000

📂 Structure principale du projet
src/ – Code source (Contrôleurs, Entités, Sécurité…)

templates/ – Vues Twig

config/ – Configuration Symfony (routes, sécurité…)

public/ – Front controller (index.php)

tests/ – Tests fonctionnels et unitaires

migrations/ – Historique des migrations Doctrine

👥 Contribution
Consultez le fichier CONTRIBUTING.md pour connaître les règles de contribution, les standards qualité et le processus de validation des Pull Requests.

📈 Suivi qualité et couverture
Ce projet utilise :

✅ PHPUnit pour les tests automatisés

✅ PHP_CodeSniffer & PHP-CS-Fixer pour le formatage PSR-12

✅ Codacy pour l’analyse de qualité de code & couverture

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/963f5be2f999444082b16f3f1a2511ec)](https://app.codacy.com/gh/Adrien1988/OcProjectToDo-Co/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)