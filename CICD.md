# CAMWATERPRO API — Pipeline CI/CD & Observabilité

**Documentation Technique Officielle**  
**Dépôt** : [https://github.com/Corei913th/camwater_api](https://github.com/Corei913th/camwater_api)

| Projet | CamwaterPRO API |
| :--- | :--- |
| **Stack** | Laravel 11 · PHP 8.2 · PostgreSQL 15 · Docker · Kubernetes |
| **CI/CD** | GitHub Actions → Docker Hub |
| **Observabilité** | ELK Stack (Logs) · Prometheus & Grafana (Métriques) · Alertmanager |

---

## PARTIE I : CONCEPTS FONDAMENTAUX DU CI/CD

### 1. Intégration Continue (CI)
Pour CamwaterPRO, la CI garantit que chaque modification est testée avant fusion.  
**Pipeline automatique** : Linting (Pint) → Analyse Statique (Larastan) → Tests Unitaires & Fonctionnels.  
*Principe : Tout échec bloque le déploiement.*

### 2. Déploiement Continu (CD)
Le passage du code validé vers les environnements de Staging et Production.
- **Livraison Continue** : Construction des images Docker et publication sur Docker Hub.
- **Déploiement Continu** : Mise à jour automatique du serveur mutualisé O2Switch avec un Health Check post-déploiement.

---

## PARTIE II : ANALYSE DE LA STACK TECHNIQUE

| Couche | Technologie | Rôle |
| :--- | :--- | :--- |
| **Backend** | Laravel 11 | API REST & Administration |
| **Base de données** | PostgreSQL 15 | Stockage persistant |
| **Conteneurisation** | Docker | Isolation des services (App, Nginx, DB) |
| **Metrics** | Prometheus | Collecte des indicateurs (Scrape /api/metrics) |
| **Visualisation** | Grafana | Dashboards Stratégiques & Business |
| **Logs** | ELK Stack | Centralisation (Filebeat → Logstash → ES → Kibana) |

---

## PARTIE III : STRATÉGIE DE GESTION DU CODE

### 1. Workflow des Branches (GitHub Flow)
- **main** : Code de production stable.
- **develop** : Branche d'intégration (Staging).
- **feature/** : Nouvelles fonctionnalités (vie courte).
- **hotfix/** : Corrections urgentes de production.

### 2. Convention de Commits
Utilisation de **Conventional Commits** :
- `feat(api):` Ajout d'une fonctionnalité.
- `fix(auth):` Correction d'un bug.
- `ci(monitoring):` Modification des fichiers de pipeline ou monitoring.

---

## PARTIE IV : OBSERVABILITÉ & MONITORING (L'Intelligence Opérationnelle)

C'est ici que nous surveillons la santé réelle de CamwaterPRO.

### 1. Dashboards Grafana
Nous avons mis en place deux niveaux de visibilité :

#### A. Strategic Dashboard v2 (Opérations)
Ce tableau de bord surveille la réactivité technique de l'application.
- **Taux d'Erreur Global** : Jauge critique basée sur les codes HTTP 4xx/5xx.
- **Temps de Réponse Moyen App** : Mesure précise via le middleware Laravel (en secondes).
- **Santé Infra** : Utilisation CPU/RAM du serveur et état de la base de données.

> **[CAPTURE D'ÉCRAN À INSÉRER ICI]**  
> *Nom suggéré : `grafana_strategic_v2.png`*  
> *Focus : Jauges d'erreurs et courbes de latence.*

#### B. Business KPIs (Métiers)
Destiné à la gestion, ce dashboard montre la valeur générée.
- **Chiffre d'Affaires** : Répartition Encaissé (Collected) vs En attente (Pending).
- **Croissance Abonnés** : Total des abonnés en temps réel.
- **Volume Facturation** : Décompte des factures payées et impayées.

> **[CAPTURE D'ÉCRAN À INSÉRER ICI]**  
> *Nom suggéré : `grafana_business_kpis.png`*  
> *Focus : Statistiques de revenus et nombre d'abonnés.*

### 2. Alerting (Alertmanager)
Le système nous prévient par email en cas d'anomalie :
- **HighErrorRate** : Seuil de 5% d'erreurs dépassé.
- **DatabaseDown** : Perte de connexion PostgreSQL.
- **AppServiceDown** : L'API ne répond plus.

### 3. Centralisation des Logs (ELK)
- **Kibana** : Point d'entrée pour le debugging. Index pattern : `laravel-logs-*`.
- **Logstash** : Parseur de logs JSON Laravel.
- **Filebeat** : Collecteur optimisé (mode `--strict.perms=false` pour compatibilité Docker Windows).

> **[CAPTURE D'ÉCRAN À INSÉRER ICI]**  
> *Nom suggéré : `kibana_discover.png`*  
> *Focus : Vue "Discover" avec les logs JSON structurés de Laravel.*

---

## PARTIE V : DÉPLOIEMENT & INFRASTRUCTURE

### 1. Docker Compose (Orchestration standard)
Permet de lancer les 14 services de la stack de manière coordonnée.

### 2. Kubernetes (Évolution Scalable)
Pour le passage à l'échelle, les ressources sont organisées en :
- **Deployments** (Laravel, Nginx)
- **StatefulSets** (PostgreSQL, Elasticsearch)
- **HorizontalPodAutoscaler** (Scaling auto de 2 à 10 réplicas).

---

## PARTIE VI : DÉTAILS DU PIPELINE GITHUB ACTIONS

Le cycle de vie du code est protégé par trois workflows principaux.

### 1. Workflow de Validation (CI)
Déclenché à chaque `push` sur n'importe quelle branche.

| Job | Étapes clés | Rôle |
| :--- | :--- | :--- |
| **Security Audit** | `composer audit` · `phpstan` | Détecte les failles dans les dépendances et les erreurs de logique (SAST). |
| **Secret Scanning** | `gitleaks` | Vérifie qu'aucun mot de passe ou clé n'est présent dans l'historique Git. |
| **IaC Scan** | `trivy` | Analyse le Dockerfile et les fichiers de config pour les failles de sécurité. |
| **Lint & Style** | `laravel pint` | Garantit que le code respecte la norme PSR-12. |
| **Tests** | `artisan test` | Lance les tests unitaires et fonctionnels avec une base PostgreSQL éphémère. |
| **SonarQube** | `sonar-scanner` | Analyse finale de la qualité et de la dette technique (après succès des tests). |

### 2. Workflow de Déploiement (CD Production)
Déclenché uniquement après le succès de la CI sur la branche **main**.

1. **Validation CI** : Vérifie que le workflow précédent est bien passé.
2. **Préparation** : Génération sécurisée des clés d'application et du fichier `.env`.
3. **Synchronisation** : Déploiement des fichiers via **FTP-Deploy-Action** vers l'hébergement O2Switch (dossier `api/`).
4. **Post-Déploiement** : (Exécuté sur le serveur) Migration de base de données et vidage des caches.

---

## GLOSSAIRE
- **CI (Continuous Integration)** : Automatisation des tests.
- **CD (Continuous Deployment)** : Automatisation de la mise en ligne.
- **Scrape** : Action de Prometheus de venir lire les métriques sur `/api/metrics`.
- **Latency** : Temps mis par l'application pour répondre à une requête.
- **P95** : Indicateur de performance excluant les 5% de requêtes les plus lentes.
