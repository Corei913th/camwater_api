# CAMWATERPRO API — Pipeline CI/CD

**Intégration Continue & Déploiement Continu**

**Url du dépôt** : [https://github.com/Corei913th/camwater_api](https://github.com/Corei913th/camwater_api)

| | |
|---|---|
| **Projet** | CamwaterPRO API |
| **Stack** | Laravel 12 · PHP 8.2 · PostgreSQL 15 · Docker · Kubernetes |
| **CI/CD** | GitHub Actions → Docker Hub |
| **Observabilité** | ELK Stack (Elasticsearch · Logstash · Kibana · Filebeat) · Prometheus · Grafana · Alertmanager |

---

## Outils utilisés

### Stack applicative

| Outil | Version | Rôle |
|---|---|---|
| **Laravel** | 12.x | Framework PHP principal de l'API REST |
| **PHP** | 8.2 | Langage backend |
| **PostgreSQL** | 15 (Alpine) | Base de données relationnelle |
| **Laravel Sanctum** | 4.2 | Authentification par tokens API |
| **Composer** | latest | Gestionnaire de dépendances PHP |

### Conteneurisation & Orchestration

| Outil | Rôle |
|---|---|
| **Docker** | Conteneurisation de l'application (image PHP-FPM) |
| **Docker Compose** | Orchestration locale de tous les services (app, nginx, db, monitoring, logging) |
| **Nginx** | Reverse proxy devant PHP-FPM |
| **Kubernetes** | Orchestration de production (Deployment, HPA, Service, Namespace) |

### CI/CD

| Outil | Rôle |
|---|---|
| **GitHub Actions** | Plateforme d'exécution des pipelines CI/CD |
| **Laravel Pint** | Linting et formatage PSR-12 du code PHP |
| **Larastan** | Analyse statique (PHPStan Level 5) adaptée à Laravel |
| **Pest / PHPUnit** | Framework de tests unitaires et fonctionnels |
| **Composer Audit** | Scan des vulnérabilités CVE dans les dépendances |
| **SonarQube** | Analyse de la qualité du code, dette technique et security hotspots |
| **Gitleaks** | Détection de secrets et credentials dans le code source |
| **Trivy** | Scan de sécurité des fichiers IaC (Dockerfile, docker-compose, k8s) |
| **Docker Hub** | Registre d'images Docker pour la publication des builds |
| **k6** | Tests de charge et de performance de l'API |

### ELK Stack — Centralisation des logs

| Composant | Version | Rôle |
|---|---|---|
| **Elasticsearch** | 7.17.10 | Moteur d'indexation et de recherche des logs |
| **Logstash** | 7.17.10 | Pipeline d'ingestion : reçoit les logs de Filebeat, les parse (JSON pour les logs Laravel) et les envoie à Elasticsearch |
| **Kibana** | 7.17.10 | Interface web de visualisation et d'exploration des logs (port 5601) |
| **Filebeat** | 7.17.10 | Agent léger de collecte des logs Docker, les transmet à Logstash |

**Architecture du flux de logs** : Conteneurs Docker → Filebeat → Logstash (parsing JSON) → Elasticsearch → Kibana

#### Capture — Kibana : Exploration des logs Laravel

> *Interface Kibana (port 5601) — Visualisation des logs Laravel indexés sous `laravel-logs-YYYY.MM.dd`.*

![Kibana — Logs Laravel](captures/kibana-logs-laravel.png)

### Prometheus Stack — Monitoring et alerting

| Composant | Rôle |
|---|---|
| **Prometheus** | Collecte et stockage des métriques (scrape toutes les 15s) |
| **Grafana** | Tableaux de bord de visualisation (dashboards business KPIs + dashboard principal) |
| **Alertmanager** | Gestion des alertes (notification par email) |
| **Node Exporter** | Métriques système (CPU, mémoire, disque) |
| **cAdvisor** | Métriques des conteneurs Docker |
| **Nginx Exporter** | Métriques du reverse proxy Nginx |
| **Postgres Exporter** | Métriques de la base PostgreSQL |

#### Capture — Grafana : Dashboard Business KPIs

Dashboard `CamwaterPRO Business KPIs` — 4 panneaux :
- **Total Factures** (stat) — métrique `camwater_invoices_total`
- **Revenu Total** (stat, XAF) — métrique `camwater_revenue_total`
- **Abonnés Actifs** (gauge) — métrique `camwater_active_subscribers`
- **Réclamations en attente** (stat) — métrique `camwater_complaints_pending`

![Grafana — Business KPIs](captures/grafana-business-kpis.png)

#### Capture — Grafana : Dashboard Advanced Performance

Dashboard `CamwaterPRO Advanced Performance Dashboard` — 5 panneaux :
- **Nginx Request Rate** (timeseries) — `rate(nginx_http_requests_total[5m])`
- **HTTP Status Codes** (timeseries) — répartition par code HTTP
- **Postgres Active Connections** (timeseries) — `pg_stat_activity_count`
- **Database Transaction Rate** (timeseries) — commits vs rollbacks
- **Infra Global Health CPU/RAM** (timeseries) — utilisation CPU et mémoire

![Grafana — Advanced Performance](captures/grafana-advanced-performance.png)

#### Capture — Prometheus : Targets

> *Interface Prometheus (port 9090) — État des cibles scrapées : app, nginx, postgres, node-exporter, cadvisor.*

![Prometheus — Targets](captures/prometheus-targets.png)

#### Capture — Alertmanager : Alertes configurées

> *Alertes actives : InstanceDown (critical), HighCpuUsage > 80% (warning), HighMemoryUsage > 90% (warning).*

![Alertmanager — Alertes](captures/alertmanager-alertes.png)

---

## PARTIE I : CONCEPTS FONDAMENTAUX DU CI/CD

### 1. Définition du CI : Intégration Continue

L'Intégration Continue (CI — Continuous Integration) pour CamwaterPRO consiste à fusionner fréquemment les modifications de code dans le dépôt principal. Chaque push déclenche automatiquement une suite de vérifications : linting (Pint), analyse statique (Larastan), audit de sécurité (Composer Audit, Gitleaks, Trivy), et tests unitaires/fonctionnels (Pest) pour garantir la stabilité de l'API.

**Principe fondateur** — Tout commit échouant à l'une des étapes bloque le pipeline et notifie l'équipe. Dans CamwaterPRO, cela assure que seuls les services validés (`CalculateurFacture`, `AuthService`, `FactureService`, `AbonneService`) atteignent l'étape de construction Docker.

### 2. Définition du CD : Livraison et Déploiement Continus

#### 2.1 Livraison Continue (Continuous Delivery)

Garantit que le code validé est toujours prêt à être déployé. Pour CamwaterPRO, les images Docker (PHP-FPM) sont construites et publiées sur Docker Hub après chaque succès de la CI.

> **Livraison Continue — CamwaterPRO**
> Après chaque CI réussie sur la branche principale, l'image Docker PHP-FPM est taguée (`latest` + SHA du commit) et publiée sur Docker Hub. L'équipe peut déclencher un déploiement sur le cluster Kubernetes à tout moment, avec la certitude que le code est opérationnel.

#### 2.2 Déploiement Continu (Continuous Deployment)

Automatisation totale : tout merge sur `main` déclenche la construction et la publication de l'image Docker de production. Pour CamwaterPRO, le déploiement Kubernetes peut ensuite être déclenché avec les manifestes présents dans le dossier `k8s/`.

> **Déploiement Continu — CamwaterPRO**
> Un merge validé sur la branche `main` déclenche automatiquement la construction de l'image Docker de production, taguée `latest` et identifiée par le SHA du commit. Le déploiement sur Kubernetes utilise les manifestes (`deployment.yaml`, `hpa.yaml`, `namespace.yaml`) avec une stratégie RollingUpdate garantissant zéro indisponibilité.

### 3. Séquence détaillée du pipeline CI/CD

Le pipeline de CamwaterPRO est structuré en 4 phases majeures automatisant le cycle de vie complet du code.

#### Stage 1 : CI — Validation (`ci.yml`)

| Job | Description technique |
|---|---|
| **Lint & Style** | Exécution de Laravel Pint (`--test`) pour garantir la conformité PSR-12. |
| **Security Audit & SAST** | Composer Audit scanne les dépendances contre les CVE. Larastan (Level 5) analyse les types et prévient les erreurs de logique sans exécution. |
| **Secret Scanning** | Gitleaks analyse l'historique Git pour détecter des secrets ou credentials commités accidentellement. |
| **IaC & Docker Scan** | Trivy scanne le Dockerfile, docker-compose.yml et les manifestes Kubernetes pour les vulnérabilités CRITICAL et HIGH. |
| **Feature & Unit Tests** | Pest exécute les tests unitaires et fonctionnels avec un service PostgreSQL 15 conteneurisé. Migrations et seeds automatiques. |
| **SonarQube Scan** | Analyse approfondie de la dette technique et de la sécurité via `sonarqube-scan-action`. Exécuté après le succès des tests. |

#### Stage 2 : CD Staging — Build & Push (`cd-staging.yml`)

| Job | Description technique |
|---|---|
| **Build & Push** | Construction de l'image Docker PHP-FPM. Publication sur Docker Hub avec les tags `develop` et SHA du commit. Déclenché sur push `develop`. |

#### Stage 3 : CD Production — Build & Push (`cd-production.yml`)

| Job | Description technique |
|---|---|
| **Build & Push** | Construction de l'image Docker PHP-FPM. Publication sur Docker Hub avec les tags `latest` et SHA du commit. Déclenché sur push `main`. |

#### Stage 4 : Verify — Garantie

| Job | Description technique |
|---|---|
| **Load Test** | Exécution de k6 (`tests/performance/load-test.js`) : montée à 20 utilisateurs simultanés, seuil p95 < 500ms, taux d'erreur < 1%. |
| **Health Check** | Test de disponibilité de l'endpoint `/api/health`. |

---

## PARTIE II : ANALYSE ET PRÉPARATION DU PROJET

### 1. Analyse de l'application CamwaterPRO

#### i. Identification du langage, du framework et de l'architecture

CamwaterPRO est une API REST dédiée à la gestion de la facturation et des abonnés de la Cameroon Water Utilities Corporation. Elle repose sur une architecture MVC Laravel. L'analyse de la stack technique retenue produit le profil CI/CD suivant :

| Couche | Technologie | Impact sur le pipeline |
|---|---|---|
| **Backend** | Laravel 12 (PHP 8.2) | `composer install` · `artisan test` (Pest) · Larastan static analysis |
| **Base de données** | PostgreSQL 15 | Migrations Laravel · Service Postgres conteneurisé en CI · backup pré-déploiement |
| **Auth** | Laravel Sanctum 4.2 | Variable `APP_KEY` injectée depuis GitHub Secrets |
| **Qualité du code** | Laravel Pint · Larastan Level 5 | Lint PSR-12 + analyse statique à chaque push, bloquant si échec |
| **Sécurité** | Composer Audit · SonarQube · Gitleaks · Trivy | Scan CVE dépendances + analyse dette technique + secret scanning + scan IaC à chaque CI |
| **Conteneurisation** | Docker (PHP-FPM + Nginx) | Build optimisé · push Docker Hub |
| **Orchestration** | Docker Compose · Kubernetes | `docker-compose.yml` pour développement/staging · `k8s/` pour production scalable |
| **Logging** | ELK Stack (Elasticsearch · Logstash · Kibana · Filebeat) | Centralisation des logs Docker via Filebeat → Logstash → Elasticsearch → Kibana |
| **Monitoring** | Prometheus · Grafana · Alertmanager | Métriques API, système, conteneurs, Nginx, PostgreSQL collectées via exporters |
| **Versioning** | GitHub (dépôt `camwater_api`) | Déclencheur natif GitHub Actions · intégration native SonarQube |
| **Tests de charge** | k6 | Validation de la performance API avant déploiement production |

#### ii. Identification des besoins CI/CD

**Fréquence de déploiement** — CamwaterPRO est une API critique gérant la facturation des abonnés en eau. La fréquence de déploiement est encadrée pour garantir la stabilité du service :

| Branche | Fréquence | Environnement cible | Condition |
|---|---|---|---|
| `feature/*` | Chaque push développeur | Aucun (CI uniquement) | Lint PSR-12 + Pest + Larastan + Gitleaks + Trivy verts |
| `develop` | Fin de chaque fonctionnalité | Staging (image Docker `develop`) | CI verte + Pull Request approuvée |
| `main` | Fin de sprint (livraison) | Production (image Docker `latest` + Kubernetes) | CI verte + PR approuvée · backup BDD auto |
| `hotfix/*` | Sur demande urgente (< 2h) | Production directe | CI verte + déclenchement manuel |

**Types de tests à automatiser**

| Type de test | Périmètre CamwaterPRO | Outil retenu | Déclencheur |
|---|---|---|---|
| Lint / Style | Code PHP — conformité PSR-12 | Laravel Pint | Chaque push |
| Analyse statique | Types, logique métier — `CalculateurFacture`, `AuthService` | Larastan Level 5 | Chaque push |
| Audit sécurité | Vulnérabilités CVE dans les dépendances Composer | Composer Audit | Chaque push |
| Secret scanning | Secrets et credentials dans le code source | Gitleaks | Chaque push |
| Scan IaC | Dockerfile, docker-compose.yml, manifestes Kubernetes | Trivy | Chaque push |
| Tests unitaires & fonctionnels | `CalculateurFacture` (tarification) · `AuthService` (Sanctum) · API endpoints | Pest | Chaque push |
| Analyse qualité | Dette technique, duplications, security hotspots de l'API | SonarQube | Chaque push (après tests) |
| Migration BDD | Schéma PostgreSQL : `Abonne`, `Facture`, `Reclamation`, `User` | `artisan migrate` | Chaque push (en CI) + déploiement |
| Test de charge | 20 utilisateurs simultanés · SLA p95 < 500ms · taux erreur < 1% | k6 | Avant déploiement production |

**Environnements nécessaires**

| Environnement | Rôle | Déclencheur CI/CD | Particularités |
|---|---|---|---|
| **Development** | Développement local. Tests Pest et hot-reload Artisan. | Push `feature/*` | Docker Compose local · `.env` · PostgreSQL conteneurisé |
| **Staging** | Validation de l'intégration et recette fonctionnelle avant production. | Merge sur `develop` | Image Docker taguée `develop` publiée sur Docker Hub · `artisan migrate` auto |
| **Production** | API live utilisée par les opérateurs et abonnés CamwaterPRO. | Merge sur `main` | Image Docker taguée `latest` · Kubernetes (RollingUpdate, HPA) · Backup PostgreSQL pré-déploiement |

#### iii. Choix de l'outil CI/CD : GitHub Actions

Trois outils ont été évalués : Jenkins, GitHub Actions et GitLab CI/CD. Le choix s'est porté sur **GitHub Actions**.

| Critère | Jenkins | GitHub Actions ✔ | GitLab CI/CD |
|---|---|---|---|
| **Infrastructure** | Serveur dédié Jenkins à maintenir | Runners Ubuntu hébergés (SaaS) — aucune infra additionnelle | Runner propre ou migration vers GitLab |
| **Coût projet** | Gratuit mais serveur à financer | Gratuit — plan GitHub Free (< 2 000 min/mois) | Payant pour SaaS · migration dépôt nécessaire |
| **Intégration dépôt** | Webhooks complexes à configurer | Native au dépôt — zéro configuration supplémentaire | Nécessite mirroring du dépôt GitHub |
| **Docker build & push** | Plugin externe requis | Action officielle `build-push-action` intégrée | Oui, intégré |
| **Déploiement K8s** | Plugin kubectl | `kubectl` action sur Marketplace | Oui |
| **Secrets** | Credentials Manager | GitHub Secrets chiffrés, natif au dépôt | CI/CD Variables GitLab |
| **Sécurité** | Plugins additionnels | Gitleaks, Trivy, SonarQube intégrés via Actions | Scanners intégrés (payants) |

> **Décision — GitHub Actions**
> CamwaterPRO est hébergé sur GitHub ([https://github.com/Corei913th/camwater_api](https://github.com/Corei913th/camwater_api)). GitHub Actions s'intègre nativement sans infrastructure additionnelle. Il supporte nativement le build Docker, le push sur Docker Hub, et le déploiement Kubernetes via `kubectl`.

---

## PARTIE III : GESTION DU CODE SOURCE

### 1. Stratégie de branches

La stratégie GitHub Flow est adoptée, adaptée à une équipe agile. Elle repose sur deux branches stables permanentes et des branches de fonctionnalité à durée de vie courte.

| Branche | Rôle | Pipeline | Protection |
|---|---|---|---|
| `main` | Code déployé en production. Toujours stable. | CI complète + Build & Push Docker `latest` → Kubernetes | PR obligatoire · 1 approbation · status checks verts |
| `develop` | Branche d'intégration. Accumule les features validées avant livraison. | CI complète + Build & Push Docker `develop` | PR obligatoire · push direct interdit |
| `feature/*` | Développement d'une fonctionnalité. Durée courte. | CI uniquement : Pint + Larastan + Pest + Gitleaks + Trivy + SonarQube | Supprimée après merge |
| `hotfix/*` | Correctif urgent issu de `main`. | CI complète + Build & Push production sur déclenchement manuel | Issue depuis `main` uniquement |

**Convention de nommage des branches**

```
feature/camwater-001-auth-sanctum
feature/camwater-002-calculateur-facture
feature/camwater-003-gestion-abonnes
feature/camwater-004-reclamations
feature/camwater-005-rapports-facturation
hotfix/camwater-fix-001-description
```

### 2. Configuration du dépôt

#### i. Protection des branches

- `main` et `develop` : tout push direct est interdit, seules les Pull Requests sont acceptées
- Tous les status checks (Pint, Pest, Larastan, Gitleaks, Trivy, SonarQube) doivent être verts avant le merge
- Au moins une approbation est requise avant le merge
- La branche est automatiquement supprimée après le merge

#### ii. Fichiers de configuration essentiels

| Fichier / Dossier | Rôle |
|---|---|
| `.github/workflows/ci.yml` | Pipeline d'Intégration Continue : Pint, Larastan, Composer Audit, Gitleaks, Trivy, Pest, SonarQube, migration PostgreSQL. Déclenché à chaque push sur toutes les branches. |
| `.github/workflows/cd-staging.yml` | Pipeline CD Staging : Build et push de l'image Docker sur Docker Hub avec les tags `develop` et SHA. Déclenché sur push `develop`. |
| `.github/workflows/cd-production.yml` | Pipeline CD Production : Build et push de l'image Docker sur Docker Hub avec les tags `latest` et SHA. Déclenché sur push `main`. |
| `docker-compose.yml` | Orchestre tous les services : PHP-FPM, Nginx, PostgreSQL, ELK Stack (Elasticsearch, Logstash, Kibana, Filebeat), Prometheus, Grafana, Alertmanager, exporters. |
| `Dockerfile` | Construction de l'image PHP-FPM : installation des dépendances système, extensions PHP, Composer install, permissions. |
| `k8s/deployment.yaml` | Deployment Kubernetes : 2 réplicas, RollingUpdate, conteneurs app (PHP-FPM) + nginx, security contexts, resource limits. |
| `k8s/hpa.yaml` | HorizontalPodAutoscaler : scaling automatique de 2 à 10 réplicas basé sur l'utilisation CPU (seuil 70%). |
| `k8s/namespace.yaml` | Namespace Kubernetes `camwater` pour isoler les ressources du projet. |
| `sonar-project.properties` | Paramétrage du scanner SonarQube : sources (`app`), tests (`tests`), exclusions, clé de projet `Corei913th_camwater_api`. |
| `nginx/default.conf` | Configuration Nginx : reverse proxy vers PHP-FPM (port 9000), endpoint `/status` pour le monitoring. |
| `docker/prometheus/prometheus.yml` | Configuration Prometheus : scrape des exporters (node, cadvisor, nginx, postgres) + métriques business de l'API. |
| `docker/prometheus/alert_rules.yml` | Règles d'alerting : InstanceDown, HighCpuUsage (> 80%), HighMemoryUsage (> 90%). |
| `docker/logstash/logstash.conf` | Pipeline Logstash : réception des logs Filebeat, parsing JSON des logs Laravel, envoi vers Elasticsearch. |
| `docker/filebeat/filebeat.yml` | Configuration Filebeat : collecte des logs des conteneurs Docker, envoi vers Logstash. |
| `docker/alertmanager/alertmanager.yml` | Configuration Alertmanager : notification par email via SMTP. |
| `docker/grafana/provisioning/` | Provisioning Grafana : datasource Prometheus + dashboards (business KPIs, dashboard principal). |
| `tests/performance/load-test.js` | Script k6 : montée en charge à 20 utilisateurs, seuil p95 < 500ms, taux d'erreur < 1%. |
| `.env.example` | Modèle documentant les variables d'environnement nécessaires. Versionné dans le dépôt. |
| `.gitignore` | Exclut du dépôt les fichiers sensibles (`.env`, `vendor/`, `storage/*.key`, fichiers de build). |

#### iii. Gestion des secrets

Toutes les valeurs sensibles sont stockées dans **GitHub Secrets** (Settings → Secrets and variables → Actions). Aucun secret n'est jamais commité dans le dépôt.

| Secret GitHub Actions | Usage |
|---|---|
| `DOCKERHUB_TOKEN` | Token d'authentification pour le push des images sur Docker Hub |
| `DOCKERHUB_USERNAME` | Nom d'utilisateur Docker Hub associé au token de publication |
| `SONAR_TOKEN` | Token d'authentification pour l'analyse SonarQube de CamwaterPRO |
| `SONAR_HOST_URL` | URL du serveur SonarQube pour l'analyse de qualité |
| `APP_KEY` | Clé de chiffrement Laravel (générée via `artisan key:generate`) |
| `DB_PASSWORD` | Mot de passe PostgreSQL 15 de production (32+ caractères) |
| `KUBECONFIG` | Configuration kubectl encodée en base64 pour le déploiement Kubernetes |

#### iv. Convention de commits

L'équipe adopte la convention **Conventional Commits**, permettant une lecture immédiate de l'historique et la génération automatique du changelog.

| Préfixe | Usage | Exemple CamwaterPRO |
|---|---|---|
| `feat:` | Nouvelle fonctionnalité | `feat(api): add invoice calculation endpoint` |
| `fix:` | Correction de bug | `fix(auth): resolve Sanctum token timeout on concurrent requests` |
| `test:` | Ajout ou modification de tests | `test(facture): add unit tests for CalculateurFacture` |
| `ci:` | Modifications du pipeline | `ci: add k6 performance job before production deploy` |
| `chore:` | Maintenance, mises à jour | `chore: upgrade Laravel to 12.x and run migrations` |
| `docs:` | Documentation | `docs: update API documentation with invoice endpoints` |
| `refactor:` | Refactorisation sans impact fonctionnel | `refactor(auth): extract token validation to AuthService` |

---

### Synthèse : Workflow CamwaterPRO

**Push `feature/*`** → CI (Pint + Larastan + Composer Audit + Gitleaks + Trivy + Pest + SonarQube + migration PostgreSQL).

**Merge `develop`** → CI complète + Build & Push image Docker `develop` sur Docker Hub.

**Merge `main`** → CI complète + Build & Push image Docker `latest` sur Docker Hub → Déploiement Kubernetes (RollingUpdate, HPA 2→10 réplicas).

**Observabilité** → ELK Stack (Filebeat → Logstash → Elasticsearch → Kibana) pour les logs · Prometheus + Grafana + Alertmanager pour le monitoring et les alertes.

---

## GLOSSAIRE : Concepts, sigles et termes techniques

### 1. CI/CD — Le cœur du pipeline

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **CI** (Continuous Integration) | Intégration Continue | Fusionner fréquemment le code et le valider automatiquement (lint, tests, sécurité). Détecte les régressions au plus tôt. | À chaque `git push` sur n'importe quelle branche. |
| **CD** (Continuous Delivery) | Livraison Continue | Garantir que le code validé est toujours prêt à être déployé. L'image Docker est construite et publiée sur un registre. | Après chaque CI réussie sur `develop` ou `main`. |
| **CD** (Continuous Deployment) | Déploiement Continu | Automatiser le déploiement en production sans intervention humaine après validation. | Lors du merge sur `main` — le déploiement Kubernetes se déclenche. |
| **Pipeline** | Enchaînement automatisé d'étapes | Séquence ordonnée de jobs (lint → tests → build → deploy) exécutée par GitHub Actions. | Déclenché automatiquement à chaque événement Git (push, PR). |
| **Job** | Unité de travail dans un pipeline | Un ensemble d'étapes exécutées sur un runner. Exemple : le job `tests` lance Pest avec PostgreSQL. | Chaque job s'exécute dans un environnement isolé (conteneur Ubuntu). |
| **Runner** | Machine d'exécution | Serveur (hébergé par GitHub) qui exécute les jobs du pipeline. CamwaterPRO utilise `ubuntu-latest`. | Alloué automatiquement par GitHub Actions à chaque déclenchement. |
| **Status Check** | Vérification de statut | Résultat (vert/rouge) d'un job CI. Les branches protégées exigent tous les checks verts avant le merge. | Lors d'une Pull Request vers `develop` ou `main`. |

### 2. Qualité du code et sécurité

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **Linting** | Vérification du style de code | Analyse le code pour détecter les erreurs de formatage et les violations de conventions. | À chaque push — Laravel Pint vérifie la conformité PSR-12. |
| **PSR-12** | PHP Standards Recommendation n°12 | Norme de style de codage PHP (indentation, espaces, accolades). Pint l'applique automatiquement. | Lors du job Lint — un code non conforme bloque le pipeline. |
| **SAST** (Static Application Security Testing) | Analyse statique de sécurité | Analyse le code source sans l'exécuter pour détecter des failles de sécurité et des erreurs de logique. | À chaque push — Larastan analyse les types et la logique métier. |
| **Larastan** | PHPStan adapté à Laravel | Analyse statique Level 5 : détecte les types incorrects, les appels à des méthodes inexistantes, les erreurs de logique dans les Services. | À chaque push — analyse `CalculateurFacture`, `AuthService`, etc. |
| **CVE** (Common Vulnerabilities and Exposures) | Base de vulnérabilités connues | Identifiant unique pour chaque faille de sécurité publiée. Composer Audit compare les dépendances contre cette base. | À chaque push — Composer Audit bloque si une dépendance a une CVE critique. |
| **Composer Audit** | Audit des dépendances PHP | Scanne le fichier `composer.lock` et signale les paquets ayant des vulnérabilités connues. | Job `security` du pipeline CI. |
| **Gitleaks** | Détection de secrets dans Git | Parcourt l'historique Git complet (`fetch-depth: 0`) pour trouver des clés API, mots de passe ou tokens commités par erreur. | Job `secret-scan` du pipeline CI. |
| **Trivy** | Scanner de sécurité IaC | Analyse les fichiers d'infrastructure (Dockerfile, docker-compose.yml, manifestes Kubernetes) pour détecter des mauvaises configurations de sécurité. | Job `iac` du pipeline CI — bloque si vulnérabilité CRITICAL ou HIGH. |
| **SonarQube** | Plateforme d'analyse de qualité | Mesure la dette technique, les duplications de code, la couverture de tests et les security hotspots. Fournit un tableau de bord web. | Après les tests — le job `sonarqube` envoie les résultats au serveur SonarQube. |
| **Dette technique** | Coût de maintenance accumulé | Code complexe, dupliqué ou mal structuré qui ralentit les évolutions futures. SonarQube la quantifie en jours de travail. | Visible sur le dashboard SonarQube après chaque analyse. |

### 3. Tests

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **Test unitaire** | Test d'une unité isolée | Vérifie qu'une fonction ou méthode produit le résultat attendu. Exemple : `CalculateurFacture` calcule correctement un montant. | À chaque push — exécuté par Pest dans le job `tests`. |
| **Test fonctionnel (Feature)** | Test d'un parcours complet | Simule une requête HTTP complète à l'API et vérifie la réponse. Exemple : `AuthTest` teste le login et l'obtention d'un token Sanctum. | À chaque push — exécuté par Pest avec une BDD PostgreSQL conteneurisée. |
| **Test de charge** | Test de performance sous stress | Simule plusieurs utilisateurs simultanés pour vérifier que l'API répond dans les délais. | Avant le déploiement production — k6 monte à 20 utilisateurs, seuil p95 < 500ms. |
| **Pest** | Framework de tests PHP | Surcouche élégante de PHPUnit, syntaxe expressive. Exécute les tests unitaires et fonctionnels de CamwaterPRO. | Job `tests` du pipeline CI. |
| **k6** | Outil de tests de charge | Script JavaScript qui envoie des requêtes HTTP massives et mesure les temps de réponse, taux d'erreur et throughput. | Fichier `tests/performance/load-test.js` — exécuté avant la mise en production. |
| **SLA** (Service Level Agreement) | Engagement de niveau de service | Seuil de performance garanti. Pour CamwaterPRO : 95% des requêtes doivent répondre en moins de 500ms. | Vérifié par k6 — si le seuil est dépassé, le test échoue. |
| **p95** | 95ème percentile | 95% des requêtes sont plus rapides que cette valeur. Plus fiable qu'une moyenne car exclut les cas extrêmes. | Métrique clé du test k6 : `http_req_duration: ['p(95)<500']`. |

### 4. Conteneurisation — Docker

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **Docker** | Plateforme de conteneurisation | Empaquette l'application et toutes ses dépendances dans une image portable. Garantit un comportement identique en dev, staging et production. | Build de l'image PHP-FPM dans les workflows CD. |
| **Conteneur** | Instance d'une image Docker | Processus isolé exécutant l'application. Plus léger qu'une machine virtuelle car partage le noyau de l'OS hôte. | Chaque service dans `docker-compose.yml` est un conteneur : `camwater_app`, `camwater_db`, etc. |
| **Image Docker** | Paquet immuable | Fichier binaire contenant le code, les dépendances et la configuration. Construite à partir du `Dockerfile`. Taguée et publiée sur Docker Hub. | Construite à chaque merge sur `develop` (tag `develop`) ou `main` (tag `latest`). |
| **Dockerfile** | Recette de construction | Fichier texte décrivant les étapes pour construire l'image : installation de PHP 8.2, extensions, Composer install, permissions. | Utilisé par `docker/build-push-action` dans les workflows CD. |
| **PHP-FPM** (FastCGI Process Manager) | Gestionnaire de processus PHP | Exécute le code PHP de Laravel. Écoute sur le port 9000. Nginx lui transmet les requêtes `.php` via FastCGI. | Conteneur principal de l'application (`camwater_app`). |
| **Docker Compose** | Orchestrateur multi-conteneurs local | Fichier YAML définissant et lançant tous les services ensemble (app, nginx, db, monitoring, logging). Un seul `docker-compose up`. | Développement local et staging — orchestre 14 services simultanément. |
| **Docker Hub** | Registre d'images public | Stocke et distribue les images Docker. Les workflows CD y publient l'image `laravel-app` avec les tags appropriés. | Après chaque build CD — l'image est poussée et disponible pour le déploiement. |
| **Tag** | Étiquette d'une image | Identifie une version précise d'une image. CamwaterPRO utilise `latest`, `develop` et le SHA du commit Git. | Permet de déployer une version spécifique ou de rollback vers une version précédente. |
| **Volume** | Stockage persistant | Données qui survivent au redémarrage d'un conteneur. `dbdata` persiste la base PostgreSQL, `esdata` persiste les index Elasticsearch. | Défini dans `docker-compose.yml` — empêche la perte de données lors des mises à jour. |

### 5. Kubernetes — Orchestration de production

Kubernetes (abrégé **K8s**) est une plateforme open-source d'orchestration de conteneurs développée par Google. Là où Docker Compose orchestre des conteneurs sur **une seule machine**, Kubernetes les orchestre sur **un cluster de machines**, avec de la haute disponibilité, du scaling automatique et du self-healing.

#### Pourquoi Kubernetes pour CamwaterPRO ?

CamwaterPRO est une API critique de facturation des abonnés en eau. Une indisponibilité impacte directement les opérations de la Cameroon Water Utilities Corporation. Kubernetes apporte :

- **Haute disponibilité** — Si un conteneur tombe, Kubernetes le redémarre automatiquement. Si un serveur entier tombe, les conteneurs sont redistribués sur les serveurs restants.
- **Scaling automatique** — En période de facturation (pic de trafic), le HPA augmente automatiquement le nombre de réplicas de 2 à 10. Hors pic, il redescend pour économiser les ressources.
- **Zero-downtime deployment** — La stratégie RollingUpdate déploie la nouvelle version progressivement : un nouveau Pod est créé avant que l'ancien ne soit supprimé. Les utilisateurs ne voient aucune interruption.
- **Rollback instantané** — Si la nouvelle version échoue, Kubernetes peut revenir à la version précédente en une commande (`kubectl rollout undo`).

#### Concepts Kubernetes utilisés dans CamwaterPRO

| Concept | Fichier | Rôle détaillé | Quand est-il utile ? |
|---|---|---|---|
| **Cluster** | — | Ensemble de machines (nœuds) gérées par Kubernetes. Contient un plan de contrôle (master) et des nœuds de travail (workers) qui exécutent les conteneurs. | Infrastructure de base — le cluster doit exister avant tout déploiement. |
| **Namespace** | `k8s/namespace.yaml` | Espace de noms isolé `camwater`. Sépare les ressources de CamwaterPRO des autres applications du cluster. Évite les conflits de noms. | Créé une seule fois lors de la mise en place initiale du cluster. |
| **Deployment** | `k8s/deployment.yaml` | Décrit l'état souhaité de l'application : 2 réplicas, image Docker à utiliser, ports, volumes, limites de ressources. Kubernetes s'assure en permanence que cet état est respecté. | Appliqué à chaque déploiement — Kubernetes compare l'état actuel à l'état souhaité et effectue les changements nécessaires. |
| **Pod** | (créé par le Deployment) | Plus petite unité déployable dans Kubernetes. Dans CamwaterPRO, chaque Pod contient 2 conteneurs : `app` (PHP-FPM, port 9000) et `nginx` (reverse proxy, port 8080). Ils partagent le même réseau local. | Kubernetes crée et gère les Pods automatiquement selon le Deployment. Un Pod qui crashe est recréé. |
| **ReplicaSet** | (géré par le Deployment) | Garantit qu'un nombre exact de Pods identiques est toujours en cours d'exécution. Le Deployment de CamwaterPRO demande 2 réplicas minimum. | En permanence — si un Pod est détruit, le ReplicaSet en crée un nouveau immédiatement. |
| **Service** | `k8s/deployment.yaml` | Point d'entrée réseau stable (IP fixe) vers les Pods. Type `LoadBalancer` : distribue le trafic entrant (port 80) vers les Pods (port 8080). Même si les Pods changent, l'adresse du Service reste la même. | En permanence — c'est l'URL par laquelle les clients accèdent à l'API CamwaterPRO. |
| **HPA** (Horizontal Pod Autoscaler) | `k8s/hpa.yaml` | Scaling automatique horizontal. Surveille l'utilisation CPU des Pods. Si la moyenne dépasse 70%, il ajoute des Pods (jusqu'à 10). Si elle redescend, il réduit (minimum 2). | En continu — réagit aux variations de charge. Pic de facturation → plus de Pods. Nuit calme → moins de Pods. |
| **RollingUpdate** | `k8s/deployment.yaml` | Stratégie de déploiement progressif. `maxSurge: 1` — crée au maximum 1 Pod supplémentaire pendant la mise à jour. `maxUnavailable: 0` — aucun Pod existant n'est supprimé tant que le nouveau n'est pas prêt. | À chaque déploiement d'une nouvelle version — garantit zéro interruption de service. |
| **SecurityContext** | `k8s/deployment.yaml` | Politique de sécurité des conteneurs. `runAsNonRoot: true` — interdit l'exécution en root. `readOnlyRootFilesystem: true` — système de fichiers en lecture seule. `drop ALL capabilities` — supprime toutes les capacités Linux. | En permanence — renforce la sécurité en cas de compromission d'un conteneur. |
| **Secret** (K8s) | `camwater-secrets` | Objet Kubernetes stockant les données sensibles (clés, mots de passe) chiffrées. Les Pods y accèdent via `envFrom: secretRef`. Différent des GitHub Secrets (CI) — ceux-ci sont pour le runtime Kubernetes. | Au démarrage de chaque Pod — les variables d'environnement sensibles sont injectées depuis le Secret. |
| **Resource Limits** | `k8s/deployment.yaml` | Limites de ressources par conteneur. `requests` : minimum garanti (100m CPU, 128Mi RAM). `limits` : maximum autorisé (500m CPU, 512Mi RAM). Empêche un conteneur de monopoliser les ressources du nœud. | En permanence — Kubernetes utilise ces valeurs pour planifier les Pods sur les nœuds et pour le HPA. |
| **emptyDir** | `k8s/deployment.yaml` | Volume temporaire partagé entre les conteneurs d'un même Pod. Utilisé pour `storage/`, `bootstrap/cache/`, `/tmp`. Détruit quand le Pod est supprimé. | Au runtime — permet à PHP-FPM d'écrire dans le cache et le storage malgré le `readOnlyRootFilesystem`. |
| **ConfigMap** | `nginx-config` | Objet Kubernetes stockant de la configuration non-sensible. Contient le fichier `default.conf` de Nginx, monté dans le conteneur nginx du Pod. | Au démarrage du Pod — Nginx charge sa configuration depuis le ConfigMap. |
| **LoadBalancer** | `k8s/deployment.yaml` | Type de Service qui expose l'application à l'extérieur du cluster via une IP publique. Le cloud provider (AWS, GCP, Azure) crée automatiquement un load balancer réseau. | En permanence — c'est le point d'entrée public de l'API CamwaterPRO pour les clients. |
| **kubectl** | — | Outil en ligne de commande pour interagir avec un cluster Kubernetes. Permet de déployer (`kubectl apply`), inspecter (`kubectl get pods`) et rollback (`kubectl rollout undo`). | Lors du déploiement CD et pour l'administration du cluster. |

#### Flux de déploiement Kubernetes — CamwaterPRO

```
Merge sur main
    │
    ▼
GitHub Actions (cd-production.yml)
    │  Build image Docker PHP-FPM
    │  Push sur Docker Hub (tag: latest + SHA)
    │
    ▼
kubectl apply -f k8s/
    │
    ├── namespace.yaml    → Crée/vérifie le namespace "camwater"
    ├── deployment.yaml   → Met à jour le Deployment (nouvelle image)
    │     │
    │     ▼
    │   RollingUpdate :
    │     1. Crée un nouveau Pod avec la nouvelle image
    │     2. Attend que le Pod soit Ready
    │     3. Supprime un ancien Pod
    │     4. Répète jusqu'à ce que tous les Pods soient à jour
    │
    ├── deployment.yaml   → Service LoadBalancer (port 80 → 8080)
    │     distribue le trafic vers tous les Pods sains
    │
    └── hpa.yaml          → HPA surveille le CPU
          Si CPU > 70% → scale up (max 10 Pods)
          Si CPU < 70% → scale down (min 2 Pods)
```

### 6. Observabilité — ELK Stack

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **ELK Stack** | Elasticsearch + Logstash + Kibana | Suite d'outils de centralisation, traitement et visualisation des logs. Complétée par Filebeat pour la collecte. | En permanence — collecte et indexe les logs de tous les conteneurs Docker. |
| **Elasticsearch** | Moteur de recherche et d'indexation | Stocke les logs dans des index journaliers (`laravel-logs-YYYY.MM.dd`). Permet des recherches full-text ultra-rapides sur des millions de lignes de logs. | Quand un développeur ou opérateur cherche un log d'erreur spécifique — recherche instantanée via Kibana. |
| **Logstash** | Pipeline de traitement des logs | Reçoit les logs bruts de Filebeat (port 5044), applique des filtres (parsing JSON pour les logs Laravel), et les envoie à Elasticsearch. | En continu — transforme les logs bruts en données structurées et interrogeables. |
| **Kibana** | Interface web de visualisation | Tableau de bord web (port 5601) pour explorer, filtrer et visualiser les logs. Permet de créer des dashboards, des alertes et des rapports. | Pour le debugging en production — filtrer les logs par niveau (error, warning), par service, par période. |
| **Filebeat** | Agent de collecte léger | Installé sur la machine Docker, lit les logs des conteneurs (`/var/lib/docker/containers/*/*.log`) et les envoie à Logstash. Très peu gourmand en ressources. | En permanence — tourne en arrière-plan et transmet les logs en temps réel. |

### 7. Observabilité — Prometheus Stack

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **Prometheus** | Système de monitoring par métriques | Collecte des métriques numériques (CPU, RAM, requêtes/s, connexions BDD) en interrogeant les exporters toutes les 15 secondes. Stocke les données en séries temporelles. | En permanence — alimente Grafana et déclenche les alertes via Alertmanager. |
| **Grafana** | Plateforme de visualisation | Affiche les métriques Prometheus sous forme de graphiques, jauges et compteurs. CamwaterPRO dispose de 2 dashboards : Business KPIs et Advanced Performance. | Pour le monitoring en temps réel — consulter l'état de l'API, du trafic et de l'infrastructure sur le port 3000. |
| **Alertmanager** | Gestionnaire d'alertes | Reçoit les alertes de Prometheus et les achemine (email SMTP). Gère le regroupement, le silence et la répétition des alertes. | Quand un seuil est franchi — InstanceDown (1 min), HighCpuUsage (> 80%, 2 min), HighMemoryUsage (> 90%, 2 min). |
| **Exporter** | Agent de collecte de métriques | Programme qui expose des métriques au format Prometheus. Chaque composant a son exporter dédié (node, cadvisor, nginx, postgres). | En permanence — chaque exporter expose un endpoint `/metrics` que Prometheus interroge. |
| **Scrape** | Collecte de métriques | Action de Prometheus d'aller chercher les métriques sur les endpoints des exporters. Intervalle configuré à 15 secondes. | Toutes les 15 secondes — Prometheus interroge tous les targets configurés dans `prometheus.yml`. |
| **PromQL** | Prometheus Query Language | Langage de requête pour interroger les métriques. Exemple : `rate(nginx_http_requests_total[5m])` calcule le taux de requêtes Nginx sur 5 minutes. | Dans Grafana — chaque panneau de dashboard utilise une requête PromQL. |
| **Alert Rule** | Règle d'alerte | Expression PromQL avec un seuil et une durée. Si la condition est vraie pendant la durée spécifiée, l'alerte se déclenche. Définies dans `alert_rules.yml`. | En permanence — Prometheus évalue les règles à chaque cycle d'évaluation (15s). |

### 8. Infrastructure et réseau

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **Nginx** | Serveur web / Reverse proxy | Reçoit les requêtes HTTP (port 80), sert les fichiers statiques et transmet les requêtes PHP à PHP-FPM (port 9000) via FastCGI. Expose aussi un endpoint `/status` pour le monitoring. | En permanence — point d'entrée de toutes les requêtes vers l'API. |
| **FastCGI** | Protocole de communication | Protocole binaire entre Nginx et PHP-FPM. Plus performant que CGI classique car maintient des processus PHP persistants. | À chaque requête PHP — Nginx communique avec PHP-FPM via `fastcgi_pass app:9000`. |
| **Reverse proxy** | Proxy inversé | Serveur intermédiaire qui reçoit les requêtes des clients et les redirige vers le serveur applicatif (PHP-FPM). Masque l'architecture interne. | En permanence — les clients ne communiquent jamais directement avec PHP-FPM. |
| **Health Check** | Vérification de santé | Requête HTTP périodique vers un endpoint (`/api/health`) pour vérifier que le service est opérationnel. Utilisé par Docker, Kubernetes et le pipeline. | PostgreSQL en CI (`pg_isready`), k6 post-déploiement, Kubernetes liveness/readiness probes. |
| **IaC** (Infrastructure as Code) | Infrastructure en tant que code | Définir l'infrastructure (Docker, Kubernetes, monitoring) dans des fichiers versionnés plutôt que via des configurations manuelles. | Tout le projet — `Dockerfile`, `docker-compose.yml`, `k8s/*.yaml`, configs Prometheus/ELK sont de l'IaC. |

### 9. Git et GitHub

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **GitHub Actions** | Plateforme CI/CD de GitHub | Exécute les workflows définis dans `.github/workflows/` sur des runners Ubuntu hébergés. Gratuit jusqu'à 2 000 min/mois. | À chaque push ou Pull Request — déclenche automatiquement les pipelines CI et CD. |
| **Workflow** | Fichier YAML de pipeline | Définit les déclencheurs (`on`), les jobs et les étapes. CamwaterPRO en a 3 : `ci.yml`, `cd-staging.yml`, `cd-production.yml`. | Chaque fichier `.yml` dans `.github/workflows/` est un workflow indépendant. |
| **GitHub Secrets** | Variables chiffrées | Valeurs sensibles (tokens, mots de passe) stockées de manière chiffrée dans les paramètres du dépôt. Accessibles uniquement dans les workflows via `${{ secrets.NOM }}`. | Lors de l'exécution des workflows — injectées comme variables d'environnement. |
| **Pull Request (PR)** | Demande de fusion | Mécanisme pour proposer des changements d'une branche vers une autre. Permet la revue de code et déclenche les status checks. | Pour fusionner `feature/*` → `develop` ou `develop` → `main`. |
| **Branch Protection** | Protection de branche | Règles empêchant le push direct sur une branche. Exige des status checks verts et des approbations avant le merge. | Sur `main` et `develop` — empêche le code non validé d'atteindre les branches stables. |
| **SHA** | Secure Hash Algorithm | Identifiant unique d'un commit Git (40 caractères hexadécimaux). Utilisé comme tag d'image Docker pour traçabilité. | Chaque image Docker est taguée avec `${{ github.sha }}` — permet de savoir exactement quel commit est déployé. |
| **Conventional Commits** | Convention de nommage des commits | Format standardisé : `type(scope): description`. Permet la génération automatique de changelog et une lecture claire de l'historique. | À chaque commit — `feat:`, `fix:`, `ci:`, `test:`, etc. |

### 10. Laravel et PHP

| Terme | Signification | Rôle | Quand est-il utile ? |
|---|---|---|---|
| **Laravel** | Framework PHP MVC | Structure l'API avec des routes, contrôleurs, modèles, services et migrations. Version 12 pour CamwaterPRO. | Base de tout le code applicatif. |
| **MVC** (Model-View-Controller) | Patron d'architecture | Sépare la logique métier (Models : `Abonne`, `Facture`), le traitement (Controllers : `AuthController`, `FactureController`) et les réponses (JSON API). | Organisation du code — chaque couche a une responsabilité claire. |
| **Artisan** | CLI Laravel | Interface en ligne de commande pour les tâches courantes : `migrate` (BDD), `test` (tests), `key:generate` (clé de chiffrement), `db:seed` (données de test). | Dans le pipeline CI (migrations, tests) et lors du déploiement. |
| **Migration** | Script de modification BDD | Fichier PHP décrivant un changement de schéma de base de données (création de table, ajout de colonne). Versionné et réversible. | À chaque déploiement — `artisan migrate --force` applique les nouvelles migrations. |
| **Sanctum** | Système d'authentification API | Gère les tokens d'API (Personal Access Tokens). Plus léger que Passport, adapté aux API SPA et mobiles. | Authentification des utilisateurs de l'API — génération et validation des tokens. |
| **Composer** | Gestionnaire de paquets PHP | Installe et gère les dépendances PHP déclarées dans `composer.json`. Le fichier `composer.lock` verrouille les versions exactes. | En CI (`composer install`) et dans le Dockerfile. |
| **Seed** | Données de test | Script PHP qui remplit la base de données avec des données fictives pour les tests. Exécuté via `artisan db:seed`. | En CI — après les migrations, les seeds fournissent les données nécessaires aux tests fonctionnels. |
