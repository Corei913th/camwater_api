# CamwaterPRO API

> Plateforme de gestion du système de CAMWATER - API Backend Laravel

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Table des Matières

- [Démarrage Rapide](#démarrage-rapide)
- [Architecture](#architecture)
- [Documentation](#documentation)
- [Modules](#modules)
- [Tests](#tests)
- [Contribution](#contribution)



## 🚀 Démarrage Rapide

### Prérequis

- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (pour les assets)

### Installation

```bash
# Cloner le repository
git clone <repository-url>
cd ec03_api

# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=enrolcm
# DB_USERNAME=root
# DB_PASSWORD=

# Exécuter les migrations
php artisan migrate

# (Optionnel) Seed la base de données
php artisan db:seed

# Démarrer le serveur
php artisan serve
```

L'API sera accessible sur `http://localhost:8000`

## 🏗️ Architecture

### Structure du Projet

```
ec03_api/
├── app/
│   ├── Enums/           # Énumérations
│   ├── Exceptions/      # Exceptions métier
│   ├── Http/
│   │   ├── Controllers/ # Contrôleurs
│   │   ├── Middleware/  # Middlewares
│   │   ├── Requests/    # Form Requests
│   ├── Models/          # Modèles Eloquent
│   ├── Services/        # Services métier
├── config/              # Configuration
├── database/
│   ├── migrations/      # Migrations
│   └── seeders/         # Seeders
├── routes/              # Routes API
└── tests/               # Tests



### Utilisateurs
- **Authentification** : Login/Logout
- **Utilisateurs** : Gestion des utilisateurs
- **Rôles** : Gestion des rôles 

## 🧪 Tests

### Exécuter les Tests

```bash
# Tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter=AuthTest

# Avec couverture
php artisan test --coverage
```

### Tests Disponibles

- Tests unitaires des services
- Tests d'intégration des contrôleurs
- Tests de validation


### Commandes Laravel

```bash
# Nettoyer le cache
php artisan optimize:clear

# Générer l'autoload
composer dump-autoload

# Lister les routes
php artisan route:list

# Créer un contrôleur
php artisan make:controller NomController

# Créer un modèle avec migration
php artisan make:model Nom -m

# Créer un service
php artisan make:class Services/NomService
```

## 📊 API Endpoints

### Base URL

```
http://localhost:8000/api/
```

### Doc URL

```
http://localhost:8000/api/docs
```


### Documentation API

```bash
# Lister toutes les routes
php artisan route:list

# Filtrer par préfixe
php artisan route:list --path=api
```

## 🤝 Contribution

### Workflow Git

```bash
# Créer une branche
git checkout -b feature/nom-fonctionnalite

# Développer et tester
php artisan test

# Commit
git add .
git commit -m "feat: description"

# Push
git push origin feature/nom-fonctionnalite
```

### Standards de Code

- PSR-12 pour le style de code
- PHPDoc pour la documentation
- Tests unitaires obligatoires
- Validation des données avec Form Requests


### Conventions de Nommage

- **Contrôleurs** : `NomController.php`
- **Services** : `NomService.php`
- **Modèles** : `Nom.php` (singulier)
- **Migrations** : `create_noms_table.php` (pluriel)
- **Routes** : `/noms` (pluriel, kebab-case)



## 📄 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👥 Équipe

- **Backend** : Laravel API

